<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PhotoUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            // Allow large files and common image formats
            $request->validate([
                // "file" MUST match Dropzone's paramName
                'file' => 'required|mimes:jpg,jpeg,png,gif,webp|max:204800', // 200 MB
            ]);
        } catch (ValidationException $e) {
            // Log what is actually failing
            Log::error('Upload validation failed', $e->errors());

            // Let Laravel return the usual 422 JSON
            throw $e;
        }

        // Store the file
        $path = $request->file('file')->store('uploads', 'public');

        return response()->json([
            'success' => true,
            'path'    => $path,
            'url'     => asset('storage/' . $path),
        ]);
    }
}
