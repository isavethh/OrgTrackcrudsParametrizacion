@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@php
    $loginUrl = route('login.post');
    $registerUrl = route('register');
    $passResetUrl = route('password.request');
@endphp

@section('auth_header', 'Inicia sesión para continuar')

@section('auth_body')
    <div id="login-error" class="alert alert-danger d-none" role="alert"></div>

    <form id="login-form" action="{{ $loginUrl }}" method="post">
        @csrf
        
        {{-- Correo field --}}
        <div class="input-group mb-3">
            <input type="email" name="correo" class="form-control" placeholder="Correo" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>

        {{-- Contraseña field --}}
        <div class="input-group mb-3">
            <input type="password" name="contrasena" class="form-control" placeholder="Contraseña" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>

        {{-- Recordarme y botón --}}
        <div class="row">
            <div class="col-7">
                <div class="form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">Recordarme</label>
                </div>
            </div>
            <div class="col-5">
                <button type="submit" class="btn btn-block btn-primary">
                    <span class="fas fa-sign-in-alt"></span>
                    Entrar
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    {{-- Password reset link --}}
    <p class="my-0">
        <a href="{{ $passResetUrl }}">
            ¿Olvidaste tu contraseña?
        </a>
    </p>

    {{-- Register link --}}
    <p class="my-0">
        <a href="{{ $registerUrl }}">
            Registrar nueva cuenta
        </a>
    </p>
@stop

@section('js')
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
            const apiBase = window.location.origin;
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
@stop
