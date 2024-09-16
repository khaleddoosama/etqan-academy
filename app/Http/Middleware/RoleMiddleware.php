<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        $user = $request->user();
        if ($user->role !== $role) {
            return redirect('dashboard');
        }

        $expireTime = Carbon::now()->addSeconds(30);
        Cache::put('user-is-online' . $user->id, true, $expireTime);
        $user->update(['last_login' => Carbon::now()]);


        return $next($request);
    }
}
