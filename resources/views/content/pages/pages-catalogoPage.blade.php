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
                    url: '{{ route('catalogos.data') }}',
                    type: 'GET'
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'partida'
                    },
                    {
                        data: 'created_at',
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return moment(data).format('DD/MM/YYYY HH:mm'); // Formato deseado
                            }
                            return data; // Devuelve el dato sin cambios para otros usos
                        }
                    },
                    {
                        data: 'updated_at',
                        render: function(data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                return moment(data).format('DD/MM/YYYY HH:mm'); // Formato deseado
                            }
                            return data; // Devuelve el dato sin cambios para otros usos
                        }
                    },
                ]
            });

            // Envío del formulario para agregar nuevo registro
            $('#form-add-new-record').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('catalogos.store') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        categoryName: $('#categoryName').val(),
                        partida: $('#partida').val(),
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
        <span class="text-muted fw-light">Tablas /</span> Catalogos
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
                        <th>Nombre</th>
                        <th>Partida</th>
                        <th>Fecha de Creación</th>
                        <th>Actualizado</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--/ DataTable with Buttons -->
    <!-- Modal to add new record -->
    <div class="offcanvas offcanvas-end" id="add-new-record">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="exampleModalLabel">Nueva Categoría</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <form class="add-new-record pt-0 row g-2" id="form-add-new-record" onsubmit="return false">
                <!-- Nombre de la Categoria -->
                <div class="col-sm-12">
                    <label class="form-label" for="categoryName">Nombre de la Categoría</label>
                    <div class="input-group input-group-merge">
                        <span id="categoryNameIcon" class="input-group-text"><i class="bx bx-category"></i></span>
                        <input type="text" id="categoryName" class="form-control" name="categoryName"
                            placeholder="Ejemplo: Herramientas de Mano" aria-label="Nombre de la Categoría"
                            aria-describedby="categoryNameIcon" />
                    </div>
                </div>
                <!-- Partida -->
                <div class="col-sm-12">
                    <label class="form-label" for="partida">Partida</label>
                    <div class="input-group input-group-merge">
                        <span id="partidaIcon" class="input-group-text"><i class="bx bx-clipboard"></i></span>
                        <input type="text" id="partida" name="partida" class="form-control" placeholder="Ejemplo: 1010"
                            aria-label="Partida" aria-describedby="partidaIcon" />
                    </div>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary data-submit me-sm-3 me-1">Guardar</button>
                    <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

@endsection
