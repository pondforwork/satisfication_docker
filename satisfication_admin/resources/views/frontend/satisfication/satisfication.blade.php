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

            #locationTable td {
                text-align: center;
            }

            #locationTable th {
                text-align: center;
            }

            #loadingDelete {
                display: none;
            }

            #loadingAdd {
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
                        url: "{{ url('/satisfication/list') }}",
                        method: 'GET',
                        success: function(data) {
                            let tableBody = $('#locationTable tbody');
                            tableBody.empty();
                            data.forEach(function(feedback, index) {
                                tableBody.append(`
                            <tr data-feedback_answer_id="${feedback.feedback_answer_id}">
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${feedback.text}</td>
                                <td class="text-center d-flex justify-content-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="toggleIsActive-${index}" ${feedback.is_active === 1 ? 'checked' : ''} 
                                            onchange="updateIsActive(this, '${feedback.text}', ${feedback.feedback_answer_id})">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-secondary move-up-btn mx-2">
                                                    <i class="bi bi-arrow-up"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary move-down-btn mx-2">
                                                    <i class="bi bi-arrow-down"></i> 
                                                </button>
                                        
                                        <button type="button" class="btn btn-outline-danger delete-btn mx-2" id="deleteBtn" data-id="${feedback.feedback_answer_id}" data-text="${feedback.text}">
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

                function updateRowNumbers() {
                    var orderData = [];
                    $('#locationTable tbody tr').each(function(index) {
                        $(this).find('td:first').text(index + 1);
                        orderData.push({
                            feedback_answer_id: $(this).data(
                                'feedback_answer_id'), // Get feedback_answer_id from data attribute
                            order_no: index + 1 // Update order_no
                        });
                    });
                    console.log(orderData);
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: '/feedbackanswer/reorderData',
                        type: 'POST',
                        data: {
                            orderData: orderData,
                            _token: csrfToken

                        },
                        success: function(response) {
                            console.log(response.message);
                        },
                        error: function(error) {
                            console.error('Error updating order:', error);
                        }
                    });
                }

                $(document).on('click', '#saveButton', function() {
                    let feedbackAnswer = $('#editText').val();
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    if (!feedbackAnswer) {
                        alert('กรอกข้อมูลคำติชม');
                        return;
                    }

                    $('#editText').hide();
                    document.querySelector('.satisficationFormGroup').style.display = 'none';
                    document.getElementById('loadingAdd').style.display = 'flex';

                    $('#loading').show();

                    $.ajax({
                        url: '/feedbackanswer/saveData',
                        type: 'POST',
                        data: {
                            feedbackAnswerId: editedId,
                            text: feedbackAnswer,
                            _token: csrfToken
                        },
                        success: function(response) {
                            console.log(response.message);
                            location.reload();
                        },
                        error: function(error) {
                            console.error('Error updating order:', error);
                        }
                    });
                });

                $(document).on('click', '#addFeedback', function() {
                    editedId = null;
                    $('#addFeedbackDialog').modal('show');
                });

                $(document).on('click', '#editCounterBtn', function() {
                    const editText = document.getElementById('editText');
                    var feedbackAnswerId = $(this).closest('tr').data('feedback_answer_id');
                    // รับค่าจาก Column ที่ 2 
                    var feedbackText = $(this).closest('tr').find('td').eq(1).text();
                    // กำหนดค่า Id ที่จะอัพเดท
                    editedId = feedbackAnswerId;
                    $('#addFeedbackDialog').modal('show');

                    $('#editText').val(feedbackText);
                });

                $(document).on('click', '.move-up-btn', function() {
                    var row = $(this).closest('tr');
                    if (row.prev().length) {
                        row.insertBefore(row.prev());
                        updateRowNumbers();
                    }
                });


                $(document).on('click', '.move-down-btn', function() {
                    var row = $(this).closest('tr');
                    if (row.next().length) {
                        row.insertAfter(row.next());
                        updateRowNumbers();
                    }
                });

                $(document).on('click', '#deleteBtn', function() {
                    console.log("Delete");
                    $('#deleteFeedbackModal').modal('show');
                    var feedbackText = $(this).data('text');
                    editedId = $(this).data('id');
                    console.log(editedId);
                    $('#feedbackTextDisplay').text(feedbackText);
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
                        url: '/feedbackanswer/deleteData',
                        type: 'DELETE',
                        data: {
                            feedbackAnswerId: editedId,
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



                window.updateIsActive = function(checkbox, locationText, feedbackAnswerId) {
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    var isActive = 0;

                    console.log('Switch toggled for', locationText, '. Checked:', checkbox.checked, 'Id',
                        feedbackAnswerId);

                    if (checkbox.checked === true) {
                        isActive = 1;
                    } else {
                        isActive = 0;
                    }

                    console.log(isActive);

                    // let formData = {
                    //     newIsActive: isActive,
                    //     targetFeedbackAnswerId: feedbackAnswerId,
                    //     // _token: csrfToken
                    // };

                    let formData = {
                        newIsActive: isActive,
                        targetFeedbackAnswerId: feedbackAnswerId,
                        // _token: csrfToken
                    };

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: 'satisfication/updateData',
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            console.log('Success:', response);
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr.responseText);
                        }
                    });
                };

                fetchLocations();
            });
        </script>
    </head>


    <div class="modal" id="addFeedbackDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-body">

                    <div class="text-center">
                        <h2>จัดการคำติชม</h2>
                    </div>
                    <form id="locationForm">
                        <div class="satisficationFormGroup"> 
                            <div class="mb-3">
                                <label for="feedbackAnswer" class="form-label">ข้อความติชม</label>
                                <input type="text" class="form-control" id="editText">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-center">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">ยกเลิก</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-center">
                                        <button type="button" class="btn btn-custom-purple"
                                            id="saveButton">บันทึก</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center align-items-center" id="loadingContainer"
                            style="display: none; height: 100%;">
                            <div class="spinner-grow text-dark" role="status" id="loadingAdd">
                                <span class="visually-hidden">Saving</span>
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
                    <h3>จัดการคำติชม</h3>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-custom-purple" id="addFeedback">เพิ่มคำติชม</button>
                </div>
            </div>
        </div>

        <div class="container ">
            <div class="row mx-5 mt-3">
                <table id="locationTable">
                    <thead>
                        <tr class="tr-custom">
                            <th style="border-top-left-radius: 10px">ลำดับ</th>
                            <th>คำติชม</th>
                            <th>เปิดใช้งาน</th>
                            <th style="border-top-right-radius: 10px">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal" id="deleteFeedbackModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
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
                                <span id="feedbackTextDisplay"></span>
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
    </body>

    </html>
</x-app-layout>
