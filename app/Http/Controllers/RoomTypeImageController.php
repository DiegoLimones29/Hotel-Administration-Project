<?php

namespace App\Http\Controllers;

use App\Http\Repositories\RoomTypeImageRepository;
use App\Http\Requests\RoomTypeImageRequests\UploadRoomTypeImageRequest;

class RoomTypeImageController extends Controller
{
    protected $roomTypeImageRepository;

    public function __construct(RoomTypeImageRepository $roomTypeImageRepository)
    {
        $this->roomTypeImageRepository = $roomTypeImageRepository;
    }

    public function index(string $roomTypeId)
    {
        $result = $this->roomTypeImageRepository->getByRoomType((int) $roomTypeId);
        return response()->json($result, 200);
    }

    public function store(UploadRoomTypeImageRequest $request, string $roomTypeId)
    {
        try {
            $result = $this->roomTypeImageRepository->upload((int) $roomTypeId, $request->file('image'));
            $status = isset($result['data']) ? 201 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
