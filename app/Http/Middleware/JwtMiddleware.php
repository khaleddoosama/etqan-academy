<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


class JwtMiddleware
{
    use ApiResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $expireTime = Carbon::now()->addSeconds(60);
            Cache::put('user-is-online' . $user->id, true, $expireTime);
            $user->update(['last_login' => Carbon::now()]);

        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                // return response()->json(['status' => 'Token is Invalid']);
                return $this->apiResponse(null, __('messages.token_invalid'), 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                // return response()->json(['status' => 'Token is Expired']);
                return $this->apiResponse(null, __('messages.token_expired'), 401);
            } else {
                // return response()->json(['status' => 'Authorization Token not found']);
                return $this->apiResponse(null, __('messages.token_not_found'), 401);
            }
        }
        return $next($request);
    }
}
