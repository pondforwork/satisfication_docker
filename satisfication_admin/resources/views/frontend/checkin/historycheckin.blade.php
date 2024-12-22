<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js">
        //     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        // <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        // <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>
    //
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    //
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />



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
        margin-top: 100;
        font-size: 70;
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
        margin-top: 250;
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

    .card-custom {
        margin: 50px auto;
        max-width: 700px;
        background-color: purple;
        border-color: #145F9A;
        border-radius: 20px;
    }

    .card-title-custom {
        font-size: 60px;
        color: white;
    }

    .card-text-custom {
        font-size: 50px;
        color: white;
    }

    /* Centering the content */
    .card-body-custom {
        padding: 30px;
        text-align: start;
    }

    #datePicker {
        font-size: 50px;
        height: 80px;
        width: 100%;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
</style>

<script>
    $(document).ready(function() {

        var userId = {{ json_encode(session('user_id')) }} || -1;

        $(document).ajaxStart(function() {
            $.LoadingOverlay("show");
        });

        $(document).ajaxStop(function() {
            $.LoadingOverlay("hide");
        });

        $.ajax({
            url: '/workingtime/getTodayCheckin/',
            type: 'GET',
            data: {
                user_id: userId
            },
            success: function(response) {
                console.log('Success:', response);
                $.each(response, function(index, item) {
                    var cardHtml =
                        '<div class="card card-custom">' +
                        '<div class="card-body card-body-custom">' +
                        '<h5 class="card-title card-title-custom">' + item.name + '</h5>' +
                        '<p class="card-text card-text-custom">' +
                        item.start_time + ' - ' + item.end_time + '<br>' +
                        '</p>' +
                        '</div>' +
                        '</div>';

                    // Append the card to the container
                    $('#cards-container').append(cardHtml);
                });
            },
            error: function(xhr, status, error) {
                var cardHtml =
                    '<div class="text-center">' +
                    '<h3 class="contentText">ไม่พบข้อมูลการลงเวลาในวันนี้</h3>' +
                    '</div>';
                $('#cards-container').append(cardHtml);
                console.log('Error:', error);
            }
        });

        document.getElementById('datePicker').addEventListener('change', function() {
            var selectedDate = this.value;
            console.log('Selected date:', selectedDate);
            $('#cards-container').empty();
            $.ajax({
                url: '/workingtime/getTodayCheckin/',
                type: 'GET',
                data: {
                    user_id: userId,
                    date :  selectedDate
                },
                success: function(response) {
                    console.log('Success:', response);
                    $.each(response, function(index, item) {
                        var cardHtml =
                            '<div class="card card-custom">' +
                            '<div class="card-body card-body-custom">' +
                            '<h5 class="card-title card-title-custom">' + item
                            .name + '</h5>' +
                            '<p class="card-text card-text-custom">' +
                            item.start_time + ' - ' + item.end_time + '<br>' +
                            '</p>' +
                            '</div>' +
                            '</div>';
                        $('#cards-container').append(cardHtml);
                    });
                },
                error: function(xhr, status, error) {
                    var cardHtml =
                        '<div class="text-center">' +
                        '<h3 class="contentText">ไม่พบข้อมูลการลงเวลาในวันนี้</h3>' +
                        '</div>';
                    $('#cards-container').append(cardHtml);
                    console.log('Error:', error);
                }
            });
        });

        $('#datePickerStart').datepicker({
            uiLibrary: 'bootstrap5',
            maxDate: new Date(),
            size: 'large', // Optional: 'large', 'default', 'small'
            modal: true, // Optional: For better UX on mobile
            header: true // Optional: To show a header in the popup
        });
    });
</script>



<div class="text-center">
    <h2 class="headerText">ประวัติการเข้างาน</h2>
</div>

<div class="text-center">
    @if (!session('username'))
        <h2 class="contentText">กรุณาเข้าสู่ระบบ</h2>
        <button id="loginBtn" class="btn btn-custom-purple">เข้าสู่ระบบ</button>
    @else
        <div class="container">
            <div class="form-group">
                <label for="datePicker" class="form-label contentText">เลือกวันที่</label>
                <input id="datePicker" class="form-control" type="date" />
            </div>
        </div>

        <h2 class="contentText">{{ session('username') }}</h2>
    @endif
</div>


<div id="cards-container" class="container mt-4">
</div>
