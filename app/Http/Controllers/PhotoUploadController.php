<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // or Imagick\Driver if you prefer

class PhotoUploadController extends Controller
{
//    public function store(Request $request): JsonResponse
//    {
//        try {
//            $request->validate([
//                // max is in kilobytes
//                'file' => 'required|mimes:jpg,jpeg,png,gif,webp|max:204800', // 200 MB
//            ]);
//        } catch (ValidationException $e) {
//            Log::error('Upload validation failed', $e->errors());
//            throw $e;
//        }
//
//        $file = $request->file('file');
//
//        // Store file on the public disk
//        $disk = 'public';
//        $path = $file->store('uploads', $disk);
//
//        // Absolute path on disk
//        $absolutePath = Storage::disk($disk)->path($path);
//
//        // Basic metadata
//        $sizeBytes = @filesize($absolutePath) ?: null;
//        $mimeType  = $file->getClientMimeType() ?? @mime_content_type($absolutePath) ?: null;
//
//        $width = null;
//        $height = null;
//
//        if (function_exists('getimagesize')) {
//            $imageInfo = @getimagesize($absolutePath);
//            if ($imageInfo && is_array($imageInfo)) {
//                $width = $imageInfo[0] ?? null;
//                $height = $imageInfo[1] ?? null;
//            }
//        }
//
//        // EXIF metadata (mostly for jpg/jpeg, tiff)
//        $exifData = null;
//        $extension = strtolower($file->getClientOriginalExtension());
//
//        if (in_array($extension, ['jpg', 'jpeg', 'tif', 'tiff'], true)
//            && function_exists('exif_read_data')) {
//
//            try {
//                $rawExif = @exif_read_data($absolutePath, null, true);
//
//                if ($rawExif && is_array($rawExif)) {
//                    // sanitize EXIF to valid UTF-8 so JSON encoding does not fail
//                    $exifData = $this->sanitizeExif($rawExif);
//                }
//            } catch (\Throwable $e) {
//                Log::warning('EXIF read failed', [
//                    'path'  => $absolutePath,
//                    'error' => $e->getMessage(),
//                ]);
//            }
//        }
//
//        // Save a record in the database
//        $photo = Photo::create([
//            'image'      => $path,                        // <- important line
//            'disk'       => $disk,
//            'path'       => $path,
//            'file_name'  => $file->getClientOriginalName(),
//            'mime_type'  => $mimeType,
//            'size_bytes' => $sizeBytes,
//            'width'      => $width,
//            'height'     => $height,
//            'exif'       => $exifData,                    // if column is TEXT, use json_encode($exifData)
//        ]);
//
//        return response()->json([
//            'success' => true,
//            'photo'   => $photo,
//            'url'     => $photo->url,
//        ]);
//    }



