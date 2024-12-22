<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    function index()
    {
        $username = session('username');
        $googleController = new GoogleAuthController();
        $isAdmin = $googleController->isAdmin();
        if (!$username) {
            return view('login.login');
        } elseif ($isAdmin) {
            return view('frontend.role.role');
        } else {
            return view('login.unauthorized');
        }
        // return view('frontend.role.role');
    }

    public function getList()
    {
        $users = DB::select("SELECT id, name, role FROM users;");
        return response()->json($users);
    }

    public function getAllRole()
    {
        $roles = DB::select("SELECT DISTINCT(role) FROM role;");
        return response()->json($roles);
    }

    public function updateRole(Request $request)
    {
        $role = $request->input('role');
        $id = $request->input('id');

        try {
            $updated = DB::update("UPDATE users SET role = ? WHERE id = ?", [$role, $id]);
            if ($updated) {
                return response()->json(['success' => true, 'message' => 'Role updated successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'No rows affected'], 400);
            }


        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update role: ' . $e->getMessage()], 500);
        }
    }
}
