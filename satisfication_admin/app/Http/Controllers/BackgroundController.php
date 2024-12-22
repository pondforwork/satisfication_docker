<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BackgroundController extends Controller
{

    public function getImage($imageName)
    {
        $path = public_path('images/' . $imageName);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        $file = file_get_contents($path);
        $type = mime_content_type($path);

        return response($file, 200)->header('Content-Type', $type);
    }

    public function upload(Request $request)
{
    try {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);

        // Store the uploaded file in the 'project/public/images' directory
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);
        $dateModified = now();

        // Update database with image file name and last updated timestamp
        DB::update('UPDATE client_settings SET wallpaper_url = ? , last_updated = ? WHERE client_settings_id = 1;', [
            $imageName,
            $dateModified
        ]);

        // Return success response
        return response()->json(['message' => 'Image uploaded successfully', 'image' => $imageName], 200);
        
    } catch (\Exception $e) {
        // Return error response
        return response()->json(['error' => 'Error uploading image', 'message' => $e->getMessage()], 500);
    }
}
}
