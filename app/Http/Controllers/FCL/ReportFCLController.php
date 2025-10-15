<?php

namespace App\Http\Controllers\fcl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Exports\fcl\plpCont;
use App\Exports\fcl\ReportBulanan;
use App\Exports\fcl\FormatJICT;
use App\Exports\fcl\ReportBeaCukaiNew;

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
        $data = Cont::query()
        ->select(
            'tcontainer_fcl.*',
            'j.nojoborder',
            'j.noplp',
            'j.ttgl_plp',
            'j.tno_bc11',
            'j.ttgl_bc11',
            DB::raw('p.nm_angkut as plp_nm_angkut'),
            DB::raw('p.kd_tps_asal as plp_kd_tps_asal'),
            DB::raw('p.namaconsolidator as plp_namaconsolidator')
        )
        ->leftJoin('tjoborder_fcl as j', 'j.id', '=', 'tcontainer_fcl.joborder_id')
         ->leftJoin('customer as cust', 'cust.id', '=', 'tcontainer_fcl.cust_id')
        ->leftJoin('tps_responplptujuanxml as p', 'p.id', '=', 'j.plp_id');

        // filtering by date (optional)
        $cont = $data;
        if ($request->has('filter') && $request->filter) {
            if ($request->filter == 'Tgl PLP') {
                $cont->whereBetween('j.ttgl_plp', [$request->start_date, $request->end_date])
                     ->orderBy('j.ttgl_plp', 'asc');
            } elseif ($request->filter == 'Tgl Gate In') {
                $cont->whereBetween('tcontainer_fcl.tglmasuk', [$request->start_date, $request->end_date])
                     ->orderBy('tcontainer_fcl.tglmasuk', 'asc');
            } elseif ($request->filter == 'Tgl Gate Out') {
                $cont->whereBetween('tcontainer_fcl.tglkeluar', [$request->start_date, $request->end_date])
                     ->orderBy('tcontainer_fcl.tglmasuk', 'asc');
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
            ->filterColumn('nocontainer', fn($q, $k) => $q->where('tcontainer_fcl.nocontainer', 'like', "%{$k}%"))
            ->orderColumn('nocontainer', 'tcontainer_fcl.nocontainer $1')

            // ctrType (dengan warna)
            ->addColumn('ctrType', function($cont){
                $color = $cont->ctr_type == 'BB' ? 'background-color:rgb(167, 40, 40); color: white;' : '';
                return '<span style="'.$color.'; padding: 5px; border-radius: 5px;">'.$cont->ctr_type.'</span>';
            })
            ->filterColumn('ctrType', fn($q, $k) => $q->where('tcontainer_fcl.ctr_type', 'like', "%{$k}%"))
            ->orderColumn('ctrType', 'tcontainer_fcl.ctr_type $1')

            // classType
            ->addColumn('classType', function($cont){
                $color = $cont->ctr_type == 'BB' ? 'background-color:rgb(167, 40, 40); color: white;' : '';
                return '<span style="'.$color.'; padding: 5px; border-radius: 5px;">'.$cont->type_class.'</span>';
            })
            ->filterColumn('classType', fn($q, $k) => $q->where('tcontainer_fcl.type_class', 'like', "%{$k}%"))
            ->orderColumn('classType', 'tcontainer_fcl.type_class $1')

            // size
            ->editColumn('size', fn($cont) => $cont->size ?? '-')
            ->filterColumn('size', fn($q, $k) => $q->where('tcontainer_fcl.size', 'like', "%{$k}%"))
            ->orderColumn('size', 'tcontainer_fcl.size $1')

            // eta
            ->editColumn('eta', fn($cont) => $cont->eta ?? '-')
            ->filterColumn('eta', fn($q, $k) => $q->where('tcontainer_fcl.eta', 'like', "%{$k}%"))
            ->orderColumn('eta', 'tcontainer_fcl.eta $1')

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
            ->filterColumn('nobl', fn($q, $k) => $q->where('tcontainer_fcl.nobl', 'like', "%{$k}%"))
            ->orderColumn('nobl', 'tcontainer_fcl.nobl $1')

            // tglBL
            ->editColumn('tglBL', fn($cont) => $cont->tgl_bl_awb ?? '-')
            ->filterColumn('tglBL', fn($q, $k) => $q->where('tcontainer_fcl.tgl_bl_awb', 'like', "%{$k}%"))
            ->orderColumn('tglBL', 'tcontainer_fcl.tgl_bl_awb $1')

            // nopol
            ->editColumn('nopol', fn($cont) => $cont->nopol ?? '-')
            ->filterColumn('nopol', fn($q, $k) => $q->where('tcontainer_fcl.nopol', 'like', "%{$k}%"))
            ->orderColumn('nopol', 'tcontainer_fcl.nopol $1')

            // tglmasuk
            ->editColumn('tglmasuk', fn($cont) => $cont->tglmasuk ?? 'Belum Masuk')
            ->filterColumn('tglmasuk', fn($q, $k) => $q->where('tcontainer_fcl.tglmasuk', 'like', "%{$k}%"))
            ->orderColumn('tglmasuk', 'tcontainer_fcl.tglmasuk $1')

            // jammasuk
            ->editColumn('jammasuk', fn($cont) => $cont->jammasuk ?? 'Belum Masuk')
            ->filterColumn('jammasuk', fn($q, $k) => $q->where('tcontainer_fcl.jammasuk', 'like', "%{$k}%"))
            ->orderColumn('jammasuk', 'tcontainer_fcl.jammasuk $1')

            // tglstripping
            ->editColumn('tglstripping', fn($cont) => $cont->tglstripping ?? 'Belum Stripping')
            ->filterColumn('tglstripping', fn($q, $k) => $q->where('tcontainer_fcl.tglstripping', 'like', "%{$k}%"))
            ->orderColumn('tglstripping', 'tcontainer_fcl.tglstripping $1')

            // jamstripping
            ->editColumn('jamstripping', fn($cont) => $cont->jamstripping ?? 'Belum Stripping')
            ->filterColumn('jamstripping', fn($q, $k) => $q->where('tcontainer_fcl.jamstripping', 'like', "%{$k}%"))
            ->orderColumn('jamstripping', 'tcontainer_fcl.jamstripping $1')

            // nopol_mty
            ->editColumn('nopol_mty', fn($cont) => $cont->nopol_mty ?? '-')
            ->filterColumn('nopol_mty', fn($q, $k) => $q->where('tcontainer_fcl.nopol_mty', 'like', "%{$k}%"))
            ->orderColumn('nopol_mty', 'tcontainer_fcl.nopol_mty $1')

            // tglkeluar
            ->editColumn('tglkeluar', fn($cont) => $cont->tglkeluar ?? 'Belum keluar')
            ->filterColumn('tglkeluar', fn($q, $k) => $q->where('tcontainer_fcl.tglkeluar', 'like', "%{$k}%"))
            ->orderColumn('tglkeluar', 'tcontainer_fcl.tglkeluar $1')

            // jamkeluar
            ->editColumn('jamkeluar', fn($cont) => $cont->jamkeluar ?? 'Belum keluar')
            ->filterColumn('jamkeluar', fn($q, $k) => $q->where('tcontainer_fcl.jamkeluar', 'like', "%{$k}%"))
            ->orderColumn('jamkeluar', 'tcontainer_fcl.jamkeluar $1')

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
        $data['photos'] = Photo::where('master_id', $id)->where('type', '=', 'fcl')->get();
        // dd($data['photos']);
        return view('lcl.report.photoCont', $data);
    }

    public function formatStandar(Request $request)
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

        $fileName = 'ReportContainer-FULL.xlsx' ;
        return Excel::download(new plpCont($conts), $fileName);
    }

    public function formatBeacukai(Request $request)
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

        $fileName = 'ReportContainer-beacukai'.$start_date.'-'.$end_date.'.xlsx' ;
        return Excel::download(new ReportBulanan($conts, $judul), $fileName);
    }

    public function formatBeacukaiNew(Request $request)
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

        $fileName = 'ReportContainer-beacukai'.$start_date.'-'.$end_date.'.xlsx' ;
        return Excel::download(new ReportBeaCukaiNew($conts, $judul), $fileName);
    }

    public function formatJict(Request $request)
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

        // dd($conts);

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $tanggalJudul =  $this->formatDateRange($start_date, $end_date);

        // dd($tanggalJudul);

        $judul = 'Laporan Delivery Container FCL (Ex OBX TERMINAL JICT) '. $tanggalJudul;

        $fileName = 'Delivery Petikemas FCL Ex PLP JICT ('.$start_date.'-'.$end_date.').xlsx' ;
        // dd($fileName);
        return Excel::download(new FormatJICT($conts, $judul), $fileName);
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

    // Daily Report
    public function indexDaily(Request $request)
    {
        $data['title'] = "Report Daily - FCL";

        $start = $request->input('start_date') ?? Carbon::now()->toDateString();
        $end = $request->input('end_date') ?? Carbon::now()->toDateString();

        $data['start'] = $start;
        $data['end'] = $end;

        $awal = Cont::whereNotNull('tglmasuk')->whereDate('tglmasuk', '<=', $start)
        ->where(function ($query) use ($start) {
            $query->whereDate('tglkeluar', '>=', $start)
                  ->orWhereNull('tglkeluar');
        })->get();
        
        $awalDry = $awal->where('ctr_type', 'DRY');
        $awalBB = $awal->where('ctr_type', 'BB');
        $awalOH = $awal->where('ctr_type', 'OH');
        $data['awal'] = $awal;
        $data['jumlahAwal'] = $awal->count();
        $data['jumlahAwalDry'] = $awalDry->count();
        $data['jumlahAwalBB'] = $awalBB->count();
        $data['jumlahAwalOH'] = $awalOH->count();

        $data['quantityAwal'] = $awal->sum('quantity');
        $data['quantityAwalDry'] = $awalDry->sum('quantity');
        $data['quantityAwalBB'] = $awalBB->sum('quantity');
        $data['quantityAwalOH'] = $awalOH->sum('quantity');

        $data['tonaseAwal'] = $awal->sum('weight');
        $data['tonaseAwalDry'] = $awalDry->sum('weight');
        $data['tonaseAwalBB'] = $awalBB->sum('weight');
        $data['tonaseAwalOH'] = $awalOH->sum('weight');

        $data['volumeAwal'] = $awal->sum('teus');
        $data['volumeAwalDry'] = $awalDry->sum('teus');
        $data['volumeAwalBB'] = $awalBB->sum('teus');
        $data['volumeAwalOH'] = $awalOH->sum('teus');

        $masuk = Cont::whereBetween('tglmasuk', [$start, $end])->get();
        $masukDry = $masuk->where('ctr_type', 'DRY');
        $masukBB = $masuk->where('ctr_type', 'BB');
        $masukOH = $masuk->where('ctr_type', 'OH');

        $data['masuk'] = $masuk;
        $data['jumlahMasuk'] = $masuk->count();
        $data['jumlahMasukDry'] = $masukDry->count();
        $data['jumlahMasukBB'] = $masukBB->count();
        $data['jumlahMasukOH'] = $masukOH->count();

        $data['quantityMasuk'] = $masuk->sum('quantity');
        $data['quantityMasukDry'] = $masukDry->sum('quantity');
        $data['quantityMasukBB'] = $masukBB->sum('quantity');
        $data['quantityMasukOH'] = $masukOH->sum('quantity');

        $data['tonaseMasuk'] = $masuk->sum('weight');
        $data['tonaseMasukDry'] = $masukDry->sum('weight');
        $data['tonaseMasukBB'] = $masukBB->sum('weight');
        $data['tonaseMasukOH'] = $masukOH->sum('weight');

        $data['volumeMasuk'] = $masuk->sum('teus');
        $data['volumeMasukDry'] = $masukDry->sum('teus');
        $data['volumeMasukBB'] = $masukBB->sum('teus');
        $data['volumeMasukOH'] = $masukOH->sum('teus');

        $keluar = Cont::whereBetween('tglkeluar', [$start, $end])->get();
        $keluarDry = $keluar->where('ctr_type', 'DRY');
        $keluarBB = $keluar->where('ctr_type', 'BB');
        $keluarOH = $keluar->where('ctr_type', 'OH');
        $data['keluar'] = $keluar;

        $data['jumlahKeluar'] = $keluar->count();
        $data['jumlahKeluarDry'] = $keluarDry->count();
        $data['jumlahKeluarBB'] = $keluarBB->count();
        $data['jumlahKeluarOH'] = $keluarOH->count();
        
        $data['quantityKeluar'] = $keluar->sum('quantity');
        $data['quantityKeluarDry'] = $keluarDry->sum('quantity');
        $data['quantityKeluarBB'] = $keluarBB->sum('quantity');
        $data['quantityKeluarOH'] = $keluarOH->sum('quantity');
        
        $data['tonaseKeluar'] = $keluar->sum('weight');
        $data['tonaseKeluarDry'] = $keluarDry->sum('weight');
        $data['tonaseKeluarBB'] = $keluarBB->sum('weight');
        $data['tonaseKeluarOH'] = $keluarOH->sum('weight');

        $data['volumeKeluar'] = $keluar->sum('teus');
        $data['volumeKeluarDry'] = $keluarDry->sum('teus');
        $data['volumeKeluarBB'] = $keluarBB->sum('teus');
        $data['volumeKeluarOH'] = $keluarOH->sum('teus');

        $akhir = Cont::whereDate('tglmasuk', '<=', $end)->whereNull('tglkeluar')->orWhereDate('tglkeluar', '>', $end)->get();
        // ->where(function ($query) use ($end) {
        //     $query->WhereNull('tglkeluar')
        //           ->orWhereDate('tglkeluar', '>=', $end);
        // })->get();
        $akhirDry = $akhir->where('ctr_type', 'DRY');
        $akhirBB = $akhir->where('ctr_type', 'BB');
        $akhirOH = $akhir->where('ctr_type', 'OH');
        $data['akhir'] = $akhir;
        $data['jumlahAkhir'] = $akhir->count();
        $data['jumlahAkhirDry'] = $akhirDry->count();
        $data['jumlahAkhirBB'] = $akhirBB->count();
        $data['jumlahAkhirOH'] = $akhirOH->count();

        $data['quantityAkhir'] = $akhir->sum('quantity');
        $data['quantityAkhirDry'] = $akhirDry->sum('quantity');
        $data['quantityAkhirBB'] = $akhirBB->sum('quantity');
        $data['quantityAkhirOH'] = $akhirOH->sum('quantity');

        $data['tonaseAkhir'] = $akhir->sum('weight');
        $data['tonaseAkhirDry'] = $akhirDry->sum('weight');
        $data['tonaseAkhirBB'] = $akhirBB->sum('weight');
        $data['tonaseAkhirOH'] = $akhirOH->sum('weight');
        
        $data['volumeAkhir'] = $akhir->sum('teus');
        $data['volumeAkhirDry'] = $akhirDry->sum('teus');
        $data['volumeAkhirBB'] = $akhirBB->sum('teus');
        $data['volumeAkhirOH'] = $akhirOH->sum('teus');

        return view('fcl.report.daily', $data);
    }

    public function dataContDaily(Request $request)
    {
        $start = $request->input('start') ?? Carbon::now()->toDateString();
        $end = $request->input('end') ?? Carbon::now()->toDateString();

        // var_dump($start);
        // die;

        switch ($request->filter) {
            case 'masuk':
                $cont = Cont::whereBetween('tglmasuk', [$start, $end])->get();
                break;
            case 'keluar':
                $cont = Cont::whereBetween('tglkeluar', [$start, $end])->get();
                break;
            case 'total';
                $cont = Cont::whereDate('tglmasuk', '<=', $end)->whereNull('tglkeluar')->orWhereDate('tglkeluar', '>', $end)->get();
                break;
            default:
                $cont = Cont::orderBy('id', 'desc')->get(); 
                break;
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

    public function formatStandarAkhir(Request $request)
    {
        $cont = Cont::whereDate('tglmasuk', '<=', $request->end_date)->whereNull('tglkeluar')->orWhereDate('tglkeluar', '>', $request->end_date)->get();
        
        $conts = $cont;
        // dd($request->all(), $cont, $conts);
        $fileName = 'ReportContainer-FULL.xlsx' ;
        return Excel::download(new plpCont($conts), $fileName);
    }

    public function formatBeacukaiAkhir(Request $request)
    {
        $cont = Cont::whereDate('tglmasuk', '<=', $request->end_date)->whereNull('tglkeluar')->orWhereDate('tglkeluar', '>', $request->end_date)->get();

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $tanggalJudul =  $this->formatDateRange($start_date, $end_date);

        $conts = $cont;;

        $judul = 'Laporan Bulanan '. $tanggalJudul;

        $fileName = 'ReportContainer-beacukai'.$start_date.'-'.$end_date.'.xlsx' ;
        return Excel::download(new ReportBulanan($conts, $judul), $fileName);
    }

}
