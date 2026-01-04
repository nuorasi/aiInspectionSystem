<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AnalyzeImageController extends Controller
{
    //
    public function analyzeImg(): View
    {
        Log::info('IN indexAi ident 112722d');
        $photos = Photo::orderBy('id', 'desc')->get(); // or paginate()

        return view('analyzeImagePage.analyzeImg', compact('photos')); //
    }
}g', compact('photos'));
