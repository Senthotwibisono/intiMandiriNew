<?php

namespace App\Http\Controllers\android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Container as Cont;
use App\Models\Manifest;
use App\Models\YardDesign as YD;
use App\Models\YardDetil as RowTier;
use App\Models\Item;
use App\Models\PlacementManifest as PM;
use App\Models\RackingDetil as Rack;
use App\Models\KeteranganPhoto as KP;

class LclController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:android, lapangan');
    }

    public function indexStripping()
    {
        $data['title'] = 'LCL || Stripping';
        $data['conts'] = Cont::where('type', '=', 'lcl')->whereNot('tglmasuk', null)->where('tglkeluar', null )->orderBy('endstripping', 'asc')->get();
        
        $data['kets'] = KP::where('kegiatan', '=', 'stripping')->get();
        return view('android.lcl.stripping', $data);
    }
    public function indexStrippingManifest()
    {
        $data['title'] = 'LCL || Stripping Mnifest';
        $data['mans'] = Manifest::whereNot('tglmasuk', null)->where('tglbuangmty', null )->orderBy('endstripping', 'asc')->get();
        $data['kets'] = KP::where('kegiatan', '=', 'stripping')->get();

        return view('android.lcl.strippingManifest', $data);
    }

    public function searchCont($id)
    {
        // var_dump($id);
        // die;
        $cont = Cont::find($id);
        if ($cont) {
            return response()->json([
                'data' => $cont,
                'message' => 'Data Ditemukan',
                'success' => true,
            ]);
        }
    }

    public function plcamenContIndex()
    {
        $data['title'] = 'LCL || Placement Container';
        $data['conts'] = Cont::where('type', '=', 'lcl')->whereNot('tglmasuk', null)->where('tglkeluar', null )->orderBy('endstripping', 'asc')->get();

        $data['yards'] = YD::whereNot('yard_block', null)->get();
        $data['yardDetils'] = RowTier::get();
        $data['kets'] = KP::where('kegiatan', '=', 'placement')->get();

        return view('android.lcl.placementCont', $data);
    }

    public function rackingIndex()
    {
        $data['title'] = 'Racking Index';

        return view('android.lcl.racking.index', $data);
    }

    public function rackingDetil($qr)
    {
        $scanItem = Item::with('manifest')->where('barcode', $qr)->first();
        $manifest = $scanItem->manifest;
        if ($manifest->tglstripping == null) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Mnifest Belum Stripping']);
        }
        $item = Item::where('manifest_id', $manifest->id)->get();
        $data['title'] = "Racking Manifest || " . $manifest->notally . "Scan in Item Number: " . $scanItem->nomor;
        $data['locs'] = PM::whereNot('use_for', 'B')->get();
        $data['manifest'] = $manifest;
        $data['placed'] = Item::where('manifest_id', $manifest->id)->whereNot('lokasi_id', null)->get();
        $data['item'] = Item::where('manifest_id', $manifest->id)->where('lokasi_id', null)->get();
        $data['kets'] = KP::where('kegiatan', '=', 'palcement')->get();

        return view('android.lcl.racking.detil', $data);
    }

    public function behandleIndex()
    {
        $data['title'] = 'Behandle Index';

        return view('android.lcl.behandle.index', $data);
    }

    public function behandleDetil($qr)
    {
        $scanItem = Item::with('manifest')->where('barcode', $qr)->first();
        $manifest = $scanItem->manifest;
        
        $data['title'] = "Behandle Manifest || " . $manifest->nohbl;
        $data['manifest'] = $manifest;
        $data['locs'] = PM::where('use_for', 'B')->get();
        $data['kets'] = KP::where('kegiatan', '=', 'behandle')->get();

        return view('android.lcl.behandle.detil', $data);
    }

    public function photoCont()
    {
        $data['title'] = 'Photo Container';

        $data['conts'] = Cont::orderBy('id', 'asc')->get();

        return view('android.photoCont', $data);
    }

    public function photoManifest()
    {
        $data['title'] = 'Photo Manifest';

        return view('android.photoManifestIndex', $data);
    }

    public function photoManifestDetil($qr)
    {
        $scanItem = Item::with('manifest')->where('barcode', $qr)->first();
        $manifest = $scanItem->manifest;
        
        $data['title'] = "Photo Manifest || " . $manifest->nohbl;
        $data['manifest'] = $manifest;

        return view('android.photoManifestDetil', $data);
    }
}
