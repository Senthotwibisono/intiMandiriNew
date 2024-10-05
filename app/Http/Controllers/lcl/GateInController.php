<?php

namespace App\Http\Controllers\lcl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Container as Cont;
use App\Models\JobOrder as Job;
use App\Models\Manifest;
use App\Models\Eseal;
use App\Models\User;
use App\Models\Photo;
use App\Models\BarcodeGate as Barcode;
use App\Models\YardDetil as RowTier;

use GuzzleHttp\Client;
class GateInController extends Controller
{
    protected $client;
    protected $url;
    protected $token;

    public function __construct()
    {
        $this->middleware('auth');

        $this->url = 'https://vtsapi.easygo-gps.co.id/api/eseal/newDoPLP';
        $this->token = '73612D582EF54F119F8E41845405B8D6';
        $this->client = new Client(); // Inisialisasi Guzzle Client
    }

    public function index()
    {
        $data['title'] = "Import LCL - Gate In";
        $data['conts'] = Cont::where('type', '=', 'lcl')->where('tglkeluar', null )->get();
        $data['user'] = Auth::user()->name;
        $data['seals'] = Eseal::get();
        
        return view('lcl.realisasi.gateIn.index', $data);
    }

    public function edit($id)
    {
        $gate = Cont::where('id', $id)->first();
        if ($gate) {
            $job = Job::where('id', $gate->joborder_id)->first();
            $user = Auth::user()->name;
            $userId = Auth::user()->id;
            $uid = User::where('id', $gate->uidmasuk)->first();
            return response()->json([
                'success' => true,
                'data' => $gate,
                'job' =>$job,
                'user' => $user,
                'userId' => $userId,
                'uid' => $uid,
            ]);
        }
    }

