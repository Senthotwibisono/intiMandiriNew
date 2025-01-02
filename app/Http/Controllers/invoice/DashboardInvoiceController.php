<?php

namespace App\Http\Controllers\invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\MasterTarif as MT;
use App\Models\InvoiceForm as Form;
use App\Models\InvoiceFormTarif as FormT;

use App\Models\Item;
use App\Models\Customer;
use App\Models\InvoiceHeader as Header;

use App\Models\Manifest;
use App\Models\Container;
use App\Models\YardDesign as YD;
use App\Models\YardDetil as RowTier;
use App\Models\PlacementManifest as PM;
use App\Models\RackTier as RT;
use App\Models\KapasitasGudang as KG;

class DashboardInvoiceController extends Controller
{
    public function dashboard()
    {
        $data['title'] = 'Dashboard Invoice';

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

        $terisi = $daily->sum('meas'); 
        $tidakTerisi = $data['kg'] - $terisi;
        $data['persentaseTerisi'] = ($terisi / $data['kg']) * 100;
        $data['persentaseTidakTerisi'] = ($tidakTerisi / $data['kg']) * 100;
        $data['tiers'] = RT::orderBy('tier', 'desc')->get();

        return view('invoice.dashboard', $data);
    }
}
