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
use DataTables;

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

    public function behandleData(Request $request)
    {
        $cont = Cont::with(['job'])->get();

        return DataTables::of($cont)
        ->addColumn('action', function($cont){
            return '<button class="btn btn-warning editButton" data-id="'.$cont->id.'"><i class="fa fa-pencil"></i></button>';
        })
        ->addColumn('photo', function($cont){
            return '<a href="javascript:void(0)" onclick="openWindow(\'/fcl/delivery/behandleDetil/'.$cont->id.'\')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
        })
        ->addColumn('statusBehandle', function($cont){
            if ($cont->no_spjm == null) {
                return '<span class="badge bg-warning">Dokumen SPJM Belum tersedia</span>';
            } else {
                if ($cont->status_behandle == 1) {
                    return '<button class="btn btn-outline-primary checkProses" data-id="'.$cont->id.'">Checking Proses</button>';
                } elseif ($status_behandle == 2) {
                    return '<button class="btn btn-primary FinishBehandle" data-id="'.$cont->id.'">Finish</button>';
                } else {
                    return '<button class="btn btn-outline-primary ReadyCheck" data-id="'.$cont->id.'">Make It Ready</button>';
                }
            }

        })
        ->addColumn('status', function($cont){
            if ($cont->status_beahandle == 1) {
                return '<span class="badge bg-primary">Ready</span>';
            } elseif ($cont->status_behandle == 2) {
                return '<span class="badge bg-warning">On Progress</span>';
            } elseif ($cont->status_behandle == 3) {
                return '<span class="badge bg-info">Finish</span>';
            }else {
                // return '<span class="badge bg-light-warning">Dokumen SPJM Belum tersedia</span>';
                return '-';
            }
        })
        ->rawColumns(['action', 'photo', 'statusBehandle', 'status'])
        ->make(true);
    }

    public function getDataCont($id)
    {
        // dd($id);
        $cont = Cont::where('id', $id)->first();
        try {
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
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Opss Something wrong : CONTAINER TIDAK DITEUKAN',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Opss Something wrong : ' . $th->getMessage(),
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

    public function gatePassBonMuat(Request $request)
    {
        $cont = Cont::where('id', $request->id)->first();
        if ($cont->active_to == null) {
            return response()->json([
                'success' => false,
                'message' => 'Harap melunasi invoice terlebih dahulu',
            ]);
        }
        // $expiredCheck = Carbon::now()->addDay();
        // // var_dump($expiredCheck);
        // // die;
        // if ($cont->active_to < Carbon::today()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Invoice Container telah expired sejak: ' . Carbon::parse($cont->active_to)->format('d/m/Y') . '. Harap melakukan perpanjangan terlebih dahulu',
        //     ]);
        // }
        
        $barcode = Barcode::where('ref_id', $cont->id)->where('ref_type', '=', 'FCL')->where('ref_action', 'release')->first();
        if ($barcode) {
                $now = Carbon::now();
                if ($barcode->status == 'inactive' || $barcode->expired <= $now) {
                    do {
                        $uniqueBarcode = Str::random(20);
                    } while (Barcode::where('barcode', $uniqueBarcode)->exists());
                    $barcode->update([
                        'barcode'=> $uniqueBarcode,
                        'status'=>'active',
                        'expired'=> $cont->active_to,
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
                'ref_type'=>'FCL',
                'ref_action'=>'release',
                'ref_number'=>$cont->nocontainer,
                'barcode'=> $uniqueBarcode,
                'status'=>'active',
                'expired'=> $cont->active_to,
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

    public function gateOutFCL(Request $request)
    {
        $cont = Cont::where('id', $request->id)->first();
        try {
            if ($cont) {
                $cont->update([
                    'nopol' => $request->nopol,
                    'nopol_mty' => $request->nopol_mty,
                    'tglkeluar' => $request->tglkeluar,
                    'jamkeluar' => $request->jamkeluar,
                ]);
            }
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imagesInt', $fileName, 'public'); 
                    $newPhoto = Photo::create([
                        'master_id' => $cont->id,
                        'type' => 'fcl',
                        'action' => 'gate-out',
                        'photo' => $fileName,
                        'detil'=> $request->detil,
                    ]);
                }
            }
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di Update']);
            
        } catch (\Throwable $e) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Something Wrong'. $e->getMessage()]);
        }
    }
}
