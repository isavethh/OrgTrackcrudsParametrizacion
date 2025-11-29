@extends('adminlte::auth.auth-page', ['authType' => 'register'])

@php
    $loginUrl = route('login');
    $registerUrl = route('register.post');
@endphp

@section('auth_header', 'Registrar nueva cuenta')

@section('auth_body')
    <div id="register-error" class="alert alert-danger d-none" role="alert"></div>
    <div id="register-success" class="alert alert-success d-none" role="alert"></div>

    <form id="register-form" action="{{ $registerUrl }}" method="post">
        @csrf

        {{-- Nombre field --}}
        <div class="input-group mb-3">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>

        {{-- Apellido field --}}
        <div class="input-group mb-3">
            <input type="text" name="apellido" class="form-control" placeholder="Apellido" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>

        {{-- Correo field --}}
        <div class="input-group mb-3">
            <input type="email" name="correo" class="form-control" placeholder="Correo" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>

        {{-- CI field --}}
        <div class="input-group mb-3">
            <input type="text" name="ci" class="form-control" placeholder="Cédula (CI)" required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-id-card"></span>
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

        {{-- Teléfono field --}}
        <div class="input-group mb-3">
            <input type="text" name="telefono" class="form-control" placeholder="Teléfono (opcional)">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-phone"></span>
                </div>
            </div>
        </div>

        {{-- Rol field --}}
        <div class="input-group mb-3">
            <select name="rol" class="form-control">
                <option value="cliente" selected>Cliente</option>
                <option value="transportista">Transportista</option>
            </select>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user-tag"></span>
                </div>
            </div>
        </div>

        {{-- Términos y botón --}}
        <div class="row">
            <div class="col-8">
                <div class="form-check">
                    <input type="checkbox" id="agreeTerms" class="form-check-input" required>
                    <label for="agreeTerms" class="form-check-label">
                        Acepto los <a href="#">términos</a>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <button type="submit" class="btn btn-block btn-primary">
                    <span class="fas fa-user-plus"></span>
                    Registrar
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="{{ $loginUrl }}">
            Ya tengo una cuenta
        </a>
    </p>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('Register page script loaded');
    const form = document.getElementById('register-form');
    const errorBox = document.getElementById('register-error');
    const successBox = document.getElementById('register-success');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        errorBox.classList.add('d-none');
        successBox.classList.add('d-none');
        errorBox.textContent = '';
        successBox.textContent = '';

        const formData = new FormData(form);
        const nombre = formData.get('nombre');
        const apellido = formData.get('apellido');
        const correo = formData.get('correo');
        const ci = formData.get('ci');
        const contrasena = formData.get('contrasena');
        const telefono = formData.get('telefono') || null;
        const rol = formData.get('rol') || 'cliente';

        try {
            const payload = { nombre, apellido, ci, correo, contrasena, telefono, rol };
            console.log('Register payload:', payload);

            // Build API base: if running on localhost, assume backend at :8000
            const isLocalhost = ['localhost', '127.0.0.1'].includes(window.location.hostname);
            const apiBase = isLocalhost ? `${window.location.protocol}//${window.location.hostname}:8000` : window.location.origin;
            console.log('Using apiBase:', apiBase);

            const response = await fetch(`${apiBase}/api/auth/register`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            console.log('Fetch returned status', response.status);

            // Try to read text first to handle non-json responses
            const text = await response.text().catch(() => '');
            let data = {};
            try { data = text ? JSON.parse(text) : {}; } catch (parseErr) { data = { _raw: text }; }
            console.log('Response body parsed:', data);

            if (!response.ok) {
                // Manejar errores de validación y conflictos
                if (response.status === 422 && data.errors) {
                    const messages = Object.values(data.errors).flat().join(' ');
                    errorBox.textContent = messages || 'Datos inválidos.';
                } else if (response.status === 409) {
                    errorBox.textContent = data.mensaje || data.error || 'Conflicto: recurso ya existe.';
                } else {
                    errorBox.textContent = data.error || data.mensaje || data._raw || 'No se pudo registrar.';
                }
                errorBox.classList.remove('d-none');
                return;
            }

            successBox.textContent = data.mensaje || 'Registro exitoso. Redirigiendo al login...';
            successBox.classList.remove('d-none');

            setTimeout(() => { window.location.href = '/login'; }, 1000);
        } catch (err) {
            console.error('Fetch error:', err);
            errorBox.textContent = 'Error de red. Intenta nuevamente.';
            errorBox.classList.remove('d-none');
        }
    });
});
</script>
@stop
