<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\UserRequests\StoreGuestRequest;
use App\Http\Requests\UserRequests\StoreStaffRequest;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function guests(Request $request)
    {
        return response()->json($this->userRepository->getGuests($request->query('search')), 200);
    }

    public function storeGuest(StoreGuestRequest $request)
    {
        try {
            $data = $request->validated();
            $result = $this->userRepository->createGuest($data);
            $status = isset($result['data']) ? 201 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function storeStaff(StoreStaffRequest $request)
    {
        try {
            $data = $request->validated();
            $result = $this->userRepository->createStaff($data);
            $status = isset($result['data']) ? 201 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
