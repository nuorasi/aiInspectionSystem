<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PredictController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:15360'], // 15MB
        ]);

        $file = $request->file('file');

        $apiBase = rtrim(config('services.predict.base_url'), '/'); // e.g. http://104.248.13.114:8000
        $predictUrl = $apiBase . '/predict';

        $response = Http::timeout(60)
            ->attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )
            ->post($predictUrl);

        if (! $response->successful()) {
            return response()->json([
                'message' => 'Predict API request failed',
                'status' => $response->status(),
                'body' => $response->body(),
            ], 502);
        }

        return response()->json($response->json());
    }
}
