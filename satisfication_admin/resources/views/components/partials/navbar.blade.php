<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid mx-5">
            <a class="navbar-brand custom-navbar-brand" href="#" style="font-size: 25px ; color:#145F9A">BUU
                LIBRARY</a>
            {{-- <a class="navbar-brand" href="#">ภราดร ศิริจันทร์</a> --}}
            @if (session('username'))
                <div class="dropdown">
                    <button class="btn dropdown-toggle btn-custom-purple" type="button" id="dropdownMenuButton1"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        {{ session('username') }} </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">ออกจากระบบ</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <div class="text-center">
                    <a href="{{ route('google-auth') }}" class="btn btn-custom-purple">เข้าสู่ระบบ</a>
                </div>
            @endif
        </div>

    </nav>
</body>

<style>
    .custom-navbar-brand {
        color: purple;
        font-weight: bold;
    }

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
</style>
