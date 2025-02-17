<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
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
use App\Models\Container as Cont;
use App\Models\JobOrderFCL as JobF;
use App\Models\ContainerFCL as ContF;
use App\Models\Manifest;
use App\Models\Customer;
use App\Models\Packing;
use App\Models\Item;
use App\Models\Vessel;
use App\Models\LokasiSandar;

use Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use SoapWrapper;
use DataTables;

class DokumenController extends Controller
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

    public function plpIndex()
    {
        $data['title'] = "Dokumen PLP Tujuan";
        
        return view('dokumen.plp.index', $data);
    }

    public function plpData(Request $request)
    {
        $dokumen = PLP::with('user')->get();
        return DataTables::of($dokumen)->make(true);
    }

    public function plpDetail($id)
    {
        $plp = PLP::where('id', $id)->first();
        $detail = PLPdetail::where('plp_id', $id)->get();
        $data['forwardings'] = Customer::get();

        $data['title'] = "Detail Respon PLP " . $plp->no_surat;
        $data['plp'] = $plp;
        $data['details'] = $detail;

        return view('dokumen.plp.detail', $data);
    }

    public function plpCont($id)
    {
        $cont = PLPdetail::where('id', $id)->first();
        if ($cont) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $cont,
            ]);
        }
    }

    public function plpUpdateDetail(Request $request)
    {
        $plp = PLP::where('id', $request->id)->first();
        if ($plp) {
            $plp->update([
                'kd_kantor'=>$request->kd_kantor,
                'no_surat'=>$request->no_surat,
                'tgl_bc11'=> Carbon::parse($request->tgl_bc11)->format('Ymd'),
                'no_bc11'=>$request->no_bc11,
                'no_plp'=>$request->no_plp,
                'tgl_plp'=>Carbon::parse($request->tgl_plp)->format('Ymd'),
                'kd_tps_asal'=>$request->kd_tps_asal,
                'yor_tps_asal'=>$request->yor_tps_asal,
                'gudang_tujuan'=>$request->gudang_tujuan,
                'alasan_reject'=>$request->alasan_reject,
                'alasan'=>$request->alasan,
                'lampiran'=>$request->lampiran,
                'no_voy_flight'=>$request->no_voy_flight,
                'call_sign'=>$request->call_sign,
                'tgl_tiba'=>Carbon::parse($request->tgl_tiba)->format('Ymd')
            ]);

            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data berhasil diupdate!!']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Something Wrong!!']);
        }
    }

    public function plpUpdateCont(Request $request)
    {
        $cont = PLPdetail::where('id', $request->id)->first();
        if ($cont) {
            $cont->update([
                'jns_cont' => $request->jns_cont,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data berhasil diupdate!!']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Something Wrong!!']);
        }
    }

    public function GetResponPLP_onDemand(Request $request)
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnlineSoap')
                ->wsdl($this->wsdl)
                ->trace(true)   
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)  
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                    
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'No_plp' => $request->no_plp,
            'Tgl_plp' => Carbon::parse($request->tgl_plp)->format('dmY'),
			'KdGudang' => $request->kode_gudang,			
        ];

        // dd($data);
        
        try{
            \SoapWrapper::service('TpsOnlineSoap', function ($service) use ($data) {        
                $this->response = $service->call('GetResponPlp_onDemands', [$data])->GetResponPlp_onDemandsResult;      
            });
        }catch (\SoapFault $e){
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
        }
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml  || !$xml->children()){
           return back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' .  $this->response]);
        }

        // $groups = [];
        // foreach($xml->children() as $child) {
        //     $groups[] = $child;
        // }

        // dd($groups);
       
        
        foreach($xml->children() as $child) {
            foreach($child as $key => $value) {
                if($key == 'header' || $key == 'HEADER'){
                    $header = $value;
                }else{
                    foreach ($value as $detail):
                        $details[] = $detail;
                    endforeach;
                }
            }
            // dd($xml);
            // Old Checking
            $oldPLP = PLP::where('no_plp', $header->NO_PLP)->where('tgl_plp', $header->TGL_PLP)->first();
            if ($oldPLP) {
                return back()->with('status', ['type' => 'error', 'message' => 'Error importing data: Data Sudah Ada!!']);
            }
    
            // Inserrt Data Header
            $consolidator = Consolidator::first();
    
            $plp = PLP::create([
                'tgl_upload' => Carbon::now()->format('Ymd'), 
                'upload_date' => Carbon::today()->format('Y-m-d'), 
                'upload_time' => Carbon::now()->format('H:i:s'),
                'kd_kantor'=>$header->KD_KANTOR,
                'kd_tps'=> $this->kode,
                'kd_tps_asal'=>$header->KD_TPS_ASAL,
                'gudang_tujuan'=>$header->GUDANG_TUJUAN,
                'no_plp'=>$header->NO_PLP,
                'tgl_plp'=>$header->TGL_PLP,
                'call_sign'=>$header->CALL_SIGN,
                'nm_angkut'=>$header->NM_ANGKUT,
                'no_voy_flight'=>$header->NO_VOY_FLIGHT,
                'tgl_tiba'=>$header->TGL_TIBA,
                'no_surat'=>$header->NO_SURAT,
                'tgl_surat'=>$header->TGL_SURAT,
                'no_bc11'=>$header->NO_BC11,
                'tgl_bc11'=>$header->TGL_BC11,
                'uid'=> Auth::user()->id,
                'consolidator_id'=>$consolidator->id,
                'namaconsolidator'=>$consolidator->namaconsolidator,
                'kd_tps_tujuan'=>$header->KD_TPS_TUJUAN,
                'gudang_asal'=>$header->GUDANG_ASAL,
                'ref_number'=>$header->REF_NUMBER,
            ]);
    
            foreach ($details as $detail) {
               $cont = PLPdetail::create([
                'plp_id' =>$plp->id,
                'tgl_upload' =>$plp->tgl_upload,
                'no_plp' =>$plp->no_plp,
                'tgl_plp' =>$plp->tgl_plp,
                'no_cont' =>$detail->NO_CONT,
                'uk_cont' =>$detail->UK_CONT,
                'jns_cont' =>$detail->JNS_CONT,
                'no_bc11' =>$plp->no_bc11,
                'tgl_bc11' =>$plp->tgl_bc11,
                'no_pos_bc11' =>$detail->NO_POS_BC11,
                'consignee' =>$detail->CONSIGNEE,
                'jns_kms' =>$detail->jns_kms ?? NULL,
                'jml_kms' =>$detail->jml_kms ?? NULL,
                'no_bl_awb' =>$detail->NO_BL_AWB ?? NULL,
                'tgl_bl_awb' =>$detail->TGL_BL_AWB ?? NULL,
                'flag_spk' =>$plp->flag_spk,
               ]);
            }
        }

        // $tstxml = $xml->children();
        // dd( $tstxml, $xml->RESPONPLP);

        
        return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data ditemukan']);
    }

    public function GetResponPLP_Tujuan()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnlineSoap')
                ->wsdl($this->wsdl)
                ->trace(true)   
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)  
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                   
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_asp' => $this->kode
        ];
        // var_dump($data);
        // die();
        try{
            \SoapWrapper::service('TpsOnlineSoap', function ($service) use ($data) {        
                $this->response = $service->call('GetResponPLP_Tujuan', [$data])->GetResponPLP_TujuanResult;      
            });
        }catch (\SoapFault $e){
            // return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage(),
            ]);
        }
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        // var_dump($this->response, $xml);
        // die();
        if(!$xml  || !$xml->children()){
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $this->response,
            ]);
        }
        
        $header = array();
        $details = [];
        $groups = [];
        foreach($xml->children() as $child) {
            $groups[] = $child;
        }

        // dd($groups[]);

        foreach ($groups as $group) {
            $header = $group->header ?? $group->HEADER;
            $oldPLP = PLP::where('no_plp', $header->NO_PLP)->where('tgl_plp', $header->TGL_PLP)->first();
            if (!$oldPLP) {
                $consolidator = Consolidator::first();
                $plp = PLP::create([
                    'tgl_upload' => Carbon::now()->format('Ymd'), 
                    'upload_date' => Carbon::today()->format('Y-m-d'), 
                    'upload_time' => Carbon::now()->format('H:i:s'),
                    'kd_kantor'=>$header->KD_KANTOR,
                    'kd_tps'=> $this->kode,
                    'kd_tps_asal'=>$header->KD_TPS_ASAL,
                    'gudang_tujuan'=>$header->GUDANG_TUJUAN,
                    'no_plp'=>$header->NO_PLP,
                    'tgl_plp'=>$header->TGL_PLP,
                    'call_sign'=>$header->CALL_SIGN,
                    'nm_angkut'=>$header->NM_ANGKUT,
                    'no_voy_flight'=>$header->NO_VOY_FLIGHT,
                    'tgl_tiba'=>$header->TGL_TIBA,
                    'no_surat'=>$header->NO_SURAT,
                    'tgl_surat'=>$header->TGL_SURAT,
                    'no_bc11'=>$header->NO_BC11,
                    'tgl_bc11'=>$header->TGL_BC11,
                    'uid'=> Auth::user()->id,
                    'consolidator_id'=>$consolidator->id,
                    'namaconsolidator'=>$consolidator->namaconsolidator,
                    'kd_tps_tujuan'=>$header->KD_TPS_TUJUAN,
                    'gudang_asal'=>$header->GUDANG_ASAL,
                    'ref_number'=>$header->REF_NUMBER,
                ]);
                $detil[] = $group->DETIL ?? $group->detil;
                foreach ($detil as $detail) {
                    $cont = PLPdetail::create([
                        'plp_id' =>$plp->id,
                        'tgl_upload' =>$plp->tgl_upload,
                        'no_plp' =>$plp->no_plp,
                        'tgl_plp' =>$plp->tgl_plp,
                        'no_cont' =>$detail->NO_CONT,
                        'uk_cont' =>$detail->UK_CONT,
                        'jns_cont' =>$detail->JNS_CONT,
                        'no_bc11' =>$plp->no_bc11,
                        'tgl_bc11' =>$plp->tgl_bc11,
                        'no_pos_bc11' =>$detail->NO_POS_BC11,
                        'consignee' =>$detail->CONSIGNEE,
                        'jns_kms' =>$detail->jns_kms ?? NULL,
                        'jml_kms' =>$detail->jml_kms ?? NULL,
                        'no_bl_awb' =>$detail->NO_BL_AWB ?? NULL,
                        'tgl_bl_awb' =>$detail->TGL_BL_AWB ?? NULL,
                        'flag_spk' =>$plp->flag_spk,
                       ]);
                }
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil di Simpan',
        ]);
    }

    // SPJM
    public function spjmIndex()
    {
        $data['title'] = "Dokumen SPJM";
        return view('dokumen.spjm.index', $data);
    }

    public function spjmData(Request $request)
    {
        $dokumen = SPJM::get();
        return DataTables::of($dokumen)->make(true);
    }

    public function GetSPJM_onDemand(Request $request)
    {     
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetSPJM_onDemand')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'noPib' => $request->no_spjm,
            'tglPib' => Carbon::parse($request->tgl_spjm)->format('dmY')
        ];
        // dd($data);
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetSPJM_onDemand', function ($service) use ($data) {        
            $this->response = $service->call('GetSPJM_onDemand', [$data])->GetSPJM_onDemandResult;      
        });
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
            return back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' .  $this->response]);
        }

        $header = null;
        $kms = null;
        $dok = null;
        $cont = null;
        
        foreach($xml->children() as $child) {
            foreach($child as $key => $value) {
                if($key == 'header' || $key == 'HEADER'){
                    $header = $value;
                }else{
                    foreach ($value as $key => $value):
                        if($key == 'kms' || $key == 'KMS'):
                            $kms [] = $value;
                        elseif($key == 'dok' || $key == 'DOK'):
                            $dok [] = $value;
                        elseif($key == 'cont' || $key == 'CONT'):
                            $cont [] = $value;
                        endif;
                    endforeach;
                }
            }
            // dd($header, $kms, $cont, $dok);
            // $oldSPJM = SPJM::where('no_pib', $header->NO_PIB)->where('tgl_pib', $header->TGL_PIB)->first();
            // if ($oldSPJM) {
            //     return back()->with('status', ['type' => 'error', 'message' => 'Data sudah tersedia']);
            // }

            $spjm = SPJM::create([
                'car'=>$header->CAR,
                'kd_kantor'=>$header->KD_KANTOR,
                'tgl_pib'=>$header->TGL_PIB,
                'no_pib'=>$header->NO_PIB,
                'no_spjm'=>$header->NO_PIB,
                'tgl_spjm'=>$header->TGL_SPJM,
                'npwp_imp'=>$header->NPWP_IMP,
                'nama_imp'=>$header->NAMA_IMP,
                'npwp_ppjk'=>$header->NPWP_PPJK,
                'nama_ppjk'=>$header->NAMA_PPJK,
                'gudang'=>$header->GUDANG,
                'jml_cont'=>$header->JML_CONT,
                'no_bc11'=>$header->NO_BC11,
                'tgl_bc11'=>$header->TGL_BC11,
                'no_pos_bc11'=>$header->NO_POS_BC11,
                'fl_karantina'=>$header->FL_KARANTINA,
                'nm_angkut'=>$header->NM_ANGKUT,
                'no_voy_flight'=>$header->NO_VOY_FLIGHT,
                'tgl_upload'=>Carbon::today()->format('Y-m-d'),
                'jam_upload'=>Carbon::now()->format('H:i:s'),
            ]);

            if ($kms) {
                foreach ($kms as $detail) {
                    $newKms = SPJMkms::create([
                        'spjm_id'=>$spjm->id,
                        'car'=>$detail->CAR,
                        'jns_kms'=>$detail->JNS_KMS,
                        'merk_kms'=>$detail->MERK_KMS,
                        'jml_kms'=>$detail->JML_KMS,
                    ]);
                }
            }
            if ($cont) {
               foreach ($cont as $detail) {
                    $newCont = SPJMcont::create([
                        'spjm_id'=>$spjm->id,
                        'car'=>$detail->CAR,
                        'no_cont'=>$detail->NO_CONT,
                        'size'=>$detail->SIZE,
                    ]);
               }
            }
            if ($dok) {
               foreach ($dok as $detail) {
                    $newDok = SPJMdok::create([
                        'spjm_id'=>$spjm->id,
                        'car'=>$detail->CAR,
                        'jns_dok'=>$detail->JNS_DOK,
                        'no_dok'=>$detail->NO_DOK,
                        'tgl_dok'=>$detail->TGL_DOK,
                    ]);
               }
            }
        }

        // dd($xml ,$header, $kms, $dok, $cont);
            return back()->with('status', ['type' => 'success', 'message' => 'Data ditemukan']);
    }

    public function GetSPJM()
    {     
        
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetSPJM')
                ->wsdl($this->wsdl)
                ->trace(true)                      
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_Tps' => $this->kode
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetSPJM', function ($service) use ($data) {        
            $this->response = $service->call('GetSPJM', [$data])->GetSPJMResult;      
        });
        
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
        //    return back()->with('error', $this->response);
           return response()->json([
            'success' => false,
            'message' => 'Error : ' . $this->response,
           ]);
        }
        
        $header = array();
        $kms = null;
        $dok = null;
        $cont = null;
        $groups = [];

        foreach ($xml->children() as $child) {
            $groups[] = $child;
        }
        foreach ($groups as $group) {
            $header = $group->header ?? $group->HEADER;
            $oldSPJM = SPJM::where('no_pib', $header->NO_PIB)->where('tgl_pib', $header->TGL_PIB)->first();
            if (!$oldSPJM) {
                $spjm = SPJM::create([
                    'car'=>$header->CAR,
                    'kd_kantor'=>$header->KD_KANTOR,
                    'tgl_pib'=>$header->TGL_PIB,
                    'no_pib'=>$header->NO_PIB,
                    'no_spjm'=>$header->NO_PIB,
                    'tgl_spjm'=>$header->TGL_SPJM,
                    'npwp_imp'=>$header->NPWP_IMP,
                    'nama_imp'=>$header->NAMA_IMP,
                    'npwp_ppjk'=>$header->NPWP_PPJK,
                    'nama_ppjk'=>$header->NAMA_PPJK,
                    'gudang'=>$header->GUDANG,
                    'jml_cont'=>$header->JML_CONT,
                    'no_bc11'=>$header->NO_BC11,
                    'tgl_bc11'=>$header->TGL_BC11,
                    'no_pos_bc11'=>$header->NO_POS_BC11,
                    'fl_karantina'=>$header->FL_KARANTINA,
                    'nm_angkut'=>$header->NM_ANGKUT,
                    'no_voy_flight'=>$header->NO_VOY_FLIGHT,
                    'tgl_upload'=>Carbon::today()->format('Y-m-d'),
                    'jam_upload'=>Carbon::now()->format('H:i:s'),
                ]);
                $detil[]  = $group->DETIL ?? $group->detil;       
                foreach ($group->DETIL->CONT as $detailCont) {
                    $newCont = SPJMcont::create([
                        'spjm_id'=>$spjm->id,
                        'car'=>$detailCont->CAR,
                        'no_cont'=>$detailCont->NO_CONT,
                        'size'=>$detailCont->SIZE,
                    ]);
                }    
                foreach ($group->DETIL->KMS as $detailKMS) {
                    $newKms = SPJMkms::create([
                        'spjm_id'=>$spjm->id,
                        'car'=>$detailKMS->CAR,
                        'jns_kms'=>$detailKMS->JNS_KMS,
                        'merk_kms'=>$detailKMS->MERK_KMS,
                        'jml_kms'=>$detailKMS->JML_KMS,
                    ]);          
                }
                foreach ($group->DETIL->DOK as $detailDok) {
                    $newDok = SPJMdok::create([
                        'spjm_id'=>$spjm->id,
                        'car'=>$detailDok->CAR,
                        'jns_dok'=>$detailDok->JNS_DOK,
                        'no_dok'=>$detailDok->NO_DOK,
                        'tgl_dok'=>$detailDok->TGL_DOK,
                    ]);         
                }
            }
        }
        return response()->json([
         'success' => true,
         'message' => 'Data Berhasil di Simpan',
        ]);
        
    }
    
    // SPPBC23
    public function bc23Index()
    {
        $data['title'] = "Dokumen SPPB BC23";
        return view('dokumen.bc23.index', $data);
    }

    public function bc23Data(Request $request)
    {
        $dokumen = BC23::get();

        return DataTables::of($dokumen)->make(true);
    }
    
    public function bc23Detail($id)
    {
        $bc23 = BC23::where('id', $id)->first();
        $data['title'] = "Detail SPPB BC23 ". $bc23->no_sppb;
        $data['dok'] = $bc23;
        $data['conts'] = BC23Cont::where('sppb23_id', $id)->get();
        $data['kmss'] = BC23Kms::where('sppb23_id', $id)->get();

        return view('dokumen.bc23.detail', $data);
    }

    public function bc23ContainerList($id)
    {
        try {
            $bc23 = BC23::find($id);
            if (!$bc23) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan.',
                ]);
            }
        
            $noDokumen = $bc23->no_sppb ?? '';
            $contDok = BC23Cont::where('sppb23_id', $id)->get();
            $cont = ContF::where('no_dok', $bc23->no_sppb)->get();
        
            $data = $contDok->map(function ($item) use ($cont) {
                $contReal = $cont->where('nocontainer', $item->no_cont)->first();
                if ($contReal) {
                    $sizeCont = $contReal->size ?? '';
                    $tglMasuk = $contReal->tglmasuk ?? 'Belum Masuk';
                    $tglKeluar = $contReal->tglkeluar ?? 'Belum Masuk';
                }else {
                    $sizeCont = 'Data Container Tidak Ditemukan';
                    $tglMasuk = 'Data Container Tidak Ditemukan';
                    $tglKeluar = 'Data Container Tidak Ditemukan';
                }
                return [
                    'noCont' => $item->no_cont ?? '',
                    'ukuranDok' => $item->size ?? '',
                    'sizeCont' =>  $sizeCont,
                    'tglMasuk' =>  $tglMasuk,
                    'tglKeluar' => $tglKeluar,
                ];
            });
        
            return response()->json([
                'success' => true,
                'noDokumen' => $noDokumen,
                'data' => $data
            ]);
        
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }



    public function bc23UpdateDetail(Request $request)
    {
        $bc23 = BC23::where('id', $request->id)->first();
        if ($bc23) {
            $bc23->update([
                'car'=>$request->car,
                'no_sppb'=>$request->no_sppb,
                'tgl_sppb'=>$request->tgl_sppb,
                'nojoborder'=>$request->nojoborder,
                'kd_kantor_pengawas'=>$request->kd_kantor_pengawas,
                'kd_kantor_bongkar'=>$request->kd_kantor_bongkar,
                'no_pib'=>$request->no_pib,
                'tgl_pib'=>$request->tgl_pib,
                'nama_imp'=>$request->nama_imp,
                'npwp_imp'=>$request->npwp_imp,
                'alamat_imp'=>$request->alamat_imp,
                'npwp_ppjk'=>$request->npwp_ppjk,
                'nama_ppjk'=>$request->nama_ppjk,
                'alamat_ppjk'=>$request->alamat_ppjk,
                'nm_angkut'=>$request->nm_angkut,
                'no_voy_flight'=>$request->no_voy_flight,
                'bruto'=>$request->bruto,
                'netto'=>$request->netto,
                'gudang'=>$request->gudang,
                'status_jalur'=>$request->status_jalur,
                'jml_cont'=>$request->jml_cont,
                'no_bc11'=>$request->no_bc11,
                'tgl_bc11'=>$request->tgl_bc11,
                'no_pos_bc11'=>$request->no_pos_bc11,
                'no_bl_awb'=>$request->no_bl_awb,
                'tgl_bl_awb'=>$request->tgl_bl_awb,
                'no_master_bl_awb'=>$request->no_master_bl_awb,
                'tgl_master_bl_awb'=>$request->tgl_master_bl_awb,
            ]);
            return back()->with('status', ['type' => 'success', 'message' => 'Data ditemukan']);
        }else {
            return back()->with('status', ['type' => 'error', 'message' => 'Something Wrong']);
        }

    }

    public function GetImpor_SPPBBC23_OnDemand(Request $request)
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetSppb_Bc23')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                                                         
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'No_Sppb' => $request->no_sppb, //063484/KPU.01/2017	
			'Tgl_Sppb' => Carbon::parse($request->tgl_sppb)->format('dmY'), //09022017
            'NPWP_Imp' => $request->npwp_imp //033153321035000
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetSppb_Bc23', function ($service) use ($data) {        
            $this->response = $service->call('GetSppb_Bc23', [$data])->GetSppb_Bc23Result;      
        });

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' .  $this->response]);
        }
        
        $header = null;
        $kms = null;
        $cont = null;
        
        foreach($xml->children() as $child) {
            foreach($child as $key => $value) {
                if($key == 'header' || $key == 'HEADER'){
                    $header = $value;
                }else{
                    foreach ($value as $key => $value):
                        if($key == 'kms' || $key == 'KMS'):
                            $kms [] = $value;
                        elseif($key == 'cont' || $key == 'CONT'):
                            $cont [] = $value;
                        endif;
                    endforeach;
                }
            }
        }
        // dd($xml, $xml->children(), $cont, $kms);
