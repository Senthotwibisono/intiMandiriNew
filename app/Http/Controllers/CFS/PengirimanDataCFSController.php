<?php

namespace App\Http\Controllers\CFS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Manifest;
use App\Models\Container as Cont;
use App\Models\Item;

class PengirimanDataCFSController extends Controller
{
    private string $wsdl;
    private string $user;
    private string $password;
    private string $kode;
    protected $response;

    public function __construct() {
        // $this->middleware('auth');

        $this->wsdl = 'https://ipccfscenter.com/TPSServices/server_plp_dev.php?wsdl';
        $this->user = '1MUT';
        $this->password = '1MUT';
        $this->kode = '1MUT';
    }

    public function CoariCont()
    {
        // $conts = Cont::whereNotNull('tglmasuk')->get();
        $cont = Cont::find(3);

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

    //     foreach ($conts as $cont) {
            $tglMasuk = $cont->tglmasuk ? Carbon::parse($cont->tglmasuk ?? $cont->tglmasuk)->format('Ymd') : null;
            $jamMasuk = $cont->jammasuk ? Carbon::parse($cont->jammasuk ?? $cont->jammasuk)->format('His') : null;
            $tglTiba = Carbon::parse($cont->job->eta)->format('Ymd');
            $dataHeader = [
                'kd_dok' => 5,
                'kd_tps' => '1MUT',
                'nm_angkut' => $cont->job->PLP->nm_angkut ?? '-',
                'no_voy_flight' => $cont->job->voy ?? '-',
                'call_sign' => $cont->job->PLP->call_sign ?? '-',
                'TGL_TIBA' => $tglTiba,
                'KD_GUDANG' => '1MUT',
                'REF_NUMBER' => $cont->job->nojoborder ?? '-',
            ];
            $wkInOut = $tglMasuk.$jamMasuk;
            $tglBl = $cont->tgl_bl_awb ? Carbon::parse($cont->tgl_bl_awb)->format('Ymd') : null;
            $tglBC11 = $cont->job->ttgl_bc11 ? Carbon::parse($cont->job->ttgl_bc11)->format('Ymd') : null; 
            $tglPLP = $cont->jon->ttgl_plp ? Carbon::parse($cont->jon->ttgl_plp)->format('Ymd') : null;
            $pelMuat = ($cont->job && $cont->job->muat && $cont->job->muat->kode) ? $cont->job->muat->kode : '';
            $pelTransit = ($cont->transit && $cont->transit->kode) ? $cont->job->transit->kode : '';
            $pelBongkar = ($cont->bongkar && $cont->bongkar->kode) ? $cont->job->bongkar->kode : '';
            $dataDetil = [
                'no_cont' => $cont->nocontainer,
                'uk_cont' => $cont->size,
                'no_segel' => '-',
                'jns_cont' => 'L',
                'no_bl_awb' => $cont->nobl ?? '',
                'tgl_bl_awb' => $tglBl,
                'no_master_bl_awb' => '', 
                'tgl_master_bl_awb' => null, 
                'id_conseignee' => '',
                'conseignee' => '',
                'bruto' => $cont->weight ?? 0,
                'no_bc11' => $cont->job->tno_bc11 ?? '',
                'tgl_bc11' => $tglBC11,
                'no_pos_bc11' => '',
                'kd_timbun' => '',
                'kd_dok_inout' => 3,
                'no_dok_inout' => $cont->job->noplp ?? $cont->job->PLP->no_plp ?? '-',
                'tgl_dok_inout' => $tglPLP,
                'wk_inout' => $wkInOut,
                'kd_sar_angkut_inout' => '',
                'nopol' => $cont->nopol ?? '',
                'fl_cont_kosong' => '',
                'iso_code' => '',
                'pel_muat' => $pelMuat ?? '-',
                'pel_transit' => $pelTransit ?? '-',
                'pel_bongkar' => $pelBongkar ?? '-',
                'gudang_tujuan' => '1MUT',
                'kode_kantor' => '040300',
                'no_daftar_pabean' => '-',
                'tgl_daftar_pabean' => null,
                'no_segel_bc' => ' ',
                'tgl_segel_bc' => null,
                'no_ijin_tps' => ' ',
                'tgl_ijin_tps' => null,
            ];

            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><DOCUMENT></DOCUMENT>');

            // Tambahkan elemen utama
            $xmldata = $xml->addAttribute('xmlns', 'cococont.xsd');
            $xmldata = $xml->addchild('COCOCONT');
            $headerXml = $xmldata->addChild('HEADER');
            $detailXml = $xmldata->addChild('DETIL');
            $kmsXml = $detailXml->addChild('CONT');

            $this->arrayToXml($dataHeader, $headerXml);
            $this->arrayToXml($dataDetil, $kmsXml);
            
            // Dump hasil XML
            // dd($xml);
    
            $datas = [
                'Username' => $this->user, 
                'Password' => $this->password,
                'fStream' => $xml->asXML()
            ];

            // dd($datas);
            
            \SoapWrapper::service('CoarriCodeco_Container', function ($service) use ($datas) {        
                $this->response = $service->call('CoarriCodeco_Container', [$datas]);      
            });
            $response = $this->response;
    
            $hasil = strpos($response, "Proses Berhasil") !== false ? true : false;
            $flag = 'N';
            if ($hasil == true) {
                $flag = 'Y';
            }
            dd($xml, $this->response);
    //    }

    }

