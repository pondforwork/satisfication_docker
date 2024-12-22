<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CounterServiceController extends Controller
{
    function index($id, $name)
    {
        return view('frontend.counter.counter', compact('id', 'name'));
    }

    public function getList($id)
    {
        try {
            $counter = DB::select('SELECT c.counter_id, c.location_id , c.name , IF(c.owned_by_client = 0,"No Device",cl.name) client_name  FROM counter c
            LEFT JOIN client_device cl on c.counter_id = cl.counter_id WHERE location_id = ?', [$id]);
            return response()->json($counter, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch counters', 'details' => $e->getMessage()], 500);
        }
    }

    public function deleteData(Request $request)
    {
        try {
            $counter_id = $request->input('counter_id');
            $locationSequence = DB::delete("DELETE FROM counter WHERE counter_id = ?", [$counter_id]);

            if ($locationSequence) {
                return response()->json(['message' => 'Counter deleted successfully'], 200);
            } else {
                return response()->json(['message' => 'Counter not found or already deleted'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the counter', 'message' => $e->getMessage()], 500);
        }
    }


    public function getListForClient()
    {
        try {
            $counter = DB::select("
                SELECT c.counter_id, c.owned_by_client, c.name, c.location_id 
                FROM counter c
                LEFT JOIN location fl ON fl.location_id = c.location_id
                WHERE c.owned_by_client = 0;
            ");

            return response()->json($counter, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch counters', 'details' => $e->getMessage()], 500);
        }
    }

    public function unlinkDevice(Request $request)
    {
        try {
            $counterId = $request->counterId;

            DB::update("UPDATE counter SET owned_by_client = 0 WHERE counter_id = ?;", [$counterId]);
            DB::delete("DELETE FROM client_device WHERE counter_id = ?;", [$counterId]);

            return response()->json(['message' => 'Device unlinked successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to unlink device', 'details' => $e->getMessage()], 500);
        }
    }

}
