<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

use Maatwebsite\Excel\Facades\Excel;

use App\Models\Container as Cont;
use App\Models\Manifest;
use App\Models\Photo;
use App\Exports\lcl\ReportCont;
use App\Exports\lcl\ReportManifest;

use DataTables;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexCont()
    {
        $data['title'] = "Report Container LCL";
        $data['conts'] = Cont::orderBy('joborder_id', 'desc')->get(); 

        return view('lcl.report.indexCont', $data);
    }

    public function dataCont(Request $request)
    {
        $cont = Cont::orderBy('joborder_id', 'desc')->get();
        
        return DataTables::of($cont)
        ->addColumn('detil', function($cont){
            $herf = '/lcl/report/contPhoto';
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
        ->addColumn('tglmasuk', function($cont){
            return $cont->tglmasuk ?? 'Belum Masuk';
        })
        ->addColumn('jammasuk', function($cont){
            return $cont->jammasuk ?? 'Belum Masuk';
        })
        ->addColumn('tglkeluar', function($cont){
            return $cont->tglkeluar ?? 'Belum Keluar';
        })
        ->addColumn('jamkeluar', function($cont){
            return $cont->jamkeluar ?? 'Belum Keluar';
        })
        ->addColumn('tglstripping', function($cont){
            return $cont->tglstripping ?? 'Belum Stripping';
        })
        ->addColumn('jamstripping', function($cont){
            return $cont->jamstripping ?? 'Belum Stripping';
        })
        ->rawColumns(['detil'])
        ->make(true);
    }

    public function photoCont($id)
    {
        $cont = Cont::where('id', $id)->first();
        $data['title'] = "Photo Container - " . $cont->nocontainer;
        $data['item'] = $cont;
        $data['photos'] = Photo::where('master_id', $id)->where('type', '=', 'lcl')->get();
        // dd($data['photos']);
        return view('lcl.report.photoCont', $data);
    }

    public function generateCont(Request $request)
    {
        $filter = $request->filter;
        switch ($filter) {
            case 'Tgl PLP':
                $conts = Cont::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_plp', [$request->start_date, $request->end_date])->orderBy('ttgl_plp', 'asc');
                })->get();
                break;
            
            case 'Tgl BC 1.1':
                $conts = Cont::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_bc11', [$request->start_date, $request->end_date])->orderBy('ttgl_bc11', 'asc');
                })->get();
                break;
            
            case 'Tgl Gate In':
                $conts = Cont::whereBetween('tglmasuk', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc')->get();
                break;

            default :  
                $conts = Cont::all();
                break;
        }

        $fileName = 'ReportContainer-' . $filter . '-' . $request->start_date . '-' . $request->end_date . '.xlsx';

      return Excel::download(new ReportCont($conts), $fileName);
    }
    
    public function indexManifest()
    {
        $data['title'] = "Report Manifest"; 

        return view('lcl.report.indexManifest', $data);
    }

    public function manifestDataTable(Request $request)
    {
        $start = $request->input('start') ?? Carbon::now()->toDateString();
        $end = $request->input('end') ?? Carbon::now()->toDateString();

        // var_dump($start);
        // die;

        switch ($request->filter) {
            case 'masuk':
                $mans = Manifest::whereHas('cont', function ($query) use ($start, $end) {
                    $query->whereBetween('tglmasuk', [$start, $end]);
                })->get();
            
                break;
            case 'keluar':
                $mans = Manifest::whereBetween('tglrelease', [$start, $end])->get();
                break;
            
            default:
                $mans = Manifest::orderBy('notally', 'desc')->get(); 
                break;
        }

        // var_dump($mans, $start, $end);
       

        return DataTables::of($mans)
        ->addColumn('detil', function($mans){
            $herf = '/lcl/report/manifestPhoto';
            return '<a href="javascript:void(0)" onclick="openWindow(\''.$herf.$mans->id.'\')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
        })
        ->addColumn('joborder', function($mans){
            return $mans->cont->job->nojoborder ?? '-';
        })
        ->addColumn('nm_angkut', function($mans){
            return $mans->cont->job->PLP->nm_angkut ?? '-';
        })
        ->addColumn('nocontainer', function($mans){
            return $mans->cont->nocontainer ?? '-';
        })
        ->addColumn('size', function($mans){
            return $mans->cont->size ?? '-';
        })
        ->addColumn('eta', function($mans){
            return $mans->cont->job->eta ?? '-';
        })
        ->addColumn('kd_tps_asal', function($mans){
            return $mans->cont->job->PLP->kd_tps_asal ?? '-';
        })
        ->addColumn('namaconsolidator', function($mans){
            return $mans->cont->job->PLP->namaconsolidator ?? '-';
        })
        ->addColumn('nohbl', function($mans){
            return $mans->nohbl ?? '-';
        })
        ->addColumn('tgl_hbl', function($mans){
            return $mans->tgl_hbl ?? '-';
        })
        ->addColumn('notally', function($mans){
            return $mans->notally ?? '-';
        })
        ->addColumn('customer', function($mans){
            return $mans->customer->name ?? '-';
        })
        ->addColumn('quantity', function($mans){
            return $mans->quantity ?? '-';
        })
        ->addColumn('final_qty', function($mans){
            return $mans->final_qty ?? '-';
        })
        ->addColumn('packingName', function($mans){
            return $mans->packing->name ?? '-';
        })
        ->addColumn('packingCode', function($mans){
            return $mans->packing->code ?? '-';
        })
        ->addColumn('desc', function($mans){
            $desc = $mans->descofgoods ?? '-';
            return '<textarea class="form-control" cols="3" readonly>'. $desc .'</textarea>';
        })
        ->addColumn('weight', function($mans){
            return $mans->weight ?? '';
        })
        ->addColumn('meas', function($mans){
            return $mans->meas ?? '-';
        })
        ->addColumn('packingTally', function($mans){
            return $mans->packingTally->name ?? '-';
        })
        ->addColumn('noplp', function($mans){
            return $mans->cont->job->noplp ?? '-';
        })
        ->addColumn('tglPLP', function($mans){
            return $mans->cont->job->ttgl_plp ?? '-';
        })
        ->addColumn('no_bc11', function($mans){
            return $mans->cont->job->PLP->no_bc11 ?? '-';
        })
        ->addColumn('tgl_bc11', function($mans){
            return $mans->cont->job->PLP->tgl_bc11 ?? '';
        })
        ->addColumn('tglmasuk', function($mans){
            return $mans->cont->tglmasuk ?? 'Belum Masuk';
        })
        ->addColumn('jammasuk', function($mans){
            return $mans->cont->jammasuk ?? 'Belum Masuk';
        })
        ->addColumn('startstripping', function ($mans) {
            return $mans->startstripping ?? '-'; // Replace with proper column name
        })
        ->addColumn('endstripping', function ($mans) {
            return $mans->endstripping ?? '-'; // Replace with proper column name
        })
        ->addColumn('dokumen', function($mans){
            return $mans->dokumen->name ?? '-';
        })
        ->addColumn('no_dok', function($mans){
            return $mans->no_dok ?? '-';
        })
        ->addColumn('tglDok', function($mans){
            return $mans->tgl_dok ?? '-';
        })
        ->addColumn('location', function($mans){
            return $mans->mostItemsLocation()->Rack->name ?? 'Location not found';
        })
        ->addColumn('lamaTimbun', function($mans){
            return ($mans->lamaTimbun() ?? '0') . 'days';
        })
        ->rawColumns(['detil', 'desc'])
        ->make(true);
    }

    public function photoManifest($id)
    {
        $man = Manifest::where('id', $id)->first();
        $data['title'] = "Photo Manifest - " . $man->nohbl;
        $data['item'] = $man;
        $data['photos'] = Photo::where('master_id', $id)->where('type', '=', 'manifest')->get();
        // dd($data['photos']);
        return view('lcl.report.photoCont', $data);
    }

    public function generateManifest(Request $request)
    {
        $filter = $request->filter;
        switch ($filter) {
            case 'Tgl PLP':
                $manifests = Manifest::whereHas('cont', function ($query) use ($request) {
                    $query->whereHas('job', function ($query) use ($request) {
                        $query->whereBetween('ttgl_plp', [$request->start_date, $request->end_date])->orderBy('ttgl_plp', 'asc');
                    });
                })->get();
                break;
            
            case 'Tgl BC 1.1':
                $manifests = Manifest::whereHas('cont', function ($query) use ($request) {
                    $query->whereHas('job', function ($query) use ($request) {
                        $query->whereBetween('ttgl_bc11', [$request->start_date, $request->end_date])->orderBy('ttgl_bc11', 'asc');
                    });
                })->get();
                break;
            
            case 'Tgl Gate In':
                $manifests = Manifest::whereHas('cont', function ($query) use ($request) {
                    $query->whereBetween('tglmasuk', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc');
                })->get();
                break;
            
            case 'ETA':
                $manifests = Manifest::whereHas('cont', function ($query) use ($request) {
                    $query->whereHas('job', function ($query) use ($request) {
                        $query->whereBetween('eta', [$request->start_date, $request->end_date])->orderBy('eta', 'asc');
                    });
                })->get();
                break;

            case 'Tgl Release':
                $manifests = Manifest::whereBetween('tglrelease', [$request->start_date, $request->end_date])->orderBy('tglrelease', 'asc')->get();
                break;

            default:  
                $manifests = Manifest::all();
                break;
        }
        
        // dd($manifests);
        $fileName = 'ReportManifest-' . $filter . '-' . $request->start_date . '-' . $request->end_date . '.xlsx';

        return Excel::download(new ReportManifest($manifests), $fileName);
    }

    public function indexDaily(Request $request)
    {
        $data['title'] = "Report Daily";

        // Use dates from the request or default to the current date
        $start = $request->input('start_date') ?? Carbon::now()->toDateString();
        $end = $request->input('end_date') ?? Carbon::now()->toDateString();

        $awal = Manifest::whereHas('cont', function ($query) use ($start) {
            $query->whereDate('tglmasuk', '<=', $start);
        })->where(function ($query) use ($start) {
            $query->whereDate('tglrelease', '>=', $start)
                  ->orWhereNull('tglrelease');
        })->get();
    
        $data['awal'] = $awal;
        $data['jumlahAwal'] = $awal->count();
        $data['quantityAwal'] = $awal->sum('quantity');
        $data['tonaseAwal'] = $awal->sum('weight');
        $data['volumeAwal'] = $awal->sum('meas');

        $masuk = Manifest::whereHas('cont', function ($query) use ($start, $end) {
            $query->whereBetween('tglmasuk', [$start, $end]);
        })->get();
        
        // dd($masuk);
        $data['masuk'] = $masuk;
        $data['jumlahMasuk'] = $masuk->count();
        $data['quantityMasuk'] = $masuk->sum('quantity');
        $data['tonaseMasuk'] = $masuk->sum('weight');
        $data['volumeMasuk'] = $masuk->sum('meas');

        $keluar = Manifest::whereBetween('tglrelease', [$start, $end])->get();
        $data['keluar'] = $keluar;
        $data['jumlahKeluar'] = $keluar->count();
        $data['quantityKeluar'] = $keluar->sum('quantity');
        $data['tonaseKeluar'] = $keluar->sum('weight');
        $data['volumeKeluar'] = $keluar->sum('meas');

        $akhir = Manifest::whereHas('cont', function ($query) use ($end) {
            $query->whereDate('tglmasuk', '<=', $end);
        })->whereNull('tglrelease')->get();
        
        // ->where(function ($query) use ($end) {
        //     $query->whereDate('tglrelease', '>=', $end)
        //           ->orWhereNull('tglrelease');
        // })->get();
        $data['akhir'] = $akhir;
        $data['jumlahAkhir'] = $akhir->count();
        $data['quantityAkhir'] = $akhir->sum('quantity');
        $data['tonaseAkhir'] = $akhir->sum('weight');
        $data['volumeAkhir'] = $akhir->sum('meas');

        return view('lcl.report.indexDaily', $data, compact('start', 'end'));
    }


}
