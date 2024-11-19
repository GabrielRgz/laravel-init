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
            $('.datatables-basic').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('usuarios.data') }}',
                    type: 'GET'
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'rol', // Esta columna mostrará el rol del usuario
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
                <button class="btn btn-sm btn-primary edit-btn" data-id="${row.id}" data-name="${row.name}" data-email="${row.email}" data-rol="${row.rol}">Editar</button>
                <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">Eliminar</button>
            `;
                        }
                    }
                ]
            });
            $('#form-add-new-record').on('submit', function(e) {
                e.preventDefault(); // Evitar el comportamiento por defecto del formulario

                const formData = $(this).serialize(); // Serializar los datos del formulario

                $.ajax({
                    url: '{{ route('usuarios.store') }}', // Ruta definida en web.php
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Mostrar mensaje de éxito
                        alert(response.message);

                        // Resetear el formulario
                        $('#form-add-new-record')[0].reset();

                        // Actualizar la DataTable
                        $('.datatables-basic').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        // Manejar errores
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            let messages = Object.values(errors).map((error) => error.join(
                                '\n')).join('\n');
                            alert(`Errores:\n${messages}`);
                        } else {
                            alert('Ocurrió un error al guardar el usuario.');
                        }
                    },
                });
            });
            $(document).on('click', '.delete-btn', function() {
                let userId = $(this).data('id');

                // Confirmar eliminación
                if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
                    $.ajax({
                        url: '/usuarios/' + userId, // Ruta para eliminar el usuario
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
                            alert('Ocurrió un error al eliminar el usuario.');
                        }
                    });
                }
            });
            $(document).on('click', '.edit-btn', function() {
                let userId = $(this).data('id');
                let userName = $(this).data('name');
                let userEmail = $(this).data('email');
                let userRol = $(this).data('rol');

                // Llenar el formulario con los datos del usuario
                $('#edit-id').val(userId);
                $('#edit-name').val(userName);
                $('#edit-email').val(userEmail);
                $('#edit-rol').val(userRol);

                // Actualizar la URL del formulario con el id del usuario
                $('#form-edit-record').attr('action', '/usuarios/' + userId); // O usa el helper route()

                // Abrir el modal
                $('#edit-record').offcanvas('show');
            });
            $('#form-edit-record').on('submit', function(e) {
                e.preventDefault(); // Evitar el comportamiento por defecto del formulario

                const formData = $(this).serialize(); // Serializar los datos del formulario

                $.ajax({
                    url: $(this).attr(
                        'action'), // La acción del formulario, que es la ruta de actualización
                    method: 'POST', // Usamos POST para enviar los datos (gracias a @method('PUT'))
                    data: formData,
                    success: function(response) {
                        // Mostrar mensaje de éxito
                        alert(response.message);

                        // Actualizar la tabla sin recargar la página
                        $('.datatables-basic').DataTable().ajax.reload();

                        // Cerrar el modal
                        $('#edit-record').offcanvas('hide');
                    },
                    error: function(xhr) {
                        // Manejar errores si ocurren
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            let messages = Object.values(errors).map((error) => error.join(
                                '\n')).join('\n');
                            alert(`Errores:\n${messages}`);
                        } else {
                            alert('Ocurrió un error al guardar el usuario.');
                        }
                    }
                });
            });


        });
    </script>
@endsection

@section('content')
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Tablas /</span> Usuarios
    </h4>
    @role('admin')
        <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#add-new-record">
            Añadir Nuevo Registro
        </button>
    @endrole
    <br>
    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Fecha de Creación</th>
                        <th>Actualizado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- Modal to add new record -->
    <div class="offcanvas offcanvas-end" id="add-new-record">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="exampleModalLabel">Nuevo Usuario</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
            <form id="form-add-new-record" action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <div class="col-sm-12">
                    <label class="form-label" for="name">Nombre</label>
                    <input type="text" id="name" class="form-control" name="name" required>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="rol">Rol</label>
                    <input type="text" id="rol" class="form-control" name="rol" required>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" class="form-control" name="email" required>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="password">Contraseña</label>
                    <input type="password" id="password" class="form-control" name="password" required>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="password_confirmation">Confirmar Contraseña</label>
                    <input type="password" id="password_confirmation" class="form-control" name="password_confirmation"
                        required>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>


        </div>
    </div>
    <!-- Modal para editar usuario -->
    <div class="offcanvas offcanvas-end" id="edit-record">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title">Editar Usuario</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="form-edit-record" action="{{ route('usuarios.update', ':id') }}" method="POST">
                @csrf
                @method('PUT') <!-- Método PUT para actualizar -->
                <input type="hidden" id="edit-id" name="id">

                <div class="col-sm-12">
                    <label class="form-label" for="edit-name">Nombre</label>
                    <input type="text" id="edit-name" class="form-control" name="name" required>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="edit-rol">Rol</label>
                    <input type="text" id="edit-rol" class="form-control" name="rol" required>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="edit-email">Email</label>
                    <input type="email" id="edit-email" class="form-control" name="email" required>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
    <!--/ DataTable with Buttons -->

    <hr class="my-5">
@endsection
