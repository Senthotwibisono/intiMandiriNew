<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use DataTables;

use App\Models\ContainerFCL as ContF;

class LayananBehandleController extends Controller
{
    public function index()
    {
        $data['title'] = 'Monitoring Behandle';

        return view('layanan.behandle', $data);
    }

    public function dataFCL(Request $request)
    {
        $data = ContF::with(['job', 'cust', 'job.dokplp', 'job.ves'])->whereNotNull('no_spjm')->whereNotNull('tglmasuk')->whereNull('tglkeluar');

        return DataTables::of($data)
        ->addColumn('photo', function($cont){
            return '<a href="javascript:void(0)"class="button is-primary"onclick="openPhoto('.$cont->id.')"><i class="fas fa-camera"></i></a>';
        })
        ->addColumn('status', function($cont){
            if ($cont->status_behandle == 1) {
                return '<span class="badge bg-primary">Ready</span>';
            } elseif ($cont->status_behandle == 2) {
                return '<span class="badge bg-warning">On Progress</span>';
            } elseif ($cont->status_behandle == 3) {
                return '<span class="badge bg-info">Finish</span>';
            }else {
                // return '<span class="badge bg-light-warning">Dokumen SPJM Belum tersedia</span>';
                return '-';
            }
        })
        ->filterColumn('status', function ($query, $keyword) {
            // var_dump($keyword);
            // die;
            switch ($keyword) {
                case 'PKK':
                    $query->whereNull('status_behandle');
                    break;
                case 'PKB':
                    // Belum ada logic
                    // nanti isi di sini
                    break;
                case 1:
                case 2:
                case 3:
                    $query->where('status_behandle', $keyword);
                    break;
            }
        })
        ->with([
            'summary' => [
                'total' => ContF::whereNotNull('no_spjm')->whereNotNull('tglmasuk')->whereNull('tglkeluar')->count(),
                'ppk'   => ContF::whereNotNull('no_spjm')->whereNotNull('tglmasuk')->whereNull('tglkeluar')->whereNull('status_behandle')->count(),
                'pkb'   => 0, // nanti isi sesuai kondisinya
                'siap'  => ContF::whereNotNull('no_spjm')->whereNotNull('tglmasuk')->whereNull('tglkeluar')->where('status_behandle', 1)->count(),
                'proses'=> ContF::whereNotNull('no_spjm')->whereNotNull('tglmasuk')->whereNull('tglkeluar')->where('status_behandle', 2)->count(),
                'selesai'=> ContF::whereNotNull('no_spjm')->whereNotNull('tglmasuk')->whereNull('tglkeluar')->where('status_behandle', 3)->count(),
            ]
        ])
        ->rawColumns(['status', 'photo'])
        ->make(true);
    }
}
