<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Services\EncryptionService;

class Obat extends Model
{
    protected $table = "obat";
    // {{ Keep kd_obat and nama out of fillable, handled by __set }}
    protected $fillable = ["satuan","stok","foto","harga","is_bpjs","deleted_at",'poli_id'];

    // {{ Define the attributes to be encrypted AND hashed }}
    private $encryptedAttributes = [
        "kd_obat", "nama"
    ];

    // {{ Add magic getter - Now decrypts kd_obat and nama }}
    public function __get($key)
    {
        // {{ If the key is one we encrypt/hash, decrypt it }}
        if (in_array($key, $this->encryptedAttributes)) {
             return $this->getDecryptedAttribute($key);
        }
        // {{ Otherwise, use default getter }}
        return parent::__get($key);
    }

    // {{ Add magic setter - Modified to use _hash columns }}
    public function __set($key, $value)
    {
        if (in_array($key, $this->encryptedAttributes)) {
            // 1. Calculate HMAC hash
            // IMPORTANT: Use a dedicated, securely stored key.
            $hash = hash_hmac('sha256', (string) $value, env('APP_KEY'));
            // {{ Store hash in the dedicated _hash attribute }}
            $this->attributes[$key . '_hash'] = $hash;

            // 2. Encrypt the original value for storage
            $this->setEncryptedAttribute($key, $value);
            // {{ Optionally clear the original attribute if it exists in $attributes }}
            // unset($this->attributes[$key]);
            return; // Prevent default setter for these attributes
        }

        // Handle other attributes normally
        parent::__set($key, $value);
    }

    // {{ Add decryption helper method (adapted from previous examples) }}
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
                Log::error("Decryption failed for Obat ID {$this->id}, attribute '{$key}': " . $e->getMessage());
                return null;
            }
        }
        return null;
    }

    // {{ Add encryption helper method (adapted from previous examples) }}
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
                    Log::error("Encryption failed for Obat ID {$this->id}, attribute '{$key}'. Value not set.");
                    $this->attributes[$key . '_encrypted'] = null;
                    $this->attributes[$key . '_key'] = null;
                }
            } catch (\Exception $e) {
                 Log::error("Encryption exception for Obat ID {$this->id}, attribute '{$key}': " . $e->getMessage());
                 $this->attributes[$key . '_encrypted'] = null;
                 $this->attributes[$key . '_key'] = null;
            }
        }
    }

    // add relation to poli table
    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }
    
}
