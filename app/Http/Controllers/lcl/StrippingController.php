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
use App\Models\KeteranganPhoto as KP;
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
        $cont = Cont::with(['job', 'user'])->where('type', '=', 'lcl')->whereNot('tglmasuk', null)->orderBy('endstripping', 'asc')->get();
        
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
        $data['id'] = $id;
        $data['kets'] = KP::where('kegiatan', '=', 'stripping')->get();

        return view('lcl.realisasi.stripping.proses', $data);
        
    }

    public function prosesData($id, Request $request)
    {
        $manifest = Manifest::with(['shipperM', 'customer', 'packing'])->where('container_id', $id)->get();
        $herf = '/lcl/realisasi/stripping-photoManifest';

        return DataTables::of($manifest)
        ->addColumn('action', function($manifest)  use ($herf){
            return '<div class="button-container">
                        <button class="btn btn-warning editButton" data-id="'.$manifest->id.'"><i class="fa fa-pencil"></i></button>
                        <a href="javascript:void(0)" onclick="openWindow(\'' . $herf . $manifest->id . '\')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                    </div>';
        })
        ->addColumn('detil', function($manifest){
            if ($manifest->ijin_stripping == 'Y') {
                return '<span class="badge bg-light-success">Approved</span>';
            } else {
                return '<span class="badge bg-light-danger">Unapprove</span>';
            }
        })
        ->addColumn('status', function($manifest){
            if ($manifest->validasi == 'Y') {
                return '<span class="badge bg-light-success">Done</span>';
            } else {
                return '<span class="badge bg-light-info">on Progress</span>';
            }
        })
        ->addColumn('nohbl', function ($manifest) {
            return $manifest->nohbl ?? '-'; // Replace with proper column name
        })
        ->addColumn('tgl_hbl', function ($manifest) {
            return $manifest->tgl_hbl ?? '-'; // Replace with proper column name
        })
        ->addColumn('notally', function ($manifest) {
            return $manifest->notally ?? '-'; // Replace with proper column name
        })
        ->addColumn('shiper', function ($manifest) {
            return $manifest->shiperM->name ?? '-'; // Replace with proper column name
        })
        ->addColumn('customer', function ($manifest) {
            return $manifest->customer->name ?? '-'; // Replace with proper column name
        })
        ->addColumn('quantity', function ($manifest) {
            return $manifest->quantity ?? '-'; // Replace with proper column name
        })
        ->addColumn('packN', function ($manifest) {
            return $manifest->packing->name ?? '-'; // Replace with proper column name
        })
        ->addColumn('packC', function ($manifest) {
            return $manifest->packing->code ?? '-'; // Replace with proper column name
        })
        ->addColumn('descofgoods', function ($manifest) {
            $content = htmlspecialchars($manifest->descofgoods ?? '-', ENT_QUOTES, 'UTF-8'); // Escape special characters
        return '<div class="justify-text">' . $content . '</div>';
        })
        ->addColumn('weight', function ($manifest) {
            return $manifest->weight ?? '-'; // Replace with proper column name
        })
        ->addColumn('meas', function ($manifest) {
            return $manifest->meas ?? '-'; // Replace with proper column name
        })
        ->addColumn('startstripping', function ($manifest) {
            return $manifest->startstripping ?? '-'; // Replace with proper column name
        })
        ->addColumn('endstripping', function ($manifest) {
            return $manifest->endstripping ?? '-'; // Replace with proper column name
        })
        ->rawColumns(['descofgoods', 'action', 'detil', 'status'])
        ->make(true);
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
                    'startstripping' => $cont->startstripping,
                ]);
                    if ($mans->ijin_stripping == 'Y') {
                        $mans->update([
                            'tglstripping' => $cont->tglstripping,
                            'jamstripping' => $cont->jamstripping,
                            'endstripping' => $cont->endstripping,
                            'validasi' => 'Y',
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
                            'action' => 'stripping',
                            'detil' => $request->keteranganPhoto,
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
                if ($manifest->ijin_stripping == 'Y') {
                    if ($request->final_qty == $manifest->quantity) {
                        if ($manifest->kd_dok_inout == 1) {
                            $statusBc = 'release';
                            $alasanHold = $manifest->alasan_hold;
                        }else {
                            if ($manifest->release_bc_uid != null) {
                                $statusBc = 'release';
                                $alasanHold = $manifest->alasan_hold;
                            }else {
                                $statusBc = $manifest->status_bc;
                                $alasanHold = $manifest->alasan_hold;
                            }
                        }
                    }else {
                        $statusBc = 'HOLD';
                        $alasanHold = trim(($manifest->alasan_hold ? $manifest->alasan_hold . ', ' : '') . 'Quantity Real Berbeda');
                    }
                    // dd($statusBc);
                    $manifest->update([
                        'tglstripping' => $request->tglstripping,
                        'jamstripping' => $request->jamstripping,
                        'startstripping' => $request->startstripping,
                        'endstripping' => $request->endstripping,
                        'validasi' => 'Y',
                        'status_bc' => $statusBc,
                        'final_qty' => $request->final_qty,
                        'dg_label' => $request->dg_label,
                        'alasan_hold' => $alasanHold,
                    ]);
                }

                // dd($manifest);

                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $fileName = $photo->getClientOriginalName();
                        $photo->storeAs('imagesInt', $fileName, 'public'); 
                        $newPhoto = Photo::create([
                            'master_id' => $manifest->id,
                            'type' => 'manifest',
                            'action' => 'stripping',
                            'detil' => $request->keteranganPhoto,
                            'photo' => $fileName,
                        ]);
                    }
                }

                if ($manifest->ijin_stripping == 'Y') {
                    $type = 'success';
                    $message = 'Data Berhasil di update';
                }elseif ($manifest->ijin_stripping != 'Y' && $request->hasFile('photos')) {
                    $type = 'error';
                    $message = 'Belum mendapat ijin, hanya dapat melakukan update Foto';
                }else {
                    $type = 'error';
                    $message = 'Belum mendapat ijin';
                }
                return redirect()->back()->with('status', ['type'=>$type, 'message'=>$message]);
                
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
            $manifest = Manifest::where('container_id', $cont->id)->where('validasi', '!=', 'Y' )->get();
            if ($manifest->isNotEmpty()) {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Masih terdapat packing yang belum stripping !!']);
            }else {
                $cont->update([
                    'endstripping' => Carbon::now(),
                ]);
                foreach ($manifest as $mans) {
                    if ($mans->endstripping == null) {
                        $mans->update([
                            'endstripping' => $cont->endstripping
                        ]);
                    }
                }
                return redirect()->route('lcl.stripping.index')->with('status', ['type'=>'success', 'message'=>'Data berhasil di simpan !!']);
            }
        }
    }
}
