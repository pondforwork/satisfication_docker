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

            #workingtimeTable td {
                text-align: center;
            }

            #workingtimeTable th {
                text-align: center;
            }

            #loadingDelete {
                display: none;
            }

            #loadingSave {
                display: none;
            }
        </style>

        <script>
            var editedId = -1;

            $(document).ready(function() {
                function fetchWorkingTime() {
                    $.ajax({
                        url: "{{ url('/workingtime/list') }}",
                        method: 'GET',
                        success: function(data) {
                            let tableBody = $('#workingtimeTable tbody');
                            tableBody.empty();
                            data.forEach(function(workingtime, index) {
                                tableBody.append(`
                            <tr data-feedback_answer_id="${workingtime.workingtime_shift_id}">
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${workingtime.start_time}</td>
                                <td class="text-center">
                                          ${workingtime.end_time}
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger delete-btn mx-2" id="deleteBtn" data-id="${workingtime.workingtime_shift_id}" data-text="${workingtime.start_time + ' - ' + workingtime.end_time}">
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

                $(document).on('click', '#addWorkingtimeBtn', function() {
                    editedId = null;
                    $('#addWorkingtimeDialog').modal('show');
                });


                $(document).on('click', '#workingtimeSettingsBtn', function() {
                    window.location.href = '/workingtime/settings/';
                });

                $(document).on('click', '#workingtimeHistoryBtn', function() {
                    window.location.href = '/workingtime/historyworkingtime/';
                });

                $(document).on('click', '#saveBtn', function() {
                    var startTimeVal = $('#start-time').val();
                    var endTimeVal = $('#end-time').val();
                    var formattedStartTime = startTimeVal + ':00';
                    var formattedEndTime = endTimeVal + ':00';
                    var startTime = new Date('1970-01-01T' + formattedStartTime);
                    var endTime = new Date('1970-01-01T' + formattedEndTime);

                    // Check if start time is less than end time
                    if (startTime >= endTime || startTime === endTime) {
                        alert('กรุณากรอกเวลาให้ถูกต้อง');
                    } else {
                        document.querySelector('.addformGroup').style.display = 'none';
                        document.getElementById('loadingSave').style.display = 'flex';
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        $.ajax({
                            url: '/workingtime/saveData',
                            type: 'POST',
                            data: {
                                start_time: formattedStartTime,
                                end_time: formattedEndTime
                            },
                            success: function(response) {
                                console.log(response.message);
                                location.reload();
                            },
                            error: function(error) {
                                console.error('Error Adding WorkingTime:', error);
                            }
                        });
                    }
                });

                $(document).on('click', '#deleteBtn', function() {
                    $('#deleteWorkingTimeModal').modal('show');
                    var workingTime = $(this).data('text');
                    editedId = $(this).data('id');
                    console.log(editedId);
                    $('#workingTimeDisplay').text(workingTime);
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
                        url: '/workingtime/deleteData',
                        type: 'DELETE',
                        data: {
                            workingtime_id: editedId,
                        },
                        success: function(response) {
                            console.log(response.message);
                            location.reload();
                        },
                        error: function(error) {
                            console.error('Error deleting location:', error);
                            $('#deleteWorkingTimeModal').modal('hide');
                            $('#errorModal').modal('show');
                        }
                    });
                });


                fetchWorkingTime();

            });
        </script>
    </head>


    <div class="modal" id="addWorkingtimeDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-body">


                    <div class="text-center">
                        <h2>เพิ่มช่วงเวลาทำงาน</h2>
                    </div>

                    <div class="d-flex justify-content-center align-items-center" id="loadingSaveContainer"
                        style="display: none; height: 100%;">
                        <div class="spinner-grow text-dark" role="status" id="loadingSave">
                            <span class="visually-hidden">Saving</span>
                        </div>
                    </div>
                    <div class="addformGroup">
                        <div class="row text-center">
                            <div class="col-md-6">
                                <div class="cs-form">
                                    <label for="start-time">ตั้งแต่</label>
                                    <input type="time" id="start-time" class="form-control" value="00:00" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="cs-form">
                                    <label for="end-time">ถึง</label>
                                    <input type="time" id="end-time" class="form-control" value="00:00" />
                                </div>
                            </div>
                        </div>


                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">ยกเลิก</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <button type="button" class="btn btn-custom-purple" id="saveBtn">บันทึก</button>
                                </div>
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
                    <h3>จัดการเวลาทำงาน</h3>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-custom-purple"
                        id="workingtimeSettingsBtn">ตั้งค่าเวลาการเข้าทำงาน</button>

                    <button type="button" class="btn btn-custom-purple"
                        id="workingtimeHistoryBtn">ประวัติการลงเวลา</button>

                    <button type="button" class="btn btn-custom-purple"
                        id="addWorkingtimeBtn">เพิ่มช่วงเวลาทำงาน</button>
                </div>
            </div>
        </div>

        <div class="container ">
            <div class="row mx-5 mt-3">
                <table id="workingtimeTable">
                    <thead>
                        <tr class="tr-custom">
                            <th style="border-top-left-radius: 10px">ลำดับ</th>
                            <th>เวลาเริ่ม</th>
                            <th>เวลาสิ้นสุด</th>
                            <th style="border-top-right-radius: 10px">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal" id="deleteWorkingTimeModal" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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


                            <div class="text-center">
                                <span id="workingTimeDisplay"></span>
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
