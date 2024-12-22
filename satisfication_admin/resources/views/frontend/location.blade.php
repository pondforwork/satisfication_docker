<x-app-layout>
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Include CSRF token -->

        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
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

            /* Center text and buttons in table cells */
            #locationTable td {
                text-align: center;
            }

            /* Optional: Center text in table headers */
            #locationTable th {
                text-align: center;
            }

            #loadingSave {
                display: none;
            }

            #loadingDelete {
                display: none;
            }
        </style>
        <script>
            $(document).ready(function() {
                var editedId = -1;

                function fetchLocations() {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ url('/location/list') }}",
                        method: 'GET',
                        success: function(data) {
                            let tableBody = $('#locationTable tbody');
                            tableBody.empty(); // Clear existing data
                            data.forEach(function(location, index) {
                                tableBody.append(`
                                    <tr>
                                        <td class="text-center">${index + 1}</td>
                                        <td class="text-center">${location.name}</td>
                                        <td class="text-center">${location.code}</td>
                                        <td class="text-center">
                                                <button type="button" class="btn btn-outline-primary edit-btn mx-2" id="editCounterBtn" data-id="${location.location_id}" data-floor_name="${location.name}">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-outline-danger delete-btn mx-2" id="deleteBtn" data-id="${location.location_id}" data-name="${location.name}"">
                                                    <i class="bi bi-trash3"></i> Delete
                                                </button>
                                              
                                          
                                        </td>
                                    </tr>
                                `);
                            });
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                        }
                    });
                }
                fetchLocations();

                $('#saveButton').on('click', function() {
                    let name = $('#location_name').val();
                    let formData = {
                        location_name: name,
                    };

                    if (!name) {
                        alert('กรอกข้อมูลชื่อสถานที่');
                        return;
                    }
                    document.getElementById('loadingSave').style.display = 'flex';
                    document.getElementById('locationForm').style.display = 'none';

                    console.log(formData);
                    $.ajax({
                        url: 'location/saveData',
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            console.log('Success:', response);
                            location.reload();
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr.responseText);
                        }
                    });
                });

                $(document).on('click', '#editCounterBtn', function() {
                    let locationId = $(this).data('id');
                    let locationName = $(this).data('floor_name');
                    if (locationId) {
                        window.location.href = '/counter/' + locationId + '/' + locationName;
                    } else {
                        console.log("nothing");
                        console.error('Location ID is not defined.');
                    }
                });

                $(document).on('click', '#deleteBtn', function() {
                    console.log("Delete");
                    $('#deleteLocationModal').modal('show');
                    var loacationName = $(this).data('name');
                    editedId = $(this).data('id');
                    console.log(editedId);
                    $('#locationNameDisplay').text(loacationName);
                });

                $(document).on('click', '#confirmDelete', function() {
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    console.log(editedId);
                    document.getElementById('loadingDelete').style.display = 'flex';
                    document.querySelector('.deleteFormGroup').style.display = 'none';
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: '/location/deleteData',
                        type: 'DELETE',
                        data: {
                            location_id: editedId,
                        },
                        success: function(response) {
                            console.log(response.message);
                            location.reload();
                        },
                        error: function(error) {
                            console.error('Error deleting location:', error);
                            $('#deleteLocationModal').modal('hide');
                            $('#errorModal').modal('show');
                        }
                    });
                });
            });
        </script>
    </head>
    <div class="modal" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-body">
                    <div class="text-center">
                        <h2>เพิ่มสถานที่</h2>
                    </div>

                    <div class="d-flex justify-content-center align-items-center" id="loadingSaveContainer"
                        style="display: none; height: 100%;">
                        <div class="spinner-grow text-dark" role="status" id="loadingSave">
                            <span class="visually-hidden">Saving</span>
                        </div>
                    </div>

                    <form id="locationForm">
                        <div class="mb-3">
                            <label for="location_name" class="form-label">ชื่อสถานที่</label>
                            <input type="text" class="form-control" id="location_name">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <button type="button" class="btn btn-secondary "
                                        data-bs-dismiss="modal">ยกเลิก</button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-center">
                                    <button type="button" class="btn btn btn-custom-purple"
                                        id="saveButton">บันทึก</button>
                                </div>
                            </div>

                        </div>


                    </form>
                </div>

            </div>
        </div>
    </div>

    <body>
        <div class="container">
            <div class="row mx-5 mt-3">
                <div class="col-md-6">
                    <h3>จัดการสถานที่</h3>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-custom-purple" data-bs-toggle="modal"
                        data-bs-target="#staticBackdrop">เพิ่มสถานที่</button>
                </div>
            </div>
        </div>

        <div class="container ">
            <div class="row mx-5 mt-3">
                <table id="locationTable">
                    <thead>
                        <tr class="tr-custom">
                            <th style="border-top-left-radius: 10px">ลำดับ</th>
                            <th>ชื่อสถานที่</th>
                            <th>รหัสสถานที่</th>
                            <th style="border-top-right-radius: 10px">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>


        <div class="modal" id="deleteLocationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered custom-modal-width">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="text-center">
                            <h2>ลบข้อมูลหรือไม่</h2>
                        </div>

                        <div class="d-flex justify-content-center align-items-center" id="loadingDeleteContainer"
                            style="display: none; height: 100%;">
                            <div class="spinner-grow text-dark" role="status" id="loadingDelete">
                                <span class="visually-hidden">Saving</span>
                            </div>
                        </div>

                        <div class="deleteFormGroup">
                            <div class="text-center">
                                <span id="locationNameDisplay"></span>
                            </div>

                            <div class="row" id='button-group-sync'>
                                <div class="col-md-6">
                                    <div class="text-center">
                                        <button type="button" class="btn btn-secondary "
                                            data-bs-dismiss="modal">ยกเลิก</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-center">
                                        <button type="button" class="btn btn btn-custom-purple"
                                            id="confirmDelete">บันทึก</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div class="modal" id="errorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered custom-modal-width">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="text-center">
                            <h2>Error</h2>
                        </div>

                        <div class="text-center">
                            <h2>ลบข้อมูลไม่สำเร็จ</h2>
                        </div>


                        <div class="row" id='button-group-sync'>
                            <div class="col-md-12">
                                <div class="text-center">
                                    <button type="button" class="btn btn-secondary "
                                        data-bs-dismiss="modal">ปิด</button>
                                </div>
                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </div>

    </body>



    </html>
</x-app-layout>
