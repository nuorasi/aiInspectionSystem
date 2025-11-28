<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PhotoUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                // max is in kilobytes
                'file' => 'required|mimes:jpg,jpeg,png,gif,webp|max:204800', // 200 MB
            ]);
        } catch (ValidationException $e) {
            Log::error('Upload validation failed', $e->errors());
            throw $e;
        }

        $file = $request->file('file');

        // Store file on the public disk
        $disk = 'public';
        $path = $file->store('uploads', $disk);

        // Absolute path on disk
        $absolutePath = Storage::disk($disk)->path($path);

        // Basic metadata
        $sizeBytes = @filesize($absolutePath) ?: null;
        $mimeType  = $file->getClientMimeType() ?? @mime_content_type($absolutePath) ?: null;

        $width = null;
        $height = null;

        if (function_exists('getimagesize')) {
            $imageInfo = @getimagesize($absolutePath);
            if ($imageInfo && is_array($imageInfo)) {
                $width = $imageInfo[0] ?? null;
                $height = $imageInfo[1] ?? null;
            }
        }

//        // EXIF metadata (mostly for jpg/jpeg, tiff)
//        $exifData = null;
//        $extension = strtolower($file->getClientOriginalExtension());
//
//        if (in_array($extension, ['jpg', 'jpeg', 'tif', 'tiff'], true)
//            && function_exists('exif_read_data')) {
//
//            try {
//                // Use @ to suppress warnings for files without EXIF
//                $rawExif = @exif_read_data($absolutePath, null, true);
//
//                if ($rawExif && is_array($rawExif)) {
//                    $exifData = $rawExif;
//                }
//            } catch (\Throwable $e) {
//                Log::warning('EXIF read failed', [
//                    'path' => $absolutePath,
//                    'error' => $e->getMessage(),
//                ]);
//            }
//        }

        // Save a record in the database
        $photo = Photo::create([
            'disk'       => $disk,
            'path'       => $path,
            'file_name'  => $file->getClientOriginalName(),
            'mime_type'  => $mimeType,
            'size_bytes' => $sizeBytes,
            'width'      => $width,
            'height'     => $height,
            'exif'       => $exifData,
        ]);

        return response()->json([
            'success' => true,
            'photo'   => $photo,
            'url'     => $photo->url,
        ]);
    }
}