// dd($cont);
      if ($header) {
            $oldBC23 = BC23::where('car', $header->CAR)->first();
            // if ($oldBC23) {
            //     return back()->with('status', ['type' => 'error', 'message' => 'Data sudah tersedia']);
            // }

            $bc23 = BC23::create([
                'car' =>$header->CAR ?? null,
                'no_sppb' =>$header->NO_SPPB ?? null,
                'tgl_sppb' =>$header->TGL_SPPB ?? null,
                'nojoborder' =>$header->NOJOBORDER ?? null,
                'kd_kantor_pengawas' =>$header->KD_KANTOR_PENGAWAS ?? null,
                'kd_kantor_bongkar' =>$header->KD_KANTOR_BONGKAR ?? null,
                'no_pib' =>$header->NO_PIB ?? null,
                'tgl_pib' =>$header->TGL_PIB ?? null,
                'nama_imp' =>$header->NAMA_IMP ?? null,
                'npwp_imp' =>$header->NPWP_IMP ?? null,
                'alamat_imp' =>$header->ALAMAT_IMP ?? null,
                'npwp_ppjk' =>$header->NPWP_PPJK ?? null,
                'nama_ppjk' =>$header->NAMA_PPJK ?? null,
                'alamat_ppjk' =>$header->ALAMAT_PPJK ?? null,
                'nm_angkut' =>$header->NM_ANGKUT ?? null,
                'no_voy_flight' =>$header->NO_VOY_FLIGHT ?? null,
                'bruto' =>$header->BRUTO ?? null,
                'netto' =>$header->NETTO ?? null,
                'gudang' =>$header->GUDANG ?? null,
                'status_jalur' =>$header->STATUS_JALUR ?? null,
                'jml_cont' =>$header->JML_CONT ?? null,
                'no_bc11' =>$header->NO_BC11 ?? null,
                'tgl_bc11' =>$header->TGL_BC11 ?? null,
                'no_pos_bc11' =>$header->NO_POS_BC11 ?? null,
                'no_bl_awb' =>$header->NO_BL_AWB ?? null,
                'tgl_bl_awb' =>$header->TGL_BL_AWB ?? null,
                'no_master_bl_awb' =>$header->NO_MASTER_BL_AWB ?? null,
                'tgl_master_bl_awb' =>$header->TGL_MASTER_BL_AWB ?? null,
                'tgl_upload'=>Carbon::today()->format('Y-m-d'),
                'jam_upload'=>Carbon::now()->format('H:i:s'),
            ]);

            if ($kms) {
                foreach ($kms as $detail) {
                    // dd($kms, $detail);
                    $bcKMS = BC23Kms::create([
                        'sppb23_id'=>$bc23->id ?? null,
                        'car'=>$detail->CAR,
                        'jns_kms'=>$detail->JNS_KMS,
                        'merk_kms'=>$detail->MERK_KMS,
                        'jml_kms'=>$detail->JML_KMS,
                    ]);

                    if ($bc23->jml_cont == 0) {
                        $manifest = Manifest::where('nohbl', $bc23->no_bl_awb)->where('tglbuangmty', null)->first();
                        if ($manifest) {
                            $alasanBasic = "Bukan Dokumen SPPB 2.0";
                            $alasanCust = null;
                            $alasanKemas = null;
                            $alasanJml = null;
                            // alasanCust 
                            if ($manifest->customer->name != $bc23->nama_imp || $manifest->customer->npwp != $bc23->npwp_imp) {
                                $alasanCust = "Data Importir Berbeda";
                            }
    
                            // Alasan Kemas
                            if ($manifest->packing->code != $bcKMS->jns_kms) {
                                $alasanKemas = "Jenis Kemas Berbeda";
                            }
    
                            if ($manifest->quantity != $bcKMS) {
                                $alasanJml = "Quantity Berbeda";
                            }
    
                            $alasanFinal = $alasanBasic . ', ' . $alasanCust . ', ' . $alasanKemas . ', ' . $alasanJml;
    
                            $manifest->update([
                                'kd_dok_inout' => 2,
                                'no_dok' => $bc23->no_sppb,
                                'tgl_dok' => Carbon::createFromFormat('d/m/Y', $bc23->tgl_sppb)->format('Y-m-d'),
                                'status_bc' => 'HOLD',
                                'alasan_hold' => $alasanFinal,
                            ]);
                        }
                    }
                }
            }

            if ($cont) {
                foreach ($cont as $detail) {
                    $bcCont = BC23Cont::create([
                        'sppb23_id' => $bc23->id,
                        'car' => $detail->CAR,
                        'no_cont' => $detail->NO_CONT,
                        'size' => $detail->SIZE,
                        'jns_muat' => $detail->JNS_MUAT,
                    ]);
                    
                    if ($bc23->jml_cont > 0) {
                        $contF = ContF::whereNull('tglkeluar')->where('nocontainer', $detail->NO_CONT)->first();
                        if ($contF) {
                            if ($contF->size != $detail->SIZE) {
                                $alasanSize = '& Ukuran Fisik Size Berbeda';
                            }else {
                                $alasanSize = null;
                            }
    
                            $alasanFinal = 'Bukan Dokumen SPPB. ' . $alasanFinal;
                            $cust = Customer::where('name', $bc23->nama_imp)->first();
                            if ($cust) {
                                $cust->update([
                                    'name' => $bc23->nama_imp,
                                    'npwp' => $bc23->npwp_imp,
                                    'alamat' => $bc23->alamat_imp,
                                ]);
                            }
                            $newCust = null;
                            if (!$cust && $bc23->nama_imp != null) {
                                $newCust = Customer::create([
                                    'name' => $bc23->nama_imp,
                                    'npwp' => $bc23->npwp_imp,
                                    'alamat' => $bc23->alamat_imp,
                                ]);
                            }
                            $contF->update([
                                'kd_dok_inout' => 2,
                                'no_dok' => $bc23->no_sppb,
                                'tgl_dok' => Carbon::createFromFormat('d/m/Y', $bc23->tgl_sppb)->format('Y-m-d'),
                                'status_bc' => 'HOLD',
                                'alasan_hold' => $alasanFinal,
                                'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
                                'nobl' => $bc23->no_bl_awb,
                                'tgl_bl_awb' => $bc23->tgl_bl_awb ? Carbon::createFromFormat('m/d/Y', $bc23->tgl_bl_awb)->format('Y-m-d') : null,
                            ]);
                        }
                    }
                }
            }
            return back()->with('status', ['type' => 'success', 'message' => 'Data ditemukan']);
      }
        return back()->with('status', ['type' => 'error', 'message' => 'Something Wrong']);
    }

    public function GetBC23Permit()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_Gudang' => 'ARN1',
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline', function ($service) use ($data) {        
            $this->response = $service->call('GetBC23Permit', [$data])->GetBC23PermitResult;      
        });
        
