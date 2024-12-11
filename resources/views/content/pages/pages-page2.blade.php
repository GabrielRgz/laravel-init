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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            var selectedUsers = [];

            // Inicializamos el DataTable
            $('.datatables-basic').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('usuarios.data') }}',
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
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'rol', // Esta columna mostrará el rol del usuario
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
                    }
                ],
                drawCallback: function(settings) {
                    // Reset selected users on table redraw
                    selectedUsers = [];
                    $(".select-checkbox").prop('checked', false);
                }
            });

            $('#form-add-new-record').on('submit', function(e) {
                e.preventDefault(); // Evitar el comportamiento por defecto del formulario

                const formData = $(this).serialize(); // Serializar los datos del formulario

                $.ajax({
                    url: '{{ route('usuarios.store') }}', // Ruta definida en web.php
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        // Verificar la respuesta del servidor
                        if (response.success) { // Suponiendo que tu controlador devuelve 'success' como true
                            // Mostrar mensaje de éxito
                            alert(response.message);

                            // Resetear el formularios
                            $('#form-add-new-record')[0].reset();

                            $('#add-new-record').offcanvas('hide');
                            // Recargar la DataTable para mostrar los datos actualizados
                            $('.datatables-basic').DataTable().ajax.reload(); // Recarga sin mover a la primera página
                        } else {
                            alert('Ocurrió un error al agregar el usuario.');
                        }
                    },
                    error: function(xhr) {
                        // Manejar errores de AJAX
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            let messages = Object.values(errors).map((error) => error.join(
                                '\n')).join('\n');
                            alert('Errores:\n' + messages);
                        } else {
                            alert('Ocurrió un error al guardar el usuario.');
                        }
                    },
                });
            });


            // Manejar la selección de checkboxes
            $(document).on('change', '.select-checkbox', function() {
                let userId = $(this).data('id');
                if ($(this).is(':checked')) {
                    selectedUsers.push(userId);
                } else {
                    selectedUsers = selectedUsers.filter(id => id !== userId);
                }
                toggleActionButtons();
            });

            // Función para habilitar/deshabilitar botones de acción según la selección
            function toggleActionButtons() {
                if (selectedUsers.length === 1) {
                    $('#edit-selected-btn').prop('disabled', false);
                    $('#delete-selected-btn').prop('disabled', false);
                } else {
                    $('#edit-selected-btn').prop('disabled', true);
                    $('#delete-selected-btn').prop('disabled', true);
                }
            }

            // Editar el usuario seleccionado
            $('#edit-selected-btn').on('click', function() {
                if (selectedUsers.length === 1) {
                    let userId = selectedUsers[0];

                    // Obtener los datos del usuario con AJAX
                    $.get('{{ url('usuarios') }}/' + userId, function(user) {
                        // Llenar el formulario de edición con los datos del usuario
                        $('#edit-id').val(user.id);
                        $('#edit-name').val(user.name);
                        $('#edit-email').val(user.email);

                        // Llenar el campo de rol en base al rol del usuario
                        // Asumimos que el usuario solo tiene un rol, si tiene más, necesitarás ajustar esto
                        $('#edit-rol').val(user.roles[0]
                            .name
                            ); // Asumiendo que roles es un array y que el primer rol es el activo

                        // Actualizar la acción del formulario con el ID del usuario
                        $('#form-edit-record').attr('action', '/usuarios/' + user.id);

                        // Mostrar el modal
                        $('#edit-record').offcanvas('show');
                    });
                }
            });

            // Eliminar el usuario seleccionado
            $('#delete-selected-btn').on('click', function() {
                if (selectedUsers.length === 1) {
                    let userId = selectedUsers[0];
                    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
                        $.ajax({
                            url: '/usuarios/' + userId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                alert(response.message);
                                $('.datatables-basic').DataTable().ajax.reload();
                            },
                            error: function(xhr) {
                                alert('Ocurrió un error al eliminar el usuario.');
                            }
                        });
                    }
                }
            });

            // Manejo del formulario de edición
            $('#form-edit-record').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
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
        <span class="text-muted fw-light">Tablas /</span> Usuarios
    </h4>
    @role('admin')
        <button type="button" id="edit-selected-btn" class="btn btn-warning" disabled>Editar</button>
        <button type="button" id="delete-selected-btn" class="btn btn-danger" disabled>Eliminar</button>
        <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#add-new-record">
            Añadir Nuevo Registro
        </button>
    @endrole
    <!-- DataTable with Buttons -->
    <br><br>
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th> <!-- Check all -->
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Fecha de Creación</th>
                        <th>Actualizado</th>
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
                    <select id="rol" name="rol" class="form-control" required>
                        @foreach (\Spatie\Permission\Models\Role::all() as $rol)
                            <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                        @endforeach
                    </select>
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
                </div><br>
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
                    <select id="edit-rol" name="rol" class="form-control" required>
                        @foreach (\Spatie\Permission\Models\Role::all() as $rol)
                            <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12">
                    <label class="form-label" for="edit-email">Email</label>
                    <input type="email" id="edit-email" class="form-control" name="email" required>
                </div><br>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
    <!--/ DataTable with Buttons -->

    <hr class="my-5">
@endsection
