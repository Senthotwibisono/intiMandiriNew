<?php

namespace App\Http\Controllers\fcl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

use DataTables;
use App\Models\ContainerFCL as Cont;
use App\Models\Photo;

class ReportFCLController extends Controller
{
    public function index()
    {
        $data['title'] = "Report Container FCL";

        return view('fcl.report.indexCont', $data);
    }

    public function dataCont(Request $request)
    {
        $cont = Cont::orderBy('joborder_id', 'desc')->get();
        
        return DataTables::of($cont)
        ->addColumn('detil', function($cont){
            $herf = '/fcl/report/photoCont';
            return '<a href="javascript:void(0)" onclick="openWindow(\''.$herf.$cont->id.'\')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
        })
        ->addColumn('jobordr', function($cont){
            return $cont->job->nojoborder ?? '-';
        })
        ->addColumn('nm_angkut', function($cont){
            return $cont->job->PLP->nm_angkut ?? '-';
        })
        ->addColumn('nocontainer', function($cont){
            return $cont->nocontainer ?? '-';
        })
        ->addColumn('size', function($cont){
            return $cont->size ?? '-';
        })
        ->addColumn('eta', function($cont){
            return $cont->job->eta ?? '-';
        })
        ->addColumn('kd_tps_asal', function($cont){
            return $cont->job->PLP->kd_tps_asal ?? '-';
        })
        ->addColumn('namaconsolidator', function($cont){
            return $cont->job->PLP->namaconsolidator ?? '-';
        })
        ->addColumn('noplp', function($cont){
            return $cont->job->noplp ?? '-';
        })
        ->addColumn('tglPLP', function($cont){
            return $cont->job->ttgl_plp ?? '-';
        })
        ->addColumn('no_bc11', function($cont){
            return $cont->job->PLP->no_bc11 ?? '-';
        })
        ->addColumn('tgl_bc11', function($cont){
            return $cont->job->PLP->tgl_bc11 ?? '';
        })
        ->addColumn('nobl', function($cont){
            return $cont->nobl ?? '-';
        })
        ->addColumn('tglBL', function($cont){
            return $cont->tgl_bl_awb ?? '-';
        })
        ->addColumn('customer', function($cont){
            return $cont->Customer->name ?? '-';
        })
        ->addColumn('npwp', function($cont){
            return $cont->Customer->npwp ?? '-';
        })
        ->addColumn('email', function($cont){
            return $cont->Customer->email ?? '-';
        })
        ->addColumn('nopol', function($cont){
            return $cont->nopol ?? '-';
        })
        ->addColumn('tglmasuk', function($cont){
            return $cont->tglmasuk ?? 'Belum Masuk';
        })
        ->addColumn('jammasuk', function($cont){
            return $cont->jammasuk ?? 'Belum Masuk';
        })
        ->addColumn('nopol_mty', function($cont){
            return $cont->nopol_mty ?? '-';
        })
        ->addColumn('tglkeluar', function($cont){
            return $cont->tglkeluar ?? 'Belum keluar';
        })
        ->addColumn('jamkeluar', function($cont){
            return $cont->jamkeluar ?? 'Belum keluar';
        })
        ->addColumn('kodeDok', function($cont){
            return $cont->dokumen->name ?? '-';
        })
        ->addColumn('noDok', function($cont){
            return $cont->no_dok ?? '-';
        })
        ->addColumn('tglDok', function($cont){
            return $cont->tgl_dok ?? '-';
        })
        ->rawColumns(['detil'])
        ->make(true);
    }

    public function photoCont($id)
    {
        $cont = Cont::where('id', $id)->first();
        $data['title'] = "Photo Container - " . $cont->nocontainer;
        $data['item'] = $cont;
        $data['photos'] = Photo::where('master_id', $id)->where('type', '=', 'fcl')->get();
        // dd($data['photos']);
        return view('lcl.report.photoCont', $data);
    }

}
