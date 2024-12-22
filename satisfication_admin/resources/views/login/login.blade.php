<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<style>
    .headerText {
        font-size: 40px;
        color: purple;
    }

    .contentText {
        font-size: 30px;
        color: purple;
    }

    .btn-custom-purple {
        background-color: #145F9A;
        border-color: #145F9A;
        padding: 10px 40px;
        font-size: 15px;
        color: white;
    }

    .btn-custom-purple:hover {
        background-color: #145F9A;
        border-color: #145F9A;
        color: white;
    }

    .custom-col {
        padding: 50px;
    }
</style>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center custom-col">
                <h1 class="headerText">BUU LIBRARY</h1>
            </div>

            <div class="col-md-8 text-center custom-col">
                <h1 class="contentText">เข้าสู่ระบบ</h1>
            </div>

            <div class="col-md-8 text-center custom-col">
                <a href="{{ route('google-auth') }}" class="btn btn-custom-purple">
                    <i class="bi bi-google"></i> เข้าสู่ระบบ
                </a>
            </div>
        </div>
    </div>
</body>
