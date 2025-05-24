<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request; // To get IP, URL etc. if not passed

class LogHelper
{
    public static function logAction(string $description, array $context = [])
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'ip_address' => Request::ip(),
                'url' => Request::fullUrl(), // Or a specific URL/route name from context
                'method' => Request::method(), // Or a custom action type
                'user_agent' => Request::userAgent(),
                'action_description' => $description,
                'request_body' => !empty($context) ? $context : null, // Store relevant context
                // Add other relevant fields like 'response_status_code' if applicable
            ]);
        } catch (\Exception $e) {
            // Log::error('Failed to log custom action to database: ' . $e->getMessage());
        }
    }
}