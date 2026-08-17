<?php

namespace App\Http\Controllers;

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
            $result = $this->reservationServiceRepository->assignService($data);
            $status = isset($result['data']) ? 201 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function byReservation(string $reservationId)
    {
        $result = $this->reservationServiceRepository->getByReservation((int) $reservationId);
        $status = isset($result['data']) ? 200 : 404;
        return response()->json($result, $status);
    }

    public function updateStatus(UpdateServiceStatusRequest $request, string $id)
    {
        try {
            $data = $request->validated();
            $result = $this->reservationServiceRepository->updateStatus((int) $id, $data['status']);
            $status = isset($result['data']) ? 200 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}