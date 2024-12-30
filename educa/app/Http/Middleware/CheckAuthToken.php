<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuthToken;

class CheckAuthToken
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
        // Get the Authorization header
        $authorizationHeader = $request->header('Authorization');

        // Check if the header exists
        if (!$authorizationHeader) {
            return response()->json(['error' => 'Authorization token missing'], 401);
        }

        // Support both plain tokens and 'Bearer <token>' formats
        if (str_starts_with($authorizationHeader, 'Bearer ')) {
            $token = substr($authorizationHeader, 7); // Extract token after 'Bearer '
        } else {
            $token = $authorizationHeader; // Use as-is if no 'Bearer ' prefix
        }

        // Check if token exists in the database
        $authToken = AuthToken::where('token', $token)->first();

        if (!$authToken) {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }
        // Proceed to the next middleware or request handler
        return $next($request);
    }
}
