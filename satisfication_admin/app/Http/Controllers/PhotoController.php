<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoController extends Controller
{
    public function uploadPhoto(Request $request)
    {
        // Validate the request to ensure a file is uploaded
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Store the uploaded file
        if ($request->file('photo')) {
            $file = $request->file('photo');
            $filename = Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/photos', $filename);

            // Return the URL of the uploaded photo
            $url = Storage::url($path);
            return response()->json(['url' => $url], 200);
        }

        return response()->json(['error' => 'File not uploaded'], 400);
    }
}
