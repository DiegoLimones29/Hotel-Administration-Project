<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - Gestión Hotelera</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; }
        .badge { font-size: .68rem; padding: .2rem .55rem; border-radius: .2rem; font-weight: 600; letter-spacing: .01em; }
        .flat-tab { font-size: .8rem; color: #78716C; padding: .6rem .9rem; border-bottom: 2px solid transparent; }
        .flat-tab:hover { color: #1C1C1C; }
        .flat-tab.active { color: #1C1C1C; border-bottom-color: #991B1B; font-weight: 600; }
        .nav-link { font-size: .82rem; color: #78716C; padding: .5rem .9rem; border-radius: .2rem; }
        .nav-link:hover { background: #F5F5F4; color: #1C1C1C; }
        .nav-link.active { background: #1C1C1C; color: #fff; }
        .btn-primary { background: #991B1B; color: #fff; }
        .btn-primary:hover { background: #7F1D1D; }
        .field-label { font-size: .68rem; text-transform: uppercase; letter-spacing: .04em; color: #78716C; font-weight: 600; display: block; margin-bottom: .2rem; }
        ::-webkit-scrollbar { width: 7px; }
        ::-webkit-scrollbar-thumb { background: #D6D3D1; border-radius: 999px; }
    </style>
</head>
<body class="bg-stone-50 min-h-screen text-stone-900">

    <!-- ===================== TOP BAR ===================== -->
    <header class="bg-white border-b border-stone-200">
        <div class="max-w-[1400px] mx-auto px-6 py-3 flex items-center justify-between gap-6">

            <div class="flex items-center gap-3 shrink-0">
                <div class="w-8 h-8 rounded-sm bg-stone-900 flex items-center justify-center">
                    <i data-lucide="hotel" class="text-white" style="width:16px;height:16px"></i>
                </div>
                <p class="text-sm font-semibold tracking-tight">Sistema de Gestión Hotelera</p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <div class="flex items-center gap-2 border-l border-stone-200 pl-4">
                    <div class="w-8 h-8 rounded-sm bg-stone-900 text-white flex items-center justify-center text-xs font-semibold" id="userInitial"></div>
                    <div class="hidden sm:block leading-tight">
                        <p id="userName" class="text-xs font-semibold"></p>
                        <p id="userRole" class="text-[.68rem] uppercase tracking-wider text-stone-400"></p>
                    </div>
                    <button onclick="logout()" title="Cerrar sesión" class="ml-1 text-stone-400 hover:text-stone-800">
                        <i data-lucide="log-out" style="width:16px;height:16px"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Nav secundaria -->
        <div class="max-w-[1400px] mx-auto px-6 flex gap-1 border-t border-stone-100">
            <button class="nav-link active" data-tab="dashboard">Dashboard</button>
            <button class="nav-link" data-tab="rooms">Habitaciones</button>
            <button class="nav-link" data-tab="checkinout">Check-in / Check-out</button>
            <button class="nav-link" data-tab="services">Servicios</button>
            <button class="nav-link" data-tab="guests">Huéspedes</button>
        </div>
    </header>

    <div class="max-w-[1400px] mx-auto px-6 py-6">

        <p id="globalMsg" class="mb-4 text-sm hidden px-4 py-2.5 rounded-sm border"></p>

        <!-- ===================== DASHBOARD (incluye Reservaciones) ===================== -->
        <section id="tab-dashboard" class="tab-content space-y-6">

            <!-- Grid 8/4 -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Columna izquierda: Reservaciones -->
                <div class="lg:col-span-8 bg-white border border-stone-200 rounded-md">
                    <div class="flex items-center justify-between px-5 pt-4">
                        <h3 class="text-sm font-semibold tracking-tight">Gestión de Reservaciones</h3>
                        <button onclick="openReservationForm()" class="btn-primary text-xs font-medium px-3 py-1.5 rounded-sm">+ Nueva Reservación</button>
                    </div>

                    <div class="flex gap-1 px-5 mt-3 border-b border-stone-100">
                        <button class="flat-tab active" data-status-filter="">Todas</button>
                        <button class="flat-tab" data-status-filter="pending">Pendientes por check-in</button>
                        <button class="flat-tab" data-status-filter="in_progress">Ocupadas</button>
                        <button class="flat-tab" data-status-filter="completed">Check-out</button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left border-b border-stone-100">
                                    <th class="px-5 py-2.5 text-xs uppercase tracking-wider text-stone-500 font-medium">Código</th>
                                    <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Huésped</th>
                                    <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Habitación</th>
                                    <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Fechas</th>
                                    <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Estado</th>
                                    <th class="px-5 text-xs uppercase tracking-wider text-stone-500 font-medium">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="reservationsTable"></tbody>
                        </table>
                    </div>

                    <!-- Formulario nueva reservación (colapsable) -->
                    <div id="reservationFormWrap" class="hidden border-t border-stone-100 px-5 py-4">
                        <p class="text-xs uppercase tracking-wider text-stone-500 mb-3 font-medium">Nueva reservación</p>

                        <div class="mb-3">
                            <label class="field-label">Buscar huésped por correo</label>
                            <div class="flex gap-2">
                                <input id="reservationGuestSearch" type="text" placeholder="correo@ejemplo.com"
                                    class="flex-1 border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                                <button type="button" onclick="searchGuestFor('reservation')" class="bg-stone-900 text-white text-xs px-3 py-2 rounded-sm font-medium">Buscar</button>
                            </div>
                            <div id="reservationGuestResults" class="mt-2 space-y-1"></div>
                        </div>

                        <form id="formReservation" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <div>
                                <label class="field-label">Huésped seleccionado</label>
                                <input name="user_id" id="reservationUserId" type="number" placeholder="Busca arriba por correo" required readonly
                                    class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-100 text-stone-500">
                            </div>
                            <div>
                                <label class="field-label">Habitación</label>
                                <select name="room_id" id="reservationRoomSelect" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                                    <option value="">Selecciona...</option>
                                </select>
                            </div>
                            <div>
                                <label class="field-label">Número de huéspedes</label>
                                <input name="num_guests" type="number" min="1" value="1" class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                            </div>
                            <div>
                                <label class="field-label">Fecha de entrada</label>
                                <input name="check_in_date" type="date" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                            </div>
                            <div>
                                <label class="field-label">Fecha de salida</label>
                                <input name="check_out_date" type="date" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                            </div>
                            <div class="flex items-end">
                                <button class="btn-primary rounded-sm px-4 py-2 text-sm font-medium w-full">Reservar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Columna derecha: widgets -->
                <div class="lg:col-span-4 space-y-6">

                    <div class="bg-white border border-stone-200 rounded-md p-5">
                        <p class="text-xs uppercase tracking-wider text-stone-500 mb-3 font-medium">Estado de habitaciones</p>
                        <div id="roomStatusWidget" class="space-y-2 text-sm"></div>
                    </div>

                    <div class="bg-white border border-stone-200 rounded-md p-5">
                        <p class="text-xs uppercase tracking-wider text-stone-500 mb-3 font-medium">Servicios solicitados pendientes</p>
                        <div id="pendingServicesWidget" class="space-y-2 text-sm">
                            <p class="text-stone-400 text-xs">Cargando...</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ===================== HABITACIONES ===================== -->
        <section id="tab-rooms" class="tab-content hidden space-y-6">

            <div data-role="admin" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border border-stone-200 rounded-md p-5">
                    <p class="text-xs uppercase tracking-wider text-stone-500 mb-3 font-medium">Crear tipo de habitación</p>
                    <form id="formRoomType" class="space-y-2">
                        <div>
                            <label class="field-label">Tipo</label>
                            <input name="type" placeholder="Ej. Suite" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        </div>
                        <div>
                            <label class="field-label">Capacidad</label>
                            <input name="capacity" type="number" min="1" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        </div>
                        <div>
                            <label class="field-label">Precio por noche</label>
                            <input name="price_per_night" type="number" step="0.01" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        </div>
                        <div>
                            <label class="field-label">Descripción</label>
                            <textarea name="description" class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50"></textarea>
                        </div>
                        <button class="bg-stone-900 text-white rounded-sm px-4 py-2 text-sm font-medium">Crear tipo</button>
                    </form>
                </div>

                <div class="bg-white border border-stone-200 rounded-md p-5">
                    <p class="text-xs uppercase tracking-wider text-stone-500 mb-3 font-medium">Registrar habitación</p>
                    <form id="formRoom" class="space-y-2">
                        <div>
                            <label class="field-label">Tipo de habitación</label>
                            <select name="room_type_id" id="roomTypeSelect" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                                <option value="">Selecciona un tipo...</option>
                            </select>
                        </div>
                        <div>
                            <label class="field-label">Número de habitación</label>
                            <input name="room_number" type="number" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        </div>
                        <div>
                            <label class="field-label">Piso</label>
                            <input name="room_floor" type="number" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        </div>
                        <button class="bg-stone-900 text-white rounded-sm px-4 py-2 text-sm font-medium">Registrar</button>
                    </form>
                </div>
            </div>

            <div data-role="admin" class="bg-white border border-stone-200 rounded-md p-5">
                <p class="text-xs uppercase tracking-wider text-stone-500 mb-3 font-medium">Fotos por tipo de habitación</p>
                <form id="formRoomImage" class="flex flex-wrap items-end gap-2 mb-4">
                    <div>
                        <label class="field-label">Tipo de habitación</label>
                        <select name="room_type_id" id="imageRoomTypeSelect" required class="border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                            <option value="">Selecciona un tipo...</option>
                        </select>
                    </div>
                    <div>
                        <label class="field-label">Imagen (JPG/PNG, máx. 5MB)</label>
                        <input type="file" name="image" accept="image/jpeg,image/png" required class="text-sm">
                    </div>
                    <button class="btn-primary rounded-sm px-4 py-2 text-sm font-medium">Subir foto</button>
                </form>
                <div id="roomImagesPreview" class="flex flex-wrap gap-3"></div>
            </div>

            <div class="bg-white border border-stone-200 rounded-md">
                <div class="flex justify-between items-center px-5 pt-4 pb-2">
                    <p class="text-xs uppercase tracking-wider text-stone-500 font-medium">Estado de habitaciones</p>
                    <button onclick="loadRooms()" class="text-xs text-stone-500 hover:text-stone-900">Refrescar</button>
                </div>
                <table class="w-full text-sm">
                    <thead><tr class="text-left border-b border-stone-100">
                        <th class="px-5 py-2 text-xs uppercase tracking-wider text-stone-500 font-medium">#</th>
                        <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Tipo</th>
                        <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Piso</th>
                        <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Estado</th>
                        <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Precio/noche</th>
                        <th data-role="admin" class="px-5"></th>
                    </tr></thead>
                    <tbody id="roomsTable"></tbody>
                </table>
            </div>
        </section>

        <!-- ===================== CHECK-IN / CHECK-OUT ===================== -->
        <section id="tab-checkinout" class="tab-content hidden space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border border-stone-200 rounded-md p-5">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-xs uppercase tracking-wider text-stone-500 font-medium">Check-ins de hoy</p>
                        <button onclick="loadTodayCheckIns()" class="text-xs text-stone-500 hover:text-stone-900">Refrescar</button>
                    </div>
                    <div id="todayCheckInsList" class="space-y-2 text-sm"></div>
                </div>
                <div class="bg-white border border-stone-200 rounded-md p-5">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-xs uppercase tracking-wider text-stone-500 font-medium">Check-outs de hoy</p>
                        <button onclick="loadTodayCheckOuts()" class="text-xs text-stone-500 hover:text-stone-900">Refrescar</button>
                    </div>
                    <div id="todayCheckOutsList" class="space-y-2 text-sm"></div>
                </div>
            </div>

            <div class="bg-white border border-stone-200 rounded-md p-5">
                <div class="flex justify-between items-center mb-3">
                    <p class="text-xs uppercase tracking-wider text-stone-500 font-medium">Estadías activas (check-out anticipado)</p>
                    <button onclick="loadActiveStaysCheckout()" class="text-xs text-stone-500 hover:text-stone-900">Refrescar</button>
                </div>
                <div id="activeStaysCheckoutList" class="space-y-2 text-sm"></div>
            </div>

            <div class="bg-white border border-stone-200 rounded-md p-5">
                <p class="text-xs uppercase tracking-wider text-stone-500 mb-3 font-medium">Walk-in (sin reservación previa)</p>

                <div class="mb-3">
                    <label class="field-label">Buscar huésped por correo</label>
                    <div class="flex gap-2 max-w-md">
                        <input id="walkinGuestSearch" type="text" placeholder="correo@ejemplo.com"
                            class="flex-1 border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        <button type="button" onclick="searchGuestFor('walkin')" class="bg-stone-900 text-white text-xs px-3 py-2 rounded-sm font-medium">Buscar</button>
                    </div>
                    <div id="walkinGuestResults" class="mt-2 space-y-1"></div>
                </div>

                <form id="formWalkIn" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div>
                        <label class="field-label">Huésped seleccionado</label>
                        <input name="user_id" id="walkinUserId" type="number" placeholder="Busca arriba por correo" required readonly
                            class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-100 text-stone-500">
                    </div>
                    <div>
                        <label class="field-label">Habitación disponible</label>
                        <select name="room_id" id="walkinRoomSelect" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                            <option value="">Selecciona...</option>
                        </select>
                    </div>
                    <div>
                        <label class="field-label">Número de huéspedes</label>
                        <input name="num_guests" type="number" min="1" value="1" class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                    </div>
                    <div>
                        <label class="field-label">Fecha de salida</label>
                        <input name="check_out_date" type="date" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                    </div>
                    <button class="btn-primary rounded-sm px-4 py-2 text-sm font-medium col-span-2 md:col-span-4">Registrar walk-in</button>
                </form>
            </div>
        </section>

        <!-- ===================== SERVICIOS ===================== -->
        <section id="tab-services" class="tab-content hidden space-y-6">
            <div class="bg-white border border-stone-200 rounded-md p-5">
                <p class="text-xs uppercase tracking-wider text-stone-500 mb-3 font-medium">Registrar servicio en catálogo</p>
                <form id="formService" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div>
                        <label class="field-label">Nombre</label>
                        <input name="name" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                    </div>
                    <div>
                        <label class="field-label">Precio</label>
                        <input name="price" type="number" step="0.01" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                    </div>
                    <div>
                        <label class="field-label">Descripción</label>
                        <input name="description" class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                    </div>
                    <div class="flex items-end">
                        <button class="bg-stone-900 text-white rounded-sm px-4 py-2 text-sm font-medium w-full">Agregar al catálogo</button>
                    </div>
                </form>
            </div>

            <div class="bg-white border border-stone-200 rounded-md">
                <div class="flex justify-between items-center px-5 pt-4 pb-2">
                    <p class="text-xs uppercase tracking-wider text-stone-500 font-medium">Catálogo de servicios</p>
                    <button onclick="loadServices()" class="text-xs text-stone-500 hover:text-stone-900">Refrescar</button>
                </div>
                <table class="w-full text-sm">
                    <thead><tr class="text-left border-b border-stone-100">
                        <th class="px-5 py-2 text-xs uppercase tracking-wider text-stone-500 font-medium">Nombre</th>
                        <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Precio</th>
                        <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Estado</th>
                        <th class="px-5"></th>
                    </tr></thead>
                    <tbody id="servicesTable"></tbody>
                </table>
            </div>

            <div class="bg-white border border-stone-200 rounded-md p-5">
                <p class="text-xs uppercase tracking-wider text-stone-500 mb-3 font-medium">Asignar servicio a una estadía activa</p>
                <form id="formAssignService" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div>
                        <label class="field-label">ID de reservación</label>
                        <input name="reservation_id" id="assignReservationId" type="number" placeholder="Busca abajo o escribe el ID" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                    </div>
                    <div>
                        <label class="field-label">Servicio</label>
                        <select name="service_id" id="assignServiceSelect" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                            <option value="">Servicio...</option>
                        </select>
                    </div>
                    <div>
                        <label class="field-label">Cantidad</label>
                        <input name="quantity" type="number" min="1" value="1" class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                    </div>
                    <div class="flex items-end">
                        <button class="btn-primary rounded-sm px-4 py-2 text-sm font-medium w-full">Asignar</button>
                    </div>
                </form>
            </div>

            <div class="bg-white border border-stone-200 rounded-md">
                <div class="flex justify-between items-center px-5 pt-4 pb-2">
                    <p class="text-xs uppercase tracking-wider text-stone-500 font-medium">Estadías activas</p>
                    <div class="flex items-center gap-2">
                        <input id="activeStaysSearch" placeholder="Buscar por correo del huésped..." class="border border-stone-200 rounded-sm px-2 py-1 text-xs bg-stone-50 w-56">
                        <button onclick="loadActiveStays()" class="text-xs text-stone-500 hover:text-stone-900">Refrescar</button>
                    </div>
                </div>
                <table class="w-full text-sm">
                    <thead><tr class="text-left border-b border-stone-100">
                        <th class="px-5 py-2 text-xs uppercase tracking-wider text-stone-500 font-medium">ID Reservación</th>
                        <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Habitación</th>
                        <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Huésped</th>
                        <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Correo</th>
                        <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Fechas</th>
                        <th class="px-5"></th>
                    </tr></thead>
                    <tbody id="activeStaysTable"></tbody>
                </table>
            </div>
        </section>

        <!-- ===================== HUÉSPEDES ===================== -->
        <section id="tab-guests" class="tab-content hidden space-y-6">

            <div data-role="admin" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white border border-stone-200 rounded-md p-5">
                    <p class="text-xs uppercase tracking-wider text-stone-500 mb-3 font-medium">Registrar huésped</p>
                    <form id="formGuest" class="space-y-2">
                        <div>
                            <label class="field-label">Nombre completo</label>
                            <input name="name" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        </div>
                        <div>
                            <label class="field-label">Correo</label>
                            <input name="email" type="email" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        </div>
                        <div>
                            <label class="field-label">Teléfono</label>
                            <input name="phone" class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        </div>
                        <div>
                            <label class="field-label">Contraseña temporal</label>
                            <input name="password" type="password" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        </div>
                        <button class="btn-primary rounded-sm px-4 py-2 text-sm font-medium w-full">Registrar huésped</button>
                    </form>
                </div>

                <div class="bg-white border border-stone-200 rounded-md p-5">
                    <p class="text-xs uppercase tracking-wider text-stone-500 mb-3 font-medium">Registrar recepcionista</p>
                    <form id="formStaff" class="space-y-2">
                        <div>
                            <label class="field-label">Nombre completo</label>
                            <input name="name" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        </div>
                        <div>
                            <label class="field-label">Correo</label>
                            <input name="email" type="email" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        </div>
                        <div>
                            <label class="field-label">Teléfono</label>
                            <input name="phone" class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        </div>
                        <div>
                            <label class="field-label">Contraseña temporal</label>
                            <input name="password" type="password" required class="w-full border border-stone-200 rounded-sm px-3 py-2 text-sm bg-stone-50">
                        </div>
                        <button class="bg-stone-900 text-white rounded-sm px-4 py-2 text-sm font-medium w-full">Registrar recepcionista</button>
                    </form>
                </div>
            </div>

            <div class="bg-white border border-stone-200 rounded-md">
                <div class="flex justify-between items-center px-5 pt-4 pb-2">
                    <p class="text-xs uppercase tracking-wider text-stone-500 font-medium">Buscar huésped</p>
                    <div class="flex items-center gap-2">
                        <input id="guestSearch" placeholder="Nombre o correo..." class="border border-stone-200 rounded-sm px-2 py-1 text-xs bg-stone-50">
                        <button onclick="loadGuests()" class="text-xs text-stone-500 hover:text-stone-900">Refrescar</button>
                    </div>
                </div>
                <table class="w-full text-sm">
                    <thead><tr class="text-left border-b border-stone-100">
                        <th class="px-5 py-2 text-xs uppercase tracking-wider text-stone-500 font-medium">ID</th>
                        <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Nombre</th>
                        <th class="text-xs uppercase tracking-wider text-stone-500 font-medium">Correo</th>
                        <th class="px-5 text-xs uppercase tracking-wider text-stone-500 font-medium">Teléfono</th>
                    </tr></thead>
                    <tbody id="guestsTable"></tbody>
                </table>
            </div>
        </section>

    </div>

    <script>
        lucide.createIcons();

        const token = localStorage.getItem('token');
        const user = JSON.parse(localStorage.getItem('user') || 'null');
        if (!token) window.location.href = '/login';

        const isAdmin = user?.role === 'admin';
        document.getElementById('userName').textContent = user?.name ?? '';
        document.getElementById('userRole').textContent = isAdmin ? 'Administrador' : 'Recepcionista';
        document.getElementById('userInitial').textContent = (user?.name ?? '?').charAt(0).toUpperCase();

        if (!isAdmin) {
            document.querySelectorAll('[data-role="admin"]').forEach(el => el.remove());
        }

        function logout() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }

        async function api(path, options = {}) {
            const res = await fetch('/api' + path, {
                ...options,
                headers: {
                    ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    ...(options.headers || {})
                }
            });
            if (res.status === 401) { logout(); return; }
            const data = await res.json();
            return { ok: res.ok, status: res.status, data };
        }

        function showMsg(text, ok = true) {
            const el = document.getElementById('globalMsg');
            el.textContent = text;
            el.className = 'mb-4 text-sm px-4 py-2.5 rounded-sm border ' + (ok ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-red-50 text-red-800 border-red-200');
            el.classList.remove('hidden');
            setTimeout(() => el.classList.add('hidden'), 4000);
        }

        function badge(status) {
            const map = {
                pending: ['bg-amber-50 text-amber-800', 'Pendiente'],
                confirmed: ['bg-blue-50 text-blue-800', 'Confirmada'],
                in_progress: ['bg-emerald-50 text-emerald-800', 'En curso'],
                completed: ['bg-stone-100 text-stone-500', 'Completada'],
                cancelled: ['bg-red-50 text-red-700', 'Cancelada'],
            };
            const [cls, label] = map[status] ?? ['bg-stone-100 text-stone-500', status];
            return `<span class="badge ${cls}">${label}</span>`;
        }

        // ---------- NAV ----------
        function switchTab(tab) {
            document.querySelectorAll('.nav-link').forEach(b => b.classList.toggle('active', b.dataset.tab === tab));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.toggle('hidden', c.id !== 'tab-' + tab));

            if (tab === 'dashboard') loadDashboard();
            if (tab === 'rooms') loadRooms();
            if (tab === 'checkinout') { loadTodayCheckIns(); loadTodayCheckOuts(); loadActiveStaysCheckout(); }
            if (tab === 'services') { loadServices(); loadActiveStays(); }
            if (tab === 'guests') loadGuests();
        }
        document.querySelectorAll('.nav-link').forEach(btn => btn.addEventListener('click', () => switchTab(btn.dataset.tab)));

        function openReservationForm() {
            document.getElementById('reservationFormWrap').classList.remove('hidden');
            document.getElementById('reservationFormWrap').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // ---------- BÚSQUEDA DE HUÉSPED POR CORREO (reutilizable) ----------
        async function searchGuestFor(context) {
            const searchInput = document.getElementById(context + 'GuestSearch');
            const resultsDiv = document.getElementById(context + 'GuestResults');
            const term = searchInput.value.trim();

            if (!term) { resultsDiv.innerHTML = ''; return; }

            const res = await api('/guests?search=' + encodeURIComponent(term));
            const list = res?.data?.data ?? [];

            resultsDiv.innerHTML = list.length
                ? list.map(g => `
                    <div class="flex items-center justify-between border border-stone-200 rounded-sm px-3 py-1.5 text-xs">
                        <span>${g.name} · ${g.email}</span>
                        <button type="button" onclick="selectGuestFor('${context}', ${g.id}, '${g.name.replace(/'/g, "\\'")}')" class="text-red-700 font-medium hover:underline">Usar</button>
                    </div>
                `).join('')
                : '<p class="text-xs text-stone-400">Sin resultados</p>';
        }

        function selectGuestFor(context, id, name) {
            document.getElementById(context + 'UserId').value = id;
            document.getElementById(context + 'GuestResults').innerHTML = `<p class="text-xs text-emerald-700">Seleccionado: ${name} (ID ${id})</p>`;
        }

        // ---------- DASHBOARD ----------
        let currentStatusFilter = '';

        document.querySelectorAll('.flat-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.flat-tab').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentStatusFilter = btn.dataset.statusFilter;
                loadReservationsTable();
            });
        });

        async function loadDashboard() {
            await loadRoomsForDashboard();
            await loadReservationsTable();
            await loadPendingServicesWidget();
        }

        async function loadRoomsForDashboard() {
            const res = await api('/rooms');
            const rooms = res?.data?.data ?? [];

            const counts = {};
            rooms.forEach(r => counts[r.state] = (counts[r.state] || 0) + 1);
            const labels = { available: ['Disponible', 'bg-emerald-500'], reserved: ['Reservada', 'bg-amber-500'], occupied: ['Ocupada', 'bg-red-500'], 'out of service': ['Fuera de servicio', 'bg-stone-400'], 'on maintenance': ['Mantenimiento', 'bg-stone-400'] };

            document.getElementById('roomStatusWidget').innerHTML = Object.entries(labels).map(([key, [label, dot]]) => `
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-stone-600"><span class="w-1.5 h-1.5 rounded-full ${dot}"></span>${label}</span>
                    <span class="font-semibold">${counts[key] || 0}</span>
                </div>
            `).join('');
        }

        async function loadPendingServicesWidget() {
            const resRes = await api('/reservations?status=in_progress');
            const active = resRes?.data?.data ?? [];

            const all = [];
            for (const r of active) {
                const svcRes = await api(`/reservations/${r.id}/services`);
                const pending = (svcRes?.data?.data ?? []).filter(s => s.status === 'solicitado');
                pending.forEach(p => all.push({ room: r.room?.room_number, guest: r.guest?.name ?? r.user_id, name: p.service?.name }));
            }

            document.getElementById('pendingServicesWidget').innerHTML = all.length
                ? all.map(a => `
                    <div class="flex items-start gap-2 border-b border-stone-100 pb-2 last:border-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mt-1.5"></span>
                        <div>
                            <p class="text-stone-800">${a.name}</p>
                            <p class="text-xs text-stone-400">Hab. ${a.room ?? ''} · ${a.guest}</p>
                        </div>
                    </div>
                `).join('')
                : '<p class="text-stone-400 text-xs">Sin solicitudes pendientes</p>';
        }

        async function loadReservationsTable() {
            const query = currentStatusFilter ? `?status=${currentStatusFilter}` : '';
            const res = await api('/reservations' + query);
            const list = res?.data?.data ?? [];

            document.getElementById('reservationsTable').innerHTML = list.map(r => `
                <tr class="border-b border-stone-100 last:border-0">
                    <td class="px-5 py-2.5 font-mono text-xs text-stone-500">#${r.id}</td>
                    <td>${r.guest?.name ?? r.user_id}</td>
                    <td>Hab. ${r.room?.room_number ?? ''} · ${r.room?.room_type?.type ?? ''}</td>
                    <td class="text-xs text-stone-500">${r.check_in_date} → ${r.check_out_date}</td>
                    <td>${badge(r.status)}</td>
                    <td class="px-5 space-x-2 whitespace-nowrap">
                        ${!['completed', 'cancelled', 'in_progress'].includes(r.status) ? `<button onclick="cancelReservation(${r.id})" class="text-red-700 text-xs hover:underline">Cancelar</button>` : ''}
                        <button onclick="switchTab('checkinout')" class="text-stone-500 text-xs hover:underline">Detalles</button>
                    </td>
                </tr>
            `).join('') || `<tr><td class="px-5 py-4 text-stone-400 text-sm" colspan="6">Sin resultados</td></tr>`;
        }

        async function cancelReservation(id) {
            const reason = prompt('Motivo de cancelación:');
            if (!reason) return;
            const res = await api(`/reservations/${id}/cancelar`, { method: 'PATCH', body: JSON.stringify({ cancellation_reason: reason }) });
            showMsg(res.data.message, res.ok);
            loadReservationsTable();
            loadRoomsForDashboard();
        }

        document.getElementById('formReservation').addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            const res = await api('/reservations', { method: 'POST', body: JSON.stringify(body) });
            showMsg(res.data.message, res.ok);
            if (res.ok) {
                e.target.reset();
                document.getElementById('reservationGuestResults').innerHTML = '';
                loadReservationsTable();
                loadRoomsForDashboard();
            }
        });

        // ---------- HABITACIONES ----------
        async function loadRoomTypesIntoSelects() {
            const res = await api('/obtenerRoomTypes');
            const types = res?.data?.data ?? [];
            const opts = types.map(t => `<option value="${t.id}">${t.type} (cap. ${t.capacity}) - $${t.price_per_night}</option>`).join('');

            const rts = document.getElementById('roomTypeSelect');
            if (rts) rts.innerHTML = '<option value="">Selecciona un tipo...</option>' + opts;

            const its = document.getElementById('imageRoomTypeSelect');
            if (its) {
                its.innerHTML = '<option value="">Selecciona un tipo...</option>' + opts;
                if (types[0]) loadRoomImages(types[0].id);
            }
        }

        async function loadRoomImages(roomTypeId) {
            const preview = document.getElementById('roomImagesPreview');
            if (!preview || !roomTypeId) return;
            const res = await api(`/room-types/${roomTypeId}/imagenes`);
            const images = res?.data?.data ?? [];
            preview.innerHTML = images.map(img => `<img src="${img.img_url}" class="w-24 h-24 object-cover rounded-sm border border-stone-200">`).join('')
                || '<p class="text-xs text-stone-400">Este tipo aún no tiene fotos</p>';
        }
        document.getElementById('imageRoomTypeSelect')?.addEventListener('change', (e) => loadRoomImages(e.target.value));

        async function loadRooms() {
            await loadRoomTypesIntoSelects();
            const res = await api('/rooms');
            const rooms = res?.data?.data ?? [];
            const dotMap = { available: 'bg-emerald-500', reserved: 'bg-amber-500', occupied: 'bg-red-500', 'out of service': 'bg-stone-400', 'on maintenance': 'bg-stone-400' };

            document.getElementById('roomsTable').innerHTML = rooms.map(r => `
                <tr class="border-b border-stone-100 last:border-0">
                    <td class="px-5 py-2.5">${r.room_number}</td>
                    <td>${r.room_type?.type ?? ''}</td>
                    <td>${r.room_floor}</td>
                    <td><span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full ${dotMap[r.state] || 'bg-stone-400'}"></span>${r.state}</span></td>
                    <td>$${r.room_type?.price_per_night ?? ''}</td>
                    <td class="px-5">
                        ${isAdmin && r.state !== 'out of service' ? `<button onclick="markOutOfService(${r.id})" class="text-red-700 text-xs hover:underline">Fuera de servicio</button>` : ''}
                    </td>
                </tr>
            `).join('');

            const availableRooms = rooms.filter(r => r.state === 'available');
            document.getElementById('reservationRoomSelect').innerHTML = '<option value="">Selecciona...</option>' + rooms.map(r => `<option value="${r.id}">#${r.room_number} - ${r.room_type?.type ?? ''}</option>`).join('');
            document.getElementById('walkinRoomSelect').innerHTML = '<option value="">Selecciona...</option>' + availableRooms.map(r => `<option value="${r.id}">#${r.room_number} - ${r.room_type?.type ?? ''}</option>`).join('');
        }

        async function markOutOfService(id) {
            const res = await api(`/rooms/${id}/fuera-de-servicio`, { method: 'PATCH' });
            showMsg(res.data.message, res.ok);
            loadRooms();
        }

        document.getElementById('formRoomType')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            const res = await api('/crearRoomType', { method: 'POST', body: JSON.stringify(body) });
            showMsg(res.data.message, res.ok);
            if (res.ok) { e.target.reset(); loadRooms(); }
        });

        document.getElementById('formRoom')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            const res = await api('/rooms', { method: 'POST', body: JSON.stringify(body) });
            showMsg(res.data.message, res.ok);
            if (res.ok) { e.target.reset(); loadRooms(); }
        });

        document.getElementById('formRoomImage')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const roomTypeId = form.room_type_id.value;
            const fd = new FormData();
            fd.append('image', form.image.files[0]);
            const res = await api(`/room-types/${roomTypeId}/imagenes`, { method: 'POST', body: fd });
            showMsg(res.data.message, res.ok);
            if (res.ok) { form.reset(); loadRoomImages(roomTypeId); }
        });

        // ---------- CHECK-IN / CHECK-OUT ----------
        async function loadTodayCheckIns() {
            const res = await api('/checkins/hoy');
            const list = res?.data?.data ?? [];
            document.getElementById('todayCheckInsList').innerHTML = list.map(r => `
                <div class="flex justify-between items-center border-b border-stone-100 pb-2 last:border-0">
                    <span>#${r.id} · Hab. ${r.room?.room_number ?? ''} · ${r.guest?.name ?? r.user_id}</span>
                    <button onclick="doCheckIn(${r.id})" class="btn-primary text-xs px-3 py-1 rounded-sm font-medium">Check-in</button>
                </div>
            `).join('') || '<p class="text-stone-400 text-sm">Sin check-ins pendientes hoy</p>';
        }

        // Mismo cálculo que hace el backend en checkOut(): noches reales
        // (desde check_in hasta hoy) por precio/noche, más servicios consumidos.
        async function estimateCheckoutTotal(r) {
            const checkIn = new Date(r.check_in_date);
            const today = new Date();
            checkIn.setHours(0, 0, 0, 0);
            today.setHours(0, 0, 0, 0);
            const nights = Math.max(Math.round((today - checkIn) / 86400000), 1);
            const pricePerNight = parseFloat(r.room?.room_type?.price_per_night ?? 0);
            const roomCost = nights * pricePerNight;

            const svcRes = await api(`/reservations/${r.id}/services`);
            const services = svcRes?.data?.data ?? [];
            const servicesCost = services.reduce((sum, s) => sum + (s.quantity * parseFloat(s.unit_price)), 0);

            const isEarly = (r.check_out_date ?? '').slice(0, 10) !== new Date().toISOString().slice(0, 10);

            return { nights, roomCost, servicesCost, total: roomCost + servicesCost, isEarly };
        }

        function totalEstimateLine(est) {
            return `<p class="text-xs text-stone-500 mt-0.5">
                Total ${est.isEarly ? 'anticipado' : 'a cobrar'}: <span class="font-semibold text-stone-900">$${est.total.toFixed(2)}</span>
                <span class="text-stone-400">(${est.nights} noche${est.nights !== 1 ? 's' : ''} · hab. $${est.roomCost.toFixed(2)}${est.servicesCost > 0 ? ` + servicios $${est.servicesCost.toFixed(2)}` : ''})</span>
            </p>`;
        }

        async function loadTodayCheckOuts() {
            const res = await api('/checkouts/hoy');
            const list = res?.data?.data ?? [];
            const withEstimates = await Promise.all(list.map(async r => ({ r, est: await estimateCheckoutTotal(r) })));

            document.getElementById('todayCheckOutsList').innerHTML = withEstimates.map(({ r, est }) => `
                <div class="border-b border-stone-100 pb-2 last:border-0">
                    <div class="flex justify-between items-center mb-0.5">
                        <span>#${r.id} · Hab. ${r.room?.room_number ?? ''} · ${r.guest?.name ?? r.user_id}</span>
                    </div>
                    ${totalEstimateLine(est)}
                    <div class="flex gap-2 mt-1.5">
                        <select id="pm-${r.id}" class="border border-stone-200 rounded-sm text-xs px-2 py-1 bg-stone-50">
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta</option>
                            <option value="transfer">Transferencia</option>
                        </select>
                        <button onclick="doCheckOut(${r.id})" class="bg-stone-900 text-white text-xs px-3 py-1 rounded-sm font-medium">Check-out</button>
                    </div>
                </div>
            `).join('') || '<p class="text-stone-400 text-sm">Sin check-outs pendientes hoy</p>';
        }

        async function doCheckIn(reservationId) {
            const res = await api(`/checkins/${reservationId}`, { method: 'POST' });
            showMsg(res.data.message, res.ok);
            loadTodayCheckIns();
            loadRooms();
        }

        async function doCheckOut(reservationId) {
            const paymentMethod = document.getElementById(`pm-${reservationId}`).value;
            const res = await api(`/checkouts/${reservationId}`, { method: 'POST', body: JSON.stringify({ payment_method: paymentMethod }) });
            showMsg(res.data.message, res.ok);
            if (res.ok) alert('Factura generada. Total: $' + res.data.data.invoice.total_cost);
            loadTodayCheckOuts();
            loadRooms();
        }

        async function loadActiveStaysCheckout() {
            const res = await api('/reservations?status=in_progress');
            const list = res?.data?.data ?? [];
            const today = new Date().toISOString().slice(0, 10);

            // Las que ya salen hoy se manejan en "Check-outs de hoy" arriba;
            // aquí mostramos las que aún no llegan a su fecha programada.
            const early = list.filter(r => (r.check_out_date ?? '').slice(0, 10) !== today);
            const withEstimates = await Promise.all(early.map(async r => ({ r, est: await estimateCheckoutTotal(r) })));

            document.getElementById('activeStaysCheckoutList').innerHTML = withEstimates.map(({ r, est }) => `
                <div class="border-b border-stone-100 pb-2 last:border-0">
                    <div class="flex justify-between items-center mb-0.5">
                        <span>#${r.id} · Hab. ${r.room?.room_number ?? ''} · ${r.guest?.name ?? r.user_id}</span>
                        <span class="text-xs text-stone-400">Salida programada: ${(r.check_out_date ?? '').slice(0, 10)}</span>
                    </div>
                    ${totalEstimateLine(est)}
                    <div class="flex gap-2 mt-1.5">
                        <select id="pme-${r.id}" class="border border-stone-200 rounded-sm text-xs px-2 py-1 bg-stone-50">
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta</option>
                            <option value="transfer">Transferencia</option>
                        </select>
                        <button onclick="doEarlyCheckOut(${r.id})" class="bg-stone-900 text-white text-xs px-3 py-1 rounded-sm font-medium">Check-out anticipado</button>
                    </div>
                </div>
            `).join('') || '<p class="text-stone-400 text-sm">Sin estadías activas fuera de las de hoy</p>';
        }

        async function doEarlyCheckOut(reservationId) {
            if (!confirm('¿Confirmas el check-out anticipado? Solo se cobrarán las noches realmente hospedadas.')) return;
            const paymentMethod = document.getElementById(`pme-${reservationId}`).value;
            const res = await api(`/checkouts/${reservationId}`, { method: 'POST', body: JSON.stringify({ payment_method: paymentMethod }) });
            showMsg(res.data.message, res.ok);
            if (res.ok) alert('Factura generada. Total: $' + res.data.data.invoice.total_cost);
            loadActiveStaysCheckout();
            loadTodayCheckOuts();
            loadRooms();
        }

        document.getElementById('formWalkIn')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            const res = await api('/checkins/walkin', { method: 'POST', body: JSON.stringify(body) });
            showMsg(res.data.message, res.ok);
            if (res.ok) {
                e.target.reset();
                document.getElementById('walkinGuestResults').innerHTML = '';
                loadRooms();
                loadTodayCheckOuts();
            }
        });

        // ---------- SERVICIOS ----------
        async function loadServices() {
            const res = await api('/services');
            const list = res?.data?.data ?? [];

            document.getElementById('servicesTable').innerHTML = list.map(s => `
                <tr class="border-b border-stone-100 last:border-0">
                    <td class="px-5 py-2.5">${s.name}</td>
                    <td>$${s.price}</td>
                    <td><span class="badge ${s.active ? 'bg-emerald-50 text-emerald-800' : 'bg-stone-100 text-stone-500'}">${s.active ? 'Activo' : 'Inactivo'}</span></td>
                    <td class="px-5">
                        ${s.active
                            ? `<button onclick="deactivateService(${s.id})" class="text-red-700 text-xs hover:underline">Desactivar</button>`
                            : `<button onclick="activateService(${s.id})" class="text-emerald-700 text-xs hover:underline">Activar</button>`}
                    </td>
                </tr>
            `).join('');

            const opts = list.filter(s => s.active).map(s => `<option value="${s.id}">${s.name} - $${s.price}</option>`).join('');
            document.getElementById('assignServiceSelect').innerHTML = '<option value="">Servicio...</option>' + opts;
        }

        async function deactivateService(id) {
            const res = await api(`/services/${id}/desactivar`, { method: 'PATCH' });
            showMsg(res.data.message, res.ok);
            loadServices();
        }

        async function activateService(id) {
            const res = await api(`/services/${id}/activar`, { method: 'PATCH' });
            showMsg(res.data.message, res.ok);
            loadServices();
        }

        document.getElementById('formService')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            const res = await api('/services', { method: 'POST', body: JSON.stringify(body) });
            showMsg(res.data.message, res.ok);
            if (res.ok) { e.target.reset(); loadServices(); }
        });

        document.getElementById('formAssignService')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            const res = await api('/reservation-services', { method: 'POST', body: JSON.stringify(body) });
            showMsg(res.data.message, res.ok);
            if (res.ok) e.target.reset();
        });

        // ---------- ESTADÍAS ACTIVAS (para asignar servicios) ----------
        let activeStaysCache = [];

        async function loadActiveStays() {
            const res = await api('/reservations?status=in_progress');
            activeStaysCache = res?.data?.data ?? [];
            renderActiveStays(activeStaysCache);
        }

        function renderActiveStays(list) {
            document.getElementById('activeStaysTable').innerHTML = list.map(r => `
                <tr class="border-b border-stone-100 last:border-0">
                    <td class="px-5 py-2.5 font-mono text-xs text-stone-500">#${r.id}</td>
                    <td>Hab. ${r.room?.room_number ?? ''}</td>
                    <td>${r.guest?.name ?? r.user_id}</td>
                    <td class="text-xs text-stone-500">${r.guest?.email ?? '—'}</td>
                    <td class="text-xs text-stone-500">${r.check_in_date} → ${r.check_out_date}</td>
                    <td class="px-5">
                        <button onclick="useReservationForService(${r.id})" class="text-red-700 text-xs hover:underline">Usar</button>
                    </td>
                </tr>
            `).join('') || `<tr><td class="px-5 py-4 text-stone-400 text-sm" colspan="6">Sin estadías activas</td></tr>`;
        }

        function useReservationForService(id) {
            document.getElementById('assignReservationId').value = id;
            document.getElementById('assignReservationId').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        document.getElementById('activeStaysSearch')?.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            const filtered = activeStaysCache.filter(r => (r.guest?.email ?? '').toLowerCase().includes(term));
            renderActiveStays(filtered);
        });

        // ---------- HUÉSPEDES ----------
        async function loadGuests() {
            const search = document.getElementById('guestSearch')?.value ?? '';
            const query = search ? `?search=${encodeURIComponent(search)}` : '';
            const res = await api('/guests' + query);
            const list = res?.data?.data ?? [];

            document.getElementById('guestsTable').innerHTML = list.map(g => `
                <tr class="border-b border-stone-100 last:border-0">
                    <td class="px-5 py-2.5 font-mono text-xs text-stone-500">${g.id}</td>
                    <td>${g.name}</td>
                    <td>${g.email}</td>
                    <td class="px-5">${g.phone ?? '—'}</td>
                </tr>
            `).join('') || `<tr><td class="px-5 py-4 text-stone-400 text-sm" colspan="4">Sin resultados</td></tr>`;
        }

        document.getElementById('guestSearch')?.addEventListener('input', () => {
            clearTimeout(window._guestSearchTimeout);
            window._guestSearchTimeout = setTimeout(loadGuests, 350);
        });

        document.getElementById('formGuest')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            const res = await api('/guests', { method: 'POST', body: JSON.stringify(body) });
            showMsg(res.data.message, res.ok);
            if (res.ok) { e.target.reset(); loadGuests(); }
        });

        document.getElementById('formStaff')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = Object.fromEntries(new FormData(e.target).entries());
            const res = await api('/staff', { method: 'POST', body: JSON.stringify(body) });
            showMsg(res.data.message, res.ok);
            if (res.ok) e.target.reset();
        });

        // ---------- INIT ----------
        loadDashboard();
    </script>

</body>
</html>
