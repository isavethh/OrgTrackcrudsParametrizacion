<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | OrgTrack</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>Org</b>Track</a>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Inicia sesión para continuar</p>

            <div id="login-error" class="alert alert-danger d-none" role="alert"></div>

            <form id="login-form" action="{{ route('login.post') }}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" name="correo" class="form-control" placeholder="Correo" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="contrasena" class="form-control" placeholder="Contraseña" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">Recordarme</label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                    </div>
                </div>
            </form>

            <div class="text-center mt-3">
                <p class="mb-1"><a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a></p>
                <p class="mb-0"><a href="{{ route('register') }}" class="text-center">Registrar nueva cuenta</a></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('login-form');
    const errorBox = document.getElementById('login-error');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        errorBox.classList.add('d-none');
        errorBox.textContent = '';

        const formData = new FormData(form);
        const correo = formData.get('correo');
        const contrasena = formData.get('contrasena');

        try {
            // If frontend running on localhost, assume API on :8000
            const isLocalhost = ['localhost', '127.0.0.1'].includes(window.location.hostname);
            const apiBase = isLocalhost ? `${window.location.protocol}//${window.location.hostname}:8000` : window.location.origin;
            console.log('Using apiBase for login:', apiBase);

            const response = await fetch(`${apiBase}/api/auth/login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ correo, contrasena })
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const message = data.error || 'No se pudo iniciar sesión.';
                errorBox.textContent = message;
                errorBox.classList.remove('d-none');
                return;
            }

            if (data.token) {
                try { localStorage.setItem('authToken', data.token); } catch {}
                try { localStorage.setItem('usuario', JSON.stringify(data.usuario || {})); } catch {}
            }

            const rol = (data.usuario?.rol || '').toLowerCase();
            const isAdmin = rol === 'admin';
            window.location.href = isAdmin ? '/admin/dashboard' : '/dashboard';
        } catch (err) {
            errorBox.textContent = 'Error de red. Intenta nuevamente.';
            errorBox.classList.remove('d-none');
        }
    });
});
</script>
</body>
</html>
