<?php

namespace App\Http\Controllers\beaCukai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\LogSegel as Log;
use App\Models\AlasanSegel as Alasan;
use App\Models\PhotoSegel as Photo;
use App\Models\Manifest;
use App\Models\ContainerFCL as ContF;

use Auth;
use carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\DB;

class BeacukaiP2Controller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:bcP2');
    }

    public function indexDashboard()
    {
        $data['title'] = 'Dashboard Bea Cukai P2';

        return view('beacukai.p2.dashboard', $data);
    }

    public function logData(Request $request)
    {
        $log = Log::with(['user', 'manifest'])->where('ref_type', 'LCL')->orderBy('created_at', 'desc')->get();

        return DataTables::of($log)
        ->addColumn('ref_name', function($log){
           return $log->manifest->nohbl ?? '-';
        })
        ->addColumn('container', function($log){
            return $log->manifest->cont->nocontainer ?? '-';
           
        })
        ->addColumn('jobOrder', function($log){
            return $log->manifest->cont->job->nojoborder ?? '-';

        })
        ->addColumn('user', function($log){
            return $log->user->name ?? '-';
        })
        ->make(true);
    }

    public function logDataFCL(Request $request)
    {
        $log = Log::with(['user', 'manifest'])->where('ref_type', 'FCL')->orderBy('created_at', 'desc')->get();

        return DataTables::of($log)
        ->addColumn('ref_name', function($log){
           return $log->fcl->nobl ?? '-';
        })
        ->addColumn('container', function($log){
            return $log->fcl->nocontainer ?? '-';
           
        })
        ->addColumn('jobOrder', function($log){
            return $log->fcl->job->nojoborder ?? '-';

        })
        ->addColumn('user', function($log){
            return $log->user->name ?? '-';
        })
        ->make(true);
    }

    public function listManifestIndex()
    {
        $data['title'] = 'List Manifest';
        $data['alasan'] = Alasan::where('type', 'lock')->get();
        // dd($data['alasan']);
        return view('beacukai.p2.listManifest', $data);
    }

    public function listManifestData(Request $request)
    {
        $manifest = Manifest::with(['user', 'cont', 'shipperM', 'packing', 'dokumen'])->whereNull('tglrelease')->where(function ($query) {
            $query->where('status_bc', '!=', 'HOLDP2')
                  ->orWhereNull('status_bc');
        })->get();

        return DataTables::of($manifest)
        ->addColumn('container', function($manifest){
            return $manifest->cont->nocontainer ?? '-';
        })
        ->addColumn('jobOrder', function($manifest){
            return $manifest->cont->job->nojoborder ?? '-';
        })
        ->addColumn('customer', function($manifest){
            return $manifest->customer->name ?? '-';
        })
        ->addColumn('gudangAsal', function($manifest){
            return $manifest->cont->job->PLP->gudang_asal ?? '-';
        })
        ->addColumn('eta', function($manifest){
            return $manifest->cont->job->eta ?? '-';
        })
        ->addColumn('shipper', function($manifest){
            return $manifest->shipperM->name ?? '-';
        })
        ->addColumn('packingName', function($manifest){
            return $manifest->packing->name ?? '-';
        })
        ->addColumn('packingCode', function($manifest){
            return $manifest->packing->code ?? '-';
        })
        ->addColumn('dokumenName', function($manifest){
            return $manifest->dokumen->name ?? '-';
        })
        ->make(true);
    }

    public function listManifestModal($id)
    {
        try {
            $manifest = Manifest::find($id);
            $label = $manifest->nohbl . '/' . $manifest->cont->nocontainer;
            return response()->json([
                'success'=>true,
                'data'=>$manifest,
                'label'=>$label,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success'=>false,
                'message'=>'Something Wrong' . $th->getMessage(),
            ]);
        }
    }

    public function lockSubmit(Request $request)
    {
        $manifest = Manifest::find($request->id);
        if ($manifest->tglrelease != null) {
           return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Opsss, manifest ini sudah keluar']);
        }
        // dd($request->hasFile('photos'));
        try {
            $log = Log::create([
                'ref_id'=> $manifest->id,
                'ref_type'=> 'LCL',
                'no_segel'=> $request->no_segel,
                'alasan'=> $request->alasan_segel,
                'keterangan'=>$request->keterangan,
                'action'=> 'lock',
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
                'uid'=> Auth::user()->id,
            ]);
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imageP2', $fileName, 'public'); 
                    $newPhoto = Photo::create([
                        'log_id' => $log->id,
                        'photo' => $fileName,
                    ]);
                }
            }

            $manifest->update([
               'alasan_segel' => $request->alasan_segel,
                'nosegel' => $request->no_segel,
                'flag_segel_merah'=>'Y',
                'uid_segel' => Auth::user()->id,
                'tanggal_segel_merah' => Carbon::now(),
            ]);
            
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data berhasil di buat']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Something Wrong!!'. $th->getMessage()]);
        }
    }

    public function listSegelIndex()
    {
        $data['title'] = 'List Manifest Segel Merah';
        $data['alasan'] = Alasan::where('type', 'unlock')->get();

        return view('beacukai.p2.listSegel', $data);
    }

    public function listSegelData(Request $request)
    {
        $manifest = Manifest::with(['user', 'cont', 'shipperM', 'packing', 'dokumen'])->whereNotNull('alasan_segel')->orderByRaw("
        CASE 
            WHEN status_bc = 'HOLDP2' THEN 1
            WHEN status_bc = 'HOLD' THEN 2
            WHEN status_bc = 'release' THEN 3
            WHEN status_bc IS NULL THEN 4
            ELSE 5
        END
    ")->get();

        return DataTables::of($manifest)
        ->addColumn('container', function($manifest){
            return $manifest->cont->nocontainer ?? '-';
        })
        ->addColumn('jobOrder', function($manifest){
            return $manifest->cont->job->nojoborder ?? '-';
        })
        ->addColumn('customer', function($manifest){
            return $manifest->customer->name ?? '-';
        })
        ->addColumn('gudangAsal', function($manifest){
            return $manifest->cont->job->PLP->gudang_asal ?? '-';
        })
        ->addColumn('eta', function($manifest){
            return $manifest->cont->job->eta ?? '-';
        })
        ->addColumn('shipper', function($manifest){
            return $manifest->shipperM->name ?? '-';
        })
        ->addColumn('packingName', function($manifest){
            return $manifest->packing->name ?? '-';
        })
        ->addColumn('packingCode', function($manifest){
            return $manifest->packing->code ?? '-';
        })
        ->addColumn('dokumenName', function($manifest){
            return $manifest->dokumen->name ?? '-';
        })
        ->make(true);
    }

    public function unlockSubmit(Request $request)
    {
        $manifest = Manifest::find($request->id);
        // dd($request->hasFile('photos'));
        try {
            DB::transaction(function() use ($manifest, $request)  {
                $log = Log::create([
                    'ref_id'=> $manifest->id,
                    'ref_type'=> 'LCL',
                    'no_segel'=> $request->no_segel,
                    'alasan'=> $request->alasan_lepas_segel,
                    'keterangan'=>$request->keterangan,
                    'action'=> 'unlock',
                    'created_at'=> Carbon::now(),
                    'updated_at'=> Carbon::now(),
                    'uid'=> Auth::user()->id,
                ]);
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $fileName = $photo->getClientOriginalName();
                        $photo->storeAs('imageP2', $fileName, 'public'); 
                        $newPhoto = Photo::create([
                            'log_id' => $log->id,
                            'photo' => $fileName,
                        ]);
                    }
                }
    
                $manifest->update([
                    'alasan_lepas_segel' => $request->alasan_lepas_segel,
                    'flag_segel_merah' => 'N',
                    'tanggal_lepas_segel'=> Carbon::now(),
                    'uid_lepas_segel'=> Auth::user()->id,
                ]);
            });
            
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data berhasil di buat']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Something Wrong!!'. $th->getMessage()]);
        }
    }

    public function logDetil($id)
    {
        $manifest = Manifest::find($id);
        $logs = Log::where('ref_id', $id)->get();
        $data['title'] = "Detil Segel Merah " . $manifest->nohbl . '/' . $manifest->cont->nocontainer;
        $data['manifest'] = $manifest;
        $data['lockSegel'] =  Log::where('ref_type', 'LCL')->where('ref_id', $id)->where('action', 'lock')->first();
        $data['photoLock'] = Photo::where('log_id', $data['lockSegel']->id)->get();
        $data['unlockSegel'] =  Log::where('ref_type', 'LCL')->where('ref_id', $id)->where('action', 'unlock')->first();
        $data['photoUnlock'] = Photo::where('log_id', $data['unlockSegel']->id??0)->get();
        
        return view('beacukai.p2.detilSegel', $data);
    }
    
    // FCL
    public function logDetilFCL($id)
    {
        $cont = ContF::find($id);
        $logs = Log::where('ref_type', 'FCL')->where('ref_id', $id)->get();
        $data['title'] = "Detil Segel Merah " . $cont->nobl . '/' . $cont->nocontainer;
        $data['cont'] = $cont;
        $data['lockSegel'] =  Log::where('ref_type', 'FCL')->where('ref_id', $id)->where('action', 'lock')->first();
        $data['photoLock'] = Photo::where('log_id', $data['lockSegel']->id)->get();
        $data['unlockSegel'] =  Log::where('ref_type', 'FCL')->where('ref_id', $id)->where('action', 'unlock')->first();
        $data['photoUnlock'] = Photo::where('log_id', $data['unlockSegel']->id??0)->get();
        
        return view('beacukai.p2.detilSegelFCL', $data);
    }

    public function indexListContainer()
    {
        $data['title'] = 'List Container';
        $data['alasan'] = Alasan::where('type', 'lock')->get();
        // dd($data['alasan']);
        return view('beacukai.p2.listContianer', $data);
    }

    public function listContainerData(Request $request)
    {
        $cont = ContF::with(['user'])->where('flag_segel_merah', '!=', 'Y')->whereNull('tglkeluar')->where(function ($query) {
            $query->where('status_bc', '!=', 'HOLDP2')
                  ->orWhereNull('status_bc');
        })->get();


        return DataTables::of($cont)
        ->addColumn('container', function($cont){
            return $cont->nocontainer ?? '-';
        })
        ->addColumn('jobOrder', function($cont){
            return $cont->job->nojoborder ?? '-';
        })
        ->addColumn('tpsAsal', function($cont){
            return $cont->job->sandar->kd_tps_asal ?? '-';
        })
        ->addColumn('eta', function($cont){
            return $cont->job->eta ?? '-';
        })
        ->addColumn('dokumenName', function($cont){
            return $cont->dokumen->name ?? '-';
        })
        ->addColumn('nobl', function($cont){
            return $cont->nobl ?? '-';
        })
        ->addColumn('tglBL', function($cont){
            return $cont->tgl_bl_awb ?? '-';
        })
        ->addColumn('tglmasuk', function($cont){
            return $cont->tglmasuk ?? 'Belum Masuk';
        })
        ->addColumn('jammasuk', function($cont){
            return $cont->jammasuk ?? 'Belum Masuk';
        })
        ->addColumn('tglkeluar', function($cont){
            return $cont->tglkeluar ?? 'Belum keluar';
        })
        ->addColumn('jamkeluar', function($cont){
            return $cont->jamkeluar ?? 'Belum keluar';
        })
        ->addColumn('customer', function($cont){
            return $cont->Customer->name ?? '-';
        })
        ->addColumn('kodeDok', function($cont){
            return $cont->dokumen->name ?? '-';
        })
        ->addColumn('noDok', function($cont){
            return $cont->no_dok ?? '-';
        })
        ->addColumn('tglDok', function($cont){
            return $cont->tgl_dok ?? '-';
        })
        ->addColumn('actionSegelMerah', function($cont){})
        ->make(true);
    }

    public function listContainerModal($id)
    {
        try {
            $cont = ContF::find($id);
            $label = $cont->nocontainer;
            return response()->json([
                'success'=>true,
                'data'=>$cont,
                'label'=>$label,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success'=>false,
                'message'=>'Something Wrong' . $th->getMessage(),
            ]);
        }
    }

    public function lockSubmitFCL(Request $request)
    {
        $cont = ContF::find($request->id);
        // dd($request->hasFile('photos'));
        if ($cont->tglkeluar != null) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'!!']);
        }
        try {
            DB::transaction(function() use($request, $cont) {
                $log = Log::create([
                    'ref_id'=> $cont->id,
                    'ref_type'=> 'FCL',
                    'no_segel'=> $request->no_segel,
                    'alasan'=> $request->alasan_segel,
                    'keterangan'=>$request->keterangan,
                    'action'=> 'lock',
                    'created_at'=> Carbon::now(),
                    'updated_at'=> Carbon::now(),
                    'uid'=> Auth::user()->id,
                ]);
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $fileName = $photo->getClientOriginalName();
                        $photo->storeAs('imageP2', $fileName, 'public'); 
                        $newPhoto = Photo::create([
                            'log_id' => $log->id,
                            'photo' => $fileName,
                        ]);
                    }
                }
    
                $cont->update([
                    'alasan_segel' => $request->alasan_segel,
                    'nosegel' => $request->no_segel,
                    'flag_segel_merah'=>'Y',
                    'uid_segel' => Auth::user()->id,
                    'tanggal_segel_merah' => Carbon::now(),
                ]);
            });
            
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data berhasil di buat']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Something Wrong!!'. $th->getMessage()]);
        }
    }

    public function listSegelIndexFCL()
    {
        $data['title'] = 'List Container Segel Merah';
        $data['alasan'] = Alasan::where('type', 'unlock')->get();

        return view('beacukai.p2.listSegelFCL', $data);
    }

    public function listSegelDataFCL(Request $request)
    {
        $cont = ContF::with(['user', 'job.sandar', 'customer', 'dokumen'])
            ->whereNotNull('alasan_segel')
            ->whereNull('tglkeluar')
            ->orderBy('flag_segel_merah', 'desc')
            ->get();

        return DataTables::of($cont)
        ->addColumn('action', function ($cont){
            return $cont->flag_segel_merah === 'Y' 
            ? '<button type="button" class="btn btn-info holdP2" data-id="'.$cont->id.'"><i class="fa fa-unlock"></i></button>' 
            : '';
        })
        ->addColumn('detil', function ($cont){
            $herf = '/bc-p2/detil-logFCL/' . $cont->id;
            return '<a href="javascript:void(0)" onclick="openWindow(\'' . $herf . '\')" class="btn btn-sm btn-info">
                        <i class="fa fa-eye"></i>
                    </a>';
        })
        ->addColumn('flagSegel', function ($cont){
            return $cont->flag_segel_merah;
        })
        ->addColumn('alasanSegel', function ($cont){
            return $cont->alasan_segel;
        })
        ->addColumn('alasanLepasSegel', function ($cont){
            return $cont->alasan_lepas_segel;
        })
        ->addColumn('container', function($cont){
            return $cont->nocontainer ?? '-';
        })
        ->addColumn('jobOrder', function($cont){
            return $cont->job->nojoborder ?? '-';
        })
        ->addColumn('tpsAsal', function($cont){
            return $cont->job->sandar->kd_tps_asal ?? '-';
        })
        ->addColumn('eta', function($cont){
            return $cont->job->eta ?? '-';
        })
        ->addColumn('dokumenName', function($cont){
            return $cont->dokumen->name ?? '-';
        })
        ->addColumn('nobl', function($cont){
            return $cont->nobl ?? '-';
        })
        ->addColumn('tglBL', function($cont){
            return $cont->tgl_bl_awb ?? '-';
        })
        ->addColumn('tglmasuk', function($cont){
            return $cont->tglmasuk ?? 'Belum Masuk';
        })
        ->addColumn('jammasuk', function($cont){
            return $cont->jammasuk ?? 'Belum Masuk';
        })
        ->addColumn('tglkeluar', function($cont){
            return $cont->tglkeluar ?? 'Belum keluar';
        })
        ->addColumn('jamkeluar', function($cont){
            return $cont->jamkeluar ?? 'Belum keluar';
        })
        ->addColumn('customer', function($cont){
            return $cont->Customer->name ?? '-';
        })
        ->addColumn('kodeDok', function($cont){
            return $cont->dokumen->name ?? '-';
        })
        ->addColumn('noDok', function($cont){
            return $cont->no_dok ?? '-';
        })
        ->addColumn('tglDok', function($cont){
            return $cont->tgl_dok ?? '-';
        })
        ->addColumn('actionSegelMerah', function($cont){})
        ->rawColumns(['action', 'detil'])
        ->make(true);
    }


    public function unlockSubmitFCL(Request $request)
    {
        $cont = ContF::find($request->id);
        // dd($request->hasFile('photos'));
        try {
            $log = Log::create([
                'ref_id'=> $cont->id,
                'ref_type'=> 'FCL',
                'no_segel'=> $request->no_segel,
                'alasan'=> $request->alasan_lepas_segel,
                'keterangan'=>$request->keterangan,
                'action'=> 'unlock',
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
                'uid'=> Auth::user()->id,
            ]);
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imageP2', $fileName, 'public'); 
                    $newPhoto = Photo::create([
                        'log_id' => $log->id,
                        'photo' => $fileName,
                    ]);
                }
            }

            $cont->update([
                'alasan_lepas_segel' => $request->alasan_lepas_segel,
                'flag_segel_merah' => 'N',
                'tanggal_lepas_segel'=> Carbon::now(),
                'uid_lepas_segel'=> Auth::user()->id,
            ]);
            
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data berhasil di buat']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Something Wrong!!'. $th->getMessage()]);
        }
    }
}
