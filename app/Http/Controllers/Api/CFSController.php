<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Manifest;
use App\Models\Container as cont;
use App\Models\TarifCFS as Tarif;
use App\Models\InvoiceCSF as Header;
use App\Models\invoiceCSFDetil as Detil;
use App\Models\BarcodeGate as Barcode;

use GuzzleHttp\Client;

class CFSController extends Controller
{
    protected $client;
    protected $token;

    public function __construct()
    {
        $this->now = Carbon::now()->format('Ymd');
        
        $this->token = '$C2FsdGVkX18WeMoBpJoh/Fklqv+HfHHjmT1pMz3sbJX6SHIJJvoDdImZEr+GQwDLWmXxVXJB4cp9iQuiESKK6A==';
        $this->client = new Client(); // Inisialisasi Guzzle Client
    }

    public function loadBillingDev(Request $request)
    {
        $header = $request->header();

        if (!isset($header['authorization'])) {
            return response()->json([
                'status' => false,
                'message' => 'Authorization header is missing',
            ]);
        }
        
        if ($header['authorization'][0] != $this->token) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Invalid Token',
            ]);
        }

        $rules = [
            'no_order'       => 'required',
            'jenis_billing'  => 'required',
            'jenis_bayar'    => 'required',
            'jenis_transaksi'=> 'required',
            'tgl_keluar'     => 'required|date_format:d-m-Y',
            'no_bl_awb'      => 'required',
            'no_cont'        => 'required',
            'consignee'      => 'required',
            'npwp_consignee' => 'required',
            'no_pol'         => 'required',
            'jns_kms'        => 'required',
            'merk_kms'       => 'required',
            'jml_kms'        => 'required|integer',
            'user'           => 'required',
            'warehouse'      => 'required',
        ];
        
        // Tambahkan aturan validasi jika jenis transaksi adalah 'P'
        if ($request->jenis_transaksi == 'P') {
            $rules['tgl_keluar_lama'] = 'required|date_format:d-m-Y';
        }
        
        $messages = [
            'no_order.required'       => 'Nomor order wajib diisi.',
            'jenis_billing.required'  => 'Jenis billing wajib diisi.',
            'jenis_bayar.required'    => 'Jenis bayar wajib diisi.',
            'jenis_transaksi.required'=> 'Jenis transaksi wajib diisi.',
            'tgl_keluar.required'     => 'Tanggal keluar wajib diisi.',
            'tgl_keluar.date_format'  => 'Format tanggal keluar harus d-m-Y.',
            'tgl_keluar_lama.required'=> 'Tanggal keluar lama wajib diisi.',
            'tgl_keluar_lama.date_format' => 'Format tanggal keluar lama harus d-m-Y.',
            'no_bl_awb.required'      => 'Nomor BL/AWB wajib diisi.',
            'no_cont.required'        => 'Nomor kontainer wajib diisi.',
            'consignee.required'      => 'Consignee wajib diisi.',
            'npwp_consignee.required' => 'NPWP Consignee wajib diisi.',
            'no_pol.required'         => 'Nomor polisi wajib diisi.',
            'jns_kms.required'        => 'Jenis kemasan wajib diisi.',
            'merk_kms.required'       => 'Merk kemasan wajib diisi.',
            'jml_kms.required'        => 'Jumlah kemasan wajib diisi.',
            'jml_kms.integer'         => 'Jumlah kemasan harus berupa angka.',
            'user.required'           => 'User wajib diisi.',
            'warehouse.required'      => 'Warehouse wajib diisi.',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(fn($error) => $error[0])->toArray();
            return response()->json([
                'status'  => false,
                'success' => false,
                'message' => implode(", ", $errors),
                'errors'  => $validator->errors(),
            ], 400);
        }

        try {
            //code...
            $manifest = Manifest::where('nohbl', $request->no_bl_awb)->whereHas('cont', function ($query) use ($request){
                $query->where('nocontainer', $request->no_cont);
            })->first();
    
            // $manifest = Manifest::where('nohbl', $request->no_bl_awb)->where('cont.nocontainer', $request->no_cont)->first();
    
            if ($manifest) {
                if ($manifest->customer->name != $request->consignee ) {
                    return response()->json([
                        'status' => false,
                        'success' => false,
                        'message' => 'Nama Consignee berbeda',
                    ]);
                }
    
                if ($manifest->customer->npwp != $request->npwp_consignee ) {
                    return response()->json([
                        'status' => false,
                        'success' => false,
                        'message' => 'NPWP Consignee berbeda',
                    ]);
                }
                if ($manifest->packing->code != $request->jns_kms ) {
                    return response()->json([
                        'status' => false,
                        'success' => false,
                        'message' => 'Jenis Kemasan Berbeda',
                    ]);
                }
    
                if ($manifest->quantity != $request->jml_kms ) {
                    return response()->json([
                        'status' => false,
                        'success' => false,
                        'message' => 'Jumlah Kemasan Berbeda',
                    ]);
                }
    
                if ($manifest->tglstripping == null) {
                    return response()->json([
                        'status' => false,
                        'success' => false,
                        'message' => 'Tgl Stripping Manifest Masih Kosong',
                    ]);
                }
    
                if ((Carbon::parse($request->tgl_keluar)->format('Y-m-d')) < $manifest->tglstripping) {
                    return response()->json([
                        'status' => false,
                        'success' => false,
                        'message' => 'Tgl Keluar Tidak Boleh Lebih Kecil dari Tanggal Stripping',
                    ]);
                }
                // $tarif = Tarif::get();
                
                $flagBehandle = ($manifest->status_behandle != null) ? 'Y' : 'N';
                $type = ($manifest->dg_label == 'Y') ? 'DG' : 'NORMAL';
                // $type = 'DG';
                $flagOH = ($manifest->weight >= 2500) ? 'Y' : 'N';
                // $flagOH = 'Y';
    
                $cbm = $manifest->meas ? max(2,ceil($manifest->meas)) : 2;
    
                // dd('Type : ' .$type, $manifest->weight, $manifest->meas, 'CBM : ' . $cbm, 'Flag OH : '.$flagOH, 'Flag Behandle : ' . $flagBehandle, 'Jenis Transaksi : ' . $request->jenis_transaksi);
    
                $total = 0;
                $tarif = [];
                $tglStripping = Carbon::parse($manifest->tglstripping);
                $tglKeluar = Carbon::parse($request->tgl_keluar);
    
                if ($request->jenis_transaksi != 'P') {
                    $interval = $tglStripping->diff($tglKeluar->addDays(1))->days;
                    
                    $massa1 = min(3, ($interval)); 
                    $massa2 = min(2, ($interval - 3));
                    $massa3 = max(0, ($interval - 5));
                  
                    // dd('Tgl Stripping : ' . $tglStripping, 'Tgl Keluar : ' . $tglKeluar, 'Jumlah Hari : ' . $interval, 'massa1 : ' . $massa1, 'massa2 : ' . $massa2, 'massa3 : ' . $massa3);
    
                    // RDM
                    $tarifRDM = ($type == 'NORMAL') ? Tarif::find(1) : Tarif::find(2);
                    $hargaRDM = ($tarifRDM->tarif_dasar * $cbm);
                    $rdm = [
                        "KODE_TARIF" => $tarifRDM->kode_bill,
                        "TARIF_DASAR" => $tarifRDM->tarif_dasar,
                        "QTY" => $cbm,
                        "HARI" => 0,
                        "NILAI" => $hargaRDM,
                        "SATUAN" => ($manifest->quantity).($manifest->packing->code ?? '')
    
                    ];
                     $total += $hargaRDM;
    
                    $tarif[] = $rdm;
    
                     if ($massa1 > 0) {
                        $tarifMassa1 = ($type == 'NORMAL') ? Tarif::find(4) : Tarif::find(7);
                        $hargaMassa1 = ($tarifMassa1->tarif_dasar * $massa1 * $cbm);
                        $storage1 = [
                            "KODE_TARIF" => $tarifMassa1->kode_bill,
                            "TARIF_DASAR" => $tarifMassa1->tarif_dasar,
                            "QTY" => $cbm,
                            "HARI" => $massa1,
                            "NILAI" => $hargaMassa1,
                            "SATUAN" => ($manifest->quantity).($manifest->packing->code ?? '')
                        ] ;
    
                        $total += $hargaMassa1;
                        $tarif[] = $storage1;
                     }
    
                     if ($massa2 > 0) {
                        $tarifMassa2 = ($type == 'NORMAL') ? Tarif::find(5) : Tarif::find(9);
                        $hargaMassa2 = ($tarifMassa2->tarif_dasar * $massa2 * $cbm);
                        $storage2 = [
                            "KODE_TARIF" => $tarifMassa2->kode_bill,
                            "TARIF_DASAR" => $tarifMassa2->tarif_dasar,
                            "QTY" => $cbm,
                            "HARI" => $massa2,
                            "NILAI" => $hargaMassa2,
                            "SATUAN" => ($manifest->quantity).($manifest->packing->code ?? '')
                        ];
    
                        $total += $hargaMassa2;
                        $tarif[] = $storage2;
                     }
    
                     if ($massa3 > 0) {
                        $tarifMassa3 = ($type == 'NORMAL') ? Tarif::find(6) : Tarif::find(11);
                        $hargaMassa3 = ($tarifMassa3->tarif_dasar * $massa3 * $cbm);
                        $storage3 = [
                            "KODE_TARIF" => $tarifMassa3->kode_bill,
                            "TARIF_DASAR" => $tarifMassa3->tarif_dasar,
                            "QTY" => $cbm,
                            "HARI" => $massa3,
                            "NILAI" => $hargaMassa3,
                            "SATUAN" => ($manifest->quantity).($manifest->packing->code ?? '')
                        ];
    
                        $total += $hargaMassa3;
                        $tarif[] = $storage3;
                     }
    
                     if ($type == 'DG') {
                        $tarifSurchargeDG = Tarif::find(14);
                        $hargaSurchargeDG = ($tarifSurchargeDG->tarif_dasar);
                        $surchargeDG = [
                            "KODE_TARIF" => $tarifSurchargeDG->kode_bill,
                            "TARIF_DASAR" => $tarifSurchargeDG->tarif_dasar,
                            "QTY" => 1,
                            "HARI" => 0,
                            "NILAI" => $hargaSurchargeDG,
                            "SATUAN" => ($manifest->quantity).($manifest->packing->code ?? '')
                        ];
                        $total += $hargaSurchargeDG;
                        $tarif[] = $surchargeDG;
                     }
    
                     if ($flagOH == 'Y') {
                        $tarifSurchargeOH = Tarif::find(15);
                        $hargaSurchargeOH = ($tarifSurchargeOH->tarif_dasar);
                        $surchargeOH = [
                            "KODE_TARIF" => $tarifSurchargeOH->kode_bill,
                            "TARIF_DASAR" => $tarifSurchargeOH->tarif_dasar,
                            "QTY" => 1,
                            "HARI" => 0,
                            "NILAI" => $hargaSurchargeOH,
                            "SATUAN" => ($manifest->quantity).($manifest->packing->code ?? '')
                        ]; 
                        $total += $hargaSurchargeOH;
                        $tarif[] = $surchargeOH;
                     }
    
                    //  dd('Jumlah Hari : ' . $interval, $total, $tarif);
                    
                }else {
                    $tglKeluarLama = ($request->tgl_keluar_lama) ? Carbon::parse($request->tgl_keluar_lama) : null;
                    if ($tglKeluarLama >= $tglKeluar) {
                        return response()->json([
                            'status' => false,
                            'success' => false,
                            'message' => 'Tgl Keluar Harus Lebih Besar dari Tgl Keluar Lama',
                        ]);
                    }
    
                    $interval = $tglKeluarLama->diff($tglKeluar)->days;
                    $intervalAwal = $tglStripping->diff($tglKeluarLama->addDays(1))->days;
                    $massa1 = 0; $massa2 = 0; $massa3 = 0;
                    if ($intervalAwal > 5) {
                        $massa3 = $intervalAwal;
                    }else {
                        $OldMassa1 = $intervalAwal;
                        $massa1 = max(0, min(3, (3 - $OldMassa1)));
                        $massa2 = (($interval - ($OldMassa1 + $massa1)) > 0) ?  max(0, min(2, (5 - ($OldMassa1 + $massa1)))) : 0;
                        $massa3 = (($interval - ($massa1 + $massa2)) > 0) ? ($interval - ($massa1 + $massa2)) : 0;
                        
                    }
                    // dd('tglStripping: ' . $tglStripping , 'IntervalAwal: ' . $intervalAwal, 'interval : ' . $interval, 'Massa1 : ' . $massa1, 'oldMassa1 : ' . $OldMassa1, 'Massa2 : ' . $massa2, 'massa 3 : ' . $massa3);
                    if ($massa1 > 0) {
                        $tarifMassa1 = ($type == 'NORMAL') ? Tarif::find(4) : Tarif::find(7);
                        $hargaMassa1 = ($tarifMassa1->tarif_dasar * $cbm * $massa1);
                        $storage1 = [
                            "KODE_TARIF" => $tarifMassa1->kode_bill,
                            "TARIF_DASAR" => $tarifMassa1->tarif_dasar,
                            "QTY" => $cbm,
                            "HARI" => $massa1,
                            "NILAI" => $hargaMassa1,
                            "SATUAN" => ($manifest->quantity).($manifest->packing->code ?? '')
                        ];
                        $total += $hargaMassa1;
                        $tarif [] = $storage1;
                    }
    
                    if ($massa2 > 0) {
                        $tarifMassa2 = ($type == 'NORMAL') ? Tarif::find(5) : Tarif::find(9);
                        $hargaMassa2 = ($tarifMassa2->tarif_dasar * $massa2 * $cbm);
                        $storage2 = [
                            "KODE_TARIF" => $tarifMassa2->kode_bill,
                            "TARIF_DASAR" => $tarifMassa2->tarif_dasar,
                            "QTY" => $cbm,
                            "HARI" => $massa2,
                            "NILAI" => $hargaMassa2,
                            "SATUAN" => ($manifest->quantity).($manifest->packing->code ?? '')
                        ];
    
                        $total += $hargaMassa2;
                        $tarif[] = $storage2;
                     }
    
                     if ($massa3 > 0) {
                        $tarifMassa3 = ($type == 'NORMAL') ? Tarif::find(6) : Tarif::find(11);
                        $hargaMassa3 = ($tarifMassa3->tarif_dasar * $massa3 * $cbm);
                        $storage3 = [
                            "KODE_TARIF" => $tarifMassa3->kode_bill,
                            "TARIF_DASAR" => $tarifMassa3->tarif_dasar,
                            "QTY" => $cbm,
                            "HARI" => $massa3,
                            "NILAI" => $hargaMassa3,
                            "SATUAN" => ($manifest->quantity).($manifest->packing->code ?? '')
                        ];
    
                        $total += $hargaMassa3;
                        $tarif[] = $storage3;
                     }
                }
    
                 //  Admin
                 $tarifAdmin = Tarif::find(13);
                 $hargaAdmin = ($tarifAdmin->tarif_dasar);
                 $admin = [
                     "KODE_TARIF" => $tarifAdmin->kode_bill,
                     "TARIF_DASAR" => $tarifAdmin->tarif_dasar,
                     "QTY" => 1,
                     "HARI" => 0,
                     "NILAI" => $hargaAdmin,
                     "SATUAN" => ($manifest->quantity).($manifest->packing->code ?? '')
                 ];
                 $total += $hargaAdmin;
                 $tarif[] = $admin;
    
                // dd($request->all());
    
                $ppn = ($total * 11) / 100;
                $grandTotal = $total + $ppn;
    
                $data['HEADER'] = [
                    "NO_ORDER" => $request->no_order,
                    "JENIS_BILLING" => $request->jenis_billing,
                    "JENIS_BAYAR" => $request->jenis_bayar,
                    "SUB_TOTAL" => $total,
                    "PPN" => $ppn,
                    "TOTAL" => $grandTotal
                ];
    
                $data['DETAIL'] = [
                    "WEIGHT" => $manifest->weight,
                    "MEASURE" => $manifest->meas,
                    "JNS_KMS" => $manifest->packing->code ?? '',
                    "MERK_KMS" => $manifest->marking ?? '',
                    "JML_KMS" => $manifest->quantity ?? 0,
                    "TARIF" => $tarif
                ];

                $OldOrder = Header::where('no_order', $data['HEADER']['NO_ORDER'])->first();
                if ($OldOrder) {
                    return response()->json([
                        'status' => false,
                        'success' => false,
                        'message' => 'No Order sudah pernah di ajukan pada : ' . $OldOrder->created_at . '. Harapn Cancel terlebih dahulu',
                    ]);
                }
                
                DB::transaction(function() use ($data, $manifest, $tglKeluar, $request){
                    // dd($request->jenis_transaksi);
                    $header = $data['HEADER'];
                    $detil = $data['DETAIL'];
                    $tarifs = $data['DETAIL']['TARIF'];

                    // dd($header, $detil, $tarifs, $tglKeluar, $manifest->id);

                    $headerInvoice =  Header::create([
                        'manifest_id' => $manifest->id,
                        'no_order' => $header['NO_ORDER'],
                        'jenis_billing' => $header['JENIS_BILLING'],
                        'jenis_bayar' => $header['JENIS_BAYAR'],
                        'jenis_transaksi' => $request->jenis_transaksi,
                        'subtotal' => $header['SUB_TOTAL'],
                        'ppn' => $header['PPN'],
                        'total' => $header['TOTAL'],
                        'weight' => $detil['WEIGHT'],
                        'measure' => $detil['MEASURE'],
                        'jns_kms' => $detil['JNS_KMS'],
                        'merk_kms' => $detil['MERK_KMS'],
                        'jml_kms' => $detil['JML_KMS'],
                        'status' => 'N',
                        'rencana_keluar' => $tglKeluar,
                        'created_at' => Carbon::now(),
                        'no_bl_awb' => $request->no_bl_awb,
                        'consignee' => $request->consignee,
                        'npwp_consignee' => $request->npwp_consignee,
                    ]);

                    foreach ($tarifs as $tarif) {
                        $detilInvoice = Detil::create([
                            'header_id' => $headerInvoice->id,
                            'kode_tarif' => $tarif['KODE_TARIF'],
                            'tarif_dasar' => $tarif['TARIF_DASAR'],
                            'qty' => $tarif['QTY'],
                            'hari' => $tarif['HARI'],
                            'nilai' => $tarif['NILAI'],
                            'satuan' => $tarif['SATUAN'],
                        ]);
                    }

                });
               
                // dd($data);
    
                return response()->json([
                    'status' => true,
                    'success' => true,
                    'message' => 'Data ditemukan',
                    'data' => $data,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Something Wrong' . $th->getMessage(),
            ]);
        }

    }

    public function CancelProforma(Request $request)
    {
        $rules = [
            'no_order'       => 'required',
            'no_bl_awb'      => 'required',
        ];
        
        // Tambahkan aturan validasi jika jenis transaksi adalah 'P'
        if ($request->jenis_transaksi == 'P') {
            $rules['tgl_keluar_lama'] = 'required|date_format:d-m-Y';
        }
        
        $messages = [
            'no_order.required'       => 'Nomor order wajib diisi.',
            'no_bl_awb.required'      => 'Nomor BL/AWB wajib diisi.',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(fn($error) => $error[0])->toArray();
            return response()->json([
                'status'  => false,
                'success' => false,
                'message' => implode(", ", $errors),
                'errors'  => $validator->errors(),
            ], 400);
        }
        $header = $request->header();

        if (!isset($header['authorization'])) {
            return response()->json([
                'status' => false,
                'message' => 'Authorization header is missing',
            ]);
        }
        
        if ($header['authorization'][0] != $this->token) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Invalid Token',
            ]);
        }

        // dd($request->all());
        $header = Header::where('no_order', $request->no_order)->where('no_bl_awb', $request->no_bl_awb)->first();
        if (!$header) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Data tidak ditemukan!!',
            ]);
        }


        if ($header->status == 'Y') {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Tidak Dapat dicancel, Invoice sudah lunas !!',
            ]);
        }

        try {
            $header->updateOrFail([
                'status' => 'C',
                'cancel_at' => ($header->cancel_at) ? $header->cancel_at : Carbon::now(),
            ]);
            return response()->json([
                'status' => true,
                'success' => true,
                'message' => 'Data ditemukan',
                'data' => [
                    'status' => $header->status,
                    'cancel_at' => $header->cancel_at,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Opss Some thing wrong : ' . $th->getMessage(),
            ]);
        }
    }

    public function FlagLunas(Request $request)
    {
        $header = $request->header();

        if (!isset($header['authorization'])) {
            return response()->json([
                'status' => false,
                'message' => 'Authorization header is missing',
            ]);
        }
        
        if ($header['authorization'][0] != $this->token) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Invalid Token',
            ]);
        }

        $rules = [
            'no_order'       => 'required',
            'no_bl_awb'      => 'required',
        ];
        
        // Tambahkan aturan validasi jika jenis transaksi adalah 'P'
        if ($request->jenis_transaksi == 'P') {
            $rules['tgl_keluar_lama'] = 'required|date_format:d-m-Y';
        }
        
        $messages = [
            'no_order.required'       => 'Nomor order wajib diisi.',
            'no_bl_awb.required'      => 'Nomor BL/AWB wajib diisi.',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(fn($error) => $error[0])->toArray();
            return response()->json([
                'status'  => false,
                'success' => false,
                'message' => implode(", ", $errors),
                'errors'  => $validator->errors(),
            ], 400);
        }

        $header = Header::where('no_order', $request->no_order)->where('no_bl_awb', $request->no_bl_awb)->first();
        if (!$header) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Data tidak ditemukan!!',
            ]);
        }


        if ($header->status == 'C') {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Tidak Dapat lunas, Status Invoice sudah cancel !!',
            ]);
        }
        
        try {
            $header->updateOrFail([
                'status' => 'Y',
                'lunas_at' => ($header->lunas_at) ? $header->lunas_at : Carbon::now(),
            ]);
            $manifest = Manifest::findOrFail($header->manifest_id);
            $manifest->update([
                'active_to' => ($header->rencana_keluar > $manifest->active_to) ? $header->rencana_keluar : $manifest->active_to,
            ]);
            return response()->json([
                'status' => true,
                'success' => true,
                'message' => 'Data ditemukan',
                'data' => [
                    'status' => 'Y',
                    'lunas_at' => $header->lunas_at,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Opss Something wrong : ' . $th->getMessage(),
            ]);
        }
        
    }

    public function gatePass(Request $request)
    {
        $header = $request->header();

        if (!isset($header['authorization'])) {
            return response()->json([
                'status' => false,
                'message' => 'Authorization header is missing',
            ]);
        }
        
        if ($header['authorization'][0] != $this->token) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Invalid Token',
            ]);
        }

        $rules = [
            'no_order'       => 'required',
            'no_bl_awb'      => 'required',
        ];
        
        // Tambahkan aturan validasi jika jenis transaksi adalah 'P'
        if ($request->jenis_transaksi == 'P') {
            $rules['tgl_keluar_lama'] = 'required|date_format:d-m-Y';
        }
        
        $messages = [
            'no_order.required'       => 'Nomor order wajib diisi.',
            'no_bl_awb.required'      => 'Nomor BL/AWB wajib diisi.',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(fn($error) => $error[0])->toArray();
            return response()->json([
                'status'  => false,
                'success' => false,
                'message' => implode(", ", $errors),
                'errors'  => $validator->errors(),
            ], 400);
        }

        $header = Header::where('no_order', $request->no_order)->where('no_bl_awb', $request->no_bl_awb)->first();
        if (!$header) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Data tidak ditemukan!!',
            ]);
        }

        try {
            $manifest = Manifest::findOrFail($header->manifest_id);
            if (!$manifest) {
                return response()->json([
                    'status' => false,
                    'success' => false,
                    'message' => 'Data tidak dektemukan',
                ]);
            }
            $barcode = Barcode::where('ref_id', $manifest->id)->where('ref_type', '=', 'Manifest')->where('ref_action', 'release')->first();
            if ($barcode) {
                $barcode->update([
                    // 'expired'=> $header->expired_date,
                    'expired'=> $manifest->active_to,
                ]);
                $barcodeId = $barcode->id;
            }else {
                do {
                    $uniqueBarcode = Str::random(20);
                } while (Barcode::where('barcode', $uniqueBarcode)->exists());    
                $newBarcode = Barcode::create([
                    'ref_id'=>$manifest->id,
                    'ref_type'=>'Manifest',
                    'ref_action'=> 'release',
                    'ref_number'=>$manifest->notally,
                    'barcode'=> $uniqueBarcode,
                    'status'=> $action,
                    // 'expired'=> $header->expired_date,
                    'expired'=> $manifest->active_to,
                    'uid'=> Auth::user()->id,
                    'created_at'=> Carbon::now(),
                ]);
                $barcodeId = $newBarcode->id;
            }

            $url = url('/barcode/autoGate-indexManifest' . $barcodeId);
            return response()->json([
                'success' => true,
                'success' => true,
                'message' => 'Data ditemukan',
                'linkGatePass' => $url,
            ]); 
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'success' => false,
                'message' => 'Opss Something Wrong : ' . $th->getMessage(),
            ]);
        }
    }
}
