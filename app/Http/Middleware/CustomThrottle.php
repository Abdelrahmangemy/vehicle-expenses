<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomThrottle
{
    public function __construct(
        private RateLimiter $limiter
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $key = 'vehicle-expenses:' . $request->ip();

        if ($this->limiter->tooManyAttempts($key, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again in ' .
                    $this->limiter->availableIn($key) . ' seconds.',
            ], 429);
        }

        $this->limiter->hit($key, 60);

        return $next($request);
    }
}
