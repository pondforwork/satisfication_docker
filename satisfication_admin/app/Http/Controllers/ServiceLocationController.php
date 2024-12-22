<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Log;

class ServiceLocationController extends Controller
{

    function index()
    {
        $username = session('username');
        $googleController = new GoogleAuthController();
        $isAdmin = $googleController->isAdmin();
        if (!$username) {
            return view('login.login');
        } elseif ($isAdmin) {
            return view('frontend.location');
        } else {
            return view('login.unauthorized');
        }
    }

    public function getList()
    {
        $location = DB::select('SELECT location_id, `name`, code FROM `location`;');
        return response()->json($location, 200);
    }

    public function saveData(Request $request)
    {
        try {
            $location_name = $request->input('location_name');
        
            // สร้างลำดับรหัสสถานที่
            $locationSequence = DB::select("SELECT CONCAT(prefix, last_order) AS location_code, last_order FROM sequence WHERE sequence_id = 1;");
            $location_code = $locationSequence[0]->location_code;
        
            // Insert the new location
            DB::insert('INSERT INTO location (`name`, `code`) VALUES (?, ?)', [$location_name, $location_code]);
        
            // อัพเดทลำดับรหัสสถานที่
            DB::update('UPDATE sequence SET last_order = last_order + 1 WHERE sequence_id = 1');
        
            return response()->json(['message' => 'Location added successfully']);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('An error occurred while adding the location: ' . $e->getMessage(), [
                'exception' => $e,
                'location_name' => $location_name ?? 'N/A',
            ]);
        
            // Handle the error and return a response
            return response()->json(['error' => 'An error occurred while adding the location.', 'details' => $e->getMessage()], 500);
        }
    }

    public function deleteData(Request $request)
    {
        try {
            $location_id = $request->input('location_id');
            $locationSequence = DB::delete("DELETE FROM `location` WHERE location_id = ?", [$location_id]);

            if ($locationSequence) {
                return response()->json(['message' => 'Location deleted successfully'], 200);
            } else {
                return response()->json(['message' => 'Location not found or already deleted'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the Location', 'message' => $e->getMessage()], 500);
        }
    }
}
