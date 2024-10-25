<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Location\Facades\Location;

class LogUserActivity
{

    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;
        $via =  request()->is('api/*') ? 'API' : 'Web';

        $response = $next($request);

        $statusCode = $response->getStatusCode();
        $statusMessage = '<span class="badge badge-success">Success</span>';
        $error_message = "";
        if ($via == 'Web' && $response->exception) {
            $statusMessage = '<span class="badge badge-danger">Failed</span>';
            $error_message = $response->exception->getMessage();
        } elseif ($via == 'API' && $statusCode >= 400) {
            $statusMessage = '<span class="badge badge-danger">Failed</span>';
            $error_message = $response->original['message'];
        }

        foreach ($guards as $guard) {
            $user = Auth::guard($guard)->check() ? Auth::guard($guard)->user() : null;

            $ip = $request->ip();
            activity()
                ->causedBy($user)
                ->withProperties([
                    'ip' => $ip,
                    'url' => $request->fullUrl(),
                    'user_agent' => $request->userAgent(),
                    'input' => $request->all(),
                    'method' => $request->method(),
                    'status_code' => $statusCode,
                    // 'session_id' => Auth::guard('web')->check() ? $request->session()->getId() : null,
                    'duration' => microtime(true) - LARAVEL_START . ' Seconds',
                    'route_name' => $request->route()->getName(),
                    'action' => $request->route()->getActionName(),
                    'geo_location' => Location::get($ip), // Requires a suitable package like "torann/geoip"
                    'referrer_domain' => parse_url($request->server('HTTP_REFERER'), PHP_URL_HOST) ?? 'Direct Access',
                    'via' => $via,
                    'error_message' => $error_message,
                ])
                ->log("User Visit {$statusMessage} - {$via} - {$request->fullUrl()}");
            // }
        }

        return $response;
    }
}
