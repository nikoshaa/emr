<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Services\EncryptionService; // Make sure EncryptionService is updated as per previous step
use Illuminate\Support\Str; // Needed for studly case conversion

class Pasien extends Model
{
    protected $table = "pasien";
    // Keep original column names in fillable for ease of use with forms/requests
    protected $fillable = ["no_rm","nama","tmp_lahir","tgl_lahir","jk","alamat_lengkap"
    ,"kelurahan","kecamatan","kabupaten","kodepos","agama","status_menikah","pendidikan"
    ,"pekerjaan","kewarganegaraan","no_hp","cara_bayar","no_bpjs","deleted_at","alergi",
    'general_uncent'
    ];

    // List of attributes to automatically encrypt/decrypt
    // Should match the list in the migration
    private $encryptedAttributes = [
        
        "nama", "tmp_lahir", "tgl_lahir", "jk", "alamat_lengkap",
        "kelurahan", "kecamatan", "kabupaten", "kodepos", "agama", "status_menikah",
        "pendidikan", "pekerjaan", "kewarganegaraan", "no_hp", "cara_bayar",
        "alergi"
    ];

    public function getEncryptedAttributes()
    {
        return $this->encryptedAttributes;
    }


    // Remove the booted method that generated RSA keys
    /*
    protected static function booted()
    {
        static::created(function ($pasien) {
            // This closure runs *after* a new Pasien record is created and saved.
            $encryptionService = app(EncryptionService::class); // Resolve the service from the container
            Log::info("Generating RSA keys for new patient ID: " . $pasien->id);
            if (!$encryptionService->generateAndStorePatientKeys($pasien)) {
                // Log an error if key generation fails.
                // Consider how to handle this failure (e.g., notify admin, retry logic?).
                // For now, we just log it. The patient record still exists.
                Log::error("!!! Failed to generate and store RSA keys for patient ID: " . $pasien->id);
            } else {
                 Log::info("Successfully generated RSA keys for patient ID: " . $pasien->id);
            }
        });
    }
    */
    public function getGeneralUncent()
    {
        $generalUncent = $this->attributes['general_uncent']?? null;
        // it is on http://localhost:8000/images/pasien/{generalUncent} modify it then
        // get env APP_URL
        $appUrl = env('APP_URL');
        $finalUrl = $appUrl . '/images/pasien/' . $generalUncent;
        return $finalUrl;
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
        $encryptedKey = $this->attributes[$key . '_key'] ?? null;

        if (!empty($encryptedValue) && !empty($encryptedKey)) {
            try {
                $encryptionService = app(EncryptionService::class);
                $decrypted = $encryptionService->decryptData($encryptedValue, $encryptedKey);
                // Handle potential null return on decryption error from service
                return $decrypted;
            } catch (\Exception $e) {
                Log::error("Decryption failed for Pasien ID {$this->id}, attribute '{$key}': " . $e->getMessage());
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
            $this->attributes[$key . '_key'] = null;
            // Optionally clear the original attribute if it exists
            // $this->attributes[$key] = null;
        } else {
            try {
                $encryptionService = app(EncryptionService::class);
                // Ensure data is string for encryption service
                $encryptedResult = $encryptionService->encryptData((string) $value);

                if ($encryptedResult) {
                    $this->attributes[$key . '_encrypted'] = $encryptedResult['encrypted_data'];
                    $this->attributes[$key . '_key'] = $encryptedResult['encrypted_aes_key'];
                    // Optionally clear the original attribute if it exists
                    // $this->attributes[$key] = null;
                } else {
                    // Handle encryption failure
                    Log::error("Encryption failed for Pasien ID {$this->id}, attribute '{$key}'. Value not set.");
                    // Decide how to handle: throw exception, set null, log?
                    // Setting null might be safest to prevent saving partial/incorrect state.
                    $this->attributes[$key . '_encrypted'] = null;
                    $this->attributes[$key . '_key'] = null;
                }
            } catch (\Exception $e) {
                 Log::error("Encryption exception for Pasien ID {$this->id}, attribute '{$key}': " . $e->getMessage());
                 $this->attributes[$key . '_encrypted'] = null;
                 $this->attributes[$key . '_key'] = null;
            }
        }
    }


    // Example relationship (if you don't have it already)
    public function rekams()
    {
        return $this->hasMany(Rekam::class, 'pasien_id');
    }
    
    // Add user relation
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    function rekamGigi(){
       return RekamGigi::where('pasien_id',$this->id)->get();
    }


    function isRekamGigi(){
        return RekamGigi::where('pasien_id',$this->id)->get()->count() > 0 ? true : false;
     }
     public function files()
     {
         return $this->hasMany(\App\Models\PasienFile::class, 'pasien_id');
     }

     // This method might be affected if created_at needs encryption (usually not needed)
     // Or if status relies on encrypted fields from Rekam model (if that's also encrypted)
     function statusPasien(){
         // ... (logic likely okay if it only uses non-encrypted fields like id/created_at) ...
         // ... Be careful if Rekam model fields used here are also encrypted ...
         $lastData = Carbon::createFromFormat('Y-m-d H:i:s', '2023-05-22 18:00:00');

         // Assuming Rekam status is NOT encrypted
         $rekam= Rekam::where('pasien_id',$this->id)
                  ->whereIn('status',[4,5])
                  ->count();
         if($rekam >0){
            if($this->created_at > $lastData){
               return ' <span class="badge badge-outline-primary">
                              <i class="fa fa-circle text-primary mr-1"></i>
                              Sudah Periksa
                        </span>';
            }else{
               return ' <span class="badge badge-outline-success">
                              <i class="fa fa-circle text-success mr-1"></i>
                              Sudah Periksa
                        </span>';
            }
         }else{
            if($this->created_at > $lastData){
               return ' <span class="badge badge-outline-primary">
                              <i class="fa fa-circle text-primary mr-1"></i>
                              Pasien Baru
                        </span>';
            }else{
               return ' <span class="badge badge-outline-danger">
                              <i class="fa fa-circle text-danger mr-1"></i>
                              Pasien Lama
                        </span>';
            }
         }
     }
}
