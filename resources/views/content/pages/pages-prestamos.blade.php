@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'DataTables - Prestamos')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-buttons/datatables-buttons.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jszip/jszip.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/pdfmake/pdfmake.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-buttons/buttons.html5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-buttons/buttons.print.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-rowgroup/datatables.rowgroup.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            var selectedRecords = [];

            // Inicialización de DataTable con checkboxes
            var table = $('.datatables-basic').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('prestamos.data') }}',
                    type: 'GET'
                },
                columns: [{
                        data: null,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="select-checkbox" data-id="${row.id}">`;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id'
                    },
                    {
                        data: 'receptor.name'
                    },
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
                    }
                ],
                drawCallback: function() {
                    selectedRecords = [];
                    $(".select-checkbox").prop('checked', false);
                    toggleActionButtons();
                }
            });

            // Manejo de los checkboxes de la tabla
            $(document).on('change', '.select-checkbox', function() {
                var recordId = $(this).data('id');
                if ($(this).is(':checked')) {
                    selectedRecords.push(recordId);
                } else {
                    selectedRecords = selectedRecords.filter(id => id !== recordId);
                }
                toggleActionButtons();
            });

            // Activar/desactivar botones de acción según los registros seleccionados
            function toggleActionButtons() {
                if (selectedRecords.length === 1) {
                    $('#edit-selected-btn').prop('disabled', false);
                    $('#delete-selected-btn').prop('disabled', false);
                } else {
                    $('#edit-selected-btn').prop('disabled', true);
                    $('#delete-selected-btn').prop('disabled', true);
                }
            }

            // Editar el préstamo seleccionado
            $('#edit-selected-btn').on('click', function() {
                if (selectedRecords.length === 1) {
                    var recordId = selectedRecords[0];
                    $.get('{{ url('prestamos') }}/' + recordId, function(record) {
                        // Llenar el formulario de edición con los datos
                        $('#edit-id').val(record.id);
                        $('#edit-receptor').val(record.receptor_id);
                        $('#edit-emisor').val(record.emisor_id);
                        $('#edit-herramienta').val(record.herramienta_id);
                        $('#edit-cantidad').val(record.cantidad);
                        $('#edit-fecha_inicio').val(record.fecha_inicio);
                        $('#edit-fecha_limite').val(record.fecha_limite);
                        $('#edit-comentarios').val(record.comentarios);
                        $('#edit-status').val(record.status);

                        $('#form-edit-record').attr('action', '/prestamos/' + record.id);
                        // Mostrar el modal
                        $('#edit-record').offcanvas('show');
                    });
                }
            });

            // Eliminar el préstamo seleccionado
            $('#delete-selected-btn').on('click', function() {
                if (selectedRecords.length === 1) {
                    var recordId = selectedRecords[0];
                    if (confirm('¿Estás seguro de que deseas eliminar este préstamo?')) {
                        $.ajax({
                            url: '/prestamos/' + recordId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                alert(response.message);
                                $('.datatables-basic').DataTable().ajax.reload();
                            },
                            error: function(xhr) {
                                alert('Ocurrió un error al eliminar el préstamo.');
                            }
                        });
                    }
                }
            });

            // Envío del formulario para agregar nuevo préstamo
            $('#form-add-new-record').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route('prestamos.store') }}',
                    type: 'POST',
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
                        $('#form-add-new-record')[0].reset();
                        $('#add-new-record').offcanvas('hide');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        alert('Ocurrió un error al guardar el registro.');
                    }
                });
            });

            // Manejo del formulario de edición
            $('#form-edit-record').on('submit', function(e) {
                e.preventDefault();
                const formData = {
                        _token: '{{ csrf_token() }}',
                        receptorId: $('#edit-receptor').val(), // Receptor seleccionado
                        emisorId: $('#edit-emisor').val(), // Emisor seleccionado
                        herramientaId: $('#edit-herramienta').val(), // Herramienta seleccionada
                        cantidad: $('#edit-cantidad').val(), // Cantidad prestada
                        fecha_inicio: $('#edit-fecha_inicio').val(), // Fecha de inicio
                        fecha_limite: $('#edit-fecha_limite').val(), // Fecha límite de retorno
                        comentarios: $('#edit-comentarios').val(), // Comentarios adicionales
                        status: $('#edit-status').val()
                    };
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'PUT',
                    data: formData,
                    success: function(response) {
                        alert(response.message);
                        $('.datatables-basic').DataTable().ajax.reload();
                        $('#edit-record').offcanvas('hide');
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            let messages = Object.values(errors).map((error) => error.join(
                                '\n')).join('\n');
                            alert('Errores:\n' + messages);
                        } else {
                            alert('Ocurrió un error al guardar los cambios.');
                        }
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
        <button type="button" id="edit-selected-btn" class="btn btn-warning" disabled>Editar</button>
        <button type="button" id="delete-selected-btn" class="btn btn-danger" disabled>Eliminar</button>
        <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#add-new-record">
            Nuevo Préstamo
        </button>
    @endrole
    <!-- DataTable with Buttons -->
    <br><br>
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>ID</th>
                        <th>Receptor</th>
                        <th>Emisor</th>
                        <th>Herramienta</th>
                        <th>Cantidad</th>
                        <th>Fecha de Creación</th>
                        <th>Fecha de Expiración</th>
                        <th>Comentarios</th>
                        <th>Estado</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
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
                            <option value="{{ $herramienta->id }}">{{ $herramienta->descripcion }} -
                                {{ $herramienta->cantidad_stock }} en stock</option>
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

    <!-- Modal para editar préstamo -->
    <div class="offcanvas offcanvas-end" id="edit-record">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="exampleModalLabel">Editar Registro de Préstamo</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <form id="form-edit-record" class="add-new-record pt-0 row g-2" action="{{ route('usuarios.update', ':id') }}" method="POST">
                @csrf
                @method('PUT')
                <!-- ID del préstamo -->
                <input type="hidden" id="edit-id" name="id">

                <div class="col-sm-12">
                    <label class="form-label" for="edit-receptor">Receptor</label>
                    <select id="edit-receptor" name="receptorId" class="form-control" required>
                        <option value="">Selecciona un receptor</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-12">
                    <label class="form-label" for="edit-emisor">Emisor</label>
                    <select id="edit-emisor" name="emisorId" class="form-control" required>
                        <option value="">Selecciona un emisor</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-12">
                    <label class="form-label" for="edit-herramienta">Herramienta</label>
                    <select id="edit-herramienta" name="herramientaId" class="form-control" required>
                        <option value="">Selecciona una herramienta</option>
                        @foreach ($herramientas as $herramienta)
                            <option value="{{ $herramienta->id }}">{{ $herramienta->descripcion }} -
                                {{ $herramienta->cantidad_stock }} en stock</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-12">
                    <label class="form-label" for="edit-cantidad">Cantidad</label>
                    <input type="number" id="edit-cantidad" name="cantidad" class="form-control" required>
                </div>

                <div class="col-sm-12">
                    <label class="form-label" for="edit-fecha_inicio">Fecha de Inicio</label>
                    <input type="datetime-local" id="edit-fecha_inicio" name="fecha_inicio" class="form-control"
                        required>
                </div>

                <div class="col-sm-12">
                    <label class="form-label" for="edit-fecha_limite">Fecha Límite</label>
                    <input type="datetime-local" id="edit-fecha_limite" name="fecha_limite" class="form-control"
                        required>
                </div>

                <div class="col-sm-12">
                    <label class="form-label" for="edit-status">Estado</label>
                    <select id="edit-status" name="status" class="form-control" required>
                        <option value="devuelto">devuelto</option>
                        <option value="atrasado">atrasado</option>
                        <option value="activo">activo</option>
                    </select>
                </div>

                <div class="col-sm-12">
                    <label class="form-label" for="edit-comentarios">Comentarios</label>
                    <textarea id="edit-comentarios" name="comentarios" class="form-control"></textarea>
                </div>

                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary data-submit me-sm-3 me-1">Guardar cambios</button>
                    <button type="reset" class="btn btn-outline-secondary"
                        data-bs-dismiss="offcanvas">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

@endsection
