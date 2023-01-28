<?php

namespace App\Http\Middleware;

use App\JWTRS256\VerifyToken;
use Closure;
use Illuminate\Http\Request;

class LevelCek
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
       if(! VerifyToken::AuthCheck()->admin)
       {
            return response()->json([
                'message' => 'You do not have Administrator access'
            ], 400);
       }

       return $next($request);

    }
}
