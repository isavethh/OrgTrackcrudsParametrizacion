@extends('layouts.app') 

@section('content')
<div class="d-flex justify-content-center align-items-center" style="height: 80vh;">
    <div class="text-center">
        <i class="fas fa-circle-notch fa-spin fa-3x text-primary mb-3"></i>
        <h3>Autenticando en Centro de Soporte...</h3>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Intentar obtener el token del LocalStorage (ajusta la key si es diferente, ej: 'token', 'access_token')
        const token = localStorage.getItem('token') || localStorage.getItem('jwt_token') || localStorage.getItem('auth_token');

        if (token) {
            // 2. Redirigir enviando el token en la URL
            window.location.href = "{{ route('helpdesk') }}?token=" + encodeURIComponent(token);
        } else {
            // 3. Si no hay token, intentar ir directo (quizás hay sesión) o mostrar error
            console.error("No token found in LocalStorage");
            window.location.href = "{{ route('helpdesk') }}";
        }
    });
</script>
@endsection
