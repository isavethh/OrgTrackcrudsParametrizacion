<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Firma Cliente - Login</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background:#f7f7f7; }
        .container { max-width:420px; margin:60px auto; background:#fff; padding:24px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.08);} 
        .field { margin-bottom:12px; }
        label { display:block; margin-bottom:6px; font-weight:600; }
        input[type="text"], input[type="password"], input[type="email"] { width:100%; padding:8px 10px; border:1px solid #ddd; border-radius:4px; }
        button { background:#0b74de; color:#fff; border:none; padding:10px 14px; border-radius:4px; cursor:pointer; }
        .small { font-size:0.9rem; color:#555; margin-top:8px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Ingreso - Firma Cliente</h2>
        <form method="POST" action="/firma-cliente/login">
            @csrf
            <div class="field">
                <label for="email">Correo electrónico</label>
                <input id="email" name="email" type="email" required placeholder="correo@ejemplo.com">
            </div>
            <div class="field">
                <label for="password">Contraseña</label>
                <input id="password" name="password" type="password" required placeholder="Contraseña">
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <button type="submit">Iniciar sesión</button>
                <a href="/firma-cliente/redirect" style="margin-left:12px; color:#0b74de; text-decoration:none;">Ir a redirección</a>
            </div>
        </form>
        <p class="small">Nota: esta vista envía el formulario a la ruta <code>/firma-cliente/login</code>. Crea la ruta/Controlador correspondiente para procesarlo.</p>
    </div>
</body>
</html>
