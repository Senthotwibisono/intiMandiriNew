<?php

namespace App\Http\Controllers\FCL\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\ContainerFCL as Cont;
use App\Models\JobOrderFCL as Job;
use App\Models\Eseal;
use App\Models\User;
use App\Models\Photo;
use App\Models\TpsSPJM as SPJM;
use App\Models\TpsSPJMCont as SPJMCont;
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
use App\Models\TpsPabean as Pabean;
use App\Models\TpsPabeanCont as PabeanCont;
use App\Models\TpsPabeanKms as PabeanKms;
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
                } elseif ($cont->status_behandle == 2) {
                    return '<button class="btn btn-primary FinishBehandle" data-id="'.$cont->id.'">Finish</button>';
                } else {
                    return '<button class="btn btn-outline-primary ReadyCheck" data-id="'.$cont->id.'">Make It Ready</button>';
                }
            }

        })
        ->addColumn('status', function($cont){
            if ($cont->status_behandle == 1) {
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

    public function searchSPJM(Request $request)
    {
        $rules = [
            'jenis_spjm' => 'required',
            'no_spjm'  => 'required',
            'tgl_spjm'  => 'required',
        ];

        $messages = [
            'jenis_spjm'       => 'Jenis SPJM Tidak Boleh Null',
            'no_spjm'  => 'No SPJM Tidak Boleh Null',
            'tgl_spjm'  => 'Tgl SPJM Tidak Boleh Null',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(fn($error) => $error[0])->toArray();
            return response()->json([
                'status'  => false,
                'success' => false,
                'message' => implode(", ", $errors),
                'errors'  => $validator->errors(),
            ], 400);
        }

        $cont = Cont::find($request->id);
        if (!$cont) {
            return response()->json([
                'success' => false,
                'message' => 'Container tidak ditemukan',
            ]);
        }

        if (!$request->tgl_spjm || $request->tgl_spjm == null) {
            return response()->json([
                'success' => false,
                'message' => 'Tgl SPJM Tidak Boleh ',
            ]);
        }

        try {
            if ($request->jenis_spjm == 'karantina') {
                $cont->update([
                    'jenis_spjm' => $request->jenis_spjm,
                    'no_spjm' => $request->no_spjm,
                    'tgl_spjm' => $request->tgl_spjm,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Data Berhasil di Simpan'
                ]);
            }else {
                // dd($request->all());
                $spjm = SPJM::where('no_spjm', $request->no_spjm)->first();
                if (!$spjm) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data tidak ditemukan',
                    ]);
                }

                $tglSPJM = ($spjm->tgl_spjm) ? Carbon::parse($spjm->tgl_spjm)->format('Y-m-d') : null;
                // dd($tglSPJM);
                if ($tglSPJM != $request->tgl_spjm) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tgl SPJM Berbeda, Harap hubung Beacukai',
                    ]);
                }

                $spjmCont = SPJMCont::where('spjm_id', $spjm->id)->where('no_cont', $cont->nocontainer)->first();
                if (!$spjmCont) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada container yang sama, harap lakukan cross check kembali',
                    ]);
                }
                
                if ($spjmCont->size != $cont->size) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ukuran Container Berbeda, harap lakukan update terlebih dahulu',
                    ]);
                }

                $cont->update([
                    'jenis_spjm' => $request->jenis_spjm,
                    'no_spjm' => $request->no_spjm,
                    'tgl_spjm' => $request->tgl_spjm,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil di simpan',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
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
        $data['doks'] = Kode::orderBy('kode', 'asc')->get();
        $data['kets'] = KP::where('tipe', 'Container')->where('kegiatan', '=', 'gate-out')->get();
        $data['user'] = Auth::user()->id;
        return view('fcl.delivery.gateOut', $data);
    }

    public function dataGateOutFCL(Request $request)
    {
        $conts = Cont::with(['job', 'user'])->whereNotNull('tglmasuk')->get();

        return DataTables::of($conts)
        ->addColumn('edit', function ($conts){
            return '<buttpn class="btn btn-outline-warning editButton" data-id="'.$conts->id.'"><i class="fa fa-pen"></i></buttpn>';
        })
        ->addColumn('detil', function($conts){
            return '<a href="javascript:void(0)" onclick="openWindow(\'/lcl/realisasi/mty-detail'.$conts->id.'\')" class="btn btn-sm btn-info">
                <i class="fa fa-eye"></i>
            </a>';
        })
        ->addColumn('printBarcode', function($conts){
            return '<button class="btn btn-danger printBarcode" data-id="'.$conts->id.'"><i class="fa fa-print"></i></button>';
        })
        ->rawColumns(['edit', 'detil', 'printBarcode'])
        ->make(true);
    }

    public function searchingDokumenGate(Request $request)
    {
        $kode = $request->kode;
        $cont = Cont::find($request->id);
        if ($kode == 1) {
            $sppb = SPPB::where('no_sppb', $request->noDok)->first();
            if ($sppb) {
                $tglSPPB = Carbon::parse($sppb->tgl_sppb)->format('Y-m-d');
                if ($tglSPPB != $request->tglDok) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dokumen tidak Diemukan!!',
                    ]);
                }

                $contDok = SPPBCont::where('sppb_id', $sppb->id)->where('no_cont', $cont->nocontainer)->first();
                if (!$contDok) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Container tidak ditemukan!!',
                    ]);
                }
                if ($contDok->size != $cont->size) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ukuran Container tidak sesuai!!',
                    ]);
                }

                $cont->update([
                    'kd_dok_inout' => $kode,
                    'no_dok' => $request->noDok,
                    'tgl_dok' => $request->tglDok,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil Disimpan!!',
                ]);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak Diemukan!!',
                ]);
            }
        }elseif ($kode == 2) {
            $bc23 = BC23::where('no_sppb', $request->noDok)->first();
            if ($bc23) {
                $tglBC23 = Carbon::parse($bc23->tgl_sppb)->format('Y-m-d');
                if ($tglBC23 != $request->tglDok) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dokumen tidak Diemukan!!',
                    ]);
                }
                $contDok = BC23Cont::where('sppb23_id', $bc23->id)->where('no_cont', $cont->nocontainer)->first();
                if (!$contDok || $contDok->size != $cont->size) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Container tidak ditemukan!!',
                    ]);
                }
                $cont->update([
                    'kd_dok_inout' => $kode,
                    'no_dok' => $request->noDok,
                    'tgl_dok' => $request->tglDok,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil Disimpan!!',
                ]);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak Diemukan!!',
                ]);
            }
        }elseif (in_array($kode, [41, 42])) {
            $pabean = Pabean::where('kd_dok_inout', $kode)->where('no_dok_inout', $request->noDok)->first();
            if ($pabean) {
                $tglPabean = Crbon::parse($pabean->tgl_dok_inout)->format('Y-m-d');
                if ($tglPabean != $request->tglDok) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dokumen tidak Diemukan!!',
                    ]);
                }
                $contDok = PabeanCont::where('pabean_id', $pabean->id)->where('no_cont', $cont->nocontianer)->where('size', $cont->size)->first();
                if (!$cont) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Container tidak Diemukan!!',
                    ]);
                }
                $cont->update([
                    'kd_dok_inout' => $kode,
                    'no_dok' => $request->noDok,
                    'tgl_dok' => $request->tglDok,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil disimpan!!',
                ]);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak Diemukan!!',
                ]);
            }
        }else {
            $manual = Manual::where('kd_dok_inout', $kode)->where('no_dok_inout', $request->noDok)->first();
            if ($manual) {
                $tglManual = Carbon::createFromFormat('d/m/Y', $manual->tgl_dok_inout)->format('Y-m-d');
                if ($tglManual != $request->tglDok) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dokumen tidak Diemukan!!',
                    ]);
                }
                $contDok = ManualCont::where('manual_id', $manual->idm)->where('no_cont', $cont->nocontainer)->where('size', $cont->size)->first();
                if (!$contDok) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Container tidak Diemukan!!',
                    ]);
                }
                $cont->update([
                    'kd_dok_inout' => $kode,
                    'no_dok' => $request->noDok,
                    'tgl_dok' => $request->tglDok,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Behasil disimpan!!',
                ]);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak Diemukan!!',
                ]);
            }
        }
       
        return response()->json([
            'success' => false,
            'message' => 'Dokumen tidak Diemukan!!',
        ]);
    }

    public function gatePassBonMuat(Request $request)
    {
        $cont = Cont::where('id', $request->id)->first();
        if ($cont->active_to == null) {
            if ($cont->lokasisandar_id != 6) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harap melunasi invoice terlebih dahulu',
                ]);
            }
        }
        
        if ($cont->flag_segel_merah == 'Y') {
            $action = 'holdp2';
        }else {
            if ($cont->status_bc != 'release') {
                $action = 'hold';
            }else {
                $action = 'active';
            }
        }
        
        $barcode = Barcode::where('ref_id', $cont->id)->where('ref_type', '=', 'FCL')->where('ref_action', 'release')->first();
        if ($barcode) {
                $now = Carbon::now();
                if ($barcode->status == 'inactive' || $barcode->expired <= $now) {
                    $barcode->update([
                        'status'=> $action,
                        'expired'=> ($cont->lokasisandar_id != 6) ? $cont->active_to : Carbon::now(),
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
                'status'=> $action,
                'expired'=> ($cont->lokasisandar_id != 6) ? $cont->active_to : Carbon::now(),
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
