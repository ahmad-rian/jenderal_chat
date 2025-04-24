<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index()
    {
        // Ambil semua percakapan user yang sedang login
        $conversations = auth()->user()->conversations()->latest('last_message_at')->get();
        return view('livewire.chat.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        // Verify user has access to this conversation
        $userId = auth()->id();
        if ($conversation->sender_id !== $userId && $conversation->receiver_id !== $userId) {
            abort(403, 'Unauthorized access to this conversation');
        }

        return view('livewire.chat.show', compact('conversation'));
    }

    public function create(User $user)
    {
        $authUser = auth()->user();

        // Log for debugging
        Log::info("Creating conversation between users: {$authUser->id} and {$user->id}");

        // Don't allow chat with self
        if ($authUser->id === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat memulai percakapan dengan diri sendiri.');
        }

        try {
            // Find or create conversation
            $conversation = Conversation::findOrCreateConversation($authUser->id, $user->id);

            // Log success
            Log::info("Conversation created/found with ID: {$conversation->id}");

            return redirect()->route('chat.show', $conversation);
        } catch (\Exception $e) {
            Log::error("Error creating conversation: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat percakapan.');
        }
    }

    public function users()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('livewire.chat.users', compact('users')); // Perbaikan path view
    }
}
