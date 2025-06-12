<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog; // Your Log model
use Illuminate\Support\Facades\Auth; // If you use authentication

class LogHttpRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        // Process the request
        $response = $next($request);

        $endTime = microtime(true);
        $duration = ($endTime - $startTime) * 1000; // Duration in milliseconds

        // Log the details
        try {
            ActivityLog::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'request_body' => $this->formatRequestBody($request),
                'response_status_code' => $response->getStatusCode(),
                // 'response_body' => $this->formatResponseBody($response), // Optional and potentially large
                'duration_ms' => (int) $duration,
            ]);
        } catch (\Exception $e) {
            // Optionally log the error to a file or another service if DB logging fails
            // Log::error('Failed to log HTTP request to database: ' . $e->getMessage());
        }

        return $response;
    }

    /**
     * Format the request body.
     * Exclude sensitive fields like 'password'.
     */
    protected function formatRequestBody(Request $request): ?array
    {
        $body = $request->all();
        // Remove sensitive information
        if (isset($body['password'])) {
            $body['password'] = '********';
        }
        if (isset($body['password_confirmation'])) {
            $body['password_confirmation'] = '********';
        }
        // Add other sensitive fields as needed
        return !empty($body) ? $body : null;
    }

    /**
     * Format the response body.
     * Be very careful with this, as responses can be large.
     * You might want to log only for certain routes or response types.
     */
    // protected function formatResponseBody($response): ?array
    // {
    //     if ($response instanceof \Illuminate\Http\JsonResponse) {
    //         $content = $response->getData(true);
    //         // Optionally, truncate or sample if too large
    //         return $content;
    //     }
    //     // For non-JSON responses, you might log a snippet or nothing
    //     return null;
    // }
}
