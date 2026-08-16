@extends('layouts.app')

@section('title', 'Ficha Técnica')
@section('page-title')
    Habitación #{{ $room->room_number }}
@endsection

@section('content')
<div class="max-w-2xl bg-white border border-gray-200 rounded-xl shadow-sm p-6">
    <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">
        <div>
            <h2 class="text-xl font-extrabold text-gray-800">Especificaciones Generales</h2>
            <p class="text-xs text-gray-400">Datos sincronizados con la App Móvil</p>
        </div>
        <span class="px-3 py-1 text-xs font-black uppercase rounded-full bg-blue-100 text-blue-800">
            Piso {{ $room->room_floor }}
        </span>
    </div>

    <div class="grid grid-cols-2 gap-4 text-sm mb-6">
        <div class="bg-gray-50 p-3 rounded-lg">
            <span class="block text-[10px] font-bold text-gray-400 uppercase">Clasificación</span>
            <strong class="text-gray-700 text-base">{{ $room->roomType->type ?? 'No asignado' }}</strong>
        </div>
        <div class="bg-gray-50 p-3 rounded-lg">
            <span class="block text-[10px] font-bold text-gray-400 uppercase">Tarifa Comercial</span>
            <strong class="text-gray-700 text-base">${{ number_format($room->roomType->price_per_night ?? 0, 2) }} / Noche</strong>
        </div>
        <div class="bg-gray-50 p-3 rounded-lg">
            <span class="block text-[10px] font-bold text-gray-400 uppercase">Estado Operativo</span>
            <strong class="capitalize text-gray-700">{{ $room->state }}</strong>
        </div>
        <div class="bg-gray-50 p-3 rounded-lg">
            <span class="block text-[10px] font-bold text-gray-400 uppercase">Última Actualización</span>
            <strong class="text-gray-500 text-xs">{{ $room->updated_at->format('d/m/Y H:i A') }}</strong>
        </div>
    </div>

    <div class="flex gap-2">
        <a href="{{ route('rooms.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-xs py-2 px-4 rounded-lg transition">
            ◀ Volver al Monitoreo
        </a>
    </div>
</div>
@endsection