    public function CodecoCont()
    {
        // $conts = Cont::whereNotNull('tglmasuk')->get();
        $cont = Cont::find(3);

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

    //     foreach ($conts as $cont) {
            $tglMasuk = $cont->tglkeluar ? Carbon::parse($cont->tglkeluar ?? $cont->tglkeluar)->format('Ymd') : null;
            $jamMasuk = $cont->jamkeluar ? Carbon::parse($cont->jamkeluar ?? $cont->jamkeluar)->format('His') : null;
            $tglTiba = Carbon::parse($cont->job->eta)->format('Ymd');
            $dataHeader = [
                'kd_dok' => 6,
                'kd_tps' => '1MUT',
                'nm_angkut' => $cont->job->PLP->nm_angkut ?? '-',
                'no_voy_flight' => $cont->job->voy ?? '-',
                'call_sign' => $cont->job->PLP->call_sign ?? '-',
                'TGL_TIBA' => $tglTiba,
                'KD_GUDANG' => '1MUT',
                'REF_NUMBER' => $cont->job->nojoborder ?? '-',
            ];
            $wkInOut = $tglMasuk.$jamMasuk;
            $tglBl = $cont->tgl_bl_awb ? Carbon::parse($cont->tgl_bl_awb)->format('Ymd') : null;
            $tglBC11 = $cont->job->ttgl_bc11 ? Carbon::parse($cont->job->ttgl_bc11)->format('Ymd') : null; 
            $tglPLP = $cont->jon->ttgl_plp ? Carbon::parse($cont->jon->ttgl_plp)->format('Ymd') : null;
            $pelMuat = ($cont->job && $cont->job->muat && $cont->job->muat->kode) ? $cont->job->muat->kode : '';
            $pelTransit = ($cont->transit && $cont->transit->kode) ? $cont->job->transit->kode : '';
            $pelBongkar = ($cont->bongkar && $cont->bongkar->kode) ? $cont->job->bongkar->kode : '';
            $dataDetil = [
                'no_cont' => $cont->nocontainer,
                'uk_cont' => $cont->size,
                'no_segel' => '-',
                'jns_cont' => 'L',
                'no_bl_awb' => $cont->nobl ?? '',
                'tgl_bl_awb' => $tglBl,
                'no_master_bl_awb' => '', 
                'tgl_master_bl_awb' => '', 
                'id_conseignee' => '',
                'conseignee' => '',
                'bruto' => $cont->weight ?? 0,
                'no_bc11' => $cont->job->tno_bc11 ?? '',
                'tgl_bc11' => $tglBC11,
                'no_pos_bc11' => '',
                'kd_timbun' => '',
                'kd_dok_inout' => 3,
                'no_dok_inout' => $cont->job->noplp ?? $cont->job->PLP->no_plp ?? '-',
                'tgl_dok_inout' => $tglPLP,
                'wk_inout' => $wkInOut,
                'kd_sar_angkut_inout' => '',
                'nopol' => $cont->nopol ?? '',
                'fl_cont_kosong' => '',
                'iso_code' => '',
                'pel_muat' => $pelMuat ?? '-',
                'pel_transit' => $pelTransit ?? '-',
                'pel_bongkar' => $pelBongkar ?? '-',
                'gudang_tujuan' => '1MUT',
                'kode_kantor' => '040300',
                'no_daftar_pabean' => '-',
                'tgl_daftar_pabean' => '-',
                'no_segel_bc' => ' ',
                'tgl_segel_bc' => ' ',
                'no_ijin_tps' => ' ',
                'tgl_ijin_tps' => ' ',
            ];

            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><DOCUMENT></DOCUMENT>');

            // Tambahkan elemen utama
            $xmldata = $xml->addAttribute('xmlns', 'cococont.xsd');
            $xmldata = $xml->addchild('COCOCONT');
            $headerXml = $xmldata->addChild('HEADER');
            $detailXml = $xmldata->addChild('DETIL');
            $kmsXml = $detailXml->addChild('CONT');

            $this->arrayToXml($dataHeader, $headerXml);
            $this->arrayToXml($dataDetil, $kmsXml);
            
            // Dump hasil XML
            // dd($xml);
    
            $datas = [
                'Username' => $this->user, 
                'Password' => $this->password,
                'fStream' => $xml->asXML()
            ];

            // dd($datas);
            
            \SoapWrapper::service('CoarriCodeco_Container', function ($service) use ($datas) {        
                $this->response = $service->call('CoarriCodeco_Container', [$datas]);      
            });
            $response = $this->response;
    
            $hasil = strpos($response, "Proses Berhasil") !== false ? true : false;
            $flag = 'N';
            if ($hasil == true) {
                $flag = 'Y';
            }
            dd($xml, $this->response);
    //    }

    }

