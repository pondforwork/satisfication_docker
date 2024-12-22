<x-app-layout>
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">


        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <style>
            .aspect-ratio-18-9 {
                position: relative;
                width: 100%;
                padding-bottom: 50%;
                /* 9 / 18 * 100 = 50% */
                background-color: #f8f9fa;
            }

            .aspect-ratio-18-9>.content {
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 1px solid black;
                border-radius: 5px;
                overflow: hidden;
                /* Allow content to overflow with scrollbars */

            }

            .container-menu {
                height: 100%;
                /* Full height of the viewport for demonstration */
                border: 1px solid black;
                /* Specifying border width, style, and color */
                background-color: white;
                text-align: center;
                /* Center align text (and inline elements) */
                padding: 20px;
                /* Optional padding for better spacing */
                border-radius: 5px;
            }

            .btn-custom {
                background-color: #145F9A;
                width: 90%;
                /* Button takes full width of its container */
                margin-bottom: 10px;
                /* Space between buttons */
            }

            .row-custom {
                align-items: center;
            }

            .content img {
                width: 100%;
                /* overflow: hidden; */
                display: block;
            }

            .centered-top {
                position: absolute;
                top: 15%;
                left: 50%;
                transform: translate(-50%, -50%);
                color: white;
                font-size: 30px;
            }

            .centered-footer {
                position: absolute;
                top: 75%;
                left: 50%;
                transform: translate(-50%, -50%);
                color: white;
                font-size: 30px;
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

            .custom-modal-width {
                max-width: 600px;
                /* Set your desired width */
                width: 100%;
            }


            .content-custom-height {
                height: 15vh;
            }

            #loadingSync {
                display: none;
            }

            #loadingSave {
                display: none;
            }

            
        </style>

        <script>
            const APP_URL = "{{ config('app.url') }}";

            function updateText(headerText, footerText, wallpaperUrl) {
                // Header
                const headerDisplay = document.getElementById('headerDisplay');
                const header_input = document.getElementById('header_input');

                headerDisplay.innerText = headerText;

                header_input.addEventListener('input', function() {
                    headerDisplay.innerText = header_input.value;
                });
                header_input.value = headerText; // Set initial value

                // Footer
                const footer_input = document.getElementById('footer_input');
                const footerDisplay = document.getElementById('footerDisplay');
                // Set ข้อความ Initial
                footerDisplay.innerText = footerText;
                footer_input.addEventListener('input', function() {
                    footerDisplay.innerText = footer_input.value;
                });
                footer_input.value = footerText; // Set initial value


                const wallpaper = document.getElementById('wallpaper');
                wallpaper.src = `${APP_URL}image/${wallpaperUrl}`;
            }

            function handleFileSelect(event) {
                const file = event.target.files[0];
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file.');
                    return;
                }
                // Example: POST request using fetch
                const formData = new FormData();
                formData.append('image', file);
                fetch('image/upload/', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            console.error('Status Code:', response.status);
                            console.error('Status Text:', response.statusText);
                            throw new Error('Network response was not ok');
                        }
                        location.reload();
                        // return response.json(); 
                    })
                    .then(data => {
                        console.log('Upload successful:', data); // Log the response data
                    })
                    .catch(error => {
                        console.error('Error uploading file:', error);
                    });


            }

            $(document).on('click', '#saveButton', function() {
                $('#loadingSave').show();
                $('#button-group-save').hide();

                console.log('Header Value = ', header_input.value);
                console.log('Footer Value = ', footer_input.value);
                let formData = {
                    header: header_input.value,
                    footer: footer_input.value
                };
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: 'client/saveSettings',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log('Success:', response);
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                });
            });

            $(document).on('click', '#syncChangeButton', function() {
                document.getElementById('loadingContainer').style.display = 'flex';
                var locaingContainer = document.getElementById('loadingContainer');

                $('#loadingSync').show();
                $('#button-group-sync').hide();
                console.log("Show");

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: 'client/sendupdate',
                    method: 'GET',
                    success: function(response) {
                        console.log('Send Update Success:');
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                });
            });


            window.onload = function() {
                const setting = @json($setting);
                updateText(setting.header_text, setting.footer_text, setting.wallpaper_url);
            };
        </script>



    </head>

    <div class="container">
        <div class="row mx-5 mt-3">
            <div class="col-md-6">
                <h3>ตั้งค่า Client</h3>
            </div>

        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="aspect-ratio-18-9">
                    <div class="content">
                        <img id="wallpaper" src="https://cdn.pixabay.com/photo/2020/05/29/22/00/field-5236879_640.jpg">

                        <div class="centered-top">
                            <p id="headerDisplay"></p>
                        </div>

                        <div class="centered-footer">
                            <p id="footerDisplay"></p>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-4">

                <div class="container-menu">
                    <div class="row-custom">
                        <div class="form-floating textbox-custom mb-3">
                            <input type="email" class="form-control" id="header_input" placeholder="name@example.com">
                            <label for="header_input">ข้อความ</label>
                        </div>

                    </div>
                    <div class="row-custom">
                        <div class="form-floating textbox-custom mb-3">
                            <input type="email" class="form-control" id="footer_input" placeholder="name@example.com">
                            <label for="footer_input">ข้อความ Footer</label>
                        </div>

                    </div>
                    <div class="row-custom">

                        <label for="fileInput" class="btn btn-primary btn-custom">
                            <i class="bi bi-card-image"></i> เลือกพื้นหลัง
                        </label>
                        <input type="file" id="fileInput" class="file-input" accept="image/*"
                            onchange="handleFileSelect(event)" style="display: none;">
                    </div>
                
                    <div class="row-custom">
                        <button type="button" class="btn btn-primary btn-custom" data-bs-toggle="modal"
                            data-bs-target="#staticBackdrop">
                            <i class="bi bi-floppy"></i> บันทึก</button>
                    </div>

                    <div class="row-custom">
                        <button type="button" class="btn btn-primary btn-custom" data-bs-toggle="modal"
                            data-bs-target="#syncChangeModal">
                            <i class="bi bi-arrow-repeat"></i> Sync to Client</button>
                    </div>





                </div>

            </div>
        </div>
    </div>

    <div class="modal" id="syncChangeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal-width">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <h2>บันทึกการเปลี่ยนแปลงไปที่ Client ?</h2>
                    </div>

                    <div class="content-custom-height">
                        <div class="d-flex justify-content-center align-items-center" id="loadingContainer"
                            style="display: none; height: 100%;">
                            <div class="spinner-grow text-dark" role="status" id="loadingSync">
                                <span class="visually-hidden">Saving</span>
                            </div>
                        </div>
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
                                    id="syncChangeButton">บันทึก</button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    </div>

    <div class="modal" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <h2>บันทึกการตั้งค่าหรือไม่ ?</h2>
                    </div>

                    <div class="content-custom-height">
                        <div class="d-flex justify-content-center align-items-center" id="loadingContainer"
                            style="display: none; height: 100%;">
                            <div class="spinner-grow text-dark" role="status" id="loadingSave">
                                <span class="visually-hidden">Saving</span>
                            </div>
                        </div>
                    </div>

                    <form id="locationForm">


                    <div class="row" id='button-group-save'>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <button type="button" class="btn btn-secondary "
                                        data-bs-dismiss="modal">ยกเลิก</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <button type="button" class="btn btn btn-custom-purple"
                                        id="saveButton">บันทึก</button>
                                </div>
                            </div>



                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
