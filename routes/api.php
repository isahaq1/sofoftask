<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('login', function () {
    return response()->json([
        'message' => 'Please login first,you are not authorized to access this route',
        'status' => 401
    ], 401);
})->name('login.check');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Task routes
    Route::apiResource('tasks', TaskController::class);
    Route::post('tasks/{task}/assign', [TaskController::class, 'assign']);
    Route::patch('tasks/{task}/status', [TaskController::class, 'changeStatus']);
});
