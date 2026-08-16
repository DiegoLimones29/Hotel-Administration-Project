<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Intranet') - Control de Hotel</title>
    <!-- Usamos el compilador local del equipo en lugar de enlaces externos -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex min-h-screen">
    <!-- Menú Lateral Izquierdo -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col justify-between p-4 min-h-screen">
        <div>
            <div class="text-center mb-8">
                <h5 class="text-xl font-black text-blue-500 tracking-wider">HOTEL CONTROL</h5>
                <p class="text-[10px] text-gray-400 mt-1 uppercase">Panel Administrativo</p>
            </div>
            
            <div class="px-2 mb-2 text-xs font-bold text-gray-500 uppercase tracking-widest">Módulos</div>
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium transition {{ Request::is('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-slate-800' }}">
                    📊 Dashboard
                </a>
                <a href="{{ route('rooms.index') }}" class="block px-4 py-2.5 rounded-lg text-sm font-medium transition {{ Request::is('habitaciones*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-slate-800' }}">
                    🛏️ Habitaciones
                </a>
                <a href="#" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-slate-800 transition">
                    📅 Reservaciones
                </a>
                <a href="#" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-slate-800 transition">
                    👤 Huéspedes
                </a>
                <a href="#" class="block px-4 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-slate-800 transition">
                    🧾 Facturación
                </a>
            </nav>
        </div>
        
        <div class="border-t border-slate-800 pt-4">
            <div class="px-4 mb-3">
                <p class="text-xs text-gray-400 truncate">Sesión: <span class="font-bold text-white">{{ Auth::user()->name }}</span></p>
                <span class="inline-block bg-blue-500/20 text-blue-400 text-[10px] font-bold px-2 py-0.5 rounded mt-1 uppercase tracking-wide">
                    {{ Auth::user()->role }}
                </span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white font-bold text-sm py-2 px-4 rounded-lg transition">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <!-- Contenido Principal Derecho -->
    <main class="flex-1 p-8">
        <header class="mb-6 pb-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-3xl font-black text-gray-800">@yield('page-title')</h1>
            @yield('header-action')
        </header>

        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>
