<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $from_user_id;
    public $to_user_id;
    public $message;
    public $staff_role;
    public $staff_role_name;
    public $staff_name;
    public $is_staff_message;
    public $is_self_role;
    public $incoming_role;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        $fromUserId, $toUserId, $message, 
        $staffRole, $staffRoleName = null,
        $isStaffMessage, $staff_name,$is_self_role,$incoming_role)
    {
        $this->from_user_id = $fromUserId;
        $this->to_user_id = $toUserId;
        $this->staff_role = $staffRole;
        $this->staff_role_name = $staffRoleName;
        $this->is_staff_message = $isStaffMessage;
        $this->message = $message;
        $this->staff_name = $staff_name;
        $this->is_self_role = $is_self_role;
        $this->incoming_role = $incoming_role;  
        
        // Encrypt the message for broadcasting
        try {
            // $encryptionService = app(\App\Services\EncryptionService::class);
            // $encryptedResult = $encryptionService->encryptData((string) $message);
            
            // if ($encryptedResult) {
            //     $this->message_encrypted = $encryptedResult['encrypted_data'];
            //     $this->message_key = $encryptedResult['encrypted_aes_key'];
            // }
        } catch (\Exception $e) {
            dd($e);
            \Log::error('Failed to encrypt message for broadcasting: ' . $e->getMessage());
            // Fallback to unencrypted message for backward compatibility
            $this->message = $message;
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // dd('chat-channel-' . $this->to_user_id.'-'.$this->staff_role);
        $target_id = $this->is_staff_message ? $this->to_user_id : $this->from_user_id;
        // dd('chat-channel-'. $target_id.'-'.$this->staff_role);
        // dd('chat-channel-'. $target_id.'-'.$this->staff_role);
        // return new Channel('chat-channel-' . $target_id.'-'.$this->staff_role);
        if ($this->is_self_role && $this->to_user_id == 99999999) {
            return new Channel('chat-channel-'. $this->staff_role);
        }
        if ($this->incoming_role && $this->incoming_role < 5) {
            $sortedIds = [$this->to_user_id, $this->from_user_id];
            sort($sortedIds);
            $target_id = implode('-', $sortedIds);
            return new Channel('chat-channel-'. $target_id);
        }
        return new Channel('chat-channel-' . $target_id.'-'.$this->staff_role);
    }
}