    public function update(Request $request)
    {
        $cont = Cont::where('id', $request->id)->first();
        if ($cont) {
            $cont->update([
                'tglmasuk'=>$request->tglmasuk,
                'jammasuk'=>$request->jammasuk,
                'uidmasuk'=>$request->uidmasuk,
                'nopol'=>$request->nopol,
                'no_seal'=> $request->no_seal,
            ]);
            $manifest = Manifest::where('container_id', $cont->id)->get();
            foreach ($manifest as $mans) {
                $mans->update([
                    'tglmasuk'=>$request->tglmasuk,
                    'jammasuk'=>$request->jammasuk,
                ]);
            }

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imagesInt', $fileName, 'public'); 
                    $newPhoto = Photo::create([
                        'master_id' => $cont->id,
                        'type' => 'lcl',
                        'action' => 'gate_in',
                        'photo' => $fileName,
                    ]);
                }
            }
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data berhasil di update']);
        }
    }

    public function detail($id)
    {
        $cont = Cont::where('id', $id)->first();
        $data['title'] = "Photo Gate In Container - " . $cont->nocontainer;
        $data['item'] = $cont;
        $data['photos'] = Photo::where('master_id', $id)->where('action', '=', 'gate_in')->get();
        // dd($data['photos']);
        return view('photo.index', $data);
    }

    public function detailDelete(Request $request)
    {
        $photo = Photo::where('id', $request->id)->first();
        if ($photo) {
            $filePath = public_path('storage/imagesInt/' . $photo->photo);

            // Hapus file foto jika ada
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $photo->delete();
            return redirect()->back()->with('success', 'Data berhasil di Hapus');
        }else {
            return redirect()->back()->with('error', 'Oopss, Something Wrong!!');
        }
    }

    // Seal
    public function indexSeal()
    {
        $data['title'] = "Import LCL - Dispatche E-Seal";
        $data['conts'] = Cont::where('type', '=', 'lcl')->where('tglkeluar', null )->get();
        $data['user'] = Auth::user()->name;
        $data['seals'] = Eseal::get();
        
        return view('lcl.realisasi.gateIn.seal', $data);
    }

    public function updateSeal(Request $request)
    {
        $cont = Cont::where('id', $request->id)->first();
        if ($cont) {
            $cont->update([
                'nopol' => $request->nopol,
                'no_seal' => $request->no_seal,
            ]);
        
            $data = [
                "no_plp" => "016805",
                "no_sj" => $request->no_sj ?? "",  // Use an empty string if null
                "no_eseal" => 'ARN1-4G-214',
                "nopol" => "B9108JH",
                "tgl_plp" => date('Y-m-d H:i:s', strtotime("2024-08-30")),
                "opsi_complete" => 0,
                "dur_valid_geofence" => 15,
                "allow_multiple_do" => 0,
                "maxtime_delivery" => 0,
                "maxtime_checking" => 0,
                "alert_telegram" => [],
                "alert_email" => [],
                "alert_dur_idle" => 0,
                "alert_dur_notUpdate" => 0,
                "alert_dur_terlarang" => 0,
                "alert_tujuan_lain" => 0,
                "flag" => " ",
                "user_login" => " ",
                "checkpoint_auto_route" => 0,
                "checkpoint_code" => " ",
                "route_type" => 0,
                "route_code" => " ",
                "note" => " ",
                "client_code" => " ",
                "url_reply" => " ",
                "backdate_leaving_asal_minutes" => 0,
                "driver_code" => " ",
                "asal" => [
                    [
                        "geo_code" => "JICT",
                        "description" => " ",
                        // Remove or replace null values with valid data or defaults
                        "lon" => 0.0,
                        "lat" => 0.0,
                        "radius" => 100,  // Default radius
                        "plan_loading_time" => ""
                    ]
                ],
                "tujuan" => [
                    [
                        "geo_code" => "JICT",
                        "no_sj" => $request->no_sj ?? " ",  // Use an empty string if null
                        "description" => " ",
                        "cust_alert_telegram" => [],
                        "cust_alert_email" => [],
                        // Remove or replace null values with valid data or defaults
                        "lon" => 0.0,
                        "lat" => 0.0,
                        "radius" => 100,  // Default radius
                        "plan_unloading_time" => " ",
                        "std_km_delivery" => 0,
                        "std_minute_delivery" => 0
                    ]
                ],
                "shipment" => [
                    "jns_muatan" => $request->jns_muatan ?? "default", // Handle nulls
                    "vol_sj" => $request->vol_sj ?? 0,
                    "vol_timbangan" => $request->vol_timbangan ?? 0,
                    "vol_kosong" => $request->vol_kosong ?? 0,
                    "vol_tujuan" => $request->vol_tujuan ?? 0,
                    "cpty_muatan" => $request->cpty_muatan ?? "default",
                    "no_tiket_timbangan" => $request->no_tiket_timbangan ?? "default",
                    "note_shipment" => " ",
                    "down_payment" => $request->down_payment ?? 0,
                    "truck_id" => " ",
                    "no_container" => "FTAU1136748",
                    "jns_cont_id" => " ",
                    "size_cont_id" => " ",
                    "driver_name_2" => " ",
                    "driver_phone_2" => " ",
                    "consignee" => " ",
                    "tarif_angkut" => $request->tarif_angkut ?? 0,
                    "uang_jalan" => $request->uang_jalan ?? 0,
                    "add_uang_jalan" => $request->add_uang_jalan ?? 0,
                    "shipping_line" => " ",
                    "shipper" => " ",
                    "transporter" => " ",
                    "do_expired" => $request->do_expired ?? "2024-12-31",
                    "tipe" => $request->tipe ?? "default"
                ]
            ];
            
        
            try {
                // Mengirim permintaan ke API eksternal menggunakan Guzzle
                $response = $this->client->post($this->url, [
                    'headers' => [
                        'Token' => $this->token,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $data, // Mengirim data dalam format JSON
                ]);
            
                if ($response->getStatusCode() == 200) {
                    $responseData = json_decode($response->getBody(), true);
                    dd($cont, json_encode($data), $responseData, $this->token, $responseData['Data']['do_id']);
                    if ($responseData['ResponseCode'] == 1) {
                        $cont->update([
                            'do_id' => $responseData['Data']['do_id']
                        ]);
                        return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data berhasil di update dan dikirim ke API']);
                    } else {
                        return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Gagal mengirim ke API: ' . $responseData['ResponseMessage']]);
                    }
                } else {
                    return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Data berhasil di update, tetapi gagal mengirim ke API']);
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            }
        }
    }


    public function indexMT()
    {
        $data['title'] = "LCL Import || Realisasi - Buang Empty";
        $data['conts'] = Cont::whereNotNull('endstripping')->whereNotNull('tglmasuk')->get();
        $data['user'] = Auth::user()->name;
        $data['seals'] = Eseal::get();

        return view('lcl.realisasi.gateIn.mty', $data);
    }

    public function detailMt($id)
    {
        $cont = Cont::where('id', $id)->first();
        $data['title'] = "Photo Gate Out Container - " . $cont->nocontainer;
        $data['item'] = $cont;
        $data['photos'] = Photo::where('master_id', $id)->where('type', '=', 'lcl')->where('action', '=', 'gate_out')->get();
        // dd($data['photos']);
        return view('photo.index', $data);
    }

    public function updateMt(Request $request)
    {
        $cont = Cont::where('id', $request->id)->first();
        if ($cont) {
            if ($cont->status_bc != 'release') {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Status belum release']);
            }
            $cont->update([
                'tglkeluar'=>$request->tglkeluar,
                'jamkeluar'=>$request->jamkeluar,
                'uidmty'=>$request->uidmty,
                'nopol_mty'=>$request->nopol_mty,
                'no_seal'=> $request->no_seal,
            ]);

            $oldYard = RowTier::where('cont_id', $cont->id)->get();
            if ($oldYard) {
                foreach ($oldYard as $old) {
                    $old->update([
                        'cont_id' => null,
                        'active' => 'N',
                    ]);
                }
            }

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imagesInt', $fileName, 'public'); 
                    $newPhoto = Photo::create([
                        'master_id' => $cont->id,
                        'type' => 'lcl',
                        'action' => 'gate_out',
                        'photo' => $fileName,
                    ]);
                }
            }
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data berhasil di update']);
        }
    }   

    public function createBarcode(Request $request)
    {
        $cont = Cont::where('id', $request->id)->first();
        if ($cont->status_bc != 'release') {
            $action = 'hold';
        }else {
            $action = 'release';
        }
        $barcode = Barcode::where('ref_id', $cont->id)->where('ref_type', '=', 'LCL')->where('ref_action', $action)->first();
        if ($barcode) {
                $now = Carbon::now();
                if ($barcode->status == 'inactive' || $barcode->expired <= $now) {
                    do {
                        $uniqueBarcode = Str::random(20);
                    } while (Barcode::where('barcode', $uniqueBarcode)->exists());
                    $barcode->update([
                        'barcode'=> $uniqueBarcode,
                        'status'=>'active',
                        'expired'=> Carbon::now()->addDays(3),
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'updated successfully!',
                        'data'    => $barcode,
                    ]);
                }else {
                    return response()->json([
                        'success' => true,
                        'message' => 'updated successfully!',
                        'data'    => $barcode,
                    ]);
                }
        }else {
            do {
                $uniqueBarcode = Str::random(20);
            } while (Barcode::where('barcode', $uniqueBarcode)->exists());    
            $newBarcode = Barcode::create([
                'ref_id'=>$cont->id,
                'ref_type'=>'LCL',
                'ref_action'=> $action,
                'ref_number'=>$cont->nocontainer,
                'barcode'=> $uniqueBarcode,
                'status'=>'active',
                'expired'=> Carbon::now()->addDays(3),
                'uid'=> Auth::user()->id,
                'created_at'=> Carbon::now(),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $newBarcode,
            ]);
        }
    }
}
