<?php

namespace App\Http\Controllers;

use App\Http\Repositories\CheckInOutRepository;
use App\Http\Requests\CheckInOutRequests\WalkInCheckInRequest;
use App\Http\Requests\CheckInOutRequests\CheckOutRequest;

class CheckInOutController extends Controller
{
    protected $checkInOutRepository;

    public function __construct(CheckInOutRepository $checkInOutRepository)
    {
        $this->checkInOutRepository = $checkInOutRepository;
    }

    public function todayCheckIns()
    {
        return response()->json($this->checkInOutRepository->todayCheckIns(), 200);
    }

    public function todayCheckOuts()
    {
        return response()->json($this->checkInOutRepository->todayCheckOuts(), 200);
    }

    public function checkIn(string $reservationId)
    {
        try {
            $result = $this->checkInOutRepository->checkIn((int) $reservationId);
            $status = isset($result['data']) ? 200 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function walkIn(WalkInCheckInRequest $request)
    {
        try {
            $result = $this->checkInOutRepository->walkInCheckIn($request->validated());
            $status = isset($result['data']) ? 201 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function checkOut(CheckOutRequest $request, string $reservationId)
    {
        try {
            $data = $request->validated();
            $result = $this->checkInOutRepository->checkOut((int) $reservationId, $data['payment_method']);
            $status = isset($result['data']) ? 200 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
