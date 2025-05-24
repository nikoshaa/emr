<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasienFile extends Model
{
    protected $table = 'pasien_files';
    protected $fillable = ['pasien_id', 'file_path', 'original_name'];

    public function pasien()
    {
        return $this->belongsTo(\App\Models\Pasien::class, 'pasien_id');
    }
}