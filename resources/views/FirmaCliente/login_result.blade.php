<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Firma Cliente - Resultado</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background:#f7f7f7; }
        .container { max-width:640px; margin:60px auto; background:#fff; padding:24px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.08);} 
+    </style>
+</head>
+<body>
+    <div class="container">
+        <h2>Resultado de inicio de sesión</h2>
+        <p>Has iniciado sesión como: <strong>{{ session('email') ?? old('email') ?? 'Usuario' }}</strong></p>
+        <p>Si quieres redirigir al usuario después del login, ajusta el controlador para devolver la vista de redirección:</p>
+        <p><a href="/firma-cliente/redirect">Ir a página de redirección</a></p>
+    </div>
+</body>
+</html>
