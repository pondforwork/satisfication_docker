<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientSettings;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GoogleAuthController;

class ClientDeviceController extends Controller
{
    function index()
    {
        $username = session('username');
        $googleController = new GoogleAuthController();
        $isAdmin = $googleController->isAdmin();
        if (!$username) {
            return view('login.login');
        } elseif ($isAdmin) {
            $settings = DB::select('SELECT wallpaper_url, header_text, footer_text FROM client_settings WHERE client_settings_id = 1 ');
            $setting = $settings[0];
            return view('frontend.client.client', compact('setting'));
        } else {
            return view('login.unauthorized');
        }
    }

    function saveSettings(Request $request)
    {
        $header = $request->input('header');
        $footer = $request->input('footer');
        DB::table('client_settings')
            ->where('client_settings_id', 1)
            ->update([
                'header_text' => $header,
                'footer_text' => $footer,
                'last_updated' => now(),
            ]);

        return response()->json(['message' => 'Settings updated successfully']);
    }

    public function registerDevice(Request $request)
    {
        $deviceName = $request->input('deviceName');
        $counterId = $request->input('counterId');
        try {
            DB::insert("INSERT INTO client_device (`name`, `counter_id`) VALUES (?, ?)", [$deviceName, $counterId]);
            DB::update("UPDATE counter SET owned_by_client = 1 WHERE counter_id = ? ", [$counterId]);
            return response()->json(['message' => 'Device registered successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to register device', 'error' => $e->getMessage()], 500);
        }
    }

    public function checkClientSettings(Request $request)
    {
        try {
            $settings = DB::select("SELECT wallpaper_url ,header_text, footer_text, last_updated FROM client_settings WHERE client_settings_id = 1;");
            return response()->json($settings, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to getClientSettings', 'error' => $e->getMessage()], 500);
        }
    }

    public function checkServerStatus()
    {
        try {
            $currentDateTime = DB::select("SELECT NOW() as currentDateTime");

            return response()->json([
                'status' => 'Server and database are running',
                'currentDateTime' => $currentDateTime[0]->currentDateTime,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Failed to connect to the database',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



}
