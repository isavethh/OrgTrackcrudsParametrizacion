@extends('layouts.adminlte')

@section('page-title', 'Gestión de Tamaño / Conteo')

@section('page-content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Catálogo de Tamaños y Conteos (Calibres)</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCrear">
                    <i class="fas fa-plus"></i> Nuevo Calibre
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>¿Cuándo usar esta tabla?</strong> Solo agregue registros aquí si necesita diferenciar pesos por
                calibre (ej: Manzanas grandes vs pequeñas).
                <br>
                Si no agrega nada para un producto (ej: Uvas), el sistema usará automáticamente el <strong>Peso
                    Promedio</strong> definido en el Catálogo de Productos.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <table id="tabla-tamano-conteo" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th>Producto</th>
                        <th>Nombre / Descripción</th>
                        <th>Conteo (Und/Empaque)</th>
                        <th>Peso Promedio (kg/Und)</th>
                        <th style="width: 15%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargan dinámicamente con JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Crear -->
    <div class="modal fade" id="modalCrear" tabindex="-1" role="dialog" aria-labelledby="modalCrearLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearLabel">Nuevo Calibre</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formCrear">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="crear_producto">Producto <span class="text-danger">*</span></label>
                            <select class="form-control" id="crear_producto" name="id_producto" required>
                                <option value="">Cargando...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="crear_nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="crear_nombre" name="nombre" required maxlength="150"
                                placeholder="Ej: 100 unidades - Calibre pequeño">
                        </div>
                        <div class="form-group">
                            <label for="crear_conteo">Conteo por Empaque <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="crear_conteo" name="conteo_por_empaque" required
                                min="1" placeholder="Ej: 100">
                        </div>
                        <div class="form-group">
                            <label for="crear_peso">Peso Promedio por Unidad (kg) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="crear_peso" name="peso_promedio_unidad" required
                                min="0" step="0.001" placeholder="Ej: 0.160">
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
    <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarLabel">Editar Calibre</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formEditar">
                    <input type="hidden" id="editar_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editar_producto">Producto <span class="text-danger">*</span></label>
                            <select class="form-control" id="editar_producto" name="id_producto" required>
                                <option value="">Cargando...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editar_nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editar_nombre" name="nombre" required
                                maxlength="150">
                        </div>
                        <div class="form-group">
                            <label for="editar_conteo">Conteo por Empaque <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="editar_conteo" name="conteo_por_empaque" required
                                min="1">
                        </div>
                        <div class="form-group">
                            <label for="editar_peso">Peso Promedio por Unidad (kg) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="editar_peso" name="peso_promedio_unidad" required
                                min="0" step="0.001">
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
        const API_URL = '/api/catalogo-tamano-conteo';
        const API_PRODUCTOS = '/api/catalogo-productos';
        const TOKEN = localStorage.getItem('authToken');
        let tabla;

        $(document).ready(function () {
            if (!TOKEN) {
                window.location.href = '/login';
                return;
            }

            tabla = $('#tabla-tamano-conteo').DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
                responsive: true,
                columns: [
                    { data: 'id' },
                    {
                        data: 'producto',
                        render: function (data) {
                            return data ? data.nombre : '<span class="text-muted">General</span>';
                        }
                    },
                    { data: 'nombre' },
                    { data: 'conteo_por_empaque' },
                    { data: 'peso_promedio_unidad' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                                                    <button class="btn btn-sm btn-info btn-editar" 
                                                        data-id="${row.id}" 
                                                        data-nombre="${row.nombre}"
                                                        data-conteo="${row.conteo_por_empaque}"
                                                        data-peso="${row.peso_promedio_unidad}"
                                                        data-producto="${row.id_producto || ''}">
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
            cargarProductos();

            $('#formCrear').on('submit', function (e) {
                e.preventDefault();
                crearItem();
            });

            $('#formEditar').on('submit', function (e) {
                e.preventDefault();
                actualizarItem();
            });

            $(document).on('click', '.btn-editar', function () {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');
                const conteo = $(this).data('conteo');
                const peso = $(this).data('peso');

                $('#editar_id').val(id);
                $('#editar_nombre').val(nombre);
                $('#editar_conteo').val(conteo);
                $('#editar_peso').val(peso);
                $('#editar_producto').val($(this).data('producto'));

                $('#modalEditar').modal('show');
            });

            $(document).on('click', '.btn-eliminar', function () {
                const id = $(this).data('id');
                eliminarItem(id);
            });
        });

        function cargarProductos() {
            $.ajax({
                url: API_PRODUCTOS,
                method: 'GET',
                headers: { 'Authorization': 'Bearer ' + TOKEN },
                success: function (response) {
                    const data = Array.isArray(response) ? response : (response.data || []);
                    let options = '<option value="">-- Seleccione Producto --</option>';
                    function compare(a, b) {
                        return a.nombre.localeCompare(b.nombre);
                    }
                    data.sort(compare);
                    data.forEach(item => {
                        options += `<option value="${item.id}">${item.nombre}</option>`;
                    });
                    $('#crear_producto').html(options);
                    $('#editar_producto').html(options);
                }
            });
        }

        function cargarDatos() {
            $.ajax({
                url: API_URL,
                method: 'GET',
                headers: { 'Authorization': 'Bearer ' + TOKEN },
                success: function (response) {
                    tabla.clear();
                    tabla.rows.add(response);
                    tabla.draw();
                },
                error: function (xhr) {
                    console.error(xhr);
                    Swal.fire('Error', 'No se pudieron cargar los datos', 'error');
                }
            });
        }

        function crearItem() {
            const datos = {
                nombre: $('#crear_nombre').val(),
                conteo_por_empaque: $('#crear_conteo').val(),
                peso_promedio_unidad: $('#crear_peso').val(),
                id_producto: $('#crear_producto').val()
            };
            $.ajax({
                url: API_URL,
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + TOKEN, 'Content-Type': 'application/json' },
                data: JSON.stringify(datos),
                success: function (response) {
                    $('#modalCrear').modal('hide');
                    $('#formCrear')[0].reset();
                    Swal.fire('Éxito', 'Calibre creado', 'success');
                    cargarDatos();
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Error al crear', 'error');
                }
            });
        }

        function actualizarItem() {
            const id = $('#editar_id').val();
            const datos = {
                nombre: $('#editar_nombre').val(),
                conteo_por_empaque: $('#editar_conteo').val(),
                peso_promedio_unidad: $('#editar_peso').val(),
                id_producto: $('#editar_producto').val()
            };
            $.ajax({
                url: `${API_URL}/${id}`,
                method: 'PUT',
                headers: { 'Authorization': 'Bearer ' + TOKEN, 'Content-Type': 'application/json' },
                data: JSON.stringify(datos),
                success: function (response) {
                    $('#modalEditar').modal('hide');
                    Swal.fire('Éxito', 'Calibre actualizado', 'success');
                    cargarDatos();
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Error al actualizar', 'error');
                }
            });
        }

        function eliminarItem(id) {
            Swal.fire({
                title: '¿Está seguro?',
                text: "No se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `${API_URL}/${id}`,
                        method: 'DELETE',
                        headers: { 'Authorization': 'Bearer ' + TOKEN },
                        success: function (response) {
                            Swal.fire('Eliminado', 'Elemento eliminado', 'success');
                            cargarDatos();
                        },
                        error: function (xhr) {
                            Swal.fire('Error', 'No se puede eliminar', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush