<?php

namespace App\Http\Middleware;

use Closure;
use App\Operater;
use App\Exceptions\UnauthorizedException;

class VerifyTokenHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->headers->has('Api-Token')) {
            throw new UnauthorizedException('No token provided.');
        }
        
        $users = Operater::withToken($request->headers->get('Api-Token'));
        
        if ($users->count() == 0) {
            throw new UnauthorizedException('No active user with provided token.');
        }
        
        return $next($request);        
    }
}
