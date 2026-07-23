<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BeaCukaiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $username = $request->header('X-Api-Username');
        $password = $request->header('X-Api-Password');
    
        if (!$username || !$password) {
            return response()->json([
                'error' => 'MISSING_CREDENTIALS',
                'message' => 'Username dan password wajib dikirim',
                'timestamp' => now()->format('d-m-Y H:i:s')
            ],400);
        }
    
        if (
            $username != config('beacukai.username') ||
            $password != config('beacukai.password')
        ){
            return response()->json([
                'error'=>'INVALID_CREDENTIALS',
                'message'=>'Username atau password tidak valid',
                'timestamp'=>now()->format('d-m-Y H:i:s')
            ],401);
        }
    
        return $next($request);
    }
}
