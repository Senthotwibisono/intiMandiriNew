<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;

use App\Models\Manifest;
use App\Models\Item;
use App\Models\Container;
use App\Models\ContainerFCL;
use App\Models\YardDesign as YD;
use App\Models\YardDetil as RowTier;
use App\Models\PlacementManifest as PM;
use App\Models\RackTier as RT;
use App\Models\KapasitasGudang as KG;

use App\Models\InvoiceHeader as Header;
use App\Models\FCL\InvoiceHeader as HeaderFCL;


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
        $daily = Manifest::whereHas('cont', function ($query) {
            $query->whereNotNull('tglmasuk');
        })->whereNull('tglrelease')->get();
    
        $data['tonase'] = $daily->sum('weight');
        $data['volume'] = $daily->sum('meas');
        $data['masukCont'] = Container::whereDate('tglmasuk', Carbon::now())->count();
        $data['masukManifest'] = Manifest::whereDate('tglmasuk', Carbon::now())->count();
        $data['keluarCont'] = Container::whereDate('tglkeluar', Carbon::now())->count();
        $data['keluarManifest'] = Manifest::whereDate('tglrelease', Carbon::now())->count();

        $data['kg'] = KG::sum('kapasitas');

        $terisi = $daily->sum('meas'); 
        $tidakTerisi = $data['kg'] - $terisi;
        $data['persentaseTerisi'] = ($terisi / $data['kg']) * 100;
        $data['persentaseTidakTerisi'] = ($tidakTerisi / $data['kg']) * 100;
        $data['tiers'] = RT::orderBy('tier', 'desc')->get();

        $data['manifests'] = Manifest::whereNull('tglrelease')->get();
        $data['barangs'] = Item::whereIn('manifest_id', $data['manifests']->pluck('id'))->get();
        // dd($data['barangs']);

        // dd($data['tonase'], $data['volume']);

        return view('home', $data);
    }

    public function indexInvoiceLCL()
    {
        $data['title'] = 'Dashboard';

        $data['lunas'] = Header::where('status', 'Y')->sum('grand_total');
        $data['piutang'] = Header::where('status', 'P')->sum('grand_total');
        $data['cancel'] = Header::where('status', 'C')->sum('grand_total');

        return view('dashboard.invoiceLCL', $data);
    }

    public function indexInvoiceLFCL()
    {
        $data['title'] = 'Dashboard';

        $data['lunas'] = HeaderFCL::where('status', 'Y')->sum('grand_total');
        $data['piutang'] = HeaderFCL::where('status', 'P')->sum('grand_total');
        $data['cancel'] = HeaderFCL::where('status', 'C')->sum('grand_total');

        return view('dashboard.invoiceFCL', $data);
    }


    public function indexFCL()
    {
        $data['title'] = 'Dashboard';

        $data['belumMasuk'] = ContainerFCL::whereNull('tglmasuk')->count();
        $data['storage'] = ContainerFCL::whereNotNull('tglmasuk')->whereNull('tglkeluar')->count();
        $data['exdepo'] = ContainerFCL::whereNotNull('tglkeluar')->count();

        return view('dashboard.indexFCL', $data);
    }
}
