<?php

namespace App\Http\Middleware;

use App\Events\UserOnlineStatusChanged;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Update status online jika belum online
            if (!$user->is_online) {
                $user->update([
                    'is_online' => true,
                    'last_active_at' => now()
                ]);

                broadcast(new UserOnlineStatusChanged($user, true));
            }
            // Update last_active_at setiap 5 menit
            else if ($user->last_active_at->diffInMinutes(now()) >= 5) {
                $user->update([
                    'last_active_at' => now()
                ]);
            }
        }

        return $next($request);
    }
}
