<?php
use App\Http\Controllers\PhotoUploadControllerForTrain;
use App\Http\Controllers\AnalyzeImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewYourImagesController;
use App\Http\Controllers\LearnImageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PredictController;
use App\Models\ProductSize;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/analyzeImagePage', [AnalyzeImageController::class, 'analyzeImg'])->name('analyzeImagePage.analyzeImg');
Route::get('/learnImagePage', [LearnImageController::class, 'learnImg'])->name('learnImagePage.learnImg');
Route::get('/reviewYourImagesPage', [ReviewYourImagesController::class, 'reviewImg'])->name('reviewYourImagesPage.reviewImg');





Route::post('/predict-upload', [PredictController::class, 'upload'])->name('predict.upload');


Route::post('/upload-photo', [PhotoUploadControllerForTrain::class, 'store'])
    ->name('photos.upload');

Route::get('/photos/upload', function () {
    return view('photos.upload');
})->name('photos.upload.form');


//Route::get('/products/{product}/sizes', function ($productId) {
//    return Product_size::where('productId', $productId)
//        ->select('id', 'size')
//        ->orderBy('size')
//        ->get();
//})->name('products.sizes');

Route::get('/products/{product}/sizes', function ($productId) {
    return ProductSize::where('productId', $productId)
        ->select('id', 'size')
        ->orderBy('size')
        ->get();
})->name('products.sizes');

Route::post('/photos', [PhotoUploadControllerForTrain::class, 'store'])->name('photos.store');



Route::delete('/photos/{photo}', [PhotoUploadControllerForTrain::class, 'destroy'])->name('photos.destroy');
Route::delete('/photos', [PhotoUploadControllerForTrain::class, 'destroyAll'])->name('photos.destroyAll');




require __DIR__.'/auth.php';
