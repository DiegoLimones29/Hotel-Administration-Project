<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: 'Segoe UI', system-ui, sans-serif; background:#F5F5F4; padding:24px;">
    <div style="max-width:480px; margin:0 auto; background:#fff; border:1px solid #E7E5E4; border-radius:6px; padding:32px;">
        <p style="font-size:14px; font-weight:600; color:#1C1C1C;">Sistema de Gestión Hotelera</p>
        <p style="font-size:14px; color:#57534E;">Recibimos una solicitud para restablecer tu contraseña. Si tú no la hiciste, ignora este correo.</p>
        <p style="text-align:center; margin:28px 0;">
            <a href="{{ $resetUrl }}" style="background:#991B1B; color:#fff; text-decoration:none; padding:10px 20px; border-radius:4px; font-size:14px; font-weight:600;">Restablecer contraseña</a>
        </p>
        <p style="font-size:12px; color:#A8A29E;">Este enlace expira en 60 minutos. Si el botón no funciona, copia y pega esta URL:</p>
        <p style="font-size:12px; color:#78716C; word-break:break-all;">{{ $resetUrl }}</p>
    </div>
</body>
</html>