    public function CoariKMS()
    {
        $manifestes = Manifest::whereNotNull('tglstripping')->whereNotNull('tgl_hbl')->take(1)->get();
        // dd($manifest);

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
        foreach ($manifestes as $manifest) {
            $tglMasuk = $manifest->tglmasuk ? Carbon::parse($manifest->tglmasuk ?? $manifest->cont->tglmasuk)->format('Ymd') : null;
            $jamMasuk = $manifest->jammasuk ? Carbon::parse($manifest->jammasuk ?? $manifest->cont->jammasuk)->format('His') : null;
            $tglTiba = Carbon::parse($manifest->job->eta)->format('Ymd');
            $dataHeader = [
                'kd_dok' => 5,
                'kd_tps' => '1MUT',
                'nm_angkut' => $manifest->job->PLP->nm_angkut ?? '-',
                'no_voy_flight' => $manifest->job->voy ?? '-',
                'call_sign' => $manifest->job->PLP->call_sign ?? '-',
                'TGL_TIBA' => $tglTiba,
                'KD_GUDANG' => '1MUT',
                'REF_NUMBER' => $manifest->notally ?? '-',
            ];

            $tglBLawb = $manifest->tgl_hbl ? Carbon::parse($manifest->tgl_hbl)->format('Ymd') : null;
            // dd($tglBLawb);
            $tglMasterBL = $manifest->job->tgl_master_bl ? Carbon::parse($manifest->job->tgl_master_bl)->format('Ymd') : null;
            $tglBC11 = $manifest->job->ttgl_bc11 ? Carbon::parse($manifest->job->ttgl_bc11)->format('Ymd') : null;
            $tglDokInout = $manifest->job->ttgl_plp ? Carbon::parse($manifest->job->ttgl_plp)->format('Ymd') : null;
            $wkInOut = $tglMasuk.$jamMasuk;
            $pelMuat = ($manifest->cont->job && $manifest->cont->job->muat && $manifest->cont->job->muat->kode) ? $manifest->cont->job->muat->kode : '';
            $pelTransit = ($manifest->cont->transit && $manifest->cont->transit->kode) ? $manifest->cont->job->transit->kode : '';
            $pelBongkar = ($manifest->cont->bongkar && $manifest->cont->bongkar->kode) ? $manifest->cont->job->bongkar->kode : '';
            $dataDetil = [
                'no_bl_awb' => $manifest->nohbl,
                'tgl_bl_awb' => $tglBLawb,
                'no_master_bl_awb' => $manifest->job->nombl,
                'tgl_master_bl_awb' => $tglMasterBL,
                'id_consignee' => $manifest->customer->id ?? null,
                'consignee' => $manifest->customer->name ?? null,
                'bruto' => $manifest->weight ?? 0,
                'no_bc11' => $manifest->job->tno_bc11,
                'tgl_bc11' => $tglBC11,
                'no_pos_bc11' => $manifest->no_pos_bc11 ?? null,
                'cont_asal' => $manifest->cont->nocontainer ?? null,
                'seri_kemas' => 1,
                'kd_kemas' => $manifest->packing->code ?? null,
                'jml_kemas' => $manifest->quantity ?? 0,
                'kd_timbun' => $manifest->kd_timbun ?? null,
                'kd_dok_inout' => 3,
                'no_dok_inout' => $manifest->job->noplp,
                'tgl_dok_inout' => $tglDokInout,
                'wk_inout' => $wkInOut,
                'kd_sar_angkut_inout' => 1,
                'no_pol' => $manifest->cont->nopol ?? null,
                'pel_muat' => $pelMuat ?? null,
                'pel_transit' => $pelTransit ?? null,
                'pel_bongkar' => $pelBongkar ?? null,
                'gudang_tujuan' => '1MUT',
                'kode_kantor' => '040300',
                'no_daftar_pabean' => null,
                'tgl_daftar_pabean' => null,
                'no_segel_bc' => null,
                'tgl_segel_bc' => null,
                'no_ijin_tps' => null,
                'tgl_ijin_tps' => null,
                'nama_consolidator' => 'PT INTI MANDIRI UTAMA TRANS',
                'npwp_consolidator' => '0022383483042000',
                'alamat_consolidator' => 'Jl. Bugis Raya No. 15 Kebon Bawang Tanjung Priok',
            ];



            // Tambahkan elemen utama
            $xml = new \SimpleXMLElement('<DOCUMENT xmlns="cocokms.xsd"/>');

            // Tambahkan namespace ke elemen root
            // $xml->addAttribute('xmlns', 'cocokms.xsd');
                    
            // Tambahkan elemen utama
            $xmldata = $xml->addChild('COCOKMS');
            $headerXml = $xmldata->addChild('HEADER');
            $detailXml = $xmldata->addChild('DETIL');
            $kmsXml = $detailXml->addChild('KMS');
                    
            // Konversi array ke XML
            $this->arrayToXml($dataHeader, $headerXml);
            $this->arrayToXml($dataDetil, $kmsXml);
                    
            // Tambahkan deklarasi XML secara manual saat menyimpan atau mencetak
            // $dom = new DOMDocument('1.0', 'UTF-8');
            // $dom->preserveWhiteSpace = false;
            // $dom->formatOutput = true;
            // $dom->loadXML($xml->asXML());
                    
            // // Output hasil XML dengan deklarasi yang benar
            // echo $dom->saveXML();
            
            // Dump hasil XML
            // dd($xml);

            $output = preg_replace('/\s+/', ' ', $xml->asXML());
            // dd($output);
    
            $datas = [
                'Username' => $this->user, 
                'Password' => $this->password,
                'fStream' => $output,
            ];

            // dd($datas);
            try {
                \SoapWrapper::service('CoarriCodeco_Kemasan', function ($service) use ($datas) {        
                    $this->response = $service->call('CoarriCodeco_Kemasan', [$datas]);  
                    dd($this->response, $datas);    
                });
                $response = $this->response;
        
                $hasil = strpos($response, "Proses Berhasil") !== false ? true : false;
                $flag = 'N';
                if ($hasil == true) {
                    $flag = 'Y';
                }
            } catch (\Throwable $th) {
                dd($th->getMessage());
            }
           
        }

    }


