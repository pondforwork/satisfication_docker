<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? '' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Import Chart.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .sidebar {
            position: sticky;
            /* Changed to sticky */
            top: 56px;
            /* Adjust based on navbar height */
            height: calc(100vh - 56px);
            /* Subtract the navbar height */
            width: 100%;
            overflow-y: auto;
            /* Enable vertical scrolling */
            background-color: #2D2222;
            color: white;
            padding: 15px;
        }

        .nav-link {
            font-size: 1rem;
            color: white;
            /* Adjust link text color */
            display: block;
            /* Ensure links behave as block elements */
            padding: 8px 15px;
            /* Add padding for spacing */
            text-decoration: none;
            /* Remove underline from links */
        }

        .nav-link:hover {
            background-color: #f8f9fa;
            /* Set background color on hover */
            color: black;
            /* Change text color on hover */
            border-radius: 5px;
            /* Optional: Add rounded corners */
        }

        .nav-item {
            margin-bottom: 10px;
            /* Adjust spacing between nav items */
        }

        .nav-link .bi {
            margin-right: 10px;
            /* Adjust margin between icon and text */
        }

        .active {
            background-color: white;
            color: black;
            border-radius: 5px;

        }
    </style>

</head>

<body>
    <div class="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ Request::is('stats') ? 'active' : '' }}" href="{{ url('/stats/') }}"><i
                        class="bi bi-bar-chart"></i>สถิติ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('client') ? 'active' : '' }}" href="{{ url('/client/') }}"><i
                        class="bi bi-phone"></i>Client</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('employees') ? 'active' : '' }}" href="{{ url('/employee/') }}"><i
                        class="bi bi-people"></i>พนักงาน</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('employees') ? 'active' : '' }}" href="{{ url('/workingtime/') }}"><i
                        class="bi bi-clock"></i>เวลาทำงาน</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('location') ? 'active' : '' }}" href="{{ url('/location/') }}"><i
                        class="bi bi-geo-alt"></i>สถานที่</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('satisfication') ? 'active' : '' }}"
                    href="{{ url('/satisfication/') }}"><i class="bi bi-emoji-smile"></i>คำติชม</a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ Request::is('role') ? 'active' : '' }}"
                    href="{{ url('/role/') }}"><i class="bi bi-key-fill"></i>จัดการสิทธิ์</a>
            </li>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