    public function store(Request $request): JsonResponse
    {

        Log::info('in PhotoUploadController store ident 2342432432 ');
        try {
            $request->validate([
                'file' => 'required|mimes:jpg,jpeg,png,gif,webp|max:204800', // 200 MB
            ]);
        } catch (ValidationException $e) {
            Log::error('Upload validation failed', $e->errors());
            throw $e;
        }

        $file = $request->file('file');

        $disk = 'public';

        // Base folders
        $baseDir     = 'uploads';
        $originalDir = "{$baseDir}/original";
        $scaledDir   = "{$baseDir}/scaled";
        $thumbDir    = "{$baseDir}/thumb";

        // Build a safe unique filename (keep original extension)
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $basename  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBase  = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $basename) ?: 'image';
        $filename  = $safeBase . '_' . now()->format('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;


        Log::info('in store ident 2342432432 filename =>> ' . $filename);


        // 1) Store original
        $originalPath = "{$originalDir}/{$filename}";
        Storage::disk($disk)->putFileAs($originalDir, $file, $filename);

        // Absolute path for metadata + EXIF
        $originalAbsolutePath = Storage::disk($disk)->path($originalPath);

        // Basic metadata from original
        $sizeBytes = @filesize($originalAbsolutePath) ?: null;
        $mimeType  = $file->getClientMimeType() ?? @mime_content_type($originalAbsolutePath) ?: null;

        $width = null;
        $height = null;
        if (function_exists('getimagesize')) {
            $imageInfo = @getimagesize($originalAbsolutePath);
            if ($imageInfo && is_array($imageInfo)) {
                $width = $imageInfo[0] ?? null;
                $height = $imageInfo[1] ?? null;
            }
        }

        // EXIF from original (jpg/jpeg/tiff)
        $exifData = null;
        if (in_array($extension, ['jpg', 'jpeg', 'tif', 'tiff'], true) && function_exists('exif_read_data')) {
            try {
                $rawExif = @exif_read_data($originalAbsolutePath, null, true);
                if ($rawExif && is_array($rawExif)) {
                    $exifData = $this->sanitizeExif($rawExif);
                }
            } catch (\Throwable $e) {
                Log::warning('EXIF read failed', [
                    'path'  => $originalAbsolutePath,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 2) Create scaled (1000px wide)
        // 3) Create thumbnail (example: 300x300 center crop)
        $manager = new ImageManager(new Driver());

        $img = $manager->read($originalAbsolutePath);

        // If you want to always output jpg for scaled/thumb, set $outExt = 'jpg'
        // Otherwise, keep original extension
        $outExt = $extension;

        // Scaled
      //  $scaled = $img->clone()->scaleDown(width: 1000); // keeps aspect ratio, no upsize
        $scaled = (clone $img)->scaleDown(width: 1000);
        $scaledPath = "{$scaledDir}/{$filename}";
        Storage::disk($disk)->put(
            $scaledPath,
            (string) $scaled->encodeByExtension($outExt, quality: 85)
        );

        // Thumb (square crop)
    //    $thumb = $img->clone()->cover(300, 300); // center-crop to exactly 300x300
        $thumb  = (clone $img)->cover(300, 300);
        $thumbPath = "{$thumbDir}/{$filename}";
        Storage::disk($disk)->put(
            $thumbPath,
            (string) $thumb->encodeByExtension($outExt, quality: 80)
        );

        // Save record in DB
        // Adjust columns to match your schema (add these columns if you want to store all paths)
        $photo = Photo::create([
            'disk'        => $disk,
            'file_name'   => $file->getClientOriginalName(),
            'mime_type'   => $mimeType,
            'size_bytes'  => $sizeBytes,
            'width'       => $width,
            'height'      => $height,
            'exif'        => $exifData,

            // Store paths
            'path_original' => $originalPath,
            'path_scaled'   => $scaledPath,
            'path_thumb'    => $thumbPath,

            // If your model currently expects `image` / `path`, pick one as the “default”
            'image'       => $scaledPath,
            'path'        => $scaledPath,
        ]);
// Call Predict API using the scaled image (or original)
        $predict = null;
        Log::info('in PhotoUploadController store ident 32323 calling TensorFlow Predict =>> ');

        try {
            $apiBase = rtrim(config('services.predict.base_url'), '/');
            $predictUrl = $apiBase . '/predict';

            $scaledAbsolutePath = Storage::disk($disk)->path($scaledPath);

            $predictResponse = Http::timeout(90)
                ->attach(
                    'file',
                    file_get_contents($scaledAbsolutePath),
                    basename($scaledAbsolutePath)
                )
                ->post($predictUrl);

            if (! $predictResponse->successful()) {
                Log::warning('Predict API failed', [
                    'status' => $predictResponse->status(),
                    'body' => $predictResponse->body(),
                ]);
            } else {
                Log::info('in PhotoUploadController store ident qq   TensorFlow Predict =>> SUCCESS');

                $predict = $predictResponse->json();
                Log::info(
                    'in PhotoUploadController store ident qq   TensorFlow Predict =>> response data: ' . json_encode($predict$predict, JSON_PRETTY_PRINT)
                );

                // Optional: store prediction in DB (add columns first if you want)
                // $photo->update([
                //     'pred_label' => $predict['label'] ?? null,
                //     'pred_confidence' => $predict['confidence'] ?? null,
                //     'pred_payload' => $predict, // json column recommended
                // ]);
            }
        } catch (\Throwable $e) {
            Log::error('Predict API exception', [
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'photo'   => $photo,
            'urls'    => [
                'original' => Storage::disk($disk)->url($originalPath),
                'scaled'   => Storage::disk($disk)->url($scaledPath),
                'thumb'    => Storage::disk($disk)->url($thumbPath),
            ],
            'predict'  => $predict, // include prediction payload
            'redirect' => route('analyzeImagePage.analyzeImg'),
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
