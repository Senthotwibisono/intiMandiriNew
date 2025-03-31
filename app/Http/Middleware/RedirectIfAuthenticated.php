<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Periksa apakah pengguna memiliki peran 'bc'
                if ($user->hasRole('bc')) {
                    return redirect()->route('bc.dashboard');
                }

                if ($user->hasRole('android')) {
                    return redirect()->route('android.dashboard');
                }

                if ($user->hasRole('lapangan')) {
                    return redirect()->route('android.dashboard');
                }

                if ($user->hasRole('invoiceLCL')) {
                    return redirect()->route('dashboard.invoiceLCL');
                }
                if ($user->hasRole('invoiceFCL')) {
                    return redirect()->route('dashboard.invoiceFCL');
                }

                if ($user->hasRole(['adminFCL', 'tpsFCL'])) {
                    return redirect()->route('dashboard.fcl');
                }
                

                // Arahkan ke halaman home default jika pengguna tidak memiliki peran 'bc'
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
