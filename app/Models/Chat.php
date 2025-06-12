<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Log;
use App\Services\EncryptionService;

class Chat extends Model
{
    protected $fillable = ['from_user_id', 'to_user_id', 'message', 'is_staff_message', 'staff_role'];
    
    // Define the attributes to be encrypted AND hashed
    private $encryptedAttributes = [
        "message"
    ];

    // Add magic getter - Now decrypts message
    public function __get($key)
    {
        // If the key is one we encrypt/hash, decrypt it
        if (in_array($key, $this->encryptedAttributes)) {
             return $this->getDecryptedAttribute($key);
        }
        // Otherwise, use default getter
        return parent::__get($key);
    }

    // Add magic setter - Modified to use _hash columns
    public function __set($key, $value)
    {
        if (in_array($key, $this->encryptedAttributes)) {
            // 1. Calculate HMAC hash
            // IMPORTANT: Use a dedicated, securely stored key.
            $hash = hash_hmac('sha256', (string) $value, env('APP_KEY'));
            // Store hash in the dedicated _hash attribute
            $this->attributes[$key . '_hash'] = $hash;

            // 2. Encrypt the original value for storage
            $this->setEncryptedAttribute($key, $value);
            return; // Prevent default setter for these attributes
        }

        // Handle other attributes normally
        parent::__set($key, $value);
    }

    // Add decryption helper method
    /**
     * Decrypts an attribute value.
     */
    protected function getDecryptedAttribute(string $key)
    {
        $encryptedValue = $this->attributes[$key . '_encrypted'] ?? null;
        $encryptedKey = $this->attributes[$key . '_key'] ?? null;

        if (!empty($encryptedValue) && !empty($encryptedKey)) {
            try {
                $encryptionService = app(EncryptionService::class);
                $decrypted = $encryptionService->decryptData($encryptedValue, $encryptedKey);
                return $decrypted;
            } catch (\Exception $e) {
                Log::error("Decryption failed for Chat ID {$this->id}, attribute '{$key}': " . $e->getMessage());
                return null;
            }
        }
        return null;
    }

    // Add encryption helper method
    /**
     * Encrypts an attribute value.
     */
    protected function setEncryptedAttribute(string $key, $value)
    {
        if ($value === null || $value === '') {
            $this->attributes[$key . '_encrypted'] = null;
            $this->attributes[$key . '_key'] = null;
        } else {
            try {
                $encryptionService = app(EncryptionService::class);
                $encryptedResult = $encryptionService->encryptData((string) $value);

                if ($encryptedResult) {
                    $this->attributes[$key . '_encrypted'] = $encryptedResult['encrypted_data'];
                    $this->attributes[$key . '_key'] = $encryptedResult['encrypted_aes_key'];
                } else {
                    Log::error("Encryption failed for Chat ID {$this->id}, attribute '{$key}'. Value not set.");
                    $this->attributes[$key . '_encrypted'] = null;
                    $this->attributes[$key . '_key'] = null;
                }
            } catch (\Exception $e) {
                 Log::error("Encryption exception for Chat ID {$this->id}, attribute '{$key}': " . $e->getMessage());
                 $this->attributes[$key . '_encrypted'] = null;
                 $this->attributes[$key . '_key'] = null;
            }
        }
    }
    
    /**
     * Get the user who sent this message
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
    
    /**
     * Get the user who received this message
     */
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
    
    /**
     * Get the role name for the staff role
     */
    public function getStaffRoleNameAttribute()
    {
        switch ($this->staff_role) {
            case 1: return 'Admin';
            case 2: return 'Pendaftaran';
            case 3: return 'Dokter';
            case 4: return 'Apotek';
            default: return 'Staff';
        }
    }
    
    /**
     * Scope a query to only include messages between a user and staff with specific role
     */
    public function scopeUserStaffConversation($query, $userId, $staffRole)
    {
        // dd($userId, $staffRole);
        return $query->where(function($q) use ($userId, $staffRole) {
            // Messages from user to staff with this role
            $q->where('from_user_id', $userId)
              ->where('is_staff_message', false)
              ->where('staff_role', $staffRole);
        })->orWhere(function($q) use ($userId, $staffRole) {
            // Messages from staff with this role to this user
            $q->where('to_user_id', $userId)
              ->where('is_staff_message', true)
              ->where('staff_role', $staffRole);
        })->with('fromUser');
    }
    public function scopePeerConversation($query, $fromUserId, $toUserId)
    {
        // dd($userId, $staffRole);
        return $query->where(function($q) use ($fromUserId, $toUserId) {
            // Messages from user to staff with this role
            $q->where('from_user_id', $fromUserId)
              ->where('to_user_id', $toUserId);
        })->orWhere(function($q) use ($fromUserId, $toUserId) {
            // Messages from staff with this role to this user
            $q->where('from_user_id', $toUserId)
              ->where('to_user_id', $fromUserId);
        })->with('fromUser');
        
    }
    
    /**
     * Scope a query to only include messages for staff with specific role
     */
    public function scopeStaffRoleMessages($query, $staffRole)
    {
        return $query->where('staff_role', $staffRole);
    }
}