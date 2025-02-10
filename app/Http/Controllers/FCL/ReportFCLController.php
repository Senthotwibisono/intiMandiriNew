<?php

namespace App\Http\Controllers\fcl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\fcl\plpCont;

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
        $cont = Cont::orderBy('joborder_id', 'desc');
        if ($request->has('filter') && $request->filter) {
            if ($request->filter == 'Tgl PLP') {
                $cont = Cont::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_plp', [$request->start_date, $request->end_date])->orderBy('ttgl_plp', 'asc');
                });
            } elseif ($request->filter == 'Tgl Gate In') {
                $cont = Cont::whereBetween('tglmasuk', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc');
            } elseif ($request->filter == 'Tgl Gate Out') {
                $cont = Cont::whereBetween('tglkeluar', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc');
            } elseif ($request->filter == 'Tgl BC 1.1') {
                $cont = Cont::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_bc11', [$request->start_date, $request->end_date])->orderBy('ttgl_bc11', 'asc');
                });
            }
        }

        if ($request->has('noplp') && $request->noplp) {
            $cont = Cont::whereHas('job', function ($query) use ($request) {
                $query->where('noplp', 'LIKE', "%{$request->noplp}%");
            });
        }
    
        if ($request->has('nobc_11') && $request->nobc_11) {
            $cont = Cont::whereHas('job', function ($query) use ($request) {
                $query->where('tno_bc11', 'LIKE', "%{$request->nobc_11}%");
            });
        }
        
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
        ->addColumn('ctrType', function($cont){
            $color = $cont->ctr_type == 'BB' ? 'background-color:rgb(167, 40, 40); color: white;' : '';

            return '<span style="'.$color.'; padding: 5px; border-radius: 5px;">'.$cont->ctr_type.'</span>';
        })
        ->addColumn('classType', function($cont){
            $color = $cont->ctr_type == 'BB' ? 'background-color:rgb(167, 40, 40); color: white;' : '';

            return '<span style="'.$color.'; padding: 5px; border-radius: 5px;">'.$cont->type_class.'</span>';
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
            return $cont->job->tno_bc11 ?? '-';
        })
        ->addColumn('tgl_bc11', function($cont){
            return $cont->job->ttgl_bc11 ?? '';
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
        ->addColumn('lamaHari', function($cont){
            if (!$cont->tglmasuk) {
                $lamaHari = 'Belum Masuk';
                $longStay = 'N';
            } else {
                $lamaHari = Carbon::parse($cont->tglmasuk)->diffInDays($cont->tglkeluar ?? now()) . ' hari';
    
                if (Carbon::parse($cont->tglmasuk)->diffInDays($cont->tglkeluar ?? now()) >= 25 ) {
                    $longStay = 'Y';
                }else {
                    $longStay = 'N';
                }
            }
            return $lamaHari;
        })
        ->addColumn('longStay', function($cont){
            if (!$cont->tglmasuk) {
                $longStay = 'N';
            } else {
                $longStay = Carbon::parse($cont->tglmasuk)->diffInDays($cont->tglkeluar ?? now()) >= 25 ? 'Y' : 'N';
            }
        
            $color = $longStay == 'Y' ? 'background-color: #28a745; color: white;' : '';
        
            return '<span style="'.$color.'; padding: 5px; border-radius: 5px;">'.$longStay.'</span>';
        })
        ->rawColumns(['detil', 'longStay', 'ctrType', 'classType'])
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

    public function formatStandar(Request $request)
    {
        $conts = Cont::orderBy('joborder_id', 'desc')->get();
        if ($request->has('filter') && $request->filter) {
            if ($request->filter == 'Tgl PLP') {
                $conts = Cont::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_plp', [$request->start_date, $request->end_date])->orderBy('ttgl_plp', 'asc')->get();
                });
            } elseif ($request->filter == 'Tgl Gate In') {
                $conts = Cont::whereBetween('tglmasuk', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc')->get();
            } elseif ($request->filter == 'Tgl Gate Out') {
                $conts = Cont::whereBetween('tglkeluar', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc')->get();
            } elseif ($request->filter == 'Tgl BC 1.1') {
                $conts = Cont::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_bc11', [$request->start_date, $request->end_date])->orderBy('ttgl_bc11', 'asc')->get();
                });
            }
        }

        if ($request->has('noplp') && $request->noplp) {
            $conts->whereHas('job', function ($query) use ($request) {
                $query->where('noplp', 'LIKE', "%{$request->noplp}%");
            });
        }
    
        if ($request->has('nobc_11') && $request->nobc_11) {
            $conts->whereHas('job', function ($query) use ($request) {
                $query->where('tno_bc11', 'LIKE', "%{$request->nobc_11}%");
            });
        }

        $fileName = 'ReportContainer-FULL.xlsx' ;
        return Excel::download(new plpCont($conts), $fileName);
    }

}
