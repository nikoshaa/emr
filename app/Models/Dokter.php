<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// {{ Add necessary use statements }}
use Illuminate\Support\Facades\Log;
use App\Services\EncryptionService; // Ensure this service exists and is configured

class Dokter extends Model
{
    use HasFactory;
    protected $table = "dokter";
    // {{ Keep original column names in fillable }}
    protected $fillable = ["nip","nama","no_hp","alamat","poli","status","user_id"];

    // {{ Define the attributes to be encrypted }}
    private $encryptedAttributes = [
        "nip", "nama", "no_hp", "alamat"
    ];

    // {{ Add magic getter }}
    public function __get($key)
    {
        if (in_array($key, $this->encryptedAttributes)) {
            return $this->getDecryptedAttribute($key);
        }
        return parent::__get($key);
    }

    // {{ Add magic setter }}
    public function __set($key, $value)
    {
        if (in_array($key, $this->encryptedAttributes)) {
            $this->setEncryptedAttribute($key, $value);
            return; // Prevent default setter
        }
        parent::__set($key, $value);
    }

    // {{ Add decryption helper method (adapted) }}
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
                // {{ Update Log message context }}
                Log::error("Decryption failed for Dokter ID {$this->id}, attribute '{$key}': " . $e->getMessage());
                return null;
            }
        }
        return null;
    }

    // {{ Add encryption helper method (adapted) }}
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
                    // {{ Update Log message context }}
                    Log::error("Encryption failed for Dokter ID {$this->id}, attribute '{$key}'. Value not set.");
                    $this->attributes[$key . '_encrypted'] = null;
                    $this->attributes[$key . '_key'] = null;
                }
            } catch (\Exception $e) {
                 // {{ Update Log message context }}
                 Log::error("Encryption exception for Dokter ID {$this->id}, attribute '{$key}': " . $e->getMessage());
                 $this->attributes[$key . '_encrypted'] = null;
                 $this->attributes[$key . '_key'] = null;
            }
        }
    }

    // ... existing methods like status_display() ...

    function status_display(){
        return $this->status ==1 ? 'Aktif' :'Tidak Aktif';
    }
}