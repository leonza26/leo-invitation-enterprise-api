<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Events
    Route::apiResource('events', EventController::class);
    Route::post('/events/{event}/duplicate', [EventController::class, 'duplicate']);
    Route::post('/events/{event}/publish', [EventController::class, 'publish']);
    Route::post('/events/{event}/archive', [EventController::class, 'archive']);

    // Guests
    Route::get('/events/{eventId}/guests', [App\Http\Controllers\GuestController::class, 'index']);
    Route::post('/events/{eventId}/guests', [App\Http\Controllers\GuestController::class, 'store']);
    Route::put('/events/{eventId}/guests/{id}', [App\Http\Controllers\GuestController::class, 'update']);
    Route::delete('/events/{eventId}/guests/{id}', [App\Http\Controllers\GuestController::class, 'destroy']);
    Route::post('/events/{eventId}/guests/import', [App\Http\Controllers\GuestController::class, 'bulkImport']);
    Route::get('/events/{eventId}/guests/{id}/whatsapp', [App\Http\Controllers\GuestController::class, 'whatsappLink']);

    // Delete GuestBook entry
    Route::delete('/events/{eventId}/guestbook/{id}', [App\Http\Controllers\GuestBookController::class, 'destroy']);
});

// --- Public Routes ---
Route::get('/public/v/{slug}/guest/{qrCode}', [App\Http\Controllers\GuestController::class, 'publicShow']);
Route::post('/public/v/{slug}/guest/{qrCode}/rsvp', [App\Http\Controllers\GuestController::class, 'publicUpdateRsvp']);

Route::get('/public/v/{slug}/guestbook', [App\Http\Controllers\GuestBookController::class, 'index']);
Route::post('/public/v/{slug}/guestbook', [App\Http\Controllers\GuestBookController::class, 'store']);

