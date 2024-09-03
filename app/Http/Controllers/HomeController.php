<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Manifest;
use App\Models\Container;
use App\Models\YardDesign as YD;
use App\Models\YardDetil as RowTier;
use App\Models\PlacementManifest as PM;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['title'] = "Dashboard";
        $data['recapManifest'] = Manifest::count();
        $data['recapContainer'] = Container::count();
        $data['yard'] = YD::with('yardDetils')
        ->get()
        ->map(function ($yard) {
            $total = $yard->yardDetils->count();
            $filled = $yard->yardDetils->whereNotNull('cont_id')->count();

            // Calculate percentage filled
            $yard->percentage_filled = $total > 0 ? round(($filled / $total) * 100, 2) : 0;

            return $yard;
        });

        $data['gudang'] = PM::orderBy('nomor', 'asc')->get();

        return view('home', $data);
    }
}
