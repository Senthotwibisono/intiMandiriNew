<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected function redirectTo()
    {
        // Redirect based on user role
        if (Auth::check() && Auth::user()->hasRole('bc')) {
            return '/bc/dashboard';
        }
        if (Auth::check() && Auth::user()->hasRole('bcP2')) {
            return '/bc-p2/dashboard';
        }

        if (Auth::check() && (Auth::user()->hasRole('android') || Auth::user()->hasRole('lapangan'))) {
            return '/android/dashboard';
        }

        return '/home'; // Default redirect path
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
