<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SequenceController extends Controller
{
    public function getSequence()
    {
        $sequence = DB::select("SELECT CONCAT(prefix, last_order) device_code , last_order FROM sequence WHERE sequence_id = 2;");
        return response()->json($sequence, 200);
    }

    public function updateSequence()
    {
        $sequence = DB::select("SELECT CONCAT(prefix, last_order) device_code , last_order FROM sequence WHERE sequence_id = 2;");
        $last_order = $sequence[0]->last_order;
        DB::update("UPDATE sequence SET last_order = ? WHERE sequence_id = 2;", [$last_order + 1]);
        return response()->json($sequence, 200);
    }
}
