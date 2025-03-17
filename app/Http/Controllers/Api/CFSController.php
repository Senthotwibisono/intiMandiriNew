<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Manifest;
use App\Models\Container as cont;
use App\Models\TarifCFS as Tarif;
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

        $validator = Validator::make($request->all(), [
            'no_order'       => 'required',
            'jenis_billing'  => 'required',
            'jenis_bayar'    => 'required',
            'jenis_transaksi'=> 'required',
            // 'tgl_keluar_lama'=> 'required|date_format:d-m-Y',
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
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'success' => false,
                'message' => 'Validation Error: ' . $validator->errors(),
                'errors'  => $validator->errors(),
            ], 400);
        }

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
            $flagOH = ($manifest->weight >= 2500) ? 'Y' : 'N';

            $cbm = $manifest->meas ? max(2,ceil($manifest->meas)) : 2;

            // dd('Type : ' .$type, $manifest->weight, $manifest->meas, 'CBM : ' . $cbm, 'Flag OH : '.$flagOH, 'Flag Behandle : ' . $flagBehandle, 'Jenis Transaksi : ' . $request->jenis_transaksi);

            if ($request->jenis_transaksi != 'P') {
                $tglStripping = Carbon::parse($manifest->tglstripping);
                $tglKeluar = Carbon::parse($request->tgl_keluar);
                $interval = $tglStripping->diff($tglKeluar->addDays(1))->days;

                $massa1 = 0; $massa2 = 0; $massa3 = 0;

                if ($interval > 5) {
                    $massa3 = $interval - 5;
                    $massa2 = 2;
                    $massa1 = 3;
                }else {
                    $massa1 = min(1, ($interval - 2)); 
                }
                
                dd('Tgl Stripping : ' . $tglStripping, 'Tgl Keluar : ' . $tglKeluar, 'Jumlah Hari : ' . $interval, 'massa1 : ' . $massa1, 'massa2 : ' . $massa2, 'massa3 : ' . $massa3);

                $tarifRDM = ($type == 'NORMAL') ? Tarif::find(1) : Tarif::find(2);
                
            }

            return response()->json([
                'status' => true,
                'success' => true,
                'message' => 'Data ditemukan',
            ]);
        }

        return response()->json([
            'status' => true,
            'success' => true,
            'message' => 'success',
        ]);
    }
}
