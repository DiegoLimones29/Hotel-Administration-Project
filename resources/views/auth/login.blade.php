<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Gestión de Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-stone-50 flex items-center justify-center min-h-screen text-stone-900" style="font-family: 'Segoe UI', system-ui, sans-serif;">

    <div class="w-full max-w-sm">
        <div class="flex items-center gap-3 justify-center mb-6">
            <div class="w-9 h-9 rounded-sm bg-stone-900 flex items-center justify-center">
                <i data-lucide="hotel" class="text-white" style="width:18px;height:18px"></i>
            </div>
            <div class="text-left leading-tight">
                <p class="text-sm font-semibold tracking-tight">Sistema de Gestión Hotelera</p>
            </div>
        </div>

        <div class="bg-white border border-stone-200 rounded-md p-8">
            <p class="text-xs uppercase tracking-wider text-stone-500 mb-5 font-medium text-center">Acceso de personal</p>

            <form id="loginForm" class="space-y-3">
                <div>
                    <label for="email" class="text-xs uppercase tracking-wider text-stone-500 font-medium">Correo Electrónico</label>
                    <input type="email" name="email" id="email" required
                        class="mt-1 block w-full px-3 py-2 border border-stone-200 rounded-sm text-sm bg-stone-50 focus:outline-none focus:ring-1 focus:ring-stone-400 focus:bg-white">
                </div>

                <div>
                    <label for="password" class="text-xs uppercase tracking-wider text-stone-500 font-medium">Contraseña</label>
                    <div class="relative mt-1">
                        <input type="password" name="password" id="password" required
                            class="block w-full px-3 py-2 pr-9 border border-stone-200 rounded-sm text-sm bg-stone-50 focus:outline-none focus:ring-1 focus:ring-stone-400 focus:bg-white">
                        <button type="button" onclick="togglePassword('password', this)" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-700">
                            <i data-lucide="eye" style="width:16px;height:16px"></i>
                        </button>
                    </div>
                </div>

                <p id="errorMsg" class="text-red-700 text-xs bg-red-50 border border-red-200 rounded-sm px-3 py-2 hidden"></p>

                <button type="submit"
                    class="w-full py-2.5 rounded-sm text-sm font-medium text-white bg-red-800 hover:bg-red-900 transition">
                    Ingresar
                </button>

                <button type="button" id="forgotBtn" class="w-full text-center text-xs text-stone-500 hover:text-stone-800 pt-1">
                    ¿Olvidaste tu contraseña?
                </button>
            </form>

            <form id="forgotForm" class="space-y-3 hidden">
                <p class="text-xs text-stone-500">Escribe tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>
                <div>
                    <label class="text-xs uppercase tracking-wider text-stone-500 font-medium">Correo Electrónico</label>
                    <input type="email" id="forgotEmail" required
                        class="mt-1 block w-full px-3 py-2 border border-stone-200 rounded-sm text-sm bg-stone-50 focus:outline-none focus:ring-1 focus:ring-stone-400 focus:bg-white">
                </div>
                <p id="forgotMsg" class="text-xs rounded-sm px-3 py-2 hidden"></p>
                <button type="submit" class="w-full py-2.5 rounded-sm text-sm font-medium text-white bg-stone-900 hover:bg-stone-800 transition">
                    Enviar enlace
                </button>
                <button type="button" id="backToLoginBtn" class="w-full text-center text-xs text-stone-500 hover:text-stone-800">
                    Volver a iniciar sesión
                </button>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.innerHTML = isHidden
                ? '<i data-lucide="eye-off" style="width:16px;height:16px"></i>'
                : '<i data-lucide="eye" style="width:16px;height:16px"></i>';
            lucide.createIcons();
        }

        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const errorMsg = document.getElementById('errorMsg');
            errorMsg.classList.add('hidden');

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const res = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const data = await res.json();

                if (!res.ok) {
                    errorMsg.textContent = data.message || 'Error al iniciar sesión';
                    errorMsg.classList.remove('hidden');
                    return;
                }

                localStorage.setItem('token', data.token);
                localStorage.setItem('user', JSON.stringify(data.user));
                window.location.href = '/panel';
            } catch (err) {
                errorMsg.textContent = 'No se pudo conectar con el servidor';
                errorMsg.classList.remove('hidden');
            }
        });

        document.getElementById('forgotBtn').addEventListener('click', () => {
            document.getElementById('loginForm').classList.add('hidden');
            document.getElementById('forgotForm').classList.remove('hidden');
        });

        document.getElementById('backToLoginBtn').addEventListener('click', () => {
            document.getElementById('forgotForm').classList.add('hidden');
            document.getElementById('loginForm').classList.remove('hidden');
        });

        document.getElementById('forgotForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const forgotMsg = document.getElementById('forgotMsg');
            const email = document.getElementById('forgotEmail').value;

            const res = await fetch('/api/forgot-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email })
            });
            const data = await res.json();

            forgotMsg.textContent = data.message;
            forgotMsg.className = 'text-xs rounded-sm px-3 py-2 ' + (res.ok ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200');
            forgotMsg.classList.remove('hidden');
        });
    </script>

</body>
</html>
