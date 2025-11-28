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

        // EXIF metadata (mostly for jpg/jpeg, tiff)
        $exifData = null;
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['jpg', 'jpeg', 'tif', 'tiff'], true)
            && function_exists('exif_read_data')) {

            try {
                $rawExif = @exif_read_data($absolutePath, null, true);

                if ($rawExif && is_array($rawExif)) {
                    // sanitize EXIF to valid UTF-8 so JSON encoding does not fail
                    $exifData = $this->sanitizeExif($rawExif);
                }
            } catch (\Throwable $e) {
                Log::warning('EXIF read failed', [
                    'path'  => $absolutePath,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Save a record in the database
        $photo = Photo::create([
            'image'      => $path,                        // <- important line
            'disk'       => $disk,
            'path'       => $path,
            'file_name'  => $file->getClientOriginalName(),
            'mime_type'  => $mimeType,
            'size_bytes' => $sizeBytes,
            'width'      => $width,
            'height'     => $height,
            'exif'       => $exifData,                    // if column is TEXT, use json_encode($exifData)
        ]);

        return response()->json([
            'success' => true,
            'photo'   => $photo,
            'url'     => $photo->url,
        ]);
    }

    private function sanitizeExif($data)
    {
        if (is_array($data)) {
            $clean = [];

            foreach ($data as $key => $value) {
                $clean[$key] = $this->sanitizeExif($value);
            }

            return $clean;
        }

        if (is_string($data)) {
            // Try to re-encode to valid UTF-8 and drop bad bytes
            $converted = @mb_convert_encoding($data, 'UTF-8', 'UTF-8, ISO-8859-1, ASCII');
            if ($converted === false) {
                $converted = @iconv('UTF-8', 'UTF-8//IGNORE', $data);
            }
            return $converted ?: '';
        }

        return $data;
    }
    public function updateManualMeta(Request $request, Photo $photo)
    {
        $validated = $request->validate([
            'product'            => 'nullable|string|max:255',
            'size'               => 'nullable|string|max:255',
            'installationStatus' => 'nullable|string|max:255',
            'confidence'         => 'nullable|numeric|min:0|max:100',
        ]);

        $photo->update($validated);

        return response()->json([
            'success' => true,
            'photo'   => $photo,
        ]);
    }
}
