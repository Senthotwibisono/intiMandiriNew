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
use App\Models\Customer;
use App\Models\Packing;
use App\Models\Item;
use App\Models\Photo;
use DataTables;

class StrippingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('updateCont');
    }

    public function index()
    {
        $data['title'] = "Import LCL || Stripping";
        $data['conts'] = Cont::where('type', '=', 'lcl')->whereNot('tglmasuk', null)->where('tglkeluar', null )->orderBy('endstripping', 'asc')->get();
        
        return view('lcl.realisasi.stripping.index', $data);
    }

    public function indexData(Request $request)
    {
        $cont = Cont::with(['job', 'user'])->where('type', '=', 'lcl')->whereNot('tglmasuk', null)->where('tglkeluar', null )->orderBy('endstripping', 'asc')->get();
        
        return DataTables::of($cont)
        ->addColumn('detil', function($cont){
            if ($cont->status_ijin == 'Y') {
                return '<a href="/lcl/realisasi/stripping/proses-' . $cont->id . '" class="btn btn-warning"><i class="fa fa-pen"></i></a>';
            } else {
                return '<span class="badge bg-light-danger">Belum Mendapat Ijin Bea Cukai</span>';
            }
        })
        ->addColumn('status', function($cont){
            if ($cont->endstripping != null) {
                return '<span class="badge bg-light-danger">Finished</span>';
            }else {
                return '<span class="badge bg-light-success">On Proggress</span>';
            }
        })
        ->addColumn('kapal', function($cont){
            return $cont->job->Kapal->name ?? '-';
        })
        ->addColumn('no_plp', function($cont){
            return $cont->job->PLP->no_plp ?? '-';
        })
        ->addColumn('tgl_plp', function($cont){
            return $cont->job->PLP->tgl_plp ?? '-';
        })
        ->addColumn('kd_kantor', function($cont){
            return $cont->job->PLP->kd_kantor ?? '-';
        })
        ->addColumn('kd_tps', function($cont){
            return $cont->job->PLP->kd_tps ?? '-';
        })
        ->addColumn('kd_tps_asal', function($cont){
            return $cont->job->PLP->kd_tps_asal ?? '-';
        })
        ->addColumn('kd_tps_tujuan', function($cont){
            return $cont->job->PLP->kd_tps_tujuan ?? '-';
        })
        ->addColumn('nm_angkut', function($cont){
            return $cont->job->PLP->nm_angkut ?? '-';
        })
        ->addColumn('no_voy_flight', function($cont){
            return $cont->job->PLP->no_voy_flight ?? '-';
        })
        ->addColumn('no_surat', function($cont){
            return $cont->job->PLP->no_surat ?? '-';
        })
        ->addColumn('no_bc11', function($cont){
            return $cont->job->PLP->no_bc11 ?? '-';
        })
        ->addColumn('tgl_bc11', function($cont){
            return $cont->job->PLP->tgl_bc11 ?? '-';
        })
        ->rawColumns(['detil', 'status'])
        ->make(true);
    }

    public function proses($id)
    {
        $cont = Cont::where('id', $id)->first();
        $data['title'] = "Stripping Proccess Container || " . $cont->nocontainer;
        $data['manifest'] = Manifest::where('container_id', $id)->get();
        $data['cont'] = $cont;
        $data['validateManifest'] = $data['manifest']->where('validasi', '=', 'Y')->count();

        return view('lcl.realisasi.stripping.proses', $data);
        
    }

    public function updateCont(Request $request)
    {
        $cont = Cont::where('id', $request->id)->first();
        $startstripping = $request->tglstripping.' '.$request->jamstripping;
        // dd($startstripping);
        if ($cont) {
            try {
               $cont->update([
                'tglstripping' => $request->tglstripping,
                'jamstripping' => $request->jamstripping,
                'startstripping' => $startstripping,
                'endstripping' => $request->endstripping,
                'uidstripping' => Auth::user()->id,
               ]);

               $manifest = Manifest::where('container_id', $cont->id)->get();
               foreach ($manifest as $mans) {
                    $mans->update([
                        'tglstripping' => $cont->tglstripping,
                        'jamstripping' => $cont->jamstripping,
                        'startstripping' => $cont->startstripping,
                        'endstripping' => $cont->endstripping,
                    ]);
               }

               if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $fileName = $photo->getClientOriginalName();
                        $photo->storeAs('imagesInt', $fileName, 'public'); 
                        $newPhoto = Photo::create([
                            'master_id' => $cont->id,
                            'type' => 'lcl',
                            'action' => 'stripping',
                            'photo' => $fileName,
                        ]);
                    }
                }

               return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di Update']);
            } catch (\Throwable $e) {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, something wrong' . $e->getMessage()]);
            }
        }else {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, something wrong']);
        }
    }

    public function photoCont($id)
    {
        $cont = Cont::where('id', $id)->first();
        $data['title'] = "Photo Gate In Container - " . $cont->nocontainer;
        $data['item'] = $cont;
        $data['photos'] = Photo::where('master_id', $id)->where('type', '=', 'lcl')->where('action', '=', 'stripping')->get();
        // dd($data['photos']);
        return view('photo.index', $data);
    }

    public function store(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();
        if ($manifest) {
            try {
                $manifest->update([
                    'tglstripping' => $request->tglstripping,
                    'jamstripping' => $request->jamstripping,
                    'startstripping' => $request->startstripping,
                    'endstripping' => $request->endstripping,
                ]);

                // dd($manifest);

                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $fileName = $photo->getClientOriginalName();
                        $photo->storeAs('imagesInt', $fileName, 'public'); 
                        $newPhoto = Photo::create([
                            'master_id' => $manifest->id,
                            'type' => 'manifest',
                            'action' => 'stripping',
                            'photo' => $fileName,
                        ]);
                    }
                }
                return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di Update']);
                
            } catch (\Throwable $e) {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Something Wrong'. $e->getMessage()]);
            }
        }
    }

    public function photoManifest($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        // dd($manifest);
        $data['title'] = "Photo Stripping Manifest - " . $manifest->notally;
        $data['item'] = $manifest;
        $data['photos'] = Photo::where('master_id', $id)->where('type', '=', 'manifest')->where('action', '=', 'stripping')->get();
        // dd($data['photos']);
        return view('photo.index', $data);
    }

    public function end(Request $request)
    {
        $cont = Cont::where('id', $request->id)->first();
        if ($cont) {
            $manifest = Manifest::where('container_id', $cont->id)->whereNull('endstripping')->get();
            if ($manifest->isNotEmpty()) {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Masih terdapat packing yang belum stripping !!']);
            }else {
                $cont->update([
                    'endstripping' => Carbon::now(),
                ]);
                return redirect()->route('lcl.stripping.index')->with('status', ['type'=>'success', 'message'=>'Data berhasil di simpan !!']);
            }
        }
    }
}
