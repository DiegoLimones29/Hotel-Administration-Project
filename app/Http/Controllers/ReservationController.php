<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\ReservationRepository;
use App\Http\Requests\ReservationRequests\StoreReservationRequest;
use App\Http\Requests\ReservationRequests\UpdateReservationRequest;
use App\Http\Requests\ReservationRequests\CancelReservationRequest;

class ReservationController extends Controller
{
    protected $reservationRepository;

    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status']);
        return response()->json($this->reservationRepository->getReservations($filters), 200);
    }

    public function show(Request $request, string $id)
    {
        $result = $this->reservationRepository->show((int) $id);

        if (isset($result['data']) && $request->user()->role === 'guest' && $result['data']->user_id !== $request->user()->id) {
            return response()->json(["message" => "No puedes ver una reservación que no es tuya"], 403);
        }

        return response()->json($result, isset($result['data']) ? 200 : 404);
    }

    public function availability(Request $request)
    {
        $data = $request->validate([
            'room_type_id'   => 'nullable|exists:room_type,id',
            'check_in_date'  => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        return response()->json($this->reservationRepository->checkAvailability(
            $data['room_type_id'] ?? null,
            $data['check_in_date'],
            $data['check_out_date']
        ), 200);
    }

    public function store(StoreReservationRequest $request)
    {
        try {
            $data = $request->validated();

            
            if ($request->user()->role === 'guest') {
                $data['user_id'] = $request->user()->id;
            } elseif (empty($data['user_id'])) {
                
                return response()->json([
                    "message" => "El huésped es requerido (rol detectado en tu sesión: {$request->user()->role})"
                ], 422);
            }

            $result = $this->reservationRepository->createReservation($data);
            $status = isset($result['data']) ? 201 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function myReservations(Request $request)
    {
        $filters = $request->only(['status']);
        $filters['user_id'] = $request->user()->id;
        return response()->json($this->reservationRepository->getReservations($filters), 200);
    }

    public function update(UpdateReservationRequest $request, string $id)
    {
        try {
            $data = $request->validated();
            $result = $this->reservationRepository->updateReservation($data, (int) $id);
            $status = isset($result['data']) ? 200 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function confirm(string $id)
    {
        try {
            $result = $this->reservationRepository->confirmReservation((int) $id);
            $status = isset($result['data']) ? 200 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function cancel(CancelReservationRequest $request, string $id)
    {
        try {
            $data = $request->validated();

            if ($request->user()->role === 'guest') {
                $reservation = \App\Models\Reservation::find((int) $id);
                if (!$reservation || $reservation->user_id !== $request->user()->id) {
                    return response()->json(["message" => "No puedes cancelar una reservación que no es tuya"], 403);
                }
            }

            $result = $this->reservationRepository->cancelReservation((int) $id, $data['cancellation_reason']);
            $status = isset($result['data']) ? 200 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}