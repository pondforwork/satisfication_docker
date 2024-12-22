<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" >
    <title>
        {{$title ?? ''}}
    </title>
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

       

    </style>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
        <x-partials.navbar /> 


            <div class="row">
                <div class="col-md-2">
                    <x-partials.sidebar />
                </div>
    
                <div class="col-md-10 content">
                    {{ $slot }}
                </div>
            </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" ></script>

    {{$script ?? ''}}
</body>
</html>