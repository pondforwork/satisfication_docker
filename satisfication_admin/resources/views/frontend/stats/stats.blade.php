<x-app-layout>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1em auto;
        }

        th,
        td {
            padding: 0.5em 1em;
            text-align: left;
        }

        thead {
            background-color: #145F9A;
            color: white;
            font-weight: bold;
            border-top-left-radius: 10px;
        }

        .tr-custom {
            border-top-left-radius: 10px;
        }

        tbody {
            border: 1px solid #ddd;
        }

        #locationTable td {
            text-align: center;
        }

        #locationTable th {
            text-align: center;
        }

        #topfeedbackTable th {
            text-align: center;
        }

        #topfeedbackTable th {
            text-align: center;
        }

        .hidden {
            display: none;
        }
    </style>
    <div class="position-fixed top-50 start-50 translate-middle d-flex justify-content-center align-items-center">
        <div class="spinner-grow text-dark" role="status" id="loading">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="row mx-5 mt-3">
        <div class="col-md-6">
            <h3>สถิติ</h3>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-custom-purple" data-bs-toggle="modal" id="printBtn">พิมพ์สถิติ</button>
        </div>
    </div>

    <div class="row mx-3 ">
        <div class="col-6">
            <canvas id="overallStat"></canvas>
        </div>
        <div class="col-6">
            <canvas id="todayChart"></canvas>
        </div>
    </div>
    <div id='selectText' class="hidden">
        <div class="row mx-3 mt-3">

            <h2>เลือกดูตามสถานที่</h2>
        </div>
    </div>
    <div class="row mx-3 mt-3">
        <table id="locationTable" class="hidden">
            <thead>
                <tr class="tr-custom">
                    <th style="border-top-left-radius: 10px">ลำดับ</th>
                    <th>ชื่อสถานที่</th>
                    <th style="border-top-right-radius: 10px">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated here via AJAX -->
            </tbody>
        </table>
    </div>

    <div id='topFeedback' class="hidden">
        <div class="row mx-3 mt-3">
            <h2>คำติชมสูงสุด</h2>
        </div>
        <div class="row mx-3 mt-3">
            <table id="topfeedbackTable">
                <thead>
                    <tr class="tr-custom tr-custom">
                        <th style="border-top-left-radius: 10px">ลำดับ</th>
                        <th>ข้อความติชม</th>
                        <th style="border-top-right-radius: 10px">จำนวน</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated here via AJAX -->
                </tbody>
            </table>
        </div>
    </div>



    <script>
        function fetchLocations() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('/location/list') }}",
                method: 'GET',
                success: function(data) {
                    let tableBody = $('#locationTable tbody');
                    tableBody.empty(); // Clear existing data
                    data.forEach(function(location, index) {
                        tableBody.append(`
                                    <tr>
                                        <td class="text-center">${index + 1}</td>
                                        <td class="text-center">${location.name}</td>
                                        <td class="text-center">
                                                <button type="button" class="btn btn-outline-primary edit-btn mx-2" id="showStatsByIdBtn" data-id="${location.location_id}">
                                                    <i class="bi bi-eye"></i> แสดง
                                                </button>
                                        </td>
                                    </tr>
                                `);
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        async function getData() {
            const url = "/stats/overall";
            const todayUrl = "/stats/today";
            document.getElementById('loading').style.display = 'flex';
            try {
                const [response, responseToday] = await Promise.all([
                    fetch(url),
                    fetch(todayUrl)
                ]);
                if (!response.ok) {
                    throw new Error(`Overall stats response status: ${response.status}`);
                }
                if (!responseToday.ok) {
                    throw new Error(`Today stats response status: ${responseToday.status}`);
                }

                const resultJson = await response.json();
                const resultToday = await responseToday.json();

                const score = resultJson.map(item => item.count);
                const scoreToday = resultToday.map(item => item.count);


                updateChart(score, scoreToday);

            } catch (error) {
                console.error("Error fetching data:", error.message);
            } finally {
                document.getElementById('loading').style.display = 'none';
                $('#locationTable').removeClass('hidden');
                $('#selectText').removeClass('hidden');
                $('#pickDate').removeClass('hidden');
                $('#topFeedback').removeClass('hidden');



            }

            $(document).on('click', '#showStatsByIdBtn', function() {
                var locationId = $(this).data('id');
                window.location.href = '/statsbylocation/' + locationId;
            });
        }


        // New
        function fetchTopFeedback() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ url('/stats/topFeedback') }}",
                method: 'GET',
                success: function(data) {
                    let tableBody = $('#topfeedbackTable tbody');
                    tableBody.empty(); // Clear existing data
                    data.forEach(function(feedback, index) {
                        tableBody.append(`
                                    <tr>
                                        <td class="text-center">${index + 1}</td>
                                        <td class="text-center">${feedback.feedback_text}</td>
                                        <td class="text-center">${feedback.count}</td>
                                    </tr>
                                `);
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }


        function updateChart(score, scoreToday) {
            const overallChart = document.getElementById('overallStat').getContext('2d');
            const todayChart = document.getElementById('todayChart').getContext('2d');

            new Chart(overallChart, {
                type: 'bar',
                data: {
                    labels: ['ไม่พอใจอย่างยิ่ง', 'ไม่พอใจ', 'เฉยๆ', 'ค่อนข้างพอใจ', 'พอใจมาก'],
                    datasets: [{
                        backgroundColor: 'purple',
                        label: 'คะแนนความพึงพอใจโดยรวม',
                        data: score,
                        borderWidth: 2
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            new Chart(todayChart, {
                type: 'bar',
                data: {
                    labels: ['ไม่พอใจอย่างยิ่ง', 'ไม่พอใจ', 'เฉยๆ', 'ค่อนข้างพอใจ', 'พอใจมาก'],
                    datasets: [{
                        backgroundColor: 'purple',
                        label: 'คะแนนความพึงพอใจวันนี้',
                        data: scoreToday,
                        borderWidth: 2
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });


        }

        $(document).on('click', '#printBtn', function() {
            window.location.href = '/stats/printstats';
        });

        window.onload = function() {
            fetchLocations();
            fetchTopFeedback();
            getData();
            console.log("Page has fully loaded.");
        };
    </script>

</x-app-layout>
