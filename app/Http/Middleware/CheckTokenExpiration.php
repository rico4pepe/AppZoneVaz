<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;


class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->user()->currentAccessToken();

        if ($token && Carbon::parse($token->created_at)->addHours(24)->isPast()) {
            $token->delete(); // Delete expired token
            return response()->json(['message' => 'Session expired. Please log in again.'], 401);
        }
        return $next($request);
    }
}
