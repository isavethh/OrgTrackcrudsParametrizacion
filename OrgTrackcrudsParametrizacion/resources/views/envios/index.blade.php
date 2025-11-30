@extends('adminlte::page')

@section('title', 'Envíos')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-shipping-fast text-purple"></i> Gestión de Envíos</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Envíos</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-gradient-purple">
            <h3 class="card-title"><i class="fas fa-list"></i> Lista de Envíos</h3>
            <div class="card-tools">
                <span class="badge badge-light">
                    <i class="fas fa-info-circle"></i> Los envíos llegan desde AgroNexus
                </span>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-sm" id="envios-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th style="width: 150px;">Usuario</th>
                        <th style="width: 120px;">Estado</th>
                        <th style="width: 80px;">Items</th>
                        <th style="width: 130px;">Fecha Creación</th>
                        <th style="width: 130px;">Entrega Est.</th>
                        <th style="width: 90px;">Totales</th>
                        <th style="width: 120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($envios as $envio)
                        <tr>
                            <td class="text-center"><strong>#{{ $envio->id }}</strong></td>
                            <td>
                                @if($envio->usuario)
                                    <small>{{ $envio->usuario->nombre }} {{ $envio->usuario->apellido }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @php
                                    $estadoAprobacion = $envio->estado_aprobacion ?? 'pendiente';
                                @endphp
                                <span class="badge badge-{{
                                    $estadoAprobacion == 'aprobado' ? 'success' :
                                    ($estadoAprobacion == 'rechazado' ? 'danger' : 'warning')
                                }}" style="font-size: 0.85rem;">
                                    @if($estadoAprobacion == 'pendiente')
                                        <i class="fas fa-clock"></i> Pendiente
                                    @elseif($estadoAprobacion == 'aprobado')
                                        <i class="fas fa-check"></i> Aprobado
                                    @else
                                        <i class="fas fa-times"></i> Rechazado
                                    @endif
                                </span>
                            </td>
                            <td class="text-center">
                                @if($envio->productos && $envio->productos->count() > 0)
                                    <span class="badge badge-pill badge-info">{{ $envio->productos->count() }}</span>
                                @else
                                    <span class="badge badge-pill badge-secondary">0</span>
                                @endif
                            </td>
                            <td><small>{{ $envio->fecha_creacion ? $envio->fecha_creacion->format('d/m/Y H:i') : '-' }}</small></td>
                            <td>
                                @if($envio->fecha_entrega_aproximada)
                                    <small>{{ \Carbon\Carbon::parse($envio->fecha_entrega_aproximada)->format('d/m/Y') }}</small>
                                    @if($envio->hora_entrega_aproximada)
                                        <br><small class="text-muted">{{ substr($envio->hora_entrega_aproximada, 0, 5) }}</small>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <small><i class="fas fa-weight"></i> {{ number_format($envio->peso_total_envio, 1) }} kg</small><br>
                                <small><i class="fas fa-dollar-sign"></i> Bs. {{ number_format($envio->costo_total_envio, 0) }}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group-vertical btn-group-sm" role="group">
                                    <a href="{{ route('envios.show', $envio) }}" class="btn btn-info btn-xs" title="Ver detalles">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    @if(($envio->estado_aprobacion ?? 'pendiente') == 'pendiente')
                                        <button type="button" class="btn btn-success btn-xs" title="Aprobar" 
                                                onclick="showAsignarModal({{ $envio->id }}, '{{ $envio->usuario->nombre ?? '' }} {{ $envio->usuario->apellido ?? '' }}')">
                                            <i class="fas fa-check"></i> Aprobar
                                        </button>
                                        <button type="button" class="btn btn-danger btn-xs" title="Rechazar" 
                                                onclick="showRechazarModal({{ $envio->id }})">
                                            <i class="fas fa-times"></i> Rechazar
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay envíos registrados</p>
                                <a href="{{ route('envios.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear primer envío
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para Asignar Transportista -->
    <div class="modal fade" id="modalAsignar" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle"></i> Aprobar Envío y Asignar Transportista</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formAsignar">
                    <div class="modal-body">
                        <input type="hidden" id="envioIdAsignar">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Cliente: <strong id="clienteNombre"></strong>
                        </div>
                        <div class="form-group">
                            <label for="transportista_id">Seleccione Transportista <span class="text-danger">*</span></label>
                            <select name="transportista_id" id="transportista_id" class="form-control" required>
                                <option value="">-- Seleccione --</option>
                                @php
                                    // Obtener rol de Transportista
                                    $rolTransportista = \App\Models\RolUsuario::where('nombre', 'Transportista')
                                                                               ->orWhere('codigo', 'TRANS')
                                                                               ->first();
                                    $transportistas = collect();
                                    if ($rolTransportista) {
                                        $transportistas = \App\Models\Usuario::where('id_rol', $rolTransportista->id)->get();
                                    }
                                @endphp
                                @forelse($transportistas as $transportista)
                                    <option value="{{ $transportista->id }}">
                                        {{ $transportista->nombre }} {{ $transportista->apellido }}
                                    </option>
                                @empty
                                    <option value="" disabled>No hay transportistas disponibles</option>
                                @endforelse
                            </select>
                            @if($transportistas->isEmpty())
                                <small class="text-danger">
                                    <i class="fas fa-exclamation-triangle"></i> No hay transportistas registrados. 
                                    Cree usuarios con rol Transportista primero.
                                </small>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Aprobar y Asignar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Rechazar -->
    <div class="modal fade" id="modalRechazar" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle"></i> Rechazar Envío</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formRechazar">
                    <div class="modal-body">
                        <input type="hidden" id="envioIdRechazar">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Esta acción no se puede deshacer
                        </div>
                        <div class="form-group">
                            <label for="motivo_rechazo">Motivo del Rechazo <span class="text-danger">*</span></label>
                            <textarea name="motivo_rechazo" id="motivo_rechazo" class="form-control" rows="4" 
                                      placeholder="Explique el motivo del rechazo (mínimo 10 caracteres)" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Rechazar Envío
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <style>
        .card {
            border-radius: 10px;
            border: none;
        }
        .bg-gradient-purple {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#envios-table').DataTable({
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar:",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                },
                order: [[0, 'desc']],
                pageLength: 25
            });
        });

        function showAsignarModal(envioId, clienteNombre) {
            console.log('Mostrando modal de asignación para envío:', envioId);
            $('#envioIdAsignar').val(envioId);
            $('#clienteNombre').text(clienteNombre);
            $('#modalAsignar').modal('show');
        }

        function showRechazarModal(envioId) {
            console.log('Mostrando modal de rechazo para envío:', envioId);
            $('#envioIdRechazar').val(envioId);
            $('#modalRechazar').modal('show');
        }

        $('#formAsignar').submit(function(e) {
            e.preventDefault();
            const envioId = $('#envioIdAsignar').val();
            const transportistaId = $('#transportista_id').val();
            
            console.log('Aprobando envío:', envioId, 'Transportista:', transportistaId);
            
            if (!transportistaId) {
                alert('Debe seleccionar un transportista');
                return;
            }

            // Deshabilitar botón para evitar doble clic
            const btnSubmit = $(this).find('button[type="submit"]');
            btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

            $.ajax({
                url: '/envios/' + envioId + '/aprobar',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    transportista_id: transportistaId
                },
                success: function(response) {
                    console.log('Respuesta éxito:', response);
                    $('#modalAsignar').modal('hide');
                    alert('Envío aprobado exitosamente');
                    window.location.reload();
                },
                error: function(xhr) {
                    console.error('Error AJAX:', xhr);
                    console.error('Status:', xhr.status);
                    console.error('Response:', xhr.responseText);
                    btnSubmit.prop('disabled', false).html('<i class="fas fa-check"></i> Aprobar y Asignar');
                    alert('Error al aprobar el envío: ' + (xhr.responseJSON?.message || xhr.statusText || 'Error desconocido'));
                }
            });
        });

        $('#formRechazar').submit(function(e) {
            e.preventDefault();
            const envioId = $('#envioIdRechazar').val();
            const motivo = $('#motivo_rechazo').val();
            
            console.log('Rechazando envío:', envioId, 'Motivo:', motivo);
            
            if (!motivo || motivo.length < 10) {
                alert('El motivo debe tener al menos 10 caracteres');
                return;
            }

            $.ajax({
                url: '/envios/' + envioId + '/rechazar',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    motivo_rechazo: motivo
                },
                success: function(response) {
                    console.log('Envío rechazado exitosamente:', response);
                    $('#modalRechazar').modal('hide');
                    alert('Envío rechazado exitosamente');
                    window.location.reload();
                },
                error: function(xhr) {
                    console.error('Error AJAX:', xhr);
                    console.error('Status:', xhr.status);
                    console.error('Response:', xhr.responseText);
                    alert('Error al rechazar el envío: ' + (xhr.responseJSON?.message || 'Error desconocido'));
                }
            });
        });
    </script>
@stop
