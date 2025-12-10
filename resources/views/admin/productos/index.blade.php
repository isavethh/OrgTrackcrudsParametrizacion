@extends('layouts.adminlte')

@section('page-title', 'Gestión de Productos')

@section('page-content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Productos</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCrear">
                    <i class="fas fa-plus"></i> Nuevo Producto
                </button>
            </div>
        </div>
        <div class="card-body">
            <table id="tabla-productos" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 10%">ID</th>
                        <th>Categoría</th>
                        <th>Nombre</th>
                        <th style="width: 15%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargan dinámicamente -->
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
                    <h5 class="modal-title" id="modalCrearLabel">Nuevo Producto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formCrear">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="crear_id_categoria">Categoría <span class="text-danger">*</span></label>
                            <select class="form-control" id="crear_id_categoria" name="id_categoria" required>
                                <option value="">Cargando categorías...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="crear_nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="crear_nombre" name="nombre" required maxlength="150"
                                placeholder="Ej: Manzana Roja">
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
                    <h5 class="modal-title" id="modalEditarLabel">Editar Producto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formEditar">
                    <input type="hidden" id="editar_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editar_id_categoria">Categoría <span class="text-danger">*</span></label>
                            <select class="form-control" id="editar_id_categoria" name="id_categoria" required>
                                <!-- Se llena dinámicamente -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editar_nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editar_nombre" name="nombre" required
                                maxlength="150">
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
        const API_PRODUCTOS = '/api/catalogo-productos';
        const API_CATEGORIAS = '/api/catalogo-categorias';
        const TOKEN = localStorage.getItem('authToken');
        let tabla;

        $(document).ready(function () {
            if (!TOKEN) {
                window.location.href = '/login';
                return;
            }

            // Cargar categorías para los selects
            cargarCategoriasSelect();

            tabla = $('#tabla-productos').DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
                responsive: true,
                columns: [
                    { data: 'id' },
                    { data: 'categoria.nombre', defaultContent: 'Sin categoría' },
                    { data: 'nombre' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                                    <button class="btn btn-sm btn-info btn-editar" 
                                        data-id="${row.id}" 
                                        data-nombre="${row.nombre}"
                                        data-id_categoria="${row.id_categoria}">
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

            cargarProductos();

            $('#formCrear').on('submit', function (e) {
                e.preventDefault();
                crearProducto();
            });

            $('#formEditar').on('submit', function (e) {
                e.preventDefault();
                actualizarProducto();
            });

            $(document).on('click', '.btn-editar', function () {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');
                const id_categoria = $(this).data('id_categoria');

                $('#editar_id').val(id);
                $('#editar_nombre').val(nombre);
                $('#editar_id_categoria').val(id_categoria);
                $('#modalEditar').modal('show');
            });

            $(document).on('click', '.btn-eliminar', function () {
                const id = $(this).data('id');
                eliminarProducto(id);
            });
        });

        function cargarCategoriasSelect() {
            $.ajax({
                url: API_CATEGORIAS,
                method: 'GET',
                headers: { 'Authorization': 'Bearer ' + TOKEN },
                success: function (response) {
                    let options = '<option value="">Seleccione...</option>';
                    response.forEach(cat => {
                        options += `<option value="${cat.id}">${cat.nombre}</option>`;
                    });
                    $('#crear_id_categoria').html(options);
                    $('#editar_id_categoria').html(options);
                }
            });
        }

        function cargarProductos() {
            $.ajax({
                url: API_PRODUCTOS,
                method: 'GET',
                headers: { 'Authorization': 'Bearer ' + TOKEN },
                success: function (response) {
                    tabla.clear();
                    tabla.rows.add(response);
                    tabla.draw();
                },
                error: function (xhr) {
                    Swal.fire('Error', 'No se pudieron cargar los productos', 'error');
                }
            });
        }

        function crearProducto() {
            const datos = {
                id_categoria: $('#crear_id_categoria').val(),
                nombre: $('#crear_nombre').val()
            };
            $.ajax({
                url: API_PRODUCTOS,
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + TOKEN, 'Content-Type': 'application/json' },
                data: JSON.stringify(datos),
                success: function (response) {
                    $('#modalCrear').modal('hide');
                    $('#formCrear')[0].reset();
                    Swal.fire('Éxito', 'Producto creado', 'success');
                    cargarProductos();
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Error al crear', 'error');
                }
            });
        }

        function actualizarProducto() {
            const id = $('#editar_id').val();
            const datos = {
                id_categoria: $('#editar_id_categoria').val(),
                nombre: $('#editar_nombre').val()
            };
            $.ajax({
                url: `${API_PRODUCTOS}/${id}`,
                method: 'PUT',
                headers: { 'Authorization': 'Bearer ' + TOKEN, 'Content-Type': 'application/json' },
                data: JSON.stringify(datos),
                success: function (response) {
                    $('#modalEditar').modal('hide');
                    Swal.fire('Éxito', 'Producto actualizado', 'success');
                    cargarProductos();
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Error al actualizar', 'error');
                }
            });
        }

        function eliminarProducto(id) {
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
                        url: `${API_PRODUCTOS}/${id}`,
                        method: 'DELETE',
                        headers: { 'Authorization': 'Bearer ' + TOKEN },
                        success: function (response) {
                            Swal.fire('Eliminado', 'Producto eliminado', 'success');
                            cargarProductos();
                        },
                        error: function (xhr) {
                            Swal.fire('Error', 'No se puede eliminar porque está en uso', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush