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
        if ($via == 'Web' && method_exists($response, 'exception') && $response->exception) {
            $statusMessage = '<span class="badge badge-danger">Failed</span>';
            $error_message = $response->exception->getMessage();
        } elseif ($via == 'API' && $statusCode >= 400) {
            $statusMessage = '<span class="badge badge-danger">Failed</span>';
            $error_message = $response->original['message'];
        }

        $ip = $request->ip();

        $locationData = Location::get($ip);
        $geoLocation = $locationData ? [
            'country' => $locationData->countryName,
            'region' => $locationData->regionName,
            'city' => $locationData->cityName,
            'latitude' => $locationData->latitude,
            'longitude' => $locationData->longitude,
        ] : null;

        $url = $request->url();
        $parsedUrl = parse_url($url, PHP_URL_PATH);

        foreach ($guards as $guard) {
            $user = Auth::guard($guard)->check() ? Auth::guard($guard)->user() : null;

            activity()
                ->causedBy($user)
                ->withProperties([
                    'ip' => $ip,
                    'url' => $parsedUrl,
                    'user_agent' => $request->userAgent(),
                    'input' => $request->all(),
                    'method' => $request->method(),
                    'status_code' => $statusCode,
                    // 'session_id' => Auth::guard('web')->check() ? $request->session()->getId() : null,
                    'duration' => microtime(true) - LARAVEL_START . ' Seconds',
                    'route_name' => $request->route()->getName(),
                    'action' => $request->route()->getActionName(),
                    'geo_location' => $geoLocation,
                    'referrer_domain' => parse_url($request->server('HTTP_REFERER'), PHP_URL_HOST) ?? 'Direct Access',
                    'via' => $via,
                    'error_message' => $error_message,
                ])
                ->log("User " . ($user ? $user->name : 'Guest') . " Visit {$statusMessage} - {$via} - {$parsedUrl}");
            // }
        }

        return $response;
    }
}
