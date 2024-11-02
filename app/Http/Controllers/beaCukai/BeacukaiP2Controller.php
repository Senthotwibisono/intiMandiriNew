<?php

namespace App\Http\Controllers\beaCukai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\LogSegel as Log;
use App\Models\AlasanSegel as Alasan;
use App\Models\PhotoSegel as Photo;
use App\Models\Manifest;

use Auth;
use carbon\Carbon;
use DataTables;
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
        $log = Log::with(['user', 'manifest'])->orderBy('created_at', 'desc')->get();

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

    public function listManifestIndex()
    {
        $data['title'] = 'List Manifest';
        $data['alasan'] = Alasan::where('type', 'lock')->get();
        // dd($data['alasan']);
        return view('beacukai.p2.listManifest', $data);
    }

    public function listManifestData(Request $request)
    {
        $manifest = Manifest::with(['user', 'cont', 'shipperM', 'packing', 'dokumen'])->whereNull('tglbuangmty')->where(function ($query) {
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
                'status_bc'=>'HOLDP2',
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
                'status_bc'=>'HOLD',
            ]);
            
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
        $data['lockSegel'] =  Log::where('ref_id', $id)->where('action', 'lock')->first();
        $data['photoLock'] = Photo::where('log_id', $data['lockSegel']->id)->get();
        $data['unlockSegel'] =  Log::where('ref_id', $id)->where('action', 'unlock')->first();
        $data['photoUnlock'] = Photo::where('log_id', $data['unlockSegel']->id)->get();
        
        return view('beacukai.p2.detilSegel', $data);
    }
}
