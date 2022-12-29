@section('user-management')
    @include('sweetalert::alert')
    @if (isset(Auth::user()->role) && Auth::user()->role != 'admin')
        <script>
            window.history.back()
        </script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.all.min.js"></script>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css'>

    <!-- CSS -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <!-- Default theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
    <!-- JavaScript -->
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>


    <div class="container-fluid text-center text-white mt-2 p-2 m-0 mb-3" style="background-color: #00305F;">
        <p class="fs-1 m-0">User Management
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                class="ms-3 bi bi-plus-circle-fill" viewBox="0 0 16 16" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasaddUsersModal">
                <path
                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z" />
            </svg>
        </p>
    </div>
    <div class="container mx-auto mb-5">

        <div class="table-responsive">
            <table id="usersTable" class="table table-striped table-bordered dt-responsive " style="width:100%">
                <thead>
                    <tr>
                        <th class="d-none">ID</th>
                        <th>Image</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    {{-- Delete Users Modal --}}
    <div class="modal fade" id="deleteUsersModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Are you sure you want to delete?</h6>
                    <input type="hidden" name="id" id="user_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="delete_user_btn btn btn-primary">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <div id="offcanvaseditUsersModal" aria-labelledby="offcanvasWithBothOptionsLabel" class="offcanvas offcanvas-end w-50"
        data-bs-scroll="true" tabindex="-1">
        <div class="offcanvas-header text-white" style="background-color: #00305F;">
            <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Edit User</h4>
            <button class="btn btn-secondary" data-bs-dismiss="offcanvas">x</button>
        </div>
        <div class="offcanvas-body" style="background-color: #E4E9F7;">
            <div class="container-fluid">
                <form id="updateUser" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <ul class="alert alert-warning d-none" id="update_errorlist" style="padding-left: 35px"></ul>
                        <ul class="alert alert-success d-none" id="success_update"></ul>

                        <input type="hidden" name="edit_user_id" id="edit_user_id">
                        <label for="" class="form-label">Full Name:</label>
                        <input type="text" class="form-control" placeholder="Full Name" id="edit_full_name"
                            name="full_name" required>

                        <label for="" class="form-label mt-2">Username</label>
                        <input type="text" class="form-control" placeholder="Username" id="edit_full_username"
                            name="username" required>

                        <label for="" class="form-label mt-2">Email:</label>
                        <input type="email" class="form-control" placeholder="Email" id="edit_full_email" name="email"
                            required>

                        <label for="" class="form-label mt-2">Department: </label>
                        <select class="form-control" name="department" id="edit_department" required>
                            <option value="none">None</option>
                            <option value="BS-ED">Secondary Education</option>
                            <option value="BS-ECD">Early Childhood Education</option>
                            <option value="BS-Architecture">Architecture</option>
                            <option value="ABEL">AB English Language</option>
                            <option value="BS-IT">IT</option>
                            <option value="BS-Math">Math</option>
                            <option value="BS-CE">Civil Engineering</option>
                            <option value="BS-ME">Mechanical Engineering</option>
                            <option value="BS-COE">Computer Engineering</option>
                            <option value="BS-EE">Electrical Engineering</option>
                        </select>

                        <label for="" class="form-label mt-2">New Password:</label>
                        <input type="password" class="form-control" placeholder="New Password" name="password">

                        <label for="" class="form-label mt-2">Confirm Password:</label>
                        <input type="password" class="form-control" placeholder="Confirm Password"
                            name="confirm_password" id="edit_full_confirm_password">

                        <label for="" class="form-label mt-2">User Image:</label>
                        <input type="file" class="form-control" name="image" id="image">

                        <label for="" class="form-label mt-2">Role: </label>
                        <select class="form-select" name="role" required>
                            <option value="teaching">Teaching</option>
                            <option value="non-teaching">Non-Teaching</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-secondary mt-2 float-end"
                        data-bs-dismiss="offcanvas">Close</button>
                    <button type="submit" class="btn btn-primary mx-2 mt-2 float-end">Update</button>
                </form>
            </div>
        </div>
    </div>

    <div id="offcanvasaddUsersModal" aria-labelledby="offcanvasWithBothOptionsLabel" class="offcanvas offcanvas-end w-50"
        data-bs-scroll="true" tabindex="-1">
        <div class="offcanvas-header text-white" style="background-color: #00305F;">
            <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Add User</h4>
            <button class="btn btn-secondary" data-bs-dismiss="offcanvas">x</button>
        </div>
        <div class="offcanvas-body" style="background-color: #E4E9F7;">
            <div class="container-fluid">
                <form id="createUser" action="{{ route('user.create') }}" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <ul class="alert alert-warning d-none" id="save_errorlist" style="padding-left: 35px"></ul>
                        <label for="" class="form-label">Full Name:</label>

                        <input type="text" class="form-control" placeholder="Full Name" name="full_name"
                            id="full_name" required>
                        <label for="" class="form-label mt-2">Username</label>

                        <input type="text" class="form-control" placeholder="Username" name="username"
                            id="username" required>
                        <label for="" class="form-label mt-2">Email:</label>
                        <input type="email" class="form-control" placeholder="Email" name="email" id="email"
                            required>

                        <label for="" class="form-label mt-2">Department: </label>
                        <select class="form-control" name="department" required>
                            <option value="none">None</option>
                            <option value="BS-ED">Secondary Education</option>
                            <option value="BS-ECD">Early Childhood Education</option>
                            <option value="BS-Architecture">Architecture</option>
                            <option value="ABEL">AB English Language</option>
                            <option value="BS-IT">IT</option>
                            <option value="BS-Math">Math</option>
                            <option value="BS-CE">Civil Engineering</option>
                            <option value="BS-ME">Mechanical Engineering</option>
                            <option value="BS-COE">Computer Engineering</option>
                            <option value="BS-EE">Electrical Engineering</option>
                        </select>

                        <label for="" class="form-label mt-2">Password:</label>
                        <input type="password" class="form-control" placeholder="Password" name="password"
                            id="password" required>

                        <label for="" class="form-label mt-2">Confirm Password:</label>
                        <input type="password" class="form-control" placeholder="Confirm Password"
                            name="confirm_password" required>

                        <label for="" class="form-label mt-2">Role: </label>
                        <select class="form-select" name="role" required>
                            <option value="teaching">Teaching</option>
                            <option value="non-teaching">Non-Teaching</option>
                        </select>

                        <label for="" class="form-label mt-2">User Image:</label>
                        <input type="file" class="form-control" name="image" id="image" required>
                    </div>
                    <button type="button" class="btn btn-secondary mt-2 float-end"
                        data-bs-dismiss="offcanvas">Close</button>
                    <button type="submit" class="btn btn-primary mx-2 mt-2 float-end">Add</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            let usersTable = $('#usersTable').DataTable();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#offcanvaseditUsersModal').on('hidden.bs.offcanvas', function() {
                $('#update_errorlist').html("");
                $('#update_errorlist').addClass('d-none');
            });

            $('#offcanvasaddUsersModal').on('hidden.bs.offcanvas', function() {
                $('#save_errorlist').html("");
                $('#save_errorlist').addClass('d-none');
            });

            fetchUsers();

            function fetchUsers() {
                $.ajax({
                    type: "GET",
                    url: "/admin/user-management/fetch",
                    dataType: "json",
                    success: function(response) {
                        usersTable.clear().draw();
                        $.each(response.users, function(key, item) {
                            const tr = $('<tr><td class="d-none">' + item.id + '</td>' +
                                '<td><img src="{{ url('/assets/users') }}/' + item.image +
                                '" width="100px" height="100px" alt="image" class="rounded-circle">' +
                                '</td>' +
                                '<td>' + item.name + '</td>' +
                                '<td>' + item.username + '</td>' +
                                '<td>' + item.email + '</td>' +
                                '<td>' + item.department + '</td>' +
                                '<td>' + item.role.toUpperCase() + '</td>' +
                                '<td><button type = "button" value ="' + item.id +
                                '" class="edit_btn btn btn-success btn-xs pt-2 px-2">  <i class="bx bx-edit bx-sm"></i></button></td>' +
                                '<td><button type = "button" value ="' + item.id +
                                '" class="delete_btn btn btn-danger btn-xs pt-2 px-2"><i class="bx bx-trash-alt bx-sm"></i></button></td>'
                            );
                            usersTable.row.add(tr[0]).draw();
                        });
                    }
                });
            }

            $(document).on('click', '.edit_btn', function(e) {
                e.preventDefault();
                const id = $(this).val();
                $('#edit_user_id').val(id);
                $('#offcanvaseditUsersModal').offcanvas('show');

                $.ajax({
                    type: "GET",
                    url: "/admin/user-management/edit/" + id,
                    success: function(response) {
                        if (response.status === 404) {
                            $('#offcanvaseditUsersModal').offcanvas('hide');
                        } else {
                            $('#edit_full_name').val(response.users.name);
                            $('#edit_full_username').val(response.users.username);
                            $('#edit_full_email').val(response.users.email);
                            $('#edit_department').val(response.users.department);
                            $('#edit_role').val(response.users.role);
                        }
                    }
                });
            });

            $(document).on('submit', '#updateUser', function(e) {
                e.preventDefault();
                var id = $('#edit_user_id').val();
                let EditFormData = new FormData($('#updateUser')[0]);

                $.ajax({
                    type: "POST",
                    url: "/admin/user-management/update/" + id,
                    data: EditFormData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.status === 400) {
                            $('#update_errorlist').html("");
                            $('#update_errorlist').removeClass('d-none');
                            $.each(response.errors, function(key, error_value) {
                                $('#update_errorlist').append('<li>' + error_value +
                                    '</li>');
                            });
                        } else if (response.status == 200) {
                            fetchUsers();
                            swal(
                                'User Updated',
                                'User Updated Successfully',
                                'success'
                            )
                            $('#offcanvaseditUsersModal').offcanvas('hide');
                        }

                    }
                });
            });

            $(document).on('click', '.delete_btn', function(e) {
                e.preventDefault();
                var user_id = $(this).val();
                $('#deleteUsersModal').modal('show');
                $('#user_id').val(user_id);
            });

            $(document).on('click', '.delete_user_btn', function(e) {
                e.preventDefault();

                var id = $('#user_id').val();

                $.ajax({
                    type: "DELETE",
                    url: "/admin/user-management/delete/" + id,
                    dataType: "json",
                    success: function(response) {
                        if (response.head_project_count > 0) {
                            swal(
                                'User Cannot Delete',
                                'User is assigned to a project',
                                'error'
                            )
                        }
                        if (response.status == 404) {
                            $('#deleteUsersModal').modal('hide');
                        } else if (response.status == 200) {
                            fetchUsers();
                            swal(
                                'User Deleted',
                                'User Deleted Successfully',
                                'error'
                            )
                            $('#deleteUsersModal').modal('hide');

                        }
                    }
                });
            });

            $(document).on('submit', '#createUser', function(e) {
                e.preventDefault();
                let formData = new FormData($('#createUser')[0]);
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.create') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.status == 400) {
                            $('#save_errorlist').html("");
                            $('#save_errorlist').removeClass('d-none');
                            $.each(response.errors, function(key, error_value) {
                                $('#save_errorlist').append('<li>' + error_value +
                                    '</li>');
                            });
                        } else if (response.status === 200) {
                            fetchUsers();
                            $('#createUser')[0].reset();
                            swal(
                                'User Added',
                                'User Added Successfully',
                                'success'
                            )
                            $('#offcanvasaddUsersModal').offcanvas('hide');
                        }
                    }
                });
            });
        });
    </script>
@endsection
