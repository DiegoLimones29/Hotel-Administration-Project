<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\ReservationServiceRepository;
use App\Http\Requests\ServiceRequests\AssignServiceRequest;
use App\Http\Requests\ServiceRequests\UpdateServiceStatusRequest;

class ReservationServiceController extends Controller
{
    protected $reservationServiceRepository;

    public function __construct(ReservationServiceRepository $reservationServiceRepository)
    {
        $this->reservationServiceRepository = $reservationServiceRepository;
    }

    public function store(AssignServiceRequest $request)
    {
        try {
            $data = $request->validated();
            $user = $request->user();
            $isStaff = in_array($user->role, ['admin', 'recep']);

            $result = $this->reservationServiceRepository->assignService($data, $user->id, $isStaff);
            $status = isset($result['data']) ? 201 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function byReservation(Request $request, string $reservationId)
    {
        $user = $request->user();
        $isStaff = in_array($user->role, ['admin', 'recep']);

        $result = $this->reservationServiceRepository->getByReservation((int) $reservationId, $user->id, $isStaff);

        if (isset($result['data'])) {
            return response()->json($result, 200);
        }

        $status = str_contains($result['message'] ?? '', 'no es tuya') ? 403 : 404;
        return response()->json($result, $status);
    }

    public function updateStatus(UpdateServiceStatusRequest $request, string $id)
    {
        try {
            
            if ($request->user()->role === 'guest') {
                return response()->json(["message" => "No tienes permiso para cambiar el estado de un servicio"], 403);
            }

            $data = $request->validated();
            $result = $this->reservationServiceRepository->updateStatus((int) $id, $data['status']);
            $status = isset($result['data']) ? 200 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
