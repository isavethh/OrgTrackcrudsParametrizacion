<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento de Envío - {{ $envio->codigo_qr }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .codigo-qr {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .seccion {
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        .seccion-header {
            background: #f8f9fa;
            padding: 12px 15px;
            font-weight: bold;
            font-size: 14px;
            border-bottom: 2px solid #007bff;
        }
        .seccion-body {
            padding: 15px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .info-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            color: #666;
        }
        .info-value {
            display: table-cell;
            width: 60%;
        }
        .productos-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .productos-table th {
            background: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        .productos-table td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 10px;
        }
        .productos-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .totales {
            margin-top: 15px;
            padding: 15px;
            background: #e7f3ff;
            border-radius: 8px;
        }
        .totales-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .totales-label {
            display: table-cell;
            width: 70%;
            text-align: right;
            font-weight: bold;
            padding-right: 10px;
        }
        .totales-value {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-size: 14px;
            color: #007bff;
        }
        .estado-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 11px;
        }
        .estado-completada { background: #28a745; color: white; }
        .estado-en_ruta { background: #17a2b8; color: white; }
        .estado-pendiente { background: #ffc107; color: #333; }
        .timeline {
            margin-top: 15px;
            padding-left: 20px;
            border-left: 3px solid #007bff;
        }
        .timeline-item {
            margin-bottom: 15px;
            padding-left: 15px;
            position: relative;
        }
        .timeline-item:before {
            content: '';
            position: absolute;
            left: -23px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #007bff;
            border: 3px solid white;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 10px;
            padding: 20px;
            border-top: 2px solid #dee2e6;
        }
        .highlight {
            background: #fff3cd;
            padding: 2px 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🚚 DOCUMENTO DE ENVÍO</h1>
        <p>Sistema de Gestión Logística - OrgTrack</p>
    </div>

    <!-- Código QR -->
    <div class="codigo-qr">
        <h2 style="color: #007bff; font-size: 16px; margin-bottom: 5px;">Código QR del Envío</h2>
        <p style="font-size: 18px; font-weight: bold; letter-spacing: 2px;">{{ $envio->codigo_qr }}</p>
    </div>

    <!-- Información del Cliente -->
    <div class="seccion">
        <div class="seccion-header">
            👤 INFORMACIÓN DEL CLIENTE
        </div>
        <div class="seccion-body">
            <div class="info-row">
                <div class="info-label">Nombre Completo:</div>
                <div class="info-value">{{ $envio->usuario->persona->nombre }} {{ $envio->usuario->persona->apellido }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Correo Electrónico:</div>
                <div class="info-value">{{ $envio->usuario->correo }}</div>
            </div>
            @if($envio->usuario->persona->telefono)
            <div class="info-row">
                <div class="info-label">Teléfono:</div>
                <div class="info-value">{{ $envio->usuario->persona->telefono }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Información de la Ruta -->
    @if($envio->direccion)
    <div class="seccion">
        <div class="seccion-header">
            🗺️ INFORMACIÓN DE LA RUTA
        </div>
        <div class="seccion-body">
            <div class="info-row">
                <div class="info-label">📍 Punto de Origen:</div>
                <div class="info-value"><strong>{{ $envio->direccion->nombreorigen }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Coordenadas Origen:</div>
                <div class="info-value">Lat: {{ $envio->direccion->origen_lat }}, Lng: {{ $envio->direccion->origen_lng }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">🏁 Punto de Destino:</div>
                <div class="info-value"><strong>{{ $envio->direccion->nombredestino }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Coordenadas Destino:</div>
                <div class="info-value">Lat: {{ $envio->direccion->destino_lat }}, Lng: {{ $envio->direccion->destino_lng }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Productos del Envío -->
    <div class="seccion">
        <div class="seccion-header">
            📦 PRODUCTOS DEL ENVÍO
        </div>
        <div class="seccion-body">
            <table class="productos-table">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th>Producto</th>
                        <th>Cant.</th>
                        <th>Empaque</th>
                        <th>Unidad</th>
                        <th>Peso/U</th>
                        <th>Peso Total</th>
                        <th>Costo/U</th>
                        <th>Costo Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($envio->productos as $producto)
                    <tr>
                        <td>{{ $producto->categoria }}</td>
                        <td><strong>{{ $producto->producto }}</strong></td>
                        <td>{{ $producto->cantidad }}</td>
                        <td>{{ $producto->tipoEmpaque->nombre }}</td>
                        <td>{{ $producto->unidadMedida->abreviatura }}</td>
                        <td>{{ number_format($producto->peso_por_unidad, 2) }}</td>
                        <td>{{ number_format($producto->peso_total, 2) }} kg</td>
                        <td>Bs. {{ number_format($producto->costo_unitario, 2) }}</td>
                        <td>Bs. {{ number_format($producto->costo_total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totales">
                <div class="totales-row">
                    <div class="totales-label">PESO TOTAL DEL ENVÍO:</div>
                    <div class="totales-value">{{ number_format($envio->peso_total_envio, 2) }} kg</div>
                </div>
                <div class="totales-row">
                    <div class="totales-label">COSTO TOTAL DEL ENVÍO:</div>
                    <div class="totales-value">Bs. {{ number_format($envio->costo_total_envio, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline del Envío -->
    <div class="seccion">
        <div class="seccion-header">
            ⏱️ CRONOLOGÍA DEL ENVÍO
        </div>
        <div class="seccion-body">
            <div class="timeline">
                <div class="timeline-item">
                    <strong>📅 Fecha de Creación:</strong><br>
                    <span class="highlight">{{ $envio->fecha_creacion->format('d/m/Y H:i:s') }}</span>
                </div>

                @if($envio->fecha_entrega_aproximada)
                <div class="timeline-item">
                    <strong>📆 Fecha Estimada de Entrega:</strong><br>
                    {{ $envio->fecha_entrega_aproximada->format('d/m/Y') }}
                    @if($envio->hora_entrega_aproximada)
                        a las {{ $envio->hora_entrega_aproximada }}
                    @endif
                </div>
                @endif

                @if($envio->fecha_inicio_tracking)
                <div class="timeline-item">
                    <strong>🚀 Inicio del Viaje:</strong><br>
                    <span class="highlight">{{ $envio->fecha_inicio_tracking->format('d/m/Y H:i:s') }}</span>
                </div>
                @endif

                @if($envio->fecha_fin_tracking)
                <div class="timeline-item">
                    <strong>✅ Entrega Completada:</strong><br>
                    <span class="highlight">{{ $envio->fecha_fin_tracking->format('d/m/Y H:i:s') }}</span>
                </div>

                @if($duracionViaje)
                <div class="timeline-item">
                    <strong>⏳ Duración del Viaje:</strong><br>
                    <span style="font-size: 16px; color: #28a745; font-weight: bold;">{{ $duracionViaje }}</span>
                </div>
                @endif
                @endif
            </div>

            <div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 8px; text-align: center;">
                <strong>Estado Actual del Envío:</strong><br>
                <span class="estado-badge estado-{{ $envio->estado_tracking }}">
                    {{ strtoupper(str_replace('_', ' ', $envio->estado_tracking)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Historial de Estados -->
    @if($envio->historialEstados->count() > 0)
    <div class="seccion">
        <div class="seccion-header">
            📋 HISTORIAL DE ESTADOS
        </div>
        <div class="seccion-body">
            <table class="productos-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($envio->historialEstados as $historial)
                    <tr>
                        <td>{{ $historial->fecha->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $historial->estadoEnvio->nombre }}</strong></td>
                        <td>{{ $historial->observaciones ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>OrgTrack - Sistema de Gestión Logística</strong></p>
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Este documento es válido como comprobante de envío</p>
    </div>
</body>
</html>
