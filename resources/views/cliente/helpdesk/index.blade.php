@extends('layouts.cliente')

@section('title', 'Centro de Soporte')

@section('page-title')
    <i class="fas fa-headset mr-2"></i>Centro de Soporte
@endsection

@section('page-content')
    <div class="row">
        <div class="col-12">

            {{-- LOADER --}}
            <div id="helpdesk-loader" class="text-center py-5">
                <i class="fas fa-circle-notch fa-spin fa-3x text-primary mb-3"></i>
                <h4 class="mt-2 text-muted">Conectando con el servidor de soporte...</h4>
                <p id="loader-status">Validando credenciales...</p>
            </div>

            {{-- ERROR CONTAINER --}}
            <div id="helpdesk-error" class="alert alert-danger d-none">
                <h5><i class="icon fas fa-ban"></i> Error de Conexión</h5>
                <p id="error-message">No se pudo establecer conexión.</p>
                <button class="btn btn-outline-light btn-sm mt-2" onclick="window.location.reload()">Reintentar</button>
            </div>

            {{-- IFRAME CONTAINER --}}
            <div id="helpdesk-container" class="card d-none" style="height: 85vh;">
                <iframe id="helpdesk-iframe" src="" style="width: 100%; height: 100%; border: none;"
                    allow="camera; microphone; display-capture">
                </iframe>
            </div>

        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loader = document.getElementById('helpdesk-loader');
            const errorContainer = document.getElementById('helpdesk-error');
            const errorMsg = document.getElementById('error-message');
            const container = document.getElementById('helpdesk-container');
            const iframe = document.getElementById('helpdesk-iframe');
            const status = document.getElementById('loader-status');

            async function initHelpdesk() {
                try {
                    // 1. Obtener Token Local
                    const token = localStorage.getItem('authToken') || localStorage.getItem('token');

                    if (!token) {
                        throw new Error('No se encontró sesión activa (Token is missing). Por favor inicia sesión nuevamente.');
                    }

                    status.innerText = "Negociando acceso seguro...";

                    // 2. Pedir URL firmada al Backend (usando el mismo endpoint del admin)
                    const response = await fetch('/admin/api/helpdesk/sso-url', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || 'Error del servidor (' + response.status + ')');
                    }

                    if (!data.iframeUrl) {
                        throw new Error('La respuesta del servidor no contenía una URL válida.');
                    }

                    // 3. Cargar Iframe
                    status.innerText = "Cargando interfaz...";
                    iframe.src = data.iframeUrl;

                    // Mostrar Iframe y ocultar loader cuando cargue
                    iframe.onload = function () {
                        loader.classList.add('d-none');
                        container.classList.remove('d-none');
                    };

                    // Fallback por si onload no dispara (cross-origin a veces)
                    setTimeout(() => {
                        loader.classList.add('d-none');
                        container.classList.remove('d-none');
                    }, 2000);

                } catch (err) {
                    console.error('Helpdesk Init Error:', err);
                    loader.classList.add('d-none');
                    errorContainer.classList.remove('d-none');
                    errorMsg.innerText = err.message;
                }
            }

            initHelpdesk();
        });
    </script>
@endpush