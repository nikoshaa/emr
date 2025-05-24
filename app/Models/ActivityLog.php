<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs'; // Explicitly define table name

    protected $fillable = [
        'user_id',
        'ip_address',
        'url',
        'method',
        'user_agent',
        'request_body',
        'response_status_code',
        // 'response_body', // Be cautious with this, can be very large
        'action_description', // For custom log messages
        'duration_ms', // To log request processing time
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'request_body' => 'array',
        // 'response_body' => 'array',
    ];

    // Optional: If you have a User model and want to link logs to users
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
