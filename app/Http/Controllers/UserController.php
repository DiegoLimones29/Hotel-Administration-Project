<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\UserRequests\StoreGuestRequest;
use App\Http\Requests\UserRequests\StoreStaffRequest;
use App\Http\Requests\UserRequests\RegisterGuestRequest;
use App\Http\Requests\UserRequests\UpdateProfileRequest;
use App\Http\Requests\UserRequests\ChangePasswordRequest;

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

    
    public function register(RegisterGuestRequest $request)
    {
        try {
            $data = $request->validated();
            $result = $this->userRepository->createGuest($data);

            if (!isset($result['data'])) {
                return response()->json($result, 422);
            }

            
            $token = $result['data']->createToken('api-token')->plainTextToken;

            return response()->json([
                "message" => $result['message'],
                "data" => $result['data'],
                "token" => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function me(Request $request)
    {
        return response()->json(["message" => "Perfil obtenido", "data" => $request->user()], 200);
    }

    public function updateMe(UpdateProfileRequest $request)
    {
        try {
            $data = $request->validated();
            $result = $this->userRepository->updateProfile($request->user()->id, $data);
            $status = isset($result['data']) ? 200 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function changeMyPassword(ChangePasswordRequest $request)
    {
        try {
            $data = $request->validated();
            $result = $this->userRepository->changePassword($request->user()->id, $data['current_password'], $data['new_password']);
            $status = ($result['success'] ?? false) ? 200 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
