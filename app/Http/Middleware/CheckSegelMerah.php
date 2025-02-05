<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSegelMerah
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $container = $request->route('tcontainer_fcl'); // Ambil model dari route parameter

        if ($container && $container->flag_segel_merah === 'Y' && !auth()->user()->hasRole('bcP2')) {
            return response()->json(['error' => 'Anda tidak memiliki izin untuk mengubah data ini.'], 403);
        }

        return $next($request);
    }
}
