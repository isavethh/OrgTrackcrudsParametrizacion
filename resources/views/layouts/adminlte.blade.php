@extends('adminlte::page')

{{-- Configuración específica para admin --}}
@php
    config(['adminlte.title' => 'OrgTrack - Admin']);
    config(['adminlte.menu' => config('menu-admin')]);
@endphp

@section('title', 'OrgTrack - Admin')

@section('content_header')
    <h1 class="m-0">@yield('page-title', 'Dashboard Admin')</h1>
@stop

@section('content')
    @yield('page-content')
@stop

@section('css')
    {{-- CSS adicional específico del admin --}}
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
