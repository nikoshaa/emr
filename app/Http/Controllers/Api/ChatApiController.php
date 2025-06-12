<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatApiController extends Controller
{
    /**
     * Get messages for the authenticated user
     */
    public function getMessages(Request $request)
    {
        $user = Auth::user();
        
        // Get messages between user and admin
        $messages = Chat::where(function($query) use ($user) {
                $query->where('from_user_id', $user->id)
                      ->orWhere('to_user_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json($messages);
    }

    /**
     * Send a message from the authenticated user
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = Auth::user();
        $message = $request->input('message');
        
        // Find an admin to send the message to (assuming admin has role=1)
        $admin = User::where('role', 1)->first();
        
        if (!$admin) {
            return response()->json(['error' => 'No admin available'], 500);
        }
        
        $chat = Chat::create([
            'from_user_id' => $user->id,
            'to_user_id' => $admin->id,
            'message' => $message,
            'is_admin_message' => false,
            'read' => false
        ]);
        
        // Broadcast the message to the admin
        event(new NewChatMessage($user->id, $admin->id, $message));
        
        return response()->json($chat);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();
        
        // Mark messages sent to the user as read
        $updated = Chat::where('to_user_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);
        
        return response()->json(['success' => true, 'updated' => $updated]);
    }
    
    /**
     * Get unread message count
     */
    public function getUnreadCount(Request $request)
    {
        $user = Auth::user();
        
        $count = Chat::where('to_user_id', $user->id)
            ->where('read', false)
            ->count();
        
        return response()->json(['unread_count' => $count]);
    }
}