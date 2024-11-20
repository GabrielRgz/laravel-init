@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'DataTables - Tables')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <!-- Row Group CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}">
    <!-- Form Validation -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-buttons/datatables-buttons.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jszip/jszip.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/pdfmake/pdfmake.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-buttons/buttons.html5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-buttons/buttons.print.js') }}"></script>
    <!-- Flat Picker -->
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <!-- Row Group JS -->
    <script src="{{ asset('assets/vendor/libs/datatables-rowgroup/datatables.rowgroup.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.js') }}"></script>
    <!-- Form Validation -->
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            // Inicialización de DataTable
            $('.datatables-basic').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('inventario.data') }}', // Aquí usamos la nueva ruta
                    type: 'GET'
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'catalogo_name'
                    },// Nombre de la categoría (esto lo definimos en el controlador si es necesario)
                    {
                        data: 'descripcion'
                    }, 
                    {
                        data: 'cantidad_stock'
                    },
                    {
                        data: 'ubicacion'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'updated_at'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
            <button 
                class="btn btn-sm btn-primary edit-btn" 
                data-id="${row.id}" 
                data-catalogo-id="${row.catalogo_id}" 
                data-cantidad-stock="${row.cantidad_stock}" 
                data-ubicacion="${row.ubicacion}">
                Editar
            </button>
            <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">Eliminar</button>
        `;
                        }
                    }
                ]
            });
            // Envío del formulario para agregar nuevo registro
            $('#form-add-new-record').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('inventario.store') }}', // Usamos la ruta para inventario
                    type: 'POST',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        catalogo_id: $('#catalogoId').val(), // ID del catálogo
                        cantidad_stock: $('#cantidadStock').val(),
                        ubicacion: $('#ubicacion').val(),
                    },
                    success: function(response) {
                        alert(response.success);
                        $('#add-new-record').offcanvas('hide');
                        $('.datatables-basic').DataTable().ajax.reload();
                    },
                    error: function(response) {
                        alert('Ocurrió un error al guardar el registro.');
                    }
                });
            });
            // Botón Editar - Abre el modal con los datos del registro
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                const catalogoId = $(this).data('catalogo-id');
                const cantidadStock = $(this).data('cantidad-stock');
                const ubicacion = $(this).data('ubicacion');

                // Asignar valores al formulario
                $('#editCatalogoId').val(catalogoId);
                $('#editCantidadStock').val(cantidadStock);
                $('#editUbicacion').val(ubicacion);

                // Mostrar el modal
                $('#edit-record-modal').modal('show');

                // Asociar el ID del registro al formulario
                $('#form-edit-record').data('id', id);
            });

            // Enviar formulario de edición
            $('#form-edit-record').on('submit', function(e) {
                e.preventDefault();

                const id = $(this).data('id');
                const formData = $(this).serialize();

                $.ajax({
                    url: `/inventario/${id}`,
                    type: 'PUT',
                    data: formData,
                    success: function(response) {
                        alert(response.success);
                        $('#edit-record-modal').modal('hide');
                        $('.datatables-basic').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseJSON);
                        alert('Ocurrió un error al actualizar el registro.');
                    }
                });
            });
            $(document).on('click', '.delete-btn', function() {
                let invId = $(this).data('id');

                // Confirmar eliminación
                if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
                    $.ajax({
                        url: '/inventario/' + invId, // Ruta para eliminar el usuario
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}' // Incluir el token CSRF
                        },
                        success: function(response) {
                            alert(response.message);
                            $('.datatables-basic').DataTable().ajax
                                .reload(); // Recargar la tabla
                        },
                        error: function(xhr) {
                            alert('Ocurrió un error al eliminar el registro.');
                        }
                    });
                }
            });
        });
    </script>
@endsection

@section('content')
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Tablas /</span> Inventario
    </h4>
    @role('admin')
        <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#add-new-record">
            Añadir Nuevo Registro
        </button>
    @endrole
    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Categoria</th>
                        <th>Descripcion</th>
                        <th>Stock</th>
                        <th>Ubicacion</th>
                        <th>Fecha de Creación</th>
                        <th>Actualizado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--/ DataTable with Buttons -->
    <!-- Modal to add new record -->
    <div class="offcanvas offcanvas-end" id="add-new-record">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="exampleModalLabel">Nuevo Registro</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <form class="add-new-record pt-0 row g-2" id="form-add-new-record" onsubmit="return false">
                <!-- Catálogo -->
                <div class="col-sm-12">
                    <label class="form-label" for="catalogoId">Categoria</label>
                    <div class="input-group input-group-merge">
                        <span id="catalogoIdIcon" class="input-group-text"><i class="bx bx-category"></i></span>
                        <select id="catalogoId" name="catalogoId" class="form-control" required>
                            <option value="">Selecciona una categoria</option>
                            @foreach ($catalogos as $catalogo)
                                <option value="{{ $catalogo->id }}">{{ $catalogo->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- Cantidad en Stock -->
                <div class="col-sm-12">
                    <label class="form-label" for="cantidadStock">Cantidad en Stock</label>
                    <div class="input-group input-group-merge">
                        <span id="cantidadStockIcon" class="input-group-text"><i class="bx bx-package"></i></span>
                        <input type="number" id="cantidadStock" name="cantidadStock" class="form-control"
                            placeholder="Ejemplo: 50" aria-label="Cantidad en Stock" aria-describedby="cantidadStockIcon" />
                    </div>
                </div>
                <!-- Ubicación -->
                <div class="col-sm-12">
                    <label class="form-label" for="ubicacion">Ubicación</label>
                    <div class="input-group input-group-merge">
                        <span id="ubicacionIcon" class="input-group-text"><i class="bx bx-location-plus"></i></span>
                        <input type="text" id="ubicacion" name="ubicacion" class="form-control"
                            placeholder="Ejemplo: Bodega A" aria-label="Ubicación" aria-describedby="ubicacionIcon" />
                    </div>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary data-submit me-sm-3 me-1">Guardar</button>
                    <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal para Editar Registro -->
    <div class="modal fade" id="edit-record-modal" tabindex="-1" aria-labelledby="editRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRecordModalLabel">Editar Inventario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-edit-record" onsubmit="return false;" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Catálogo -->
                        <div class="mb-3">
                            <label class="form-label" for="editCatalogoId">Categoria</label>
                            <select id="editCatalogoId" name="catalogoId" class="form-control" required>
                                <option value="">Selecciona una categoria</option>
                                @foreach ($catalogos as $catalogo)
                                    <option value="{{ $catalogo->id }}">{{ $catalogo->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Cantidad en Stock -->
                        <div class="mb-3">
                            <label class="form-label" for="editCantidadStock">Cantidad en Stock</label>
                            <input type="number" id="editCantidadStock" name="cantidadStock" class="form-control"
                                required>
                        </div>
                        <!-- Ubicación -->
                        <div class="mb-3">
                            <label class="form-label" for="editUbicacion">Ubicación</label>
                            <input type="text" id="editUbicacion" name="ubicacion" class="form-control" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">Guardar</button>
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
