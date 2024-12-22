<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use \DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StatController extends Controller
{
    public function index()
    {

        $username = session('username');
        $googleController = new GoogleAuthController();
        $isExecutive = $googleController->isExecutive();
        if (!$username) {
            return view('login.login');
        } elseif ($isExecutive) {
            return view('frontend.stats.stats');
        } else {
            return view('login.unauthorized');
        }

        // return view('frontend.stats.stats');
    }

    public function statsByLocation($id)
    {
        return view('frontend.stats.statsbylocation', compact('id'));
    }

    public function goToPrintStats()
    {
        return view('frontend.stats.printstats');
    }

    public function getOverallStat()
    {
        $score = DB::select("
            SELECT '1 score' AS score_label, COUNT(score) AS count
            FROM feedback
            WHERE score = 1
              AND MONTH(date_added) = MONTH(CURDATE())
              AND YEAR(date_added) = YEAR(CURDATE())
            UNION
            SELECT '2 score' AS score_label, COUNT(score) AS count
            FROM feedback
            WHERE score = 2
              AND MONTH(date_added) = MONTH(CURDATE())
              AND YEAR(date_added) = YEAR(CURDATE())
            UNION
            SELECT '3 score' AS score_label, COUNT(score) AS count
            FROM feedback
            WHERE score = 3
              AND MONTH(date_added) = MONTH(CURDATE())
              AND YEAR(date_added) = YEAR(CURDATE())
            UNION
            SELECT '4 score' AS score_label, COUNT(score) AS count
            FROM feedback
            WHERE score = 4
              AND MONTH(date_added) = MONTH(CURDATE())
              AND YEAR(date_added) = YEAR(CURDATE())
            UNION
            SELECT '5 score' AS score_label, COUNT(score) AS count
            FROM feedback
            WHERE score = 5
              AND MONTH(date_added) = MONTH(CURDATE())
              AND YEAR(date_added) = YEAR(CURDATE());
        ");
        return response()->json($score, 200);
    }

    public function getTodayStat()
    {
        try {
            $score = DB::select("
                SELECT '1 score' AS score_label, COUNT(score) AS count
                FROM feedback
                WHERE score = 1 AND DATE(date_added) = CURDATE()
                UNION
                SELECT '2 score' AS score_label, COUNT(score) AS count
                FROM feedback
                WHERE score = 2 AND DATE(date_added) = CURDATE()
                UNION
                SELECT '3 score' AS score_label, COUNT(score) AS count
                FROM feedback
                WHERE score = 3 AND DATE(date_added) = CURDATE()
                UNION
                SELECT '4 score' AS score_label, COUNT(score) AS count
                FROM feedback
                WHERE score = 4 AND DATE(date_added) = CURDATE()
                UNION
                SELECT '5 score' AS score_label, COUNT(score) AS count
                FROM feedback
                WHERE score = 5 AND DATE(date_added) = CURDATE();
            ");
            return response()->json($score, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch score data'], 500);
        }
    }

    public function getTopFeedback()
    {
        try {
            $score = DB::select("
            SELECT feedback_text, COUNT(*) AS count
            FROM feedback
            WHERE feedback_text IS NOT NULL 
            AND MONTH(date_added) = MONTH(CURDATE())
			AND YEAR(date_added) = YEAR(CURDATE())
            GROUP BY feedback_text
            ORDER BY count DESC;
            ");
            return response()->json($score, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch score data'], 500);
        }

    }

    public function getStatsByLocation(request $Request)
    {
        try {
            $locationId = $Request->location_id;
            $dateStart = $Request->date_start;
            $dateEnd = $Request->date_end;

            $counter_ids = DB::select('
                SELECT DISTINCT c.counter_id 
                FROM counter c
                WHERE c.location_id = ?
            ', [$locationId]);
            $results = [];

            foreach ($counter_ids as $counter) {
                $counter_id = $counter->counter_id;

                if ($dateStart && $dateEnd) {
                    $query = "
                        SELECT '1 score' AS score_label, COUNT(f.score) AS count
                        FROM feedback f
                        LEFT JOIN counter c ON c.counter_id = ?
                        WHERE score = 1 AND f.counter_id = ? AND f.date_added BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
                        UNION
                        SELECT '2 score' AS score_label, COUNT(f.score) AS count
                        FROM feedback f
                        LEFT JOIN counter c ON c.counter_id = ?
                        WHERE score = 2 AND f.counter_id = ? AND f.date_added BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
                        UNION
                        SELECT '3 score' AS score_label, COUNT(f.score) AS count
                        FROM feedback f
                        LEFT JOIN counter c ON c.counter_id = ?
                        WHERE score = 3 AND f.counter_id = ? AND f.date_added BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY) 
                        UNION
                        SELECT '4 score' AS score_label, COUNT(f.score) AS count
                        FROM feedback f
                        LEFT JOIN counter c ON c.counter_id = ?
                        WHERE score = 4 AND f.counter_id = ? AND f.date_added BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
                        UNION
                        SELECT '5 score' AS score_label, COUNT(f.score) AS count
                        FROM feedback f
                        LEFT JOIN counter c ON c.counter_id = ?
                        WHERE score = 5 AND f.counter_id = ? AND f.date_added BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)          
                     ";

                    $data = DB::select($query, [
                        $counter_id,
                        $counter_id,
                        $dateStart,
                        $dateEnd,
                        $counter_id,
                        $counter_id,
                        $dateStart,
                        $dateEnd,
                        $counter_id,
                        $counter_id,
                        $dateStart,
                        $dateEnd,
                        $counter_id,
                        $counter_id,
                        $dateStart,
                        $dateEnd,
                        $counter_id,
                        $counter_id,
                        $dateStart,
                        $dateEnd,
                    ]);
                } else {
                    $params = array_fill(0, 10, $counter_id);
                    $data = DB::select("
                    SELECT '1 score' AS score_label, COUNT(f.score) AS count
                    FROM feedback f
                    LEFT JOIN counter c on c.counter_id = ?
                    WHERE score = 1 AND f.counter_id = ?
                    AND MONTH(f.date_added) = MONTH(CURRENT_DATE()) 
                    AND YEAR(f.date_added) = YEAR(CURRENT_DATE())
                    UNION
                    SELECT '2 score' AS score_label, COUNT(f.score) AS count
                    FROM feedback f
                    LEFT JOIN counter c on c.counter_id = ?
                    WHERE score = 2 AND f.counter_id = ?
                    AND MONTH(f.date_added) = MONTH(CURRENT_DATE()) 
                    AND YEAR(f.date_added) = YEAR(CURRENT_DATE())
                    UNION
                    SELECT '3 score' AS score_label, COUNT(f.score) AS count
                    FROM feedback f
                    LEFT JOIN counter c on c.counter_id = ?
                    WHERE score = 3 AND f.counter_id = ?
                    AND MONTH(f.date_added) = MONTH(CURRENT_DATE()) 
                    AND YEAR(f.date_added) = YEAR(CURRENT_DATE())
                    UNION
                    SELECT '4 score' AS score_label, COUNT(f.score) AS count
                    FROM feedback f
                    LEFT JOIN counter c on c.counter_id = ?
                    WHERE score = 4 AND f.counter_id = ?
                    AND MONTH(f.date_added) = MONTH(CURRENT_DATE()) 
                    AND YEAR(f.date_added) = YEAR(CURRENT_DATE())
                    UNION
                    SELECT '5 score' AS score_label, COUNT(f.score) AS count
                    FROM feedback f
                    LEFT JOIN counter c on c.counter_id = ?
                    WHERE score = 5 AND f.counter_id = ?
                    AND MONTH(f.date_added) = MONTH(CURRENT_DATE()) 
                    AND YEAR(f.date_added) = YEAR(CURRENT_DATE())
                ", $params);
                }



                $counter = DB::selectOne("
                  SELECT name from counter counter_name
                    WHERE counter_id = ?
                ",
                    [
                        $counter_id,
                    ]
                );


                $scoreCount = array_map(function ($score) {
                    return $score->count;
                }, $data);
                // Store the results for each counter_id
                $results[] = [
                    'counter_name' => $counter->name,
                    'counter_id' => $counter_id,
                    'scores' => $scoreCount
                ];
            }

            return response()->json($results, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch counters', 'details' => $e->getMessage()], 500);
        }
    }

    public function printStatsByPickLocation(Request $request)
    {
        try {
            // Fetch an array of location_ids from the request
            $locationIds = $request->input('location_ids'); // Expecting an array of location IDs
            // Ensure $locationIds is an array
            if (!is_array($locationIds)) {
                return response()->json(['error' => 'Invalid location_ids format'], 400);
            }

            // Fetch start_date and end_date from the request
            $startDate = new DateTime($request->input('start_date', '2024-09-12'));
            $endDate = new DateTime($request->input('end_date', '2024-09-13'));

            // Initialize an array to store query parts
            $queryParts = [];
            $params = [];

            // Loop through each location_id and build the query
            foreach ($locationIds as $locationId) {
                $queryParts[] = "
                SELECT l.location_id, f.score AS score_label, COUNT(f.score) AS count
                FROM feedback f
                LEFT JOIN counter c ON f.counter_id = c.counter_id
                LEFT JOIN location l ON c.location_id = l.location_id
                WHERE l.location_id = ?
                AND f.date_added BETWEEN ? AND ?
                GROUP BY l.location_id, f.score
            ";
                // Add parameters for location_id, start_date, and end_date
                $params[] = $locationId;
                $params[] = $startDate->format('Y-m-d');
                $params[] = $endDate->format('Y-m-d');
            }

            // Combine all query parts with UNION ALL
            $finalQuery = implode(" UNION ALL ", $queryParts);

            // Execute the combined query with bound parameters
            $results = DB::select($finalQuery, $params);

            // Initialize the formatted result array
            $formattedResults = [];

            // Loop through results and format them
            foreach ($results as $row) {
                $locationId = $row->location_id;
                $scoreLabel = "{$row->score_label} score";
                $count = $row->count;

                // Initialize location in the results array if not already
                if (!isset($formattedResults[$locationId])) {
                    $formattedResults[$locationId] = [];
                }
                $formattedResults[$locationId][$scoreLabel] = $count;
            }

            // Ensure all score labels from 1 to 5 are present for each location
            $finalResult = [];
            foreach ($formattedResults as $locationId => $scores) {
                $formattedScores = [];
                for ($i = 1; $i <= 5; $i++) {
                    $label = "{$i} score";
                    $formattedScores[$label] = isset($scores[$label]) ? $scores[$label] : 0;
                }
                $finalResult["location_$locationId"] = $formattedScores;
            }

            return response()->json($finalResult, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch score data'], 500);
        }
    }

    public function printSelected(Request $request)
    {
        try {
            // Fetch location IDs from the request
            $locationIds = $request->input('location_ids');
            $startDate = new DateTime($request->input('start_date'));
            $endDate = new DateTime($request->input('end_date'));

            // if (!is_array($locationIds)) {
            //     return response()->json(['error' => 'Invalid location_ids format'], 400);
            // }
            // $locationIds = [1, 2, 16];

            // $startDate = new DateTime($request->input('start_date', '2024-09-12'));
            // $endDate = new DateTime($request->input('end_date', '2024-09-13'));

            $queryParts = [];
            $params = [];

            // Loop through each location_id and build the query
            foreach ($locationIds as $locationId) {
                $queryParts[] = "
                    SELECT l.location_id,
                           COALESCE(SUM(CASE WHEN f.score = 1 THEN 1 ELSE 0 END), 0) AS `1 score`,
                           COALESCE(SUM(CASE WHEN f.score = 2 THEN 1 ELSE 0 END), 0) AS `2 score`,
                           COALESCE(SUM(CASE WHEN f.score = 3 THEN 1 ELSE 0 END), 0) AS `3 score`,
                           COALESCE(SUM(CASE WHEN f.score = 4 THEN 1 ELSE 0 END), 0) AS `4 score`,
                           COALESCE(SUM(CASE WHEN f.score = 5 THEN 1 ELSE 0 END), 0) AS `5 score`
                    FROM feedback f
                    LEFT JOIN counter c ON f.counter_id = c.counter_id
                    LEFT JOIN location l ON c.location_id = l.location_id
                    WHERE l.location_id = ?
                    AND f.date_added BETWEEN ? AND ?
                    GROUP BY l.location_id
                ";
                // Add parameters for location_id, start_date, and end_date
                $params[] = $locationId;
                $params[] = $startDate->format('Y-m-d');
                $params[] = $endDate->format('Y-m-d');
            }

            // Combine all query parts with UNION ALL
            $finalQuery = implode(" UNION ALL ", $queryParts);

            // Execute the combined query with bound parameters
            $results = DB::select($finalQuery, $params);

            // Initialize the formatted result array
            $formattedResults = [];

            foreach ($results as $row) {
                $locationId = $row->location_id;
                $formattedResults["ชั้น $locationId"] = [
                    '1 score' => $row->{'1 score'},
                    '2 score' => $row->{'2 score'},
                    '3 score' => $row->{'3 score'},
                    '4 score' => $row->{'4 score'},
                    '5 score' => $row->{'5 score'},
                ];
            }

            // Create a new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Add start date and end date at the top of the document
            $sheet->setCellValue('A1', 'ข้อมูลตั้งแต่: ' . $startDate->format('Y-m-d'));
            $sheet->setCellValue('A2', 'ถึง: ' . $endDate->format('Y-m-d'));

            // Set column headers starting from row 3
            $headers = ['ชื่อสถานที่/คะแนน', 'ไม่พอใจอย่างยิ่ง', 'ไม่พอใจ', 'เฉยๆ', 'พอใจ', 'พอใจมาก', 'รวมทั้งสิ้น'];
            $sheet->fromArray($headers, NULL, 'A3');

            $rowIndex = 4;
            foreach ($formattedResults as $location => $scores) {
                $row = [$location];
                $total = 0; // Initialize total sum for the row
                foreach (['1 score', '2 score', '3 score', '4 score', '5 score'] as $label) {
                    $value = $scores[$label];
                    $row[] = $value;
                    $total += $value; // Accumulate total
                }
                $row[] = $total; // Add total sum to the end of the row
                $sheet->fromArray($row, NULL, "A$rowIndex");
                $rowIndex++;
            }

            // Create a Writer instance
            $writer = new Xlsx($spreadsheet);

            // Save to PHP output
            // $filename = 'stats_by_pick_location.xlsx';

            $filename = $startDate->format('Y-m-d') . '-' . $endDate->format('Y-m-d') . '-Feedback.xlsx';

            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment;filename=\"$filename\"",
                'Cache-Control' => 'max-age=0',
            ];

            return response()->stream(
                function () use ($writer) {
                    $writer->save('php://output');
                },
                200,
                $headers
            );

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch score data'], 500);
        }
    }

    public function getAllLocationAndCounter(Request $request)
    {
        try {
            // Fetch all locations
            $locations = DB::select("SELECT location_id, name AS location_name FROM location");

            // Prepare the final result array
            $result = [];

            // Loop through each location
            foreach ($locations as $location) {
                // Fetch counters associated with this location
                $counters = DB::select("SELECT counter_id, name FROM counter WHERE location_id = ?", [$location->location_id]);

                // Format each location with its counters
                $result[] = [
                    'locationName' => $location->location_name,
                    'counters' => $counters
                ];
            }

            // Return the result as JSON
            return response()->json($result, 200);

        } catch (\Exception $e) {
            // Log the error
            Log::error("Failed to fetch locations and counters: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }
    }

    public function getFeedbackByCounterId(Request $request)
    {
        $counterIds = $request->input('counter_ids');
        $startDate = new DateTime($request->input('start_date'));
        $endDate = new DateTime($request->input('end_date'));

        $allResults = [];

        foreach ($counterIds as $counterId) {
            // Query feedback for the current counter ID
            $scores = DB::select("
                SELECT c.name AS counter_name,        
                COALESCE(u.name, 'Guest') AS user_name
                , f.date_added, f.time_added, f.score 
                FROM feedback f
                LEFT JOIN counter c ON f.counter_id = c.counter_id
                LEFT JOIN users u ON f.user_id = u.id
                WHERE f.counter_id = ? 
                AND f.date_added BETWEEN ? AND ?
                ORDER BY f.date_added
            ", [$counterId, $startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            // Append results for the current counter
            foreach ($scores as $score) {
                $allResults[] = [
                    'counter_name' => $score->counter_name,
                    'user_name' => $score->user_name,
                    'date_added' => $score->date_added,
                    'time_added' => $score->time_added,
                    'score' => $score->score
                ];
            }
        }

        // Generate Excel file using PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add start date and end date at the top of the document
        $sheet->setCellValue('A1', 'ข้อมูลตั้งแต่: ' . $startDate->format('Y-m-d'));
        $sheet->setCellValue('A2', 'ถึง: ' . $endDate->format('Y-m-d'));

        // Set column headers starting from row 3
        $headers = ['ชื่อเคาน์เตอร์', 'ชื่อเจ้าหน้าที่', 'วันที่', 'เวลา', 'คะแนน'];
        $sheet->fromArray($headers, NULL, 'A3');

        // Add the feedback data starting from row 4
        $sheet->fromArray($allResults, NULL, 'A4');

        // Set the filename for the download
        $filename = $startDate->format('Y-m-d') . '-' . $endDate->format('Y-m-d') . '-Feedback.xlsx';

        // Prepare the response headers for Excel download
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=\"$filename\"",
            'Cache-Control' => 'max-age=0',
        ];

        // Create a writer and stream the file as a download
        try {
            $writer = new Xlsx($spreadsheet);

            return response()->stream(function () use ($writer) {
                $writer->save('php://output');
            }, 200, $headers);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate the Excel file: ' . $e->getMessage()], 500);
        }
    }

}
