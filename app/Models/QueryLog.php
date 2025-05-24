<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryLog extends Model
{
    use HasFactory;

    protected $table = 'query_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'connection_name',
        'sql_query',
        'bindings',
        'execution_time_ms',
        'user_id',
        'url',
        'ip_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bindings' => 'array', // Automatically cast JSON bindings to array and vice-versa
        'execution_time_ms' => 'float',
    ];

    // Optional: Define relationship to User model
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}