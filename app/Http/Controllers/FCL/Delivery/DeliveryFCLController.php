<?php

namespace App\Http\Controllers\FCL\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\ContainerFCL as Cont;
use App\Models\JobOrderFCL as Job;
use App\Models\Eseal;
use App\Models\User;
use App\Models\Photo;
use App\Models\TpsSPJM as SPJM;
use App\Models\TpsSPPB as SPPB;
use App\Models\TpsSPPBCont as SPPBCont;
use App\Models\TpsSPPBKms as SPPBKms;
use App\Models\KodeDok as Kode;
use App\Models\TpsManual as Manual;
use App\Models\TpsManualCont as ManualCont;
use App\Models\TpsManualKms as ManualKms;
use App\Models\TpsSPPBBC23 as BC23;
use App\Models\TpsSPPBBC23Cont as BC23Cont;
use App\Models\TpsSPPBBC23Kms as BC23Kms;
use App\Models\BarcodeGate as Barcode;
use App\Models\PlacementManifest as PM;
use App\Models\YardDesign as YD;
use App\Models\YardDetil as RowTier;
use App\Models\InvoiceHeader as Header;
use App\Models\KeteranganPhoto as KP;

class DeliveryFCLController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function indexBehandle()
    {
        $data['title'] = "FCL - Behandle";
        $data['containers'] = Cont::whereNotNull('tglmasuk')->whereNull('tglkeluar')->get();
        $data['kets'] = KP::where('tipe', 'Container')->where('kegiatan', '=', 'behandle')->get();
        $data['yards'] = YD::whereNot('yard_block', null)->get();

        return view('fcl.delivery.behandle', $data);
    }

    public function getDataCont($id)
    {
        $cont = Cont::where('id', $id)->first();
        if ($cont) {
            $job = Job::where('id', $cont->joborder_id)->first();
            $user = Auth::user()->name;
            $userId = Auth::user()->id;
            $uid = User::where('id', $cont->uidmasuk)->first();
            $rowTier = RowTier::where('id', $cont->yard_detil_id)->first();
            // var_dump($cont->yard_detil_id, $rowTier);
            // die;
            if ($rowTier) {
                $slot = $rowTier->slot;
                $row = $rowTier->row;
                $tier = $rowTier->tier;
            } else {
                $slot = null;
                $row = null;
                $tier = null;
            }
            return response()->json([
                'success' => true,
                'data' => $cont,
                'job' =>$job,
                'user' => $user,
                'userId' => $userId,
                'uid' => $uid,
                'slot' => $slot,
                'row' => $row,
                'tier' => $tier,
            ]);
        }
    }

    public function readyCheckBehandle($id)
    {
        // var_dump($id);
        // die();
        try {
            $cont = Cont::find($id);
            if ($cont) {
                $cont->update([
                    'date_ready_behandle' => Carbon::now(),
                    'status_behandle' => 1,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Container Siap Untuk Behandle',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
            ]);
        }
    }

    public function prosesCheckBehandle($id)
    {
        // var_dump($id);
        // die();
        try {
            $cont = Cont::find($id);
            if ($cont) {
                $cont->update([
                    'date_check_behandle' => Carbon::now(),
                    'status_behandle' => 2,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Container Memasuki Proses Behandle',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
            ]);
        }
    }

    public function finishCheckBehandle($id)
    {
        // var_dump($id);
        // die();
        try {
            $cont = Cont::find($id);
            if ($cont) {
                $cont->update([
                    'date_finish_behandle' => Carbon::now(),
                    'status_behandle' => 3,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Proses Behandle Selesai',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
            ]);
        }
    }

    public function updateDataBehandle(Request $request)
    {
        try {
            $cont = Cont::find($request->id);
            $oldYard = RowTier::where('cont_type', 'fcl')->where('cont_id', $cont->id)->get();
            if ($oldYard) {
                foreach ($oldYard as $old) {
                    $old->update([
                        'cont_id' => null,
                        'cont_type'=>  null,
                        'active' => 'N',
                    ]);
                }
            }
            $yardDetil = RowTier::where('yard_id', $request->yard_id)->where('slot', $request->slot)->where('row', $request->row)->where('tier', $request->tier)->first();
            if ($yardDetil) {
                if ($yardDetil->cont_id != null && $yardDetil->cont_id != $cont->id) {
                    return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Yard Sudah Terisi, Silahkan pilih yard lain']);
                }
                
                
                $cont->update([
                    'yard_id'=>$request->yard_id,
                    'yard_detil_id'=> $yardDetil->id,
                    'desc_check_behandle' => $request->desc_check_behandle,
                    'desc_finish_behandle' => $request->desc_finish_behandle
                ]);


                if ($cont->size == '40') {
                    $nextSlot = $request->slot + 1;
                    $nexyard = RowTier::where('yard_id', $request->yard_id)->where('slot', $nextSlot)->where('row', $request->row)->where('tier', $request->tier)->first();
                    $nexyard->update([
                        'cont_id' => $cont->id,
                        'cont_type'=>  'fcl',
                        'active' => 'Y',
                    ]);
                }
                $yardDetil->update([
                    'cont_id' => $cont->id,
                    'cont_type'=>  'fcl',
                    'active' => 'Y',
                ]);
    
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $fileName = $photo->getClientOriginalName();
                        $photo->storeAs('imagesInt', $fileName, 'public'); 
                        $newPhoto = Photo::create([
                            'master_id' => $cont->id,
                            'type' => 'fcl',
                            'action' => 'behandle',
                            'detil' => $request->keteranganPhoto,
                            'photo' => $fileName,
                        ]);
                    }
                }
                return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data berhasil di update']);
            }else {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Yard Tidak Ditemukan']);
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'error :' . $th->getMessage()]);
        }
    }

    public function detailBehandle($id)
    {
        $manifest = Cont::where('id', $id)->first();
        $data['title'] = "Photo Behandle Manifest - " . $manifest->notally;
        $data['item'] = $manifest;
        $data['photos'] = Photo::where('master_id', $id)->where('type', '=', 'fcl')->where('action', '=', 'behandle')->get();
        // dd($data['photos']);
        return view('photo.index', $data);
    }

    public function indexGateOut()
    {
        $data['title'] = "FCL - Gate Out";
        $data['containers'] = Cont::whereNotNull('tglmasuk')->where(function ($query) {
            $query->where('status_behandle', '3')
                  ->orWhereNull('status_behandle');
        })->get();
        $data['kets'] = KP::where('tipe', 'Container')->where('kegiatan', '=', 'gate-out')->get();
        
        $data['user'] = Auth::user()->id;
        return view('fcl.delivery.gateOut', $data);
    }
}
