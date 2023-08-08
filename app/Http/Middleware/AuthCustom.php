<?php   

namespace App\Http\Middleware;

use Closure;
use illuminate\Http\Request;
use illuminate\Support\Facades\Auth;

class AuthCustom
{
    /**
     * Handle an incoming request.
     * 
     * @param \illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Admin check failed'], 401);
        }
        return $next($request);
    }
}