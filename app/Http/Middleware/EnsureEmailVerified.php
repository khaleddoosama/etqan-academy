<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && !$user->hasVerifiedEmail()) {
            return $this->apiResponse(null, __('messages.email_not_verified'), 403);
        }
        return $next($request);
    }
}
