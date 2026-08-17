<?php

namespace App\Http\Repositories; 

use App\Models\Room;
use Exception;

class RoomRepository{

    public function getRooms(array $filters= [])
    {
        try{
            $query = Room::with('roomType'); 

            if (!empty($filters['type'])){
                $query->whereHas('roomType', function ($q) use ($filters){
                    $q->where('type', $filters['type']); 
                });
            }

            if(!empty($filters['floor'])){
                $query->where('room_floor', $filters['floor']);
            }

            if(!empty($filters['state'])){
                $query->where('state', $filters['state']); 
            }

            return [
                "message" => "Habitaciones obtenidas",
                'data' => $query->get()
            ];
        }
        catch(Exception $e){
            return[
                'message' => $e->getMessage()
            ];
        }
    }

    public function createRoom(array $data)
    {
        try{
            $room = Room::create([
                'room_type_id' => $data['room_type_id'], 
                'room_number' => $data['room_number'], 
                'room_floor' => $data['room_floor'],
            ]);

            return [
                'message' => 'Habitacion registrada', 
                'data' => $room->load('roomType')
            ];
        }
        catch(Exception $e){
            return[
                'message' => $e->getMessage()
            ];
        }

    }

    public function updateRoom(array $data, int $id)
    {
        try {
            $room = Room::find($id);

            if (!$room) {
                return ["message" => "Habitación no encontrada"];
            }

            $room->update($data);

            return [
                "message" => "Habitación actualizada",
                "data" => $room->load('roomType')
            ];
        } catch (\Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function markOutOfService(int $id)
    {
        try {
            $room = Room::find($id);

            if (!$room) {
                return ["message" => "Habitación no encontrada"];
            }

            if (in_array($room->state, ['occupied', 'reserved'])) {
                return [
                    "message" => "No se puede marcar fuera de servicio: la habitación está ocupada o reservada"
                ];
            }

            $room->update(['state' => 'out of service']);

            return [
                "message" => "Habitación marcada fuera de servicio",
                "data" => $room
            ];
        } catch (\Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

   
    private const MANUAL_STATES = ['available', 'on maintenance', 'out of service'];

    public function updateRoomState(int $id, string $newState)
    {
        try {
            if (!in_array($newState, self::MANUAL_STATES)) {
                return ["message" => "Estado no válido para asignación manual"];
            }

            $room = Room::find($id);

            if (!$room) {
                return ["message" => "Habitación no encontrada"];
            }

            // Si está ocupada o reservada por el flujo normal del sistema,
            // no se puede reasignar a mano: debe salir por check-out o
            // cancelación primero.
            if (in_array($room->state, ['occupied', 'reserved'])) {
                return [
                    "message" => "No se puede cambiar el estado a mano mientras está ocupada o reservada. Realiza el check-out o cancela la reservación primero."
                ];
            }

            
            if ($newState === 'available') {
                $hasActiveReservation = \App\Models\Reservation::where('room_id', $room->id)
                    ->whereIn('status', ['confirmed', 'in_progress'])
                    ->exists();

                if ($hasActiveReservation) {
                    return [
                        "message" => "No se puede marcar disponible: tiene una reservación confirmada o en curso"
                    ];
                }
            }

            $room->update(['state' => $newState]);

            return [
                "message" => "Estado de la habitación actualizado",
                "data" => $room->load('roomType')
            ];
        } catch (\Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

}