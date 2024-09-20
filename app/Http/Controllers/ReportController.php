<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Container as Cont;
use App\Models\Manifest;
use App\Models\Photo;
use App\Exports\lcl\ReportCont;
use App\Exports\lcl\ReportManifest;

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
        $data['manifest'] = Manifest::orderBy('notally', 'desc')->get(); 

        return view('lcl.report.indexManifest', $data);
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
                $manifests = Manifest::whereBetween('tglbuangmty', [$request->start_date, $request->end_date])->orderBy('tglbuangmty', 'asc')->get();
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
        $data['title'] = "Report Manifest";

        // Use dates from the request or default to the current date
        $start = $request->input('start_date') ?? Carbon::now()->toDateString();
        $end = $request->input('end_date') ?? Carbon::now()->toDateString();

        $awal = Manifest::whereDate('tglmasuk', '<=', $start)
        ->where(function ($query) use ($start) {
            $query->whereDate('tglrelease', '>=', $start)
                  ->orWhereNull('tglrelease');
        })->get();
        $data['awal'] = $awal;
        $data['jumlahAwal'] = $awal->count();
        $data['quantityAwal'] = $awal->sum('quantity');
        $data['tonaseAwal'] = $awal->sum('weight');
        $data['volumeAwal'] = $awal->sum('meas');

        $masuk = Manifest::whereBetween('tglmasuk', [$start, $end])->get();
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

        $akhir = Manifest::whereDate('tglmasuk', '<=', $end)
        ->where(function ($query) use ($end) {
            $query->whereDate('tglrelease', '>=', $end)
                  ->orWhereNull('tglrelease');
        })->get();
        $data['akhir'] = $akhir;
        $data['jumlahAkhir'] = $akhir->count();
        $data['quantityAkhir'] = $akhir->sum('quantity');
        $data['tonaseAkhir'] = $akhir->sum('weight');
        $data['volumeAkhir'] = $akhir->sum('meas');

        return view('lcl.report.indexDaily', $data);
    }


}