    public function CodecoKMS()
    {
        $manifestes = Manifest::whereNotNull('tglstripping')->whereNotNull('tglrelease')->take(1)->get();
        // dd($manifest);

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
        foreach ($manifestes as $manifest) {
            $tglMasuk = $manifest->tglrelease ? Carbon::parse($manifest->tglrelease ?? $manifest->cont->tglrelease)->format('Ymd') : null;
            $jamMasuk = $manifest->jamrelease ? Carbon::parse($manifest->jamrelease ?? $manifest->cont->jamrelease)->format('His') : null;
            $tglTiba = Carbon::parse($manifest->job->eta)->format('Ymd');
            $dataHeader = [
                'kd_dok' => 6,
                'kd_tps' => '1MUT',
                'nm_angkut' => $manifest->job->PLP->nm_angkut ?? '-',
                'no_voy_flight' => $manifest->job->voy ?? '-',
                'call_sign' => $manifest->job->PLP->call_sign ?? '-',
                'TGL_TIBA' => $tglTiba,
                'KD_GUDANG' => '1MUT',
                'REF_NUMBER' => $manifest->notally ?? '-',
            ];

            $tglBLawb = $manifest->tglhbl ? Carbon::parse($manifest->tgl_hbl)->format('Ymd') : null;
            $tglMasterBL = $manifest->job->tgl_master_bl ? Carbon::parse($manifest->job->tgl_master_bl)->format('Ymd') : null;
            $tglBC11 = $manifest->job->ttgl_bc11 ? Carbon::parse($manifest->job->ttgl_bc11)->format('Ymd') : null;
            $tglDokInout = $manifest->tgl_dok ? Carbon::parse($manifest->tgl_dok)->format('Ymd') : null;
            $wkInOut = $tglMasuk.$jamMasuk;
            $pelMuat = ($manifest->cont->job && $manifest->cont->job->muat && $manifest->cont->job->muat->kode) ? $manifest->cont->job->muat->kode : '';
            $pelTransit = ($manifest->cont->transit && $manifest->cont->transit->kode) ? $manifest->cont->job->transit->kode : '';
            $pelBongkar = ($manifest->cont->bongkar && $manifest->cont->bongkar->kode) ? $manifest->cont->job->bongkar->kode : '';
            $dataDetil = [
                'no_bl_awb' => $manifest->nohbl,
                'tgl_bl_awb' => $tglBLawb,
                'no_master_bl_awb' => $manifest->job->nombl,
                'tgl_master_bl_awb' => $tglMasterBL,
                'id_consignee' => $manifest->customer->id ?? '-',
                'consignee' => $manifest->customer->name ?? '-',
                'bruto' => $manifest->weight ?? 0,
                'no_bc11' => $manifest->job->tno_bc11,
                'tgl_bc11' => $tglBC11,
                'no_pos_bc11' => $manifest->no_pos_bc11 ?? '-',
                'cont_asal' => $manifest->cont->nocontainer ?? '-',
                'seri_kemas' => 1,
                'kd_kemas' => $manifest->packing->code ?? '-',
                'jml_kemas' => $manifest->quantity ?? 0,
                'kd_timbun' => $manifest->kd_timbun ?? '-',
                'kd_dok_inout' => $manifest->kd_dok_inout,
                'no_dok_input' => $manifest->no_dok,
                'tgl_dok_inout' => $tglDokInout,
                'wk_inout' => $wkInOut,
                'kd_sar_angkut_inout' => 1,
                'no_pol' => $manifest->cont->nopol ?? '-',
                'pel_muat' => $pelMuat ?? '-',
                'pel_transit' => $pelTransit ?? '-',
                'pel_bongkar' => $pelBongkar ?? '-',
                'gudang_tujuan' => '1MUT',
                'kode_kantor' => '040300',
                'no_daftar_pabean' => '-',
                'tgl_daftar_pabean' => '-',
                'no_segel_bc' => ' ',
                'tgl_segel_bc' => ' ',
                'no_ijin_tps' => ' ',
                'tgl_ijin_tps' => ' ',
                'nama_consolidator' => 'PT INTI MANDIRI UTAMA TRANS',
                'npwp_consolidator' => '0022383483042000',
                'alamat_consolidator' => 'Jl. Bugis Raya No. 15 Kebon Bawang Tanjung Priok',
            ];

            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><DOCUMENT></DOCUMENT>');

            // Tambahkan elemen utama
            $xmldata = $xml->addAttribute('xmlns', 'cocokms.xsd');
            $xmldata = $xml->addchild('COCOKMS');
            $headerXml = $xmldata->addChild('HEADER');
            $detailXml = $xmldata->addChild('DETIL');
            $kmsXml = $detailXml->addChild('KMS');
            
            // Konversi array ke XML
            $this->arrayToXml($dataHeader, $headerXml);
            $this->arrayToXml($dataDetil, $kmsXml);
            
            // Dump hasil XML
            // dd($xml);
    
            $datas = [
                'Username' => $this->user, 
                'Password' => $this->password,
                'fStream' => $xml->asXML()
            ];

            // dd($datas);
            
            \SoapWrapper::service('CoarriCodeco_Kemasan', function ($service) use ($datas) {    
                // dd($service);    
                $this->response = $service->call('CoarriCodeco_Kemasan', [$datas]);      
            });
            $response = $this->response;
    
            $hasil = strpos($response, "Proses Berhasil") !== false ? true : false;
            $flag = 'N';
            if ($hasil == true) {
                $flag = 'Y';
            }
            dd($xml, $this->response);
        }

    }
    
