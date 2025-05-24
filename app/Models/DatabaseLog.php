<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseLog extends Model
{
    // table = database_logs_web
    protected $table = 'database_logs_web';
    protected $fillable = [
        'user_id',
        'sql',
        'bindings',
        'execution_time',
        'ip_address',
        'url',
        'method',
    ];
    
    // Relationship with User model if needed
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}