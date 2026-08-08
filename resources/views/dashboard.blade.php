<h1>Bienvenido al Panel del Hotel, {{ Auth::user()->name }} ({{ Auth::user()->role }})</h1>
<form action="{{ route('logout') }}" method="POST">@csrf <button type="submit">Cerrar Sesión</button></form>

