<?php

namespace App\Http\Middleware;

use App\ApiToken;
use Closure;
use Illuminate\Http\Request;

class CheckToken
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
        $token = $request->offsetGet('token');

        /** @noinspection PhpUndefinedMethodInspection */
        $message = !$token ? 'Empty token given'
            : (strlen($token) < 80 ? 'Invalid token given'
                : (!ApiToken::where('api_token', $token)->first() ? 'Token not found'
                    : null));

        return
            $message ? response()->json(['message' => $message])
                : $next($request);
    }
}
