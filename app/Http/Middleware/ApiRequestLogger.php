<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApiRequestLogger
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        try {
            $response = $next($request);
        } catch (Throwable $exception) {
            Log::channel('error_daily')->error('api.exception', [
                'method' => $request->method(),
                'path' => $request->path(),
                'ip' => $request->ip(),
                'user_id' => optional($request->user())->id,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        $status = $response->getStatusCode();
        $context = [
            'method' => $request->method(),
            'path' => $request->path(),
            'status' => $status,
            'ip' => $request->ip(),
            'duration_ms' => (int) ((microtime(true) - $start) * 1000),
            'user_id' => optional($request->user())->id,
            'request' => $this->sanitizePayload($request->all()),
        ];

        if ($status >= 400) {
            Log::channel('error_daily')->error('api.request', $context);
        } else {
            Log::channel('success_daily')->info('api.request', $context);
        }

        return $response;
    }

    private function sanitizePayload(array $payload): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'access_token', 'refresh_token'];

        foreach ($sensitiveKeys as $key) {
            if (array_key_exists($key, $payload)) {
                $payload[$key] = '***';
            }
        }

        return $payload;
    }
}
