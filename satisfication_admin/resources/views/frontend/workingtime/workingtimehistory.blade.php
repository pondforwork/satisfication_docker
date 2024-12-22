<x-app-layout>
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
    </head>

    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background-color: purple;
            color: white;
            border: 1px solid #202020;
            padding: 8px;
            text-align: center;
        }

        td {
            background-color: white;
            color: #202020;
            /* Text color for table body */
            border: 1px solid #202020;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) td {
            background-color: #f2f2f2;
        }

        .date-picker-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btnShow {
            margin-left: 1rem;
            margin-top: 14px;
        }
    </style>
    </head>


    <div class="container">
        <div class="date-picker-container mt-3">
            <div class="form-group">
                <label for="datePickerStart" class="form-label">เลือกวันที่</label>
                <input id="datePickerStart" class="form-control" />
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-outline-primary btnShow" id="show">
                    <i class="bi bi-eye"></i> แสดง
                </button>
            </div>
        </div>


        <div class="row mx-5 mt-3">
            <div class="col-md-6">
                <h3>การลงเวลางานประจำวันที่ <span id="currentDateDisplay"></span></h3>
            </div>
        </div>

        <table id="timetable">
            <thead>
                <tr id="header-row">
                    <th>เวลา/สถานที่</th>
                </tr>
            </thead>
            <tbody id="table-body">
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {

            $('#datePickerStart').datepicker({
                uiLibrary: 'bootstrap5',
                // maxDate: new Date()
            });

            const currentDate = new Date().toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            })
            $('#currentDateDisplay').text(currentDate);
            $.ajax({
                url: '/workingtime/getalltodayworkingtime/',
                type: 'GET',
                success: function(response) {
                    console.log('Success:', response);

                    // Generate the table with the fetched data
                    generateTableHeader(response.times);
                    generateTableBody(response.locations);
                },
                error: function(xhr, status, error) {
                    console.error('An error occurred:', error);
                }
            });

            $(document).on('click', '#show', function() {

                var selectedDate = $('#datePickerStart').val();
                var dateObj = new Date(selectedDate);
                // ปรับวันให้เป็นวันของประเทศไทย
                dateObj.setHours(dateObj.getHours() + 7);
                // Format the adjusted date to 'YYYY-MM-DD' for MySQL
                var formattedDateStart = dateObj.toISOString().split('T')[0];
                console.log(formattedDateStart);
                $('#header-row').empty();
                $('#table-body').empty();
                $('#header-row').append('<th>เวลา/สถานที่</th>');

                const currentDate = new Date(selectedDate).toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                })
                // แสดงวันที่่บนหน้าจอ
                $('#currentDateDisplay').text(currentDate);

                $.ajax({
                    url: '/workingtime/getalltodayworkingtime/',
                    data: {
                        'date': formattedDateStart
                    },
                    type: 'GET',
                    success: function(response) {
                        console.log('Success:', response);

                        // Generate the table with the fetched data
                        generateTableHeader(response.times);
                        generateTableBody(response.locations);
                    },
                    error: function(xhr, status, error) {
                        console.error('An error occurred:', error);
                    }
                });

            });
        });


        function generateTableHeader(times) {
            var headerRow = document.getElementById('header-row');
            times.forEach(function(time) {
                var th = document.createElement('th');
                th.innerText = time;
                headerRow.appendChild(th);
            });
        }

        function generateTableBody(locations) {
            var tableBody = document.getElementById('table-body');
            locations.forEach(function(location) {
                var tr = document.createElement('tr');

                // Location name column
                var tdLocation = document.createElement('td');
                tdLocation.innerText = location.location;
                tr.appendChild(tdLocation);

                // Activities for each time slot
                location.activities.forEach(function(activity) {
                    var tdActivity = document.createElement('td');
                    tdActivity.innerText = activity;
                    tr.appendChild(tdActivity);
                });

                // Append the row to the table body
                tableBody.appendChild(tr);
            });
        }
    </script>

    </body>



    </html>
</x-app-layout>