//        var_dump($this->response);
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return response()->json([
            'success' => false,
            'message' => 'error : ' . $this->response,
           ]);
        }
        
        $header = array();
        $kms = null;
        $dok = null;
        $cont = null;
        $groups = [];

        foreach ($xml->children() as $child) {
            $groups[] = $child;
        }
        foreach ($groups as $group) {
            $header = $group->header ?? $group->HEADER;
            $oldBC23 = BC23::where('car', $header->CAR)->first();
            if (!$oldBC23) {
                $bc23 = BC23::create([
                    'car' =>$header->CAR ?? null,
                    'no_sppb' =>$header->NO_SPPB ?? null,
                    'tgl_sppb' =>$header->TGL_SPPB ?? null,
                    'nojoborder' =>$header->NOJOBORDER ?? null,
                    'kd_kantor_pengawas' =>$header->KD_KANTOR_PENGAWAS ?? null,
                    'kd_kantor_bongkar' =>$header->KD_KANTOR_BONGKAR ?? null,
                    'no_pib' =>$header->NO_PIB ?? null,
                    'tgl_pib' =>$header->TGL_PIB ?? null,
                    'nama_imp' =>$header->NAMA_IMP ?? null,
                    'npwp_imp' =>$header->NPWP_IMP ?? null,
                    'alamat_imp' =>$header->ALAMAT_IMP ?? null,
                    'npwp_ppjk' =>$header->NPWP_PPJK ?? null,
                    'nama_ppjk' =>$header->NAMA_PPJK ?? null,
                    'alamat_ppjk' =>$header->ALAMAT_PPJK ?? null,
                    'nm_angkut' =>$header->NM_ANGKUT ?? null,
                    'no_voy_flight' =>$header->NO_VOY_FLIGHT ?? null,
                    'bruto' =>$header->BRUTO ?? null,
                    'netto' =>$header->NETTO ?? null,
                    'gudang' =>$header->GUDANG ?? null,
                    'status_jalur' =>$header->STATUS_JALUR ?? null,
                    'jml_cont' =>$header->JML_CONT ?? null,
                    'no_bc11' =>$header->NO_BC11 ?? null,
                    'tgl_bc11' =>$header->TGL_BC11 ?? null,
                    'no_pos_bc11' =>$header->NO_POS_BC11 ?? null,
                    'no_bl_awb' =>$header->NO_BL_AWB ?? null,
                    'tgl_bl_awb' =>$header->TGL_BL_AWB ?? null,
                    'no_master_bl_awb' =>$header->NO_MASTER_BL_AWB ?? null,
                    'tgl_master_bl_awb' =>$header->TGL_MASTER_BL_AWB ?? null,
                    'tgl_upload'=>Carbon::today()->format('Y-m-d'),
                    'jam_upload'=>Carbon::now()->format('H:i:s'),
                ]);

                $detil[]  = $group->DETIL ?? $group->detil;       
                foreach ($group->DETIL->CONT as $detailCont) {
                    $bcCont = BC23Cont::create([
                        'sppb23_id' => $bc23->id,
                        'car' => $detailCont->CAR,
                        'no_cont' => $detailCont->NO_CONT,
                        'size' => $detailCont->SIZE,
                        'jns_muat' => $detailCont->JNS_MUAT,
                    ]);

                    if ($bc23->jml_cont > 0) {
                        $contF = ContF::whereNull('tglkeluar')->where('nocontainer', $detailCont->NO_CONT)->where('size', $detailCont->SIZE)->first();
                        if ($contF) {
                            if ($contF->size != $detailCont->SIZE) {
                                $alasanSize = '& Ukuran Fisik Size Berbeda';
                            }else {
                                $alasanSize = null;
                            }
                            $alasanFinal = 'Bukan Dokumen SPPB. ' . $alasanFinal;
                            $cust = Customer::where('name', $bc23->nama_imp)->first();
                            if ($cust) {
                                $cust->update([
                                    'name' => $bc23->nama_imp,
                                    'npwp' => $bc23->npwp_imp,
                                    'alamat' => $bc23->alamat_imp,
                                ]);
                            }
                            $newCust = null;
                            if (!$cust && $bc23->nama_imp != null) {
                                $newCust = Customer::create([
                                    'name' => $bc23->nama_imp,
                                    'npwp' => $bc23->npwp_imp,
                                    'alamat' => $bc23->alamat_imp,
                                ]);
                            }
                            $contF->update([
                               'kd_dok_inout' => 2,
                                'no_dok' => $bc23->no_sppb,
                                'tgl_dok' => Carbon::createFromFormat('d/m/Y', $bc23->tgl_sppb)->format('Y-m-d'),
                                'status_bc' => 'HOLD',
                                'alasan_hold' => $alasanFinal,
                                'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
                                'nobl' => $bc23->no_bl_awb,
                                'tgl_bl_awb' => $bc23->tgl_bl_awb ? Carbon::createFromFormat('m/d/Y', $bc23->tgl_bl_awb)->format('Y-m-d') : null,
                            ]);
                        }
                    }
                }    
                foreach ($group->DETIL->KMS as $detailKMS) {
                    $bcKMS = BC23Kms::create([
                        'sppb23_id'=>$bc23->id ?? null,
                        'car'=>$detailKMS->CAR,
                        'jns_kms'=>$detailKMS->JNS_KMS,
                        'merk_kms'=>$detailKMS->MERK_KMS,
                        'jml_kms'=>$detailKMS->JML_KMS,
                    ]);      
                    
                    if ($bc23->jml_cont == 0) {
                        $manifest = Manifest::where('nohbl', $bc23->no_bl_awb)->where('tglbuangmty', null)->first();
                        if ($manifest) {
                            $alasanBasic = "Bukan Dokumen SPPB 2.0";
                            $alasanCust = null;
                            $alasanKemas = null;
                            $alasanJml = null;
                            // alasanCust 
                            if ($manifest->customer->name != $bc23->nama_imp || $manifest->customer->npwp != $bc23->npwp_imp) {
                                $alasanCust = "Data Importir Berbeda";
                            }
    
                            // Alasan Kemas
                            if ($manifest->packing->code != $bcKMS->jns_kms) {
                                $alasanKemas = "Jenis Kemas Berbeda";
                            }
    
                            if ($manifest->quantity != $bcKMS) {
                                $alasanJml = "Quantity Berbeda";
                            }
    
                            $alasanFinal = $alasanBasic . ', ' . $alasanCust . ', ' . $alasanKemas . ', ' . $alasanJml;
    
                            $manifest->update([
                                'kd_dok_inout' => 2,
                                'no_dok' => $bc23->no_sppb,
                                'tgl_dok' => Carbon::createFromFormat('d/m/Y', $bc23->tgl_sppb)->format('Y-m-d'),
                                'status_bc' => 'HOLD',
                                'alasan_hold' => $alasanFinal,
                            ]);
                        }
                    }
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil di Simpan',
        ]);
        
    }

    public function sppbIndex()
    {
        $data['title'] = "Dokumen SPPB";

        return view('dokumen.sppb.index', $data);
    }

    public function sppbData(Request $request)
    {
        $dokumen = SPPB::get();
        return DataTables::of($dokumen)->make('true');
    }

    public function SPPBContainerList($id)
    {
        try {
            $sppb = SPPB::find($id);
            if (!$sppb) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan.',
                ]);
            }
        
            $noDokumen = $sppb->no_sppb ?? '';
            $contDok = SPPBCont::where('sppb_id', $id)->get();
            $cont = ContF::where('no_dok', $sppb->no_sppb)->get();
            // var_dump($cont);
        
            $data = $contDok->map(function ($item) use ($cont) {
                $contReal = $cont->where('nocontainer', $item->no_cont)->first();
                // var_dump($item, $contReal);
                if ($contReal) {
                    $sizeCont = $contReal->size ?? '';
                    $tglMasuk = $contReal->tglmasuk ?? 'Belum Masuk';
                    $tglKeluar = $contReal->tglkeluar ?? 'Belum Masuk';
                }else {
                    $sizeCont = 'Data Container Tidak Ditemukan';
                    $tglMasuk = 'Data Container Tidak Ditemukan';
                    $tglKeluar = 'Data Container Tidak Ditemukan';
                }
                return [
                    'noCont' => $item->no_cont ?? '',
                    'ukuranDok' => $item->size ?? '',
                    'sizeCont' =>  $sizeCont,
                    'tglMasuk' =>  $tglMasuk,
                    'tglKeluar' => $tglKeluar,
                ];
            });
        
            return response()->json([
                'success' => true,
                'noDokumen' => $noDokumen,
                'data' => $data
            ]);
        
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function sppbDetail($id)
    {
        $sppb = SPPB::where('id', $id)->first();
        $data['title'] = "Detail SPPB PIB ". $sppb->no_sppb;
        $data['dok'] = $sppb;
        $data['conts'] = SPPBCont::where('sppb_id', $id)->get();
        $data['kmss'] = SPPBKms::where('sppb_id', $id)->get();

        return view('dokumen.sppb.detail', $data);
    }

    public function sppbUpdateDetail(Request $request)
    {
        $sppb = SPPB::where('id', $request->id)->first();
        if ($sppb) {
            $sppb->update([
                'car'=>$request->car,
                'no_sppb'=>$request->no_sppb,
                'tgl_sppb'=>$request->tgl_sppb,
                'nojoborder'=>$request->nojoborder,
                'kd_kantor_pengawas'=>$request->kd_kantor_pengawas,
                'kd_kantor_bongkar'=>$request->kd_kantor_bongkar,
                'no_pib'=>$request->no_pib,
                'tgl_pib'=>$request->tgl_pib,
                'nama_imp'=>$request->nama_imp,
                'npwp_imp'=>$request->npwp_imp,
                'alamat_imp'=>$request->alamat_imp,
                'npwp_ppjk'=>$request->npwp_ppjk,
                'nama_ppjk'=>$request->nama_ppjk,
                'alamat_ppjk'=>$request->alamat_ppjk,
                'nm_angkut'=>$request->nm_angkut,
                'no_voy_flight'=>$request->no_voy_flight,
                'bruto'=>$request->bruto,
                'netto'=>$request->netto,
                'gudang'=>$request->gudang,
                'status_jalur'=>$request->status_jalur,
                'jml_cont'=>$request->jml_cont,
                'no_bc11'=>$request->no_bc11,
                'tgl_bc11'=>$request->tgl_bc11,
                'no_pos_bc11'=>$request->no_pos_bc11,
                'no_bl_awb'=>$request->no_bl_awb,
                'tgl_bl_awb'=>$request->tgl_bl_awb,
                'no_master_bl_awb'=>$request->no_master_bl_awb,
                'tgl_master_bl_awb'=>$request->tgl_master_bl_awb,
            ]);
            return back()->with('status', ['type' => 'success', 'message' => 'Data ditemukan']);
        }else {
            return back()->with('status', ['type' => 'error', 'message' => 'Something Wrong']);
        }

    }

    public function GetImpor_SPPB_OnDemand(Request $request)
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                                                         
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                   
        });
        
		
			if($request->thn_sppb<='2022'){
			$sppbkode = '/KPU.01/';
            }else{
			$sppbkode = '/KPU.1/';            
			}
		
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'No_Sppb' => $request->no_sppb, //063484/KPU.01/2017	
			'Tgl_Sppb' => Carbon::parse($request->tgl_sppb)->format('dmY'), //09022017
            'NPWP_Imp' => $request->npwp_imp //033153321035000
        ];

        
        // Using the added service
        \SoapWrapper::service('TpsOnline', function ($service) use ($data) {        
            $this->response = $service->call('GetImpor_Sppb', [$data])->GetImpor_SppbResult;      
        }); 
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
            return back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' .  $this->response]);
        }

        $header = null;
        $kms = null;
        $cont = null;
        foreach($xml->children() as $child) {
            foreach($child as $key => $value) {
                if($key == 'header' || $key == 'HEADER'){
                    $header = $value;
                }else{
                    foreach ($value as $key => $value):
                        if($key == 'kms' || $key == 'KMS'):
                            $kms [] = $value;
                        elseif($key == 'dok' || $key == 'DOC'):
                            $dok [] = $value;
                        elseif($key == 'cont' || $key == 'CONT'):
                            $cont [] = $value;
                        endif;
                    endforeach;
                }
            }
        }
        if ($header) {
            $oldSPPB = SPPB::where('car', $header->CAR)->first();
            if ($oldSPPB) {
                return back()->with('status', ['type' => 'error', 'message' => 'Data sudah tersedia']);
            }

            $sppb = SPPB::create([
                'car' =>$header->CAR ?? null,
                'no_sppb' =>$header->NO_SPPB ?? null,
                'tgl_sppb' =>$header->TGL_SPPB ?? null,
                'nojoborder' =>$header->NOJOBORDER ?? null,
                'kd_kantor_pengawas' =>$header->KD_KANTOR_PENGAWAS ?? null,
                'kd_kantor_bongkar' =>$header->KD_KANTOR_BONGKAR ?? null,
                'no_pib' =>$header->NO_PIB ?? null,
                'tgl_pib' =>$header->TGL_PIB ?? null,
                'nama_imp' =>$header->NAMA_IMP ?? null,
                'npwp_imp' =>$header->NPWP_IMP ?? null,
                'alamat_imp' =>$header->ALAMAT_IMP ?? null,
                'npwp_ppjk' =>$header->NPWP_PPJK ?? null,
                'nama_ppjk' =>$header->NAMA_PPJK ?? null,
                'alamat_ppjk' =>$header->ALAMAT_PPJK ?? null,
                'nm_angkut' =>$header->NM_ANGKUT ?? null,
                'no_voy_flight' =>$header->NO_VOY_FLIGHT ?? null,
                'bruto' =>$header->BRUTO ?? null,
                'netto' =>$header->NETTO ?? null,
                'gudang' =>$header->GUDANG ?? null,
                'status_jalur' =>$header->STATUS_JALUR ?? null,
                'jml_cont' =>$header->JML_CONT ?? null,
                'no_bc11' =>$header->NO_BC11 ?? null,
                'tgl_bc11' =>$header->TGL_BC11 ?? null,
                'no_pos_bc11' =>$header->NO_POS_BC11 ?? null,
                'no_bl_awb' =>$header->NO_BL_AWB ?? null,
                'tgl_bl_awb' =>$header->TGL_BL_AWB ?? null,
                'no_master_bl_awb' =>$header->NO_MASTER_BL_AWB ?? null,
                'tgl_master_bl_awb' =>$header->TGL_MASTER_BL_AWB ?? null,
                'tgl_upload'=>Carbon::today()->format('Y-m-d'),
                'jam_upload'=>Carbon::now()->format('H:i:s'),
            ]);

            if ($kms) {
                foreach ($kms as $detail) {
                    // dd($kms, $detail);
                    $sppbKMS = SPPBKms::create([
                        'sppb_id'=>$sppb->id ?? null,
                        'car'=>$detail->CAR,
                        'jns_kms'=>$detail->JNS_KMS,
                        'merk_kms'=>$detail->MERK_KMS,
                        'jml_kms'=>$detail->JML_KMS,
                    ]);
                    if ($sppb->jml_cont == 0) {
                        $manifest = Manifest::where('nohbl', $sppb->no_bl_awb)->where('tglbuangmty', null)->first();
                        if ($manifest) {
                            $alasanCust = null;
                            $alasanKemas = null;
                            $alasanJml = null;
                            $statusBC = "release";
                            if ($manifest->customer->name != $sppb->nama_imp || $manifest->customer->npwp != $sppb->npwp_imp) {
                                $alasanCust = "Data Importir Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            // Alasan Kemas
                            if ($manifest->packing->code != $sppbKMS->jns_kms) {
                                $alasanKemas = "Jenis Kemas Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            if ($manifest->quantity != $sppbKMS) {
                                $alasanJml = "Quantity Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            $alasanFinal = $alasanBasic . ', ' . $alasanCust . ', ' . $alasanKemas . ', ' . $alasanJml;
    
                            $manifest->update([
                                'kd_dok_inout' => 1,
                                'no_dok' => $sppb->no_sppb,
                                'tgl_dok' => Carbon::createFromFormat('d/m/Y', $sppb->tgl_sppb)->format('Y-m-d'),
                                'status_bc' => $statusBC,
                                'alasan_hold' => $alasanFinal,
                            ]);
                        }
                    }
                }
            }

            if ($cont) {
                foreach ($cont as $detail) {
                    $sppbCont = SPPBCont::create([
                        'sppb_id' => $sppb->id,
                        'car' => $detail->CAR,
                        'no_cont' => $detail->NO_CONT,
                        'size' => $detail->SIZE,
                        'jns_muat' => $detail->JNS_MUAT,
                    ]);

                    if ($sppb->jml_cont > 0) {
                        $contF = ContF::whereNull('tglkeluar')->where('nocontainer', $detail->NO_CONT)->first();
                        if ($contF) {
                            if ($contF->size != $detail->SIZE) {
                                $alasanSize = 'Ukuran Fisik Size Berbeda';
                                $statusBC = 'HOLD';
                            }else {
                                $alasanSize = null;
                                $statusBC = 'release';
                            }
                            $cust = Customer::where('name', $sppb->nama_imp)->first();
                            if ($cust) {
                                $cust->update([
                                    'name' => $sppb->nama_imp,
                                    'npwp' => $sppb->npwp_imp,
                                    'alamat' => $sppb->alamat_imp,
                                ]);
                            }
                            $newCust = null;
                            if (!$cust && $sppb->nama_imp != null) {
                                $newCust = Customer::create([
                                    'name' => $sppb->nama_imp,
                                    'npwp' => $sppb->npwp_imp,
                                    'alamat' => $sppb->alamat_imp,
                                ]);
                            }
                            $contF->update([
                                'kd_dok_inout' => 1,
                                 'no_dok' => $sppb->no_sppb,
                                 'tgl_dok' => Carbon::createFromFormat('d/m/Y', $sppb->tgl_sppb)->format('Y-m-d'),
                                 'status_bc' => $statusBC,
                                 'alasan_hold' => $alasanSize,
                                 'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
                                 'nobl' => $sppb->no_bl_awb,
                                 'tgl_bl_awb' => $sppb->tgl_bl_awb ? Carbon::createFromFormat('m/d/Y', $sppb->tgl_bl_awb)->format('Y-m-d') : null,
 
                             ]);
                        }
                    }
                }
            }
            return back()->with('status', ['type' => 'success', 'message' => 'Data ditemukan']);
      }else {
        return back()->with('status', ['type' => 'error', 'message' => 'Something Wrong']);
      }
    }

    public function GetImporPermit()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                    
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_Gudang' => 'ARN1'
        ];
        
        \SoapWrapper::service('TpsOnline', function ($service) use ($data) {        
            $this->response = $service->call('GetImporPermit', [$data])->GetImporPermitResult;      
        });
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return response()->json([
            'success' => false,
            'message' => 'Error : ' . $this->response,
           ]);
        }

        $groups = [];
        $nextGroup = [];
        $header = [];
        $valueTest = [];
        $detil = [];
        
        foreach ($xml->children() as $child) {
            $groups[] = $child;
        }
        
        foreach ($groups as $group) {
            $header = $group->header ?? $group->HEADER;
            $oldSPPB = SPPB::where('car', $header->CAR)->first();
            if (!$oldSPPB) {
                $sppb = SPPB::create([
                    'car' =>$header->CAR ?? null,
                    'no_sppb' =>$header->NO_SPPB ?? null,
                    'tgl_sppb' =>$header->TGL_SPPB ?? null,
                    'nojoborder' =>$header->NOJOBORDER ?? null,
                    'kd_kantor_pengawas' =>$header->KD_KANTOR_PENGAWAS ?? null,
                    'kd_kantor_bongkar' =>$header->KD_KANTOR_BONGKAR ?? null,
                    'no_pib' =>$header->NO_PIB ?? null,
                    'tgl_pib' =>$header->TGL_PIB ?? null,
                    'nama_imp' =>$header->NAMA_IMP ?? null,
                    'npwp_imp' =>$header->NPWP_IMP ?? null,
                    'alamat_imp' =>$header->ALAMAT_IMP ?? null,
                    'npwp_ppjk' =>$header->NPWP_PPJK ?? null,
                    'nama_ppjk' =>$header->NAMA_PPJK ?? null,
                    'alamat_ppjk' =>$header->ALAMAT_PPJK ?? null,
                    'nm_angkut' =>$header->NM_ANGKUT ?? null,
                    'no_voy_flight' =>$header->NO_VOY_FLIGHT ?? null,
                    'bruto' =>$header->BRUTO ?? null,
                    'netto' =>$header->NETTO ?? null,
                    'gudang' =>$header->GUDANG ?? null,
                    'status_jalur' =>$header->STATUS_JALUR ?? null,
                    'jml_cont' =>$header->JML_CONT ?? null,
                    'no_bc11' =>$header->NO_BC11 ?? null,
                    'tgl_bc11' =>$header->TGL_BC11 ?? null,
                    'no_pos_bc11' =>$header->NO_POS_BC11 ?? null,
                    'no_bl_awb' =>$header->NO_BL_AWB ?? null,
                    'tgl_bl_awb' =>$header->TGL_BL_AWB ?? null,
                    'no_master_bl_awb' =>$header->NO_MASTER_BL_AWB ?? null,
                    'tgl_master_bl_awb' =>$header->TGL_MASTER_BL_AWB ?? null,
                    'tgl_upload'=>Carbon::today()->format('Y-m-d'),
                    'jam_upload'=>Carbon::now()->format('H:i:s'),
                ]);
                $detil[]  = $group->DETIL ?? $group->detil;       
                foreach ($group->DETIL->CONT as $detailCont) {
                    $sppbCont = SPPBCont::create([
                        'sppb_id' => $sppb->id,
                        'car' => $detailCont->CAR,
                        'no_cont' => $detailCont->NO_CONT,
                        'size' => $detailCont->SIZE,
                        'jns_muat' => $detailCont->JNS_MUAT,
                    ]);

                    if ($sppb->jml_cont > 0) {
                        $contF = ContF::whereNull('tglkeluar')->where('nocontainer', $detailCont->NO_CONT)->where('size', $detailCont->SIZE)->first();
                        if ($contF) {
                            if ($contF->size == $detail->SIZE) {
                                $alasanSize = null;
                                $statusBC = 'release';
                            }else {
                                $alasanSize = 'Ukuran Fisik Size Berbeda';
                                $statusBC = 'HOLD';
                            }
                            $cust = Customer::where('name', $sppb->nama_imp)->first();
                            if ($cust) {
                                $cust->update([
                                    'name' => $sppb->nama_imp,
                                    'npwp' => $sppb->npwp_imp,
                                    'alamat' => $sppb->alamat_imp,
                                ]);
                            }
                            $newCust = null;
                            if (!$cust && $sppb->nama_imp != null) {
                                $newCust = Customer::create([
                                    'name' => $sppb->nama_imp,
                                    'npwp' => $sppb->npwp_imp,
                                    'alamat' => $sppb->alamat_imp,
                                ]);
                            }
                            $contF->update([
                                 'kd_dok_inout' => 1,
                                 'no_dok' => $sppb->no_sppb,
                                 'tgl_dok' => Carbon::createFromFormat('d/m/Y', $sppb->tgl_sppb)->format('Y-m-d'),
                                 'status_bc' => $statusBC,
                                 'alasan_hold' => $alasanSize,
                                 'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
                                 'nobl' => $sppb->no_bl_awb,
                                 'tgl_bl_awb' => $sppb->tgl_bl_awb ? Carbon::createFromFormat('m/d/Y', $sppb->tgl_bl_awb)->format('Y-m-d') : null,
                             ]);
                        }
                    }
                }    
                foreach ($group->DETIL->KMS as $detailKMS) {
                    # code...
                    $sppbKMS = SPPBKms::create([
                        'sppb_id'=>$sppb->id ?? null,
                        'car'=>$detailKMS->CAR,
                        'jns_kms'=>$detailKMS->JNS_KMS,
                        'merk_kms'=>$detailKMS->MERK_KMS,
                        'jml_kms'=>$detailKMS->JML_KMS,
                    ]);     
                    
                    if ($sppb->jml_cont == 0) {
                        $manifest = Manifest::where('nohbl', $sppb->no_bl_awb)->where('tglbuangmty', null)->first();
                        if ($manifest) {
                            $alasanCust = null;
                            $alasanKemas = null;
                            $alasanJml = null;
                            $statusBC = "release";
                            if ($manifest->customer->name != $sppb->nama_imp || $manifest->customer->npwp != $sppb->npwp_imp) {
                                $alasanCust = "Data Importir Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            // Alasan Kemas
                            if ($manifest->packing->code != $sppbKMS->jns_kms) {
                                $alasanKemas = "Jenis Kemas Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            if ($manifest->quantity != $sppbKMS) {
                                $alasanJml = "Quantity Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            $alasanFinal = $alasanBasic . ', ' . $alasanCust . ', ' . $alasanKemas . ', ' . $alasanJml;
    
                            $manifest->update([
                                'kd_dok_inout' => 1,
                                'no_dok' => $sppb->no_sppb,
                                'tgl_dok' => Carbon::createFromFormat('d/m/Y', $sppb->tgl_sppb)->format('Y-m-d'),
                                'status_bc' => $statusBC,
                                'alasan_hold' => $alasanFinal,
                            ]);
                        }
                    }
                }
            }
        }
        
       return response()->json([
        'success' => true,
        'message' => 'Data Successed added',
       ]);
        
    }

    public function createJob(Request $request)
    {
        $plp = PLP::where('id', $request->id)->first();
        $ctr_type = $request->ctr_type;
        if ($plp) {
            try {
                // dd($request->all());
                if ($request->type == 'lcl') {
                    $noJob = $this->generateJobNumber();
                    $job = $this->createJobOrder($plp, $noJob, $request);
    
                    $plpDetails = PLPdetail::where('plp_id', $plp->id)->get();
    
                    $this->createContainers($plpDetails, $job, $ctr_type);
    
                    $plp->update([
                        'joborder_id' => $job->id,
                        'type' => $request->type,
                    ]);
    
                    return redirect()->route('lcl.register.detail', ['id' => $job->id])
                        ->with('status', ['type' => 'success', 'message' => 'Data berhasil dibuat']);
                }else {
                    $noJob = $this->generateJobNumberFCL();
                    $job = $this->createJobOrderFCL($plp, $noJob, $request);
    
                    $plpDetails = PLPdetail::where('plp_id', $plp->id)->get();
    
                    $this->createContainersFCL($plpDetails, $job, $ctr_type);
    
                    $plp->update([
                        'joborder_id' => $job->id,
                        'type' => $request->type,
                    ]);
    
                    return redirect()->route('fcl.register.detail', ['id' => $job->id])
                        ->with('status', ['type' => 'success', 'message' => 'Data berhasil dibuat']);
                }
            } catch (\Throwable $e) {
                return redirect()->back()
                    ->with('status', ['type' => 'error', 'message' => 'Oopss, Something Wrong: ' . $e->getMessage()]);
            }
        }
    }


   private function generateJobNumber()
    {
        $currentYear = Carbon::now()->format('y');
        $currentMonth = Carbon::now()->format('m');
        $lastJob = Job::whereYear('c_datetime', Carbon::now()->year)
            ->whereMonth('c_datetime', Carbon::now()->month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastJob) {
            $lastJobNumber = intval(substr($lastJob->nojoborder, 3, 5));
            $newJobNumber = str_pad($lastJobNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newJobNumber = '00001';
        }

        return 'ITM' . $newJobNumber . '/' . $currentMonth . '/' . $currentYear;
    }

    private function generateJobNumberFCL()
    {
        $currentYear = Carbon::now()->format('y');
        $currentMonth = Carbon::now()->format('m');
        $lastJob = JobF::whereYear('c_datetime', Carbon::now()->year)
            ->whereMonth('c_datetime', Carbon::now()->month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastJob) {
            $lastJobNumber = intval(substr($lastJob->nojoborder, 3, 5));
            $newJobNumber = str_pad($lastJobNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newJobNumber = '00001';
        }

        return 'ITM' . $newJobNumber . '/' . $currentMonth . '/' . $currentYear;
    }

    private function createJobOrder($plp, $noJob, $request)
    {
        $kapal = Vessel::where('name', $plp->nm_angkut)->where('call_sign', $plp->call_sign)->first();
        if ($kapal) {
            $ves = $kapal;
        }else {
            $ves = Vessel::create([
                'name'=> $plp->nm_angkut,
                'call_sign'=> $plp->call_sign,
            ]);

        }
        $lokasiSndar = LokasiSandar::where('kd_tps_asal', $plp->kd_tps_asal)->first();
        if ($lokasiSndar) {
            $lokId = $lokasiSndar->id;
        }else {
            $newLokasiSnadar = LokasiSandar::create([
                'kd_tps_asal' => $plp->kd_tps_asal,
            ]);

            $lokId = $newLokasiSnadar->id;
        }

        return Job::create([
            'nojoborder' => $noJob,
            'plp_id' => $plp->id,
            'tno_bc11' => $plp->no_bc11,
            'ttgl_bc11' => $plp->tgl_bc11,
            'noplp' => $plp->no_plp,
            'ttgl_plp' => $plp->tgl_plp,
            'type' => 'lcl',
            'uid' => Auth::user()->id,
            'c_datetime' => Carbon::now(),
            'vessel' => $ves->id,
            'voy' => $plp->no_voy_flight,
            'call_sign' => $plp->call_sign,
            'nospk' => $request->nospk,
            'forwarding_id' => $request->forwarding_id,
            'eta' => $plp->tgl_tiba,
            'lokasisandar_id' => $lokId,
        ]);
    }

    private function createJobOrderFCL($plp, $noJob, $request)
    {
        $kapal = Vessel::where('name', $plp->nm_angkut)->where('call_sign', $plp->call_sign)->first();
        if ($kapal) {
            $ves = $kapal;
        }else {
            $ves = Vessel::create([
                'name'=> $plp->nm_angkut,
                'call_sign'=> $plp->call_sign,
            ]);
        }

        $lokasiSndar = LokasiSandar::where('kd_tps_asal', $plp->kd_tps_asal)->first();
        if ($lokasiSndar) {
            $lokId = $lokasiSndar->id;
        }else {
            $newLokasiSnadar = LokasiSandar::create([
                'kd_tps_asal' => $plp->kd_tps_asal,
            ]);

            $lokId = $newLokasiSnadar->id;
        }
        return JobF::create([
            'nojoborder' => $noJob,
            'plp_id' => $plp->id,
            'tno_bc11' => $plp->no_bc11,
            'ttgl_bc11' => $plp->tgl_bc11,
            'noplp' => $plp->no_plp,
            'ttgl_plp' => $plp->tgl_plp,
            'type' => 'fcl',
            'uid' => Auth::user()->id,
            'c_datetime' => Carbon::now(),
            'vessel' => $ves->id,
            'voy' => $plp->no_voy_flight,
            'call_sign' => $plp->call_sign,
            'nospk' => $request->nospk,
            'forwarding_id' => $request->forwarding_id,
            'eta' => $plp->tgl_tiba,
            'lokasisandar_id' => $lokId,
        ]);
    }

    private function createContainers($plpDetails, $job, $ctr_type)
    {
        $conts = $plpDetails->unique('no_cont');

        foreach ($conts as $cont) {
            $teus = $this->calculateTeus($cont->uk_cont);
            $customer = Customer::where('name', $cont->consignee)->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $cont->consignee,
                    'code' => '000',
                    'alamat' => '-',
                    'npwp' => '111111111',
                    'email' => '-',
                    'fax' => '111111',
                    'phone' => '111111111',
                ]);
            }
            Cont::create([
                'nocontainer' => $cont->no_cont,
                'type' => 'lcl',
                'joborder_id' => $job->id,
                'size' => $cont->uk_cont,
                'teus' => $teus,
                'uid' => Auth::user()->id,
                'nobl' => $cont->no_bl_awb,
                'tgl_bl_awb' => $cont->tgl_bl_awbl ? Carbon::createFromFormat('Ymd', $cont->tgl_bl_awb)->format('Y-m-d') : null,
                'eta'=> $job->eta,
                'lokasisandar_id' => $job->lokasisandar_id,
                'cust_id' => $customer->id,
                'ctr_type' => $ctr_type,
            ]);
        }
    }

    private function createContainersFCL($plpDetails, $job, $ctr_type)
    {
        $conts = $plpDetails->unique('no_cont');

        // dd($conts);
        
        foreach ($conts as $cont) {
            $teus = $this->calculateTeus($cont->uk_cont);
            
            $customer = Customer::where('name', $cont->consignee)->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $cont->consignee,
                    'code' => '000',
                    'alamat' => '-',
                    'npwp' => '111111111',
                    'email' => '-',
                    'fax' => '111111',
                    'phone' => '111111111',
                ]);
            }
            
            ContF::create([
                'nocontainer' => $cont->no_cont,
                'type' => 'fcl',
                'joborder_id' => $job->id,
                'size' => $cont->uk_cont,
                'teus' => $teus,
                'uid' => Auth::user()->id,
                'nobl' => $cont->no_bl_awb,
                'tgl_bl_awb' => $cont->tgl_bl_awbl ? Carbon::createFromFormat('Ymd', $cont->tgl_bl_awb)->format('Y-m-d') : null,
                'eta'=> $job->eta,
                'lokasisandar_id' => $job->lokasisandar_id,
                'cust_id' => $customer->id,
                'ctr_type' => $ctr_type,
                'status_bc' => 'HOLD',
            ]);
        }
    }

    private function calculateTeus($size)
    {
        return $size === '20' ? 1 : ($size === '40' ? 2 : 0);
    }

    // private function createManifests($plpDetails, $job)
    // {
    //     $plpKms = $plpDetails->unique('no_bl_awb');

    //     foreach ($plpKms as $kms) {
    //         $contKMS = Cont::where('joborder_id', $job->id)
    //             ->where('nocontainer', $kms->no_cont)
    //             ->first();

    //         $cust = Customer::where('name', $kms->consignee)->first();
    //         $pack = Packing::where('name', $kms->jns_kms)->first();
    //         $noTally = $this->generateTallyNumber($job);

    //         $manifest = Manifest::create([
    //             'notally' => $noTally,
    //             'validasi' => 'N',
    //             'barcode' => $this->generateUniqueBarcode(),
    //             'nohbl' => $kms->no_bl_awb,
    //             'container_id' => $contKMS->id ?? null,
    //             'joborder_id' => $job->id,
    //             'tgl_hbl' => $kms->tgl_bl_awb,
    //             'customer_id' => $cust->id ?? null,
    //             'quantity' => $kms->jml_kms ?? null,
    //             'packing_id' => $pack->id ?? null,
    //             'uid' => Auth::user()->id,
    //         ]);

    //         $this->createItems($manifest);
    //     }
    // }

    // private function generateTallyNumber($job)
    // {
    //     $lastTally = Manifest::where('joborder_id', $job->id)
    //         ->orderBy('id', 'desc')
    //         ->first();

    //     $lastTallyNumber = $lastTally ? intval(substr($lastTally->notally, 15, 3)) : 0;
    //     $newTallyNumber = str_pad($lastTallyNumber + 1, 3, '0', STR_PAD_LEFT);

    //     return $job->nojoborder . '-' . $newTallyNumber;
    // }

    // private function generateUniqueBarcode()
    // {
    //     do {
    //         $uniqueBarcode = Str::random(19);
    //     } while (Manifest::where('barcode', $uniqueBarcode)->exists());

    //     return $uniqueBarcode;
    // }

    // private function createItems($manifest)
    // {
    //     if ($manifest->quantity) {
    //         for ($i = 1; $i <= $manifest->quantity; $i++) {
    //             Item::create([
    //                 'manifest_id' => $manifest->id,
    //                 'barcode' => $manifest->barcode . $i,
    //                 'nomor' => $i,
    //                 'stripping' => 'N',
    //                 'uid' => Auth::user()->id,
    //             ]);
    //         }
    //     }
    // }

    public function manualIndex()
    {
        $data['title'] = "Dokumen Manual";
        $data['doks'] = Manual::get();
        $data['codes'] = Kode::orderBy('kode', 'asc')->get();

        return view('dokumen.manual.index', $data);
    }

    public function manualData(Request $request)
    {
        $dokumen = Manual::with('dokumen')->get();
        return DataTables::of($dokumen)->make(true);
    }

    public function manualContainerList($id)
    {
        try {
            $manual = Manual::where('idm', $id)->first();
            if (!$manual) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan.',
                ]);
            }
        
            $noDokumen = $manual->no_dok_inout ?? '';
            $contDok = ManualCont::where('manual_id', $id)->get();
            $cont = ContF::where('no_dok', $manual->no_dok_inout)->get();
        
            $data = $contDok->map(function ($item) use ($cont) {
                $contReal = $cont->where('nocontainer', $item->no_cont)->first();
                if ($contReal) {
                    $sizeCont = $contReal->size ?? '';
                    $tglMasuk = $contReal->tglmasuk ?? 'Belum Masuk';
                    $tglKeluar = $contReal->tglkeluar ?? 'Belum Masuk';
                }else {
                    $sizeCont = 'Data Container Tidak Ditemukan';
                    $tglMasuk = 'Data Container Tidak Ditemukan';
                    $tglKeluar = 'Data Container Tidak Ditemukan';
                }
                return [
                    'noCont' => $item->no_cont ?? '',
                    'ukuranDok' => $item->size ?? '',
                    'sizeCont' =>  $sizeCont,
                    'tglMasuk' =>  $tglMasuk,
                    'tglKeluar' => $tglKeluar,
                ];
            });
        
            return response()->json([
                'success' => true,
                'noDokumen' => $noDokumen,
                'data' => $data
            ]);
        
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function manualDetail($id)
    {
        $manual = Manual::where('idm', $id)->first();
        $noManual = $manual->no_dok_inout ?? '';
        $data['title'] = "Detail manual PIB ". $noManual;
        $data['dok'] = $manual;
        $data['conts'] = ManualCont::where('manual_id', $id)->get();
        $data['kmss'] = ManualKms::where('manual_id', $id)->get();

        return view('dokumen.manual.detail', $data);
    }

    public function GetDokumenManual_OnDemand(Request $request)
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetDokumenManual_OnDemand')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'KdDok' => $request->kd_dok,
            'NoDok' => $request->no_dok,
            'TglDok' =>Carbon::parse($request->tgl_dok)->format('dmY'),
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetDokumenManual_OnDemand', function ($service) use ($data) {        
            $this->response = $service->call('GetDokumenManual_OnDemand', [$data])->GetDokumenManual_OnDemandResult;      
        });
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
            return back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' .  $this->response]);
        }
        
        $docmanual_id = 0;
        $header = null;
        $kms = null;
        $cont = null;
        foreach($xml->children() as $child) {
            foreach($child as $key => $value) {
                if($key == 'header' || $key == 'HEADER'){
                    $header = $value;
                }else{
                    foreach ($value as $key => $value):
                        if($key == 'kms' || $key == 'KMS'):
                            $kms [] = $value;
                        elseif($key == 'dok' || $key == 'DOC'):
                            $dok [] = $value;
                        elseif($key == 'cont' || $key == 'CONT'):
                            $cont [] = $value;
                        endif;
                    endforeach;
                }
            }
        }

        // dd($header, $kms, $cont);
        if ($header) {
           $oldManual = Manual::where('id', $header->ID)->first();
           if ($oldManual) {
                return back()->with('status', ['type' => 'error', 'message' => 'Data sudah tersedia']);
           }

           $manual = Manual::create([
                'id'=>$header->ID,
                'kd_kantor'=>$header->KD_KANTOR,
                'kd_dok_inout'=>$header->KD_DOK_INOUT,
                'no_dok_inout'=>$header->NO_DOK_INOUT,
                'tgl_dok_inout'=>$header->TGL_DOK_INOUT,
                'id_consignee'=>$header->ID_CONSIGNEE,
                'consignee'=>$header->CONSIGNEE,
                'npwp_ppjk'=>$header->NPWP_PPJK,
                'nama_ppjk'=>$header->NAMA_PPJK,
                'nm_angkut'=>$header->NM_ANGKUT,
                'no_voy_flight'=>$header->NO_VOY_FLIGHT,
                'kd_gudang'=>$header->KD_GUDANG,
                'jml_cont'=>$header->JML_CONT,
                'no_bc11'=>$header->NO_BC11,
                'tgl_bc11'=>$header->TGL_BC11,
                'no_pos_bc11'=>$header->NO_POS_BC11,
                'no_bl_awb'=>$header->NO_BL_AWB,
                'tgl_bl_awb'=>$header->TGL_BL_AWB,
                'fl_segel'=>$header->FL_SEGEL,
                'tgl_upload'=>Carbon::today()->format('Y-m-d'),
                'jam_upload'=>Carbon::now()->format('H:i:s'),
           ]);

           if ($kms) {
                foreach ($kms as $detail) {
                    // dd($kms, $detail);
                    $manualKms = ManualKms::create([
                        'manual_id' => $manual->idm,
                        'id' => $detail->ID,
                        'jns_kms' => $detail->JNS_KMS,
                        'merk_kms' => $detail->MERK_KMS,
                        'jml_kms' => $detail->JML_KMS,
                    ]);

                    if ($manual->jml_cont == 0) {
                        $manifest = Manifest::where('nohbl', $manual->no_bl_awb)->where('tglbuangmty', null)->first();
                        if ($manifest) {
                            $alasanCust = null;
                            $alasanKemas = null;
                            $alasanJml = null;
                            $statusBC = "release";
                            if ($manifest->customer->name != $manual->consignee) {
                                $alasanCust = "Data Importir Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            // Alasan Kemas
                            if ($manifest->packing->code != $manualKMS->jns_kms) {
                                $alasanKemas = "Jenis Kemas Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            if ($manifest->quantity != $manualKMS) {
                                $alasanJml = "Quantity Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            $alasanFinal = $alasanBasic . ', ' . $alasanCust . ', ' . $alasanKemas . ', ' . $alasanJml;
    
                            $manifest->update([
                                'kd_dok_inout' => $manual->kd_dok_inout,
                                'no_dok' => $manual->no_dok_inout,
                                'tgl_dok' => Carbon::createFromFormat('d/m/Y', $manual->tgl_dok_inout)->format('Y-m-d'),
                                'status_bc' => $statusBC,
                                'alasan_hold' => $alasanFinal,
                            ]);
                        }
                    }
                }
            }
            if ($cont) {
                foreach ($cont as $detail) {
                    $manualCont = ManualCont::create([
                        'manual_id'=>$manual->idm,
                        'id'=>$detail->ID,
                        'no_cont'=>$detail->NO_CONT,
                        'size'=>$detail->SIZE,
                        'jns_muat'=>$detail->JNS_MUAT,
                    ]);
                    $contF = ContF::whereNull('tglkeluar')->where('nocontainer', $detail->NO_CONT)->where('size', $detail->SIZE)->first();
                    if ($contF) {
                        if ($contF->size != $detail->SIZE) {
                            $alasanSize = '& Ukuran Fisik Size Berbeda';
                        }else {
                            $alasanSize = null;
                        }
                        $alasanFinal = 'Bukan Dokumen SPPB. ' . $alasanFinal;
                        $cust = Customer::where('name', $manual->consignee)->first();
                            $newCust = null;
                            if (!$cust && $manual->consignee != null) {
                                $newCust = Customer::create([
                                    'name' => $manual->consignee,
                                ]);
                            }
                        $contF->update([
                           'kd_dok_inout' => $manual->kd_dok_inout,
                           'no_dok' => $manual->no_dok_inout,
                           'tgl_dok' => Carbon::createFromFormat('d/m/Y', $manual->tgl_dok_inout)->format('Y-m-d'),
                           'status_bc' => 'HOLD',
                           'alasan_hold' => $alasanFinal,
                           'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
                         ]);
                    }
                }
            }
            return back()->with('status', ['type' => 'success', 'message' => 'Data ditemukan']);
        }else {
            return back()->with('status', ['type' => 'error', 'message' => 'Something Wrong']);
        }
    }

    public function GetDokumenManual()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetDokumenManual')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_Tps' => $this->kode
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetDokumenManual', function ($service) use ($data) {        
            $this->response = $service->call('GetDokumenManual', [$data])->GetDokumenManualResult;      
        });
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
            return response()->json([
             'success' => false,
             'message' => 'Error : ' . $this->response,
            ]);
         }
        
         $groups = [];
         $nextGroup = [];
         $header = [];
         $valueTest = [];
         $detil = [];
         
         foreach ($xml->children() as $child) {
             $groups[] = $child;
         }
         
         foreach ($groups as $group) {
             $header = $group->header ?? $group->HEADER;
             $oldManual = Manual::where('id', $header->ID)->first();
             if (!$oldManual) {
                $manual = Manual::create([
                        'id'=>$header->ID,
                        'kd_kantor'=>$header->KD_KANTOR,
                        'kd_dok_inout'=>$header->KD_DOK_INOUT,
                        'no_dok_inout'=>$header->NO_DOK_INOUT,
                        'tgl_dok_inout'=>$header->TGL_DOK_INOUT,
                        'id_consignee'=>$header->ID_CONSIGNEE,
                        'consignee'=>$header->CONSIGNEE,
                        'npwp_ppjk'=>$header->NPWP_PPJK,
                        'nama_ppjk'=>$header->NAMA_PPJK,
                        'nm_angkut'=>$header->NM_ANGKUT,
                        'no_voy_flight'=>$header->NO_VOY_FLIGHT,
                        'kd_gudang'=>$header->KD_GUDANG,
                        'jml_cont'=>$header->JML_CONT,
                        'no_bc11'=>$header->NO_BC11,
                        'tgl_bc11'=>$header->TGL_BC11,
                        'no_pos_bc11'=>$header->NO_POS_BC11,
                        'no_bl_awb'=>$header->NO_BL_AWB,
                        'tgl_bl_awb'=>$header->TGL_BL_AWB,
                        'fl_segel'=>$header->FL_SEGEL,
                        'tgl_upload'=>Carbon::today()->format('Y-m-d'),
                        'jam_upload'=>Carbon::now()->format('H:i:s'),
                    ]);

                 $detil[]  = $group->DETIL ?? $group->detil;       
                 foreach ($group->DETIL->CONT as $detailCont) {
                    $manualCont = ManualCont::create([
                        'manual_id'=>$manual->idm,
                        'id'=>$detail->ID,
                        'no_cont'=>$detail->NO_CONT,
                        'size'=>$detail->SIZE,
                        'jns_muat'=>$detail->JNS_MUAT,
                    ]);
 
                     if ($manualCont->jml_cont > 0) {
                         $contF = ContF::whereNull('tglkeluar')->where('nocontainer', $detailCont->NO_CONT)->where('size', $detailCont->SIZE)->first();
                         if ($contF) {
                            if ($contF->size != $detailCont->SIZE) {
                                $alasanSize = '& Ukuran Fisik Size Berbeda';
                            }else {
                                $alasanSize = null;
                            }
                            $alasanFinal = 'Bukan Dokumen SPPB. ' . $alasanFinal;
                            $cust = Customer::where('name', $manual->consignee)->first();
                            $newCust = null;
                            if (!$cust && $manual->consignee != null) {
                                $newCust = Customer::create([
                                    'name' => $manual->consignee,
                                ]);
                            }
                             $contF->update([
                                'kd_dok_inout' => $manual->kd_dok_inout,
                                'no_dok' => $manual->no_dok_inout,
                                'tgl_dok' => Carbon::createFromFormat('d/m/Y', $manual->tgl_dok_inout)->format('Y-m-d'),
                                'status_bc' => 'HOLD',
                                'alasan_hold' => $alasanFinal,
                                'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
                              ]);
                         }
                     }
                }    
                foreach ($group->DETIL->KMS as $detail) {
                    // dd($kms, $detail);
                    $manualKms = ManualKms::create([
                        'manual_id' => $manual->idm,
                        'id' => $detail->ID,
                        'jns_kms' => $detail->JNS_KMS,
                        'merk_kms' => $detail->MERK_KMS,
                        'jml_kms' => $detail->JML_KMS,
                    ]);

                    if ($manual->jml_cont == 0) {
                        $manifest = Manifest::where('nohbl', $manual->no_bl_awb)->where('tglbuangmty', null)->first();
                        if ($manifest) {
                            $alasanCust = null;
                            $alasanKemas = null;
                            $alasanJml = null;
                            $statusBC = "release";
                            if ($manifest->customer->name != $manual->consignee) {
                                $alasanCust = "Data Importir Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            // Alasan Kemas
                            if ($manifest->packing->code != $manualKMS->jns_kms) {
                                $alasanKemas = "Jenis Kemas Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            if ($manifest->quantity != $manualKMS) {
                                $alasanJml = "Quantity Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            $alasanFinal = $alasanBasic . ', ' . $alasanCust . ', ' . $alasanKemas . ', ' . $alasanJml;
    
                            $manifest->update([
                                'kd_dok_inout' => $manual->kd_dok_inout,
                                'no_dok' => $manual->no_dok_inout,
                                'tgl_dok' => Carbon::createFromFormat('d/m/Y', $manual->tgl_dok_inout)->format('Y-m-d'),
                                'status_bc' => $statusBC,
                                'alasan_hold' => $alasanFinal,
                            ]);
                        }
                    }
                }
             }
         }
         
        return response()->json([
         'success' => true,
         'message' => 'Data Successed added',
        ]);
        
    }

    public function pabeanIndex()
    {
        $data['title'] = 'Dokumen Pabean';
        
        $data['codes'] = Kode::orderBy('kode', 'asc')->get();

        return view('dokumen.pabean.index', $data);
    }

    public function pabeanData(Request $request)
    {
        $dokumen = Pabean::with('dokumen')->get();
        return DataTables::of($dokumen)->make(true);
    }

    public function pabeanContainerList($id)
    {
        try {
            $pabean = Pabean::find($id);
            if (!$pabean) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan.',
                ]);
            }
        
            $noDokumen = $pabean->no_dok_inout ?? '';
            $contDok = PabeanCont::where('pabean_id', $id)->get();
            $cont = ContF::where('no_dok', $pabean->no_dok_inout)->get();
            // var_dump($cont);
        
            $data = $contDok->map(function ($item) use ($cont) {
                $contReal = $cont->where('nocontainer', $item->no_cont)->first();
                // var_dump($item, $contReal);
                if ($contReal) {
                    $sizeCont = $contReal->size ?? '';
                    $tglMasuk = $contReal->tglmasuk ?? 'Belum Masuk';
                    $tglKeluar = $contReal->tglkeluar ?? 'Belum Masuk';
                }else {
                    $sizeCont = 'Data Container Tidak Ditemukan';
                    $tglMasuk = 'Data Container Tidak Ditemukan';
                    $tglKeluar = 'Data Container Tidak Ditemukan';
                }
                return [
                    'noCont' => $item->no_cont ?? '',
                    'ukuranDok' => $item->size ?? '',
                    'sizeCont' =>  $sizeCont,
                    'tglMasuk' =>  $tglMasuk,
                    'tglKeluar' => $tglKeluar,
                ];
            });
        
            return response()->json([
                'success' => true,
                'noDokumen' => $noDokumen,
                'data' => $data
            ]);
        
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function pabeanDetail($id)
    {
        $pabean = Pabean::where('id', $id)->first();
        $data['title'] = "Detail Pabean ". $pabean->no_dok_inout;
        $data['dok'] = $pabean;
        $data['conts'] = PabeanCont::where('pabean_id', $id)->get();
        $data['kmss'] = PabeanKms::where('pabean_id', $id)->get();

        return view('dokumen.pabean.detail', $data);
    }

    public function GetDokumenPabean_OnDemand(Request $request)
    {      
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetDokumenPabean_OnDemand')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'KdDok' => $request->kd_dok,
            'NoDok' => $request->no_dok,
            'TglDok' =>Carbon::parse($request->tgl_dok)->format('dmY'),
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetDokumenPabean_OnDemand', function ($service) use ($data) {        
            $this->response = $service->call('GetDokumenPabean_OnDemand', [$data])->GetDokumenPabean_OnDemandResult;      
        });
        
