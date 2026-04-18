<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIfAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            // Redirect to login page with a custom message
            return redirect()->route('login')->with('veuillez vous connecter');
        }

        // Continue to the next request if authenticated
        return $next($request);
    }
}
