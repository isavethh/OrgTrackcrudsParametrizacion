@extends('layouts.adminlte')

@section('page-title', 'Gestión de Tipos de Empaque')

@section('page-content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lista de Tipos de Empaque</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCrear">
                    <i class="fas fa-plus"></i> Nuevo Tipo de Empaque
                </button>
            </div>
        </div>
        <div class="card-body">
            <table id="tabla-empaques" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th>Nombre</th>
                        <th>Largo (cm)</th>
                        <th>Ancho (cm)</th>
                        <th>Alto (cm)</th>
                        <th>Tara (kg)</th>
                        <th>Capacidad</th>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearLabel">Nuevo Tipo de Empaque</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formCrear">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="crear_nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="crear_nombre" name="nombre" required maxlength="100"
                                placeholder="Ej: Caja de Cartón Grande">
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="crear_largo">Largo (cm)</label>
                                    <input type="number" class="form-control" id="crear_largo" name="largo" step="0.01"
                                        min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="crear_ancho">Ancho (cm)</label>
                                    <input type="number" class="form-control" id="crear_ancho" name="ancho" step="0.01"
                                        min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="crear_alto">Alto (cm)</label>
                                    <input type="number" class="form-control" id="crear_alto" name="alto" step="0.01"
                                        min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="crear_tara">Tara (kg)</label>
                                    <input type="number" class="form-control" id="crear_tara" name="tara" step="0.01"
                                        min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="crear_capacidad">Capacidad (Unidades)</label>
                                    <input type="number" class="form-control" id="crear_capacidad" name="capacidad" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="crear_unidades_pallet">Unidades por Pallet</label>
                                    <input type="number" class="form-control" id="crear_unidades_pallet"
                                        name="unidades_por_pallet" min="0">
                                </div>
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
    <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarLabel">Editar Tipo de Empaque</h5>
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
        const API_URL = '/api/catalogo-tipos-empaque';
        const TOKEN = localStorage.getItem('authToken');
        let tabla;

        $(document).ready(function () {
            if (!TOKEN) {
                window.location.href = '/login';
                return;
            }

            tabla = $('#tabla-empaques').DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json" },
                responsive: true,
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    {
                        data: 'largo',
                        render: function (data) { return data ? parseFloat(data) : '-'; }
                    },
                    {
                        data: 'ancho',
                        render: function (data) { return data ? parseFloat(data) : '-'; }
                    },
                    {
                        data: 'alto',
                        render: function (data) { return data ? parseFloat(data) : '-'; }
                    },
                    { data: 'tara', defaultContent: '-' },
                    { data: 'capacidad', defaultContent: '-' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            // Guardamos datos en dataset para fácil acceso
                            const jsonData = JSON.stringify(row).replace(/"/g, '&quot;');
                            return `
                                        <button class="btn btn-sm btn-info btn-editar" data-json="${jsonData}">
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

            cargarEmpaques();

            $('#formCrear').on('submit', function (e) {
                e.preventDefault();
                crearEmpaque();
            });

            $('#formEditar').on('submit', function (e) {
                e.preventDefault();
                actualizarEmpaque();
            });

            $(document).on('click', '.btn-editar', function () {
                const data = $(this).data('json');

                $('#editar_id').val(data.id);
                $('#editar_nombre').val(data.nombre);
                $('#editar_largo').val(data.largo);
                $('#editar_ancho').val(data.ancho);
                $('#editar_alto').val(data.alto);
                $('#editar_tara').val(data.tara);
                $('#editar_capacidad').val(data.capacidad);
                $('#editar_unidades_pallet').val(data.unidades_por_pallet);

                $('#modalEditar').modal('show');
            });

            $(document).on('click', '.btn-eliminar', function () {
                const id = $(this).data('id');
                eliminarEmpaque(id);
            });
        });

        function cargarEmpaques() {
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
                    Swal.fire('Error', 'No se pudieron cargar los tipos de empaque', 'error');
                }
            });
        }

        function crearEmpaque() {
            const datos = {
                nombre: $('#crear_nombre').val(),
                largo: $('#crear_largo').val(),
                ancho: $('#crear_ancho').val(),
                alto: $('#crear_alto').val(),
                tara: $('#crear_tara').val(),
                capacidad: $('#crear_capacidad').val(),
                unidades_por_pallet: $('#crear_unidades_pallet').val()
            };
            $.ajax({
                url: API_URL,
                method: 'POST',
                headers: { 'Authorization': 'Bearer ' + TOKEN, 'Content-Type': 'application/json' },
                data: JSON.stringify(datos),
                success: function (response) {
                    $('#modalCrear').modal('hide');
                    $('#formCrear')[0].reset();
                    Swal.fire('Éxito', 'Tipo de empaque creado', 'success');
                    cargarEmpaques();
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Error al crear', 'error');
                }
            });
        }

        function actualizarEmpaque() {
            const id = $('#editar_id').val();
            const datos = {
                nombre: $('#editar_nombre').val(),
                largo: $('#editar_largo').val(),
                ancho: $('#editar_ancho').val(),
                alto: $('#editar_alto').val(),
                tara: $('#editar_tara').val(),
                capacidad: $('#editar_capacidad').val(),
                unidades_por_pallet: $('#editar_unidades_pallet').val()
            };
            $.ajax({
                url: `${API_URL}/${id}`,
                method: 'PUT',
                headers: { 'Authorization': 'Bearer ' + TOKEN, 'Content-Type': 'application/json' },
                data: JSON.stringify(datos),
                success: function (response) {
                    $('#modalEditar').modal('hide');
                    Swal.fire('Éxito', 'Tipo de empaque actualizado', 'success');
                    cargarEmpaques();
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Error al actualizar', 'error');
                }
            });
        }

        function eliminarEmpaque(id) {
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
                            Swal.fire('Eliminado', 'Tipo de empaque eliminado', 'success');
                            cargarEmpaques();
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