//        var_dump($this->response);return false;
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
            return back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' .  $this->response]);
        }
        
        $docmanual_id = 0;
        $header = null;
        $kms = null;
        $cont = null;
        foreach($xml->children() as $child) {
            foreach($child as $key => $value) {
                if($key == 'header' || $key == 'HEADER'){
                    $header = $value;
                }else{
                    foreach ($value as $key => $value):
                        if($key == 'kms' || $key == 'KMS'):
                            $kms [] = $value;
                        elseif($key == 'dok' || $key == 'DOC'):
                            $dok [] = $value;
                        elseif($key == 'cont' || $key == 'CONT'):
                            $cont [] = $value;
                        endif;
                    endforeach;
                }
            }
        }
    
        //  dd($header, $kms, $cont);
         if ($header) {
            $oldPabean = Pabean::where('car', $header->CAR)->first();
            if ($oldPabean) {
                return back()->with('status', ['type' => 'error', 'message' => 'Data sudah tersedia']);
            }

            $pabean = Pabean::create([
                'kd_dok_inout' => $header->KD_DOK_INOUT,
                'car' => $header->CAR,
                'no_dok_inout' => $header->NO_DOK_INOUT,
                'tgl_dok_inout' => $header->TGL_DOK_INOUT,
                'no_daftar' => $header->NO_DAFTAR,
                'tgl_daftar' => $header->TGL_DAFTAR,
                'kd_kantor' => $header->KD_KANTOR,
                'kd_kantor_pengawas' => $header->KD_KANTOR_PENGAWAS,
                'kd_kantor_bongkar' => $header->KD_KANTOR_BONGKAR,
                'npwp_imp' => $header->NPWP_IMP,
                'nm_imp' => $header->NM_IMP,
                'al_imp' => $header->AL_IMP,
                'npwp_ppjk' => $header->NPWP_PPJK,
                'nm_ppjk' => $header->NM_PPJK,
                'al_ppjk' => $header->AL_PPJK,
                'nm_angkut' => $header->NM_ANGKUT,
                'no_voy_flight' => $header->NO_VOY_FLIGHT,
                'brutto' => $header->BRUTTO,
                'netto' => $header->NETTO,
                'gudang' => $header->GUDANG,
                'status_jalur' => $header->STATUS_JALUR,
                'jml_cont' => $header->JML_CONT,
                'no_bc11' => $header->NO_BC11,
                'tgl_bc11' => $header->TGL_BC11,
                'no_pos_bc11' => $header->NO_POS_BC11,
                'no_bl_awb' => $header->NO_BL_AWB,
                'tgl_bl_awb' => $header->TGL_BL_AWB,
                'no_master_bl_awb' => $header->NO_MASTER_BL_AWB,
                'tgl_master_bl_awb' => $header->TGL_MASTER_BL_AWB,
                'fl_segel' => $header->FL_SEGEL,
                'tgl_upload'=>Carbon::today()->format('Y-m-d'),
                'jam_upload'=>Carbon::now()->format('H:i:s'),
            ]);
            if ($kms) {
                foreach ($kms as $detail) {
                    $pabeanKms = PabeanKms::create([
                        'pabean_id' => $pabean->id,
                        'car' => $detail->CAR,
                        'jns_kms' => $detail->JNS_KMS,
                        'jml_kms' => $detail->JML_KMS,
                    ]);

                    if ($pabean->jml_cont == 0) {
                        $manifest = Manifest::where('nohbl', $pabean->no_bl_awb)->where('tglbuangmty', null)->first();
                        if ($manifest) {
                            $alasanCust = null;
                            $alasanKemas = null;
                            $alasanJml = null;
                            $statusBC = "release";
                            if ($manifest->customer->name != $pabean->nm_imp || $manifest->customer->npwp != $pabean->npwp_imp) {
                                $alasanCust = "Data Importir Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            // Alasan Kemas
                            if ($manifest->packing->code != $pabeanKMS->jns_kms) {
                                $alasanKemas = "Jenis Kemas Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            if ($manifest->quantity != $pabeanKMS) {
                                $alasanJml = "Quantity Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            $alasanFinal = $alasanBasic . ', ' . $alasanCust . ', ' . $alasanKemas . ', ' . $alasanJml;
    
                            $manifest->update([
                                'kd_dok_inout' => $pabean->kd_dok_inout,
                                'no_dok' => $pabean->no_dok_inout,
                                'tgl_dok' => Carbon::createFromFormat('d/m/Y', $pabean->tgl_dok_inout)->format('Y-m-d'),
                                'status_bc' => $statusBC,
                                'alasan_hold' => $alasanFinal,
                            ]);
                        }
                    }
                }
            }

            if ($cont) {
                foreach ($cont as $detail) {
                    $pabeanCont = PabeanCont::create([
                        'pabean_id' => $pabean->id,
                        'car' => $detail->CAR,
                        'no_cont' => $detail->NO_CONT,
                        'ukr_cont' => $detail->UKR_CONT,
                        'size' => $detail->SIZE,
                        'jns_muat' => $detail->JNS_MUAT,
                    ]);
                    if ($pabean->jml_cont > 0) {
                        $contF = ContF::whereNull('tglkeluar')->where('nocontainer', $detail->NO_CONT)->first();
                        if ($contF) {
                            if ($contF->size != $detail->SIZE) {
                                $alasanSize = '& Ukuran Fisik Size Berbeda';
                            }else {
                                $alasanSize = null;
                            }
    
                            $alasanFinal = 'Bukan Dokumen SPPB. ' . $alasanSize;
                            $cust = Customer::where('name', $pabean->nm_imp)->first();
                            if ($cust) {
                                $cust->update([
                                   'name' => $pabean->nm_imp,
                                    'npwp' => $pabean->npwp_imp,
                                    'alamat' => $pabean->al_imp,
                                ]);
                            }
                            $newCust = null;
                            if (!$cust && $pabean->nama_imp != null) {
                                $newCust = Customer::create([
                                    'name' => $pabean->nm_imp,
                                    'npwp' => $pabean->npwp_imp,
                                    'alamat' => $pabean->al_imp,
                                ]);
                            }
                            $contF->update([
                                'kd_dok_inout' => $pabean->kd_dok_inout,
                                'no_dok' => $pabean->no_dok_inout,
                                'tgl_dok' => Carbon::createFromFormat('Ymd', $pabean->tgl_dok_inout)->format('Y-m-d'),
                                'status_bc' => 'HOLD',
                                'alasan_hold' => $alasanFinal,
                                'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
                                'nobl' => $pabean->no_bl_awb,
                              
                            ]);
                        }
                    }
                }
            }
            return back()->with('status', ['type' => 'success', 'message' => 'Data ditemukan']);
         }else {
            return back()->with('status', ['type' => 'error', 'message' => 'Something Wrong']);
         }
    }

    public function GetDokumenPabean()
    {      
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetDokumenPabeanPermit_FASP')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_Tps' => $this->kode
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetDokumenPabeanPermit_FASP', function ($service) use ($data) {        
            $this->response = $service->call('GetDokumenPabeanPermit_FASP', [$data])->GetDokumenPabeanPermit_FASPResult;      
        });
        
