<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    function register()
    {
        return view('login.login_employee');
    }

    function index()
    {
        return view('frontend.employee.employee');
    }

    public function getList()
    {
        $location = DB::select("SELECT name, email FROM users WHERE role = 'employee';");
        return response()->json($location);
    }

}
