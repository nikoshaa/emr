<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatApiController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Chat routes
    Route::get('/chat/messages', [ChatApiController::class, 'getMessages']);
    Route::post('/chat/send', [ChatApiController::class, 'sendMessage']);
    Route::post('/chat/mark-read', [ChatApiController::class, 'markAsRead']);
    Route::get('/chat/unread-count', [ChatApiController::class, 'getUnreadCount']);
});
