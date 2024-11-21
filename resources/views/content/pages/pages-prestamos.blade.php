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
                    url: '{{ route('prestamos.data') }}', // Aquí usamos la nueva ruta
                    type: 'GET'
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'receptor.name'
                    }, // Nombre de la categoría (esto lo definimos en el controlador si es necesario)
                    {
                        data: 'emisor.name'
                    },
                    {
                        data: 'herramienta.id'
                    },
                    {
                        data: 'cantidad'
                    },
                    {
                        data: 'fecha_inicio'
                    },
                    {
                        data: 'fecha_limite'
                    },
                    {
                        data: 'comentarios'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-primary edit-btn"> Editar </button>
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
                    url: '{{ route('prestamos.store') }}', // Usamos la ruta para inventario
                    type: 'POST',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        receptorId: $('#receptorId').val(), // Receptor seleccionado
                        emisorId: $('#emisorId').val(), // Emisor seleccionado
                        herramientaId: $('#herramientaId').val(), // Herramienta seleccionada
                        cantidad: $('#cantidad').val(), // Cantidad prestada
                        fecha_inicio: $('#fecha_inicio').val(), // Fecha de inicio
                        fecha_limite: $('#fecha_limite').val(), // Fecha límite de retorno
                        comentarios: $('#comentarios').val() // Comentarios adicionales
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

        });
    </script>
@endsection

@section('content')
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Tablas /</span> Prestamos
    </h4>
    @role('admin')
        <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#add-new-record">
            Nuevo prestamo
        </button>
    @endrole
    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Receptor</th>
                        <th>Emisor</th>
                        <th>herramienta</th>
                        <th>Cantidad</th>
                        <th>Fecha de Creación</th>
                        <th>Fecha de Expiracion</th>
                        <th>Comentarios</th>
                        <th>Estado</th>
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
                <div class="col-sm-12">
                    <label class="form-label" for="receptorId">Receptor</label>
                    <select id="receptorId" name="receptorId" class="form-control" required>
                        <option value="">Selecciona un receptor</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-12">
                    <label class="form-label" for="emisorId">Emisor</label>
                    <select id="emisorId" name="emisorId" class="form-control" required>
                        <option value="">Selecciona un emisor</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-12">
                    <label class="form-label" for="herramientaId">Herramienta</label>
                    <select id="herramientaId" name="herramientaId" class="form-control" required>
                        <option value="">Selecciona una herramienta</option>
                        @foreach ($herramientas as $herramienta)
                            <option value="{{ $herramienta->id }}">{{ $herramienta->ubicacion }} - {{ $herramienta->cantidad_stock }} en stock</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-12">
                    <label class="form-label" for="cantidad">Cantidad</label>
                    <input type="number" id="cantidad" name="cantidad" class="form-control" required>
                </div>

                <div class="col-sm-12">
                    <label class="form-label" for="fecha_inicio">Fecha de Inicio</label>
                    <input type="datetime-local" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                </div>
                
                <div class="col-sm-12">
                    <label class="form-label" for="fecha_limite">Fecha Límite</label>
                    <input type="datetime-local" id="fecha_limite" name="fecha_limite" class="form-control" required>
                </div>

                <div class="col-sm-12">
                    <label class="form-label" for="comentarios">Comentarios</label>
                    <textarea id="comentarios" name="comentarios" class="form-control"></textarea>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary data-submit me-sm-3 me-1">Guardar</button>
                    <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
