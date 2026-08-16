<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;       
use App\Models\Room_Img;   
use App\Models\Room_Type;  

class RoomController extends Controller
{
    /**
     * Listar Habitaciones (Intranet Web y API Móvil)
     */
    public function index(Request $request)
    {
        // Envolvemos todo en un escudo para que la intranet NUNCA se caiga por culpa de las imágenes
        try {
            $roomTypes = Room_Type::all();
            $roomImages = Room_Img::all(); 
            $rooms = Room::with('roomType')->orderBy('room_number', 'asc')->get();
        } catch (\Illuminate\Database\QueryException $e) {
            // Si alguna tabla falla o no se encuentra en Supabase, creamos listas vacías
            // para que la vista cargue de todos modos sin dar el error 500
            $roomTypes = Room_Type::all();
            $roomImages = collect(); 
            $rooms = Room::with('roomType')->orderBy('room_number', 'asc')->get();
        }

        // 🚀 Camino API: Si la pide la App Móvil, le mandamos JSON puro
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'room_types' => $roomTypes,
                'rooms' => $rooms,
                'room_images' => $roomImages
            ], 200);
        }

        // 🚀 Camino Web: Tu intranet en Blade
        return view('rooms.index', compact('roomTypes', 'roomImages', 'rooms'));
    }
   
    /**
     * Registrar nueva habitación desde la Intranet
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_type_id' => ['required', 'exists:room_type,id'],
            'room_number' => ['required', 'integer', 'unique:rooms,room_number'],
            'room_floor' => ['required', 'integer'],
        ]);

        Room::create([
            'room_type_id' => $request->room_type_id,
            'room_number' => $request->room_number,
            'room_floor' => $request->room_floor,
            'state' => 'available' 
        ]);

        return redirect()->route('rooms.index')->with('success', 'Habitación creada exitosamente en Supabase.');
    }

    /**
     * Mostrar la ficha de detalles de la habitación
     */
    public function show(string $id)
    {
        $room = Room::with('roomType')->findOrFail($id);
        return view('rooms.show', compact('room'));
    }

    /**
     * Actualizar estado operativo
     */
    public function update(Request $request, string $id)
    {
        $request->validate(['state' => ['required', 'in:available,occupied,on maintenance,out of service']]);
        $room = Room::findOrFail($id);
        $room->update(['state' => $request->state]);

        return back()->with('success', 'Estado modificado correctamente.');
    }
}
