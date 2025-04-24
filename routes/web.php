<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Api\UserStatusController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('login/google', [GoogleController::class, 'redirect'])
    ->name('login.google');

Route::get('login/google/callback', [GoogleController::class, 'callback']);

Route::middleware(['auth', 'web'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Chat routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/users', [ChatController::class, 'users'])->name('chat.users');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/create/{user}', [ChatController::class, 'create'])->name('chat.create');

    // User status endpoints
    Route::post('/user/status/online', [UserStatusController::class, 'online']);
    Route::post('/user/status/offline', [UserStatusController::class, 'offline']);

    Route::middleware(['track.user.activity'])->group(function () {});
});

require __DIR__ . '/auth.php';
