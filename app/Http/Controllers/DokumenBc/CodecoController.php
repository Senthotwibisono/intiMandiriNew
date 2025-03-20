<?php

namespace App\Http\Controllers\DokumenBc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Auth;
use Carbon\Carbon;

use App\Models\Container as Cont;
use App\Models\ContainerFCL as ContF;
use App\Models\Manifest;

// coari
use App\Models\Pengiriman\Coari\CoariCont as CC;
use App\Models\Pengiriman\Coari\CoariContDetil as CD;
use App\Models\Pengiriman\Coari\CoariKms as KC;
use App\Models\Pengiriman\Coari\CoariKmsDetil as KD;

// codeco
use App\Models\Pengiriman\Codeco\CodecoCont;
use App\Models\Pengiriman\Codeco\CodecoContDetil;
use App\Models\Pengiriman\Codeco\CodecoKms;
use App\Models\Pengiriman\Codeco\CodecoKmsDetil;

// Reff Number
use App\Models\Pengiriman\RefNumber as RN;

use DataTables;

class CodecoController extends Controller
{
    public function __construct() {
        $this->middleware('auth');

        $this->wsdl = 'https://tpsonline.beacukai.go.id/tps/service.asmx?WSDL';
        $this->user = '1MUT';
        $this->password = 'INTIMANDIRI1';
        $this->kode = 'INTI';
    }

    private function RefNumber()
    {
        return DB::transaction(function(){
            $tahun = Carbon::now()->format('y'); 
            $bulan = Carbon::now()->format('m'); 
            $tanggal = Carbon::now()->format('d');
            
            $lastNomor = RN::where('tahun', $tahun)->where('bulan', $bulan)->where('tanggal', $tanggal)->orderBy('nomor', 'desc')->first();
            if ($lastNomor) {
                $nomor = str_pad($lastNomor->nomor + 1, 4, '0', STR_PAD_LEFT);
            }else {
                $nomor = '0001';
            }
    
            $ref = RN::create([
                'tahun' => $tahun,
                'bulan' => $bulan,
                'tanggal' => $tanggal,
                'nomor' => $nomor,
            ]);
    
            $refNumber = $ref->main . $ref->tahun . $ref->bulan . $ref->tanggal . $ref->nomor;
    
            return $refNumber;
        });
    }

    public function indexContLCL()
    {
        $data['title'] = 'Data Codeco Cont LCL';

        return view('pengiriman.lcl.cont.codeco', $data);
    }

