<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiKey;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-API-KEY');

    if (!$key) {
        return response()->json(['message' => 'API key missing'], 401);
    }

    $hashedKey = hash('sha256', $key);

    $apiKey = ApiKey::where('key', $hashedKey)
                    ->where('is_active', true)
                    ->first();

    if (!$apiKey) {
        return response()->json(['message' => 'Invalid API key'], 401);
    }

    return $next($request);
    }
}
