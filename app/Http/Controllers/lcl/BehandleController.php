<?php

namespace App\Http\Controllers\lcl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use DataTables;
use Illuminate\Support\Facades\DB;

use App\Models\Container as Cont;
use App\Models\JobOrder as Job;
use App\Models\Manifest;
use App\Models\TpsSPJM as SPJM;

class BehandleController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    public function behandleIndex()
    {
        $data['title'] = 'Behandle LCL';
        $data['manifestes'] = Manifest::whereNotNull('tglstripping')->whereNull('tglrelease')->get(); 

        return view('lcl.behandle.index', $data);
    }

    public function behandleData(Request $request)
    {
        $data = Manifest::with(['cont', 'job', 'customer', 'packing'])->whereNotNull('no_spjm');

        return DataTables::of($data)
        ->addColumn('edit', function($data){
            return '<button class="btn btn-warning" onClick="editData(this)" data-id="'.$data->id.'" data-label="'.$data->nohbl.'"><i class="fas fa-pencil"></i></button>';
        })
        ->rawColumns(['edit'])
        ->make(true);
    }

    public function manifestData(Request $request)
    {
        $data = Manifest::find($request->id);

        return response()->json([
            'success' => true,
            'message' => 'Data ditemukan',
            'data' => $data
        ]);
    }

    public function spjmData(Request $request)
    {
        try {
            DB::transaction(function() use($request) {
                $manifest = Manifest::find($request->id);   

                if (!$manifest) {
                    throw new \Exception('Manifest tidak ditemukan.');
                }   

                if ($request->jenis_spjm === 'spjm') {
                    $spjm = SPJM::where('no_spjm', $request->no_spjm)
                        ->where('tgl_pib', Carbon::parse($request->tgl_spjm)->format('Y-m-d'))
                        ->first();  

                    if (!$spjm) {
                        throw new \Exception('Data SPJM tidak ditemukan.');
                    }
                }   

                $manifest->update([
                    'jenis_spjm' => $request->jenis_spjm,
                    'no_spjm' => $request->no_spjm,
                    'tgl_spjm' => $request->tgl_spjm,
                ]);
            }); 

            return response()->json([
                'success' => true,
                'message' => 'Aksi berhasil'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 422);
        }
    }

    public function behandleSubmit(Request $request)
    {
        // var_dump($request->all());
        // die;
        try {
            db::transaction(function() use($request){
                $data = Manifest::find($request->id);
                $status = null;

                if (!empty($request->date_finish_behandle)) {
                    $status = 3;
                } elseif (!empty($request->date_check_behandle)) {
                    $status = 2;
                } elseif (!empty($request->date_ready_behandle)) {
                    $status = 1;
                }
                $data->update([
                    'date_ready_behandle' => $request->date_ready_behandle,
                    'date_check_behandle' => $request->date_check_behandle,
                    'date_finish_behandle' => $request->date_finish_behandle,
                    'desc_check_behandle' => $request->desc_check_behandle,
                    'desc_finish_behandle' => $request->desc_finish_behandle,
                    'status_behandle' => $status,
                    'petugas_behandle' => $request->petugas_behandle,
                ]);

                // if ($request->hasFile('photos')) {
                //     foreach ($request->file('photos') as $photo) {
                //         $fileName = $photo->getClientOriginalName();
                //         $photo->storeAs('imagesInt', $fileName, 'public'); 
                //         $newPhoto = Photo::create([
                //             'master_id' => $data->id,
                //             'type' => 'fcl',
                //             'action' => 'behandle',
                //             'detil' => $request->detilPhoto,
                //             'photo' => $fileName,
                //         ]);
                //     }
                // }
            });

            return response()->json([
                "success" => true,
                "message" => 'Aksi Berhasil'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => $th->getMessage()
            ]);
        }
    }

    public function behandleReport(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;    

        $data = Manifest::query()
            ->when($fromDate, function($query) use($fromDate) {
                $query->whereDate('date_check_behandle', '>=', $fromDate);
            })
            ->when($toDate, function($query) use($toDate) {
                $query->whereDate('date_check_behandle', '<=', $toDate);
            })
            ->get();    

        $filename = 'Laporan_Behandle_' . ($fromDate ?: 'all') . '_sd_' . ($toDate ?: 'all') . '.xls';  

        $html = '
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                }   

                table {
                    border-collapse: collapse;
                    width: 100%;
                }   

                th, td {
                    border: 1px solid #000;
                    padding: 5px;
                    vertical-align: middle;
                }   

                .title {
                    font-size: 14px;
                    font-weight: bold;
                    text-align: center;
                }   

                .header {
                    font-weight: bold;
                    text-align: center;
                    vertical-align: middle;
                }   

                .center {
                    text-align: center;
                }
            </style>
        </head>
        <body>  

        <table>
            <tr>
                <td colspan="13" class="title">
                    DAFTAR CARGO LCL JALUR MERAH PER HARI YANG SUDAH SELESAI
                    PROSES PEMERIKSAAN (BEHANDLE) DI TPS INTI MANDIRI UTAMA TRANS
                </td>
            </tr>   

            <tr>
                <td colspan="13"></td>
            </tr>   

            <tr class="header">
                <th rowspan="2">NO</th>
                <th colspan="3">SURAT PEMBERITAHUAN JALUR MERAH (SPJM)</th>
                <th rowspan="2">TGL PEMERIKSAAN</th>
                <th rowspan="2">PETUGAS PEMERIKSA</th>
                <th colspan="6">DATA CARGO</th>
                <th rowspan="2">KET</th>
            </tr>   

            <tr class="header">
                <th>JENIS DOK</th>
                <th>NO. PENDAFTARAN PIB</th>
                <th>TANGGAL SPJM</th>
                <th>VESSEL & VOY</th>
                <th>NO CONTAINER</th>
                <th>NO. H B/L</th>
                <th>CONSIGNEE</th>
                <th>PARTY</th>
                <th>DESKRIPSI</th>
            </tr>
        ';  

        $no = 1;    

        foreach ($data as $item) {
            $html .= '
                <tr>
                    <td class="center">' . $no++ . '</td>
                    <td class="center">' . e($item->jenis_spjm) . '</td>
                    <td class="center">' . e($item->no_spjm) . '</td>
                    <td class="center">' . ($item->tgl_spjm ? \Carbon\Carbon::parse($item->tgl_spjm)->format('d-m-Y') : '') . '</td>
                    <td class="center">' . ($item->date_check_behandle ? \Carbon\Carbon::parse($item->date_check_behandle)->format('d-m-Y') : '') . '</td>
                    <td>' . e($item->petugas_behandle) . '</td>
                    <td>' . e($item->job->voy ?? '') . '</td>
                    <td>' . e($item->cont->nocontainer) . '</td>
                    <td>' . e($item->nohbl) . '</td>
                    <td>' . e($item->customer->name) . '</td>
                    <td>' . e($item->quantity) .  e($item->packing->code) .'</td>
                    <td>' . e($item->descofgoods ?? '') . '</td>
                    <td>' . e($item->desc_check_behandle) . '</td>
                </tr>
            ';
        }   

        $html .= '
            <tr>
                <td colspan="13" style="font-style: italic;">
                    * dokumen dan foto proses pemeriksaan terlampir
                </td>
            </tr>
        </table>    

        </body>
        </html>
        ';  

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
