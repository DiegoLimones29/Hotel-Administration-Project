<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Intranet Hotel') - Panel de Control</title>
    <!-- Bootstrap 5 CDN para un diseño limpio y rápido -->
    <link href="https://jsdelivr.net" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #212529; color: white; padding-top: 20px; }
        .sidebar a { color: rgba(255,255,255,0.75); text-decoration: none; padding: 10px 20px; display: block; }
        .sidebar a:hover, .sidebar a.active { color: white; background-color: rgba(255,255,255,0.1); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Menú Lateral Izquierdo (Navegación de la Intranet) -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="position-sticky">
                <h5 class="text-center text-primary fw-bold mb-4">HOTEL CONTROL</h5>
                <div class="px-3 mb-3 small text-muted">MÓDULOS</div>
                <a href="{{ route('dashboard') }}" class="active">📊 Dashboard</a>
                <a href="#">🛏️ Habitaciones</a>
                <a href="#">📅 Reservaciones</a>
                <a href="#">👤 Huéspedes</a>
                <a href="#">🧾 Facturación</a>
                
                <div class="hr bg-secondary my-4"></div>
                
                <!-- Botón de Cerrar Sesión seguro usando tu ruta -->
                <form action="{{ route('logout') }}" method="POST" class="px-3 mt-4">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">Cerrar Sesión</button>
                </form>
            </div>
        </nav>

        <!-- Contenido Dinámico de cada pantalla -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">@yield('page-title')</h1>
                <div class="text-muted small">
                    Usuario: <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->role }})
                </div>
            </div>

            <!-- Aquí se inyectará el contenido de las otras vistas -->
            @yield('content')
        </main>
    </div>
</div>

<script src="https://jsdelivr.net"></script>
</body>
</html>
