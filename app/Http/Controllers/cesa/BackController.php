<?php

namespace App\Http\Controllers\cesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use Carbon\Carbon;
use Auth;

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
                'tanggalPlp' => Carbon::parse($request->tanggalPlp)->format('d-m-Y'),
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

                    // if ($oldPLP) {
                    //     continue;
                    // }

                    $plp = PLP::create([
                        'tgl_upload'        => now()->format('Ymd'),
                        'upload_date'       => today(),
                        'upload_time'       => now()->format('H:i:s'),

                        'kd_kantor'         => $header['kodeKantor'],
                        'kd_tps'            => '1MUT',
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
                            'kd_tps' => '1MUT',
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
                            'uid' => 1,
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

                        $kontainer = $group['kontainer'][0] ?? null;
                        $kemasan = $group['kemasan'][0] ?? null;

                        $dataCfs = [
                            'header' => [
                                'idheader' => $header['idheader'] ?? null,
                                'car' => $header['car'] ?? null,
                                'nomorDokumen' => $header['noSppb'] ?? null,
                                'tanggalDokumen' => $header['tglSppb'] ?? null,
                                'kodeKantor' => $header['kodeKantorPengawas'] ?? null,
                                'nomorPib' => $header['noPib'] ?? null,
                                'tanggalPib' => $header['tglPib'] ?? null,
                                'npwpImp' => $header['npwpImp'] ?? null,
                                'namaImp' => $header['namaImp'] ?? null,
                                'alamatImp' => $header['alamatImp'] ?? null,
                                'npwpPpjk' => $header['npwpPpjk'] ?? null,
                                'namaPpjk' => $header['namaPpjk'] ?? null,
                                'alamatPpjk' => $header['alamatPpjk'] ?? null,
                                'namaAngkut' => $header['namaAngkut'] ?? null,
                                'nomorVoyFlight' => $header['noVoyFlight'] ?? null,
                                'bruto' => $header['bruto'] ?? null,
                                'netto' => $header['netto'] ?? null,
                                'gudang' => $header['gudang'] ?? null,
                                'statusJalur' => $header['statusJalur'] ?? null,
                                'flagKarantina' => $header['flagKarantina'] ?? null,
                                'jumlahKontainer' => $header['jumlahKontainer'] ?? null,
                                'nomorBc11' => $header['noBc11'] ?? null,
                                'tanggalBc11' => $header['tglBc11'] ?? null,
                                'nomorPosBc11' => $header['noPosBc11'] ?? null,
                                'nomorBlAwb' => $header['noBlAwb'] ?? null,
                                'tanggalBlAwb' => $header['tglBlAwb'] ?? null,
                                'nomorMasterBlAwb' => $header['noMasterBlAwb'] ?? null,
                                'tanggalMasterBlAwb' => $header['tglMasterBlAwb'] ?? null,
                            ],
                            'kontainer' => $kontainer ?? (object) [],
                            'kemasan' => $kemasan ?? (object) [],
                        ];

                        /*
                         * ==========================================================
                         * KIRIM KE CFS CENTER
                         * ==========================================================
                         */

                        $cfsSuccess = false;
                        $cfsResponse = null;
                        $cfsStatus = null;

                        try {
                            $responseCFS = Http::timeout(60)
                                ->withBasicAuth('1MUT', '1MUT')
                                ->withoutVerifying()
                                ->withHeaders([
                                    'Accept' => 'application/json',
                                    'Content-Type' => 'application/json',
                                ])
                                ->post(
                                    'https://pelindo-cfscenter.com/index.php/apijson/receivepermit',
                                    $dataCfs
                                );

                            $cfsSuccess = $responseCFS->successful();
                            $cfsStatus = $responseCFS->status();
                            $cfsResponse = $responseCFS->json() ?? $responseCFS->body();

                            if ($cfsSuccess) {
                                $hasil['cfs_berhasil']++;
                            } else {
                                $hasil['cfs_gagal']++;
                            }
                        } catch (\Throwable $e) {
                            $cfsSuccess = false;
                            $cfsResponse = [
                                'message' => $e->getMessage()
                            ];
                            $hasil['cfs_gagal']++;
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

    public function sppbOnDemand(Request $request)
    {
        $response = $this->request(
            'get',
            $this->baseUrl . '/get-impor-sppb',
            [
                'kodeGudang' => 'INTI',
                'nomorDokumen' => $request->no_dok,
                'tanggalDokumen' => Carbon::parse($request->tgl_dok)->format('d-m-Y'),
                'npwpImp' => $request->npwp
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
                $data = $response['data'][0] ?? null;
                $header = $data['header'][0] ?? null;
                $kontainer = $data['kontainer'][0] ?? null;
                $kemasan = $data['kemasan'][0] ?? null;

                if (!$header) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data header SPPB tidak ditemukan'
                    ]);
                }
                $dataCfs = [
                    'header' => [
                        'idheader' => $header['idheader'] ?? null,
                        'car' => $header['car'] ?? null,
                        'nomorDokumen' => $header['nomorDokumen'] ?? null,
                        'tanggalDokumen' => $header['tanggalDokumen'] ?? null,
                        'kodeKantor' => $header['kodeKantor'] ?? null,
                        'nomorPib' => $header['nomorPib'] ?? null,
                        'tanggalPib' => $header['tanggalPib'] ?? null,
                        'npwpImp' => $header['npwpImp'] ?? null,
                        'namaImp' => $header['namaImp'] ?? null,
                        'alamatImp' => $header['alamatImp'] ?? null,
                        'npwpPpjk' => $header['npwpPpjk'] ?? null,
                        'namaPpjk' => $header['namaPpjk'] ?? null,
                        'alamatPpjk' => $header['alamatPpjk'] ?? null,
                        'namaAngkut' => $header['namaAngkut'] ?? null,
                        'nomorVoyFlight' => $header['nomorVoyFlight'] ?? null,
                        'bruto' => $header['bruto'] ?? null,
                        'netto' => $header['netto'] ?? null,
                        'gudang' => $header['gudang'] ?? null,
                        'statusJalur' => $header['statusJalur'] ?? null,
                        'flagKarantina' => $header['flagKarantina'] ?? null,
                        'jumlahKontainer' => $header['jumlahKontainer'] ?? null,
                        'nomorBc11' => $header['nomorBc11'] ?? null,
                        'tanggalBc11' => $header['tanggalBc11'] ?? null,
                        'nomorPosBc11' => $header['nomorPosBc11'] ?? null,
                        'nomorBlAwb' => $header['nomorBlAwb'] ?? null,
                        'tanggalBlAwb' => $header['tanggalBlAwb'] ?? null,
                        'nomorMasterBlAwb' => $header['nomorMasterBlAwb'] ?? null,
                        'tanggalMasterBlAwb' => $header['tanggalMasterBlAwb'] ?? null,
                    ],
                    'kontainer' => $kontainer,
                    'kemasan' => $kemasan,
                ];
                $cfsSuccess = false;
                $cfsResponse = null;
                $cfsStatus = null;

                try {
                    $responseCFS = Http::timeout(60)
                        ->withBasicAuth('1MUT', '1MUT')
                        ->withoutVerifying()
                        ->withHeaders([
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ])
                        ->post(
                            'https://pelindo-cfscenter.com/index.php/apijson/receivepermit',
                            $dataCfs
                        );

                    $cfsSuccess = $responseCFS->successful();
                    $cfsStatus = $responseCFS->status();
                    $cfsResponse = $responseCFS->json() ?? $responseCFS->body();
                } catch (\Throwable $e) {
                    $cfsSuccess = false;
                    $cfsResponse = [
                        'message' => $e->getMessage()
                    ];
                }
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
                    'message' => $cfsSuccess
                        ? 'Data berhasil disimpan dan dikirim ke CFS Center'
                        : 'Data berhasil disimpan, tetapi pengiriman ke CFS Center gagal :' . $cfsResponse['message'],
                    'cfs_success' => $cfsSuccess,
                    'cfs_status' => $cfsStatus,
                    'cfs_response' => $cfsResponse,
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

    // bc23
    public function bc23Get()
    {
        $response = $this->request(
            'get',
            $this->baseUrl . '/get-bc23-permit',
            [
                'kodeGudang' => 'INTI',
            ]
        )->json();  

        if ($response['code'] !== 200) {
            return response()->json([
                'success' => false,
                'message' => $response['detail'] ?? 'Terjadi kesalahan'
            ]);
        }   

        if (empty($response['data']['sppb'])) {
            return response()->json([
                'success' => false,
                'message' => $response['detail'] ?? 'Data BC23 tidak ditemukan',
                'data' => [],
            ]);
        }   

        $hasil = [
            'total' => 0,
            'berhasil' => 0,
            'dilewati' => 0,
            'cfs_berhasil' => 0,
            'cfs_gagal' => 0,
            'detail' => [],
        ];  

        foreach ($response['data']['sppb'] as $sppb) {
            $hasil['total']++;  

            $header = $sppb['header'] ?? [];
            $detil = $sppb['detil'] ?? [];  

            try {
                if (empty($header)) {
                    $hasil['dilewati']++;
                    $hasil['detail'][] = [
                        'status' => 'skip',
                        'message' => 'Header BC23 tidak ditemukan',
                    ];
                    continue;
                }   

                $oldBC23 = BC23::where('car', $header['car'] ?? null)->first(); 

                if ($oldBC23) {
                    $hasil['dilewati']++;
                    $hasil['detail'][] = [
                        'status' => 'skip',
                        'car' => $header['car'] ?? null,
                        'no_sppb' => $header['nomorSppb'] ?? null,
                        'message' => 'BC23 sudah ada',
                    ];
                    continue;
                }   

                /*
                 * ==========================================================
                 * DATA UNTUK CFS CENTER
                 * ==========================================================
                 */ 

                $kontainer = $detil['kontainer'][0] ?? null;
                $kemasan = $detil['kemasan'][0] ?? null;    

                $dataCfs = [
                    'sppb' => [
                        'detil' => [
                            'kemasan' => $kemasan ?? (object) [],
                            'kontainer' => $kontainer ?? (object) [],
                        ],
                        'header' => [
                            'tanggalBc11' => $header['tanggalBc11'] ?? null,
                            'tanggalBlAwb' => $header['tanggalBlAwb'] ?? null,
                            'gudang' => $header['gudang'] ?? null,
                            'statusJalur' => $header['statusJalur'] ?? null,
                            'nomorBc11' => $header['nomorBc11'] ?? null,
                            'nomorMasterBlAwb' => $header['nomorMasterBlAwb'] ?? null,
                            'namaPpjk' => $header['namaPpjk'] ?? null,
                            'alamatImp' => $header['alamatImp'] ?? null,
                            'car' => $header['car'] ?? null,
                            'namaAngkut' => $header['namaAngkut'] ?? null,
                            'nomorBlAwb' => $header['nomorBlAwb'] ?? null,
                            'tanggalPib' => $header['tanggalPib'] ?? null,
                            'kodeKantorBongkar' => $header['kodeKantorBongkar'] ?? null,
                            'npwpPpjk' => $header['npwpPpjk'] ?? null,
                            'nomorPosBc11' => $header['nomorPosBc11'] ?? null,
                            'tanggalMasterBlAwb' => $header['tanggalMasterBlAwb'] ?? null,
                            'nomorVoyFlight' => $header['nomorVoyFlight'] ?? null,
                            'namaImp' => $header['namaImp'] ?? null,
                            'netto' => $header['netto'] ?? null,
                            'idHeader' => $header['idHeader'] ?? null,
                            'tanggalSppb' => $header['tanggalSppb'] ?? null,
                            'kodeKantorPengawas' => $header['kodeKantorPengawas'] ?? null,
                            'alamatPpjk' => $header['alamatPpjk'] ?? null,
                            'nomorSppb' => $header['nomorSppb'] ?? null,
                            'bruto' => $header['bruto'] ?? null,
                            'nomorPib' => $header['nomorPib'] ?? null,
                            'npwpImp' => $header['npwpImp'] ?? null,
                            'jumlahKontainer' => $header['jumlahKontainer'] ?? 0,
                        ],
                    ],
                ];  

                /*
                 * ==========================================================
                 * KIRIM KE CFS CENTER
                 * CFS GAGAL TIDAK MENGHENTIKAN PROSES
                 * ==========================================================
                 */ 

                $cfsSuccess = false;
                $cfsResponse = null;
                $cfsStatus = null;  

                try {
                    $responseCFS = Http::timeout(60)
                        ->withBasicAuth('1MUT', '1MUT')
                        ->withoutVerifying()
                        ->withHeaders([
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ])
                        ->post(
                            'https://pelindo-cfscenter.com/index.php/apijson/receivepermit_23',
                            $dataCfs
                        );  

                    $cfsSuccess = $responseCFS->successful();
                    $cfsStatus = $responseCFS->status();
                    $cfsResponse = $responseCFS->json() ?? $responseCFS->body();
                } catch (\Throwable $e) {
                    $cfsSuccess = false;
                    $cfsResponse = [
                        'message' => $e->getMessage()
                    ];
                }   

                if ($cfsSuccess) {
                    $hasil['cfs_berhasil']++;
                } else {
                    $hasil['cfs_gagal']++;
                }   

                /*
                 * ==========================================================
                 * SIMPAN KE DATABASE
                 * ==========================================================
                 */ 

                DB::transaction(function () use ($sppb) {
                    $header = $sppb['header'] ?? [];
                    $detil = $sppb['detil'] ?? [];  

                    $oldBC23 = BC23::where('car', $header['car'] ?? null)->first(); 

                    if ($oldBC23) {
                        return;
                    }   

                    $bc23 = BC23::create([
                        'car' => $header['car'] ?? null,
                        'no_sppb' => $header['nomorSppb'] ?? null,
                        'tgl_sppb' => $header['tanggalSppb'] ?? null,
                        'nojoborder' => null,
                        'kd_kantor_pengawas' => $header['kodeKantorPengawas'] ?? null,
                        'kd_kantor_bongkar' => $header['kodeKantorBongkar'] ?? null,
                        'no_pib' => $header['nomorPib'] ?? null,
                        'tgl_pib' => $header['tanggalPib'] ?? null,
                        'nama_imp' => $header['namaImp'] ?? null,
                        'npwp_imp' => $header['npwpImp'] ?? null,
                        'alamat_imp' => $header['alamatImp'] ?? null,
                        'npwp_ppjk' => $header['npwpPpjk'] ?? null,
                        'nama_ppjk' => $header['namaPpjk'] ?? null,
                        'alamat_ppjk' => $header['alamatPpjk'] ?? null,
                        'nm_angkut' => $header['namaAngkut'] ?? null,
                        'no_voy_flight' => $header['nomorVoyFlight'] ?? null,
                        'bruto' => $header['bruto'] ?? null,
                        'netto' => $header['netto'] ?? null,
                        'gudang' => $header['gudang'] ?? null,
                        'status_jalur' => $header['statusJalur'] ?? null,
                        'jml_cont' => $header['jumlahKontainer'] ?? 0,
                        'no_bc11' => $header['nomorBc11'] ?? null,
                        'tgl_bc11' => $header['tanggalBc11'] ?? null,
                        'no_pos_bc11' => $header['nomorPosBc11'] ?? null,
                        'no_bl_awb' => $header['nomorBlAwb'] ?? null,
                        'tgl_bl_awb' => $header['tanggalBlAwb'] ?? null,
                        'no_master_bl_awb' => $header['nomorMasterBlAwb'] ?? null,
                        'tgl_master_bl_awb' => $header['tanggalMasterBlAwb'] ?? null,
                        'tgl_upload' => Carbon::today()->format('Y-m-d'),
                        'jam_upload' => Carbon::now()->format('H:i:s'),
                    ]); 

                    $tglSppb = null;    

                    if (!empty($header['tanggalSppb'])) {
                        $tglSppb = Carbon::createFromFormat(
                            'd-m-Y',
                            $header['tanggalSppb']
                        )->format('Y-m-d');
                    }   

                    foreach ($detil['kontainer'] ?? [] as $detailCont) {
                        $bcCont = BC23Cont::create([
                            'sppb23_id' => $bc23->id,
                            'car' => $detailCont['car'] ?? null,
                            'no_cont' => $detailCont['nomorKontainer']
                                ?? $detailCont['noCont']
                                ?? $detailCont['no_cont']
                                ?? null,
                            'size' => $detailCont['size']
                                ?? $detailCont['ukuranKontainer']
                                ?? null,
                            'jns_muat' => $detailCont['jenisMuat']
                                ?? $detailCont['jnsMuat']
                                ?? null,
                        ]); 

                        if (($bc23->jml_cont ?? 0) > 0) {
                            $nomorContainer = $detailCont['nomorKontainer']
                                ?? $detailCont['noCont']
                                ?? $detailCont['no_cont']
                                ?? null;    

                            $size = $detailCont['size']
                                ?? $detailCont['ukuranKontainer']
                                ?? null;    

                            $contF = ContF::whereNull('tglkeluar')
                                ->where('nocontainer', $nomorContainer)
                                ->where('size', $size)
                                ->first();  

                            if ($contF) {
                                $alasanSize = null; 

                                if ($contF->size != $size) {
                                    $alasanSize = '& Ukuran Fisik Size Berbeda';
                                }   

                                $alasanFinal = 'Bukan Dokumen SPPB. ' . $alasanSize;    

                                $cust = Customer::where(
                                    'name',
                                    $bc23->nama_imp
                                )->first(); 

                                if ($cust) {
                                    $cust->update([
                                        'name' => $bc23->nama_imp,
                                        'npwp' => $bc23->npwp_imp,
                                        'alamat' => $bc23->alamat_imp,
                                    ]);
                                } elseif (!empty($bc23->nama_imp)) {
                                    $cust = Customer::create([
                                        'name' => $bc23->nama_imp,
                                        'npwp' => $bc23->npwp_imp,
                                        'alamat' => $bc23->alamat_imp,
                                    ]);
                                }   

                                $flagNoBl = !empty(trim($bc23->no_bl_awb ?? ''))
                                    ? $bc23->no_bl_awb
                                    : (!empty(trim($bc23->no_master_bl_awb ?? ''))
                                        ? $bc23->no_master_bl_awb
                                        : null);    

                                $contF->update([
                                    'kd_dok_inout' => 2,
                                    'no_dok' => $bc23->no_sppb,
                                    'tgl_dok' => $tglSppb,
                                    'status_bc' => 'HOLD',
                                    'alasan_hold' => $alasanFinal,
                                    'cust_id' => $cust?->id,
                                    'nobl' => $flagNoBl,
                                ]);
                            }
                        }
                    }   

                    foreach ($detil['kemasan'] ?? [] as $detailKMS) {
                        $bcKMS = BC23Kms::create([
                            'sppb23_id' => $bc23->id,
                            'car' => $detailKMS['car'] ?? null,
                            'jns_kms' => $detailKMS['jenisKemasan'] ?? null,
                            'merk_kms' => $detailKMS['merkKemasan']
                                ?? $detailKMS['merkKms']
                                ?? null,
                            'jml_kms' => $detailKMS['jumlahKemasan'] ?? null,
                        ]); 

                        if (($bc23->jml_cont ?? 0) == 0) {
                            $manifest = Manifest::where(
                                'nohbl',
                                $bc23->no_bl_awb
                            )->whereNull('tglbuangmty')->first();   

                            if ($manifest) {
                                $cust = Customer::where(
                                    'name',
                                    $bc23->nama_imp
                                )->first(); 

                                if ($cust) {
                                    $cust->update([
                                        'name' => $bc23->nama_imp,
                                        'npwp' => $bc23->npwp_imp,
                                        'alamat' => $bc23->alamat_imp,
                                    ]);
                                } elseif (!empty($bc23->nama_imp)) {
                                    $cust = Customer::create([
                                        'name' => $bc23->nama_imp,
                                        'npwp' => $bc23->npwp_imp,
                                        'alamat' => $bc23->alamat_imp,
                                    ]);
                                }   

                                $alasanKemas = null;    

                                if (
                                    $manifest->packing &&
                                    $manifest->packing->code != $bcKMS->jns_kms
                                ) {
                                    $alasanKemas = 'Jenis Kemas Berbeda';
                                }   

                                $alasanJml = null;  

                                if ($manifest->quantity != $bcKMS->jml_kms) {
                                    $alasanJml = 'Quantity Berbeda';
                                }   

                                $alasanQty = null;  

                                if ($manifest->final_qty != $manifest->quantity) {
                                    $alasanQty = 'Jumlah QTY Fisik Berbeda';
                                }   

                                $alasanFinal = implode(', ', array_filter([
                                    'Bukan Dokumen SPPB',
                                    $alasanKemas,
                                    $alasanJml,
                                    $alasanQty,
                                ]));    

                                $manifest->update([
                                    'kd_dok_inout' => 2,
                                    'no_dok' => $bc23->no_sppb,
                                    'tgl_dok' => $tglSppb,
                                    'status_bc' => 'HOLD',
                                    'cust_id' => $cust?->id,
                                    'alasan_hold' => $alasanFinal,
                                ]);
                            }
                        }
                    }
                }); 

                $hasil['berhasil']++;   

                $hasil['detail'][] = [
                    'status' => 'success',
                    'car' => $header['car'] ?? null,
                    'no_sppb' => $header['nomorSppb'] ?? null,
                    'cfs_success' => $cfsSuccess,
                    'cfs_status' => $cfsStatus,
                    'cfs_response' => $cfsResponse,
                ];  

            } catch (\Throwable $th) {
                $hasil['detail'][] = [
                    'status' => 'error',
                    'car' => $header['car'] ?? null,
                    'no_sppb' => $header['nomorSppb'] ?? null,
                    'message' => $th->getMessage(),
                ];
            }
        }   

        return response()->json([
            'success' => true,
            'message' => 'Proses BC23 selesai',
            'summary' => [
                'total' => $hasil['total'],
                'berhasil' => $hasil['berhasil'],
                'dilewati' => $hasil['dilewati'],
                'cfs_berhasil' => $hasil['cfs_berhasil'],
                'cfs_gagal' => $hasil['cfs_gagal'],
            ],
            'detail' => $hasil['detail'],
        ]);
    }

    public function bc23OnDemand(Request $request)
    {
        $response = $this->request(
            'get',
            $this->baseUrl . '/get-sppb-bc23',
            [
                'kodeGudang' => 'INTI',
                'noSppb' => $request->no_dok,
                'tglSppb' => Carbon::parse($request->tgl_dok)->format('d-m-Y'),
                'npwpImp' => $request->npwp
            ]
        )->json();  

        if ($response['code'] !== 200) {
            return response()->json([
                'success' => false,
                'message' => $response['detail'] ?? 'Terjadi kesalahan'
            ]);
        }   

        if (empty($response['data'])) {
            return response()->json([
                'success' => false,
                'message' => $response['detail'] ?? 'Data tidak ditemukan',
                'data' => [],
            ]);
        }   

        $sppbList = $response['data']['sppb'] ?? [];    

        if (empty($sppbList)) {
            return response()->json([
                'success' => false,
                'message' => $response['detail'] ?? 'Data SPPB BC23 tidak ditemukan',
                'data' => [],
            ]);
        }   

        $hasil = [
            'total' => 0,
            'berhasil' => 0,
            'dilewati' => 0,
            'cfs_berhasil' => 0,
            'cfs_gagal' => 0,
            'detail' => [],
        ];  

        foreach ($sppbList as $sppbData) {
            $hasil['total']++;  

            $header = $sppbData['header'] ?? [];
            $detil = $sppbData['detil'] ?? [];  

            try {
                if (empty($header)) {
                    $hasil['dilewati']++;   

                    $hasil['detail'][] = [
                        'status' => 'skip',
                        'message' => 'Header BC23 tidak ditemukan',
                    ];  

                    continue;
                }   

                $oldBC23 = BC23::where('car', $header['car'] ?? null)->first(); 

                if ($oldBC23) {
                    $hasil['dilewati']++;   

                    $hasil['detail'][] = [
                        'status' => 'skip',
                        'car' => $header['car'] ?? null,
                        'no_sppb' => $header['nomorSppb'] ?? null,
                        'message' => 'BC23 sudah ada',
                    ];  

                    continue;
                }   

                /*
                 * ==========================================================
                 * DATA DETAIL UNTUK CFS
                 * ==========================================================
                 */ 

                $kontainer = $detil['kontainer'][0] ?? null;
                $kemasan = $detil['kemasan'][0] ?? null;    

                $dataCfs = [
                    'sppb' => [
                        'detil' => [
                            'kemasan' => $kemasan ?? (object) [],
                            'kontainer' => $kontainer ?? (object) [],
                        ],
                        'header' => [
                            'tanggalBc11' => $header['tanggalBc11'] ?? null,
                            'tanggalBlAwb' => $header['tanggalBlAwb'] ?? null,
                            'gudang' => $header['gudang'] ?? null,
                            'statusJalur' => $header['statusJalur'] ?? null,
                            'nomorBc11' => $header['nomorBc11'] ?? null,
                            'nomorMasterBlAwb' => $header['nomorMasterBlAwb'] ?? null,
                            'namaPpjk' => $header['namaPpjk'] ?? null,
                            'alamatImp' => $header['alamatImp'] ?? null,
                            'car' => $header['car'] ?? null,
                            'namaAngkut' => $header['namaAngkut'] ?? null,
                            'nomorBlAwb' => $header['nomorBlAwb'] ?? null,
                            'tanggalPib' => $header['tanggalPib'] ?? null,
                            'kodeKantorBongkar' => $header['kodeKantorBongkar'] ?? null,
                            'npwpPpjk' => $header['npwpPpjk'] ?? null,
                            'nomorPosBc11' => $header['nomorPosBc11'] ?? null,
                            'tanggalMasterBlAwb' => $header['tanggalMasterBlAwb'] ?? null,
                            'nomorVoyFlight' => $header['nomorVoyFlight'] ?? null,
                            'namaImp' => $header['namaImp'] ?? null,
                            'netto' => $header['netto'] ?? null,
                            'idHeader' => $header['idHeader'] ?? null,
                            'tanggalSppb' => $header['tanggalSppb'] ?? null,
                            'kodeKantorPengawas' => $header['kodeKantorPengawas'] ?? null,
                            'alamatPpjk' => $header['alamatPpjk'] ?? null,
                            'nomorSppb' => $header['nomorSppb'] ?? null,
                            'bruto' => $header['bruto'] ?? null,
                            'nomorPib' => $header['nomorPib'] ?? null,
                            'npwpImp' => $header['npwpImp'] ?? null,
                            'jumlahKontainer' => $header['jumlahKontainer'] ?? 0,
                        ],
                    ],
                ];  

                /*
                 * ==========================================================
                 * KIRIM KE CFS CENTER BC23
                 * ==========================================================
                 */ 

                $cfsSuccess = false;
                $cfsResponse = null;
                $cfsStatus = null;  

                try {
                    $responseCFS = Http::timeout(60)
                        ->withBasicAuth('1MUT', '1MUT')
                        ->withoutVerifying()
                        ->withHeaders([
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ])
                        ->post(
                            'https://pelindo-cfscenter.com/index.php/apijson/receivepermit_23',
                            $dataCfs
                        );  

                    $cfsSuccess = $responseCFS->successful();
                    $cfsStatus = $responseCFS->status();
                    $cfsResponse = $responseCFS->json() ?? $responseCFS->body();    

                    if ($cfsSuccess) {
                        $hasil['cfs_berhasil']++;
                    } else {
                        $hasil['cfs_gagal']++;
                    }
                } catch (\Throwable $e) {
                    $cfsSuccess = false;
                    $cfsResponse = [
                        'message' => $e->getMessage()
                    ];  

                    $hasil['cfs_gagal']++;
                }   

                /*
                 * ==========================================================
                 * SIMPAN DATABASE
                 * CFS GAGAL TETAP LANJUT
                 * ==========================================================
                 */ 

                DB::transaction(function () use ($sppbData, $header, $detil) {  

                    $bc23 = BC23::create([
                        'car' => $header['car'] ?? null,
                        'no_sppb' => $header['nomorSppb'] ?? null,
                        'tgl_sppb' => $header['tanggalSppb'] ?? null,
                        'nojoborder' => null,
                        'kd_kantor_pengawas' => $header['kodeKantorPengawas'] ?? null,
                        'kd_kantor_bongkar' => $header['kodeKantorBongkar'] ?? null,
                        'no_pib' => $header['nomorPib'] ?? null,
                        'tgl_pib' => $header['tanggalPib'] ?? null,
                        'nama_imp' => $header['namaImp'] ?? null,
                        'npwp_imp' => $header['npwpImp'] ?? null,
                        'alamat_imp' => $header['alamatImp'] ?? null,
                        'npwp_ppjk' => $header['npwpPpjk'] ?? null,
                        'nama_ppjk' => $header['namaPpjk'] ?? null,
                        'alamat_ppjk' => $header['alamatPpjk'] ?? null,
                        'nm_angkut' => $header['namaAngkut'] ?? null,
                        'no_voy_flight' => $header['nomorVoyFlight'] ?? null,
                        'bruto' => $header['bruto'] ?? null,
                        'netto' => $header['netto'] ?? null,
                        'gudang' => $header['gudang'] ?? null,
                        'status_jalur' => $header['statusJalur'] ?? null,
                        'jml_cont' => $header['jumlahKontainer'] ?? 0,
                        'no_bc11' => $header['nomorBc11'] ?? null,
                        'tgl_bc11' => $header['tanggalBc11'] ?? null,
                        'no_pos_bc11' => $header['nomorPosBc11'] ?? null,
                        'no_bl_awb' => $header['nomorBlAwb'] ?? null,
                        'tgl_bl_awb' => $header['tanggalBlAwb'] ?? null,
                        'no_master_bl_awb' => $header['nomorMasterBlAwb'] ?? null,
                        'tgl_master_bl_awb' => $header['tanggalMasterBlAwb'] ?? null,
                        'tgl_upload' => Carbon::today()->format('Y-m-d'),
                        'jam_upload' => Carbon::now()->format('H:i:s'),
                    ]); 

                    $tglSppb = !empty($header['tanggalSppb'])
                        ? Carbon::createFromFormat('d-m-Y', $header['tanggalSppb'])->format('Y-m-d')
                        : null; 

                    foreach ($detil['kontainer'] ?? [] as $detailCont) {
                        $nomorContainer = $detailCont['nomorKontainer']
                            ?? $detailCont['noCont']
                            ?? $detailCont['no_cont']
                            ?? null;    

                        $size = $detailCont['size']
                            ?? $detailCont['ukuranKontainer']
                            ?? null;    

                        $jnsMuat = $detailCont['jenisMuat']
                            ?? $detailCont['jnsMuat']
                            ?? null;    

                        BC23Cont::create([
                            'sppb23_id' => $bc23->id,
                            'car' => $detailCont['car'] ?? null,
                            'no_cont' => $nomorContainer,
                            'size' => $size,
                            'jns_muat' => $jnsMuat,
                        ]); 

                        if (($bc23->jml_cont ?? 0) > 0) {
                            $contF = ContF::whereNull('tglkeluar')
                                ->where('nocontainer', $nomorContainer)
                                ->where('size', $size)
                                ->first();  

                            if ($contF) {
                                $alasanSize = $contF->size != $size
                                    ? 'Ukuran Fisik Size Berbeda'
                                    : null; 

                                $alasanFinal = implode(', ', array_filter([
                                    'Bukan Dokumen SPPB',
                                    $alasanSize
                                ]));    

                                $cust = Customer::where('name', $bc23->nama_imp)->first();  

                                if ($cust) {
                                    $cust->update([
                                        'name' => $bc23->nama_imp,
                                        'npwp' => $bc23->npwp_imp,
                                        'alamat' => $bc23->alamat_imp,
                                    ]);
                                } elseif (!empty($bc23->nama_imp)) {
                                    $cust = Customer::create([
                                        'name' => $bc23->nama_imp,
                                        'npwp' => $bc23->npwp_imp,
                                        'alamat' => $bc23->alamat_imp,
                                    ]);
                                }   

                                $flagNoBl = !empty(trim($bc23->no_bl_awb ?? ''))
                                    ? $bc23->no_bl_awb
                                    : (!empty(trim($bc23->no_master_bl_awb ?? ''))
                                        ? $bc23->no_master_bl_awb
                                        : null);    

                                $contF->update([
                                    'kd_dok_inout' => 2,
                                    'no_dok' => $bc23->no_sppb,
                                    'tgl_dok' => $tglSppb,
                                    'status_bc' => 'HOLD',
                                    'alasan_hold' => $alasanFinal,
                                    'cust_id' => $cust?->id,
                                    'nobl' => $flagNoBl,
                                ]);
                            }
                        }
                    }   

                    foreach ($detil['kemasan'] ?? [] as $detailKMS) {
                        $bcKMS = BC23Kms::create([
                            'sppb23_id' => $bc23->id,
                            'car' => $detailKMS['car'] ?? null,
                            'jns_kms' => $detailKMS['jenisKemasan'] ?? null,
                            'merk_kms' => $detailKMS['merkKemasan']
                                ?? $detailKMS['merkKms']
                                ?? null,
                            'jml_kms' => $detailKMS['jumlahKemasan'] ?? null,
                        ]); 

                        if (($bc23->jml_cont ?? 0) == 0) {
                            $manifest = Manifest::where('nohbl', $bc23->no_bl_awb)
                                ->whereNull('tglbuangmty')
                                ->first();  

                            if ($manifest) {
                                $cust = Customer::where('name', $bc23->nama_imp)->first();  

                                if ($cust) {
                                    $cust->update([
                                        'name' => $bc23->nama_imp,
                                        'npwp' => $bc23->npwp_imp,
                                        'alamat' => $bc23->alamat_imp,
                                    ]);
                                } elseif (!empty($bc23->nama_imp)) {
                                    $cust = Customer::create([
                                        'name' => $bc23->nama_imp,
                                        'npwp' => $bc23->npwp_imp,
                                        'alamat' => $bc23->alamat_imp,
                                    ]);
                                }   

                                $alasanKemas = ($manifest->packing && $manifest->packing->code != $bcKMS->jns_kms)
                                    ? 'Jenis Kemas Berbeda'
                                    : null; 

                                $alasanJml = $manifest->quantity != $bcKMS->jml_kms
                                    ? 'Quantity Berbeda'
                                    : null; 

                                $alasanQty = $manifest->final_qty != $manifest->quantity
                                    ? 'Jumlah QTY Fisik Berbeda'
                                    : null; 

                                $alasanFinal = implode(', ', array_filter([
                                    'Bukan Dokumen SPPB',
                                    $alasanKemas,
                                    $alasanJml,
                                    $alasanQty,
                                ]));    

                                $manifest->update([
                                    'kd_dok_inout' => 2,
                                    'no_dok' => $bc23->no_sppb,
                                    'tgl_dok' => $tglSppb,
                                    'status_bc' => 'HOLD',
                                    'cust_id' => $cust?->id,
                                    'alasan_hold' => $alasanFinal,
                                ]);
                            }
                        }
                    }
                }); 

                $hasil['berhasil']++;   

                $hasil['detail'][] = [
                    'status' => 'success',
                    'car' => $header['car'] ?? null,
                    'no_sppb' => $header['nomorSppb'] ?? null,
                    'cfs_success' => $cfsSuccess,
                    'cfs_status' => $cfsStatus,
                    'cfs_response' => $cfsResponse,
                ];  

            } catch (\Throwable $th) {
                $hasil['detail'][] = [
                    'status' => 'error',
                    'car' => $header['car'] ?? null,
                    'no_sppb' => $header['nomorSppb'] ?? null,
                    'message' => $th->getMessage(),
                ];
            }
        }   

        return response()->json([
            'success' => true,
            'message' => 'Proses BC23 selesai',
            'summary' => [
                'total' => $hasil['total'],
                'berhasil' => $hasil['berhasil'],
                'dilewati' => $hasil['dilewati'],
                'cfs_berhasil' => $hasil['cfs_berhasil'],
                'cfs_gagal' => $hasil['cfs_gagal'],
            ],
            'detail' => $hasil['detail'],
        ]);
    }

    // Pabean
    public function pabeanGet()
    {
        $response = $this->request(
            'get',
            $this->baseUrl . '/get-dokumen-pabean-permit',
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
                DB::transaction(function() use($response){
                    foreach ($response['data'] as $item) {
                        $dokumen = $item['dokumenPabean'] ?? [];
                        $header = $dokumen['header'] ?? [];
                        $detil = $dokumen['detil'] ?? [];                  
                        if (empty($header['car'])) {
                            continue;
                        }                  
                        $oldPabean = Pabean::where('car', $header['car'])->first();                
                        if ($oldPabean) {
                            continue;
                        }                  
                        $tglDok = !empty($header['tanggalDokumenInOut'])
                            ? Carbon::createFromFormat('d-m-Y', $header['tanggalDokumenInOut'])->format('Y-m-d')
                            : null;                
                        $tglDaftar = !empty($header['tanggalDaftar'])
                            ? Carbon::createFromFormat('d-m-Y', $header['tanggalDaftar'])->format('Y-m-d')
                            : null;                
                        $tglBc11 = !empty($header['tanggalBc11'])
                            ? Carbon::createFromFormat('d-m-Y', $header['tanggalBc11'])->format('Y-m-d')
                            : null;                
                        $tglBlAwb = !empty($header['tanggalBlAWb'])
                            ? Carbon::createFromFormat('d-m-Y', $header['tanggalBlAWb'])->format('Y-m-d')
                            : null;                
                        $tglMasterBlAwb = !empty($header['tanggalMasterBlAwb'])
                            ? Carbon::createFromFormat('d-m-Y', $header['tanggalMasterBlAwb'])->format('Y-m-d')
                            : null;                
                        $pabean = Pabean::create([
                            'kd_dok_inout' => $header['kodeDokumenInOut'] ?? null,
                            'car' => $header['car'],
                            'no_dok_inout' => $header['nomorDokumenInOut'] ?? null,
                            'tgl_dok_inout' => $tglDok,
                            'no_daftar' => $header['nomorDaftar'] ?? null,
                            'tgl_daftar' => $tglDaftar,
                            'kd_kantor' => $header['kodeKantor'] ?? null,
                            'kd_kantor_pengawas' => $header['kodeKantorPengawas'] ?? null,
                            'kd_kantor_bongkar' => $header['kodeKantorBongkar'] ?? null,
                            'npwp_imp' => $header['npwpImp'] ?? null,
                            'nm_imp' => $header['namaImp'] ?? null,
                            'al_imp' => $header['alamatImp'] ?? null,
                            'npwp_ppjk' => $header['npwpPpjk'] ?? null,
                            'nm_ppjk' => $header['namaPpjk'] ?? null,
                            'al_ppjk' => $header['alamatPpjk'] ?? null,
                            'nm_angkut' => $header['namaAngkut'] ?? null,
                            'no_voy_flight' => $header['nomorVoyFlight'] ?? null,
                            'brutto' => $header['bruto'] ?? null,
                            'netto' => $header['netto'] ?? null,
                            'gudang' => $header['gudang'] ?? null,
                            'status_jalur' => $header['statusJalur'] ?? null,
                            'jml_cont' => $header['jumlahKontainer'] ?? 0,
                            'no_bc11' => $header['nomorBc11'] ?? null,
                            'tgl_bc11' => $tglBc11,
                            'no_pos_bc11' => $header['nomorPosBc11'] ?? null,
                            'no_bl_awb' => $header['nomorBlAwb'] ?? null,
                            'tgl_bl_awb' => $tglBlAwb,
                            'no_master_bl_awb' => $header['nomorMasterBlAwb'] ?? null,
                            'tgl_master_bl_awb' => $tglMasterBlAwb,
                            'fl_segel' => $header['flagSegel'] ?? null,
                            'tgl_upload' => Carbon::today()->format('Y-m-d'),
                            'jam_upload' => Carbon::now()->format('H:i:s'),
                        ]);                
                        foreach (($detil['kontainer'] ?? []) as $detailCont) {
                            $pabeanCont = PabeanCont::create([
                                'pabean_id' => $pabean->id,
                                'car' => $detailCont['car'] ?? $header['car'],
                                'no_cont' => $detailCont['nomorKontainer'] ?? null,
                                'ukr_cont' => $detailCont['ukuranKontainer'] ?? null,
                                'size' => $detailCont['ukuranKontainer'] ?? null,
                                'jns_muat' => $detailCont['jenisMuat'] ?? null,
                            ]);                
                            if ($pabean->jml_cont > 0) {
                                $contF = ContF::whereNull('tglkeluar')
                                    ->where('nocontainer', $detailCont['nomorKontainer'] ?? null)
                                    ->first();                 
                                if ($contF) {
                                    $alasanSize = $contF->size != ($detailCont['ukuranKontainer'] ?? null)
                                        ? '& Ukuran Fisik Size Berbeda'
                                        : null;                
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
                                    if (!$cust && $pabean->nm_imp != null) {
                                        $newCust = Customer::create([
                                            'name' => $pabean->nm_imp,
                                            'npwp' => $pabean->npwp_imp,
                                            'alamat' => $pabean->al_imp,
                                        ]);
                                    }                  
                                    $flagTglBl = !empty(trim($pabean->tgl_bl_awb ?? ''))
                                        ? $pabean->tgl_bl_awb
                                        : (!empty(trim($pabean->tgl_master_bl_awb ?? ''))
                                            ? $pabean->tgl_master_bl_awb
                                            : null);                   
                                    $flagNoBl = !empty(trim($pabean->no_bl_awb ?? ''))
                                        ? $pabean->no_bl_awb
                                        : (!empty(trim($pabean->no_master_bl_awb ?? ''))
                                            ? $pabean->no_master_bl_awb
                                            : null);                   
                                    $contF->update([
                                        'kd_dok_inout' => $pabean->kd_dok_inout,
                                        'no_dok' => $pabean->no_dok_inout,
                                        'tgl_dok' => $tglDok,
                                        'status_bc' => 'HOLD',
                                        'alasan_hold' => $alasanFinal,
                                        'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
                                        'nobl' => $flagNoBl,
                                    ]);
                                }
                            }
                        }                  
                        foreach (($detil['kemasan'] ?? []) as $detailKms) {
                            PabeanKms::create([
                                'pabean_id' => $pabean->id,
                                'car' => $detailKms['car'] ?? $header['car'],
                                'jns_kms' => $detailKms['jenisKemasan'] ?? null,
                                'jml_kms' => $detailKms['jumlahKemasan'] ?? 0,
                            ]);                
                            if ($pabean->jml_cont == 0) {
                                $manifest = Manifest::where('nohbl', $pabean->no_bl_awb)
                                    ->whereNull('tglbuangmty')
                                    ->first();                 
                                if ($manifest) {
                                    $alasanCust = null;
                                    $statusBC = 'release';                 
                                    $cust = Customer::where('name', $pabean->nm_imp)->first();                 
                                    if ($cust) {
                                        $cust->update([
                                            'name' => $pabean->nm_imp,
                                            'npwp' => $pabean->npwp_imp,
                                            'alamat' => $pabean->al_imp,
                                        ]);
                                    }                  
                                    $newCust = null;                   
                                    if (!$cust && $pabean->nm_imp != null) {
                                        $newCust = Customer::create([
                                            'name' => $pabean->nm_imp,
                                            'npwp' => $pabean->npwp_imp,
                                            'alamat' => $pabean->al_imp,
                                        ]);
                                    }                  
                                    $alasanFinal = 'Bukan Dokume SPPB, ' . $alasanCust . ', ';                 
                                    $manifest->update([
                                        'kd_dok_inout' => $pabean->kd_dok_inout,
                                        'no_dok' => $pabean->no_dok_inout,
                                        'tgl_dok' => $tglDok,
                                        'status_bc' => $statusBC,
                                        'alasan_hold' => $alasanFinal,
                                        'cust_id' => $cust ? $cust->id : ($newCust ? $newCust->id : null),
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

    public function pabeanOnDemand(Request $request)
    {
        $response = $this->request(
            'get',
            $this->baseUrl . '/get-dokumen-pabean-ondemand',
            [
                'kodeDokumen' => $request->kd_dok,
                'nomorDokumen' => $request->no_dok,
                'tanggalDokumen' => carbon::parse($request->tgl_dok)->format('d-m-Y'),
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
                DB::transaction(function() use($response){
                    foreach ($response['data'] as $item) {
                       $header = $item['header'] ?? [];
                       $containers = $item['containers'] ?? [];
                       $kemasan = $item['kemasan'] ?? [];

                    //    if (empty($header['car'])) {
                    //        continue;
                    //    }

                       $oldPabean = Pabean::where('car', $header['car'])->first();

                    //    if ($oldPabean) {
                    //        continue;
                    //    }

                       $tglDok = !empty($header['tanggalDokumenInOut'])
                           ? Carbon::createFromFormat('d-m-Y', $header['tanggalDokumenInOut'])->format('Y-m-d')
                           : null;

                       $tglDaftar = !empty($header['tanggalDaftar'])
                           ? Carbon::createFromFormat('d-m-Y', $header['tanggalDaftar'])->format('Y-m-d')
                           : null;

                       $tglBc11 = !empty($header['tanggalBc11'])
                           ? Carbon::createFromFormat('d-m-Y', $header['tanggalBc11'])->format('Y-m-d')
                           : null;

                       $tglBlAwb = !empty($header['tanggalBlAwb'])
                           ? Carbon::createFromFormat('d-m-Y', $header['tanggalBlAwb'])->format('Y-m-d')
                           : null;

                       $tglMasterBlAwb = !empty($header['tanggalMasterBlAwb'])
                           ? Carbon::createFromFormat('d-m-Y', $header['tanggalMasterBlAwb'])->format('Y-m-d')
                           : null;

                       $pabean = Pabean::create([
                           'kd_dok_inout' => $header['kodeDokumenInOut'] ?? null,
                           'car' => $header['car'],
                           'no_dok_inout' => $header['nomorDokumenInOut'] ?? null,
                           'tgl_dok_inout' => $tglDok,
                           'no_daftar' => $header['nomorDaftar'] ?? null,
                           'tgl_daftar' => $tglDaftar,
                           'kd_kantor' => $header['kodeKantor'] ?? null,
                           'kd_kantor_pengawas' => $header['kodeKantorPengawas'] ?? null,
                           'kd_kantor_bongkar' => $header['kodeKantorBongkar'] ?? null,
                           'npwp_imp' => $header['npwpImp'] ?? null,
                           'nm_imp' => $header['namaImp'] ?? null,
                           'al_imp' => $header['alamatImp'] ?? null,
                           'npwp_ppjk' => $header['npwpPpjk'] ?? null,
                           'nm_ppjk' => $header['namaPpjk'] ?? null,
                           'al_ppjk' => $header['alamatPpjk'] ?? null,
                           'nm_angkut' => $header['namaAngkut'] ?? null,
                           'no_voy_flight' => $header['nomorVoyFlight'] ?? null,
                           'brutto' => $header['bruto'] ?? null,
                           'netto' => $header['netto'] ?? null,
                           'gudang' => $header['gudang'] ?? null,
                           'status_jalur' => $header['statusJalur'] ?? null,
                           'jml_cont' => $header['jumlahKontainer'] ?? 0,
                           'no_bc11' => $header['nomorBc11'] ?? null,
                           'tgl_bc11' => $tglBc11,
                           'no_pos_bc11' => $header['nomorPosBc11'] ?? null,
                           'no_bl_awb' => $header['nomorBlAwb'] ?? null,
                           'tgl_bl_awb' => $tglBlAwb,
                           'no_master_bl_awb' => $header['nomorMasterBlAwb'] ?? null,
                           'tgl_master_bl_awb' => $tglMasterBlAwb,
                           'fl_segel' => $header['flagSegel'] ?? null,
                           'tgl_upload' => Carbon::today()->format('Y-m-d'),
                           'jam_upload' => Carbon::now()->format('H:i:s'),
                       ]);

                       foreach ($containers as $detailCont) {
                           $nomorContainer = $detailCont['nomorContainer'] ?? null;
                           $ukuranContainer = $detailCont['ukuranContainer'] ?? null;

                           PabeanCont::create([
                               'pabean_id' => $pabean->id,
                               'car' => $detailCont['car'] ?? $header['car'],
                               'no_cont' => $nomorContainer,
                               'ukr_cont' => $ukuranContainer,
                               'size' => $ukuranContainer,
                               'jns_muat' => $detailCont['jenisMuat'] ?? null,
                           ]);

                           if ($pabean->jml_cont > 0 && $nomorContainer) {
                               $contF = ContF::whereNull('tglkeluar')
                                   ->where('nocontainer', $nomorContainer)
                                   ->first();

                               if ($contF) {
                                   $alasanSize = $contF->size != $ukuranContainer
                                       ? '& Ukuran Fisik Size Berbeda'
                                       : null;

                                   $alasanFinal = 'Bukan Dokumen SPPB. ' . $alasanSize;

                                   $cust = Customer::where('name', $pabean->nm_imp)->first();

                                   if ($cust) {
                                       $cust->update([
                                           'name' => $pabean->nm_imp,
                                           'npwp' => $pabean->npwp_imp,
                                           'alamat' => $pabean->al_imp,
                                       ]);
                                   } else {
                                       $cust = Customer::create([
                                           'name' => $pabean->nm_imp,
                                           'npwp' => $pabean->npwp_imp,
                                           'alamat' => $pabean->al_imp,
                                       ]);
                                   }

                                   $flagNoBl = !empty(trim($pabean->no_bl_awb ?? ''))
                                       ? $pabean->no_bl_awb
                                       : (!empty(trim($pabean->no_master_bl_awb ?? ''))
                                           ? $pabean->no_master_bl_awb
                                           : null);

                                   $contF->update([
                                       'kd_dok_inout' => $pabean->kd_dok_inout,
                                       'no_dok' => $pabean->no_dok_inout,
                                       'tgl_dok' => $tglDok,
                                       'status_bc' => 'HOLD',
                                       'alasan_hold' => $alasanFinal,
                                       'cust_id' => $cust->id,
                                       'nobl' => $flagNoBl,
                                   ]);
                               }
                           }
                       }

                       foreach ($kemasan as $detailKms) {
                           PabeanKms::create([
                               'pabean_id' => $pabean->id,
                               'car' => $detailKms['car'] ?? $header['car'],
                               'jns_kms' => $detailKms['jenisKemasan'] ?? null,
                               'jml_kms' => $detailKms['jumlahKemasan'] ?? 0,
                           ]);

                           if ($pabean->jml_cont == 0) {
                               $manifest = Manifest::where('nohbl', $pabean->no_bl_awb)
                                   ->whereNull('tglbuangmty')
                                   ->first();

                               if ($manifest) {
                                   $cust = Customer::where('name', $pabean->nm_imp)->first();

                                   if ($cust) {
                                       $cust->update([
                                           'name' => $pabean->nm_imp,
                                           'npwp' => $pabean->npwp_imp,
                                           'alamat' => $pabean->al_imp,
                                       ]);
                                   } else {
                                       $cust = Customer::create([
                                           'name' => $pabean->nm_imp,
                                           'npwp' => $pabean->npwp_imp,
                                           'alamat' => $pabean->al_imp,
                                       ]);
                                   }

                                   $manifest->update([
                                       'kd_dok_inout' => $pabean->kd_dok_inout,
                                       'no_dok' => $pabean->no_dok_inout,
                                       'tgl_dok' => $tglDok,
                                       'status_bc' => 'release',
                                       'alasan_hold' => 'Bukan Dokume SPPB, , ',
                                       'cust_id' => $cust->id,
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


    // manual

    public function manualGet()
    {
        $response = $this->request(
            'get',
            $this->baseUrl . '/get-dokumen-pabean-permit',
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
                DB::transaction(function() use($response){

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
    
    public function manualOnDemand()
    {
        $response = $this->request(
            'get',
            $this->baseUrl . '/get-dokumen-manual-ondemand',
            [
                'kodeDokumen' => $request->kd_dok,
                'nomorDokumen' => $request->no_dok,
                'tanggalDokumen' => carbon::parse($request->tgl_dok)->format('d-m-Y'),
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
                DB::transaction(function() use($response){

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


    // spjm
    public function spjmGet()
    {
        $response = $this->request(
            'get',
            $this->baseUrl . '/get-spjm',
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
                DB::transaction(function() use($response){
                    foreach ($response['data']['spjm'] as $item) {
                        $header = $item['header'] ?? [];
                        $kontainer = $item['kontainer'] ?? [];
                        $kemasan = $item['kemasan'] ?? [];

                        if (empty($header['nomorPib']) || empty($header['tanggalPib'])) {
                            continue;
                        }

                        $tglPib = Carbon::createFromFormat(
                            'd-m-Y',
                            $header['tanggalPib']
                        )->format('Y-m-d');

                        $oldSPJM = SPJM::where('no_pib', $header['nomorPib'])
                            ->where('tgl_pib', $tglPib)
                            ->first();

                        if ($oldSPJM) {
                            continue;
                        }

                        $tglSpjm = !empty($header['tanggalSpjm'])
                            ? Carbon::parse($header['tanggalSpjm'])->format('Y-m-d H:i:s')
                            : null;

                        $tglBc11 = !empty($header['tanggalBc11'])
                            ? Carbon::createFromFormat('d-m-Y', $header['tanggalBc11'])->format('Y-m-d')
                            : null;

                        $spjm = SPJM::create([
                            'car' => $header['car'] ?? null,
                            'kd_kantor' => $header['kodeKantor'] ?? null,
                            'tgl_pib' => $tglPib,
                            'no_pib' => $header['nomorPib'] ?? null,
                            'no_spjm' => $header['nomorPib'] ?? null,
                            'tgl_spjm' => $tglSpjm,
                            'npwp_imp' => $header['npwpImp'] ?? null,
                            'nama_imp' => $header['namaImp'] ?? null,
                            'npwp_ppjk' => $header['npwpPpjk'] ?? null,
                            'nama_ppjk' => $header['namaPpjk'] ?? null,
                            'gudang' => $header['gudang'] ?? null,
                            'jml_cont' => $header['jumlahKontainer'] ?? 0,
                            'no_bc11' => $header['nomorBc11'] ?? null,
                            'tgl_bc11' => $tglBc11,
                            'no_pos_bc11' => $header['nomorPosBc11'] ?? null,
                            'fl_karantina' => $header['flagKarantina'] ?? null,
                            'nm_angkut' => $header['namaAngkut'] ?? null,
                            'no_voy_flight' => $header['nomorVoyFlight'] ?? null,
                            'tgl_upload' => Carbon::today()->format('Y-m-d'),
                            'jam_upload' => Carbon::now()->format('H:i:s'),
                        ]);

                        foreach ($kontainer as $detailCont) {
                            $newCont = SPJMcont::create([
                                'spjm_id' => $spjm->id,
                                'car' => $detailCont['car'] ?? $header['car'],
                                'no_cont' => $detailCont['nomorKontainer'] ?? null,
                                'size' => $detailCont['kodeUkuranKontainer'] ?? null,
                            ]);

                            if ($newCont->no_cont) {
                                $containerTPS = ContF::whereNotNull('tglmasuk')
                                    ->whereNull('tglkeluar')
                                    ->where('nocontainer', $newCont->no_cont)
                                    ->first();

                                if ($containerTPS) {
                                    $containerTPS->update([
                                        'no_spjm' => $spjm->no_spjm,
                                        'tgl_spjm' => $tglSpjm
                                            ? Carbon::parse($tglSpjm)->format('Y-m-d')
                                            : null,
                                    ]);
                                }
                            }
                        }

                        foreach ($kemasan as $detailKms) {
                            SPJMkms::create([
                                'spjm_id' => $spjm->id,
                                'car' => $detailKms['car'] ?? $header['car'],
                                'jns_kms' => $detailKms['kodeJenisKemasan'] ?? null,
                                'merk_kms' => null,
                                'jml_kms' => $detailKms['jumlahKemasan'] ?? 0,
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

    public function spjmOnDemand(Request $request)
    {
        $response = $this->request(
            'get',
            $this->baseUrl . '/get-spjm-ondemand',
            [
                'nomorDaftar' => $request->no_dok,
                'tanggalDaftar' => carbon::parse($request->tgl_dok)->format('d-m-Y'),
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
                DB::transaction(function() use($response){
                    foreach ($response['data'] as $item) {
                    //    if (empty($item['nomorPib']) || empty($item['tanggalPib'])) {
                    //        continue;
                    //    }

                       $tglPib = Carbon::createFromFormat('d-m-Y', $item['tanggalPib'])->format('Y-m-d');

                       $oldSPJM = SPJM::where('no_pib', $item['nomorPib'])
                           ->where('tgl_pib', $tglPib)
                           ->first();

                    //    if ($oldSPJM) {
                    //        continue;
                    //    }

                       $tglSpjm = !empty($item['tanggalSpjm'])
                           ? Carbon::parse($item['tanggalSpjm'])->format('Y-m-d H:i:s')
                           : null;

                       $tglBc11 = !empty($item['tanggalBc11'])
                           ? Carbon::createFromFormat('d-m-Y', $item['tanggalBc11'])->format('Y-m-d')
                           : null;

                       $spjm = SPJM::create([
                           'car' => $item['car'] ?? null,
                           'kd_kantor' => $item['kodeKantor'] ?? null,
                           'tgl_pib' => $tglPib,
                           'no_pib' => $item['nomorPib'] ?? null,
                           'no_spjm' => $item['nomorPib'] ?? null,
                           'tgl_spjm' => $tglSpjm,
                           'npwp_imp' => $item['npwpImp'] ?? null,
                           'nama_imp' => $item['namaImp'] ?? null,
                           'npwp_ppjk' => $item['npwpPpjk'] ?? null,
                           'nama_ppjk' => $item['namaPpjk'] ?? null,
                           'gudang' => $item['kodeGudang'] ?? null,
                           'jml_cont' => $item['jumlahKontainer'] ?? 0,
                           'no_bc11' => $item['nomorBc11'] ?? null,
                           'tgl_bc11' => $tglBc11,
                           'no_pos_bc11' => $item['nomorPosBc11'] ?? null,
                           'fl_karantina' => $item['flagKarantina'] ? 'Y' : 'T',
                           'nm_angkut' => $item['namaAngkut'] ?? null,
                           'no_voy_flight' => $item['nomorVoyFlight'] ?? null,
                           'tgl_upload' => Carbon::today()->format('Y-m-d'),
                           'jam_upload' => Carbon::now()->format('H:i:s'),
                       ]);

                       foreach (($item['kontainer'] ?? []) as $detailCont) {
                           $newCont = SPJMcont::create([
                               'spjm_id' => $spjm->id,
                               'car' => $detailCont['car'] ?? $item['car'],
                               'no_cont' => $detailCont['nomorKontainer'] ?? null,
                               'size' => $detailCont['kodeUkuranKontainer'] ?? null,
                           ]);

                           if ($newCont->no_cont) {
                               $containerTPS = ContF::whereNotNull('tglmasuk')
                                   ->whereNull('tglkeluar')
                                   ->where('nocontainer', $newCont->no_cont)
                                   ->first();

                               if ($containerTPS) {
                                   $containerTPS->update([
                                       'no_spjm' => $spjm->no_spjm,
                                       'tgl_spjm' => $tglSpjm
                                           ? Carbon::parse($tglSpjm)->format('Y-m-d')
                                           : null,
                                   ]);
                               }
                           }
                       }

                       foreach (($item['kemasan'] ?? []) as $detailKms) {
                           SPJMkms::create([
                               'spjm_id' => $spjm->id,
                               'car' => $detailKms['car'] ?? $item['car'],
                               'jns_kms' => $detailKms['kodeJenisKemasan'] ?? null,
                               'merk_kms' => null,
                               'jml_kms' => $detailKms['jumlahKemasan'] ?? 0,
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
        $startDate = '2025-07-01';
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
                "refNumber"           => Str::random(18),
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
                "refNumber"           => Str::random(18),
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
        $startDate = '2025-07-01';
        $containersFCL = ContF::with(['job'])->whereNotNull('tglkeluar')->whereDate('tglmasuk', '>=', $startDate)->where('coarri_cesa_flag', 'Y')->where('codeco_cesa_flag', 'N')->take(20)->get();
        foreach ($containersFCL as $container) {
            $container->update([
                'codeco_cesa_flag' => 'P',
                'codeco_cesa_time' => Carbon::now(),
                'codeco_cesa_status' => 'PROCESSING',
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
                "refNumber"           => Str::random(18),
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
                "nomorDaftarPabean" => $container->job->noplp ?? null,
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
                'codeco_cesa_flag' => $status,
                'codeco_cesa_time' => Carbon::now(),
                'codeco_cesa_status' => $message,
            ]);
        }


        $containersLCL = Cont::with(['job'])->whereNotNull('tglkeluar')->whereDate('tglmasuk', '>=', $startDate)->where('coarri_cesa_flag', 'Y')->where('codeco_cesa_flag', 'N')->take(20)->get();
        foreach ($containersLCL as $container) {
            $container->update([
                'codeco_cesa_flag' => 'P',
                'codeco_cesa_time' => Carbon::now(),
                'codeco_cesa_status' => 'PROCESSING',
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
                "refNumber"           => Str::random(18),
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
                'codeco_cesa_flag' => $status,
                'codeco_cesa_time' => Carbon::now(),
                'codeco_cesa_status' => $message,
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
