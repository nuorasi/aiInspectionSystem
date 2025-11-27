<?php

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
Route::get('/learnImagePage', [LearnImageController::class, 'indexTi'])->name('learnImagePage.indexTi');
Route::get('/reviewYourImagesPage', [ReviewYourImagesController::class, 'indexRyi'])->name('reviewYourImagesPage.indexRyi');

require __DIR__.'/auth.php';
