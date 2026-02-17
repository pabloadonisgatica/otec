<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DiplomaTemplateController;
use App\Http\Controllers\DiplomaController;



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
    Route::resource('courses', CourseController::class);
    Route::middleware(['auth'])->group(function () {
    Route::resource('budgets', BudgetController::class);
    Route::middleware(['auth'])->group(function () {
    Route::get('/configuracion', [SettingsController::class, 'index'])->name('settings.index');});
    Route::post('/configuracion/logo', [SettingsController::class, 'updateLogo'])
    ->name('settings.logo.update');


Route::prefix('configuracion')->middleware(['auth'])->group(function () {
    Route::get('/usuarios', [UserManagementController::class, 'index'])->name('settings.users.index');
    Route::get('/usuarios/crear', [UserManagementController::class, 'create'])->name('settings.users.create');
    Route::post('/usuarios', [UserManagementController::class, 'store'])->name('settings.users.store');
    Route::get('/usuarios/{user}/editar', [UserManagementController::class, 'edit'])->name('settings.users.edit');
    Route::put('/usuarios/{user}', [UserManagementController::class, 'update'])->name('settings.users.update');
});


    // Acciones extra
    Route::post('budgets/{budget}/duplicate', [BudgetController::class, 'duplicate'])
        ->name('budgets.duplicate');

    Route::get('budgets/{budget}/pdf', [BudgetController::class, 'pdf'])
        ->name('budgets.pdf');
});


Route::middleware(['auth'])->group(function () {
    Route::resource('diploma-templates', DiplomaTemplateController::class);
});


Route::get('diplomas', [DiplomaController::class, 'index'])->name('diplomas.index');
Route::get('diplomas/emit', [DiplomaController::class, 'create'])->name('diplomas.create');
Route::post('diplomas/emit', [DiplomaController::class, 'store'])->name('diplomas.store');


});


require __DIR__.'/auth.php';
