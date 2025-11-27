<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnalyzeImageController extends Controller
{
    //
    public function indexAi(Request $request): string
    {
        //   Log::info('IN oAuthTest ident 112722d Input variable userId= '.$request->userId);
        Log::info('IN indexAi ident 112722d ' );

       // return $this->apiResponse(200, 'Success', ['oAuthTestResponse' => $oneRosterBearerToken[0]->tokenValue]);
        return 'analyzeImagePage.indexAi';
    }
}
