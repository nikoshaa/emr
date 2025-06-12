<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\EncryptionService;


class Rekam extends Model
{
    protected $table = "rekam";
    // Add 'is_decrypted' to fillable
    protected $fillable = ["tgl_rekam","pasien_id","poli","dokter_id", "apoteker_id",
    "no_rekam","status","petugas_id","biaya_pemeriksaan","biaya_tindakan",
    "biaya_obat","total_biaya","cara_bayar","resep_obat",
    "keluhan_file", "pemeriksaan_file", "diagnosa_file", "tindakan_file",
    'is_decrypted' // Add this line
    ];

    function getFileKeluhan(){
        return $this->keluhan_file != null ? asset('images/keluhan/'.$this->keluhan_file) : null;
    }

    function getFilePemeriksaan(){
        return $this->pemeriksaan_file != null ? asset('images/pemeriksaan/'.$this->pemeriksaan_file) : null;
    }

    function getFileDiagnosa(){
        return $this->diagnosa_file != null ? asset('images/diagnosa/'.$this->diagnosa_file) : null;
    }

    function getFileTindakan(){
        return $this->tindakan_file != null ? asset('images/tindakan/'.$this->tindakan_file) : null;
    }

    function gigi(){
      return  RekamGigi::where('rekam_id',$this->id)->get();
    }

    function diagnosa(){
        return  RekamDiagnosa::where('rekam_id',$this->id)->get();
      }

    function pasien(){
        return $this->belongsTo(Pasien::class);
    }
    private function getEncryptionService(): EncryptionService
    {
        return app(EncryptionService::class);
    }

    // Helper function to get the associated Pasien model, ensuring it's loaded
    private function getAssociatedPasien(): ?Pasien
    {
        $pasien = null;
        // Eager load if not already loaded, or find if only ID is present
        if (!$this->relationLoaded('pasien') && $this->pasien_id) {
             Log::debug("Rekam ID {$this->id}: Pasien relationship not loaded, attempting to load."); // Add Log
             $this->load('pasien'); // Load the relationship
             // As a fallback if load fails or pasien_id was set but model not saved yet
             if (!$this->relationLoaded('pasien')) {
                 Log::warning("Rekam ID {$this->id}: Failed to load pasien relationship, finding by ID {$this->pasien_id}."); // Add Log
                 $pasien = Pasien::find($this->pasien_id);
             } else {
                 $pasien = $this->pasien;
                 Log::debug("Rekam ID {$this->id}: Pasien relationship loaded successfully."); // Add Log
             }
        } else if ($this->relationLoaded('pasien')) {
            $pasien = $this->pasien;
            Log::debug("Rekam ID {$this->id}: Pasien relationship already loaded."); // Add Log
        } else {
            Log::warning("Rekam ID {$this->id}: Pasien relationship not loaded and no pasien_id set."); // Add Log
        }

        if (!$pasien) {
            Log::error("Rekam ID {$this->id}: Could not retrieve associated Pasien."); // Add Log
        }

        return $pasien; // Return the loaded relationship or null
    }


    // -- Keluhan --
    public function getKeluhanAttribute($value)
    {
        // Prefer encrypted value if available
        if (!empty($this->encrypted_keluhan) && !empty($this->encrypted_keluhan_aes_key)) {
            $pasien = $this->getAssociatedPasien();
            if (!$pasien) {
                Log::error("Rekam ID {$this->id}: Cannot decrypt keluhan, Pasien not found or associated.");
                return null; // Or return placeholder text like "[Decryption Error]"
            }
            $decrypted = $this->getEncryptionService()->decryptData(
                $this->encrypted_keluhan,
                $this->encrypted_keluhan_aes_key,
                $pasien
            );
            if ($decrypted === null) {
                 Log::error("Rekam ID {$this->id}: Failed to decrypt keluhan.");
                 return "[Decryption Failed]"; // Indicate failure
            }
            return $decrypted;
        }
        // Fallback to original value if encryption hasn't happened yet (for old data)
        // You might want to remove this fallback after data migration
        return $value;
    }

