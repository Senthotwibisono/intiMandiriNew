<?php

namespace App\Http\Controllers\lcl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\JobOrder as Job;
use App\Models\Container as Cont;
use App\Models\Consolidator;
use App\Models\Negara;
use App\Models\Pelabuhan;
use App\Models\Vessel;
use App\Models\Gudang;
use App\Models\Eseal;
use App\Models\ShippingLine as SL;
use App\Models\LokasiSandar as LS;
use App\Models\TpsPLP as PLP;
use App\Models\TpsPLPdetail as PLPdetail;
use App\Models\BarcodeGate as Barcode;

use DataTables;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['title'] = "Import LCL - Register";
        $data['negaras'] = Negara::get();
        $data['consolidators'] = Consolidator::get();
        $data['vessel'] = Vessel::get();
        $data['ports'] = Pelabuhan::get();
        $data['ships'] = SL::get();
        $data['loks'] = LS::get();
        $data['gudangs'] = Gudang::get();
        return view('lcl.register.index', $data);
    }

    public function indexData()
    {
        $jobs = Job::with(['containers', 'Kapal', 'user'])
            ->where('type', 'lcl')
            ->get()
            ->map(function($job) {
                $containers = $job->containers;
                if ($containers->isNotEmpty()) {
                    return $containers->map(function($container) use ($job) {
                        return [
                            'actions' => '<a href="/lcl/register/detail-'.$container->joborder_id.'" class="btn btn-warning"><i class="fa fa-pen"></i></a> 
                                          <button class="btn btn-danger printBarcode" data-id="'.$container->id.'"><i class="fa fa-print"></i></button>',
                            'nojoborder' => $job->nojoborder,
                            'nospk' => $job->nospk,
                            'nocontainer' => $container->nocontainer,
                            'nombl' => $job->nombl,
                            'eta' => $job->eta,
                            'Kapal_name' => $job->Kapal->name ?? '',
                            'user_name' => $job->user->name,
                        ];
                    });
                } else {
                    return [[
                        'actions' => '<a href="/lcl/register/detail-'.$job->id.'" class="btn btn-warning"><i class="fa fa-pen"></i></a>',
                        'nojoborder' => $job->nojoborder,
                        'nospk' => $job->nospk,
                        'nocontainer' => 'Belum ada Container',
                        'nombl' => $job->nombl,
                        'eta' => $job->eta,
                        'Kapal_name' => $job->Kapal->name ?? '',
                        'user_name' => $job->user->name,
                    ]];
                }
            })
            ->flatten(1);
        
        return datatables()->of($jobs)->rawColumns(['actions'])->make(true);
    }


    public function create(Request $request)
    {
        try {
            $currentYear = Carbon::now()->format('y');
            $currentMonth = Carbon::now()->format('m');
            $lastJob = Job::whereYear('c_datetime', Carbon::now()->year)->whereMonth('c_datetime', Carbon::now()->month)
                          ->orderBy('id', 'desc')
                          ->first();
        
            if ($lastJob) {
                $lastJobNumber = intval(substr($lastJob->nojoborder, 3, 5));
                $newJobNumber = str_pad($lastJobNumber + 1, 5, '0', STR_PAD_LEFT);

                // dd($lastJobNumber, $newJobNumber);
            } else {
                $newJobNumber = '00001';
            }
            $noJob = 'ITM' . $newJobNumber . '/' .$currentMonth . '/' .$currentYear;
            // dd($noJob);
            $job = Job::create([
                'nojoborder'=>$noJob,
                'nospk'=>$request->nospk,
                'nombl'=>$request->nombl,
                'tgl_master_bl'=>$request->tgl_master_bl,
                'consolidator_id'=>$request->consolidator_id,
                'negara_id'=>$request->negara_id,
                'pelabuhan_id'=>$request->pelabuhan_id,
                'vessel'=>$request->vessel,
                'voy'=>$request->voy,
                'eta'=>$request->eta,
                'etd'=>$request->etd,
                'shipping_id'=>$request->shipping_id,
                'lokasisandar_id'=>$request->lokasisandar_id,
                'gudang_tujuan'=>$request->gudang_tujuan,
                'jeniskegiatan'=>$request->jeniskegiatan,
                'jumlahhbl'=>$request->jumlahhbl,
                'keterangan'=>$request->keterangan,
                'pel_muat'=>$request->pel_muat,
                'pel_bongkar'=>$request->pel_bongkar,
                'uid'=> Auth::user()->id,
                'c_datetime'=>Carbon::now(),
                'type'=>'lcl',
            ]);
            return redirect()->route('lcl.register.detail', ['id' => $job->id])->with('status', ['type'=>'success', 'message'=>'Data berhasil di buat']);
        } catch (\Throwable $e) {
           return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Something Wrong' . $e->getMessage()]);
        }
    }

    public function detail($id)
    {
        $job = Job::where('id', $id)->first();
        $data['title'] = "Detail Job- " . $job->nojoborder;
        $data['job'] = $job;
        $data['negaras'] = Negara::get();
        $data['consolidators'] = Consolidator::get();
        $data['vessel'] = Vessel::get();
        $data['ports'] = Pelabuhan::get();
        $data['ships'] = SL::get();
        $data['loks'] = LS::get();
        $data['gudangs'] = Gudang::get();
        $data['seals'] = Eseal::get();
        $data['conts'] = Cont::where('joborder_id', $id)->get(); 

        return view('lcl.register.detail', $data);
    }

    public function update(Request $request)
    {
        $job = Job::where('id', $request->id)->first();
        if ($job) {
            $job->update([
                'nospk'=>$request->nospk,
                'nombl'=>$request->nombl,
                'tgl_master_bl'=>$request->tgl_master_bl,
                'consolidator_id'=>$request->consolidator_id,
                'negara_id'=>$request->negara_id,
                'pelabuhan_id'=>$request->pelabuhan_id,
                'vessel'=>$request->vessel,
                'voy'=>$request->voy,
                'eta'=>$request->eta,
                'etd'=>$request->etd,
                'shipping_id'=>$request->shipping_id,
                'lokasisandar_id'=>$request->lokasisandar_id,
                'gudang_tujuan'=>$request->gudang_tujuan,
                'jeniskegiatan'=>$request->jeniskegiatan,
                'jumlahhbl'=>$request->jumlahhbl,
                'keterangan'=>$request->keterangan,
                'pel_muat'=>$request->pel_muat,
                'pel_bongkar'=>$request->pel_bongkar,
                'uid'=> Auth::user()->id,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data berhasil di update']);
        }else {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Something Wrong']);
        }
    }

    public function createContainer(Request $request)
    {
        $job = Job::where('id', $request->joborder_id)->first();
        if ($job) {
            if ($request->size == '20') {
                $teus = 1;
            }elseif ($request->size == '40') {
                $teus = 2;
            }else {
                $teus = 0;
            }
            $cont = Cont::create([
                'nocontainer'=>$request->nocontainer,
                'type'=>'lcl',
                'joborder_id'=>$request->joborder_id,
                'size'=>$request->size,
                'no_seal'=>$request->no_seal,
                'weight'=>$request->weight,
                'meas'=>$request->meas,
                'teus'=>$teus,
                'uid' => Auth::user()->id,
            ]);
            $oldWeight = $job->grossweight ?? 0;
            $newWeight = $oldWeight + $request->weight;
            $oldMeas = $job->measurement ?? 0;
            $newMeas = $oldMeas + $request->meas;
            $job->update([
                'measurement'=>$newWeight,
                'grossweight'=>$newMeas,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data berhasil di buat']);
            
        }else {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Something Wrong']);
        }
    }

    public function editContainer($id)
    {
        $cont = Cont::where('id', $id)->first();
        if ($cont) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $cont,
            ]);
        }
    }

    public function updateContainer(Request $request)
    {
        $job = Job::where('id', $request->joborder_id)->first();
        if ($job) {
            if ($request->size == '20') {
                $teus = 1;
            }elseif ($request->size == '40') {
                $teus = 2;
            }else {
                $teus = 0;
            }
            $cont = Cont::where('id', $request->id)->first();
            $oldWeight = $job->grossweight - $cont->weight;
            $oldMeas = $job->measurement - $cont->meas;
            $cont->update([
                'nocontainer'=>$request->nocontainer,
                'joborder_id'=>$request->joborder_id,
                'size'=>$request->size,
                'no_seal'=>$request->no_seal,
                'weight'=>$request->weight,
                'meas'=>$request->meas,
                'teus'=>$teus,
                'uid' => Auth::user()->id,
            ]);
            $newWeight = $oldWeight + $request->weight;
            $newMeas = $oldMeas + $request->meas;
            $job->update([
                'measurement'=>$newWeight,
                'grossweight'=>$newMeas,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data berhasil di buat']);
            
        }else {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Something Wrong']);
        }
    }

    public function deleteContainer($id)
    {
        $cont = Cont::where('id', $id)->first();
        if ($cont) {
            
            $cont->delete();
            return response()->json(['success' => 'Lokasi Sandar deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }

    public function postPLP(Request $request)
    {
        $date = $request->ttgl_plp;
        $formatDate = Carbon::parse($date)->format('Ymd');
        $plp = PLP::where('no_plp', $request->noplp)->where('tgl_plp', $formatDate)->first();

        if ($plp) {
            if ($plp->joborder_id != null) {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, PLP sudah memiliki job order']);
            }
            $job = Job::where('id', $request->id)->first();
            $plpDetails = PLPdetail::where('plp_id', $plp->id)->get();
            $conts = $plpDetails->unique('no_cont');
            // dd($conts);
            if (!$conts->isEmpty()) {
                foreach ($conts as $cont) {
                    $oldCont = Cont::where('joborder_id', $job->id)->where('nocontainer', $cont->no_cont)->first();
                    if (!$oldCont) {
                        if ($cont->uk_cont == '20') {
                            $teus = 1;
                        }elseif ($cont->uk_cont == '40') {
                            $teus = 2;
                        }else {
                            $teus = 0;
                        }
                        $newCont = Cont::create([
                            'nocontainer'=>$cont->no_cont,
                            'type'=>'lcl',
                            'joborder_id'=>$job->id,
                            'size'=>$cont->uk_cont,
                            'teus'=>$teus,
                            'uid' => Auth::user()->id,
                        ]);
                    }
                }
            }

            

            $job->update([
                'plp_id' => $plp->id,
                'tno_bc11' => $plp->no_bc11,
                'ttgl_bc11' => $plp->tgl_bc11,
                'noplp' => $plp->no_plp,
                'ttgl_plp' => $plp->tgl_plp,
            ]);
            
            $plp->update([
                'joborder_id'=>$job->id,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data berhasil dibuat']);
        } else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Dokumen tidak ditemukan']);
        }
    }

    public function createBarcode(Request $request)
    {
        $cont = Cont::where('id', $request->id)->first();
        $barcode = Barcode::where('ref_id', $cont->id)->where('ref_type', '=', 'LCL')->where('ref_action', 'get')->first();
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
                'ref_action'=>'get',
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
