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
        $cont = Cont::orderBy('joborder_id', 'desc');
        if ($request->has('filter') && $request->filter) {
            if ($request->filter == 'Tgl PLP') {
                $cont = Cont::whereHas('job', function ($query) use ($request) {
                    $query->whereBetween('ttgl_plp', [$request->start_date, $request->end_date])->orderBy('ttgl_plp', 'asc');
                });
            } elseif ($request->filter == 'Tgl Gate In') {
                $cont = Cont::whereBetween('tglmasuk', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc');
            } elseif ($request->filter == 'Tgl Gate Out') {
                $cont = Cont::whereBetween('tglbuangmty', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc');
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
        ->addColumn('tglstripping', function($cont){
            return $cont->tglstripping ?? 'Belum Stripping';
        })
        ->addColumn('jamstripping', function($cont){
            return $cont->jamstripping ?? 'Belum Stripping';
        })
        ->addColumn('nopol_mty', function($cont){
            return $cont->nopol_mty ?? '-';
        })
        ->addColumn('tglkeluar', function($cont){
            return $cont->tglbuangmty ?? 'Belum keluar';
        })
        ->addColumn('jamkeluar', function($cont){
            return $cont->jambuangmty ?? 'Belum keluar';
        })
        ->addColumn('lamaHari', function($cont){
            if (!$cont->tglmasuk) {
                $lamaHari = 'Belum Masuk';
                $longStay = 'N';
            } else {
                $lamaHari = Carbon::parse($cont->tglmasuk)->diffInDays($cont->tglbuangmty ?? now()) . ' hari';
    
                if (Carbon::parse($cont->tglmasuk)->diffInDays($cont->tglbuangmty ?? now()) >= 25 ) {
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
                $longStay = Carbon::parse($cont->tglmasuk)->diffInDays($cont->tglbuangmty ?? now()) >= 25 ? 'Y' : 'N';
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
      $mans = Manifest::orderBy('joborder_id', 'asc');

      if ($request->has('filter') && $request->filter) {
        if ($request->filter == 'Tgl PLP') {
            $mans = Manifest::whereHas('job', function ($query) use ($request) {
                $query->whereBetween('ttgl_plp', [$request->start_date, $request->end_date])->orderBy('ttgl_plp', 'asc');
            });
        } elseif ($request->filter == 'Tgl Gate In') {
            $mans = Manifest::whereBetween('tglmasuk', [$request->start_date, $request->end_date])->orderBy('tglmasuk', 'asc');
        } elseif ($request->filter == 'Tgl Release') {
            $mans = Manifest::whereBetween('tglrelease', [$request->start_date, $request->end_date])->orderBy('tglrelease', 'asc');
        } elseif ($request->filter == 'Tgl BC 1.1') {
            $mans = Manifest::whereHas('job', function ($query) use ($request) {
                $query->whereBetween('ttgl_bc11', [$request->start_date, $request->end_date])->orderBy('ttgl_bc11', 'asc');
            });
        } elseif ($request->filter == 'ETA') {
            $mans = Manifest::whereHas('job', function ($query) use ($request) {
                $query->whereBetween('eta', [$request->start_date, $request->end_date])->orderBy('eta', 'asc');
            });
        }
      }

      if ($request->has('container_id') && $request->container_id) {
        $mans->where('container_id', $request->container_id);
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
        // Mulai Query Builder tanpa langsung `get()`
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
            }
        }

        $cont = Cont::find($request->container_id);
        if ($cont) {
            $manifests = $manifests->where('container_id', $cont->id);
        }
        // Ambil data setelah semua filter diterapkan   
      
        // dd($manifests, $request->container_id, $request->all());

        // Cek hasil sebelum download (bisa dihapus jika sudah benar)
        

        $fileName = 'ReportManifest-' . $request->start_date . '-' . $request->end_date . '.xlsx';

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
