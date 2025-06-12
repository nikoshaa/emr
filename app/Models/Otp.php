<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\EncryptionService; // Ensure this service is correctly set up to handle encryption/decryption

class Otp extends Model
{
    protected $fillable = ['email', 'otp', 'expires_at', 'is_used'];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    private $encryptedAttributes = [

        "otp"
    ];

    public function getEncryptedAttributes()
    {
        return $this->encryptedAttributes;
    }

    public function isExpired()
    {
        return $this->expires_at < Carbon::now();
    }

    public function isValid()
    {
        return !$this->is_used && !$this->isExpired();
    }

    // Dynamically handle getters and setters for encrypted attributes
    public function __get($key)
    {
        if (in_array($key, $this->encryptedAttributes)) {
            return $this->getDecryptedAttribute($key);
        }
        return parent::__get($key);
    }

    public function __set($key, $value)
    {
        if (in_array($key, $this->encryptedAttributes)) {
            $this->setEncryptedAttribute($key, $value);
            return; // Important: prevent default setter after handling
        }
        parent::__set($key, $value);
    }

    /**
     * Decrypts an attribute value.
     */
    protected function getDecryptedAttribute(string $key)
    {
        $encryptedValue = $this->attributes[$key . '_encrypted'] ?? null;
        $encryptedKey = $this->attributes[$key . '_encrypted_key'] ?? null;

        if (!empty($encryptedValue) && !empty($encryptedKey)) {
            try {
                $encryptionService = app(EncryptionService::class);
                $decrypted = $encryptionService->decryptData($encryptedValue, $encryptedKey);
                // Handle potential null return on decryption error from service
                return $decrypted;
            } catch (\Exception $e) {
                Log::error("Decryption failed for OTP ID {$this->id}, attribute '{$key}': " . $e->getMessage());
                return null; // Or return a specific error indicator
            }
        }
        // Return null if no encrypted data exists
        // Or potentially return $this->attributes[$key] if you kept the original column with old data
        return null;
    }

    /**
     * Encrypts an attribute value.
     */
    protected function setEncryptedAttribute(string $key, $value)
    {
        if ($value === null || $value === '') {
            $this->attributes[$key . '_encrypted'] = null;
            $this->attributes[$key . '_encrypted_key'] = null;
            // Optionally clear the original attribute if it exists
            // $this->attributes[$key] = null;
        } else {
            try {
                $encryptionService = app(EncryptionService::class);
                // Ensure data is string for encryption service
                $encryptedResult = $encryptionService->encryptData((string) $value);

                if ($encryptedResult) {
                    $this->attributes[$key . '_encrypted'] = $encryptedResult['encrypted_data'];
                    $this->attributes[$key . '_encrypted_key'] = $encryptedResult['encrypted_aes_key'];
                    // Optionally clear the original attribute if it exists
                    // $this->attributes[$key] = null;
                } else {
                    // Handle encryption failure
                    Log::error("Encryption failed for OTP ID {$this->id}, attribute '{$key}'. Value not set.");
                    // Decide how to handle: throw exception, set null, log?
                    // Setting null might be safest to prevent saving partial/incorrect state.
                    $this->attributes[$key . '_encrypted'] = null;
                    $this->attributes[$key . '_encrypted_key'] = null;
                }
            } catch (\Exception $e) {
                 Log::error("Encryption exception for OTP ID {$this->id}, attribute '{$key}': " . $e->getMessage());
                 $this->attributes[$key . '_encrypted'] = null;
                 $this->attributes[$key . '_encrypted_key'] = null;
            }
        }
    }
}
