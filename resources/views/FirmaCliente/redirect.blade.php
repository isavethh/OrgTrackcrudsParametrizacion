<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Firma Cliente - Redirigiendo</title>
+    <style>
+        body { font-family: Arial, Helvetica, sans-serif; background:#f7f7f7; }
+        .container { max-width:640px; margin:80px auto; background:#fff; padding:24px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.08);} 
+    </style>
+</head>
+<body>
+    <div class="container">
+        <h2>Redirigiendo...</h2>
+        <p>Serás redirigido en unos segundos. Si la redirección no funciona, haz click <a href="/">aquí</a>.</p>
+        <script>
+            setTimeout(function(){ window.location.href = '/'; }, 2000);
+        </script>
+    </div>
+</body>
+</html>
