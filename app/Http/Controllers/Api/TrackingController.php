<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Manifest;
use App\Models\Container as cont;
use App\Models\ContainerFCL as contF;
use App\Models\JobOrderFCL as JobF;
use App\Models\Photo;
use App\Models\Customer;

class TrackingController extends Controller
{
    protected $token;

    public function __construct() {
        $this->token = 'PcmMcozpCr2KjcuXmipJZ5LYMq3OZhxsiUyVVZ4ldzfviRZi9DU9JbKltPo3qrEo';
    }

    public function searchCargo(Request $request)
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
            'no_hbl_awb'      => 'required',
            'tgl_hbl_awb'      => 'required',
            'nocontainer'        => 'required',
        ];
        
        // Tambahkan aturan validasi jika jenis transaksi adalah 'P'
        if ($request->jenis_transaksi == 'P') {
            $rules['tgl_keluar_lama'] = 'required|date_format:d-m-Y';
        }
        
        $messages = [
            'no_hbl_awb.required'      => 'Nomor BL/AWB wajib diisi.',
            'tgl_hbl_awb.required'      => 'Tanggal BL/AWB wajib diisi.',
            'nocontainer.required'        => 'Nomor kontainer wajib diisi.',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(fn($error) => $error[0])->toArray();
            return response()->json([
                'status'  => false,
                'success' => false,
                'message' => implode(", ", $errors),
                'errors'  => $validator->errors(),
            ]);
        }

        // var_dump($request->all());
        // die;
        $manifest = Manifest::with(['job.sandar', 'cont', 'customer', 'dokumen', 'packing'])->where('nohbl', $request->no_hbl_awb)->where('tgl_hbl', $request->tgl_hbl_awb)->first();
        try {
            if ($manifest) {
                $photoContainers = $photos = Photo::whereIn('type', ['lcl', 'LCL'])
                ->where('master_id', $manifest->container_id)
                ->get()
                ->map(function ($photo) {
                    return [
                        'id' => $photo->id,
                        'type' => $photo->type,
                        'action' => $photo->action,
                        'detil' => $photo->detil,
                        'url' => asset("storage/imagesInt/{$photo->photo}") // Pastikan ada symlink ke storage
                    ];
                }); 

                // dd($photoContainers);
    
                $photoManifestes = $photos = Photo::whereIn('type', ['manifest', 'Manifest'])
                ->where('master_id', $manifest->id)
                ->get()
                ->map(function ($photo) {
                    return [
                        'id' => $photo->id,
                        'type' => $photo->type,
                        'action' => $photo->action,
                        'detil' => $photo->detil,
                        'url' => asset("storage/imagesInt/{$photo->photo}") // Pastikan ada symlink ke storage
                    ];
                }); 

                return response()->json([
                    'success' => true,
                    'message' => 'Data ditemukan',
                    'data' =>[
                        'manifest' => $manifest,
                        'photoContainers' => $photoContainers,
                        'photoManifestes' => $photoManifestes,
                    ],
                ]);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Opss something wrong: ' . $th->getMessage(),
            ]);
        }
    }

    public function searchContainer(Request $request)
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
            'no_bl_awb'      => 'required',
            'tgl_bl_awb'      => 'required',
            'nocontainer'        => 'required',
        ];
        
        // Tambahkan aturan validasi jika jenis transaksi adalah 'P'
        if ($request->jenis_transaksi == 'P') {
            $rules['tgl_keluar_lama'] = 'required|date_format:d-m-Y';
        }
        
        $messages = [
            'no_bl_awb.required'      => 'Nomor BL/AWB wajib diisi.',
            'tgl_bl_awb.required'      => 'Tanggal BL/AWB wajib diisi.',
            'nocontainer.required'        => 'Nomor kontainer wajib diisi.',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(fn($error) => $error[0])->toArray();
            return response()->json([
                'status'  => false,
                'success' => false,
                'message' => implode(", ", $errors),
                'errors'  => $validator->errors(),
            ]);
        }

        $cont = ContF::with(['dokumen'])->where('nobl', $request->no_bl_awb)->where('nocontainer', $request->nocontainer)->first();
        if ($cont) {
            $job = JobF::with('sandar')->find($cont->joborder_id);
            $customer = Customer::find($cont->cust_id);
            $photos = Photo::whereIn('type', ['fcl', 'FCL'])
                ->where('master_id', $cont->id)
                ->get()
                ->map(function ($photo) {
                    return [
                        'id' => $photo->id,
                        'type' => $photo->type,
                        'action' => $photo->action,
                        'detil' => $photo->detil,
                        'url' => asset("storage/imagesInt/{$photo->photo}") // Pastikan ada symlink ke storage
                    ];
                });
            // var_dump($photos);
            // die;
            return response()->json([
                'success' => true,
                'message' => 'Data ditemukan',
                'data' =>[
                    'container' => $cont,
                    'job' => $job,
                    'customer' => $customer,
                    'photos' => $photos
                ],
            ]);
        }else {
            return response()->json([
                'status'  => false,
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ]);
        }
    }
}
