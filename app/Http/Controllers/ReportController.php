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
use App\Exports\lcl\ReportContJICT;
use App\Exports\lcl\ReportManifest;
use App\Exports\lcl\ReportManifestBeaCukaiNew;
use Illuminate\Support\Facades\DB;


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
        $data = Cont::query()
        ->select(
            'tcontainer.*',
            'j.nojoborder',
            'j.noplp',
            'j.ttgl_plp',
            'j.tno_bc11',
            'j.ttgl_bc11',
            DB::raw('p.nm_angkut as plp_nm_angkut'),
            DB::raw('p.kd_tps_asal as plp_kd_tps_asal'),
            DB::raw('p.namaconsolidator as plp_namaconsolidator')
        )
        ->leftJoin('tjoborder as j', 'j.id', '=', 'tcontainer.joborder_id')
        ->leftJoin('tps_responplptujuanxml as p', 'p.id', '=', 'j.plp_id');

        // filtering by date (optional)
        $cont = $data;
        if ($request->has('filter') && $request->filter) {
            if ($request->filter == 'Tgl PLP') {
                $cont->whereBetween('j.ttgl_plp', [$request->start_date, $request->end_date])
                     ->orderBy('j.ttgl_plp', 'asc');
            } elseif ($request->filter == 'Tgl Gate In') {
                $cont->whereBetween('tcontainer.tglmasuk', [$request->start_date, $request->end_date])
                     ->orderBy('tcontainer.tglmasuk', 'asc');
            } elseif ($request->filter == 'Tgl Gate Out') {
                $cont->whereBetween('tcontainer.tglbuangmty', [$request->start_date, $request->end_date])
                     ->orderBy('tcontainer.tglmasuk', 'asc');
            } elseif ($request->filter == 'Tgl BC 1.1') {
                $cont->whereBetween('j.ttgl_bc11', [$request->start_date, $request->end_date])
                     ->orderBy('j.ttgl_bc11', 'asc');
            }
        }

        if ($request->filled('noplp')) {
            $cont->where('j.noplp', 'like', "%{$request->noplp}%");
        }

        if ($request->filled('nobc_11')) {
            $cont->where('j.tno_bc11', 'like', "%{$request->nobc_11}%");
        }

        return DataTables::of($cont)
            ->addColumn('detil', function($cont){
                $herf = '/lcl/report/contPhoto';
                return '<a href="javascript:void(0)" onclick="openWindow(\''.$herf.$cont->id.'\')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
            })

            // joborder
            ->editColumn('jobordr', fn($cont) => $cont->nojoborder ?? '-')
            ->filterColumn('jobordr', fn($q, $k) => $q->where('j.nojoborder', 'like', "%{$k}%"))
            ->orderColumn('jobordr', 'j.nojoborder $1')

            // nm_angkut (plp)
            ->editColumn('nm_angkut', fn($cont) => $cont->nm_angkut ?? '-')
            ->filterColumn('nm_angkut', fn($q, $k) => $q->where('p.nm_angkut', 'like', "%{$k}%"))
            ->orderColumn('nm_angkut', 'p.nm_angkut $1')

            // nocontainer
            ->editColumn('nocontainer', fn($cont) => $cont->nocontainer ?? '-')
            ->filterColumn('nocontainer', fn($q, $k) => $q->where('tcontainer.nocontainer', 'like', "%{$k}%"))
            ->orderColumn('nocontainer', 'tcontainer.nocontainer $1')

            // ctrType (dengan warna)
            ->addColumn('ctrType', function($cont){
                $color = $cont->ctr_type == 'BB' ? 'background-color:rgb(167, 40, 40); color: white;' : '';
                return '<span style="'.$color.'; padding: 5px; border-radius: 5px;">'.$cont->ctr_type.'</span>';
            })
            ->filterColumn('ctrType', fn($q, $k) => $q->where('tcontainer.ctr_type', 'like', "%{$k}%"))
            ->orderColumn('ctrType', 'tcontainer.ctr_type $1')

            // classType
            ->addColumn('classType', function($cont){
                $color = $cont->ctr_type == 'BB' ? 'background-color:rgb(167, 40, 40); color: white;' : '';
                return '<span style="'.$color.'; padding: 5px; border-radius: 5px;">'.$cont->type_class.'</span>';
            })
            ->filterColumn('classType', fn($q, $k) => $q->where('tcontainer.type_class', 'like', "%{$k}%"))
            ->orderColumn('classType', 'tcontainer.type_class $1')

            // size
            ->editColumn('size', fn($cont) => $cont->size ?? '-')
            ->filterColumn('size', fn($q, $k) => $q->where('tcontainer.size', 'like', "%{$k}%"))
            ->orderColumn('size', 'tcontainer.size $1')

            // eta
            ->editColumn('eta', fn($cont) => $cont->eta ?? '-')
            ->filterColumn('eta', fn($q, $k) => $q->where('tcontainer.eta', 'like', "%{$k}%"))
            ->orderColumn('eta', 'tcontainer.eta $1')

            // kd_tps_asal (plp)
            ->addColumn('kd_tps_asal', fn($cont) => $cont->plp_kd_tps_asal ?? '-')
            ->filterColumn('kd_tps_asal', function($query, $keyword) {
                $query->where('p.kd_tps_asal', 'like', "%{$keyword}%");
            })
            ->orderColumn('kd_tps_asal', 'p.kd_tps_asal $1')

            // namaconsolidator (plp)
            ->addColumn('namaconsolidator', fn($cont) => $cont->plp_namaconsolidator ?? '-')
            ->filterColumn('namaconsolidator', function($query, $keyword) {
                $query->where('p.namaconsolidator', 'like', "%{$keyword}%");
            })
            ->orderColumn('namaconsolidator', 'p.namaconsolidator $1')

            // noplp
            ->editColumn('noplp', fn($cont) => $cont->noplp ?? '-')
            ->filterColumn('noplp', fn($q, $k) => $q->where('j.noplp', 'like', "%{$k}%"))
            ->orderColumn('noplp', 'j.noplp $1')

            // tglPLP
            ->editColumn('tglPLP', fn($cont) => $cont->ttgl_plp ?? '-')
            ->filterColumn('tglPLP', fn($q, $k) => $q->where('j.ttgl_plp', 'like', "%{$k}%"))
            ->orderColumn('tglPLP', 'j.ttgl_plp $1')

            // no_bc11
            ->editColumn('no_bc11', fn($cont) => $cont->tno_bc11 ?? '-')
            ->filterColumn('no_bc11', fn($q, $k) => $q->where('j.tno_bc11', 'like', "%{$k}%"))
            ->orderColumn('no_bc11', 'j.tno_bc11 $1')

            // tgl_bc11
            ->editColumn('tgl_bc11', fn($cont) => $cont->ttgl_bc11 ?? '-')
            ->filterColumn('tgl_bc11', fn($q, $k) => $q->where('j.ttgl_bc11', 'like', "%{$k}%"))
            ->orderColumn('tgl_bc11', 'j.ttgl_bc11 $1')

            // nobl
            ->editColumn('nobl', fn($cont) => $cont->nobl ?? '-')
            ->filterColumn('nobl', fn($q, $k) => $q->where('tcontainer.nobl', 'like', "%{$k}%"))
            ->orderColumn('nobl', 'tcontainer.nobl $1')

            // tglBL
            ->editColumn('tglBL', fn($cont) => $cont->tgl_bl_awb ?? '-')
            ->filterColumn('tglBL', fn($q, $k) => $q->where('tcontainer.tgl_bl_awb', 'like', "%{$k}%"))
            ->orderColumn('tglBL', 'tcontainer.tgl_bl_awb $1')

            // nopol
            ->editColumn('nopol', fn($cont) => $cont->nopol ?? '-')
            ->filterColumn('nopol', fn($q, $k) => $q->where('tcontainer.nopol', 'like', "%{$k}%"))
            ->orderColumn('nopol', 'tcontainer.nopol $1')

            // tglmasuk
            ->editColumn('tglmasuk', fn($cont) => $cont->tglmasuk ?? 'Belum Masuk')
            ->filterColumn('tglmasuk', fn($q, $k) => $q->where('tcontainer.tglmasuk', 'like', "%{$k}%"))
            ->orderColumn('tglmasuk', 'tcontainer.tglmasuk $1')

            // jammasuk
            ->editColumn('jammasuk', fn($cont) => $cont->jammasuk ?? 'Belum Masuk')
            ->filterColumn('jammasuk', fn($q, $k) => $q->where('tcontainer.jammasuk', 'like', "%{$k}%"))
            ->orderColumn('jammasuk', 'tcontainer.jammasuk $1')

            // tglstripping
            ->editColumn('tglstripping', fn($cont) => $cont->tglstripping ?? 'Belum Stripping')
            ->filterColumn('tglstripping', fn($q, $k) => $q->where('tcontainer.tglstripping', 'like', "%{$k}%"))
            ->orderColumn('tglstripping', 'tcontainer.tglstripping $1')

            // jamstripping
            ->editColumn('jamstripping', fn($cont) => $cont->jamstripping ?? 'Belum Stripping')
            ->filterColumn('jamstripping', fn($q, $k) => $q->where('tcontainer.jamstripping', 'like', "%{$k}%"))
            ->orderColumn('jamstripping', 'tcontainer.jamstripping $1')

            // nopol_mty
            ->editColumn('nopol_mty', fn($cont) => $cont->nopol_mty ?? '-')
            ->filterColumn('nopol_mty', fn($q, $k) => $q->where('tcontainer.nopol_mty', 'like', "%{$k}%"))
            ->orderColumn('nopol_mty', 'tcontainer.nopol_mty $1')

            // tglkeluar
            ->editColumn('tglkeluar', fn($cont) => $cont->tglkeluar ?? 'Belum keluar')
            ->filterColumn('tglkeluar', fn($q, $k) => $q->where('tcontainer.tglkeluar', 'like', "%{$k}%"))
            ->orderColumn('tglkeluar', 'tcontainer.tglkeluar $1')

            // jamkeluar
            ->editColumn('jamkeluar', fn($cont) => $cont->jamkeluar ?? 'Belum keluar')
            ->filterColumn('jamkeluar', fn($q, $k) => $q->where('tcontainer.jamkeluar', 'like', "%{$k}%"))
            ->orderColumn('jamkeluar', 'tcontainer.jamkeluar $1')

            // lamaHari (hitung manual)
            ->addColumn('lamaHari', function($cont){
                if (!$cont->tglmasuk) return 'Belum Masuk';
                return Carbon::parse($cont->tglmasuk)->diffInDays($cont->tglbuangmty ?? now()) . ' hari';
            })

            // longStay
            ->addColumn('longStay', function($cont){
                if (!$cont->tglmasuk) {
                    $longStay = 'N';
                } else {
                    $longStay = Carbon::parse($cont->tglmasuk)->diffInDays($cont->tglbuangmty ?? now()) >= 25 ? 'Y' : 'N';
                }
                $color = $longStay == 'Y' ? 'background-color: #28a745; color: white;' : '';
                return '<span style="'.$color.'; padding: 5px; border-radius: 5px;">'.$longStay.'</span>';
            })

            ->rawColumns(['detil','ctrType','classType','longStay'])
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
        $conts = Cont::orderBy('joborder_id', 'desc');
        if ($request->has('filter') && $request->filter) {
            if ($request->filter == 'Tgl PLP') {
                $conts = Cont::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_plp', [$request->start_date, $request->end_date])->orderBy('ttgl_plp', 'asc');
                });
            } elseif ($request->filter == 'Tgl Gate In') {
                $conts = Cont::whereBetween('tglmasuk', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc');
            } elseif ($request->filter == 'Tgl Gate Out') {
                $conts = Cont::whereBetween('tglkeluar', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc');
            } elseif ($request->filter == 'Tgl BC 1.1') {
                $conts = Cont::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_bc11', [$request->start_date, $request->end_date])->orderBy('ttgl_bc11', 'asc');
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

        $conts = $conts->get();

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $tanggalJudul =  $this->formatDateRange($start_date, $end_date);

        // dd($tanggalJudul);

        $judul = 'Laporan Bulanan '. $tanggalJudul;

        $fileName = 'ReportContainer-LCL'.$start_date.'-'.$end_date.'.xlsx' ;
        return Excel::download(new ReportCont($conts, $judul), $fileName);
    }


    public function contGenerateJICT(Request $request)
    {
        $conts = Cont::orderBy('joborder_id', 'desc');
        if ($request->has('filter') && $request->filter) {
            if ($request->filter == 'Tgl PLP') {
                $conts = Cont::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_plp', [$request->start_date, $request->end_date])->orderBy('ttgl_plp', 'asc');
                });
            } elseif ($request->filter == 'Tgl Gate In') {
                $conts = Cont::whereBetween('tglmasuk', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc');
            } elseif ($request->filter == 'Tgl Gate Out') {
                $conts = Cont::whereBetween('tglkeluar', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc');
            } elseif ($request->filter == 'Tgl BC 1.1') {
                $conts = Cont::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_bc11', [$request->start_date, $request->end_date])->orderBy('ttgl_bc11', 'asc');
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

        $conts = $conts->whereHas('job', function($query) use ($request){
            $query->where('lokasisandar_id', 3);
        })->get();

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $tanggalJudul =  $this->formatDateRange($start_date, $end_date);

        // dd($tanggalJudul);

        $judul = 'Laporan Bulanan '. $tanggalJudul;

        $fileName = 'ReportContainer-LCL'.$start_date.'-'.$end_date.'.xlsx' ;
        return Excel::download(new ReportContJICT($conts, $judul), $fileName);
    }

    private function formatDateRange($start_date, $end_date)
    {
        if (!$start_date && !$end_date) {
            return null; // Jika keduanya kosong, abaikan
        }

        // Jika salah satu kosong, gunakan yang tersedia
        if (!$start_date) {
            return Carbon::parse($end_date)->translatedFormat('j F Y');
        }
        if (!$end_date) {
            return Carbon::parse($start_date)->translatedFormat('j F Y');
        }

        $start = Carbon::parse($start_date);
        $end = Carbon::parse($end_date);

        if ($start->year === $end->year) {
            if ($start->month === $end->month) {
                return $start->format('j') . ' - ' . $end->translatedFormat('j F Y');
            }
            return $start->translatedFormat('j F') . ' - ' . $end->translatedFormat('j F Y');
        }

        return $start->translatedFormat('j F Y') . ' - ' . $end->translatedFormat('j F Y');
    }
    
    public function indexManifest()
    {
        $data['title'] = "Report Manifest"; 

        $data['conts'] = Cont::orderBy('joborder_id', 'asc')->get();

        return view('lcl.report.indexManifest', $data);
    }

    public function manifestDataTable(Request $request)
    {
        $mans = Manifest::query()
            ->select(['tmanifest.*',
                 DB::raw("DATEDIFF(COALESCE(tmanifest.tglrelease, NOW()), tmanifest.tglstripping) as lamaTimbun")
            ])
            ->with(['cont.job.PLP', 'customer', 'packing', 'packingTally', 'dokumen'])
            ->leftJoin('tcontainer as c', 'c.id', '=', 'tmanifest.container_id')
            ->leftJoin('tjoborder as j', 'j.id', '=', 'c.joborder_id')
            ->leftJoin('tps_responplptujuanxml as p', 'p.id', '=', 'j.plp_id')
            ->leftJoin('customer as cust', 'cust.id', '=', 'tmanifest.customer_id')
            ->leftJoin('tpacking as pk', 'pk.id', '=', 'tmanifest.packing_id')
            ->leftJoin('tpacking as pkt', 'pkt.id', '=', 'tmanifest.packing_tally')
            ->leftJoin('kode_dok as d', 'd.id', '=', 'tmanifest.kd_dok_inout')
            ->orderBy('j.id', 'asc');
    
        // 🔎 Filtering by date types
        if ($request->has('filter') && $request->filter) {
            if ($request->filter == 'Tgl PLP') {
                $mans->whereBetween('j.ttgl_plp', [$request->start_date, $request->end_date]);
            } elseif ($request->filter == 'Tgl Gate In') {
                $mans->whereBetween('c.tglmasuk', [$request->start_date, $request->end_date]);
            } elseif ($request->filter == 'Tgl Release') {
                $mans->whereBetween('tmanifest.tglrelease', [$request->start_date, $request->end_date]);
            } elseif ($request->filter == 'Tgl BC 1.1') {
                $mans->whereBetween('j.ttgl_bc11', [$request->start_date, $request->end_date]);
            } elseif ($request->filter == 'ETA') {
                $mans->whereBetween('j.eta', [$request->start_date, $request->end_date]);
            } elseif ($request->filter == 'masuk') {
                $mans->whereBetween('c.tglmasuk', [$request->start_date, $request->end_date]);
            } elseif ($request->filter == 'keluar') {
                $mans->whereBetween('tmanifest.tglrelease', [$request->start_date, $request->end_date]);
            } elseif ($request->filter == 'akhir') {
                $mans->whereDate('c.tglmasuk', '<=', $request->end_date)
                     ->where(function($q) use ($request){
                         $q->whereNull('tmanifest.tglrelease')
                           ->orWhereDate('tmanifest.tglrelease', '>', $request->end_date);
                     });
            }
        }
    
        // 🔎 Filter by container id
        if ($request->has('container_id') && $request->container_id) {
            $mans->where('tmanifest.container_id', $request->container_id);
        }
    
        return DataTables::of($mans)
            // detil button
            ->addColumn('detil', function($mans){
                $herf = '/lcl/report/manifestPhoto';
                return '<a href="javascript:void(0)" onclick="openWindow(\''.$herf.$mans->id.'\')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
            })
        
            // joborder
            ->addColumn('joborder', fn($m) => $m->cont->job->nojoborder ?? '-')
            ->filterColumn('joborder', fn($q, $kw) => $q->where('j.nojoborder', 'like', "%$kw%"))
            ->orderColumn('joborder', fn($q, $order) => $q->orderBy('j.nojoborder', $order))
        
            // nm_angkut
            ->addColumn('nm_angkut', fn($m) => $m->cont->job->PLP->nm_angkut ?? '-')
            ->filterColumn('nm_angkut', fn($q, $kw) => $q->where('p.nm_angkut', 'like', "%$kw%"))
            ->orderColumn('nm_angkut', fn($q, $order) => $q->orderBy('p.nm_angkut', $order))
        
            // nocontainer
            ->addColumn('nocontainer', fn($m) => $m->cont->nocontainer ?? '-')
            ->filterColumn('nocontainer', fn($q, $kw) => $q->where('c.nocontainer', 'like', "%$kw%"))
            ->orderColumn('nocontainer', fn($q, $order) => $q->orderBy('c.nocontainer', $order))
        
            // size
            ->addColumn('size', fn($m) => $m->cont->size ?? '-')
            ->filterColumn('size', fn($q, $kw) => $q->where('c.size', 'like', "%$kw%"))
            ->orderColumn('size', fn($q, $order) => $q->orderBy('c.size', $order))
        
            // eta
            ->addColumn('eta', fn($m) => $m->cont->job->eta ?? '-')
            ->filterColumn('eta', fn($q, $kw) => $q->where('j.eta', 'like', "%$kw%"))
            ->orderColumn('eta', fn($q, $order) => $q->orderBy('j.eta', $order))
        
            // kd_tps_asal
            ->addColumn('kd_tps_asal', fn($m) => $m->cont->job->PLP->kd_tps_asal ?? '-')
            ->filterColumn('kd_tps_asal', fn($q, $kw) => $q->where('p.kd_tps_asal', 'like', "%$kw%"))
            ->orderColumn('kd_tps_asal', fn($q, $order) => $q->orderBy('p.kd_tps_asal', $order))
        
            // namaconsolidator
            ->addColumn('namaconsolidator', fn($m) => $m->cont->job->PLP->namaconsolidator ?? '-')
            ->filterColumn('namaconsolidator', fn($q, $kw) => $q->where('p.namaconsolidator', 'like', "%$kw%"))
            ->orderColumn('namaconsolidator', fn($q, $order) => $q->orderBy('p.namaconsolidator', $order))
        
            // nohbl
            ->addColumn('nohbl', fn($m) => $m->nohbl ?? '-')
            ->filterColumn('nohbl', fn($q, $kw) => $q->where('tmanifest.nohbl', 'like', "%$kw%"))
            ->orderColumn('nohbl', fn($q, $order) => $q->orderBy('tmanifest.nohbl', $order))
        
            // tgl_hbl
            ->addColumn('tgl_hbl', fn($m) => $m->tgl_hbl ?? '-')
            ->filterColumn('tgl_hbl', fn($q, $kw) => $q->where('tmanifest.tgl_hbl', 'like', "%$kw%"))
            ->orderColumn('tgl_hbl', fn($q, $order) => $q->orderBy('tmanifest.tgl_hbl', $order))
        
            // notally
            ->addColumn('notally', fn($m) => $m->notally ?? '-')
            ->filterColumn('notally', fn($q, $kw) => $q->where('tmanifest.notally', 'like', "%$kw%"))
            ->orderColumn('notally', fn($q, $order) => $q->orderBy('tmanifest.notally', $order))
        
            // customer
            ->addColumn('customer', fn($m) => $m->customer->name ?? '-')
            ->filterColumn('customer', fn($q, $kw) => $q->where('cust.name', 'like', "%$kw%"))
            ->orderColumn('customer', fn($q, $order) => $q->orderBy('cust.name', $order))
        
            // quantity
            ->addColumn('quantity', fn($m) => $m->quantity ?? '-')
            ->filterColumn('quantity', fn($q, $kw) => $q->where('tmanifest.quantity', 'like', "%$kw%"))
            ->orderColumn('quantity', fn($q, $order) => $q->orderBy('tmanifest.quantity', $order))
        
            // final_qty
            ->addColumn('final_qty', fn($m) => $m->final_qty ?? '-')
            ->filterColumn('final_qty', fn($q, $kw) => $q->where('tmanifest.final_qty', 'like', "%$kw%"))
            ->orderColumn('final_qty', fn($q, $order) => $q->orderBy('tmanifest.final_qty', $order))
        
            // packing
            ->addColumn('packingName', fn($m) => $m->packing->name ?? '-')
            ->addColumn('packingCode', fn($m) => $m->packing->code ?? '-')
        
            // desc
            ->addColumn('desc', fn($m) => '<textarea class="form-control" cols="3" readonly>'.($m->descofgoods ?? '-').'</textarea>')
        
            // weight, meas
            ->addColumn('weight', fn($m) => $m->weight ?? '-')
            ->addColumn('meas', fn($m) => $m->meas ?? '-')
        
            // packingTally
            ->addColumn('packingTally', fn($m) => $m->packingTally->name ?? '-')
        
            // noplp
            ->addColumn('noplp', fn($m) => $m->cont->job->noplp ?? '-')
            ->orderColumn('noplp', fn($q, $order) => $q->orderBy('j.noplp', $order))
        
            // tglPLP
            ->addColumn('tglPLP', fn($m) => $m->cont->job->ttgl_plp ?? '-')
        
            // no_bc11, tgl_bc11
            ->addColumn('no_bc11', fn($m) => $m->cont->job->tno_bc11 ?? '-')
            ->addColumn('tgl_bc11', fn($m) => $m->cont->job->ttgl_bc11 ?? '-')
        
            // tglmasuk, jammasuk
            ->addColumn('tglmasuk', fn($m) => $m->cont->tglmasuk ?? 'Belum Masuk')
            ->addColumn('jammasuk', fn($m) => $m->cont->jammasuk ?? 'Belum Masuk')
        
            // stripping
            ->addColumn('startstripping', fn($m) => $m->startstripping ?? '-')
            ->addColumn('endstripping', fn($m) => $m->endstripping ?? '-')
        
            // dokumen
            ->addColumn('dokumen', fn($m) => $m->dokumen->name ?? '-')
            ->addColumn('no_dok', fn($m) => $m->no_dok ?? '-')
            ->addColumn('tglDok', fn($m) => $m->tgl_dok ?? '-')
        
            // location
            ->addColumn('location', fn($m) => $m->mostItemsLocation()->Rack->name ?? 'Location not found')
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
        // Mulai Query Builder tanpa langsung `get()`
        // dd($request->all(), $request->end_date);
        $manifests = Manifest::orderBy('joborder_id', 'asc')->get();
    

        if ($request->has('filter') && $request->filter) {
            if ($request->filter == 'Tgl PLP') {
                $manifests = Manifest::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_plp', [$request->start_date, $request->end_date]);
                })->get();
            } elseif ($request->filter == 'Tgl Gate In') {
                $manifests = Manifest::whereBetween('tglmasuk', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc')->get();
            } elseif ($request->filter == 'Tgl Release') {
                $manifests = Manifest::whereBetween('tglrelease', [$request->start_date, $request->end_date])->orderBy('tglrelease', 'asc')->get();
            } elseif ($request->filter == 'Tgl BC 1.1') {
                $manifests = Manifest::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_bc11', [$request->start_date, $request->end_date]);
                })->get();
            } elseif ($request->filter == 'ETA') {
                $manifests = Manifest::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('eta', [$request->start_date, $request->end_date]);
                })->get();
            } elseif ($request->filter == 'akhir') {
                $manifests = Manifest::whereDate('tglmasuk', '<=', $request->end_date)->whereNull('tglrelease')->orWhereDate('tglrelease', '>', $request->end_date)->get();
            }
        }

        if ($request->has('container_id' && $request->container)) {
            $cont = Cont::find($request->container_id);
            if ($cont) {
                $manifests = $manifests->where('container_id', $cont->id);
            }
        }
        // Ambil data setelah semua filter diterapkan   
      
        // dd($manifests, $request->container_id, $request->all());

        // Cek hasil sebelum download (bisa dihapus jika sudah benar)
        

        $fileName = 'ReportManifest-' . $request->start_date . '-' . $request->end_date . '.xlsx';

        return Excel::download(new ReportManifest($manifests), $fileName);
    }

    public function generateManifestBeaCukaiNew(Request $request)
    {
        // Mulai Query Builder tanpa langsung `get()`
        // dd($request->all(), $request->end_date);
        $manifests = Manifest::orderBy('joborder_id', 'asc')->get();
    

        if ($request->has('filter') && $request->filter) {
            if ($request->filter == 'Tgl PLP') {
                $manifests = Manifest::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_plp', [$request->start_date, $request->end_date]);
                })->get();
            } elseif ($request->filter == 'Tgl Gate In') {
                $manifests = Manifest::whereBetween('tglmasuk', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc')->get();
            } elseif ($request->filter == 'Tgl Release') {
                $manifests = Manifest::whereBetween('tglrelease', [$request->start_date, $request->end_date])->orderBy('tglrelease', 'asc')->get();
            } elseif ($request->filter == 'Tgl BC 1.1') {
                $manifests = Manifest::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_bc11', [$request->start_date, $request->end_date]);
                })->get();
            } elseif ($request->filter == 'ETA') {
                $manifests = Manifest::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('eta', [$request->start_date, $request->end_date]);
                })->get();
            } elseif ($request->filter == 'akhir') {
                $manifests = Manifest::whereDate('tglmasuk', '<=', $request->end_date)->whereNull('tglrelease')->orWhereDate('tglrelease', '>', $request->end_date)->get();
            }
        }

        if ($request->has('container_id' && $request->container)) {
            $cont = Cont::find($request->container_id);
            if ($cont) {
                $manifests = $manifests->where('container_id', $cont->id);
            }
        }
        // Ambil data setelah semua filter diterapkan   
      
        // dd($manifests, $request->container_id, $request->all());

        // Cek hasil sebelum download (bisa dihapus jika sudah benar)
        

        $fileName = 'ReportManifestBeaCukaiNew-' . $request->start_date . '-' . $request->end_date . '.xlsx';

        return Excel::download(new ReportManifestBeaCukaiNew($manifests), $fileName);
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

        $masuk = Manifest::whereBetween('tglmasuk', [$start, $end])->get();
        
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

        $akhir = Manifest::where('tglmasuk', '<=', $end)->whereNull('tglrelease')->orWhere('tglrelease', '>', $end)->get();
        
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