    public function setKeluhanAttribute($value)
    {
        if (Str::of($value)->isEmpty()) {
             // Handle empty input: clear encrypted fields
             $this->attributes['encrypted_keluhan'] = null;
             $this->attributes['encrypted_keluhan_aes_key'] = null;
             // $this->attributes['keluhan'] = null; // Remove this line
             return;
        }

        $pasien = $this->getAssociatedPasien();
        if (!$pasien) {
            Log::error("Rekam ID {$this->id}: Cannot encrypt keluhan, Pasien not found or associated.");
            // Decide how to handle: throw exception, set null, log and skip?
            // Setting null might be safest to avoid saving partial data.
            $this->attributes['encrypted_keluhan'] = null;
            $this->attributes['encrypted_keluhan_aes_key'] = null;
            return; // Stop encryption
        }

        $encrypted = $this->getEncryptionService()->encryptData($value, $pasien);

        if ($encrypted) {
            $this->attributes['encrypted_keluhan'] = $encrypted['encrypted_data'];
            $this->attributes['encrypted_keluhan_aes_key'] = $encrypted['encrypted_aes_key'];
            // $this->attributes['keluhan'] = null; // Remove this line
        } else {
             Log::error("Rekam ID {$this->id}: Failed to encrypt keluhan.");
             // Handle encryption failure (e.g., log, maybe set original field as fallback?)
             // Setting null might be safest.
             $this->attributes['encrypted_keluhan'] = null;
             $this->attributes['encrypted_keluhan_aes_key'] = null;
        }
    }

    // -- Pemeriksaan -- (Repeat pattern for other fields)
    public function getPemeriksaanAttribute($value)
    {
        if (!empty($this->encrypted_pemeriksaan) && !empty($this->encrypted_pemeriksaan_aes_key)) {
            $pasien = $this->getAssociatedPasien();
             if (!$pasien) {
                Log::error("Rekam ID {$this->id}: Cannot decrypt pemeriksaan, Pasien not found or associated.");
                return null;
            }
            $decrypted = $this->getEncryptionService()->decryptData(
                $this->encrypted_pemeriksaan,
                $this->encrypted_pemeriksaan_aes_key,
                $pasien
            );
             if ($decrypted === null) {
                 Log::error("Rekam ID {$this->id}: Failed to decrypt pemeriksaan.");
                 return "[Decryption Failed]";
             }
             return $decrypted;
        }
        return $value; // Fallback
    }

    public function setPemeriksaanAttribute($value)
    {
         if (Str::of($value)->isEmpty()) {
             $this->attributes['encrypted_pemeriksaan'] = null;
             $this->attributes['encrypted_pemeriksaan_aes_key'] = null;
             // $this->attributes['pemeriksaan'] = null; // Remove this line
             return;
        }
        $pasien = $this->getAssociatedPasien();
         if (!$pasien) {
            Log::error("Rekam ID {$this->id}: Cannot encrypt pemeriksaan, Pasien not found or associated. Setting encrypted fields to null."); // Enhanced Log
            $this->attributes['encrypted_pemeriksaan'] = null;
            $this->attributes['encrypted_pemeriksaan_aes_key'] = null;
            return;
        }
        $encrypted = $this->getEncryptionService()->encryptData($value, $pasien);
        if ($encrypted) {
            $this->attributes['encrypted_pemeriksaan'] = $encrypted['encrypted_data'];
            $this->attributes['encrypted_pemeriksaan_aes_key'] = $encrypted['encrypted_aes_key'];
            // $this->attributes['pemeriksaan'] = null; // Remove this line
        } else {
             Log::error("Rekam ID {$this->id}: Failed to encrypt pemeriksaan. Setting encrypted fields to null."); // Enhanced Log
             $this->attributes['encrypted_pemeriksaan'] = null; // Ensure null on failure
             $this->attributes['encrypted_pemeriksaan_aes_key'] = null; // Ensure null on failure
        }
    }

    // -- Diagnosa --
     public function getDiagnosaAttribute($value)
    {
        if (!empty($this->encrypted_diagnosa) && !empty($this->encrypted_diagnosa_aes_key)) {
            $pasien = $this->getAssociatedPasien();
             if (!$pasien) {
                Log::error("Rekam ID {$this->id}: Cannot decrypt diagnosa, Pasien not found or associated.");
                return null;
            }
            $decrypted = $this->getEncryptionService()->decryptData(
                $this->encrypted_diagnosa,
                $this->encrypted_diagnosa_aes_key,
                $pasien
            );
             if ($decrypted === null) {
                 Log::error("Rekam ID {$this->id}: Failed to decrypt diagnosa.");
                 return "[Decryption Failed]";
             }
             return $decrypted;
        }
        return $value; // Fallback
    }

