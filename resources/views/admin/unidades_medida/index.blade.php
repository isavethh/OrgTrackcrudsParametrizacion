@extends('layouts.admin')

@section('title', 'Unidades de Medida')

@section('page-title', 'Gestión de Unidades de Medida')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Unidades de Medida</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Unidades de Medida</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCrear">
                <i class="fas fa-plus"></i> Nueva Unidad
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="tabla-unidades" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargan dinámicamente con JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="modalCrear" tabindex="-1" role="dialog" aria-labelledby="modalCrearLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearLabel">Nueva Unidad de Medida</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCrear">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="crear_codigo">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="crear_codigo" name="codigo" required maxlength="20" placeholder="Ej: KG, TON, UNI">
                    </div>
                    <div class="form-group">
                        <label for="crear_nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="crear_nombre" name="nombre" required maxlength="50" placeholder="Ej: Kilogramos">
                    </div>
                    <div class="form-group">
                        <label for="crear_tipo">Tipo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="crear_tipo" name="tipo" required maxlength="20" placeholder="Ej: Peso, Volumen, Cantidad">
                    </div>
                    <div class="form-group">
                        <label for="crear_descripcion">Descripción</label>
                        <textarea class="form-control" id="crear_descripcion" name="descripcion" rows="3" maxlength="150"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">Editar Unidad de Medida</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditar">
                <input type="hidden" id="editar_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editar_codigo">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editar_codigo" name="codigo" required maxlength="20">
                    </div>
                    <div class="form-group">
                        <label for="editar_nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editar_nombre" name="nombre" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label for="editar_tipo">Tipo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editar_tipo" name="tipo" required maxlength="20">
                    </div>
                    <div class="form-group">
                        <label for="editar_descripcion">Descripción</label>
                        <textarea class="form-control" id="editar_descripcion" name="descripcion" rows="3" maxlength="150"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
    const API_URL = '/api/unidades-medida';
    const TOKEN = localStorage.getItem('authToken');
    let tabla;

    $(document).ready(function() {
        // Verificar que el token exista
        if (!TOKEN) {
            Swal.fire('Error', 'No se encontró el token de autenticación. Por favor, inicie sesión nuevamente.', 'error').then(() => {
                window.location.href = '/login';
            });
            return;
        }

        // Inicializar DataTable
        tabla = $('#tabla-unidades').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay datos disponibles",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ registros",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron registros coincidentes",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "aria": {
                    "sortAscending": ": activar para ordenar la columna ascendente",
                    "sortDescending": ": activar para ordenar la columna descendente"
                }
            },
            responsive: true,
            columns: [
                { data: 'id' },
                { data: 'codigo' },
                { data: 'nombre' },
                { data: 'tipo' },
                { data: 'descripcion', defaultContent: '-' },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-info btn-editar" data-id="${row.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }
                }
            ]
        });

        cargarUnidades();

        // Form crear
        $('#formCrear').on('submit', function(e) {
            e.preventDefault();
            crearUnidad();
        });

        // Form editar
        $('#formEditar').on('submit', function(e) {
            e.preventDefault();
            actualizarUnidad();
        });

        // Event delegation para botones
        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');
            editarUnidad(id);
        });

        $(document).on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');
            eliminarUnidad(id);
        });
    });

    function cargarUnidades() {
        $.ajax({
            url: API_URL,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + TOKEN
            },
            success: function(response) {
                if (response.success) {
                    tabla.clear();
                    tabla.rows.add(response.data);
                    tabla.draw();
                }
            },
            error: function(xhr) {
                console.error('Error al cargar unidades:', xhr);
                let mensaje = 'No se pudieron cargar las unidades de medida';
                
                if (xhr.status === 401) {
                    mensaje = 'Sesión expirada. Por favor, inicie sesión nuevamente.';
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    mensaje += ': ' + xhr.responseJSON.error;
                }
                
                Swal.fire('Error', mensaje, 'error');
            }
        });
    }

    function crearUnidad() {
        const datos = {
            codigo: $('#crear_codigo').val(),
            nombre: $('#crear_nombre').val(),
            tipo: $('#crear_tipo').val(),
            descripcion: $('#crear_descripcion').val()
        };

        $.ajax({
            url: API_URL,
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + TOKEN,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(datos),
            success: function(response) {
                if (response.success) {
                    $('#modalCrear').modal('hide');
                    $('#formCrear')[0].reset();
                    Swal.fire('Éxito', response.message, 'success');
                    cargarUnidades();
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                let mensaje = 'Error al crear la unidad de medida';
                if (error && error.errors) {
                    mensaje = Object.values(error.errors).join('<br>');
                }
                Swal.fire('Error', mensaje, 'error');
            }
        });
    }

    function editarUnidad(id) {
        $.ajax({
            url: `${API_URL}/${id}`,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + TOKEN
            },
            success: function(response) {
                if (response.success) {
                    const unidad = response.data;
                    $('#editar_id').val(unidad.id);
                    $('#editar_codigo').val(unidad.codigo);
                    $('#editar_nombre').val(unidad.nombre);
                    $('#editar_tipo').val(unidad.tipo);
                    $('#editar_descripcion').val(unidad.descripcion);
                    $('#modalEditar').modal('show');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'No se pudo cargar la unidad de medida', 'error');
            }
        });
    }

    function actualizarUnidad() {
        const id = $('#editar_id').val();
        const datos = {
            codigo: $('#editar_codigo').val(),
            nombre: $('#editar_nombre').val(),
            tipo: $('#editar_tipo').val(),
            descripcion: $('#editar_descripcion').val()
        };

        $.ajax({
            url: `${API_URL}/${id}`,
            method: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + TOKEN,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(datos),
            success: function(response) {
                if (response.success) {
                    $('#modalEditar').modal('hide');
                    Swal.fire('Éxito', response.message, 'success');
                    cargarUnidades();
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                let mensaje = 'Error al actualizar la unidad de medida';
                if (error && error.errors) {
                    mensaje = Object.values(error.errors).join('<br>');
                }
                Swal.fire('Error', mensaje, 'error');
            }
        });
    }

    function eliminarUnidad(id) {
        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción no se puede revertir",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${API_URL}/${id}`,
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + TOKEN
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Eliminado', response.message, 'success');
                            cargarUnidades();
                        }
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        let mensaje = 'Error al eliminar la unidad de medida';
                        if (error && error.message) {
                            mensaje = error.message;
                        }
                        Swal.fire('Error', mensaje, 'error');
                    }
                });
            }
        });
    }
</script>
@endsection
