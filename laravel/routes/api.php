<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DocumentApiController;
use App\Http\Controllers\Api\AuthApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthApiController::class, 'login']);
    Route::post('/auth/register', [AuthApiController::class, 'register']);
    
    // Public documents
    Route::get('/documents/public', [DocumentApiController::class, 'publicDocuments']);
    Route::get('/documents/public/{id}', [DocumentApiController::class, 'showPublic']);
    
    // Protected API routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);
        
        // Document API
        Route::apiResource('documents', DocumentApiController::class);
        Route::post('/documents/{id}/transcribe', [DocumentApiController::class, 'transcribe']);
        Route::post('/documents/{id}/generate', [DocumentApiController::class, 'generateDocument']);
        Route::post('/documents/{id}/sign', [DocumentApiController::class, 'sign']);
        Route::post('/documents/{id}/publish', [DocumentApiController::class, 'publish']);
    });
});