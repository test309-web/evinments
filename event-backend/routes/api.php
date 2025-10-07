<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;

// routes المصادقة العامة
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// routes المحمية
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // routes الأحداث
    Route::apiResource('events', EventController::class);
    
    // routes التسجيل في الأحداث
    Route::post('/events/{event}/register', [EventRegistrationController::class, 'register']);
    Route::get('/my-events', [EventController::class, 'userEvents']);
    Route::get('/my-registrations', [EventRegistrationController::class, 'getUserRegistrations']);
});