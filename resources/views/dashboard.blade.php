<<<<<<< Updated upstream
<h1>Bienvenido al Panel del Hotel, {{ Auth::user()->name }} ({{ Auth::user()->role }})</h1>
<form action="{{ route('logout') }}" method="POST">@csrf <button type="submit">Cerrar Sesión</button></form>

=======
@extends('layouts.app')

@section('title', 'Inicio')
@section('page-title', 'Panel Principal de Gestión')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Tarjeta de Habitaciones -->
    <div class="bg-white border border-gray-200 shadow-sm p-6 rounded-lg flex flex-col justify-between">
        <div>
            <h5 class="text-gray-400 text-xs uppercase font-bold tracking-wider mb-2">Control de Habitaciones</h5>
            <p class="text-2xl font-extrabold text-gray-800 mb-1">Monitoreo</p>
            <span class="text-green-600 text-sm font-medium">Revisar limpieza y disponibilidad</span>
        </div>
        <!-- FORZAMOS EL LINK DIRECTO EN CASO DE QUE EL ROUTE() ESTÉ CACHADO -->
        <a href="/habitaciones" class="block text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-2 px-4 rounded mt-4 transition w-full z-50">
            Ver Habitaciones
        </a>
    </div>

    <!-- Tarjeta de Reservaciones -->
    <div class="bg-white border border-gray-200 shadow-sm p-6 rounded-lg flex flex-col justify-between">
        <div>
            <h5 class="text-gray-400 text-xs uppercase font-bold tracking-wider mb-2">Check-In & Check-Out</h5>
            <p class="text-2xl font-extrabold text-gray-800 mb-1">Reservas</p>
            <span class="text-blue-600 text-sm font-medium">Administrar entradas del día</span>
        </div>
        <a href="#" class="block text-center bg-gray-200 text-gray-400 font-semibold text-sm py-2 px-4 rounded mt-4 cursor-not-allowed w-full">
            Ir a Recepción
        </a>
    </div>

    <!-- Tarjeta de Clientes -->
    <div class="bg-white border border-gray-200 shadow-sm p-6 rounded-lg flex flex-col justify-between">
        <div>
            <h5 class="text-gray-400 text-xs uppercase font-bold tracking-wider mb-2">Directorio Comercial</h5>
            <p class="text-2xl font-extrabold text-gray-800 mb-1">Huéspedes</p>
            <span class="text-yellow-600 text-sm font-medium">Registros e identificaciones</span>
        </div>
        <a href="#" class="block text-center bg-gray-200 text-gray-400 font-semibold text-sm py-2 px-4 rounded mt-4 cursor-not-allowed w-full">
            Ver Expedientes
        </a>
    </div>

</div>
@endsection
>>>>>>> Stashed changes
