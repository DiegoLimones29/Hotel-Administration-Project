@extends('layouts.app')

@section('title', 'Control de Habitaciones')
@section('page-title', 'Monitoreo de Habitaciones')

@section('header-action')
<!-- 🔍 FILTROS POR PISO -->
<div class="bg-white p-4 border border-gray-200 rounded-xl shadow-sm flex items-center gap-4">
    <form action="{{ route('rooms.index') }}" method="GET" class="flex items-center gap-2">
        <label class="text-xs font-bold uppercase text-gray-500">Filtrar por Piso:</label>
        <select name="floor" onchange="this.form.submit()" class="border border-gray-300 rounded-lg p-1 text-xs focus:outline-none bg-gray-50 font-bold">
            <option value="">Todos los pisos</option>
            <option value="1" {{ request('floor') == '1' ? 'selected' : '' }}>Piso 1</option>
            <option value="2" {{ request('floor') == '2' ? 'selected' : '' }}>Piso 2</option>
            <option value="3" {{ request('floor') == '3' ? 'selected' : '' }}>Piso 3</option>
            <option value="4" {{ request('floor') == '4' ? 'selected' : '' }}>Piso 4</option>
        </select>
    </form>
</div>
@endsection

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
    
    <!-- Mosaico Comercial -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($rooms as $room)
            @php
                // 1. Buscamos si este tipo de cuarto tiene una foto asignada en la tabla rooms_imgs
                $imgSrc = $roomImages->where('room_type_id', $room->room_type_id)->first();
                $urlFoto = $imgSrc ? $imgSrc->img_url : 'https://unsplash.com';

                // 2. Colores de la barra de estado superior
                $statusColor = match($room->state) {
                    'available' => 'bg-green-500',
                    'occupied' => 'bg-red-500',
                    'on maintenance' => 'bg-yellow-500',
                    default => 'bg-gray-400',
                };
            @endphp

            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm flex flex-col justify-between group hover:shadow-md transition">
                <div>
                    <!-- Contenedor Visual de la Foto del Cuarto -->
                    <div class="h-36 w-full bg-cover bg-center relative" style="background-image: url('{{ $urlFoto }}')">
                        <div class="absolute top-2 left-2 bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-black px-2 py-0.5 rounded-md uppercase tracking-wider">
                            Piso {{ $room->room_floor }}
                        </div>
                        <!-- Indicador Flotante de Estado -->
                        <span class="absolute top-2 right-2 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $statusColor }}"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 {{ $statusColor }}"></span>
                        </span>
                    </div>

                    <!-- Ficha Comercial Inferior -->
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-1">
                            <h3 class="text-xl font-black text-gray-800">#{{ $room->room_number }}</h3>
                            <span class="text-sm font-extrabold text-blue-600">${{ number_format($room->roomType->price_per_night ?? 0, 2) }}</span>
                        </div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide truncate mb-1">
                            {{ $room->roomType->type ?? 'Sin Categoría' }}
                        </p>
                        <span class="inline-block text-[10px] font-bold uppercase px-2 py-0.5 rounded-sm {{ $room->state == 'available' ? 'bg-green-100 text-green-700' : ($room->state == 'occupied' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $room->state == 'available' ? 'Disponible' : ($room->state == 'occupied' ? 'Ocupada' : 'Mantenimiento / Bloqueada') }}
                        </span>
                    </div>
                </div>

                <!-- Controles Operativos de la Recepción -->
                <div class="p-4 pt-0 border-t border-gray-50 mt-2 flex gap-1 items-center">
                    <form action="{{ route('rooms.update', $room->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PUT')
                        <!-- ⚠️ REGLA DE NEGOCIO: Quitamos 'occupied' del selector manual -->
                        <select name="state" onchange="this.form.submit()" {{ $room->state == 'occupied' ? 'disabled' : '' }} class="text-[11px] bg-gray-50 border border-gray-200 text-gray-700 rounded-lg p-1.5 w-full font-bold focus:outline-none cursor-pointer">
                            <option value="available" {{ $room->state == 'available' ? 'selected' : '' }}>🟢 Disponible</option>
                            <option value="on maintenance" {{ $room->state == 'on maintenance' ? 'selected' : '' }}>🛠️ Mantenimiento</option>
                            <option value="out of service" {{ $room->state == 'out of service' ? 'selected' : '' }}>⚪ Fuera de Servicio</option>
                            @if($room->state == 'occupied')
                                <option value="occupied" selected>🔴 Ocupada (En Estadía)</option>
                            @endif
                        </select>
                    </form>
                    
                    <a href="{{ route('rooms.show', $room->id) }}" class="bg-slate-900 hover:bg-blue-600 text-white p-2 rounded-lg transition" title="Ver Expediente">
                        🔍
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 text-gray-400 font-medium">
                No hay habitaciones que coincidan con el filtro seleccionado.
            </div>
        @endforelse
    </div>
</div>
@endsection
