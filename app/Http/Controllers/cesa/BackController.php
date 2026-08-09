<?php

namespace App\Http\Controllers\cesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use Carbon\Carbon;

use App\Models\cesa\Token;

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

class BackController extends Controller
{
    private $username = 'tpsimut';
    private $password = 'Jakarta#20211';
    private $apiKey   = '2d404382-449c-46bc-9084-d256a8cb3ab1'; // jika diperlukan
    private $baseUrl   = 'https://apis-gw.beacukai.go.id/v1/openapi-tpsonline'; // jika diperlukan

    public function login()
    {
        $response = Http::withHeaders([
            // 'beacukai-api-key' => $this->apiKey,
            'Accept'    => 'application/json',
        ])->post('https://apis-gw.beacukai.go.id/v1/openapi-auth/user/login', [
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'status' => false,
                'message' => 'Login gagal',
                'response' => $response->body()
            ], 500);
        }

        $data = $response->json();

        Token::create([
            'access_token'  => $data['item']['access_token'],
            'refresh_token' => $data['item']['refresh_token'],
            'expired_at'    => now()->addSeconds($data['item']['expires_in'] - 60),
        ]);

        return $data['item']['access_token'];
    }

    private function getToken()
    {
        $token = Token::latest('id')->first();

        if (!$token || now()->gte($token->expired_at)) {
            return $this->login();
        }

        return $token->access_token;
    }

    public function plpOndemand(Request $request)
    {
        $response = $this->request(
            'get',
            $this->baseUrl . '/get-respon-plp-on-demand',
            [
                'nomorPlp' => $request->nomorPlp,
                'tanggalPlp' => $request->tanggalPlp,
                'nomorReference' => $request->nomorReference,
                'kodeGudang' => $request->kodeGudang,
            ]
        )->json();

        if ($response['code'] != 200) {
            return response()->json([
                'success' => false,
                'message' => $response['detail'] ?? 'Terjadi kesalahan'
            ]);
        }

        if (empty($response['data'])) {
            return response()->json([
                'success' => false,
                'message' => $response['detail'] ?? 'Data tidak ditemukan',
                'data' => []
            ]);
        }

        try {

            DB::transaction(function () use ($response) {

                $consolidator = Consolidator::first();

                foreach ($response['data'] as $data) {

                    $header = $data['header'];

                    $oldPLP = PLP::where('no_plp', $header['nomorPlp'])
                        ->where('tgl_plp', $header['tanggalPlp'])
                        ->first();

                    if ($oldPLP) {
                        continue;
                    }

                    $plp = PLP::create([
                        'tgl_upload'        => now()->format('Ymd'),
                        'upload_date'       => today(),
                        'upload_time'       => now()->format('H:i:s'),

                        'kd_kantor'         => $header['kodeKantor'],
                        'kd_tps'            => $this->kode,
                        'kd_tps_asal'       => $header['kodeTpsAsal'],
                        'kd_tps_tujuan'     => $header['kodeTpsTujuan'],

                        'gudang_asal'       => $header['kodeGudangAsal'],
                        'gudang_tujuan'     => $header['kodeGudangTujuan'],

                        'no_plp'            => $header['nomorPlp'],
                        'tgl_plp'           => $header['tanggalPlp'],

                        'call_sign'         => $header['callSign'],
                        'nm_angkut'         => $header['namaAngkut'],
                        'no_voy_flight'     => $header['nomorVoyFlight'],
                        'tgl_tiba'          => $header['tanggalTiba'],

                        'no_surat'          => $header['nomorSurat'],
                        'tgl_surat'         => $header['tanggalSurat'],

                        'no_bc11'           => $header['nomorBc11'],
                        'tgl_bc11'          => $header['tanggalBc11'],

                        'ref_number'        => $header['refNumberPlp'],

                        'uid'               => Auth::id(),
                        'consolidator_id'   => $consolidator->id,
                        'namaconsolidator'  => $consolidator->namaconsolidator,
                    ]);

                    // simpan kontainer
                    foreach ($data['detil']['kontainer'] ?? [] as $container) {

                        PLPdetail::create([
                            'plp_id'       => $plp->id,
                            'tgl_upload'   => $plp->tgl_upload,
                            'no_plp'       => $plp->no_plp,
                            'tgl_plp'      => $plp->tgl_plp,

                            'no_cont'      => $container['nomorKontainer'],
                            'uk_cont'      => $container['ukuranKontainer'],

                            // response tidak memiliki jenisKontainer
                            'jns_cont'     => $container['jenisMuat'] ?? null,

                            'no_bc11'      => $plp->no_bc11,
                            'tgl_bc11'     => $plp->tgl_bc11,

                            'no_pos_bc11'  => $container['nomorPosBc11'],

                            // response tidak memiliki field ini
                            'consignee'    => null,
                            'no_bl_awb'    => null,
                            'tgl_bl_awb'   => null,

                            'flag_spk'     => $plp->flag_spk,
                        ]);
                    }

                    // update data kemasan
                    foreach ($data['detil']['kemasan'] ?? [] as $kemasan) {

                        PLPdetail::where('plp_id', $plp->id)
                            ->where('no_pos_bc11', $kemasan['nomorPosBc11'])
                            ->update([
                                'consignee'  => $kemasan['consignee'],
                                'no_bl_awb'  => $kemasan['nomorBlAwb'],
                                'tgl_bl_awb' => $kemasan['tanggalBlAwb'],
                                'jns_kms'    => $kemasan['jenisKemasan'],
                                'jml_kms'    => $kemasan['jumlahKemasan'],
                            ]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan'
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
    public function plpGet()
    {
        $response = $this->request(
            'get',
            $this->baseUrl . '/get-respon-plp-tujuan',
            [
                'kodeTps' => '1MUT',
            ]
        )->json();

        // dd($response);
        if ($response['code'] === 200) {
            if (empty($response['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => $response['detail'],
                    'data' => [],
                ]);
            }
            try {
                $result = db::transaction(function() use($response){
    
                    $data = $response['data']['responPlp'] ?? [];
                    foreach ($data as $item) {
                        $header = $item['header'];
                        $oldPLP = PLP::where('no_plp', $header['nomorPlp'])
                            ->where('tgl_plp', $header['tanggalPlp'])
                            ->first();
        
                        if ($oldPLP) {
                            continue;
                        }
        
                        $consolidator = Consolidator::first();
        
                        $plp = PLP::create([
                            'tgl_upload' => now()->format('Ymd'),
                            'upload_date' => today(),
                            'upload_time' => now()->format('H:i:s'),
                            'kd_kantor' => $header['kodeKantor'],
                            'kd_tps' => $this->kode,
                            'kd_tps_asal' => $header['kodeTpsAsal'],
                            'kd_tps_tujuan' => $header['kodeTpsTujuan'],
                            'gudang_asal' => $header['gudangAsal'],
                            'gudang_tujuan' => $header['gudangTujuan'],
                            'no_plp' => $header['nomorPlp'],
                            'tgl_plp' => $header['tanggalPlp'],
                            'call_sign' => $header['callSign'],
                            'nm_angkut' => $header['namaAngkut'],
                            'no_voy_flight' => $header['nomorVoyFlight'],
                            'tgl_tiba' => $header['tanggalTiba'],
                            'no_surat' => $header['nomorSurat'],
                            'tgl_surat' => $header['tanggalSurat'],
                            'no_bc11' => $header['nomorBc11'],
                            'tgl_bc11' => $header['tanggalBc11'],
                            'ref_number' => $header['refNumber'],
                            'uid' => Auth::id(),
                            'consolidator_id' => $consolidator->id,
                            'namaconsolidator' => $consolidator->namaconsolidator,
                        ]);
        
                        // ========================
                        // Simpan Kontainer
                        // ========================
        
                        foreach ($item['kontainer'] as $container) {
                            PLPdetail::create([
                                'plp_id' => $plp->id,
                                'tgl_upload' => $plp->tgl_upload,
                                'no_plp' => $plp->no_plp,
                                'tgl_plp' => $plp->tgl_plp,
                                'no_cont' => $container['nomorKontainer'],
                                'uk_cont' => $container['ukuranKontainer'],
                                'jns_cont' => $container['jenisKontainer'],
                                'no_bc11' => $plp->no_bc11,
                                'tgl_bc11' => $plp->tgl_bc11,
                                'no_pos_bc11' => $container['nomorPosBc11'],
                                'consignee' => $container['consignee'],
                                'no_bl_awb' => $container['nomorBlAwb'],
                                'tgl_bl_awb' => $container['tanggalBlAwb'],
                                'flag_spk' => $plp->flag_spk,
                            ]);
                        }
        
                        // ========================
                        // Update data kemasan
                        // ========================
        
                        foreach ($item['kemasan'] as $kemasan) {
        
                            PLPdetail::where('plp_id', $plp->id)
                                ->where('no_bl_awb', $kemasan['nomorBlAwb'])
                                ->where('no_pos_bc11', $kemasan['nomorPosBc11'])
                                ->update([
                                    'jns_kms' => $kemasan['jenisKemasan'],
                                    'jml_kms' => $kemasan['jumlahKemasan'],
                                ]);
                        }
                    }
                });
                return response()->json([
                    'success' => true,
                    'message'=> 'Data berhasil disimpan'
                ]);

            } catch (\Throwable $th) {
                return response()->json([
                    'success' => false,
                    'message' => $th->getMessage()
                ]);
            }

        }else {
            return response()->json([
                'success' => false,
                'message' => $response['detail'] ?? 'Terjadi kesalahan'
            ]);
        }
    }

    public function sppbGet()
    {
        $response = $this->request(
            'get',
            $this->baseUrl . '/get-impor-permit',
            [
                'kodeGudang' => 'INTI',
            ]
        )->json();

        // dd($response);
        if ($response['code'] === 200) {
            if (empty($response['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => $response['detail'],
                    'data' => [],
                ]);
            }
            try {
                $result = db::transaction(function() use($response){
    
                    $data = $response['data']['sppb'] ?? [];
                    foreach ($data as $group) {
                        $header = $group['header'];

                        $oldSPPB = SPPB::where('car', $header['car'])->first();

                        if ($oldSPPB) {
                            continue;
                        }

                        $sppb = SPPB::create([
                            'car'                  => $header['car'] ?? null,
                            'no_sppb'              => $header['noSppb'] ?? null,
                            'tgl_sppb'             => $header['tglSppb'] ?? null,
                            'kd_kantor_pengawas'   => $header['kodeKantorPengawas'] ?? null,
                            // response baru tidak memiliki ini
                            'kd_kantor_bongkar'    => $header['kodeKpbc'] ?? null,
                            'no_pib'               => $header['noPib'] ?? null,
                            'tgl_pib'              => $header['tglPib'] ?? null,
                            'nama_imp'             => $header['namaImp'] ?? null,
                            'npwp_imp'             => $header['npwpImp'] ?? null,
                            'alamat_imp'           => $header['alamatImp'] ?? null,
                            'npwp_ppjk'            => $header['npwpPpjk'] ?? null,
                            'nama_ppjk'            => $header['namaPpjk'] ?? null,
                            'alamat_ppjk'          => $header['alamatPpjk'] ?? null,
                            'nm_angkut'            => $header['namaAngkut'] ?? null,
                            'no_voy_flight'        => $header['noVoyFlight'] ?? null,
                            'bruto'                => $header['bruto'] ?? null,
                            'netto'                => $header['netto'] ?? null,
                            'gudang'               => $header['gudang'] ?? null,
                            'status_jalur'         => $header['statusJalur'] ?? null,
                            'jml_cont'             => $header['jumlahKontainer'] ?? null,
                            'no_bc11'              => $header['noBc11'] ?? null,
                            'tgl_bc11'             => $header['tglBc11'] ?? null,
                            'no_pos_bc11'          => $header['noPosBc11'] ?? null,
                            'no_bl_awb'            => $header['noBlAwb'] ?? null,
                            'tgl_bl_awb'           => $header['tglBlAwb'] ?? null,
                            'no_master_bl_awb'     => $header['noMasterBlAwb'] ?? null,
                            'tgl_master_bl_awb'    => $header['tglMasterBlAwb'] ?? null,
                            'tgl_upload'           => Carbon::today()->format('Y-m-d'),
                            'jam_upload'           => Carbon::now()->format('H:i:s'),
                        ]);

                        foreach ($group['kontainer'] ?? [] as $detailCont) {
                            SPPBCont::create([
                                'sppb_id'  => $sppb->id,
                                'car'      => $detailCont['car'],
                                'no_cont'  => $detailCont['nomorKontainer'],
                                'size'     => $detailCont['ukuranKontainer'],
                                'jns_muat' => $detailCont['jenisMuat'],
                            ]);

                            if ($sppb->jml_cont > 0) {
                                $contF = ContF::whereNull('tglkeluar')->where('nocontainer', $detailCont['nomorKontainer'])->where('size', $detailCont['ukuranKontainer'])->first();
                                if ($contF) {
                                    if ($contF->size == $detailCont['ukuranKontainer']) {
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
                                    $flagTglBl = !empty(trim($sppb->tgl_bl_awb ?? '')) ? $sppb->tgl_bl_awb : (!empty(trim($sppb->tgl_master_bl_awb ?? '')) ? $sppb->tgl_master_bl_awb : null);
                                    $flagNoBl = !empty(trim($sppb->no_bl_awb ?? '')) ? $sppb->no_bl_awb : (!empty(trim($sppb->no_master_bl_awb ?? '')) ? $sppb->no_master_bl_awb : null);
                                    $contF->update([
                                         'kd_dok_inout' => 1,
                                         'no_dok' => $sppb->no_sppb,
                                         'tgl_dok' => date('Y-m-d', strtotime($sppb->tgl_sppb)),
                                         'status_bc' => $statusBC,
                                         'alasan_hold' => $alasanSize,
                                         'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
                                         'nobl' => $flagNoBl,
                                     ]);
                                }
                            }
                        }

                        foreach ($group['kemasan'] ?? [] as $detailKMS) {

                            SPPBKms::create([
                                'sppb_id' => $sppb->id,
                                'car'     => $detailKMS['car'],
                                'jns_kms' => $detailKMS['jenisKemasan'],
                                'merk_kms'=> $detailKMS['kodeJenisKemasan'],
                                'jml_kms' => $detailKMS['jumlahKemasan'],
                            ]);

                            if ($sppb->jml_cont == 0) {
                                $manifest = Manifest::where('nohbl', $sppb->no_bl_awb)->where('tglrelease', null)->first();
                                if ($manifest) {
                                    $alasanCust = null;
                                    $statusBC = "release";
                                    $cust = Customer::where('name', $sppb->nama_imp)->first();
                                    if ($cust) {
                                        $cust->update([
                                            'name' => $sppb->nama_imp,
                                            'npwp' => $sppb->npwp_imp,
                                            'alamat' => $sppb->alamat_imp,
                                        ]);
                                    }
                                    $newCont = null;
                                    if (!$cust && $sppb->nama_imp != null) {
                                        $newCust = Customer::create([
                                            'name' => $sppb->nama_imp,
                                            'npwp' => $sppb->npwp_imp,
                                            'alamat' => $sppb->alamat_imp,
                                        ]);
                                    }
    
                                    $alsasanQTY = null;
                                    if ($manifest->quantity != $manifest->final_qty) {
                                        $alsasanQTY = 'Jumlah QTY Real Berbeda';
                                        $statusBC = 'HOLD';
                                    }

                                    $alasanFinal =  $alasanCust . ', ' . $alsasanQTY;
    
                                    $manifest->update([
                                        'kd_dok_inout' => 1,
                                        'no_dok' => $sppb->no_sppb,
                                        'tgl_dok' => date('Y-m-d', strtotime($sppb->tgl_sppb)),
                                        'status_bc' => $statusBC,
                                        'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
                                        'alasan_hold' => $alasanFinal,
                                    ]);
                                }
                            }
                        }
                    }
                });
                return response()->json([
                    'success' => true,
                    'message'=> 'Data berhasil disimpan'
                ]);

            } catch (\Throwable $th) {
                return response()->json([
                    'success' => false,
                    'message' => $th->getMessage()
                ]);
            }

        }else {
            return response()->json([
                'success' => false,
                'message' => $response['detail'] ?? 'Terjadi kesalahan'
            ]);
        }
    }

    public function sppbOnDemand()
    {
        $response = $this->request(
            'get',
            $this->baseUrl . '/get-impor-sppb',
            [
                'kodeGudang' => 'INTI',
                'nomorDokumen' => '150404/KPU.1/2026',
                'tanggalDokumen' => "05-03-2026",
                'npwpImp' => '0013859574091000000000'
            ]
        )->json();

        // dd($response);
        if ($response['code'] === 200) {
            if (empty($response['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => $response['detail'],
                    'data' => [],
                ]);
            }
            try {
                $result = db::transaction(function() use($response){
    
                    $data = $response['data'][0] ?? null;
                    $header = $data['header'][0] ?? null;
                    $sppb = SPPB::create([
                        'car'                  => $header['car'],
                        'no_sppb'              => $header['nomorDokumen'],
                        'tgl_sppb'             => $header['tanggalDokumen'],
                        'kd_kantor_pengawas'   => $header['kodeKantor'],
                        'no_pib'               => $header['nomorPib'],
                        'tgl_pib'              => $header['tanggalPib'],
                        'nama_imp'             => $header['namaImp'],
                        'npwp_imp'             => $header['npwpImp'],
                        'alamat_imp'           => $header['alamatImp'],
                        'npwp_ppjk'            => $header['npwpPpjk'],
                        'nama_ppjk'            => $header['namaPpjk'],
                        'alamat_ppjk'          => $header['alamatPpjk'],
                        'nm_angkut'            => $header['namaAngkut'],
                        'no_voy_flight'        => $header['nomorVoyFlight'],
                        'bruto'                => $header['bruto'],
                        'netto'                => $header['netto'],
                        'gudang'               => $header['gudang'],
                        'status_jalur'         => $header['statusJalur'],
                        'jml_cont'             => $header['jumlahKontainer'],
                        'no_bc11'              => $header['nomorBc11'],
                        'tgl_bc11'             => $header['tanggalBc11'],
                        'no_pos_bc11'          => $header['nomorPosBc11'],
                        'no_bl_awb'            => $header['nomorBlAwb'],
                        'tgl_bl_awb'           => $header['tanggalBlAwb'],
                        'no_master_bl_awb'     => $header['nomorMasterBlAwb'],
                        'tgl_master_bl_awb'    => $header['tanggalMasterBlAwb'],
                        'tgl_upload'           => Carbon::today()->format('Y-m-d'),
                        'jam_upload'           => Carbon::now()->format('H:i:s'),
                    ]);

                    foreach ($data['kontainer'] ?? [] as $detailCont) {
                        SPPBCont::create([
                            'sppb_id'  => $sppb->id,
                            'car'      => $detailCont['car'],
                            'no_cont'  => $detailCont['nomorKontainer'],
                            'size'     => $detailCont['ukuranKontainer'],
                            'jns_muat' => $detailCont['jenisMuat'],
                        ]);

                        if ($sppb->jml_cont > 0) {
                            $contF = ContF::whereNull('tglkeluar')->where('nocontainer', $detailCont['nomorKontainer'])->where('size', $detailCont['ukuranKontainer'])->first();
                            if ($contF) {
                                if ($contF->size == $detailCont['ukuranKontainer']) {
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
                                $flagTglBl = !empty(trim($sppb->tgl_bl_awb ?? '')) ? $sppb->tgl_bl_awb : (!empty(trim($sppb->tgl_master_bl_awb ?? '')) ? $sppb->tgl_master_bl_awb : null);
                                $flagNoBl = !empty(trim($sppb->no_bl_awb ?? '')) ? $sppb->no_bl_awb : (!empty(trim($sppb->no_master_bl_awb ?? '')) ? $sppb->no_master_bl_awb : null);
                                $contF->update([
                                     'kd_dok_inout' => 1,
                                     'no_dok' => $sppb->no_sppb,
                                     'tgl_dok' => date('Y-m-d', strtotime($sppb->tgl_sppb)),
                                     'status_bc' => $statusBC,
                                     'alasan_hold' => $alasanSize,
                                     'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
                                     'nobl' => $flagNoBl,
                                 ]);
                            }
                        }
                    }

                    foreach ($data['kemasan'] ?? [] as $detailKMS) {

                        SPPBKms::create([
                            'sppb_id' => $sppb->id,
                            'car'     => $detailKMS['car'],
                            'jns_kms' => $detailKMS['jenisKemasan'],
                            'merk_kms'=> $detailKMS['merkKemasan'],
                            'jml_kms' => $detailKMS['jumlahKemasan'],
                        ]);

                        if ($sppb->jml_cont == 0) {
                            $manifest = Manifest::where('nohbl', $sppb->no_bl_awb)->where('tglrelease', null)->first();
                            if ($manifest) {
                                $alasanCust = null;
                                $statusBC = "release";
                                $cust = Customer::where('name', $sppb->nama_imp)->first();
                                if ($cust) {
                                    $cust->update([
                                        'name' => $sppb->nama_imp,
                                        'npwp' => $sppb->npwp_imp,
                                        'alamat' => $sppb->alamat_imp,
                                    ]);
                                }
                                $newCont = null;
                                if (!$cust && $sppb->nama_imp != null) {
                                    $newCust = Customer::create([
                                        'name' => $sppb->nama_imp,
                                        'npwp' => $sppb->npwp_imp,
                                        'alamat' => $sppb->alamat_imp,
                                    ]);
                                }

                                $alsasanQTY = null;
                                // if ($manifest->quantity != $manifest->final_qty) {
                                //     $alsasanQTY = 'Jumlah QTY Real Berbeda';
                                //     $statusBC = 'HOLD';
                                // }

                                $alasanFinal =  $alasanCust . ', ' . $alsasanQTY;

                                $manifest->update([
                                    'kd_dok_inout' => 1,
                                    'no_dok' => $sppb->no_sppb,
                                    'tgl_dok' => date('Y-m-d', strtotime($sppb->tgl_sppb)),
                                    'status_bc' => $statusBC,
                                    'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
                                    'alasan_hold' => $alasanFinal,
                                ]);
                            }
                        }
                    }
                });
                return response()->json([
                    'success' => true,
                    'message'=> 'Data berhasil disimpan'
                ]);

            } catch (\Throwable $th) {
                return response()->json([
                    'success' => false,
                    'message' => $th->getMessage()
                ]);
            }

        }else {
            return response()->json([
                'success' => false,
                'message' => $response['detail'] ?? 'Terjadi kesalahan'
            ]);
        }
    }

    // Tracking
    public function apiTrackingIn()
    {
        $containersFCL = ContF::with(['job'])->whereNotNull('tglmasuk')->where('tracking_in_flag', 'N')->take(50)->get();
        foreach ($containersFCL as $container) {
            $container->update([
                'tracking_in_flag' => 'P',
                'tracking_in_time' => Carbon::now(),
                'tracking_in_status' => 'PROCESSING',
            ]);
            $data = [
                "kodeTps"=> "1MUT",
                "nomorKontainer"=> $container->nocontainer,
                "ukuranKontainer"=> $container->size,
                "jenisKontainer"=> "8",
                "nomorBlAwb"=> $container->nobl,
                "tanggalBlAwb"=> Carbon::parse($container->tgl_bl_awb)->format('d-m-Y'),
                "kodeDokumen"=> "3",
                "nomorDokumen"=> $container->job->noplp,
                "tanggalDokumen"=> Carbon::parse($container->job->ttgl_plp)->format('d-m-Y'),
                "kodeKegiatan"=> 5,
                "waktuKegiatan" =>  Carbon::parse($container->tglmasuk . ' ' . $container->jammasuk)->format('d-m-Y H:i:s'),
                "kodeGudang"=> "INTI",
                "block"=> NULL,
                "slot"=> NULL,
                "tier"=> NULL,
                "nomorPolisi"=> $container->nopol,
                "stid"=> null
            ];
    
            // dd($data);
            
            $response = $this->request(
                'post',
                $this->baseUrl . '/kirim-tps-tracking',
                $data
            )->json();

            if (isset($response['code']) && in_array($response['code'], [200, 201])) {
                $status = 'Y';
            } else {
                $status = 'C';
            }

            $container->update([
                'tracking_in_flag' => $status,
                'tracking_in_time' => Carbon::now(),
                'tracking_in_status' => $response['detail'],
            ]);
        }

        $containersLCL = Cont::with(['job'])->whereNotNull('tglmasuk')->where('tracking_in_flag', 'N')->take(50)->get();
        foreach ($containersLCL as $container) {
            $container->update([
                'tracking_in_flag' => 'P',
                'tracking_in_time' => Carbon::now(),
                'tracking_in_status' => 'PROCESSING',
            ]);
            $data = [
                "kodeTps"=> "1MUT",
                "nomorKontainer"=> $container->nocontainer,
                "ukuranKontainer"=> $container->size,
                "jenisKontainer"=> "7",
                "nomorBlAwb"=> $container->nobl,
                "tanggalBlAwb"=> Carbon::parse($container->tgl_bl_awb)->format('d-m-Y'),
                "kodeDokumen"=> "3",
                "nomorDokumen"=> $container->job->noplp,
                "tanggalDokumen"=> Carbon::parse($container->job->ttgl_plp)->format('d-m-Y'),
                "kodeKegiatan"=> 5,
                "waktuKegiatan" =>  Carbon::parse($container->tglmasuk . ' ' . $container->jammasuk)->format('d-m-Y H:i:s'),
                "kodeGudang"=> "INTI",
                "block"=> NULL,
                "slot"=> NULL,
                "tier"=> NULL,
                "nomorPolisi"=> $container->nopol,
                "stid"=> null
            ];
    
            // dd($data);
            
            $response = $this->request(
                'post',
                $this->baseUrl . '/kirim-tps-tracking',
                $data
            )->json();

            if (isset($response['code']) && in_array($response['code'], [200, 201])) {
                $status = 'Y';
            } else {
                $status = 'C';
            }

            $container->update([
                'tracking_in_flag' => $status,
                'tracking_in_time' => Carbon::now(),
                'tracking_in_status' => $response['detail'],
            ]);
        }

        return;
    }

    public function apiTrackingOut()
    {
        $containersFCL = ContF::with(['job'])->whereNotNull('tglkeluar')->where('tracking_out_flag', 'N')->take(50)->get();
        foreach ($containersFCL as $container) {
            $container->update([
                'tracking_out_flag' => 'P',
                'tracking_out_time' => Carbon::now(),
                'tracking_out_status' => 'PROCESSING',
            ]);
            $data = [
                "kodeTps"=> "1MUT",
                "nomorKontainer"=> $container->nocontainer,
                "ukuranKontainer"=> $container->size,
                "jenisKontainer"=> "8",
                "nomorBlAwb"=> $container->nobl,
                "tanggalBlAwb"=> Carbon::parse($container->tgl_bl_awb)->format('d-m-Y'),
                "kodeDokumen"=> $container->kd_dok_inout,
                "nomorDokumen"=> $container->no_dok,
                "tanggalDokumen"=> Carbon::parse($container->tgl_dok)->format('d-m-Y'),
                "kodeKegiatan"=> 6,
                "waktuKegiatan" =>  Carbon::parse($container->tglkeluar . ' ' . $container->jamkeluar)->format('d-m-Y H:i:s'),
                "kodeGudang"=> "INTI",
                "block"=> NULL,
                "slot"=> NULL,
                "tier"=> NULL,
                "nomorPolisi"=> $container->nopol_mty,
                "stid"=> null
            ];
    
            // dd($data);
            
            $response = $this->request(
                'post',
                $this->baseUrl . '/kirim-tps-tracking',
                $data
            )->json();

            if (isset($response['code']) && in_array($response['code'], [200, 201])) {
                $status = 'Y';
            } else {
                $status = 'C';
            }

            $container->update([
                'tracking_out_flag' => $status,
                'tracking_out_time' => Carbon::now(),
                'tracking_out_status' => $response['detail'],
            ]);
        }

        $containersLCL = Cont::with(['job'])->whereNotNull('tglkeluar')->where('tracking_out_flag', 'N')->take(50)->get();
        foreach ($containersLCL as $container) {
            $container->update([
                'tracking_out_flag' => 'P',
                'tracking_out_time' => Carbon::now(),
                'tracking_out_status' => 'PROCESSING',
            ]);
            $data = [
                "kodeTps"=> "1MUT",
                "nomorKontainer"=> $container->nocontainer,
                "ukuranKontainer"=> $container->size,
                "jenisKontainer"=> "7",
                "nomorBlAwb"=> $container->nobl,
                "tanggalBlAwb"=> Carbon::parse($container->tgl_bl_awb)->format('d-m-Y'),
                "kodeDokumen"=> "3",
                "nomorDokumen"=> $container->job->noplp,
                "tanggalDokumen"=> Carbon::parse($container->job->ttgl_plp)->format('d-m-Y'),
                "kodeKegiatan"=> 6,
                "waktuKegiatan" =>  Carbon::parse($container->tglkeluar . ' ' . $container->jamkeluar)->format('d-m-Y H:i:s'),
                "kodeGudang"=> "INTI",
                "block"=> NULL,
                "slot"=> NULL,
                "tier"=> NULL,
                "nomorPolisi"=> $container->nopol_mty,
                "stid"=> null
            ];
    
            // dd($data);
            
            $response = $this->request(
                'post',
                $this->baseUrl . '/kirim-tps-tracking',
                $data
            )->json();

            if (isset($response['code']) && in_array($response['code'], [200, 201])) {
                $status = 'Y';
            } else {
                $status = 'C';
            }

            $container->update([
                'tracking_out_flag' => $status,
                'tracking_out_time' => Carbon::now(),
                'tracking_out_status' => $response['detail'],
            ]);
        }

        return;
    }

    public function coariCont()
    {
        $startDate = '2026-07-01';
        $containersFCL = ContF::with(['job'])->whereNotNull('tglmasuk')->whereDate('tglmasuk', '>=', $startDate)->where('coarri_cesa_flag', 'N')->take(20)->get();
        foreach ($containersFCL as $container) {
            $container->update([
                'coarri_cesa_flag' => 'P',
                'coarri_cesa_time' => Carbon::now(),
                'coarri_cesa_status' => 'PROCESSING',
            ]);
            $header = [
                "kodeDokumen"         => "5",
                "noBc11"              => $container->job->tno_bc11 ?? null,
                "tanggalBc11"        => !empty($container->job->ttgl_bc11)
                    ? Carbon::parse($container->job->ttgl_bc11)->format('d-m-Y')
                    : null,
                "nomorVoyFlight"      => $container->job->dokplp->no_voy_flight ?? null,
                "tanggalBerangkat"    => !empty($container->job->tgl_berangkat)
                    ? Carbon::parse($container->job->tgl_berangkat)->format('d-m-Y')
                    : null,
                "namaAngkut"          => $container->job->dokplp->nm_angkut ?? null,
                "refNumber"           => Str::random(20),
                "kodeSaranaPengangkut"=> $container->job->kode_sarana_pengangkut ?? null,
                "kodeTps"             => "1MUT",
                "tanggalTiba"         => !empty($container->job->dokplp->tgl_tiba)
                    ? Carbon::parse($container->job->dokplp->tgl_tiba)->format('d-m-Y')
                    : null,
                "kodeGudang"          => "INTI",
                "callSign"            => $container->job->dokplp->call_sign ?? null,
            ];

             $waktuInOut = null;

            if (!empty($container->tglmasuk)) {
                $tanggalMasuk = $container->tglmasuk;

                $jamMasuk = !empty($container->jammasuk)
                    ? $container->jammasuk
                    : '00:00:00';

                $waktuInOut = Carbon::parse(
                    $tanggalMasuk . ' ' . $jamMasuk
                )->format('d-m-Y H:i:s');
            }

            $kontainer = [];
            $plpDetail = PLPdetail::where('plp_id', $container->job->plp_id)
                ->where('no_cont', $container->nocontainer)
                ->first();

            $nomorPosBc11 = $plpDetail?->no_pos_bc11;

            $kontainer[] = [
                "tanggalSegelBc" => !empty($container->tgl_segel_bc)
                    ? Carbon::parse($container->tgl_segel_bc)->format('d-m-Y')
                    : Carbon::parse($container->tglmasuk)->format('d-m-Y'),
                "tanggalDokumenInOut" => !empty($container->job->ttgl_plp)
                    ? Carbon::parse($container->job->ttgl_plp)->format('d-m-Y')
                    : null,
                "tanggalBlAwb" => !empty($container->tgl_bl_awb)
                    ? Carbon::parse($container->tgl_bl_awb)->format('d-m-Y')
                    : null,
                "flagKontainerKosong" => false,
                "waktuInOut" => $waktuInOut,
                "gudangTujuan" => "INTI",
                "kodeDokumenInOut" => "3",
                "ukuranKontainer" => $container->size,
                "flagKontainer" => true,
                "kodeTimbun" => null,
                "noMasterBlAwb" => $container->job->nombl ?? null,
                "pelabuhanBongkar" => $container->job->bongkar->kode ?? null,
                "nomorDokumenInOut" => $container->job->noplp ?? null,
                "nomorPolisi" => $container->nopol ?? '-',
                "nomorIjinTps" => $container->nomor_ijin_tps ?? null,
                "nomorPosBc11" => $nomorPosBc11 ?? null,
                "tanggalMasterBlAwb" => !empty($container->job->tgl_master_bl)
                    ? Carbon::parse($container->job->tgl_master_bl)->format('d-m-Y')
                    : null,
                "nomorSegelBc" => $container->nomor_segel_bc ?? '-',
                "consignee" => $container->cust->name ?? '-',
                "pelabuhanMuat" => $container->job->muat->kode ?? null,
                "nomorDaftarPabean" => isset($container->job->noplp)
                    ? substr($container->job->noplp, 0, 10)
                    : null,
                "noBlAwb" => $container->nobl ?? null,
                "kodeKantor" => $container->job->dokplp->kd_kantor ?? null,
                "nomorKontainer" => $container->nocontainer,
                "idConsignee" => $container->cust_id ?? '-',
                "jenisKontainer" => 'FCL',
                "nomorSegel" => $container->seal->code ?? '-',
                "isoCode" => $container->iso_code ?? null,
                "tanggalDaftarPabean" => !empty($container->job->ttgl_plp)
                    ? Carbon::parse($container->job->ttgl_plp)->format('d-m-Y')
                    : null,
                "pelabuhanTransit" => $container->job->transit->kode ?? null,
                "bruto" => $container->weight ?? 0,
                "tanggalIjinTps" => !empty($container->tanggal_ijin_tps)
                    ? Carbon::parse($container->tanggal_ijin_tps)->format('d-m-Y')
                    : null,
            ];
    
            // dd($data);
             $data = [
                "header"     => $header,
                "kontainer"  => $kontainer,
            ];
            $response = $this->request(
                'post',
                $this->baseUrl . '/coarri-codeco-container',
                $data
            )->json();

            if (isset($response['code']) && in_array($response['code'], [200, 201])) {
                $status = 'Y';
            } else {
                $status = 'C';
            }
            if (isset($response['code']) && in_array($response['code'], [200, 201])) {          
                $status = 'Y';          
                $message = $response['detail'] ?? 'SUCCESS';            
            } else {            
                $status = 'C';          
                $errors = [];           
                if (!empty($response['data']['header']['errors'])) {            
                    foreach ($response['data']['header']['errors'] as $error) {         
                        $field = $error['field'] ?? '-';
                        $errorMessage = $error['message'] ?? '-';           
                        $errors[] = "HEADER: {$field} = {$errorMessage}";
                    }
                }           
                if (!empty($response['data']['kontainer'])) {           
                    foreach ($response['data']['kontainer'] as $containerError) {           
                        if (!empty($containerError['errors'])) {            
                            foreach ($containerError['errors'] as $error) {         
                                $field = $error['field'] ?? '-';
                                $errorMessage = $error['message'] ?? '-';           
                                $errors[] = "CONTAINER: {$field} = {$errorMessage}";
                            }
                        }
                    }
                }           
                $message = !empty($errors)
                    ? implode("\n", $errors)
                    : ($response['detail']
                        ?? $response['result']
                        ?? 'API ERROR');
            }
            $container->update([
                'coarri_cesa_flag' => $status,
                'coarri_cesa_time' => Carbon::now(),
                'coarri_cesa_status' => $message,
            ]);
        }


        $containersLCL = Cont::with(['job'])->whereNotNull('tglmasuk')->whereDate('tglmasuk', '>=', $startDate)->where('coarri_cesa_flag', 'N')->take(20)->get();
        foreach ($containersLCL as $container) {
            $container->update([
                'coarri_cesa_flag' => 'P',
                'coarri_cesa_time' => Carbon::now(),
                'coarri_cesa_status' => 'PROCESSING',
            ]);
            $header = [
                "kodeDokumen"         => "5",
                "noBc11"              => $container->job->tno_bc11 ?? null,
                "tanggalBc11"        => !empty($container->job->ttgl_bc11)
                    ? Carbon::parse($container->job->ttgl_bc11)->format('d-m-Y')
                    : null,
                "nomorVoyFlight"      => $container->job->dokplp->no_voy_flight ?? null,
                "tanggalBerangkat"    => !empty($container->job->tgl_berangkat)
                    ? Carbon::parse($container->job->tgl_berangkat)->format('d-m-Y')
                    : null,
                "namaAngkut"          => $container->job->dokplp->nm_angkut ?? null,
                "refNumber"           => Str::random(20),
                "kodeSaranaPengangkut"=> $container->job->kode_sarana_pengangkut ?? null,
                "kodeTps"             => "1MUT",
                "tanggalTiba"         => !empty($container->job->dokplp->tgl_tiba)
                    ? Carbon::parse($container->job->dokplp->tgl_tiba)->format('d-m-Y')
                    : null,
                "kodeGudang"          => "INTI",
                "callSign"            => $container->job->dokplp->call_sign ?? null,
            ];

             $waktuInOut = null;

            if (!empty($container->tglmasuk)) {
                $tanggalMasuk = $container->tglmasuk;

                $jamMasuk = !empty($container->jammasuk)
                    ? $container->jammasuk
                    : '00:00:00';

                $waktuInOut = Carbon::parse(
                    $tanggalMasuk . ' ' . $jamMasuk
                )->format('d-m-Y H:i:s');
            }

            $kontainer = [];
            $plpDetail = PLPdetail::where('plp_id', $container->job->plp_id)
                ->where('no_cont', $container->nocontainer)
                ->first();

            $nomorPosBc11 = $plpDetail?->no_pos_bc11;

            $kontainer[] = [
                "tanggalSegelBc" => !empty($container->tgl_segel_bc)
                    ? Carbon::parse($container->tgl_segel_bc)->format('d-m-Y')
                    : Carbon::parse($container->tglmasuk)->format('d-m-Y'),
                "tanggalDokumenInOut" => !empty($container->job->ttgl_plp)
                    ? Carbon::parse($container->job->ttgl_plp)->format('d-m-Y')
                    : null,
                "tanggalBlAwb" => !empty($container->tgl_bl_awb)
                    ? Carbon::parse($container->tgl_bl_awb)->format('d-m-Y')
                    : null,
                "flagKontainerKosong" => false,
                "waktuInOut" => $waktuInOut,
                "gudangTujuan" => "INTI",
                "kodeDokumenInOut" => "3",
                "ukuranKontainer" => $container->size,
                "flagKontainer" => true,
                "kodeTimbun" => null,
                "noMasterBlAwb" => $container->job->nombl ?? null,
                "pelabuhanBongkar" => $container->job->bongkar->kode ?? null,
                "nomorDokumenInOut" => $container->job->noplp ?? null,
                "nomorPolisi" => $container->nopol ?? '-',
                "nomorIjinTps" => $container->nomor_ijin_tps ?? null,
                "nomorPosBc11" => $nomorPosBc11 ?? null,
                "tanggalMasterBlAwb" => !empty($container->job->tgl_master_bl)
                    ? Carbon::parse($container->job->tgl_master_bl)->format('d-m-Y')
                    : null,
                "nomorSegelBc" => $container->nomor_segel_bc ?? '-',
                "consignee" => $container->cust->name ?? '-',
                "pelabuhanMuat" => $container->job->muat->kode ?? null,
                "nomorDaftarPabean" => isset($container->job->noplp)
                    ? substr($container->job->noplp, 0, 10)
                    : null,
                "noBlAwb" => $container->nobl ?? null,
                "kodeKantor" => $container->job->dokplp->kd_kantor ?? null,
                "nomorKontainer" => $container->nocontainer,
                "idConsignee" => $container->cust_id ?? '-',
                "jenisKontainer" => 'LCL',
                "nomorSegel" => $container->seal->code ?? '-',
                "isoCode" => $container->iso_code ?? null,
                "tanggalDaftarPabean" => !empty($container->job->ttgl_plp)
                    ? Carbon::parse($container->job->ttgl_plp)->format('d-m-Y')
                    : null,
                "pelabuhanTransit" => $container->job->transit->kode ?? null,
                "bruto" => $container->weight ?? 0,
                "tanggalIjinTps" => !empty($container->tanggal_ijin_tps)
                    ? Carbon::parse($container->tanggal_ijin_tps)->format('d-m-Y')
                    : null,
            ];
    
            // dd($data);
             $data = [
                "header"     => $header,
                "kontainer"  => $kontainer,
            ];
            $response = $this->request(
                'post',
                $this->baseUrl . '/coarri-codeco-container',
                $data
            )->json();

            if (isset($response['code']) && in_array($response['code'], [200, 201])) {
                $status = 'Y';
            } else {
                $status = 'C';
            }

            if (isset($response['code']) && in_array($response['code'], [200, 201])) {
                $status = 'Y';
            } else {
                $status = 'C';
            }
            if (isset($response['code']) && in_array($response['code'], [200, 201])) {          
                $status = 'Y';          
                $message = $response['detail'] ?? 'SUCCESS';            
            } else {            
                $status = 'C';          
                $errors = [];           
                if (!empty($response['data']['header']['errors'])) {            
                    foreach ($response['data']['header']['errors'] as $error) {         
                        $field = $error['field'] ?? '-';
                        $errorMessage = $error['message'] ?? '-';           
                        $errors[] = "HEADER: {$field} = {$errorMessage}";
                    }
                }           
                if (!empty($response['data']['kontainer'])) {           
                    foreach ($response['data']['kontainer'] as $containerError) {           
                        if (!empty($containerError['errors'])) {            
                            foreach ($containerError['errors'] as $error) {         
                                $field = $error['field'] ?? '-';
                                $errorMessage = $error['message'] ?? '-';           
                                $errors[] = "CONTAINER: {$field} = {$errorMessage}";
                            }
                        }
                    }
                }           
                $message = !empty($errors)
                    ? implode("\n", $errors)
                    : ($response['detail']
                        ?? $response['result']
                        ?? 'API ERROR');
            }

            $container->update([
                'coarri_cesa_flag' => $status,
                'coarri_cesa_time' => Carbon::now(),
                'coarri_cesa_status' => $message,
            ]);
        }
        return;
    }

    public function codecoCont()
    {
        $startDate = '2026-07-01';
        $containersFCL = ContF::with(['job'])->whereNotNull('tglkeluar')->whereDate('tglmasuk', '>=', $startDate)->where('coarri_cesa_flag', 'Y')->where('codeco_cesa_flag', 'N')->take(20)->get();
        foreach ($containersFCL as $container) {
            $container->update([
                'coarri_cesa_flag' => 'P',
                'coarri_cesa_time' => Carbon::now(),
                'coarri_cesa_status' => 'PROCESSING',
            ]);
            $header = [
                "kodeDokumen"         => "6",
                "noBc11"              => $container->job->tno_bc11 ?? null,
                "tanggalBc11"        => !empty($container->job->ttgl_bc11)
                    ? Carbon::parse($container->job->ttgl_bc11)->format('d-m-Y')
                    : null,
                "nomorVoyFlight"      => $container->job->dokplp->no_voy_flight ?? null,
                "tanggalBerangkat"    => !empty($container->job->tgl_berangkat)
                    ? Carbon::parse($container->job->tgl_berangkat)->format('d-m-Y')
                    : null,
                "namaAngkut"          => $container->job->dokplp->nm_angkut ?? null,
                "refNumber"           => Str::random(20),
                "kodeSaranaPengangkut"=> $container->job->kode_sarana_pengangkut ?? null,
                "kodeTps"             => "1MUT",
                "tanggalTiba"         => !empty($container->job->dokplp->tgl_tiba)
                    ? Carbon::parse($container->job->dokplp->tgl_tiba)->format('d-m-Y')
                    : null,
                "kodeGudang"          => "INTI",
                "callSign"            => $container->job->dokplp->call_sign ?? null,
            ];

             $waktuInOut = null;

            if (!empty($container->tglkeluar)) {
                $tanggalMasuk = $container->tglkeluar;

                $jamMasuk = !empty($container->jamkeluar)
                    ? $container->jamkeluar
                    : '00:00:00';

                $waktuInOut = Carbon::parse(
                    $tanggalMasuk . ' ' . $jamMasuk
                )->format('d-m-Y H:i:s');
            }

            $kontainer = [];
            $plpDetail = PLPdetail::where('plp_id', $container->job->plp_id)
                ->where('no_cont', $container->nocontainer)
                ->first();

            $nomorPosBc11 = $plpDetail?->no_pos_bc11;

            $kontainer[] = [
                "tanggalSegelBc" => !empty($container->tgl_segel_bc)
                    ? Carbon::parse($container->tgl_segel_bc)->format('d-m-Y')
                    : Carbon::parse($container->tglmasuk)->format('d-m-Y'),
                "tanggalDokumenInOut" => !empty($container->job->ttgl_plp)
                    ? Carbon::parse($container->job->ttgl_plp)->format('d-m-Y')
                    : null,
                "tanggalBlAwb" => !empty($container->tgl_bl_awb)
                    ? Carbon::parse($container->tgl_bl_awb)->format('d-m-Y')
                    : null,
                "flagKontainerKosong" => false,
                "waktuInOut" => $waktuInOut,
                "gudangTujuan" => "INTI",
                "kodeDokumenInOut" => $container->kd_dok_inout  ?? null,
                "ukuranKontainer" => $container->size,
                "flagKontainer" => true,
                "kodeTimbun" => null,
                "noMasterBlAwb" => $container->job->nombl ?? null,
                "pelabuhanBongkar" => $container->job->bongkar->kode ?? null,
                "nomorDokumenInOut" => $container->no_dok ?? null,
                "nomorPolisi" => $container->nopol_mty ?? '-',
                "nomorIjinTps" => $container->nomor_ijin_tps ?? null,
                "nomorPosBc11" => $nomorPosBc11 ?? null,
                "tanggalMasterBlAwb" => !empty($container->job->tgl_master_bl)
                    ? Carbon::parse($container->job->tgl_master_bl)->format('d-m-Y')
                    : null,
                "nomorSegelBc" => $container->nomor_segel_bc ?? '-',
                "consignee" => $container->cust->name ?? '-',
                "pelabuhanMuat" => $container->job->muat->kode ?? null,
                "nomorDaftarPabean" => $container->no_dok ?? null,
                "noBlAwb" => $container->nobl ?? null,
                "kodeKantor" => $container->job->dokplp->kd_kantor ?? null,
                "nomorKontainer" => $container->nocontainer,
                "idConsignee" => $container->cust_id ?? '-',
                "jenisKontainer" => 'FCL',
                "nomorSegel" => $container->seal->code ?? '-',
                "isoCode" => $container->iso_code ?? null,
                "tanggalDaftarPabean" => !empty($container->tgl_dok)
                    ? Carbon::parse($container->tgl_dok)->format('d-m-Y')
                    : null,
                "pelabuhanTransit" => $container->job->transit->kode ?? null,
                "bruto" => $container->weight ?? 0,
                "tanggalIjinTps" => !empty($container->tanggal_ijin_tps)
                    ? Carbon::parse($container->tanggal_ijin_tps)->format('d-m-Y')
                    : null,
            ];
    
            // dd($data);
             $data = [
                "header"     => $header,
                "kontainer"  => $kontainer,
            ];
            $response = $this->request(
                'post',
                $this->baseUrl . '/coarri-codeco-container',
                $data
            )->json();

            if (isset($response['code']) && in_array($response['code'], [200, 201])) {
                $status = 'Y';
            } else {
                $status = 'C';
            }

            if (isset($response['code']) && in_array($response['code'], [200, 201])) {
                $status = 'Y';
            } else {
                $status = 'C';
            }
            if (isset($response['code']) && in_array($response['code'], [200, 201])) {          
                $status = 'Y';          
                $message = $response['detail'] ?? 'SUCCESS';            
            } else {            
                $status = 'C';          
                $errors = [];           
                if (!empty($response['data']['header']['errors'])) {            
                    foreach ($response['data']['header']['errors'] as $error) {         
                        $field = $error['field'] ?? '-';
                        $errorMessage = $error['message'] ?? '-';           
                        $errors[] = "HEADER: {$field} = {$errorMessage}";
                    }
                }           
                if (!empty($response['data']['kontainer'])) {           
                    foreach ($response['data']['kontainer'] as $containerError) {           
                        if (!empty($containerError['errors'])) {            
                            foreach ($containerError['errors'] as $error) {         
                                $field = $error['field'] ?? '-';
                                $errorMessage = $error['message'] ?? '-';           
                                $errors[] = "CONTAINER: {$field} = {$errorMessage}";
                            }
                        }
                    }
                }           
                $message = !empty($errors)
                    ? implode("\n", $errors)
                    : ($response['detail']
                        ?? $response['result']
                        ?? 'API ERROR');
            }

            $container->update([
                'coarri_cesa_flag' => $status,
                'coarri_cesa_time' => Carbon::now(),
                'coarri_cesa_status' => $message,
            ]);
        }


        $containersLCL = Cont::with(['job'])->whereNotNull('tglkeluar')->whereDate('tglmasuk', '>=', $startDate)->where('coarri_cesa_flag', 'Y')->where('codeco_cesa_flag', 'N')->take(20)->get();
        foreach ($containersLCL as $container) {
            $container->update([
                'coarri_cesa_flag' => 'P',
                'coarri_cesa_time' => Carbon::now(),
                'coarri_cesa_status' => 'PROCESSING',
            ]);
            $header = [
                "kodeDokumen"         => "6",
                "noBc11"              => $container->job->tno_bc11 ?? null,
                "tanggalBc11"        => !empty($container->job->ttgl_bc11)
                    ? Carbon::parse($container->job->ttgl_bc11)->format('d-m-Y')
                    : null,
                "nomorVoyFlight"      => $container->job->dokplp->no_voy_flight ?? null,
                "tanggalBerangkat"    => !empty($container->job->tgl_berangkat)
                    ? Carbon::parse($container->job->tgl_berangkat)->format('d-m-Y')
                    : null,
                "namaAngkut"          => $container->job->dokplp->nm_angkut ?? null,
                "refNumber"           => Str::random(20),
                "kodeSaranaPengangkut"=> $container->job->kode_sarana_pengangkut ?? null,
                "kodeTps"             => "1MUT",
                "tanggalTiba"         => !empty($container->job->dokplp->tgl_tiba)
                    ? Carbon::parse($container->job->dokplp->tgl_tiba)->format('d-m-Y')
                    : null,
                "kodeGudang"          => "INTI",
                "callSign"            => $container->job->dokplp->call_sign ?? null,
            ];

             $waktuInOut = null;

            if (!empty($container->tglmasuk)) {
                $tanggalMasuk = $container->tglkeluar;

                $jamMasuk = !empty($container->jamkeluar)
                    ? $container->jamkeluar
                    : '00:00:00';

                $waktuInOut = Carbon::parse(
                    $tanggalMasuk . ' ' . $jamMasuk
                )->format('d-m-Y H:i:s');
            }

            $kontainer = [];
            $plpDetail = PLPdetail::where('plp_id', $container->job->plp_id)
                ->where('no_cont', $container->nocontainer)
                ->first();

            $nomorPosBc11 = $plpDetail?->no_pos_bc11;

            $kontainer[] = [
                "tanggalSegelBc" => !empty($container->tgl_segel_bc)
                    ? Carbon::parse($container->tgl_segel_bc)->format('d-m-Y')
                    : Carbon::parse($container->tglmasuk)->format('d-m-Y'),
                "tanggalDokumenInOut" => !empty($container->job->ttgl_plp)
                    ? Carbon::parse($container->job->ttgl_plp)->format('d-m-Y')
                    : null,
                "tanggalBlAwb" => !empty($container->tgl_bl_awb)
                    ? Carbon::parse($container->tgl_bl_awb)->format('d-m-Y')
                    : null,
                "flagKontainerKosong" => false,
                "waktuInOut" => $waktuInOut,
                "gudangTujuan" => "INTI",
                "kodeDokumenInOut" => "3",
                "ukuranKontainer" => $container->size,
                "flagKontainer" => true,
                "kodeTimbun" => null,
                "noMasterBlAwb" => $container->job->nombl ?? null,
                "pelabuhanBongkar" => $container->job->bongkar->kode ?? null,
                "nomorDokumenInOut" => $container->job->noplp ?? null,
                "nomorPolisi" => $container->nopol ?? '-',
                "nomorIjinTps" => $container->nomor_ijin_tps ?? null,
                "nomorPosBc11" => $nomorPosBc11 ?? null,
                "tanggalMasterBlAwb" => !empty($container->job->tgl_master_bl)
                    ? Carbon::parse($container->job->tgl_master_bl)->format('d-m-Y')
                    : null,
                "nomorSegelBc" => $container->nomor_segel_bc ?? '-',
                "consignee" => $container->cust->name ?? '-',
                "pelabuhanMuat" => $container->job->muat->kode ?? null,
                "nomorDaftarPabean" => isset($container->job->noplp)
                    ? substr($container->job->noplp, 0, 10)
                    : null,
                "noBlAwb" => $container->nobl ?? null,
                "kodeKantor" => $container->job->dokplp->kd_kantor ?? null,
                "nomorKontainer" => $container->nocontainer,
                "idConsignee" => $container->cust_id ?? '-',
                "jenisKontainer" => 'LCL',
                "nomorSegel" => $container->seal->code ?? '-',
                "isoCode" => $container->iso_code ?? null,
                "tanggalDaftarPabean" => !empty($container->job->ttgl_plp)
                    ? Carbon::parse($container->job->ttgl_plp)->format('d-m-Y')
                    : null,
                "pelabuhanTransit" => $container->job->transit->kode ?? null,
                "bruto" => $container->weight ?? 0,
                "tanggalIjinTps" => !empty($container->tanggal_ijin_tps)
                    ? Carbon::parse($container->tanggal_ijin_tps)->format('d-m-Y')
                    : null,
            ];
    
            // dd($data);
             $data = [
                "header"     => $header,
                "kontainer"  => $kontainer,
            ];
            $response = $this->request(
                'post',
                $this->baseUrl . '/coarri-codeco-container',
                $data
            )->json();

            if (isset($response['code']) && in_array($response['code'], [200, 201])) {
                $status = 'Y';
            } else {
                $status = 'C';
            }

            if (isset($response['code']) && in_array($response['code'], [200, 201])) {
                $status = 'Y';
            } else {
                $status = 'C';
            }
            if (isset($response['code']) && in_array($response['code'], [200, 201])) {          
                $status = 'Y';          
                $message = $response['detail'] ?? 'SUCCESS';            
            } else {            
                $status = 'C';          
                $errors = [];           
                if (!empty($response['data']['header']['errors'])) {            
                    foreach ($response['data']['header']['errors'] as $error) {         
                        $field = $error['field'] ?? '-';
                        $errorMessage = $error['message'] ?? '-';           
                        $errors[] = "HEADER: {$field} = {$errorMessage}";
                    }
                }           
                if (!empty($response['data']['kontainer'])) {           
                    foreach ($response['data']['kontainer'] as $containerError) {           
                        if (!empty($containerError['errors'])) {            
                            foreach ($containerError['errors'] as $error) {         
                                $field = $error['field'] ?? '-';
                                $errorMessage = $error['message'] ?? '-';           
                                $errors[] = "CONTAINER: {$field} = {$errorMessage}";
                            }
                        }
                    }
                }           
                $message = !empty($errors)
                    ? implode("\n", $errors)
                    : ($response['detail']
                        ?? $response['result']
                        ?? 'API ERROR');
            }

            $container->update([
                'coarri_cesa_flag' => $status,
                'coarri_cesa_time' => Carbon::now(),
                'coarri_cesa_status' => $message,
            ]);
        }
        return;
    }

    private function request($method, $url, $query = [])
    {
        $token = $this->getToken();
        // $token = '123';
        $response = Http::withToken($token)
            ->withHeaders([
                'beacukai-api-key' => $this->apiKey,
                'Accept' => 'application/json',
            ])
            ->$method($url, $query);

        // Jika token expired
        if (in_array($response->status(), [401])) {

            $token = $this->login(); // login dan simpan token baru

            $response = Http::withToken($token)
                ->withHeaders([
                    'beacukai-api-key' => $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->$method($url, $query);
        }
        // dd($response);
        // dd($response->effectiveUri(), $response->status(), $response->body());
        return $response;
    }
}
