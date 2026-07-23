<?php

namespace App\Http\Repositories;
use App\Models\Room_Type;  

class RoomTypeRepository{

    public function getRoomTypes(){
        try{
            $room_types = Room_Type::all(); 
            return [
                "message" => "Tipos de habitaciones obtenidas",
                "data" => $room_types
            ]; 
        }
        catch(\Exception $e){
            return [
                "message" => $e->getMessage()
            ];
        }
    }

    public function createRoomType(Array $data){

        try{
            $room_type= Room_Type::create([
            "type" => $data['type'],
            "price_per_night" => $data['price_per_night'],
            "description" => $data['description']
            ]);

            return [
                "message" => 'Room type registered',
                'Room_type: ' => $room_type
            ];
        

        }
        catch(\Exception $e)
        {
            return [
                "message" => $e->getMessage()
            ];
        }
    }

    public function updateRoomType(Array $data, $id){

        try{
            $room_type= Room_Type::find($id); 
        
            if(!$room_type){
                return[
                    "message" => "Room Type not found"
                ];
            }

            $room_type->update([
                "type" => $data['type'],
                'price_per_night' => $data['price_per_night'],
                'description' => $data['description']
            ]);
            $room_type->save(); 

            return[
                'message' => 'Room Type successfully updated',
                'room_type' => $room_type
            ];

        }
        catch(\Exception $e){
            return [
                "message" => $e->getMessage()
            ];
        }

    }



}