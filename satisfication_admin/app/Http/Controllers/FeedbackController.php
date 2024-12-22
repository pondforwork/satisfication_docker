<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    function index()
    {

        $username = session('username');
        $googleController = new GoogleAuthController();
        $isAdmin = $googleController->isAdmin();
        if (!$username) {
            return view('login.login');
        } elseif ($isAdmin) {
            return view('frontend.satisfication.satisfication');
        } else {
            return view('login.unauthorized');
        }

        // return view('frontend.satisfication.satisfication');
    }

    public function getList()
    {
        try {
            $location = DB::select('SELECT feedback_answer_id, text, order_no, is_active FROM feedback_answer ORDER BY order_no');

            return response()->json($location, 200);
        } catch (Exception $e) {
            Log::error('Error fetching feedback answers: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching feedback answers'], 500);
        }
    }

    public function getListForClient()
    {
        try {
            $location = DB::select('SELECT feedback_answer_id, text, order_no, is_active FROM feedback_answer WHERE is_active = 1 ORDER BY order_no');

            return response()->json($location, 200);
        } catch (Exception $e) {
            Log::error('Error fetching feedback answers for client: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching feedback answers'], 500);
        }
    }

    public function updateData(Request $request)
    {
        $isActive = $request->input('newIsActive');
        $feedbackAnswerId = $request->input('targetFeedbackAnswerId');

        try {
            $result = DB::update('UPDATE feedback_answer SET is_active = ? WHERE feedback_answer_id = ?', [
                $isActive,
                $feedbackAnswerId
            ]);

            if ($result) {
                return response()->json(['message' => 'Feedback answer updated successfully'], 200);
            } else {
                return response()->json(['message' => 'Failed to update feedback answer'], 500);
            }
        } catch (Exception $e) {
            Log::error('Error updating feedback answer: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating feedback answer'], 500);
        }
    }

    public function saveData(Request $request)
    {
        $id = $request->input('feedbackAnswerId');
        $text = $request->input('text');

        try {
            if ($id != null) {
                $result = DB::update('UPDATE feedback_answer SET text = ? WHERE feedback_answer.feedback_answer_id = ?', [$text, $id]);
                if ($result) {
                    return response()->json(['message' => 'Feedback answer updated successfully'], 200);
                } else {
                    return response()->json(['message' => 'Failed to update feedback answer'], 500);
                }
            } else {
                $lastOrderNo = DB::table('feedback_answer')->max('order_no');
                $newOrderNo = $lastOrderNo ? $lastOrderNo + 1 : 1;

                $result = DB::insert('INSERT INTO feedback_answer (text, order_no) VALUES (?, ?)', [
                    $text,
                    $newOrderNo
                ]);

                if ($result) {
                    return response()->json(['message' => 'Feedback answer saved successfully'], 200);
                } else {
                    return response()->json(['message' => 'Failed to save feedback answer'], 500);
                }
            }
        } catch (Exception $e) {
            Log::error('Error saving feedback answer: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while saving feedback answer'], 500);
        }
    }

    public function reorderData(Request $request)
    {
        $orderData = $request->input('orderData');

        try {
            DB::transaction(function () use ($orderData) {
                foreach ($orderData as $orderItem) {
                    DB::update('UPDATE feedback_answer SET order_no = ? WHERE feedback_answer_id = ?', [
                        $orderItem['order_no'],
                        $orderItem['feedback_answer_id']
                    ]);
                }
            });

            return response()->json(['message' => 'Order updated successfully'], 200);
        } catch (Exception $e) {
            Log::error('Error updating order: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating order'], 500);
        }
    }

    public function deleteData(Request $request)
    {
        $id = $request->input('feedbackAnswerId');

        try {
            if ($id != null) {
                $result = DB::delete('DELETE FROM feedback_answer WHERE feedback_answer_id = ?', [$id]);

                if ($result) {
                    return response()->json(['message' => 'Feedback answer deleted successfully'], 200);
                } else {
                    return response()->json(['message' => 'Failed to delete feedback answer'], 500);
                }
            } else {
                return response()->json(['message' => 'No feedbackAnswerId provided'], 400);
            }
        } catch (Exception $e) {
            Log::error('Error deleting feedback answer: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while deleting feedback answer'], 500);
        }
    }


    public function saveFeedback(Request $request)
    {
        $score = $request->input('score');
        $counterId = $request->input('counterId');
        if ($counterId == -1) {
            $counterId = null;
        }
        $feedbackText = $request->input('feedbackText');
        $dateAdded = $request->input('dateAdded') ?? now();
        $dateAddedFormatted = $dateAdded->format('Y-m-d');
        $timeAddedFormatted = $dateAdded->format('H:i:s');

        try {
            // รับ User Id 
            $todayDate = now()->format('Y-m-d');
            $currentTime = now()->format('H:i:s');
            $user = DB::select('SELECT user_id FROM workingtime_history wh 
                LEFT JOIN workingtime_shift wts on wts.workingtime_shift_id = wh.workingtime_shift_id
                WHERE checkin_date = ?  AND wh.counter_id = ? 
                AND ? BETWEEN wts.start_time AND wts.end_time',
                [$todayDate, $counterId, $currentTime]
            );
            if (!empty($user)) {
                $userId = $user[0]->user_id;
            } else {
                $userId = null;
            }

            $result = DB::insert('INSERT INTO `feedback` (`score`, `counter_id`, `feedback_text`, `date_added`, `time_added`, `user_id`) 
            VALUES(?, ?, ?, ?, ?, ?)', [
                $score,
                $counterId,
                $feedbackText,
                $dateAddedFormatted,
                $timeAddedFormatted,
                $userId
            ]);
            if ($result) {
                return response()->json(['message' => 'Feedback submitted successfully'], 200);
            } else {
                return response()->json(['message' => 'Failed to submit feedback'], 500);
            }
        } catch (Exception $e) {
            Log::error('Error submitting feedback: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while submitting feedback'], 500);
        }
    }
}
