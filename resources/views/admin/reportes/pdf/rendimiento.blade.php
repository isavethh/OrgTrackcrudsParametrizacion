<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Rendimiento</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { color: #333; }
        .meta { margin-bottom: 20px; font-size: 14px; color: #555; }
    </style>
</head>
<body>
    <h1>Reporte de Rendimiento de Transporte</h1>
    <div class="meta">
        <strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}<br>
        <strong>Rango:</strong> {{ $fechaInicio }} al {{ $fechaFin }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Transportista</th>
                <th>Envíos Entregados</th>
                <th>Tiempo Promedio (Días)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $row->nombre }} {{ $row->apellido }}</td>
                    <td>{{ $row->entregas_totales }}</td>
                    <td>{{ number_format($row->promedio_dias, 1) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
