<?php

namespace App\Http\Repositories;

use App\Models\Reservation;
use App\Models\Service;
use App\Models\ReservationService;
use Carbon\Carbon;
use Exception;

class ReservationServiceRepository
{
    public function assignService(array $data)
    {
        try {
            $reservation = Reservation::find($data['reservation_id']);

            if (!$reservation) {
                return ["message" => "Reservación no encontrada"];
            }

            if ($reservation->status !== 'in_progress') {
                return ["message" => "Solo se pueden agregar servicios a huéspedes con check-in activo"];
            }

            $service = Service::find($data['service_id']);

            if (!$service) {
                return ["message" => "Servicio no encontrado"];
            }

            if (!$service->active) {
                return ["message" => "Este servicio está desactivado y no puede asignarse"];
            }

            $reservationService = ReservationService::create([
                'reservation_id' => $reservation->id,
                'service_id' => $service->id,
                'quantity' => $data['quantity'] ?? 1,
                'unit_price' => $service->price,
                'requested_date' => $data['requested_date'] ?? Carbon::today()->format('Y-m-d'),
                'status' => 'solicitado',
            ]);

            return [
                "message" => "Servicio agregado a la cuenta del huésped",
                "data" => $reservationService->load('service')
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function getByReservation(int $reservationId)
    {
        try {
            $reservation = Reservation::find($reservationId);

            if (!$reservation) {
                return ["message" => "Reservación no encontrada"];
            }

            $services = ReservationService::with('service')
                ->where('reservation_id', $reservationId)
                ->orderByDesc('created_at')
                ->get();

            return [
                "message" => "Servicios de la estadía obtenidos",
                "data" => $services
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function updateStatus(int $id, string $status)
    {
        try {
            $reservationService = ReservationService::find($id);

            if (!$reservationService) {
                return ["message" => "Servicio asignado no encontrado"];
            }

            $reservationService->update(['status' => $status]);

            return [
                "message" => "Estado del servicio actualizado",
                "data" => $reservationService->load('service')
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }
}