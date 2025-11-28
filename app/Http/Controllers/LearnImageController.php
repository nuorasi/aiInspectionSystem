<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LearnImageController extends Controller
{
    //
    public function indexImg(Request $request)
    {
        //   Log::info('IN oAuthTest ident 112722d Input variable userId= '.$request->userId);
        Log::info('IN indexAi ident 112722d ' );

        // return $this->apiResponse(200, 'Success', ['oAuthTestResponse' => $oneRosterBearerToken[0]->tokenValue]);
        $photos = Photo::orderBy('id', 'desc')->get(); // or paginate()

        return view('learnImagePage.indexImg', compact('photos'));
    }
}
