<?php

namespace App\Http\Middleware;

use App\JWTRS256\VerifyToken;
use Closure;
use Exception;
use Illuminate\Http\Request;

class AuthJWT
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
        try{

            $user = VerifyToken::AuthCheck();

        }catch(Exception $e){

            return response()->json(['message' => $e->getMessage()], 412);

        }

        return $next($request);
    }
}
