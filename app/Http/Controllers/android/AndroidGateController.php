<?php

namespace App\Http\Controllers\android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Container as Cont;
use App\Models\Manifest;
use App\Models\ContainerFCL as ContF;
use App\Models\KeteranganPhoto as KP;
use App\Models\Photo;
use App\Models\BarcodeGate;

class AndroidGateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:android, lapangan, gate');
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
            switch ($barcode->ref_type) {
                case 'FCL':
                    return redirect('/android/gate/fcl/'.$barcode->barcode)->with('status', ['type'=>'success', 'message'=>'Data Ditemukan']);
                    break;
                case 'LCL';
                    return redirect('/android/gate/lcl/'.$barcode->barcode)->with('status', ['type'=>'success', 'message'=>'Data Ditemukan']);
                    break;
                case 'Manifest';
                    return redirect('/android/gate/manifest/'.$barcode->barcode)->with('status', ['type'=>'success', 'message'=>'Data Ditemukan']);
                    break;
                default:
                    # code...
                    break;
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Data tidak ditemukan']);
        }
    }

    public function indexGateFCL($qr)
    {
        $data['title'] = 'Gate Inti Mandiri';
        $barcode = BarcodeGate::where('barcode', $qr)->first();
        $cont = ContF::find($barcode->ref_id);
        $data['kets'] = KP::where('tipe', 'container')->whereIn('kegiatan', ['gate-in', 'gate-out'])->get();

        $photoTake = Photo::where('type', 'FCL')->where('master_id', $cont->id)->get();
        if ($barcode->ref_action == 'get') {
            $data['kets'] = KP::where('tipe', 'container')->where('kegiatan', 'gate-in')->get();
            $photoTake = Photo::where('type', 'FCL')->where('master_id', $cont->id)->where('action', 'gate-in')->get();
        }else {
            $data['kets'] = KP::where('tipe', 'container')->where('kegiatan', 'gate-out')->get();
            $photoTake = Photo::where('type', 'FCL')->where('master_id', $cont->id)->where('action', 'gate-out')->get();
        }
        $data['take'] = $photoTake->pluck('detil');
        $data['cont'] = $cont;
        
        $data['barcode'] = $barcode;

        return view('android.gate.indexFCL', $data);
    }

    public function indexGateLCL($qr)
    {
        $data['title'] = 'Gate Inti Mandiri';
        $barcode = BarcodeGate::where('barcode', $qr)->first();
        $cont = Cont::find($barcode->ref_id);
        $data['kets'] = KP::where('tipe', 'container')->whereIn('kegiatan', ['gate-out', 'buang-mty'])->get();

        $photoTake = Photo::where('type', 'LCL')->where('master_id', $cont->id)->get();
        if ($barcode->ref_action == 'get') {
            $data['kets'] = KP::where('tipe', 'container')->where('kegiatan', 'gate-in')->get();
            $photoTake = Photo::where('type', 'LCL')->where('master_id', $cont->id)->where('action', 'gate-in')->get();
        }else {
            $data['kets'] = KP::where('tipe', 'container')->where('kegiatan', 'buang-mty')->get();
            $photoTake = Photo::where('type', 'LCL')->where('master_id', $cont->id)->where('action', 'buang-mty')->get();
        }
        $data['take'] = $photoTake->pluck('detil');
        $data['cont'] = $cont;
        
        $data['barcode'] = $barcode;

        return view('android.gate.indexLCL', $data);
    }

    public function indexGateManifest($qr)
    {
        $data['title'] = 'Gate Inti Mandiri';
        $barcode = BarcodeGate::where('barcode', $qr)->first();
        $manifest = Manifest::find($barcode->ref_id);
        $data['manifest'] = $manifest;
        
        $data['kets'] = KP::where('tipe', 'Manifest')->whereIn('kegiatan', ['gate-in', 'gate-out'])->get();

        $data['take'] = Photo::where('type', 'manifest')->where('master_id', $manifest->id)->where('action', 'gate-out')->pluck('detil');


        return view('android.gate.indexManifest', $data);
    }
}
