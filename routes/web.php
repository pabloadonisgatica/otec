<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ParticipantController;


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
    Route::resource('companies', CompanyController::class);
    Route::resource('instructors', InstructorController::class);
    Route::get('/documents/{type}/{id}/{index}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{type}/{id}/{index}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{type}/{id}/{index}', [DocumentController::class, 'delete'])->name('documents.delete');
    Route::get('/instructors/{instructor}/json', [InstructorController::class, 'show'])->name('instructors.json');
    Route::get('participants/{participant}/json', [ParticipantController::class, 'json'])->name('participants.json');
    Route::get('participants/import', [ParticipantController::class, 'importForm'])->name('participants.import.form');
    Route::post('participants/import', [ParticipantController::class, 'import'])->name('participants.import');
    Route::resource('participants', ParticipantController::class);
   


});


require __DIR__.'/auth.php';
