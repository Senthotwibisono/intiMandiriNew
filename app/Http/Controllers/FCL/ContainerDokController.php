<?php

namespace App\Http\Controllers\FCL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContainerFCL as Cont;

use App\Models\TpsPLP as PLP;
use App\Models\TpsPLPdetail as PLPdetail;
use App\Models\Consolidator;
use App\Models\TpsSPJM as SPJM;
use App\Models\TpsSPJMCont as SPJMcont;
use App\Models\TpsSPJMKms as SPJMkms;
use App\Models\TpsSPJMDok as SPJMdok;
use App\Models\TpsSPPBBC23 as BC23;
use App\Models\TpsSPPBBC23Cont as BC23Cont;
use App\Models\TpsSPPBBC23Kms as BC23Kms;
use App\Models\TpsSPPB as SPPB;
use App\Models\TpsSPPBCont as SPPBCont;
use App\Models\TpsSPPBKms as SPPBKms;
use App\Models\KodeDok as Kode;
use App\Models\TpsManual as Manual;
use App\Models\TpsManualCont as ManualCont;
use App\Models\TpsManualKms as ManualKms;
use App\Models\TpsPabean as Pabean;
use App\Models\TpsPabeanCont as PabeanCont;
use App\Models\TpsPabeanKms as PabeanKms;

use App\Models\JobOrder as Job;

use App\Models\JobOrderFCL as JobF;
use App\Models\Manifest;
use App\Models\Customer;
use App\Models\Packing;
use App\Models\Item;
use App\Models\Vessel;

use Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use SoapWrapper;
use DataTables;

class ContainerDokController extends Controller
{
    protected $wsdl;
    protected $user;
    protected $password;
    protected $kode;
    protected $response;

    public function __construct()
    {
        $this->middleware('auth');

        $this->wsdl = 'https://tpsonline.beacukai.go.id/tps/service.asmx?WSDL';
        $this->user = 'INTIMANDIRI';
        $this->password = 'INTIMANDIRI1';
        $this->kode = '1MUT';
        
    }

    public function index()
    {
        $data['title'] = 'List Container';

        $data['conts'] = Cont::get();
        $data['doks'] = Kode::orderBy('kode', 'asc')->get();

        return view('fcl.containerList.index', $data);
    }

    public function dataTable(Request $request)
    {
        $cont = Cont::get();

        return DataTables::of($cont)
        ->addColumn('edit', function($cont){
            return '<button class="btn btn-outline-warning editButton" data-id="'.$cont->id.'"><i class="fa fa-pen"></i></button>';
        })
        ->addColumn('photo', function($cont){
            return '<button class="btn btn-outline-info photoButton" data-id="'.$cont->id.'"><i class="fa fa-camera"></i></button>';
        })
        ->addColumn('nojob', function($cont){
            return $cont->job->nojoborder ?? '-';
        })
        ->addColumn('nombl', function($cont){
            return $cont->job->nombl ?? '-';
        })
        ->addColumn('nocontainer', function($cont){
            return $cont->nocontainer ?? '-';
        })
        ->addColumn('nobl', function($cont){
            return $cont->nobl ?? '-';
        })
        ->addColumn('tglBL', function($cont){
            return $cont->tgl_bl_awb ?? '-';
        })
        ->addColumn('nopol', function($cont){
            return $cont->nopol ?? '-';
        })
        ->addColumn('tglmasuk', function($cont){
            return $cont->tglmasuk ?? 'Belum Masuk';
        })
        ->addColumn('jammasuk', function($cont){
            return $cont->jammasuk ?? 'Belum Masuk';
        })
        ->addColumn('nopol_mty', function($cont){
            return $cont->nopol_mty ?? '-';
        })
        ->addColumn('tglkeluar', function($cont){
            return $cont->tglkeluar ?? 'Belum keluar';
        })
        ->addColumn('jamkeluar', function($cont){
            return $cont->jamkeluar ?? 'Belum keluar';
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
        ->rawColumns(['edit', 'photo'])
        ->make(true);
    }

    public function dataDok(Request $request)
    {
        $cont = Cont::where('id', $request->id)->first();

        $kdDok = $request->kd_dok;
        $tglDok = Carbon::parse($request->tgl_dok)->format('n/j/Y');
        $tglDokManual = Carbon::parse($request->tgl_dok)->format('d/m/Y');
        // var_dump($tglDok, $request->no_dok, $request->kd_dok);
        // die();
        if ($kdDok == 1) {
            $dok = SPPB::where('no_sppb', $request->no_dok)->where('tgl_sppb', $tglDok)->first();
            if ($dok) {
                $dokDetil = SPPBCont::where('sppb_id', $dok->id)->where('no_cont', $cont->nocontainer)->first();
                if ($dokDetil) {
                   
                    $cont->update([
                        'kd_dok_inout' => $kdDok,
                        'no_dok' => $request->no_dok,
                        'tgl_dok' => $request->tgl_dok,
                        'status_bc' => $statusBC,
                    ]);
    
                    return response()->json([
                        'success' => true,
                        'message' => 'Data di temukan',
                    ]);
                }else {
                    return response()->json([
                        'success' => false,
                        'message' => 'No HBL Berbeda',
                    ]);
                }
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak di temukan',
                ]);
            }
        }elseif ($kdDok == 2) {
            $dok = BC23::where('no_sppb', $request->no_dok)->where('tgl_sppb', $tglDok)->first();
            if ($dok) {
                $dokDetil = BC23Cont::where('sppb23_id', $dok->id)->where('no_cont', $cont->nocontainer)->first();
                if ($dokDetil) {
                    $cont->update([
                        'kd_dok_inout' => $kdDok,
                        'no_dok' => $request->no_dok,
                        'tgl_dok' => $request->tgl_dok,
                        'status_bc' => 'HOLD',
                    ]);
    
                    return response()->json([
                        'success' => true,
                        'message' => 'Data di temukan',
                    ]);
                }else {
                    return response()->json([
                        'success' => false,
                        'message' => 'No HBL Berbeda',
                    ]);
                }
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak di temukan',
                ]);
            }
        }else {
            $dok = Manual::where('kd_dok_inout', $kdDok)->where('no_dok_inout', $request->no_dok)->where('tgl_dok_inout', $tglDokManual)->first();
            if ($dok) {
                $dokDetil = ManualCont::where('manual_id', $dok->id)->where('no_cont', $cont->nocontainer)->first();
                if ($dokDetil) {
                    $cont->update([
                        'kd_dok_inout' => $kdDok,
                        'no_dok' => $request->no_dok,
                        'tgl_dok' => $request->tgl_dok,
                        'status_bc' => 'HOLD',
                    ]);
    
                    return response()->json([
                        'success' => true,
                        'message' => 'Data di temukan',
                    ]);
                }
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak di temukan',
                ]);
            }
        }
    }

    public function updateCont(Request $request)
    {
        try {
            $cont = Cont::find($request->id)->update([
                'size' => $request->size,
                'weight' => $request->weight,
                'tglmasuk' => $request->tglmasuk,
                'jammasuk' => $request->jammasuk,
                'ctr_type' => $request->ctr_type,
                'kd_dok' => $request->kd_dok,
                'no_dok' => $request->no_dok,
                'tgl_dok' => $request->tgl_dok,
            ]);

            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di simpan']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Something Wrong: '.$th->getessage()]);
        }
    }
}
