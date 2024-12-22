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

        .chart-container {
            width: 1000px;
            margin: auto;
        }

        .chart-row {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-col {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .chart-col canvas {
            width: 400px;
            height: 400px;
        }

        .text-col {
            flex: 0 0 300px;
            text-align: center;
            padding-left: 20px;
        }

        .scores {
            margin-top: 10px;
            text-align: left;
        }
    </style>

    <body>
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
                <button type="button" class="btn btn-outline-primary btnShow" id="btnShowByDate">
                    <i class="bi bi-eye"></i> แสดง
                </button>
            </div>
        </div>

        <div id="chartContainer" class="chart-container mx-3"></div>

    </body>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var locationId = -1;
        document.addEventListener('DOMContentLoaded', function() {
            locationId = @json($id);
            console.log(locationId);
            $.ajax({
                url: `/stats/statsbylocation?location_id=${locationId}`,
                method: 'GET',
                success: function(data) {
                    console.log(data);
                    const jsonData = data.map(item => ({
                        label: item.counter_name,
                        scores: item.scores
                    }));
                    createChartsFromJSON(jsonData);

                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch data:', error);
                }
            });

        });

        function createChartsFromJSON(data) {
            const chartContainer = document.getElementById('chartContainer');
            chartContainer.innerHTML = ''; // Clear any existing charts

            data.forEach((dataset, index) => {
                // Check if there is any score data
                const hasData = dataset.scores.some(score => score > 0);

                // Create a new row for each chart and its label
                const row = document.createElement('div');
                row.classList.add('chart-row');
                row.style.backgroundColor = '#f8f9fa'; // Light background color for the card
                chartContainer.appendChild(row);

                // Column for the chart or message
                const chartCol = document.createElement('div');
                chartCol.classList.add('chart-col');
                row.appendChild(chartCol);

                if (hasData) {
                    // Create the canvas for the chart
                    const canvas = document.createElement('canvas');
                    canvas.id = `chart${index}`;
                    chartCol.appendChild(canvas);

                    const ctx = canvas.getContext('2d');

                    // Create the pie chart
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['ไม่พอใจอย่างยิ่ง', 'ไม่พอใจ', 'เฉยๆ', 'ค่อนข้างพอใจ', 'พอใจมาก'],
                            datasets: [{
                                backgroundColor: ['red', 'orange', 'yellow', 'green', 'blue'],
                                data: dataset.scores
                            }]
                        },
                        options: {
                            responsive: false,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            return `${tooltipItem.label}: ${tooltipItem.raw}`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    // Show message when no score data is available
                    const noDataMessage = document.createElement('div');
                    noDataMessage.innerText = "ยังไม่มีการให้คะแนน";
                    noDataMessage.style.fontWeight = 'bold';
                    noDataMessage.style.fontSize = '18px';
                    noDataMessage.style.color = '#ff0000';
                    noDataMessage.style.height = '200px'; // Set the height here
                    noDataMessage.style.lineHeight = '200px'; // Center the text vertically (optional)
                    noDataMessage.style.textAlign = 'center'; // Center the text horizontally (optional)
                    chartCol.appendChild(noDataMessage);

                }

                // Column for the text
                const textCol = document.createElement('div');
                textCol.classList.add('text-col');

                // Add label
                const label = document.createElement('div');
                label.innerText = dataset.label;
                label.style.fontWeight = 'bold';
                label.style.fontSize = '20px';

                textCol.appendChild(label);

                if (hasData) {
                    // Add scores
                    const scores = document.createElement('div');
                    scores.classList.add('scores');

                    // Create formatted score strings
                    const labels = ['ไม่พอใจอย่างยิ่ง', 'ไม่พอใจ', 'เฉยๆ', 'ค่อนข้างพอใจ', 'พอใจมาก'];
                    labels.forEach((label, i) => {
                        const scoreLine = document.createElement('div');
                        scoreLine.innerText = `${label} : ${dataset.scores[i]}`;
                        scores.appendChild(scoreLine);
                    });

                    textCol.appendChild(scores);
                }

                row.appendChild(textCol);
            });
        }


        $(document).on('click', '#btnShowByDate', function() {
            console.log(locationId);
            var dateStart = $('#datePickerStart').val();
            var dateEnd = $('#datePickerEnd').val();

            function convertToMySQLDate(dateStr) {
                var dateParts = dateStr.split('/');
                return dateParts[2] + '-' + dateParts[0] + '-' + dateParts[1];
            }
            var dateStartMySQL = convertToMySQLDate(dateStart);
            var dateEndMySQL = convertToMySQLDate(dateEnd);
            console.log('Converted Start Date:', dateStartMySQL);
            console.log('Converted End Date:', dateEndMySQL);
            $.ajax({
                url: `/stats/statsbylocation?location_id=${locationId}&date_start=${dateStartMySQL}&date_end=${dateEndMySQL}`,
                method: 'GET',
                success: function(data) {
                    console.log(data);
                    const jsonData = data.map(item => ({
                        label: item.counter_name,
                        scores: item.scores
                    }));
                    createChartsFromJSON(jsonData);
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
