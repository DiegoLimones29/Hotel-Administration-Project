<?php

namespace App\Http\Repositories;

use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Exception;

class ReservationRepository
{
    private function hasDateConflict(int $roomId, string $checkIn, string $checkOut, ?int $ignoreReservationId = null): bool
    {
        $query = Reservation::where('room_id', $roomId)
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn);

        if ($ignoreReservationId) {
            $query->where('id', '!=', $ignoreReservationId);
        }

        return $query->exists();
    }

    public function getReservations(array $filters = [])
    {
        try {
            $query = Reservation::with(['room.roomType', 'guest']);

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            return [
                "message" => "Reservaciones obtenidas",
                "data" => $query->orderByDesc('created_at')->get()
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function show(int $id)
    {
        try {
            $reservation = Reservation::with(['room.roomType', 'guest'])->find($id);

            if (!$reservation) {
                return ["message" => "Reservación no encontrada"];
            }

            return [
                "message" => "Reservación obtenida",
                "data" => $reservation
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function checkAvailability(?int $roomTypeId, string $checkIn, string $checkOut)
    {
        try {
            $roomsQuery = Room::with('roomType')
                ->whereNotIn('state', ['out of service', 'on maintenance']);

            if ($roomTypeId) {
                $roomsQuery->where('room_type_id', $roomTypeId);
            }

            $rooms = $roomsQuery->get();

            $available = $rooms->filter(function ($room) use ($checkIn, $checkOut) {
                return !$this->hasDateConflict($room->id, $checkIn, $checkOut);
            })->values();

            return [
                "message" => "Disponibilidad calculada",
                "data" => $available
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function createReservation(array $data)
    {
        try {
            $room = Room::find($data['room_id']);

            if (!$room) {
                return ["message" => "Habitación no encontrada"];
            }

            if (in_array($room->state, ['out of service', 'on maintenance'])) {
                return ["message" => "La habitación no está disponible (fuera de servicio o en mantenimiento)"];
            }

            if ($this->hasDateConflict($room->id, $data['check_in_date'], $data['check_out_date'])) {
                return ["message" => "La habitación ya está reservada u ocupada en ese rango de fechas"];
            }

            $numGuests = $data['num_guests'] ?? 1;

            if ($numGuests > $room->roomType->capacity) {
                return ["message" => "La habitación tiene capacidad para {$room->roomType->capacity} huésped(es), no para {$numGuests}"];
            }

            $checkIn = Carbon::parse($data['check_in_date']);
            $checkOut = Carbon::parse($data['check_out_date']);
            $nights = $checkIn->diffInDays($checkOut);

            $pricePerNight = $room->roomType->price_per_night;
            $totalCost = $nights * $pricePerNight;

            $reservation = Reservation::create([
                'room_id' => $room->id,
                'user_id' => $data['user_id'],
                'check_in_date' => $data['check_in_date'],
                'check_out_date' => $data['check_out_date'],
                'num_guests' => $data['num_guests'] ?? 1,
                'num_nights' => $nights,
                'total_cost' => $totalCost,
                'status' => 'pending',
            ]);

            $room->update(['state' => 'reserved']);

            return [
                "message" => "Reservación creada",
                "data" => $reservation->load(['room.roomType', 'guest'])
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function updateReservation(array $data, int $id)
    {
        try {
            $reservation = Reservation::find($id);

            if (!$reservation) {
                return ["message" => "Reservación no encontrada"];
            }

            if (in_array($reservation->status, ['in_progress', 'completed', 'cancelled'])) {
                return ["message" => "No se puede editar una reservación con check-in ya realizado, completada o cancelada"];
            }

            $roomId = $data['room_id'] ?? $reservation->room_id;
            $checkIn = $data['check_in_date'] ?? $reservation->check_in_date->format('Y-m-d');
            $checkOut = $data['check_out_date'] ?? $reservation->check_out_date->format('Y-m-d');

            if ($this->hasDateConflict((int) $roomId, $checkIn, $checkOut, $reservation->id)) {
                return ["message" => "La habitación ya está reservada u ocupada en ese rango de fechas"];
            }

            $room = Room::find($roomId);
            $numGuests = $data['num_guests'] ?? $reservation->num_guests;

            if ($numGuests > $room->roomType->capacity) {
                return ["message" => "La habitación tiene capacidad para {$room->roomType->capacity} huésped(es), no para {$numGuests}"];
            }

            $nights = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));

            $reservation->update([
                'room_id' => $roomId,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'num_guests' => $numGuests,
                'num_nights' => $nights,
                'total_cost' => $nights * $room->roomType->price_per_night,
            ]);

            return [
                "message" => "Reservación actualizada",
                "data" => $reservation->load(['room.roomType', 'guest'])
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function cancelReservation(int $id, ?string $reason = null)
    {
        try {
            $reservation = Reservation::find($id);

            if (!$reservation) {
                return ["message" => "Reservación no encontrada"];
            }

            if ($reservation->status === 'cancelled') {
                return ["message" => "La reservación ya estaba cancelada"];
            }

            if ($reservation->status === 'completed') {
                return ["message" => "No se puede cancelar una reservación completada"];
            }

            $reservation->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
            ]);

            $room = $reservation->room;
            if ($room && $room->state !== 'out of service' && $room->state !== 'on maintenance') {
                $stillHasActive = Reservation::where('room_id', $room->id)
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if (!$stillHasActive) {
                    $room->update(['state' => 'available']);
                }
            }

            return [
                "message" => "Reservación cancelada, habitación liberada",
                "data" => $reservation
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }
}