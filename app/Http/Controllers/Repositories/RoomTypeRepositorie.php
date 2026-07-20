<?php

namespace App\Http\Repositories;
use App\Model\Room_Type;  

class RoomTypeRepository{

    public function getRoomTypes(){
        try{
            $room_types = Room_Type::all(); 
            return [
                "message" => "Tipo de habitaciones obtenidas",
                "data" => $room_types
            ]; 
        }
        catch(\Exception $e){
            return [
                "message" => $e->getMessage()
            ];
        }
    }

    

}