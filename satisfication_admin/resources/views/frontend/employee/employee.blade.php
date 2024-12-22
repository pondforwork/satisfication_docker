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
                /* General table styles */
                width: 100%;
                /* Set a width for the table */
                border-collapse: collapse;
                /* Combine table borders for cleaner appearance */
                margin: 1em auto;
                /* Add some margin for better spacing */
            }

            th,
            td {
                /* Styles for table cells (headers and data) */
                padding: 0.5em 1em;
                /* Add padding for content within cells */
                /* Add a border around cells */
                text-align: left;
                /* Align content to the left by default */
            }

            thead {
                /* Styles for table headers specifically */
                background-color: #145F9A;
                color: white;
                /* Change background color to red */
                font-weight: bold;
                border-top-left-radius: 10px;

                /* Make headers bolder */
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
    </head>
    <script>
        $(document).ready(function() {

            function fetchEmployee() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ url('/employee/list') }}",
                    method: 'GET',
                    success: function(data) {

                        console.log(data);
                        let tableBody = $('#employeeTable tbody');
                        tableBody.empty(); // Clear existing data
                        data.forEach(function(employee, index) {
                            tableBody.append(`
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td class="text-center">${employee.name}</td>
                        <td class="text-center">${employee.email}</td>
                    </tr>
                `);
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });

                console.log("Fetched");
            }



            fetchEmployee();

        });
    </script>


    <body>
        <div class="container ">
            <div class="row mx-5 mt-3">
                <table id="employeeTable">
                    <thead>
                        <tr class="tr-custom">
                            <th style="border-top-left-radius: 10px">ลำดับ</th>
                            <th>ชื่อ</th>
                            <th style="border-top-right-radius: 10px"> E-mail</th>
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
