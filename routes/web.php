<?php
use App\Http\Controllers\PhotoUploadController;
use App\Http\Controllers\AnalyzeImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewYourImagesController;
use App\Http\Controllers\LearnImageController;
use Illuminate\Support\Facades\Route;

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


Route::get('/analyzeImagePage', [AnalyzeImageController::class, 'indexAi'])->name('analyzeImagePage.indexAi');
Route::get('/learnImagePage', [LearnImageController::class, 'indexImg'])->name('learnImagePage.indexImg');
Route::get('/reviewYourImagesPage', [ReviewYourImagesController::class, 'indexRyi'])->name('reviewYourImagesPage.indexRyi');





Route::post('/upload-photo', [PhotoUploadController::class, 'store'])
    ->name('photos.upload');

Route::get('/photos/upload', function () {
    return view('photos.upload');
})->name('photos.upload.form');


Route::post('/photos/{photo}/manual-meta', [PhotoUploadController::class, 'updateManualMeta'])
    ->name('photos.updateManualMeta');

require __DIR__.'/auth.php';
