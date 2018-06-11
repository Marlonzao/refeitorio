<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role 
     * @return mixed
     */
    public function handle($request, Closure $next, ...$role)
    {

        $response = $next($request);
        if(!in_array(JWTAuth::parseToken()->authenticate()->role, $role))
            return new Response(null, 403);            
        return $response;
    }
}
