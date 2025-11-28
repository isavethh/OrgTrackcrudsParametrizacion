@extends('layouts.admin')

@section('title', 'Catálogo de Carga')

@section('page-title', 'Gestión de Catálogo de Productos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Catálogo de Carga</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Productos Transportables</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCrear">
                <i class="fas fa-plus"></i> Nuevo Producto
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="tabla-catalogo" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Temp. Min/Max</th>
                    <th>Humedad Min/Max</th>
                    <th>Refrigeración</th>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearLabel">Nuevo Producto al Catálogo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCrear">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="crear_nombre_producto">Nombre del Producto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="crear_nombre_producto" name="nombre_producto" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="crear_categoria">Categoría <span class="text-danger">*</span></label>
                                <select class="form-control" id="crear_categoria" name="categoria" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Frutas">Frutas</option>
                                    <option value="Verduras">Verduras</option>
                                    <option value="Hortalizas">Hortalizas</option>
                                    <option value="Granos">Granos</option>
                                    <option value="Cereales">Cereales</option>
                                    <option value="Tubérculos">Tubérculos</option>
                                    <option value="Legumbres">Legumbres</option>
                                    <option value="Otros">Otros</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="crear_descripcion">Descripción</label>
                        <textarea class="form-control" id="crear_descripcion" name="descripcion" rows="2" maxlength="500"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="crear_temp_min">Temperatura Mín (°C)</label>
                                <input type="number" step="0.1" class="form-control" id="crear_temp_min" name="temp_min">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="crear_temp_max">Temperatura Máx (°C)</label>
                                <input type="number" step="0.1" class="form-control" id="crear_temp_max" name="temp_max">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="crear_humedad_min">Humedad Mín (%)</label>
                                <input type="number" step="0.1" class="form-control" id="crear_humedad_min" name="humedad_min" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="crear_humedad_max">Humedad Máx (%)</label>
                                <input type="number" step="0.1" class="form-control" id="crear_humedad_max" name="humedad_max" min="0" max="100">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="crear_requiere_refrigeracion" name="requiere_refrigeracion">
                            <label class="custom-control-label" for="crear_requiere_refrigeracion">Requiere Refrigeración</label>
                        </div>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">Editar Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditar">
                <input type="hidden" id="editar_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editar_nombre_producto">Nombre del Producto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editar_nombre_producto" name="nombre_producto" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editar_categoria">Categoría <span class="text-danger">*</span></label>
                                <select class="form-control" id="editar_categoria" name="categoria" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Frutas">Frutas</option>
                                    <option value="Verduras">Verduras</option>
                                    <option value="Hortalizas">Hortalizas</option>
                                    <option value="Granos">Granos</option>
                                    <option value="Cereales">Cereales</option>
                                    <option value="Tubérculos">Tubérculos</option>
                                    <option value="Legumbres">Legumbres</option>
                                    <option value="Otros">Otros</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editar_descripcion">Descripción</label>
                        <textarea class="form-control" id="editar_descripcion" name="descripcion" rows="2" maxlength="500"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="editar_temp_min">Temperatura Mín (°C)</label>
                                <input type="number" step="0.1" class="form-control" id="editar_temp_min" name="temp_min">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="editar_temp_max">Temperatura Máx (°C)</label>
                                <input type="number" step="0.1" class="form-control" id="editar_temp_max" name="temp_max">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="editar_humedad_min">Humedad Mín (%)</label>
                                <input type="number" step="0.1" class="form-control" id="editar_humedad_min" name="humedad_min" min="0" max="100">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="editar_humedad_max">Humedad Máx (%)</label>
                                <input type="number" step="0.1" class="form-control" id="editar_humedad_max" name="humedad_max" min="0" max="100">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="editar_requiere_refrigeracion" name="requiere_refrigeracion">
                            <label class="custom-control-label" for="editar_requiere_refrigeracion">Requiere Refrigeración</label>
                        </div>
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
    const API_URL = '/api/catalogo-carga';
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
        tabla = $('#tabla-catalogo').DataTable({
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
                { data: 'id_catalogo' },
                { data: 'nombre_producto' },
                { data: 'categoria' },
                {
                    data: null,
                    render: function(data) {
                        const min = data.temp_min !== null ? data.temp_min + '°C' : '-';
                        const max = data.temp_max !== null ? data.temp_max + '°C' : '-';
                        return min + ' / ' + max;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        const min = data.humedad_min !== null ? data.humedad_min + '%' : '-';
                        const max = data.humedad_max !== null ? data.humedad_max + '%' : '-';
                        return min + ' / ' + max;
                    }
                },
                {
                    data: 'requiere_refrigeracion',
                    render: function(data) {
                        return data ? '<span class="badge badge-info">Sí</span>' : '<span class="badge badge-secondary">No</span>';
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-info btn-editar" data-id="${row.id_catalogo}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id_catalogo}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    }
                }
            ]
        });

        cargarCatalogo();

        // Form crear
        $('#formCrear').on('submit', function(e) {
            e.preventDefault();
            crearProducto();
        });

        // Form editar
        $('#formEditar').on('submit', function(e) {
            e.preventDefault();
            actualizarProducto();
        });

        // Event delegation para botones
        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');
            editarProducto(id);
        });

        $(document).on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');
            eliminarProducto(id);
        });
    });

    function cargarCatalogo() {
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
                console.error('Error al cargar catálogo:', xhr);
                let mensaje = 'No se pudo cargar el catálogo de productos';
                
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

    function crearProducto() {
        const datos = {
            nombre_producto: $('#crear_nombre_producto').val(),
            categoria: $('#crear_categoria').val(),
            descripcion: $('#crear_descripcion').val() || null,
            temp_min: $('#crear_temp_min').val() || null,
            temp_max: $('#crear_temp_max').val() || null,
            humedad_min: $('#crear_humedad_min').val() || null,
            humedad_max: $('#crear_humedad_max').val() || null,
            requiere_refrigeracion: $('#crear_requiere_refrigeracion').is(':checked')
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
                    cargarCatalogo();
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                let mensaje = 'Error al crear el producto';
                if (error && error.errors) {
                    mensaje = Object.values(error.errors).join('<br>');
                }
                Swal.fire('Error', mensaje, 'error');
            }
        });
    }

    function editarProducto(id) {
        $.ajax({
            url: `${API_URL}/${id}`,
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + TOKEN
            },
            success: function(response) {
                if (response.success) {
                    const producto = response.data;
                    $('#editar_id').val(producto.id_catalogo);
                    $('#editar_nombre_producto').val(producto.nombre_producto);
                    $('#editar_categoria').val(producto.categoria);
                    $('#editar_descripcion').val(producto.descripcion);
                    $('#editar_temp_min').val(producto.temp_min);
                    $('#editar_temp_max').val(producto.temp_max);
                    $('#editar_humedad_min').val(producto.humedad_min);
                    $('#editar_humedad_max').val(producto.humedad_max);
                    $('#editar_requiere_refrigeracion').prop('checked', producto.requiere_refrigeracion);
                    $('#modalEditar').modal('show');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'No se pudo cargar el producto', 'error');
            }
        });
    }

    function actualizarProducto() {
        const id = $('#editar_id').val();
        const datos = {
            nombre_producto: $('#editar_nombre_producto').val(),
            categoria: $('#editar_categoria').val(),
            descripcion: $('#editar_descripcion').val() || null,
            temp_min: $('#editar_temp_min').val() || null,
            temp_max: $('#editar_temp_max').val() || null,
            humedad_min: $('#editar_humedad_min').val() || null,
            humedad_max: $('#editar_humedad_max').val() || null,
            requiere_refrigeracion: $('#editar_requiere_refrigeracion').is(':checked')
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
                    cargarCatalogo();
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON;
                let mensaje = 'Error al actualizar el producto';
                if (error && error.errors) {
                    mensaje = Object.values(error.errors).join('<br>');
                }
                Swal.fire('Error', mensaje, 'error');
            }
        });
    }

    function eliminarProducto(id) {
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
                            cargarCatalogo();
                        }
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON;
                        let mensaje = 'Error al eliminar el producto';
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
