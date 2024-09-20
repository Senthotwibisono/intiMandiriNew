<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;

use App\Models\Manifest;
use App\Models\Container;
use App\Models\YardDesign as YD;
use App\Models\YardDetil as RowTier;
use App\Models\PlacementManifest as PM;
use App\Models\KapasitasGudang as KG;


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

        $data['now'] = Carbon::now()->translatedFormat('l, d F Y H:i:s');
        $data['gudang'] = PM::orderBy('nomor', 'asc')->get();

        // Daily Recap
        $daily = Manifest::whereNot('tglmasuk', null)->where('tglrelease', null)->get();
        $data['tonase'] = $daily->sum('weight');
        $data['volume'] = $daily->sum('meas');
        $data['masukCont'] = Container::whereDate('tglmasuk', Carbon::now())->count();
        $data['masukManifest'] = Manifest::whereDate('tglmasuk', Carbon::now())->count();
        $data['keluarCont'] = Container::whereDate('tglkeluar', Carbon::now())->count();
        $data['keluarManifest'] = Manifest::whereDate('tglrelease', Carbon::now())->count();

        $data['kg'] = KG::sum('kapasitas');

        $terisi = $daily->count(); 
        $tidakTerisi = $data['kg'] - $terisi;
        $data['persentaseTerisi'] = ($terisi / $data['kg']) * 100;
        $data['persentaseTidakTerisi'] = ($tidakTerisi / $data['kg']) * 100;

        // dd($data['tonase'], $data['volume']);

        return view('home', $data);
    }
}
