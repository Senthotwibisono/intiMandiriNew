<?php

namespace App\Http\Controllers\CFS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Manifest;
use App\Models\Container as Cont;
use App\Models\Item;

use Auth;
use DataTables;

class CfsDefaultController extends Controller
{
    private string $wsdl;
    private string $user;
    private string $password;
    private string $kode;
    protected $response;

    public function __construct() {
        $this->middleware('auth');

        // $this->wsdlProd = 'https://ipccfscenter.com/TPSServices/server_plp_dev.php?wsdl';
        $this->wsdl = 'https://pelindo-cfscenter.com/TPSServices/server_plp.php?wsdl';
        $this->user = '1MUT';
        $this->password = '1MUT';
        $this->kode = '1MUT';
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

    public function indexContainer()
    {
        $data['title'] = 'Container LCL - CFS Center';
        // $data['containers'] = Cont::where('coari_cfs_flag', 'N')->get();

        return view('cfs.data.container', $data);
    }

    public function dataContainer()
    {
        $containers = Cont::with(['job'])->where('coari_cfs_flag', 'Y')->get();

        return DataTables::of($containers)
        ->addColumn('action', function($containers){
            return '<button class="btn btn-info resend" id="resend" data-id="'.$containers->id.'">Kirim Ulang</button>';
        })
        ->addColumn('kd_tps_asal', function($containers){
            return $containers->job->sandar->kd_tps_asal ?? '-';
        })
        ->addColumn('kapal', function($containers){
            return $containers->job->Kapal->name ?? '-';
        })
        // ->addColumn('noplp', function($containers){
        //     return $containers->job->noplp ?? '-';
        // })
        // ->addColumn('ttgl_plp', function($containers){
        //     return $containers->job->ttgl_plp ?? '-';
        // })
        // ->addColumn('tno_bc11', function($containers){
        //     return $containers->job->tno_bc11 ?? '-';
        // })
        // ->addColumn('ttgl_bc11', function($containers){
        //     return $containers->job->ttgl_bc11 ?? '-';
        // })
        // ->addColumn('eta', function($containers){
        //     return $containers->job->eta ?? '-';
        // })
        // ->addColumn('voy', function($containers){
        //     return $containers->job->voy ?? '-';
        // })
        ->rawColumns(['action', 'checkBox'])
        ->make(true);

    }

    public function resendContainer(Request $request)
    {
        if (!$request->has('ids')) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih Container dahulu!'
            ]);
        }

        $containers = Cont::whereIn('id', $request->ids)->get();
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

        try {
            foreach ($containers as $cont) {
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
                $tglPLP = $cont->job->ttgl_plp ? Carbon::parse($cont->job->ttgl_plp)->format('Ymd') : null;
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
                    'no_daftar_pabean' => '',
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
                $contXml = $detailXml->addChild('CONT');
    
                $this->arrayToXml($dataHeader, $headerXml);
                $this->arrayToXml($dataDetil, $contXml);
                
                // Dump hasil XML
                // dd($xml);
        
                $output = preg_replace('/\s+/', ' ', $xml->asXML());
                // dd($output);
                $datas = [
                    'Username' => $this->user, 
                    'Password' => $this->password,
                    'fStream' => $xml->asXML()
                ];
                $userName = $this->user;
                $password = $this->password;
    
                // dd($datas);
                
                \SoapWrapper::service('CoarriCodeco_Container', function ($service) use ($output, $userName, $password) {        
                    $this->response = $service->call('CoarriCodeco_Container', [
                        'fStream' => $output,
                        'Username' => $userName,
                        'Password' => $password,
                    ]);      
                });
                $response = $this->response;
        
                $hasil = strpos($response, "Proses Berhasil") !== false ? true : false;
                $flag = 'N';
                if ($hasil == true) {
                    $flag = 'Y';
                }
                $cont->update([
                    'coari_cfs_flag' => $flag,
                    'coari_cfs_response' => $response,
                    'coari_cfs_at' => Carbon::now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
            ]);
            
        } catch (\Throwable $th) {
           return response()->json([
            'success' => false,
            'message' => 'Opss Something Wrong : ' .$th->getMessage(),
           ]);
        }
    }

    public function indexManifest()
    {
        $data['title'] = 'Manifest LCL - CFS Center';

        return view('cfs.data.manifest', $data);
    }

    public function dataManifest(Request $request)
    {
        $manifest = Manifest::with(['job', 'cont'])->where('coari_cfs_flag' ,'Y')->orWhere('codeco_cfs_flag', 'Y')->orWhere('detil_hbl_cfs_flag', 'Y')->get();

        return DataTables::of($manifest)
        ->addColumn('coari', function($manifest){
            return '<button class="btn btn-success coariResend" id="coariResend" data-id="'.$manifest->id.'">Resend Coari</button>';
        })
        ->addColumn('codeco', function($manifest){
            return '<button class="btn btn-warning" id="codecoResend" data-id="'.$manifest->id.'">Resend Codeco</button>';
        })
        ->addColumn('detail', function($manifest){
            return '<button class="btn btn-danger" id="detailResend" data-id="'.$manifest->id.'">Resend Detail HBL</button>';
        })
        ->addColumn('kapal', function($manifest){
            return $manifest->job->Kapal->name ?? '-';
        })
        ->addColumn('kd_tps_asal', function($manifest){
            return $manifest->job->sandar->kd_tps_asal ?? '-';
        })
        ->rawColumns(['coari', 'codeco', 'detail'])
        ->make(true);
    }

    public function coariManifest(Request $request)
    {
        if (!$request->has('ids')) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih Manifest terlebih dahulu'
            ]);
        }

        $manifestes = Manifest::whereIn('id', $request->ids)->get();
        $notAllowed = $manifestes->whereNull('tglmasuk');
        if ($notAllowed->isNotEmpty()) {
            $noHbl = $notAllowed->pluck('nohbl')->implode(', ');
            // var_dump($noHbl);
            // die();

            return response()->json([
                'success' => false,
                'message' => 'Kamu tidak dapat mengirimkan pilihan karena ada No HBL yang belum gate-in : ' . $noHbl . ', Hubungi admin atau petugas gate',
            ]);
        }
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
                'nama_consolidator' => 'PT. LOGISTIK KARYA BERMITRA',
                'npwp_consolidator' => '0924302722427000',
                'alamat_consolidator' => 'JL. BINTARA 9 NO. 158 RT. 001 RW. 005 BINTARA BEKASI BARAT KOTA BEKASI JAWA BARAT 97134',
            ];



            // Tambahkan elemen utama
            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><DOCUMENT></DOCUMENT>');

            // Tambahkan elemen utama
            $xmldata = $xml->addAttribute('xmlns', 'cocokms.xsd');
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
            $userName = $this->user;
            $password = $this->password;

            // dd($datas);
   
            \SoapWrapper::service('CoarriCodeco_Kemasan', function ($service) use ($output, $userName, $password) {        
                $this->response = $service->call('CoarriCodeco_Kemasan', [
                    'fstream' => $output,
                    'Username' => $userName,
                    'Password' => $password,
                ]);      
            });
            $response = $this->response;
    
            // dd($response, $output);
            $hasil = strpos($response, "Proses Berhasil") !== false ? true : false;
            $flag = 'N';
            if ($hasil == true) {
                $flag = 'Y';
            }
            
            $manifest->update([
                'coari_cfs_flag' => $flag,
                'coari_cfs_response' => $response,
                'coari_cfs_at' => Carbon::now(),
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil di kirim',
        ]);
    }

    public function codecoManifest(Request $request)
    {
        if (!$request->has('ids')) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih Manifest terlebih dahulu'
            ]);
        }

        $manifestes = Manifest::whereIn('id', $request->ids)->get();
        $notAllowed = $manifestes->whereNull('tglrelease');
        if ($notAllowed->isNotEmpty()) {
            $noHbl = $notAllowed->pluck('nohbl')->implode(', ');
            return response()->json([
                'success' => false,
                'message' => 'Kamu tidak dapat mengirimkan pilihan karena ada No HBL yang belum gate-out : ' . $noHbl . ', Hubungi admin atau petugas gate',
            ]);
        }

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

            $tglBLawb = $manifest->tgl_hbl ? Carbon::parse($manifest->tgl_hbl)->format('Ymd') : null;
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
                'kd_dok_inout' => $manifest->kd_dok_inout,
                'no_dok_inout' => $manifest->no_dok,
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
                'nama_consolidator' => 'PT. LOGISTIK KARYA BERMITRA',
                'npwp_consolidator' => '0924302722427000',
                'alamat_consolidator' => 'JL. BINTARA 9 NO. 158 RT. 001 RW. 005 BINTARA BEKASI BARAT KOTA BEKASI JAWA BARAT 97134',
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

            $output = preg_replace('/\s+/', ' ', $xml->asXML());
    
            $datas = [
                'Username' => $this->user, 
                'Password' => $this->password,
                'fStream' => $xml->asXML()
            ];

            $userName = $this->user;
            $password = $this->password;

            // dd($datas);
            
            \SoapWrapper::service('CoarriCodeco_Kemasan', function ($service) use ($output, $userName, $password) {    
                // dd($service);    
                $this->response = $service->call('CoarriCodeco_Kemasan', [
                    'fstream' => $output,
                    'Username' => $userName,
                    'Password' => $password,
                ]);      
            });
            $response = $this->response;
    
            $hasil = strpos($response, "Proses Berhasil") !== false ? true : false;
            $flag = 'N';
            if ($hasil == true) {
                $flag = 'Y';
            }
            // dd($this->response, $output, $xml);
            $manifest->update([
                'codeco_cfs_flag' => $flag,
                'codeco_cfs_response' => $response,
                'codeco_cfs_at' => Carbon::now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil di kirim'
        ]);
    }

    public function detilManifest(Request $request)
    {
        if (!$request->has('ids')) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih Manifest terlebih dahulu'
            ]);
        }

        $manifestes = Manifest::whereIn('id', $request->ids)->get();
        $notAllowed = $manifestes->whereNull('tglstripping');
        if ($notAllowed->isNotEmpty()) {
            $noHbl = $notAllowed->pluck('nohbl')->implode(', ');
            return response()->json([
                'success' => false,
                'message' => 'Kamu tidak dapat mengirimkan pilihan karena ada No HBL yang belum Stripping : ' . $noHbl . ', Hubungi admin atau petugas gudang',
            ]);
        }

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


        foreach ($manifestes as $manifest) {
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
            $jamBehandle = $manifest->jambehandle ? Carbon::parse($manifest->jambehandle)->format('H:i:s') : NULL;
            $behandleAt = $tglBehandle.' '.$jamBehandle;
            $tglSegelMerah = $manifest->tanggal_segel_merah ? Carbon::parse($manifest->tanggal_segel_merah)->format('Y-m-d H:i:s') : NULL;
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
                'nama_consolidator' => 'PT. LOGISTIK KARYA BERMITRA',
                'npwp_consolidator' => '0924302722427000',
                'alamat_consolidator' => 'JL. BINTARA 9 NO. 158 RT. 001 RW. 005 BINTARA BEKASI BARAT KOTA BEKASI JAWA BARAT 97134',
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
            // return $xml;
            // var_dump($xml);
            // die();
            
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
            
            $manifest->update([
                'detil_hbl_cfs_flag' => $flag,
                'detil_hbl_cfs_response' => $response,
                'detil_hbl_cfs_at' => Carbon::now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dikirim',
        ]);
    }
}
