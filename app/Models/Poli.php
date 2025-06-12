<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Add this line

class Poli extends Model
{
    use HasFactory; // Add this line

    protected $table = "poli";
    protected $fillable = ["nama","status"];

    function status_display(){
        return $this->status ==1 ? 'Aktif' :'Tidak Aktif';
    }
}