//        var_dump($this->response);return false;
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return response()->json([
            'success' => false,
            'message' => 'Error : ' . $this->response,
           ]);
        }

        $groups = [];
        $nextGroup = [];
        $header = [];
        $valueTest = [];

        $detil = [];
        foreach ($xml->children() as $child) {
            $groups[] = $child;
        }
        
        foreach ($groups as $group) {
            $header = $group->header ?? $group->HEADER;
            $oldPabean = Pabean::where('car', $header->CAR)->first();
            if (!$oldPabean) {
                $pabean = Pabean::create([
                    'kd_dok_inout' => $header->KD_DOK_INOUT,
                    'car' => $header->CAR,
                    'no_dok_inout' => $header->NO_DOK_INOUT,
                    'tgl_dok_inout' => $header->TGL_DOK_INOUT,
                    'no_daftar' => $header->NO_DAFTAR,
                    'tgl_daftar' => $header->TGL_DAFTAR,
                    'kd_kantor' => $header->KD_KANTOR,
                    'kd_kantor_pengawas' => $header->KD_KANTOR_PENGAWAS,
                    'kd_kantor_bongkar' => $header->KD_KANTOR_BONGKAR,
                    'npwp_imp' => $header->NPWP_IMP,
                    'nm_imp' => $header->NM_IMP,
                    'al_imp' => $header->AL_IMP,
                    'npwp_ppjk' => $header->NPWP_PPJK,
                    'nm_ppjk' => $header->NM_PPJK,
                    'al_ppjk' => $header->AL_PPJK,
                    'nm_angkut' => $header->NM_ANGKUT,
                    'no_voy_flight' => $header->NO_VOY_FLIGHT,
                    'brutto' => $header->BRUTTO,
                    'netto' => $header->NETTO,
                    'gudang' => $header->GUDANG,
                    'status_jalur' => $header->STATUS_JALUR,
                    'jml_cont' => $header->JML_CONT,
                    'no_bc11' => $header->NO_BC11,
                    'tgl_bc11' => $header->TGL_BC11,
                    'no_pos_bc11' => $header->NO_POS_BC11,
                    'no_bl_awb' => $header->NO_BL_AWB,
                    'tgl_bl_awb' => $header->TGL_BL_AWB,
                    'no_master_bl_awb' => $header->NO_MASTER_BL_AWB,
                    'tgl_master_bl_awb' => $header->TGL_MASTER_BL_AWB,
                    'fl_segel' => $header->FL_SEGEL,
                    'tgl_upload'=>Carbon::today()->format('Y-m-d'),
                    'jam_upload'=>Carbon::now()->format('H:i:s'),
                ]);

                $detil[]  = $group->DETIL ?? $group->detil;       
                foreach ($group->DETIL->CONT as $detailCont) {
                    $pabeanCont = PabeanCont::create([
                        'pabean_id' => $pabean->id,
                        'car' => $detailCont->CAR,
                        'no_cont' => $detailCont->NO_CONT,
                        'ukr_cont' => $detailCont->UKR_CONT,
                        'size' => $detailCont->SIZE,
                        'jns_muat' => $detailCont->JNS_MUAT,
                    ]);

                    if ($pabean->jml_cont > 0) {
                        $contF = ContF::whereNull('tglkeluar')->where('nocontainer', $detailCont->NO_CONT)->first();
                        if ($contF) {
                            if ($contF->size != $detailCont->SIZE) {
                                $alasanSize = '& Ukuran Fisik Size Berbeda';
                            }else {
                                $alasanSize = null;
                            }
    
                            $alasanFinal = 'Bukan Dokumen SPPB. ' . $alasanSize;
                            $cust = Customer::where('name', $pabean->nm_imp)->first();
                            if ($cust) {
                                $cust->update([
                                   'name' => $pabean->nm_imp,
                                    'npwp' => $pabean->npwp_imp,
                                    'alamat' => $pabean->al_imp,
                                ]);
                            }
                            $newCust = null;
                            if (!$cust && $pabean->nama_imp != null) {
                                $newCust = Customer::create([
                                    'name' => $pabean->nm_imp,
                                    'npwp' => $pabean->npwp_imp,
                                    'alamat' => $pabean->al_imp,
                                ]);
                            }
                            $contF->update([
                                'kd_dok_inout' => $pabean->kd_dok_inout,
                                'no_dok' => $pabean->no_dok_inout,
                                'tgl_dok' => Carbon::createFromFormat('Ymd', $pabean->tgl_dok_inout)->format('Y-m-d'),
                                'status_bc' => 'HOLD',
                                'alasan_hold' => $alasanFinal,
                                'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
                                'nobl' => $pabean->no_bl_awb,
                            ]);
                        }
                    }
                }    
                foreach ($group->DETIL->KMS as $detailKMS) {
                    # code...
                    $pabeanKms = PabeanKms::create([
                        'pabean_id' => $pabean->id,
                        'car' => $detailKMS->CAR,
                        'jns_kms' => $detailKMS->JNS_KMS,
                        'jml_kms' => $detailKMS->JML_KMS,
                    ]);          

                    if ($pabean->jml_cont == 0) {
                        $manifest = Manifest::where('nohbl', $pabean->no_bl_awb)->where('tglbuangmty', null)->first();
                        if ($manifest) {
                            $alasanCust = null;
                            $alasanKemas = null;
                            $alasanJml = null;
                            $statusBC = "release";
                            if ($manifest->customer->name != $pabean->nm_imp || $manifest->customer->npwp != $pabean->npwp_imp) {
                                $alasanCust = "Data Importir Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            // Alasan Kemas
                            if ($manifest->packing->code != $pabeanKMS->jns_kms) {
                                $alasanKemas = "Jenis Kemas Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            if ($manifest->quantity != $pabeanKMS) {
                                $alasanJml = "Quantity Berbeda";
                                $statusBC = "HOLD";
                            }
    
                            $alasanFinal = $alasanBasic . ', ' . $alasanCust . ', ' . $alasanKemas . ', ' . $alasanJml;
    
                            $manifest->update([
                                'kd_dok_inout' => $pabean->kd_dok_inout,
                                'no_dok' => $pabean->no_dok_inout,
                                'tgl_dok' => Carbon::createFromFormat('d/m/Y', $pabean->tgl_dok_inout)->format('Y-m-d'),
                                'status_bc' => $statusBC,
                                'alasan_hold' => $alasanFinal,
                            ]);
                        }
                    }
                }
            }
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Data Added',
           ]);
    }
    
}

