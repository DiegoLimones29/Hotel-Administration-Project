<?php

namespace App\Http\Repositories;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Invoice;
use App\Models\ReservationService;
use Carbon\Carbon;
use Exception;

class CheckInOutRepository
{
    
    public function todayCheckIns()
    {
        try {
            $reservations = Reservation::with(['room.roomType', 'guest'])
                ->whereDate('check_in_date', Carbon::today())
                ->whereIn('status', ['pending', 'confirmed'])
                ->get();

            return ["message" => "Check-ins de hoy", "data" => $reservations];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    
    public function todayCheckOuts()
    {
        try {
            $reservations = Reservation::with(['room.roomType', 'guest'])
                ->whereDate('check_out_date', Carbon::today())
                ->where('status', 'in_progress')
                ->get();

            return ["message" => "Check-outs de hoy", "data" => $reservations];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

   
    public function checkIn(int $reservationId)
    {
        try {
            $reservation = Reservation::with('room')->find($reservationId);

            if (!$reservation) {
                return ["message" => "Reservación no encontrada"];
            }

            if (!in_array($reservation->status, ['pending', 'confirmed'])) {
                return ["message" => "Esta reservación no está en un estado válido para hacer check-in"];
            }

            if (Carbon::today()->lt($reservation->check_in_date)) {
                return ["message" => "Aún no es la fecha de entrada de esta reservación"];
            }

            $reservation->update([
                'status' => 'in_progress',
                'actual_check_in_at' => now(),
            ]);

            $reservation->room->update(['state' => 'occupied']);

            return [
                "message" => "Check-in registrado",
                "data" => $reservation->load(['room.roomType', 'guest'])
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

   
    public function walkInCheckIn(array $data)
    {
        try {
            $room = Room::find($data['room_id']);

            if (!$room) {
                return ["message" => "Habitación no encontrada"];
            }

            if (in_array($room->state, ['out of service', 'on maintenance'])) {
                return ["message" => "La habitación no está disponible (fuera de servicio o en mantenimiento)"];
            }

            $checkOutDate = $data['check_out_date'];
            $todayDate = Carbon::today()->format('Y-m-d');

            
            $hasConflict = Reservation::where('room_id', $room->id)
                ->where('status', '!=', 'cancelled')
                ->where('check_in_date', '<', $checkOutDate)
                ->where('check_out_date', '>', $todayDate)
                ->exists();

            if ($hasConflict) {
                return ["message" => "La habitación ya está reservada u ocupada en ese rango de fechas"];
            }

            $nights = Carbon::today()->diffInDays(Carbon::parse($checkOutDate));

            if ($nights < 1) {
                return ["message" => "La fecha de salida debe ser al menos un día después de hoy"];
            }

            $numGuests = $data['num_guests'] ?? 1;

            if ($numGuests > $room->roomType->capacity) {
                return ["message" => "La habitación tiene capacidad para {$room->roomType->capacity} huésped(es), no para {$numGuests}"];
            }

            $totalCost = $nights * $room->roomType->price_per_night;

            $reservation = Reservation::create([
                'room_id' => $room->id,
                'user_id' => $data['user_id'],
                'check_in_date' => Carbon::today()->format('Y-m-d'),
                'check_out_date' => $checkOutDate,
                'num_guests' => $data['num_guests'] ?? 1,
                'num_nights' => $nights,
                'total_cost' => $totalCost,
                'status' => 'in_progress',
                'actual_check_in_at' => now(),
            ]);

            $room->update(['state' => 'occupied']);

            return [
                "message" => "Check-in walk-in registrado",
                "data" => $reservation->load(['room.roomType', 'guest'])
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    
    public function checkOut(int $reservationId, string $paymentMethod)
    {
        try {
            $reservation = Reservation::with('room.roomType')->find($reservationId);

            if (!$reservation) {
                return ["message" => "Reservación no encontrada"];
            }

            if ($reservation->status !== 'in_progress') {
                return ["message" => "Esta reservación no tiene un check-in activo"];
            }

            if ($reservation->invoice) {
                return ["message" => "Esta reservación ya tiene una factura generada"];
            }

            $servicesCost = ReservationService::where('reservation_id', $reservation->id)
                ->get()
                ->sum(function ($rs) {
                    return $rs->quantity * $rs->unit_price;
                });

            
            $checkInDate = Carbon::parse($reservation->check_in_date);
            $today = Carbon::today();
            $actualNights = max($checkInDate->diffInDays($today), 1);
            $pricePerNight = $reservation->room->roomType->price_per_night;
            $roomCost = $actualNights * $pricePerNight;

            $invoice = Invoice::create([
                'reservation_id' => $reservation->id,
                'room_cost' => $roomCost,
                'services_cost' => $servicesCost,
                'total_cost' => $roomCost + $servicesCost,
                'payment_method' => $paymentMethod,
                'issued_at' => now(),
            ]);

            $reservation->update([
                'status' => 'completed',
                'actual_check_out_at' => now(),
                'num_nights' => $actualNights,
                'total_cost' => $roomCost,
            ]);

            $reservation->room->update(['state' => 'available']);

            return [
                "message" => "Check-out registrado, factura generada",
                "data" => [
                    "reservation" => $reservation->load(['room.roomType', 'guest']),
                    "invoice" => $invoice
                ]
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }
}
