<x-app-layout>

    <head>
        <style>
            .btn-custom-purple {
                background-color: #145F9A;
                border-color: #145F9A;
                color: white;
            }

            .btn-custom-purple:hover {
                background-color: #145F9A;
                border-color: #145F9A;
                color: white;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin: 1em auto;
            }

            th,
            td {
                padding: 0.5em 1em;
                text-align: left;
            }

            thead {
                background-color: #145F9A;
                color: white;
                font-weight: bold;
                border-top-left-radius: 10px;
            }

            .tr-custom {
                border-top-left-radius: 10px;
            }

            tbody {
                border: 1px solid #ddd;
            }

            #employeeTable td {
                text-align: center;
            }

            #employeeTable th {
                text-align: center;
            }

            #loading {
                display: none;
            }
        </style>

        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Include CSRF token -->
    </head>
    <script>
        $(document).ready(function() {
            var sessionId = @json(session('user_id')) || -1;

            function fetchEmployee() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                function fetchAllRoles() {
                    return $.ajax({
                        url: "{{ url('/role/all') }}",
                        method: 'GET'
                    });
                }

                function fetchUsers() {
                    return $.ajax({
                        url: "{{ url('/role/list') }}",
                        method: 'GET'
                    });
                }

                $.when(fetchAllRoles(), fetchUsers())
                    .done(function(rolesData, usersData) {
                        let roles = rolesData[0];
                        let users = usersData[0];

                        let tableBody = $('#employeeTable tbody');
                        tableBody.empty();

                        users.forEach(function(employee, index) {
                            let roleOptions = '';
                            roles.forEach(function(roleObj) {
                                let role = roleObj.role;
                                let selected = employee.role === role ? 'selected' : '';
                                let disabled = employee.id === sessionId ? 'disabled' : '';
                                roleOptions +=
                                    `<option value="${role}" ${selected} ${disabled}>${role}</option>`;
                            });

                            tableBody.append(`
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td class="text-center">${employee.name}</td>
                            <td class="text-center">
                                <select class="form-select role-select" data-user-id="${employee.id}">
                                    ${roleOptions}
                                </select>
                            </td>
                        </tr>
                `);
                        });
                        $('.role-select').on('change', function() {
                            let selectedRole = $(this).val();
                            let userId = $(this).data(
                                'user-id');
                            console.log(userId);

                            console.log(`User ID: ${userId}, Selected role: ${selectedRole}`);
                            let newRole = $(this).val();
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                url: "{{ url('/role/update') }}",
                                method: 'POST',
                                data: {
                                    id: userId,
                                    role: newRole,
                                    sessionUserId: sessionId
                                },
                                success: function(response) {
                                    console.log(response
                                        .message); // Log the success message
                                },
                                error: function(xhr) {
                                    console.error('Error:', xhr
                                        .responseText); // Log error if any
                                }
                            });
                        });
                    })
                    .fail(function(xhr) {
                        console.error(xhr.responseText);
                    });

                console.log("Fetched");
            }





            fetchEmployee();

        });
    </script>


    <body>

        <div class="container">
            <div class="row mx-5 mt-3">
                <div class="col-md-6">
                    <h3>จัดการสิทธิ์การใช้งาน</h3>
                </div>
            </div>
        </div>

        <div class="container ">
            <div class="row mx-5 mt-3">
                <table id="employeeTable">
                    <thead>
                        <tr class="tr-custom">
                            <th style="border-top-left-radius: 10px">ลำดับ</th>
                            <th>ชื่อ</th>
                            <th style="border-top-right-radius: 10px">Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</x-app-layout>
