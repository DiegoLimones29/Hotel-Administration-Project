<?php

namespace App\Http\Controllers;

use App\Models\Room; 

use Illuminate\Http\Request;
use App\Http\Repositories\RoomTypeRepository;
use App\Http\Requests\StoreRoomTypeRequest; 
use App\Http\Requests\UpdateRoomType; 


class RoomTypeController extends Controller
{ 
    protected $roomTRepository;

    public function __construct(RoomTypeRepository $roomTRepository){

        $this->roomTRepository= $roomTRepository; 
    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      try{
        $room_types= $this->roomTRepository->getRoomTypes(); 
        return response()->json([
            $room_types,
            200
        ]);

      } 
      catch(\Exception $e){
        return response()->json([
            "message" => $e->getMessage()
        ], 500);
      } 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomTypeRequest $request)
    {
        try{

            $validatedData = $request->validated(); 

           $room_type = $this->roomTRepository->createRoomType($validatedData); 
           return response()->json($room_type, 201);

        }
        catch(\Exception $e){
            return response()->json([
                "message" => $e->getMessage()
            ], 500);

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomType $request,  $id)
    {
        try{
            $validatedData = $request->validated(); 

            $room_type = $this -> roomTRepository -> updateRoomType($request->all(), (int)$id); 
            return response() -> json($room_type);
            
        }
        catch(\Exception $e)
        {
            return response() -> json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
