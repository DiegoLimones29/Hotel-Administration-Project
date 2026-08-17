<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-stone-50 flex items-center justify-center min-h-screen text-stone-900" style="font-family: 'Segoe UI', system-ui, sans-serif;">

    <div class="w-full max-w-sm">
        <div class="flex items-center gap-3 justify-center mb-6">
            <div class="w-9 h-9 rounded-sm bg-stone-900 flex items-center justify-center">
                <i data-lucide="key-round" class="text-white" style="width:18px;height:18px"></i>
            </div>
            <p class="text-sm font-semibold tracking-tight">Restablecer contraseña</p>
        </div>

        <div class="bg-white border border-stone-200 rounded-md p-8">
            <form id="resetForm" class="space-y-3">
                <div>
                    <label class="text-xs uppercase tracking-wider text-stone-500 font-medium">Nueva contraseña</label>
                    <input type="password" id="password" required minlength="6"
                        class="mt-1 block w-full px-3 py-2 border border-stone-200 rounded-sm text-sm bg-stone-50 focus:outline-none focus:ring-1 focus:ring-stone-400 focus:bg-white">
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wider text-stone-500 font-medium">Confirmar contraseña</label>
                    <input type="password" id="passwordConfirm" required minlength="6"
                        class="mt-1 block w-full px-3 py-2 border border-stone-200 rounded-sm text-sm bg-stone-50 focus:outline-none focus:ring-1 focus:ring-stone-400 focus:bg-white">
                </div>

                <p id="msg" class="text-xs rounded-sm px-3 py-2 hidden"></p>

                <button type="submit" class="w-full py-2.5 rounded-sm text-sm font-medium text-white bg-red-800 hover:bg-red-900 transition">
                    Guardar nueva contraseña
                </button>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const params = new URLSearchParams(window.location.search);
        const email = params.get('email');
        const token = params.get('token');
        const msg = document.getElementById('msg');

        if (!email || !token) {
            msg.textContent = 'Enlace inválido. Solicita uno nuevo desde el login.';
            msg.className = 'text-xs rounded-sm px-3 py-2 bg-red-50 text-red-800 border border-red-200';
            msg.classList.remove('hidden');
            document.getElementById('resetForm').querySelector('button').disabled = true;
        }

        document.getElementById('resetForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('passwordConfirm').value;

            if (password !== passwordConfirm) {
                msg.textContent = 'Las contraseñas no coinciden';
                msg.className = 'text-xs rounded-sm px-3 py-2 bg-red-50 text-red-800 border border-red-200';
                msg.classList.remove('hidden');
                return;
            }

            const res = await fetch('/api/reset-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email, token, password })
            });
            const data = await res.json();

            msg.textContent = data.message;
            msg.className = 'text-xs rounded-sm px-3 py-2 ' + (res.ok ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200');
            msg.classList.remove('hidden');

            if (res.ok) {
                setTimeout(() => window.location.href = '/login', 1800);
            }
        });
    </script>

</body>
</html>
