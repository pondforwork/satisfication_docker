<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Log;

class WorkingTimeController extends Controller
{

    public function index()
    {
        return view('frontend.workingtime.workingtime');
    }
    public function getList()
    {
        try {
            $workingTime = DB::select("SELECT workingtime_shift_id , start_time, end_time FROM workingtime_shift ORDER BY start_time");
            return response()->json($workingTime, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch WorkingTime', 'details' => $e->getMessage()], 500);
        }
    }

    public function deleteData(Request $request)
    {
        try {
            $id = $request->input('workingtime_id');
            $workingTime = DB::select('SELECT workingtime_shift_id FROM workingtime_shift WHERE workingtime_shift_id = ?', [$id]);
            if (empty($workingTime)) {
                return response()->json(['error' => 'WorkingTime not found'], 404);
            }
            DB::delete('DELETE FROM workingtime_shift WHERE workingtime_shift_id = ?', [$id]);
            return response()->json(['message' => 'WorkingTime deleted successfully'], 200);
        } catch (\Exception $e) {
            // Handle any errors
            return response()->json(['error' => 'Failed to delete WorkingTime', 'details' => $e->getMessage()], 500);
        }
    }

    public function saveData(Request $request)
    {
        try {
            $startTime = $request->input('start_time');
            $endTime = $request->input('end_time');
            DB::insert("INSERT INTO workingtime_shift (start_time, end_time) VALUES (?, ?)", [$startTime, $endTime]);
            return response()->json(['message' => 'Workingtime Shift Added Successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add Workingtime Shift', 'details' => $e->getMessage()], 500);
        }
    }

    public function checkIn($id)
    {
        // Get Counter Id 
        $counter = DB::select("SELECT counter_id,name FROM counter WHERE counter_id = ?;", [$id]);
        if (empty($counter)) {
            abort(404, 'Counter not found');
        }

        // Extract name from the result
        $counter_name = $counter[0]->name;
        $counter_id = $counter[0]->counter_id;

        // Pass the variable name as a string to compact
        return view('frontend.checkin.checkin', compact('counter_id', 'counter_name'));
    }

    public function isCheckInAvail()
    {
        try {
            // $currentTime = Carbon::createFromFormat('H:i:s', '08:15:00');
            $currentTime = now();

            $advanceLate = DB::selectOne("SELECT advance_late_duration FROM checkin_settings WHERE checkin_settings_id = 1;");
            $advanceLateDuration = $advanceLate->advance_late_duration;

            $checkinTime = DB::select("
               SELECT 
                    workingtime_shift_id, 
                    start_time,
                    end_time
                FROM 
                    workingtime_shift
                WHERE 
                    TIME(?) BETWEEN TIME(DATE_SUB(start_time, INTERVAL ? MINUTE)) 
                                AND TIME(DATE_SUB(end_time, INTERVAL ? MINUTE))
                ORDER BY start_time DESC
            ", [$currentTime->format('H:i:s'), $advanceLateDuration, $advanceLateDuration]);

            if (!empty($checkinTime)) {
                // Calculate the time when it is considered "late"
                $startTime = Carbon::createFromFormat('H:i:s', $checkinTime[0]->start_time);
                $lateTime = $startTime->addMinutes($advanceLateDuration);
                $isLate = $currentTime->gt($lateTime) ? 1 : 0;
                return response()->json([
                    'message' => $isLate ? 'Late' : 'Available',
                    'workingtime_shift_id' => $checkinTime[0]->workingtime_shift_id,
                    'start_time' => $checkinTime[0]->start_time,
                    'isLate' => $isLate
                ]);
            } else {
                return response()->json([
                    'isAvail' => false,
                    'message' => 'Not in Checkin time'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'isAvail' => false,
                'message' => 'An error occurred while processing your request'
            ], 500);
        }
    }
    public function getAdvancetime()
    {
        try {
            $advanceLate = DB::selectOne("SELECT advance_late_duration FROM checkin_settings WHERE checkin_settings_id = 1;");
            if ($advanceLate) {
                $advanceLateDuration = $advanceLate->advance_late_duration;
                return $advanceLateDuration;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error fetching advance late duration: ' . $e->getMessage());
            return null;
        }
    }
    public function updateAdvanceDuration(Request $request)
    {
        try {
           
            $request->validate([
                'duration' => 'required|integer|min:0',  
            ]);
          
            $duration = $request->input('duration');

            $updateResult = DB::update("
                UPDATE checkin_settings 
                SET advance_late_duration = ? 
                WHERE checkin_settings_id = 1;
            ", [$duration]);

            if ($updateResult) {
                return response()->json(['success' => true, 'message' => 'Advance late duration updated successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'No changes made to advance late duration']);
            }

        } catch (\Exception $e) {
            Log::error('Error updating advance late duration: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update advance late duration'], 500);
        }
    }


    public function saveCheckIn(Request $request)
    {
        try {
            $userName = $request->input('user_id');
            $counterId = $request->input(key: 'counter_id');
            $workingTimeShiftId = $request->input('workingtime_shift_id');
            $currentDateTime = Carbon::now();
            $isLate = $request->input('is_late');
            Log::debug($isLate);
            $checkinDate = $currentDateTime->format('Y-m-d');
            $checkinTime = $currentDateTime->format('H:i:s');

            // Try to get user ID by user name
            $result = DB::select("SELECT id user_id FROM users WHERE id = ?", [$userName]);

            // If user is found, extract user ID, otherwise return an error
            if (!empty($result)) {
                $userId = $result[0]->user_id;
            } else {
                return response()->json(['message' => 'Failed to check In. User not found'], 404);
            }

            // Insert the check-in details into workingtime_history
            $insertResult = DB::insert("
                INSERT INTO workingtime_history (user_id, counter_id, workingtime_shift_id, checkin_date, checkin_time,  is_late) 
                VALUES (?, ?, ?, ?, ?,?)
            ", [$userId, $counterId, $workingTimeShiftId, $checkinDate, $checkinTime, $isLate]);

            // Check if the insert was successful
            if ($insertResult) {
                return response()->json(['message' => 'User Checked In Successfully'], 200);
            } else {
                return response()->json(['message' => 'Failed to check In'], 500);
            }

        } catch (Exception $e) {
            // Log the error with the error message and stack trace
            Log::error('Error during check-in', [
                'user_name' => $userName,
                'counter_id' => $counterId,
                'working_shift_id' => $workingTimeShiftId,
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Return a response to the user
            return response()->json(['message' => 'An error occurred during check-in', 'error' => $e->getMessage()], 500);
        }
    }

    function getTodayCheckInView()
    {
        return view('frontend.checkin.historycheckin');
    }

    public function getTodayCheckIn(request $request)
    {
        // $currentDate = Carbon::now()->format('Y-m-d');
        $userId = $request->input('user_id');
        $checkinDate = $request->input('date');

        if ($checkinDate === null) {
            $checkinDate = Carbon::now()->format('Y-m-d');
        }

        try {
            $workingtimeHistory = DB::select("SELECT ws.start_time , ws.end_time , u.name , c.name FROM workingtime_history wh
                LEFT JOIN users u on u.id =  wh.user_id
                LEFT JOIN workingtime_shift ws on ws.workingtime_shift_id = wh.workingtime_shift_id
                LEFT JOIN counter c on c.counter_id = wh.counter_id
                WHERE wh.user_id = ? and DATE(wh.checkin_date) = ?
                ORDER BY ws.start_time DESC
            ", [$userId, $checkinDate]);
            if (empty($workingtimeHistory)) {
                return response()->json(['message' => 'No check-ins found for today.'], 404);
            }
            return response()->json($workingtimeHistory, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    function getWorkingTimeHistoryView()
    {
        return view('frontend.workingtime.workingtimehistory');
    }

    public function settings()
    {
        return view('frontend.workingtime.settings');
    }

    public function getTodayWorkingTime(Request $request)
    {
        // $userId = $request->input('user_id');
        $currentDate = $request->input('date');
        if ($currentDate === null) {
            $currentDate = Carbon::now()->format('Y-m-d');
        }

        try {
            // Fetch all working time shifts
            $workingtimeShifts = DB::select("
                SELECT workingtime_shift_id, CONCAT(DATE_FORMAT(start_time, '%H:%i'), ' - ', DATE_FORMAT(end_time, '%H:%i')) AS time_shift_range
                FROM workingtime_shift
                ORDER BY start_time
            ");

            // Fetch all counter ids
            $counters = DB::select("
                SELECT counter_id, location_id
                FROM counter
                ORDER BY counter_id
            ");

            // Initialize arrays for JSON output
            $times = [];
            $locations = [];

            // Create an associative array to map counter IDs to their activities
            $counterActivitiesMap = [];
            foreach ($counters as $counter) {
                $counterId = $counter->counter_id;
                $counterResult = DB::select("
                    SELECT name
                    FROM counter
                    WHERE counter_id = ?
                ", [$counterId]);

                $counterName = $counterResult[0]->name;

                // Initialize the counter entry
                if (!isset($counterActivitiesMap[$counterId])) {
                    $counterActivitiesMap[$counterId] = [

                        // แก้ให้เป็นชื่อสถานที่ แทนที่จะเป็น Id
                        'location' => "$counterName",
                        'activities' => array_fill(0, count($workingtimeShifts), 'Guest'),
                    ];
                }
            }

            // Loop through each working time shift
            foreach ($workingtimeShifts as $shiftIndex => $shift) {
                $shiftId = $shift->workingtime_shift_id;

                // Loop through each counter
                foreach ($counters as $counter) {
                    $counterId = $counter->counter_id;

                    // Fetch users for the current shift and counter
                    $workingtimeUser = DB::select("
                        SELECT IFNULL(u.name, 'Guest') as user_name
                        FROM workingtime_history wh
                        LEFT JOIN `users` u ON wh.user_id = u.id
                        WHERE wh.workingtime_shift_id = ? AND wh.counter_id = ? AND DATE(wh.checkin_date) = ?
                    ", [$shiftId, $counterId, $currentDate]);

                    // Collect activities for this shift and counter
                    if (!empty($workingtimeUser)) {
                        foreach ($workingtimeUser as $user) {
                            $counterActivitiesMap[$counterId]['activities'][$shiftIndex] = $user->user_name;
                        }
                    }
                }
            }

            // Collect times
            foreach ($workingtimeShifts as $shift) {
                $times[] = $shift->time_shift_range;
            }

            // Convert counter map to locations array
            $locations = array_values($counterActivitiesMap);

            // Format the result as JSON
            $result = [
                'times' => $times,
                'locations' => $locations
            ];

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }





}
