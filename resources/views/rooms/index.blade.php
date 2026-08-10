@extends('layouts.app')

@section('title', 'Control de Habitaciones')
@section('page-title', 'Monitoreo de Habitaciones en Tiempo Real')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <!-- Indicadores de estado para el recepcionista -->
    <div class="flex flex-wrap gap-4 mb-6 text-sm font-semibold">
        <span class="flex items-center gap-2"><span class="w-4 h-4 bg-green-500 rounded-full"></span> Disponible (Available)</span>
        <span class="flex items-center gap-2"><span class="w-4 h-4 bg-red-500 rounded-full"></span> Ocupada (Occupied)</span>
        <span class="flex items-center gap-2"><span class="w-4 h-4 bg-yellow-500 rounded-full"></span> Mantenimiento (On Maintenance)</span>
        <span class="flex items-center gap-2"><span class="w-4 h-4 bg-gray-400 rounded-full"></span> Fuera de Servicio (Out of Service)</span>
    </div>

    <!-- Cuadrícula de Habitaciones -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @forelse($rooms as $room)
            @php
                // Mapeo dinámico de colores según el estado en inglés de la base de datos
                $bgColor = match($room->state) {
                    'available' => 'bg-green-100 border-green-400 text-green-800',
                    'occupied' => 'bg-red-100 border-red-400 text-red-800',
                    'on maintenance' => 'bg-yellow-100 border-yellow-400 text-yellow-800',
                    default => 'bg-gray-100 border-gray-400 text-gray-700',
                };
            @endphp

            <div class="border rounded-lg p-4 text-center shadow-sm {{ $bgColor }}">
                <div class="text-xs uppercase font-bold tracking-wider opacity-75">
                    Piso {{ $room->room_floor }}
                </div>
                <div class="text-2xl font-black my-1">
                    #{{ $room->room_number }}
                </div>
                <div class="text-xs font-semibold truncate">
                    {{ $room->roomType->type ?? 'Sin tipo' }}
                </div>
                <div class="text-[10px] mt-1 font-bold">
                    ${{ number_format($room->roomType->price_per_night ?? 0, 2) }} / Noche
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-8 text-gray-500">
                No hay habitaciones registradas en la base de datos de Supabase todavía.
            </div>
        @endforelse
    </div>
</div>
@endsection
