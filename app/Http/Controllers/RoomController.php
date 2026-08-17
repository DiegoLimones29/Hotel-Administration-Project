<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\RoomRepository;
use App\Http\Requests\RoomRequests\StoreRoomRequest;
use App\Http\Requests\RoomRequests\UpdateRoomRequest;

class RoomController extends Controller
{
    protected $roomRepository;

    public function __construct(RoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['type', 'floor', 'state']);
        return response()->json($this->roomRepository->getRooms($filters), 200);
    }

    public function store(StoreRoomRequest $request)
    {
        try {
            $data = $request->validated();
            $room = $this->roomRepository->createRoom($data);
            return response()->json($room, 201);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $room = \App\Models\Room::with('roomType')->find($id);

        if (!$room) {
            return response()->json(["message" => "Habitación no encontrada"], 404);
        }

        return response()->json($room, 200);
    }

    public function update(UpdateRoomRequest $request, string $id)
    {
        try {
            $data = $request->validated();
            $room = $this->roomRepository->updateRoom($data, (int) $id);
            return response()->json($room, 200);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function markOutOfService(string $id)
    {
        try {
            $room = $this->roomRepository->markOutOfService((int) $id);
            return response()->json($room, 200);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        //
    }
}