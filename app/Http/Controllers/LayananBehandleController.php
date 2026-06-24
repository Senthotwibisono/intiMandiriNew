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
        $data = ContF::with(['job', 'cust', 'job.dokplp', 'job.ves'])->whereNotNull('no_spjm');

        return DataTables::of($data)
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

            $statuses = explode('|', $keyword);

            $query->where(function ($q) use ($statuses) {

                if (in_array('null', $statuses)) {
                    $q->orWhereNull('status_behandle');
                }

                $numericStatuses = array_filter($statuses, function ($status) {
                    return $status !== 'null';
                });

                if (!empty($numericStatuses)) {
                    $q->orWhereIn('status_behandle', $numericStatuses);
                }
            });
        })
        ->rawColumns(['status'])
        ->make(true);
    }
}
