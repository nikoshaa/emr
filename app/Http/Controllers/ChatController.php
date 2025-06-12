<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\User;
use Auth;

class ChatController extends Controller
{
    // Fetch chat users for staff
    public function users(Request $request)
    {
        $staffRole = auth()->user()->role;
        $searchTerm = $request->input('search');
        $page = $request->input('page', 1);
        $perPage = 15; // Number of users per page

        $usersQuery = User::where('role', '!=', $staffRole)
            ->where('id', '!=', auth()->id()) // Exclude self
            ->select('users.*')
            ->selectSub(function ($query) use ($staffRole) {
                $currentUserId = auth()->id(); // Get current staff user's ID
                $query->selectRaw('COUNT(*)')
                    ->from('chats')
                    ->where(function ($subQuery) use ($staffRole, $currentUserId) {
                        $subQuery->whereColumn('users.id', 'chats.from_user_id')
                                 ->where('chats.staff_role', $staffRole)
                                 ->where('chats.is_staff_message', 0); // Messages from user to staff in this role
                        
                        $subQuery->orWhere(function($q) use ($currentUserId) {
                            $q->where('chats.to_user_id', $currentUserId)
                              ->whereColumn('users.id', 'chats.from_user_id'); // Ensure it's still related to the user in the outer query
                        });
                    })
                    ->where('chats.read', 0);
            }, 'unread_count')

            ->selectSub(function ($query) use ($staffRole) {
                $query->select('created_at')
                    ->from('chats')
                    ->where(function($q) use ($staffRole) {
                        $q->whereColumn('users.id', 'chats.from_user_id')
                          ->orWhereColumn('users.id', 'chats.to_user_id');
                    })
                    ->where('chats.staff_role', $staffRole)
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
            }, 'last_chat_activity');

        if ($searchTerm) {
            $usersQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Order by: users with unread messages first, then by last chat activity, then by name
        $users = $usersQuery->orderBy('unread_count', 'desc')
                           ->orderBy('last_chat_activity', 'desc')
                           ->orderBy('name', 'asc')
                           ->paginate($perPage, ['*'], 'page', $page);
        
        return response()->json($users);
    }

    public function markAsRead(Request $request)
    {
        
        $staffRole = $request->input('staff_role',auth()->user()->role);
        $userId = $request->input('user_id');
        // dd($userId, $staffRole);
        
        // Mark all messages from this user to the current staff role as read
        Chat::where('from_user_id', $userId)
            ->where('staff_role', $staffRole)
            ->where('read', false)
            ->update(['read' => true]);
        
        return response()->json(['success' => true]);
    }

    // Fetch messages between user and staff
    public function messages(Request $request)
    {
        // dd($request->input('staff_role'));
        $currentUserRole = auth()->user()->role;
        $currentUserId = auth()->id();
        $staffRole = $request->input('staff_role', $currentUserRole);
        $userId = $request->input('user_id') ?? auth()->user()->id;
        $isChatCorner = $request->input('is_chat_corner', 0) == 1;
        // dd($isChatCorner);  
        $commonID = $userId;
        $isPeer = $request->input('is_peer', 0) == 1;
        // dd($isPeer);

        // dd($commonID);
        
        if ($currentUserRole >= 1 && $currentUserRole < 5) {
            // Staff viewing messages with a specific user
            if (!$userId) {
                return response()->json(['error' => 'User ID is required'], 400);
            }
            if ($isChatCorner) {
                $commonID = 99999999;
            }

            if ($isPeer) {
                $toUserId = $userId;
                $messages = Chat::peerConversation($currentUserId, $toUserId)
                ->orderBy('created_at')
                ->get();
                // dd($messages);
            }else{
                $messages = Chat::userStaffConversation($commonID, $staffRole)
                    ->orderBy('created_at')
                    ->get();
            }
            
                // ->toSql();
            // dd($messages);
            // dd($messages);
        } else {
            // User viewing their messages with a specific staff role
            if (!$staffRole) {
                $staffRole = 1; // Default to Admin if not specified
            }
            
            $messages = Chat::userStaffConversation(auth()->id(), $staffRole)
                ->orderBy('created_at','asc')
                ->get();
        }
        
        // Decrypt messages before sending to client
        $decryptedMessages = $messages->map(function($chat) {
            $chatData = $chat->toArray();
            $chatData['message'] = $chat->message; // This will use the __get magic method to decrypt
            $chatData['staff_role_name'] = $chat->staff_role_name;
            return $chatData;
        });
        
        return response()->json($decryptedMessages);
    }

    // Send a message
    public function send(Request $request)
    {
        try {
            $fromUserId = auth()->id();
            $currentUserRole = auth()->user()->role;
            $toUserId = $request->input('user_id') ?? 99999999;
            $staffName = null;
            $staffRole = $request->input('staff_role', $currentUserRole); // Default to Admin if not specified
            $isChatCorner = $request->input('is_chat_corner', 0) == 1;
            $isSelfRole = ($staffRole == $currentUserRole);
            $incomingRole = $request->input('incoming_role', null); // Default to Admin if not specified
            
            if ($currentUserRole >= 1 && $currentUserRole < 5) {
                // Staff sending message to user
                
                // $staffRole = $currentUserRole;
                $isStaffMessage = true;
                if ($isChatCorner && !$isSelfRole) {
                    $isStaffMessage = false;
                }
                $staffName = auth()->user()->name;
            } else {
                $isStaffMessage = false;
            }
            
            $message = $request->input('message');
            
            $chat = new Chat();
            $chat->from_user_id = $fromUserId;
            $chat->to_user_id = $toUserId;
            $chat->message = $message;
            $chat->is_staff_message = $isStaffMessage;
            $chat->staff_role = $staffRole;
            // dd($chat);
            // dd($chat);
            $chat->save();
            // dd($staffRole);
            

            // dd($isSelfRole);

            // Broadcast the message to the recipient
            event(new \App\Events\NewChatMessage($fromUserId, 
                $toUserId, $message, $staffRole, 
                $chat->staff_role_name, $isStaffMessage,
                $staffName,
                $isSelfRole,
                $incomingRole
            ));
            
            return response()->json([
                'id' => $chat->id,
                'from_user_id' => $chat->from_user_id,
                'to_user_id' => $chat->to_user_id,
                'staff_role' => $chat->staff_role,
                'staff_role_name' => $chat->staff_role_name,
                'is_staff_message' => $chat->is_staff_message,
                'created_at' => $chat->created_at
            ]);
        } catch (\Exception $e) {
            \Log::error('Chat send error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to send message',
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 500);
        }
    }
    public function staffChat()
    {
        // Only staff can access this page
        if (auth()->user()->role < 1 || auth()->user()->role > 4) {
            return redirect()->route('dashboard');
        }
        
        return view('chat.staff');
    }

    /**
     * Decrypt a message for the client-side
     */
    public function decryptMessage(Request $request)
    {
        $request->validate([
            'message_encrypted' => 'required|string',
            'message_key' => 'required|string',
        ]);
        
        try {
            // Create a temporary Chat model instance to use its decryption methods
            $tempChat = new Chat();
            
            // Manually set the encrypted attributes
            $tempChat->attributes['message_encrypted'] = $request->message_encrypted;
            $tempChat->attributes['message_key'] = $request->message_key;
            
            // Get the decrypted message
            $decryptedMessage = $tempChat->getDecryptedAttribute('message');
            
            if ($decryptedMessage) {
                return response()->json([
                    'success' => true,
                    'message' => $decryptedMessage
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to decrypt message'
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Message decryption error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error decrypting message'
            ], 500);
        }
    }
}