    public function detilHouseBl() 
    {
        // $manifestes = Manifest::whereNotNull('tglstripping')->take(1)->get();
        $manifest = Manifest::find(58);
        \SoapWrapper::override(function ($service) {
            $service
                ->name('DetailHouseBL')
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

//         \SoapWrapper::override(function ($service) {
//             $service
// //                ->name('CoCoKms_Tes')
//                 ->name('DetailHouseBL')
//                 ->wsdl($this->wsdl_cfs)
//                 ->trace(true)                                                                                                  
// //                ->certificate()                                                 
//                 ->cache(WSDL_CACHE_NONE)                                        
//                 ->options([
//                     'stream_context' => stream_context_create([
//                         'ssl' => array(
//                             'verify_peer' => false,
//                             'verify_peer_name' => false,
//                             'allow_self_signed' => true
//                         )
//                     ])
//                 ]);                                                    
//         });


        // foreach ($manifestes as $manifest) {
            $tglBLawb = $manifest->tgl_hbl ? Carbon::parse($manifest->tgl_hbl)->format('Y-m-d') : null;
            $tglMasterBL = $manifest->job->tgl_master_bl ? Carbon::parse($manifest->job->tgl_master_bl)->format('Y-m-d') : null;
            $tglBC11 = $manifest->job->ttgl_bc11 ? Carbon::parse($manifest->job->ttgl_bc11)->format('Y-m-d') : null;
            $tglPLP = $manifest->job->ttgl_plp ? Carbon::parse($manifest->job->ttgl_plp)->format('Y-m-d') : null;
            $tglStripping = $manifest->tglstripping ? Carbon::parse($manifest->tglstripping) : null;
            $jamStripping = $manifest->jamstripping ? Carbon::parse($manifest->jamstripping) : null;
            $strippingAt = $tglStripping;

            $type = 'NORMAL';
            if ($manifest->weight >= 2500) {
                $type = 'OH';
            }

            if ($manifest->dg_label == 'Y' ) {
                $type = 'BB';
            }

            $item = Item::where('manifest_id', $manifest->id)->with('tier')->whereNotNull('tier')->get();
            if ($item->isNotEmpty()) {
                $rak = $item->map(function ($i) {
                    if ($i->Tier && $i->Tier->Rack) {
                        return $i->Tier->Rack->name . '-' . $i->Tier->tier;
                    }
                    return null;
                })->filter()->implode(', ');
            }else {
                $rak = '-';
            }

            // dd($rak);
            $tglBehandle = $manifest->tglbehandle ? Carbon::parse($manifest->tglbehandle)->format('Y-m-d') : NULL;
            $jamBehandle = $manifest->jambehandle ? Carbon::parse($manifest->jambehandle)->format('Y-m-d') : NULL;
            $behandleAt = $tglBehandle.$jamBehandle;
            $tglSegelMerah = $manifest->tanggal_segel_merah ? Carbon::parse($manifest->tanggal_segel_merah)->foramt('Y-m-d H:i:s') : NULL;
            $dataDetil = [
                'no_bl_awb' => $manifest->nohbl,
                'tgl_bl_awb' => $tglBLawb,
                'no_master_bl_awb' => $manifest->job->nombl,
                'tgl_master_bl_awb' => $tglMasterBL,
                'weight' => $manifest->weight ?? 0,
                'measure' => $manifest->meas ?? 0,
                'KD_GUDANG' => '1MUT',
                'no_plp' => $manifest->job->noplp,
                'tgl_plp' => $tglPLP,
                'no_bc11' => $manifest->job->tno_bc11,
                'tgl_bc11' => $tglBC11,
                'tgl_stripping' => $strippingAt,
                'type_cargo' => $type,
                'rak' => $rak,
                'description' => $manifest->descofgoods ?? '-',
                'tgl_behandle' => $behandleAt,
                'fl_segel_merah' => $manifest->flag_segel_merah,
                'tgl_segel_merah' => $tglSegelMerah,
                'nama_consolidator' => 'PT INTI MANDIRI UTAMA TRANS',
                'npwp_consolidator' => '0022383483042000',
                'alamat_consolidator' => 'Jl. Bugis Raya No. 15 Kebon Bawang Tanjung Priok',
            ];

            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8" standalone="yes"?><DOCUMENT></DOCUMENT>');
            $xmldata = $xml->addchild('DETAIL_HBL');
            // Konversi array ke XML
            $this->arrayToXml($dataDetil, $xmldata);
    
            // $datas = [
            //     'Username' => 'AIRN', 
            //     'Password' => 'AIRN',
            //     'fStream' => 'TEST'
            // ];
            // dd($datas);
            $datas = [
                'Username' => $this->user, 
                'Password' => $this->password,
                'fStream' => $xml->asXML()
            ];

            // dd($xml);
            
            \SoapWrapper::service('DetailHouseBL', function ($service) use ($xml) {    
                // dd($service);    
                $this->response = $service->call('DetailHouseBL', [
                    'Username' => $this->user, 
                    'Password' => $this->password,
                    'fStream' => $xml->asXML()
                ]);      
            });
            $response = $this->response;

            $hasil = strpos($response, "Berhasil insert data") !== false ? true : false;
            $flag = 'N';
            if ($hasil == true) {
                $flag = 'Y';
            }
            dd($xml->asXML(), $this->response);
        // }
        
    }

    private function arrayToXml($data, &$xmlData)
    {
        foreach ($data as $key => $value) {
            $key = strtoupper($key); // Ubah nama elemen menjadi huruf kapital

            if (is_array($value)) {
                $subnode = $xmlData->addChild($key);
                $this->arrayToXml($value, $subnode);
            } else {
                $xmlData->addChild($key, htmlspecialchars($value));
            }
        }
    }

}
