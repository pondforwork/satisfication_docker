<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleAuthController extends Controller
{

    public function redirect()
    {
        return Socialite::driver("google")->redirect();
    }
    public function callbackGoogle()
    {
        try {
            $google_user = Socialite::driver("google")->stateless()->user();
            $user = User::where('google_id', $google_user->getId())->first();

            if (!$user) {
                $new_user = User::create([
                    'name' => $google_user->getName(),
                    'email' => $google_user->getEmail(),
                    'google_id' => $google_user->getId(),
                    'role' => "employee",
                ]);
                Auth::login($new_user);

                session([
                    'user_id' => $new_user->id,
                    'username' => $new_user->name
                ]);
                if ($this->isAdmin()) {
                    return view('frontend.location');
                } elseif ($this->isExecutive()) {
                    return view('frontend.stats.stats');
                } else {
                    return view('login.loginsuccessemployee');
                }

            } else {
                Auth::login($user);
                session([
                    'user_id' => $user->id,
                    'username' => $user->name
                ]);
                if ($this->isAdmin()) {
                    return view('frontend.location');
                } elseif ($this->isExecutive()) {
                    return view('frontend.stats.stats');

                } else {
                    return view('login.loginsuccessemployee');
                }
            }
        } catch (InvalidStateException $e) {
            Log::error('Invalid state error: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid state error. Please try again.'], 400);
        } catch (AuthenticationException $e) {
            Log::error('Authentication error: ' . $e->getMessage());
            return response()->json(['error' => 'Authentication error: ' . $e->getMessage()], 401);

        } catch (QueryException $e) {
            Log::error('Database error: ' . $e->getMessage());
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);

        } catch (Exception $e) {
            Log::error('Exception class: ' . get_class($e));  // Log the class of the exception
            Log::error('General error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }


    public function getUsername()
    {
        // Retrieve username from session
        $username = session('username');
        // Check if username exists in session
        if ($username) {
            // Username is set, return as JSON response
            return response()->json(['username' => $username]);
        } else {
            // Username is not set in session
            return response()->json(['error' => 'Username not found in session.'], 404);
        }
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // return redirect()->route('login');
        return redirect()->route('client');
    }
    function register()
    {
        return view('login.login_employee');
    }

    public function isAdmin()
    {
        $username = session('username');
        $role = DB::select("SELECT role FROM users WHERE name = ?;", [$username]);
        if (!empty($role) && $role[0]->role == "admin") {
            return true;
        } else {
            return false;
        }
    }

    public function isExecutive()
    {
        $username = session('username');
        $role = DB::select("SELECT role FROM users WHERE name = ?;", [$username]);
        if (!empty($role) && $role[0]->role == "executive") {
            return true;
        } else {
            return false;
        }
    }

}
