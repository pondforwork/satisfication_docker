<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
</style>

<script>
    function getCheckInUrl() {
        console.log("Loaded Current URL:", localStorage.getItem('checkinurl'));

        var savedUrl = localStorage.getItem('checkinurl');
        console.log("Retrieved URL:", savedUrl);
        return savedUrl;
    }


    function goBack() {
        const lastVisitedUrl = getCheckInUrl();
        if (lastVisitedUrl) {
            // Navigate to the last visited URL
            window.location.href = lastVisitedUrl;
        } else {
            // Optionally, handle the case where no URL is stored
            console.log('No previous URL found');
        }
    }
</script>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12 text-center">
                <h1 class="headerText">BUU LIBRARY</h1>
            </div>

            <div class="col-sm-12 text-center">
                <h1 class="contentText">ลงทะเบียนสำเร็จแล้ว
                    <br>
                    โดยชื่อ : {{ session('username') }}
                </h1>
            </div>

            <div class="col-sm-12 text-center">
                <button href="{{ route('google-auth') }}" class="btn btn-custom-purple" onclick="goBack()">
                    เข้าสู่หน้าจอลงเวลางาน</button>
            </div>

            {{-- <button class="btn btn-secondary btn-lg" onclick="goBack()">Go Back</button> --}}

        </div>
    </div>
</body>
