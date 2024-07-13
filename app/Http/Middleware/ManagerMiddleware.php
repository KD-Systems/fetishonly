<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ManagerMiddleware
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
        if(!auth()->check()) {
            return redirect()->route('manager.login');
        }

        if((auth()->user()->identity_verified_at == null) || (!in_array(auth()->user()->role_id, [1,4]))) {
            abort(401, 'You are not authorized!');
        }

        return $next($request);
    }
}
