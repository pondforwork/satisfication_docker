<x-app-layout>
    <style>
        .date-picker-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .date-picker-container .form-label {
            margin-bottom: 0;
        }

        .date-picker-container .form-control {
            width: 276px;
        }

        .date-picker-container .btn {
            margin-left: 1rem;
        }

        .btnShow {
            margin-left: 1rem;
            margin-top: 8px;
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

        table {
            /* General table styles */
            width: 100%;
            /* Set a width for the table */
            border-collapse: collapse;
            /* Combine table borders for cleaner appearance */
            margin: 1em auto;
            /* Add some margin for better spacing */
        }

        th,
        td {
            /* Styles for table cells (headers and data) */
            padding: 0.5em 1em;
            /* Add padding for content within cells */
            /* Add a border around cells */
            text-align: left;
            /* Align content to the left by default */
        }

        thead {
            /* Styles for table headers specifically */
            background-color: #145F9A;
            color: white;
            /* Change background color to red */
            font-weight: bold;
            border-top-left-radius: 10px;

            /* Make headers bolder */
        }

        .tr-custom {
            border-top-left-radius: 10px;
        }

        tbody {
            border: 1px solid #ddd;
        }

        /* Center text and buttons in table cells */
        #locationTable td {
            text-align: center;
        }

        /* Optional: Center text in table headers */
        #locationTable th {
            text-align: center;
        }

        #loadingSave {
            display: none;
        }

        #loadingDelete {
            display: none;
        }

        .accordion-button:not(.collapsed) {
            background-color: purple;
            /* Change to your desired color */
            color: #fff;
            /* Text color */
            border-color: #007bff;
            /* Border color */
        }

        /* Optionally, you can change the color of the accordion button when collapsed */
        .accordion-button {
            background-color: #f8f9fa;
            /* Change to your desired color */
            color: #000;
            /* Text color */
            border-color: #dee2e6;
            /* Border color */
        }

        /* Change the color of the accordion header when focused */
        .accordion-button:focus {
            box-shadow: none;
            /* Remove the default focus shadow */
            outline: none;
            /* Remove default focus outline */
        }
    </style>

    <body>
        <div class='container'>
            <div class="date-picker-container mt-3">
                <div class="form-group">
                    <label for="datePickerStart" class="form-label">ตั้งแต่</label>
                    <input id="datePickerStart" class="form-control" />
                </div>
                <div class="form-group">
                    <label for="datePickerEnd" class="form-label">ถึง</label>
                    <input id="datePickerEnd" class="form-control" />
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-outline-success btnShow" id="download">
                        <i class="bi bi-download"></i> ดาวน์โหลด
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h2>เลือกเคาน์เตอร์ที่ต้องการ</h2>
                </div>

                <div class="col-md-6 d-flex justify-content-end">
                    <button class="btn btn-sm btn-primary select-all-btn btn-custom-purple"
                        data-location-id="${location.location_id}">เลือกทั้งหมด</button>
                </div>
                
            </div>



            <div class="accordion" id="locationAccordion">
                <!-- Dynamic content will be inserted here -->
            </div>
        </div>
    </body>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let selectedIds = [];

        document.addEventListener('DOMContentLoaded', function() {
            $.ajax({
                url: "{{ url('/location/list/') }}",
                method: 'GET',
                success: function(data) {
                    let accordion = $('#locationAccordion');
                    accordion.empty();

                    data.forEach(function(location, index) {
                        accordion.append(`
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading${index}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="false" aria-controls="collapse${index}">
                                ${location.name}
                            </button>
                        </h2>
                        <div id="collapse${index}" class="accordion-collapse collapse" aria-labelledby="heading${index}">
                            <div class="accordion-body">
                                <ul id="counterList${location.location_id}" class="list-group">
                                    <!-- Counters will be added here dynamically -->
                                </ul>
                            </div>
                        </div>
                    </div>
                `);

                        // Fetch counters for each location
                        $.ajax({
                            url: `{{ url('/counter/listbylocationid') }}`,
                            method: 'GET',
                            data: {
                                location_id: location.location_id
                            },
                            success: function(response) {
                                let counters = response.counters;
                                let counterList = $(
                                    `#counterList${location.location_id}`);
                                counterList
                                    .empty(); // Clear existing counters before appending new ones

                                // Initialize selectedIds array
                                // let selectedIds = [];

                                // Check if counters is an array
                                if (Array.isArray(counters)) {
                                    counters.forEach(function(counter) {
                                        counterList.append(`
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        ${counter.name}
                                        <input type="checkbox" class="form-check-input me-2" value="${counter.counter_id}">
                                    </li>
                                `);
                                    });

                                    counterList.on('change',
                                        'input[type="checkbox"]',
                                        function() {
                                            let counterId = $(this).val();
                                            if ($(this).is(':checked')) {
                                                if (!selectedIds.includes(
                                                        counterId)) {
                                                    selectedIds.push(counterId);
                                                }
                                            } else {
                                                selectedIds = selectedIds
                                                    .filter(id => id !==
                                                        counterId);
                                            }

                                            console.log('Selected IDs:',
                                                selectedIds);
                                        });
                                } else {
                                    console.error(`Expected an array but received:`,
                                        counters);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(
                                    `Failed to fetch counters for location ${location.location_id}:`,
                                    error);
                            }
                        });
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch locations:', error);
                }
            });
        });

        $(document).on('click', '.select-all-btn', function() {

            var btn = $(this); 
            if (btn.text() === 'เลือกทั้งหมด') {
                btn.text('ไม่เลือกทั้งหมด'); 
            } else {
                btn.text('เลือกทั้งหมด'); 
            }
            let checkboxes = $('input[type="checkbox"]');
            let allChecked = checkboxes.first().prop('checked');
            checkboxes.prop('checked', !allChecked);
            checkboxes.each(function() {
                let counterId = $(this).val();
                if (!allChecked) {
                    if (!selectedIds.includes(counterId)) {
                        selectedIds.push(counterId);
                    }
                } else {
                    selectedIds = selectedIds.filter(id => id !== counterId);
                }
            });




            console.log(allChecked ? 'All deselected' : 'All selected IDs:', selectedIds);
        });


        $(document).on('click', '#download', function() {
            var dateStart = $('#datePickerStart').val();
            var dateEnd = $('#datePickerEnd').val();
            console.log(selectedIds);
            if (!selectedIds || selectedIds.length === 0) {
                alert('Please select at least one location.');
                return;
            }

            if (!dateStart || !dateEnd) {
                alert('Please select both start and end dates.');
                return;
            }

            function convertToMySQLDate(dateStr) {
                var dateParts = dateStr.split('/');
                return dateParts[2] + '-' + dateParts[0] + '-' + dateParts[1];
            }
            var dateStartMySQL = convertToMySQLDate(dateStart);
            var dateEndMySQL = convertToMySQLDate(dateEnd);

            $.ajax({
                url: `/stats/getstatbycounter/`,
                method: 'GET',
                data: {
                    counter_ids: selectedIds,
                    start_date: dateStartMySQL,
                    end_date: dateEndMySQL
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data, status, xhr) {
                    var contentDisposition = xhr.getResponseHeader('Content-Disposition');
                    var filename = 'stats_by_pick_location.xlsx';

                    if (contentDisposition) {
                        var filenameMatch = contentDisposition.match(/filename="(.+)"/);
                        if (filenameMatch) {
                            filename = filenameMatch[1];
                        }
                    } else {
                        filename = `${dateStartMySQL}-${dateEndMySQL}-StatsCounter.xlsx`;
                    }

                    var blob = new Blob([data], {
                        type: xhr.getResponseHeader('Content-Type')
                    });

                    var link = document.createElement('a');
                    var url = URL.createObjectURL(blob);
                    link.href = url;
                    link.download = filename; // Set the file name for download
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch data:', error);
                }
            });
        });




        $('#datePickerStart').datepicker({
            uiLibrary: 'bootstrap5',
            maxDate: new Date()
        });
        $('#datePickerEnd').datepicker({
            uiLibrary: 'bootstrap5',
            maxDate: new Date()
        });

        window.onload = function() {
            console.log("Page has fully loaded.");
        };
    </script>
</x-app-layout>
