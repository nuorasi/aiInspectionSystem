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

        return view('analyzeImagePage.analyzeImg');
    }
}
