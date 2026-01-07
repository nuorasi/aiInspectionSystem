<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Products;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LearnImageController extends Controller
{
    //
    public function learnImg(Request $request)
    {
        //   Log::info('IN oAuthTest ident 112722d Input variable userId= '.$request->userId);
        Log::info('IN indexAi ident 112722d ' );

        // return $this->apiResponse(200, 'Success', ['oAuthTestResponse' => $oneRosterBearerToken[0]->tokenValue]);
        $photos = Photo::orderBy('id', 'desc')->get(); // or paginate()

        $products = Products::select('id', 'name')->orderBy('name')->get();

        $productSizes = ProductSize::select('id', 'productId', 'size')
            ->orderBy('size')
            ->get();
//        Log::info('IN LearnImageController ident 112722d products->', (array)print_r($products, true));
//        Log::info('IN LearnImageController ident 112722d productSizes->', (array)print_r($productSizes, true));
//        return view('your-blade-view', compact('products', 'productSizes'));

        return view('learnImagePage.learnImg', compact('photos','productSizes','products'));
    }
}