    public function setDiagnosaAttribute($value)
    {
         if (Str::of($value)->isEmpty()) {
             $this->attributes['encrypted_diagnosa'] = null;
             $this->attributes['encrypted_diagnosa_aes_key'] = null;
             // $this->attributes['diagnosa'] = null; // Remove this line
             return;
        }
        $pasien = $this->getAssociatedPasien();
         if (!$pasien) {
            Log::error("Rekam ID {$this->id}: Cannot encrypt diagnosa, Pasien not found or associated.");
            $this->attributes['encrypted_diagnosa'] = null;
            $this->attributes['encrypted_diagnosa_aes_key'] = null;
            return;
        }
        $encrypted = $this->getEncryptionService()->encryptData($value, $pasien);
        if ($encrypted) {
            $this->attributes['encrypted_diagnosa'] = $encrypted['encrypted_data'];
            $this->attributes['encrypted_diagnosa_aes_key'] = $encrypted['encrypted_aes_key'];
            // $this->attributes['diagnosa'] = null; // Remove this line
        } else {
            // ... error handling ...
        }
    }

    // -- Tindakan --
     public function getTindakanAttribute($value)
    {
        if (!empty($this->encrypted_tindakan) && !empty($this->encrypted_tindakan_aes_key)) {
            $pasien = $this->getAssociatedPasien();
             if (!$pasien) {
                Log::error("Rekam ID {$this->id}: Cannot decrypt tindakan, Pasien not found or associated.");
                return null;
            }
            $decrypted = $this->getEncryptionService()->decryptData(
                $this->encrypted_tindakan,
                $this->encrypted_tindakan_aes_key,
                $pasien
            );
             if ($decrypted === null) {
                 Log::error("Rekam ID {$this->id}: Failed to decrypt tindakan.");
                 return "[Decryption Failed]";
             }
             return $decrypted;
        }
        return $value; // Fallback
    }

    public function setTindakanAttribute($value)
    {
         if (Str::of($value)->isEmpty()) {
             $this->attributes['encrypted_tindakan'] = null;
             $this->attributes['encrypted_tindakan_aes_key'] = null;
             // $this->attributes['tindakan'] = null; // Remove this line
             return;
        }
        $pasien = $this->getAssociatedPasien();
         if (!$pasien) {
            Log::error("Rekam ID {$this->id}: Cannot encrypt tindakan, Pasien not found or associated.");
            $this->attributes['encrypted_tindakan'] = null;
            $this->attributes['encrypted_tindakan_aes_key'] = null;
            return;
        }
        $encrypted = $this->getEncryptionService()->encryptData($value, $pasien);
        if ($encrypted) {
            $this->attributes['encrypted_tindakan'] = $encrypted['encrypted_data'];
            $this->attributes['encrypted_tindakan_aes_key'] = $encrypted['encrypted_aes_key'];
            // $this->attributes['tindakan'] = null; // Remove this line
        } else {
            // ... error handling ...
        }
    }

    // function diagnosis(){
    //     return $this->belongsTo(Icd::class,'diagnosa','code');
    // }

    function dokter(){
        return $this->belongsTo(Dokter::class);
    }
    function status_rekams(){
        switch ($this->status) {
            case 1:
                return "<span class='badge badge-rounded badge-danger'>Belum Diperiksa</span>";
                break;
            case 2:
                return "<span class='badge badge-rounded badge-danger'>Belum Diperiksa</span>";
                break;
            case 3:
                return "<span class='badge badge-rounded badge-primary'>Sudah Diperiksa</span>";
                break;
            case 4:
                return "<span class='badge badge-rounded badge-primary'>Selesai</span>";
                break;
            case 5:
                return "<span class='badge badge-rounded badge-primary'>Selesai</span>";
                break;
            default:
                # code...
                break;
        }
    }

    function status_display(){
        switch ($this->status) {
            case 1:
                return '<span class="badge badge-outline-warning">
                            <i class="fa fa-circle text-warning mr-1"></i>
                             Antrian
                        </span>';
            break;
            case 2:
                return '<span class="badge badge-info light">
                            <i class="fa fa-circle text-info mr-1"></i>
                            Pemeriksaan
                        </span>';
            break;
            case 3:
                return '<span class="badge badge-warning light" style="width:100px">
                           Di Apotek
                        </span>';
            break;
            case 4:
                return '<span class="badge badge-danger light">
                            <i class="fa fa-circle text-danger mr-1"></i>
                            Pembayaran
                        </span>';
            break;
            case 5:
                return '<span class="badge badge-primary light" style="width:100px">
                            <i class="fa fa-check text-primary mr-1"></i>
                            Selesai
                        </span>';
            break;
            default:
                # code...
                break;
        }
    }
}
