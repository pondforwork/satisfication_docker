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
        </style>


        <script>
            $(document).ready(function() {
                $.ajax({
                    url: '/workingtime/getadvancetime/',
                    method: 'GET',
                    success: function(response) {
                        console.log('Success:', response);
                        $('#editText').val(response);
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                    }
                });

                $(document).on('click', '#saveButton', function() {

                    var textFieldData = $('#editText').val();
                    if (!/^\d+$/.test(textFieldData)) {
                        alert('Please enter a positive number.');
                    } else {
                        console.log(textFieldData); 
                    }

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: '/workingtime/upadateadvanceduration/',
                        type: 'POST',
                        data: {
                            duration: textFieldData,
                        },
                        success: function(response) {
                            console.log(response.message);
                            location.reload();
                        },
                        error: function(error) {
                            console.error('Error updating order:', error);
                            $('#errorModal').modal('show');
                        }
                    });
                });

            });
        </script>

    <body>
        <div class="container">
            <h2>สามารถเข้างานได้ก่อนและหลัง (นาที)</h2>

            <div class="row">
                <div class="col-md-3"> <input type="text" class="form-control" id="editText">
                </div>

                <div class="col-md-3">
                    <button type="button" class="btn btn-custom-purple" id="saveButton">บันทึก</button>
                </div>
            </div>

        </div>
    </body>


    <div class="modal" id="errorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered custom-modal-width">
        <div class="modal-content">
            <div class="modal-body">
                <div class="text-center">
                    <h2>Error</h2>
                </div>

                <div class="text-center">
                    <h2>แก้ไขข้อมูลไม่สำเร็จ</h2>
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

    </html>
</x-app-layout>
