<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// Channel private untuk percakapan
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    Log::info('Channel authentication attempt', [
        'user_id' => $user->id,
        'conversation_id' => $conversationId,
        'exists' => (bool)$conversation
    ]);

    if (!$conversation) {
        return false;
    }

    // Hanya user yang terlibat dalam percakapan yang dapat mengakses channel
    $hasAccess = $conversation->sender_id === $user->id || $conversation->receiver_id === $user->id;

    Log::info('Channel access decision', [
        'user_id' => $user->id,
        'conversation_id' => $conversationId,
        'has_access' => $hasAccess
    ]);

    return $hasAccess;
});

// Channel publik untuk status user
Broadcast::channel('user-status', function ($user) {
    return true;
});
