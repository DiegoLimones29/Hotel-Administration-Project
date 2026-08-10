<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room; // Importamos el modelo Room real para administrar
use App\Models\Room_Img; 
use App\Models\Room_Type; 

class RoomController extends Controller
{
    /**
     * Listar Habitaciones (Sirve para la API Móvil y para tu Intranet Web)
     */
    public function index(Request $request)
    {
        // Traemos los tipos y las habitaciones reales de Supabase con sus estados
        $roomTypes = Room_Type::all();
        $rooms = Room::with('roomType')->orderBy('room_number', 'asc')->get();

        // Parche seguro para la tabla fantasma de imágenes
        try {
            $roomImages = Room_Img::all(); 
        } catch (\Illuminate\Database\QueryException $e) {
            $roomImages = collect(); 
        }

        // 🚀 CAMINO A: Si la petición viene de la App Móvil (API), respondemos con JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'room_types' => $roomTypes,
                'rooms' => $rooms
            ], 200);
        }

        // 🚀 CAMINO B: Si viene de tu navegador web (Intranet), renderizamos la vista Blade
        // Pasamos también la variable $rooms para que puedas pintar la cuadrícula administrativa
        return view('rooms.index', compact('roomTypes', 'roomImages', 'rooms'));
    }

    /**
     * Actualizar Estado de la Habitación (Exclusivo de la Intranet)
     * Permite al recepcionista cambiar el estado de 'available' a 'on maintenance', 'occupied', etc.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'state' => ['required', 'in:available,occupied,on maintenance,out of service'],
        ]);

        $room = Room::findOrFail($id);
        $room->update([
            'state' => $request->state
        ]);

        // Si la petición fuera por API (caso raro), responde JSON, si no, regresa a la pantalla con éxito
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Estado actualizado.']);
        }

        return back()->with('success', 'El estado de la habitación #' . $room->room_number . ' se actualizó correctamente.');
    }
}
