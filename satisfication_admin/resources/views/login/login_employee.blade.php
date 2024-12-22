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

    .btn-custom-purple {
        background-color: #145F9A;
        border-color: #145F9A;
        margin-top: 250;
        padding: 30px 70px;
        font-size: 24px;
        color: white;
        font-size: 50;
    }

    .btn-custom-purple:hover {
        background-color: #145F9A;
        border-color: #145F9A;
        color: white;
    }
</style>

<body>
    <div class="col-sm text-center">
        <h1 class="headerText">BUU LIBRARY</h1>
    </div>

    <div class="col-sm text-center">
        <h1 class="contentText">ลงทะเบียน</h1>
    </div>

    <div class="col-sm text-center">
        <a href="{{ route('google-auth') }}" class="btn btn-custom-purple"><i class="bi bi-google"></i> เข้าสู่ระบบ</a>
    </div>
</body>
