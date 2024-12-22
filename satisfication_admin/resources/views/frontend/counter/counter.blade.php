<x-app-layout>
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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

            #locationTable td {
                text-align: center;
            }

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
                var id = @json($id);
                var editedId = -1;

                function fetchLocations() {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ url('/getcounter') }}" + "/" + id, // Corrected URL construction
                        method: 'GET',
                        success: function(data) {

                            console.log(data);
                            let tableBody = $('#locationTable tbody');
                            tableBody.empty(); // Clear existing data
                            data.forEach(function(counter, index) {
                                tableBody.append(`
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td class="text-center">${counter.name}</td>
                            <td class="text-center">${counter.client_name}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-primary edit-btn mx-2" id="editCounterBtn" data-id="${location.id}" data-floor_name="${location.floor_name}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button type="button" class="btn btn-outline-danger delete-btn mx-2" id="deleteBtn" data-name="${counter.name}" data-id="${counter.counter_id}">
                                    <i class="bi bi-trash3"></i> Delete
                                </button>
                                <button type="button" class="btn btn-outline-success delete-btn mx-2 print-btn" data-name="${counter.name}" data-id="${counter.counter_id}">
                                    <i class="bi bi-printer"></i> Print
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

                $('#saveButton').on('click', function() {
                    let name = $('#counter_name').val();
                    let formData = {
                        name: name,
                        location_id: id
                    };

                    if (!name) {
                        alert('กรอกข้อมูลชื่อเคาน์เตอร์บริการ');
                        return;
                    }
                    document.getElementById('loadingSave').style.display = 'flex';
                    document.getElementById('counterForm').style.display = 'none';

                    console.log(formData);
                    $.ajax({
                        url: '/counter/saveData',
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

                $(document).on('click', '.print-btn', function() {
                    // Get the counter name and ID from the data attributes
                    var counterName = $(this).data('name');
                    var counterId = $(this).data(
                        'id'); // Note: Use 'data-id' here since that's what you used in the data attribute

                    // Construct the URL with the counter name and ID as query parameters
                    var url = '/genqr?counterName=' + encodeURIComponent(counterName) + '&counterId=' +
                        encodeURIComponent(counterId);

                    // Redirect the user to the generated URL
                    window.location.href = url;
                });

                $(document).on('click', '#deleteBtn', function() {
                    $('#deleteCounterModal').modal('show');
                    var counterName = $(this).data('name');
                    editedId = $(this).data('id');
                    $('#counterNameDisplay').text(counterName);
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
                        url: '/counter/deleteCounter',
                        type: 'DELETE',
                        data: {
                            counter_id: editedId,
                        },
                        success: function(response) {
                            console.log(response.message);
                            location.reload();
                        },
                        error: function(error) {
                            console.error('Error deleting counter:', error);
                            $('#deleteCounterModal').modal('hide');
                            $('#errorModal').modal('show');
                        }
                    });
                });

                fetchLocations()

            });
        </script>
    </head>

    <body>

        <div class="container">
            <div class="row mx-5 mt-3">
                <div class="col-md-6">
                    <h3>จัดการเคาน์เตอร์ {{ $name }}</h3>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-custom-purple" data-bs-toggle="modal"
                        data-bs-target="#addCounterDialog">เพิ่มเคาน์เตอร์</button>
                </div>
            </div>
        </div>

        <div class="container ">
            <div class="row mx-5 mt-3">
                <table id="locationTable">
                    <thead>
                        <tr class="tr-custom">
                            <th style="border-top-left-radius: 10px">ลำดับ</th>
                            <th>ชื่อเคาน์เตอร์</th>
                            <th>ชื่ออุปกรณ์ Client</th>
                            <th style="border-top-right-radius: 10px">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </body>


    <div class="modal" id="addCounterDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <h2>เพิ่มเคาน์เตอร์บริการ</h2>
                    </div>

                    <div class="d-flex justify-content-center align-items-center" id="loadingSaveContainer"
                        style="display: none; height: 100%;">
                        <div class="spinner-grow text-dark" role="status" id="loadingSave">
                            <span class="visually-hidden">Saving</span>
                        </div>
                    </div>

                    <form id="counterForm">
                        <div class="mb-3">
                            <label for="counter_name" class="form-label">ชื่อเคาน์เตอร์บริการ</label>
                            <input type="text" class="form-control" id="counter_name">
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

    <div class="modal" id="deleteCounterModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
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
                            <span id="counterNameDisplay"></span>
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


</x-app-layout>
