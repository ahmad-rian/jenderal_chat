<?php

namespace App\Http\Controllers\Api;

use App\Events\UserOnlineStatusChanged;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserStatusController extends Controller
{
    /**
     * Update user status to online
     */
    public function online(Request $request)
    {
        $user = $request->user();
        $user->update([
            'is_online' => true,
            'last_active_at' => now()
        ]);

        broadcast(new UserOnlineStatusChanged($user, true))->toOthers();

        return response()->json(['status' => 'success']);
    }

    /**
     * Update user status to offline
     */
    public function offline(Request $request)
    {
        $user = $request->user();
        $user->update([
            'is_online' => false,
            'last_active_at' => now()
        ]);

        broadcast(new UserOnlineStatusChanged($user, false))->toOthers();

        return response()->json(['status' => 'success']);
    }
}
