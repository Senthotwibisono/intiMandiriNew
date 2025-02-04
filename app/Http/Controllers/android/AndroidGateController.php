<?php

namespace App\Http\Controllers\android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Container as Cont;
use App\Models\ContainerFCL as ContF;
use App\Models\KeteranganPhoto as KP;
use App\Models\Photo;
use App\Models\BarcodeGate;

class AndroidGateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:android, lapangan');
    }

    public function index()
    {
        $data['title'] = 'Gate Android';

        return view('android.gate.index', $data);
    }

    public function recivingBarcode($qr)
    {
        try {
            $barcode = BarcodeGate::where('barcode', $qr)->first();
            switch ($barcode->type) {
                case 'FCL':
                    return redirect('')->with('status', ['type'=>'success', 'message'=>'Data Ditemukan']);
                    break;
                case 'LCL';
                    return redirect('')->with('status', ['type'=>'success', 'message'=>'Data Ditemukan']);
                    break;
                default:
                    # code...
                    break;
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Data tidak ditemukan']);
        }
    }

    public function indexGateFCL($id, $qr)
    {
        $cont = ContF::find($id);
        $barcode = BarcodeGate::where('barcode', $qr)->first();

        $data['kets'] = KP::where('kegiatan', '=', 'behandle')->get();
    }
}
