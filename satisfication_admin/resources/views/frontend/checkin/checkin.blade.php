<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js">
    </script>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>


<style>
    .headerText {
        margin-top: 150;
        font-size: 110;
        color: purple;
    }

    .contentText {
        margin-top: 400;
        font-size: 90;
        color: purple;
    }

    .dialogText {
        margin-top: 100;
        font-size: 50;
        text-align: center;
        color: purple;
    }

    .btn-custom-purple {
        background-color: #145F9A;
        border-color: #145F9A;
        margin-top: 150;
        padding: 30px 170px;
        font-size: 24px;
        color: white;
        font-size: 50;
    }

    .btn-dialog-purple {
        background-color: #145F9A;
        border-color: #145F9A;
        margin-top: 250;
        padding: 30px 50px;
        font-size: 50px;
        color: white;
    }

    .btn-custom-purple:hover {
        background-color: #145F9A;
        border-color: #145F9A;
        color: white;
    }

    .input-sm {
        padding: 50px;
        font-size: 80px;
    }
</style>

<script>
    $(document).ready(function() {
        var currentUrl = window.location.href;
        var username = @json(session('username')) || '';
        var userId = @json(session('user_id')) || -1;
        var counterName = @json($counter_name);
        var counterId = @json($counter_id);
        var workingTimeShiftId = null;
        var isLate = false;

        // แสดง Loading เมื่อ ajax ทำงาน
        $(document).ajaxStart(function() {
            $.LoadingOverlay("show");
        });

        $(document).ajaxStop(function() {
            $.LoadingOverlay("hide");
        });

        $('#loginBtn').on('click', function() {
            console.log("Login Pressed")
            if (typeof(Storage) !== "undefined") {
                console.log("LocalStorage is supported");
            } else {
                console.log("LocalStorage is not supported");
            }
            if (username) {
                $.ajax({
                    url: `/workingtime/checkinNow`,
                    method: 'GET',
                    success: function(data) {
                        if (data.isAvail === false) {
                            // ไม่อยู่ในช่วงลงเวลางาน
                            $('#notInTimeDialog').modal('show');
                        } else {
                            isLate = data.isLate;
                            $('#startTimeDisplay').text(data.start_time);
                            workingTimeShiftId = data.workingtime_shift_id;
                            $('#addFeedbackDialog').modal('show');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to fetch data:', error);
                    }
                });

            } else {
                console.log("Saving");
                setTimeout(function() {
                    localStorage.setItem('checkinurl', currentUrl);
                    console.log("Saved Current URL:", localStorage.getItem('checkinurl'));
                    window.location.href = "{{ route('google-auth') }}";
                }, 100);
            }
        });

        $('#confirmLoginBtn').on('click', function(event) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: `/workingtime/saveCheckIn`,
                method: 'POST',
                data: {
                    // user_name: username,
                    user_id: userId,
                    counter_id: counterId,
                    workingtime_shift_id: workingTimeShiftId,
                    is_late: isLate
                },
                success: function(data) {
                    $('#addFeedbackDialog').modal('hide');
                    $('#successDialog').modal('show');

                    console.log("Success");
                },
                error: function(xhr, status, error) {
                    console.error('Failed to :', error);
                    $('#addFeedbackDialog').modal('hide');
                    $('#errorDialog').modal('show');

                }
            });

        });


        $('#cancelBtn').on('click', function() {
            $('#addFeedbackDialog').modal('hide');
        });

        $('#CloseDialogNotinTimeBtn').on('click', function() {
            $('#notInTimeDialog').modal('hide');
        });


        $('#closeSuccessDialog').on('click', function() {
            $('#successDialog').modal('hide');
        });

        $('#closeErrorDialog').on('click', function() {
            $('#errorDialog').modal('hide');
        });

        $('#checkHistoryBtn').on('click', function() {
            window.location.href = '/workingtime/TodayCheckinList/';
            console.log(userId);
        });

    });
</script>

<div class="text-center">
    <h2 class="headerText">BUU LIBRARY</h2>
    <h2 class="headerText">Check In</h2>
</div>
<div class="text-center">
    <h2><span id="locationName" class="contentText">{{ $counter_name }}</span></h2>
</div>

<div class="text-center">
    @if (session('username'))
        <h2 class="contentText">{{ session('username') }}</h2>
    @else
        <h2 class="contentText">กรุณาเข้าสู่ระบบ</h2>
    @endif
</div>

<div class="text-center">
    @if (session('username'))
        <button id="loginBtn" class="btn btn-custom-purple">ลงชื่อ</button>
        <button id="checkHistoryBtn" class="btn btn-custom-purple">ประวัติการลงเวลา</button>
    @else
        <button id="loginBtn" class="btn btn-custom-purple">เข้าสู่ระบบ</button>
    @endif
</div>

<div class="modal" id="addFeedbackDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <h2 class="dialogText">ข้อมูลถูกต้องหรือไม่ ?</h2>
                <h4 class="dialogText">ชื่อ : {{ session('username') }}</h4>
                <h4 class="dialogText">ช่วงเวลา : <span id="startTimeDisplay"></span></h4>
                <h4 class="dialogText">สถานที่ : {{ $counter_name }}</h4>
                <div class="row mt-20 justify-content-center">
                    <div class="col-6 d-flex justify-content-center">
                        <button type="button" id="cancelBtn" class="btn btn-dialog-purple">ยกเลิก</button>
                    </div>
                    <div class="col-6 d-flex justify-content-center">
                        <button type="button" id="confirmLoginBtn" class="btn btn-dialog-purple">ตกลง</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal" id="notInTimeDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <h2 class="dialogText">ไม่อยู่ในช่วงการลงเวลา</h2>
                <div class="row mt-20 justify-content-center">
                    <div class="col-12 d-flex justify-content-center">
                        <button type="button" id="CloseDialogNotinTimeBtn" class="btn btn-dialog-purple">ปิด</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="modal" id="successDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <h2 class="dialogText">ลงเวลาสำเร็จ</h2>

                <div class="row mt-20 justify-content-center">
                    <div class="col-6 d-flex justify-content-center">
                        <button type="button" id="closeSuccessDialog" class="btn btn-dialog-purple">ปิด</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="errorDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <h2 class="dialogText">ลงเวลาไม่สำเร็จ </h2>
                <h3 class="dialogText">เนื่องจาก มีคนลงเวลาในช่วงเวลานี้แล้ว</h3>
                <div class="row mt-20 justify-content-center">
                    <div class="col-6 d-flex justify-content-center">
                        <button type="button" id="closeErrorDialog" class="btn btn-dialog-purple">ปิด</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
