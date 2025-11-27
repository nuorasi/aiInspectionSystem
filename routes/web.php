<?php

use App\Http\Controllers\AnalyzeImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewYourImagesController;
use App\Http\Controllers\TeachImageController;
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


Route::get('/analyzeImagePage', [AnalyzeImageController::class, 'indexAi'])->name('analyzeImagePage.indexStu');
Route::get('/teachImagePage', [TeachImageController::class, 'indexTi'])->name('teachImagePage.indexStu');
Route::get('/reviewYourImagesPage', [ReviewYourImagesController::class, 'indexRyi'])->name('reviewYourImagesPage.indexRyi');

require __DIR__.'/auth.php';
