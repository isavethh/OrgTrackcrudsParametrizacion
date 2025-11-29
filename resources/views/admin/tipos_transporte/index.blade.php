@extends('layouts.adminlte')

@section('page-title', 'Gestión de Tipos de Transporte')

@section('page-content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Tipos de Transporte</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCrear">
                <i class="fas fa-plus"></i> Nuevo Tipo
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="tabla-tipos" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
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
                <h5 class="modal-title" id="modalCrearLabel">Nuevo Tipo de Transporte</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCrear">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="crear_nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="crear_nombre" name="nombre" required maxlength="50" placeholder="Ej: Terrestre, Aéreo, Marítimo">
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
                <h5 class="modal-title" id="modalEditarLabel">Editar Tipo de Transporte</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditar">
                <input type="hidden" id="editar_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editar_nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editar_nombre" name="nombre" required maxlength="50">
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

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
    const API_URL = '/api/tipotransporte';
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
        tabla = $('#tabla-tipos').DataTable({
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
                { data: 'nombre' },
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

        cargarDatos();

        // Form crear
        $('#formCrear').on('submit', function(e) {
            e.preventDefault();
            crearRegistro();
        });

        // Form editar
        $('#formEditar').on('submit', function(e) {
            e.preventDefault();
            actualizarRegistro();
        });

        // Event delegation para botones
        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');
            editarRegistro(id);
        });

        $(document).on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');
            eliminarRegistro(id);
        });
    });

    function cargarDatos() {
        $.ajax({
            url: API_URL,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + TOKEN
            },
            success: function(response) {
                // La API puede devolver array directo o {data: []}
                const data = Array.isArray(response) ? response : (response.data || []);
                tabla.clear();
                tabla.rows.add(data);
                tabla.draw();
            },
            error: function(xhr) {
                console.error('Error al cargar datos:', xhr);
                let mensaje = 'No se pudieron cargar los datos';
                
                if (xhr.status === 401) {
                    mensaje = 'Sesión expirada. Por favor, inicie sesión nuevamente.';
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                }
                
                Swal.fire('Error', mensaje, 'error');
            }
        });
    }

    function crearRegistro() {
        const datos = {
            nombre: $('#crear_nombre').val(),
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
                $('#modalCrear').modal('hide');
                $('#formCrear')[0].reset();
                Swal.fire('Éxito', 'Registro creado correctamente', 'success');
                cargarDatos();
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                let mensaje = 'Error al crear el registro';
                if (error && error.errors) {
                    mensaje = Object.values(error.errors).join('<br>');
                }
                Swal.fire('Error', mensaje, 'error');
            }
        });
    }

    function editarRegistro(id) {
        const tr = $(`button[data-id="${id}"]`).closest('tr');
        const row = tabla.row(tr).data();
        
        if (row) {
            $('#editar_id').val(row.id);
            $('#editar_nombre').val(row.nombre);
            $('#editar_descripcion').val(row.descripcion);
            $('#modalEditar').modal('show');
        } else {
             Swal.fire('Error', 'No se pudo cargar el registro', 'error');
        }
    }

    function actualizarRegistro() {
        const id = $('#editar_id').val();
        const datos = {
            nombre: $('#editar_nombre').val(),
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
                $('#modalEditar').modal('hide');
                Swal.fire('Éxito', 'Registro actualizado correctamente', 'success');
                cargarDatos();
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                let mensaje = 'Error al actualizar el registro';
                if (error && error.errors) {
                    mensaje = Object.values(error.errors).join('<br>');
                }
                Swal.fire('Error', mensaje, 'error');
            }
        });
    }

    function eliminarRegistro(id) {
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
                        Swal.fire('Eliminado', 'Registro eliminado correctamente', 'success');
                        cargarDatos();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Error al eliminar el registro', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