    public function dataContLCL(Request $request)
    {
        $cont = CodecoContDetil::where('jns_cont', 'L')->get();
        return DataTables::of($cont)
        ->addColumn('action', function($cont){
            return '<button class="btn btn-outline-success kirimUlang" id="kirimUlang" data-id="'.$cont->cont_id.'">Kirim Ulang</button>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function sendContLCL(Request $request)
    {
        switch ($request->type) {
            case 'LCL':
                $cont = Cont::find($request->id);
                $typeCont = 'L';
                break;
            case 'FCL':
                $cont = ContF::find($request->id);
                $typeCont = 'F';
            break;
            default:
                # code...
                break;
        }

        \SoapWrapper::override(function ($service) {
            $service
                ->name('CoarriCodeco_Container')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                                                                
                ->cache(WSDL_CACHE_NONE)                                        
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

        $tanggal = Carbon::createFromFormat('Y-m-d', $cont->tglkeluar)->format('Ymd');
        $jam = Carbon::createFromFormat('H:i:s', $cont->jamkeluar)->format('His');
        $wk_in = $tanggal . $jam;
        $header = [
            'nojoborder' => $cont->job->nojoborder,
            'ref_number' => $this->RefNumber(),
            'tgl_entry' => Carbon::now()->format('YYYY-MM-DD'),
            'jam_entry' => Carbon::now()->format('H:i:s'),
            'uid' => 'Auto',
            'nomor' => null,
            'status_ref' => null,
            'ref_number_revisi' => null,
            'flag_revisi' => 0,
            'tgl_revisi' => null,
            'kd_dok' => '6',
            'kd_tps' => '1MUT',
            'nm_angkut' => $cont->job->PLP->nm_angkut ?? $cont->job->Kapal->name ?? null,
            'no_voy_flight' => $cont->job->PLP->no_voy_flight ?? $cont->job->voy ?? null,
            'call_sign' => $cont->job->PLP->call_sign ?? $cont->job_callsign ?? null,
            'tgl_tiba' => $cont->job->PLP->tgl_tiba ?? null,
            'kd_gudang' => '1MUT',
            'kd_dok_inout' => 3,
            'no_dok_inout' => $cont->job->PLP->no_plp,
            'tanggal_dok_inout' => $cont->job->PLP->tgl_plp,
            'wk_inout' => $wk_in,
            'no_pol' => $cont->nopol,
            'bruto' => $cont->weight,
            'no_master_bl_awb' => $cont->job->nombl ?? '',
            'tgl_master_bl_awb' => $cont->job->tgl_master_bl 
               ? Carbon::createFromFormat('Y-m-d', $cont->job->tgl_master_bl)->format('Ymd') 
               : null,
            'no_bl_awb' => $cont->nobl ?? '',
            'tgl_bl_awb' => $cont->tgl_bl_awb 
               ? Carbon::createFromFormat('Y-m-d', $cont->tgl_bl_awb)->format('Ymd') 
               : null,
            'no_cont' => $cont->nocontainer,
            'uk_cont' => $cont->size,
            'no_segel' => $cont->seal->code ?? ' ',
            'jns_cont' => $typeCont,
            'no_bc11' => $cont->job->tno_bc11 ?? '',
            'tgl_bc11' => $cont->job->ttgl_bc11 
                ? Carbon::createFromFormat('Y-m-d', $cont->job->ttgl_bc11)->format('Ymd') 
                : null,
        ];
        
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><DOCUMENT></DOCUMENT>');
        $xmldata = $xml->addAttribute('xmlns', 'cococont.xsd');
        $xmldata = $xml->addchild('COCOCONT');
        $headerXml = $xmldata->addchild('HEADER');
        $detail = $xmldata->addchild('DETIL');
        $contXml = $detail->addChild('CONT');
        $headerXml->addChild('KD_DOK', !empty($header['kd_dok']) ? $header['kd_dok'] : '');
        $headerXml->addChild('KD_TPS', !empty($header['kd_tps']) ? $header['kd_tps'] : '');
        $headerXml->addChild('NM_ANGKUT', !empty($header['nm_angkut']) ? $header['nm_angkut'] : '');
        $headerXml->addChild('NO_VOY_FLIGHT', !empty($header['no_voy_flight']) ? $header['no_voy_flight'] : '');
        $headerXml->addChild('CALL_SIGN', !empty($header['call_sign']) ? $header['call_sign'] : '');
        $headerXml->addChild('TGL_TIBA', !empty($header['tgl_tiba']) ? $header['tgl_tiba'] : '');
        $headerXml->addChild('KD_GUDANG', !empty($header['kd_gudang']) ? $header['kd_gudang'] : '');
        $headerXml->addChild('REF_NUMBER', !empty($header['ref_number']) ? $header['ref_number'] : '');
        $contXml->addChild('NO_CONT', !empty($header['no_cont']) ? $header['no_cont'] : '');
        $contXml->addChild('UK_CONT', !empty($header['uk_cont']) ? $header['uk_cont'] : '');
        $contXml->addChild('NO_SEGEL', !empty($header['no_segel']) ? $header['no_segel'] : '');
        $contXml->addChild('JNS_CONT', !empty($header['jns_cont']) ? $header['jns_cont'] : '');
        $contXml->addChild('NO_BL_AWB', !empty($header['no_bl_awb']) ? $header['no_bl_awb'] : '');
        $contXml->addChild('TGL_BL_AWB', !empty($header['tgl_bl_awb']) ? $header['tgl_bl_awb'] : '');
        $contXml->addChild('NO_MASTER_BL_AWB', !empty($header['no_master_bl_awb']) ? $header['no_master_bl_awb'] : '');
        $contXml->addChild('TGL_MASTER_BL_AWB', !empty($header['tgl_master_bl_awb']) ? $header['tgl_master_bl_awb'] : '');
        $contXml->addChild('ID_CONSIGNEE', ($cont->id_consignee != 000000000000000) ? $cont->id_consignee : '');
        $contXml->addChild('CONSIGNEE', ($cont->consignee != '') ? $cont->consignee : '');
        $contXml->addChild('BRUTO', !empty($header['bruto']) ? $header['bruto'] : 0);
        $contXml->addChild('NO_BC11', !empty($header['no_bc11']) ? $header['no_bc11'] : '');
        $contXml->addChild('TGL_BC11', !empty($header['tgl_bc11']) ? $header['tgl_bc11'] : '');        
        $contXml->addChild('NO_POS_BC11', ($cont->no_pos_bc11 != '') ? $cont->no_pos_bc11 : '');
        $contXml->addChild('KD_TIMBUN', ($cont->kd_timbun != '') ? $cont->kd_timbun : '');
        $contXml->addChild('KD_DOK_INOUT', !empty($header['kd_dok_inout']) ? $header['kd_dok_inout'] : '');
        $contXml->addChild('NO_DOK_INOUT', !empty($header['no_dok_inout']) ? $header['no_dok_inout'] : '');
        $contXml->addChild('TGL_DOK_INOUT', !empty($header['tanggal_dok_inout']) ? $header['tanggal_dok_inout'] : '');
        $contXml->addChild('WK_INOUT', !empty($header['wk_inout']) ? $header['wk_inout'] : '');
        $contXml->addChild('KD_SAR_ANGKUT_INOUT', ($cont->kd_sar_angkut_inout != '') ? $cont->kd_sar_angkut_inout : '');
        $contXml->addChild('NO_POL', !empty($header['no_pol']) ? $header['no_pol'] : '');
        $contXml->addChild('FL_CONT_KOSONG', ($cont->fl_cont_kosong != '') ? $cont->fl_cont_kosong : '');
        $contXml->addChild('ISO_CODE', ($cont->iso_code != '') ? $cont->iso_code : '');
        $pelMuat = ($cont->job && $cont->job->muat && $cont->job->muat->kode) ? $cont->job->muat->kode : '';
        $pelTransit = ($cont->transit && $cont->transit->kode) ? $cont->transit->kode : '';
        $pelBongkar = ($cont->bongkar && $cont->bongkar->kode) ? $cont->bongkar->kode : '';
        
        $contXml->addChild('PEL_MUAT', $pelMuat);
        $contXml->addChild('PEL_TRANSIT', $pelTransit);
        $contXml->addChild('PEL_BONGKAR', $pelBongkar);
        $contXml->addChild('GUDANG_TUJUAN', ($cont->gudang_tujuan != '') ? $cont->gudang_tujuan : '');
        $contXml->addChild('KODE_KANTOR', ($cont->kode_kantor != '') ? $cont->kode_kantor : '');
        $contXml->addChild('NO_DAFTAR_PABEAN', ($cont->no_daftar_pabean != '') ? $cont->no_daftar_pabean : '');
        $contXml->addChild('TGL_DAFTAR_PABEAN', ($cont->tgl_daftar_pabean != '') ? $cont->tgl_daftar_pabean : '');
        $contXml->addChild('NO_SEGEL_BC', ($cont->no_segel_bc != '') ? $cont->no_segel_bc : '');
        $contXml->addChild('TGL_SEGEL_BC', ($cont->tgl_segel_bc != '') ? $cont->tgl_segel_bc : '');
        $contXml->addChild('NO_IJIN_TPS', ($cont->no_ijin_tps != '') ? $cont->no_ijin_tps : '');
        $contXml->addChild('TGL_IJIN_TPS', ($cont->tgl_ijin_tps != '') ? $cont->tgl_ijin_tps : '');
        // dd($xml);
        
        
        $datas = [
            'Username' => $this->user, 
            'Password' => $this->password,
            'fStream' => $xml->asXML()
        ];
        
        \SoapWrapper::service('CoarriCodeco_Container', function ($service) use ($datas) {        
            $this->response = $service->call('CoarriCodeco_Container', [$datas])->CoarriCodeco_ContainerResult;      
        });
        $response = $this->response;
        $hasil = strpos($response, "Proses Berhasil") !== false ? true : false;
        $flag = 'N';
        if ($hasil == true) {
            $flag = 'Y';
        }
        $codecoCont = CodecoCont::create([
            'nojoborder' => $header['nojoborder'],
            'ref_number' => $header['ref_number'],
            'tgl_entry' => Carbon::now()->format('Y-m-d'),
            'jam_entry' => Carbon::now()->format('H:i:s'),
            'uid' => 'Automatic',
            'nomor' => null,
            'status_ref' => $header['status_ref'],
            'ref_number_revisi' => $header['ref_number_revisi'],
            'flag_revisi' => $header['flag_revisi'],
            'tgl_revisi' => $header['tgl_revisi'],
        ]);
        $codecoContDetil = CodecoContDetil::create([
            'codeco' => $codecoCont->id,
            'cont_id' => $cont->id,
            'ref_number' => $codecoCont->ref_number,
            'nojoborder' => $header['nojoborder'],
            'kd_dok' => 5,
            'kd_tps' =>$header['kd_tps'],
            'nm_angkut' =>$header['nm_angkut'],
            'no_voy_flight' =>$header['no_voy_flight'],
            'call_sign' =>$header['call_sign'],
            'tgl_tiba' =>$header['tgl_tiba'],
            'kd_gudang' =>$header['kd_gudang'],
            'no_cont' => $header['no_cont'],
            'uk_cont' => $header['uk_cont'],
            'no_segel' =>$header['no_segel'],
            'jns_cont' => $header['jns_cont'],
             'no_bl_awb' => $header['no_bl_awb'],
            'tgl_bl_awb' => $header['tgl_bl_awb'],
            'no_bl_awb' => $header['no_bl_awb'],
            'tgl_bl_awb' => $header['tgl_bl_awb'],
            'bruto' => $cont->weight,
            'no_bc11' => $cont->job->tno_bc11,
            'tgl_bc11' => $header['tgl_bc11'],
            'kd_dok_inout' => $header['kd_dok_inout'],
            'no_dok_inout' => $header['no_dok_inout'],
            'tgl_dok_inout' => $header['tanggal_dok_inout'],
            'wk_inout' => $header['wk_inout'],
            'no_pol' => $cont->nopol,
            'pel_muat' => $pelMuat,
            'pel_transit' => $pelTransit,
            'pel_bongkar' => $pelBongkar,
            'gudang_tujuan' => $cont->job->PLP->gudang_tujuan,
            'uid' => 'Auto',
            'response' => $response,
            'kode_kantor' => $cont->job->PLP->kd_kantor,
            'noplp' => $cont->job->PLP->no_plp,
            'tglplp' => $cont->job->PLP->tgl_plp,
            'tgl_entry' => $codecoCont->tgl_entry,
            'jam_entry' => $codecoCont->jam_entry,
        ]);
        $cont->update([
            'codeco_flag' => $flag,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Behrasil Upload',
        ]);
    }

    public function indexManifestLCL()
    {
        $data['title'] = 'Data Codeco Manifest LCL';

        $data['manifestes'] = Manifest::whereNotNull('tglrelease')->where('codeco_flag', 'N')->orWhereNull('codeco_flag')->get();

        return view('pengiriman.lcl.manifest.codeco', $data);
    }

    public function dataManifestLCL(Request $request)
    {
        $manifest = CodecoKmsDetil::get();
        return DataTables::of($manifest)
        ->addColumn('action', function($manifest){
            return '<button class="btn btn-outline-success kirimUlangCoari" id="kirimUlangCoari" data-id="'.$manifest->manifest_id.'">Kirim Ulang</button>';
        })
        ->addColumn('cfs', function($manifest){
            return '<button class="btn btn-outline-warning sendCFS" id="sendCFS" data-id="'.$manifest->manifest_id.'">Kirim Ulang</button>';
        })
        ->addColumn('cont', function($manifest){
            return $manifest->manifest->cont->nocontainer;
        })
        ->addColumn('size', function($manifest){
            return $manifest->manifest->cont->size;
        })
        ->rawColumns(['action', 'cfs'])
        ->make(true);
    }

    public function kirimManualManifest(Request $request)
    {
        $manifest = Manifest::findOrFail($request->id);

        // dd($manifest, $request->all());       
        \SoapWrapper::override(function ($service) {
            $service
                ->name('CoarriCodeco_Kemasan')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                                                               
                ->cache(WSDL_CACHE_NONE)                                        
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

        $cont = Cont::findOrFail($manifest->id);
        $header = [
            'nojoborder' => $cont->job->nojoborder,
            'ref_number' => $this->RefNumber(),
            'tgl_entry' => Carbon::now()->format('YYYY-MM-DD'),
            'jam_entry' => Carbon::now()->format('H:i:s'),
            'uid' => 'Auto',
            'nomor' => null,
            'status_ref' => null,
            'ref_number_revisi' => null,
            'flag_revisi' => 0,
            'tgl_revisi' => null,
            'kd_dok' => '6',
            'kd_tps' => '1MUT',
            'nm_angkut' => $cont->job->PLP->nm_angkut ?? $cont->job->Kapal->name ?? null,
            'no_voy_flight' => $cont->job->PLP->no_voy_flight ?? $cont->job->voy ?? null,
            'call_sign' => $cont->job->PLP->call_sign ?? $cont->job_callsign ?? null,
            'tgl_tiba' => $cont->job->PLP->tgl_tiba ?? null,
            'kd_gudang' => '1MUT',
            'no_master_bl_awb' => $cont->job->nombl,
            'tgl_master_bl_awb' => $cont->job->tgl_master_bl 
               ? Carbon::createFromFormat('Y-m-d', $cont->job->tgl_master_bl)->format('Ymd') 
               : null,
            'no_cont' => $cont->nocontainer,
            'uk_cont' => $cont->size,
            'no_segel' => $cont->seal->code ?? ' ',
            'jns_cont' => 'F',
            'no_bc11' => $cont->job->tno_bc11 ?? '',
            'tgl_bc11' => $cont->job->ttgl_bc11 
                ? Carbon::createFromFormat('Y-m-d', $cont->job->ttgl_bc11)->format('Ymd') 
                : null,
        ];
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><DOCUMENT></DOCUMENT>');       
        
        $xmldata = $xml->addAttribute('xmlns', 'cocokms.xsd');
        $xmldata = $xml->addchild('COCOKMS');
        $headerXml = $xmldata->addchild('HEADER');
        $detail = $xmldata->addchild('DETIL');
        
        $headerXml->addChild('KD_DOK', !empty($header['kd_dok']) ? $header['kd_dok'] : '');
        $headerXml->addChild('KD_TPS', !empty($header['kd_tps']) ? $header['kd_tps'] : '');
        $headerXml->addChild('NM_ANGKUT', !empty($header['nm_angkut']) ? $header['nm_angkut'] : '');
        $headerXml->addChild('NO_VOY_FLIGHT', !empty($header['no_voy_flight']) ? $header['no_voy_flight'] : '');
        $headerXml->addChild('CALL_SIGN', !empty($header['call_sign']) ? $header['call_sign'] : '');
        $headerXml->addChild('TGL_TIBA', !empty($header['tgl_tiba']) ? $header['tgl_tiba'] : '');
        $headerXml->addChild('KD_GUDANG', !empty($header['kd_gudang']) ? $header['kd_gudang'] : '');
        $headerXml->addChild('REF_NUMBER', !empty($header['ref_number']) ? $header['ref_number'] : '');
        
        $dataDetailkms = $manifest;
        $tgl_dok = $dataDetailkms->tgl_dok ? Carbon::createFromFormat('Y-m-d', $dataDetailkms->tgl_dok)->format('Ymd') : null;
        $tanggal = Carbon::createFromFormat('Y-m-d', $dataDetailkms->tglrelease)->format('Ymd');
        $jam = Carbon::createFromFormat('H:i:s', $dataDetailkms->jamrelease)->format('His');
        $wk_in = $tanggal . $jam;
        $detilData = [
            'tanggal_dok_inout'=> $tgl_dok,
            'kd_dok_inout' => $dataDetailkms->kd_dok_inout ?? null,
            'no_dok_inout' => $dataDetailkms->no_dok ?? null,
            'wk_inout' => $wk_in,
            'no_pol' => $dataDetailkms->nopol_mty ?? null,
        ];
        $kms = $detail->addChild('KMS');

        $kms->addChild('NO_BL_AWB', $dataDetailkms->nohbl);
        $kms->addChild('TGL_BL_AWB', $dataDetailkms->tgl_hbl 
        ? Carbon::createFromFormat('Y-m-d', $dataDetailkms->tgl_hbl)->format('Ymd') 
        : null,); 
        $kms->addChild('NO_MASTER_BL_AWB', $dataDetailkms->cont->job->nombl); 
        $kms->addChild('TGL_MASTER_BL_AWB', $header['tgl_master_bl_awb']); 
        $kms->addChild('ID_CONSIGNEE', ($dataDetailkms->customer_id ?? '00000000'));
        $kms->addChild('CONSIGNEE', htmlspecialchars($dataDetailkms->customer->name ?? ''));
        $kms->addChild('BRUTO', $dataDetailkms->weight ?? 0 );
        $kms->addChild('NO_BC11', $header['no_bc11']);
        $kms->addChild('TGL_BC11', $header['tgl_bc11'] );
        $kms->addChild('NO_POS_BC11', $dataDetailkms->NO_POS_BC11 );
        $kms->addChild('CONT_ASAL', $dataDetailkms->CONT_ASAL );
        $kms->addChild('SERI_KEMAS', 1 );
        $kms->addChild('KD_KEMAS', $dataDetailkms->packing->code ?? null );
        $kms->addChild('JML_KEMAS', $dataDetailkms->quantity );
        $kms->addChild('KD_TIMBUN', $dataDetailkms->KD_TIMBUN );
        $kms->addChild('KD_DOK_INOUT', $detilData['kd_dok_inout'] );
        $kms->addChild('NO_DOK_INOUT', $detilData['no_dok_inout'] );
        $kms->addChild('TGL_DOK_INOUT', $detilData['tanggal_dok_inout'] );
        $kms->addChild('WK_INOUT', $detilData['wk_inout'] );
        $kms->addChild('KD_SAR_ANGKUT_INOUT', $dataDetailkms->KD_SAR_ANGKUT_INOUT );
        $kms->addChild('NO_POL', $detilData['no_pol']);
        $pelMuat = ($cont->job && $cont->job->muat && $cont->job->muat->kode) ? $cont->job->muat->kode : '';
        $pelTransit = ($cont->transit && $cont->transit->kode) ? $cont->job->transit->kode : '';
        $pelBongkar = ($cont->bongkar && $cont->bongkar->kode) ? $cont->job->bongkar->kode : '';
        
        $kms->addChild('PEL_MUAT', $pelMuat);
        $kms->addChild('PEL_TRANSIT', $pelTransit);
        $kms->addChild('PEL_BONGKAR', $pelBongkar);
        $kms->addChild('GUDANG_TUJUAN', '1MUT');
        $kms->addChild('KODE_KANTOR', !empty($dataDetailkms->kode_kantor) ? $dataDetailkms->kode_kantor : '');
        $kms->addChild('NO_DAFTAR_PABEAN', !empty($dataDetailkms->no_daftar_pabean) ? $dataDetailkms->no_daftar_pabean : '');
        $kms->addChild('TGL_DAFTAR_PABEAN', !empty($dataDetailkms->tgl_daftar_pabean) ? $dataDetailkms->tgl_daftar_pabean : '');        
        $kms->addChild('NO_SEGEL_BC', $dataDetailkms->NO_SEGEL_BC);
        $kms->addChild('TGL_SEGEL_BC', $dataDetailkms->TGL_SEGEL_BC );
        $kms->addChild('NO_IJIN_TPS', $dataDetailkms->NO_IJIN_TPS );
        $kms->addChild('TGL_IJIN_TPS', $dataDetailkms->TGL_IJIN_TPS);
        
        $data = [
            'Username' => $this->user, 
            'Password' => $this->password,
            'fStream' => $xml->asXML()
        ];
        
        // Using the added service
        \SoapWrapper::service('CoarriCodeco_Kemasan', function ($service) use ($data) {        
            $this->response = $service->call('CoarriCodeco_Kemasan', [$data])->CoarriCodeco_KemasanResult;      
        });
        $response = $this->response;
        $hasil = strpos($response, "Proses Berhasil") !== false ? true : false;

        // dd($xml, $response, $hasil, $manifest, $detilData);
        $flag = 'N';
        if ($hasil == true) {
            $flag = 'Y';
        }
        $codecoKMS = CodecoKms::create([
            'nojoborder' => $header['nojoborder'],
            'tgl_entry' => Carbon::now()->format('Y-m-d'),
            'jam_entry' => Carbon::now()->format('H:i:s'),
            'ref_number' => $header['ref_number'],
            'uid' => 'Auto',
            'nomor' => null,
        ]);
        
        $data = $manifest;
        $tgl_dok = $data->tgl_dok ? Carbon::createFromFormat('Y-m-d', $data->tgl_dok)->format('Ymd') : null;
        $tanggal = Carbon::createFromFormat('Y-m-d', $data->tglrelease)->format('Ymd');
        $jam = Carbon::createFromFormat('H:i:s', $data->jamrelease)->format('His');
        $wk_in = $tanggal . $jam;
        $detilData = [
            'tanggal_dok_inout'=> $tgl_dok,
            'kd_dok_inout' => $data->kd_dok_inout ?? null,
            'no_dok_inout' => $data->no_dok_inout ?? null,
            'wk_inout' => $wk_in,
            'no_pol' => $data->nopol_mty ?? null,
        ];
        $coariKmsDetil = CodecoKmsDetil::create([
            'nojoborder' => $header['nojoborder'],
            'codeco_id' => $codecoKMS->id,
            'manifest_id' => $data->id,
            'ref_number' => $codecoKMS->ref_number,
            'notally' => $data->notally,
            'kd_dok' => '5',
            'kd_tps' =>$header['kd_tps'],
            'nm_angkut' =>$header['nm_angkut'],
            'no_voy_flight' => $header['no_voy_flight'],
            'call_sign' => $header['call_sign'],
            'tgl_tiba' => $header['tgl_tiba'],
            'kd_gudang' => $header['kd_gudang'],
            'no_bl_awb' => $data->nohbl,
            'tgl_bl_awb' => $data->tgl_hbl 
            ? Carbon::createFromFormat('Y-m-d', $data->tgl_hbl)->format('Ymd') 
            : null,
            'no_master_bl_awb' => $data->cont->job->nombl,
            'tgl_master_bl_awb' => $header['tgl_master_bl_awb'],
            'id_consignee' => $data->customer_id ?? null,
            'consignee' => $data->customer->name ?? null,
            'bruto' => $data->weight,
            'no_bc11' => $header['no_bc11'],
            'tgl_bc11' => $header['tgl_bc11'],
            
            'seri_kemas' => 1,
            'kd_kemas' => $data->packing->code ?? null,
            'jml_kemas' => $data->quantity,
            'kd_timbun',
            'kd_dok_inout' => $data->dokumen->kode ?? null,
            'no_dok_inout' => $data->no_dok,
            'tgl_dok_inout' => $detilData['tanggal_dok_inout'],
            'wk_inout' => $detilData['wk_inout'],
            'no_pol' => $detilData['no_pol'],
            'pel_muat' => $data->cont->job->muat->kode ?? null,
            'pel_transit' => $data->cont->job->transit->kode ?? null,
            'pel_bongkar' => $data->cont->job->bongkar->kode ?? null,
            'gudang_tujuan' => $data->gudang_tujuan ?? null,
            'uid' => 'Auto',
            'response' => $response,
            'kode_kantor' => $data->cont->job->PLP->kode_kantor ?? null,
            'kd_tps_asal' => $data->cont->job->PLP->kd_tps_asal ?? null,
            'tgl_entry' => $codecoKMS->tgl_entry,
            'jam_entry' => $codecoKMS->jam_entry,
        ]);
        $data->update([
            'codeco_flag' => $flag,
        ]);

        if ($flag == 'Y') {
            return response()->json([
                'success' => true,
                'message' => $response,
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => $response,
            ]);
        }

        return 'success';
    }

    public function indexContFCL()
    {
        $data['title'] = 'Data Codeco Cont FCL';

        return view('pengiriman.fcl.codeco', $data);
    }

    public function dataContFCL(Request $request)
    {
        $cont = CodecoContDetil::where('jns_cont', 'F')->get();
        return DataTables::of($cont)
        ->addColumn('action', function($cont){
            return '<button class="btn btn-outline-success kirimUlang" id="kirimUlang" data-id="'.$cont->cont_id.'">Kirim Ulang</button>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }

}
