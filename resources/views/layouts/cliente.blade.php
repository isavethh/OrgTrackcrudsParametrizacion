@extends('adminlte::page')

{{-- Configuración específica para cliente --}}
@php
    config(['adminlte.title' => 'OrgTrack - Cliente']);
    config(['adminlte.menu' => config('menu-cliente')]);
@endphp

@section('title', 'OrgTrack - Cliente')

@section('content_header')
    <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
@stop

@section('content')
    @yield('page-content')
@stop

@section('css')
    {{-- CSS adicional específico del cliente --}}
    @stack('css')
@stop

@section('js')
    <script>
    // Función de logout
    function performLogout(event) {
        if (event) event.preventDefault();
        try {
            localStorage.removeItem('authToken');
            localStorage.removeItem('usuario');
        } catch (error) {
            console.warn('No se pudo limpiar el localStorage', error);
        }
        window.location.replace('/login');
    }

    // Agregar evento de logout
    document.addEventListener('DOMContentLoaded', function() {
        var logoutLink = document.getElementById('logout-link');
        if (logoutLink) {
            logoutLink.addEventListener('click', performLogout);
        }
    });
    </script>
    @stack('js')
@stop
