<?php

namespace App\Http\Controllers\android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;


class AndroidHomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:android, lapangan');
    }

    public function indexDashboard()
    {
        $data['title'] = 'Dashboard';

        return view('android.dashboard', $data);
    }
}
