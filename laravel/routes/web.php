<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Public document repository
Route::get('/documentos-publicos', [DocumentController::class, 'publicRepository'])->name('documents.public');
Route::get('/documentos-publicos/{id}', [DocumentController::class, 'showPublic'])->name('documents.public.show');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Document management
    Route::resource('documents', DocumentController::class);
    Route::post('/documents/{id}/transcribe', [DocumentController::class, 'transcribe'])->name('documents.transcribe');
    Route::post('/documents/{id}/generate', [DocumentController::class, 'generateDocument'])->name('documents.generate');
    Route::post('/documents/{id}/sign', [DocumentController::class, 'sign'])->name('documents.sign');
    Route::post('/documents/{id}/publish', [DocumentController::class, 'publish'])->name('documents.publish');
});