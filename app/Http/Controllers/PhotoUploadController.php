<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoUploadController extends Controller
{
    public function store(Request $request)
    {
        // Validate file
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,gif|max:5120', // 5MB
        ]);

        // Store in storage/app/public/uploads
        $path = $request->file('file')->store('uploads', 'public');

        // You can save $path to DB here if you like

        return response()->json([
            'success' => true,
            'path'    => $path,
            'url'     => asset('storage/' . $path),
        ]);
    }
}
