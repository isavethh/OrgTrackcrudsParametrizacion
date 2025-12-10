@extends('layouts.adminlte')

@section('page-title', 'Gestión de Categorías')

@section('page-content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Categorías de Productos</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCrear">
                    <i class="fas fa-plus"></i> Nueva Categoría
                </button>
            </div>
        </div>
        <div class="card-body">
            <table id="tabla-categorias" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 10%">ID</th>
                        <th>Nombre</th>
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
                    <h5 class="modal-title" id="modalCrearLabel">Nueva Categoría</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formCrear">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="crear_nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="crear_nombre" name="nombre" required maxlength="100"
                                placeholder="Ej: Frutas, Verduras">
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
                    <h5 class="modal-title" id="modalEditarLabel">Editar Categoría</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formEditar">
                    <input type="hidden" id="editar_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editar_nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editar_nombre" name="nombre" required
                                maxlength="100">
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
        const API_URL = '/api/catalogo-categorias';
        const TOKEN = localStorage.getItem('authToken');
        let tabla;

        $(document).ready(function () {
            if (!TOKEN) {
                window.location.href = '/login';
                return;
            }

            tabla = $('#tabla-categorias').DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
                responsive: true,
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                                        <button class="btn btn-sm btn-info btn-editar" data-id="${row.id}" data-nombre="${row.nombre}">
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

            cargarCategorias();

            $('#formCrear').on('submit', function (e) {
                e.preventDefault();
                crearCategoria();
            });

            $('#formEditar').on('submit', function (e) {
                e.preventDefault();
                actualizarCategoria();
            });

            $(document).on('click', '.btn-editar', function () {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');
                $('#editar_id').val(id);
                $('#editar_nombre').val(nombre);
                $('#modalEditar').modal('show');
            });

            $(document).on('click', '.btn-eliminar', function () {
                const id = $(this).data('id');
                eliminarCategoria(id);
            });
        });

        function cargarCategorias() {
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
                    Swal.fire('Error', 'No se pudieron cargar las categorías', 'error');
                }
            });
        }

        function crearCategoria() {
            const datos = { nombre: $('#crear_nombre').val() };
            $.ajax({
                url: API_URL,
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + TOKEN, 'Content-Type': 'application/json' },
                data: JSON.stringify(datos),
                success: function (response) {
                    $('#modalCrear').modal('hide');
                    $('#formCrear')[0].reset();
                    Swal.fire('Éxito', 'Categoría creada', 'success');
                    cargarCategorias();
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Error al crear', 'error');
                }
            });
        }

        function actualizarCategoria() {
            const id = $('#editar_id').val();
            const datos = { nombre: $('#editar_nombre').val() };
            $.ajax({
                url: `${API_URL}/${id}`,
                method: 'PUT',
                headers: { 'Authorization': 'Bearer ' + TOKEN, 'Content-Type': 'application/json' },
                data: JSON.stringify(datos),
                success: function (response) {
                    $('#modalEditar').modal('hide');
                    Swal.fire('Éxito', 'Categoría actualizada', 'success');
                    cargarCategorias();
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Error al actualizar', 'error');
                }
            });
        }

        function eliminarCategoria(id) {
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
                            Swal.fire('Eliminado', 'Categoría eliminada', 'success');
                            cargarCategorias();
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