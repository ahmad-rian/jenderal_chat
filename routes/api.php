<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\UserOnlineStatusChanged;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/status/online', function (Request $request) {
        $user = $request->user();
        $user->update([
            'is_online' => true,
            'last_active_at' => now()
        ]);

        broadcast(new UserOnlineStatusChanged($user, true))->toOthers();

        return response()->json(['status' => 'success']);
    });

    Route::post('/user/status/offline', function (Request $request) {
        $user = $request->user();
        $user->update([
            'is_online' => false,
            'last_active_at' => now()
        ]);

        broadcast(new UserOnlineStatusChanged($user, false))->toOthers();

        return response()->json(['status' => 'success']);
    });
});
