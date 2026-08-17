<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use App\Models\Manifest;
use App\Models\Container as ContL;
use App\Models\ContainerFCL as ContF;
use App\Models\KapasitasGudang;

use App\Models\BarcodeGate as gate;

class BeaCukaiController extends Controller
{
    public function YorKapasitas(Request $request)
    {
        $fasilitas = [];
        // Fasilitas FCL
        $fcl = $this->fasilitasFCL();
        $fasilitas[] = $fcl;
        
        $lcl = $this->fasilitasLCL();
        $fasilitas[] = $lcl;

        $manifest = $this->fasilitasManifest();
        $fasilitas[] = $manifest;
        return response()->json([
            'header'=>[
                'kodeTps'=>'1MUT',
                'refNumber'=>Str::uuid(),
                'waktuPencatatan'=>now()->format('d-m-Y H:i:s')
            ],
            'fasilitas'=>$fasilitas
        ]);
    }

    private function fasilitasFCL()
    {
        $totalTeus = 2000;
        $container = ContF::whereNotNull('tglmasuk')->whereNull('tglkeluar')->get();
        $terisi = $container->sum('teus');
        $tersedia = 2000 - $terisi;
        $stok = $container->count();
        $persentase = round(($terisi / 2000) * 100, 2);

        $rincian = [
            [
                'key' => 'ukuran_20ft',
                'label' => '20 Feet',
                'nilai' => $container->where('size', '20')->sum('teus'),
                'satuan' => 'TEUS',
            ],
            [
                'key' => 'ukuran_40ft',
                'label' => '40 Feet',
                'nilai' => $container->where('size', '40')->sum('teus'),
                'satuan' => 'TEUS',
            ],
            [
                'key' => 'ukuran_45ft',
                'label' => '45 Feet',
                'nilai' => $container->where('size', '45')->sum('teus'),
                'satuan' => 'TEUS',
            ],
        ];

        return $data = [
            "jenisFasilitas"=> "LAPANGAN",
            "kodeGudang"=> "FCL",
            "namaFasilitas"=> "Lapangan FCL",
            "kategoriFasilitas"=> "UMUM",
            "kapasitasTotal"=> 2000,
            "kapasitasTerisi"=> $terisi,
            "kapasitasTersedia"=> $tersedia,
            "okupansiPersen"=> $persentase,
            "satuanKapasitas"=> "TEUS",
            "stokBarang"=> $stok,
            "stokBarangSatuan"=> "TEUS",
            "rincian" => $rincian
        ];
    }

    private function fasilitasLCL()
    {
        $totalTeus = 20;
        $container = ContL::whereNotNull('tglmasuk')->whereNull('tglkeluar')->get();
        $terisi = $container->sum('teus');
        $tersedia = 20 - $terisi;
        $stok = $container->count();
        $persentase = round(($terisi / 20) * 100, 2);

        $rincian = [
            [
                'key' => 'ukuran_20ft',
                'label' => '20 Feet',
                'nilai' => $container->where('size', '20')->sum('teus'),
                'satuan' => 'TEUS',
            ],
            [
                'key' => 'ukuran_40ft',
                'label' => '40 Feet',
                'nilai' => $container->where('size', '40')->sum('teus'),
                'satuan' => 'TEUS',
            ],
            [
                'key' => 'ukuran_45ft',
                'label' => '45 Feet',
                'nilai' => $container->where('size', '45')->sum('teus'),
                'satuan' => 'TEUS',
            ],
        ];

        return $data = [
            "jenisFasilitas"=> "LAPANGAN",
            "kodeGudang"=> "LCL",
            "namaFasilitas"=> "Lapangan LCL",
            "kategoriFasilitas"=> "UMUM",
            "kapasitasTotal"=> 20,
            "kapasitasTerisi"=> $terisi,
            "kapasitasTersedia"=> $tersedia,
            "okupansiPersen"=> $persentase,
            "satuanKapasitas"=> "TEUS",
            "stokBarang"=> $stok,
            "stokBarangSatuan"=> "TEUS",
            "rincian" => $rincian
        ];
    }


    private function fasilitasManifest()
    {
        $totalMeas = KapasitasGudang::sum('kapasitas');
        $container = Manifest::whereNotNull('tglstripping')->whereNull('tglrelease')->get();
        $terisi = round($container->sum('meas'), 2);
        $tersedia = $totalMeas - $terisi;
        $stok = $container->count();
        $persentase = round(($terisi / $totalMeas) * 100, 2);

        $longStay = $container->filter(function ($item) {
            return Carbon::parse($item->tglstripping)->diffInDays(now()) >= 25;
        });

        $nonLongStay = $container->filter(function ($item) {
            return Carbon::parse($item->tglstripping)->diffInDays(now()) < 25;
        });
        $rincian = [
           [
                "key" => "non_long_stay",
                "label" => "Non Long Stay",
                "nilai" => round($nonLongStay->sum('meas'), 2),
                "satuan" => "M3"
            ],
            [
                "key" => "long_stay",
                "label" => "Long Stay",
                "nilai" => round($longStay->sum('meas'), 2),
                "satuan" => "M3"
            ]
        ];

        return $data = [
            "jenisFasilitas"=> "GUDANG",
            "kodeGudang"=> "LKB",
            "namaFasilitas"=> "GUDANG LKB",
            "kategoriFasilitas"=> "UMUM",
            "kapasitasTotal"=> $totalMeas,
            "kapasitasTerisi"=> $terisi,
            "kapasitasTersedia"=> $tersedia,
            "okupansiPersen"=> $persentase,
            "satuanKapasitas"=> "M3",
            "stokBarang"=> $stok,
            "stokBarangSatuan"=> "HBL",
            "rincian" => $rincian
        ];
    }


    public function GateAktifitas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggalMulai' => 'required|date_format:d-m-Y',
            'tanggalAkhir' => 'required|date_format:d-m-Y',
            'activity'     => 'nullable|in:IN,OUT',
            'page'         => 'nullable|integer|min:1',
            'pageSize'     => 'nullable|integer|min:1|max:500',
            'nomorKontainer' => 'nullable|string|max:11',
        ]);

        if ($validator->fails()) {

            if (
                $validator->errors()->has('tanggalMulai') ||
                $validator->errors()->has('tanggalAkhir')
            ) {
                return response()->json([
                    'error' => 'MISSING_DATE_RANGE',
                    'message' => 'Parameter tanggalMulai dan tanggalAkhir wajib diisi',
                    'timestamp' => now()->format('d-m-Y H:i:s')
                ], 400);
            }

            return response()->json([
                'error' => 'INVALID_PARAMETER',
                'message' => $validator->errors()->first(),
                'timestamp' => now()->format('d-m-Y H:i:s')
            ], 400);
        }

        $start = Carbon::createFromFormat('d-m-Y', $request->tanggalMulai)->startOfDay();
        $end = Carbon::createFromFormat('d-m-Y', $request->tanggalAkhir)->endOfDay();
        $page = $request->page ?? 1;
        $pageSize = $request->pageSize ?? 100;
        $result = $this->getAktifitasData($start, $end, $pageSize, $page);
        return response()->json([
            'header' => [
                'kodeTps' => '1MUT',
                'kodeGudang' => 'INTI',
                'refNumber' => Str::uuid(),
                'waktuPencatatan' => now()->format('d-m-Y H:i:s'),

                'totalData' => $result['pagination']['totalData'],
                'page' => $result['pagination']['page'],
                'pageSize' => $result['pagination']['pageSize'],
                'totalPage' => $result['pagination']['totalPage'],
            ],

            'aktivitas' => $result['data']
        ]);
    }

    private function getAktifitasData($start, $end, $pageSize, $page)
    {
        $gates = Gate::where(function ($query) use ($start, $end) {
            $query->whereBetween('time_in', [$start, $end])->orWhereBetween('time_out', [$start, $end]);
            })->paginate($pageSize, ['*'], 'page', $page);

        $data = [];
        foreach ($gates as $gate) {
            if ($gate->ref_type == 'Manifest') {
                $data[]= [
                    "idEvent"=> $gate->barcode,
                    "nomorKontainer"=> $gate->manifest->nohbl ?? '',
                    "size"=> round($gate->manifest->meas, 2) ?? '',
                    "category"=> $gate->ref_type,
                    "jenisKegiatan"=> "IMPORT",
                    "activity"=> $gate->ref_action == 'get' ? 'IN' : 'OUT',
                    "waktuAktivitas"=> $gate->ref_action == 'get' ? $gate->time_in : $gate->time_out,
                    "kodeFasilitas"=> $gate->ref_type,
                    "jenisFasilitas"=> "GUDANG",
                    "nomorTruk"=> $gate->manifest->nopol_releaase ?? '',
                    "segel"=> null,
                    "kondisiKontainer"=> 'FULL',
                    "referensiDokumen"=> [
                        "jenisDokumen"=> $gate->manifest->dokumen->name ?? '',
                        "nomorDokumen"=> $gate->manifest->no_dok ?? '',
                        "tanggalDokumen"=> $gate->manifest->tgl_dok ?? ''
                    ]
                ];
            }else {
                if ($gate->ref_action == 'get') {
    
                    $referensiDokumen = [
                        "jenisDokumen" => "PLP",
                        "nomorDokumen" => $gate->cont->job->noplp ?? '',
                        "tanggalDokumen" => $gate->cont->job->ttgl_plp ?? '',
                    ];
    
                } else {
    
                    if ($gate->ref_type == 'FCL') {
    
                        $referensiDokumen = [
                            "jenisDokumen" => $gate->cont->dokumen->name ?? '',
                            "nomorDokumen" => $gate->cont->no_dok ?? '',
                            "tanggalDokumen" => $gate->cont->tgl_dok ?? '',
                        ];
    
                    } else {
    
                        $referensiDokumen = [
                            "jenisDokumen" => "PLP",
                            "nomorDokumen" => $gate->cont->job->noplp ?? '',
                            "tanggalDokumen" => $gate->cont->job->ttgl_plp ?? '',
                        ];
                    }
                }
    
                $data[] = [
                    "idEvent" => $gate->barcode,
                    "nomorKontainer" => $gate->cont->nocontainer ?? '',
                    "size" => $gate->cont->size ?? '',
                    "category" => $gate->ref_type,
                    "jenisKegiatan" => "IMPORT",
                    "activity" => $gate->ref_action == 'get' ? 'IN' : 'OUT',
                    "waktuAktivitas" => $gate->ref_action == 'get'
                        ? $gate->time_in
                        : $gate->time_out,
                    "kodeFasilitas" => $gate->ref_type,
                    "jenisFasilitas" => "LAPANGAN",
                    "nomorTruk" => $gate->ref_action == 'get'
                        ? $gate->cont->nopol
                        : $gate->cont->nopol_mty,
                    "segel" => $gate->cont->seal->code ?? '',
                    "kondisiKontainer" => $gate->ref_action == 'get'
                        ? 'FULL'
                        : 'EMPTY',
                    "referensiDokumen" => $referensiDokumen,
                ];
            }

        }

        return [
            'data' => $data,
            'pagination' => [
                'totalData' => $gates->total(),
                'page' => $gates->currentPage(),
                'pageSize' => $gates->perPage(),
                'totalPage' => $gates->lastPage(),
            ]
        ];
    }

    public function kontainer($nomorKontainer)
    {
        $container = ContF::where('nocontainer', $nomorKontainer)->first();
        if ($container) {
            $type = "7";
        }
        if (!$container) {
            $container = ContL::where('nocontainer', $nomorKontainer)->first();
            $type = "8";
        }

        if ($container) {
            $history = [];
            if ($container->tglmasuk !== null) {
                $in = [
                    "kodeKegiatan"=> 5,
                    "waktuKegiatan"=> Carbon::parse($container->tglmasuk . ' ' . $container->jammasuk)->format('d-m-Y H:i:s'),
                    "block"=> null,
                    "row"=> null,
                    "slot"=> null,
                    "tier"=> null,
                    "nomorPolisi"=> $container->nopol,
                    "gate"=> 'IN',
                    "stid"=> null,
                    "dokumen"=> [
                      "kodeDokumen"=> "3",
                      "nomorDokumen"=> $container->job->noplp,
                      "tanggalDokumen"=> $container->job->ttgl_plp
                    ]
                ];
                $history[] = $in;
            }

            if ($container->tglkeluar !== null) {
                $out = [
                    "kodeKegiatan"=> 6,
                    "waktuKegiatan"=> Carbon::parse($container->tglkeluar . ' ' . $container->jamkeluar)->format('d-m-Y H:i:s'),
                    "block"=> null,
                    "row"=> null,
                    "slot"=> null,
                    "tier"=> null,
                    "nomorPolisi"=> $container->nopol_mty,
                    "gate"=> 'OUT',
                    "stid"=> null,
                    "dokumen"=> [
                      "kodeDokumen"=> $container->kd_dok_inout ?? '3',
                      "nomorDokumen"=> $container->no_dok ?? $container->job->noplp,
                      "tanggalDokumen"=> $container->tgl_dok ?? $container->job->ttgl_plp
                    ]
                ];
                $history[] = $out;
            }
            $data = [
                "kodeTps"=> "1MUT",
                "kodeGudang"=> "INTI",
                "nomorKontainer"=> $nomorKontainer,
                "ukuranKontainer"=> $container->size,
                "jenisKontainer"=> $type,
                "nomorBlAwb"=> $container->nobl ?? '',
                "tanggalBlAwb"=> $container->tgl_bl_awb ?? '',
                "history" => [
                    $history
                ]
            ];

            // $data = [
            //     $header,
            //     'history'
            // ];
            return response()->json([
                "status"=> 200,
                "message"=> "Data Ditemukan",
                "nomorKontainer"=> $nomorKontainer,
                "data" => $data
            ]);
        }else {
            return response()->json([
                "status"=> 404,
                "message"=> "Nomor kontainer tidak ditemukan",
                "nomorKontainer"=> $nomorKontainer
            ]);
        }


    }

    public function kontainerHistory($nomorKontainer)
    {
        $container = ContF::where('nocontainer', $nomorKontainer)->first();
        if ($container) {
            $type = "7";
        }
        if (!$container) {
            $container = ContL::where('nocontainer', $nomorKontainer)->first();
            $type = "8";
        }

        if ($container) {
            $history = [];
            if ($container->tglmasuk !== null) {
                $in = [
                    "kodeKegiatan"=> 5,
                    "waktuKegiatan"=> Carbon::parse($container->tglmasuk . ' ' . $container->jammasuk)->format('d-m-Y H:i:s'),
                    "block"=> null,
                    "row"=> null,
                    "slot"=> null,
                    "tier"=> null,
                    "nomorPolisi"=> $container->nopol,
                    "gate"=> 'IN',
                    "stid"=> null,
                    "dokumen"=> [
                      "kodeDokumen"=> "3",
                      "nomorDokumen"=> $container->job->noplp,
                      "tanggalDokumen"=> $container->job->ttgl_plp
                    ]
                ];
                $history[] = $in;
            }

            if ($container->tglkeluar !== null) {
                $out = [
                    "kodeKegiatan"=> 6,
                    "waktuKegiatan"=> Carbon::parse($container->tglkeluar . ' ' . $container->jamkeluar)->format('d-m-Y H:i:s'),
                    "block"=> null,
                    "row"=> null,
                    "slot"=> null,
                    "tier"=> null,
                    "nomorPolisi"=> $container->nopol_mty,
                    "gate"=> 'OUT',
                    "stid"=> null,
                    "dokumen"=> [
                      "kodeDokumen"=> $container->kd_dok_inout ?? '3',
                      "nomorDokumen"=> $container->no_dok ?? $container->job->noplp,
                      "tanggalDokumen"=> $container->tgl_dok ?? $container->job->ttgl_plp
                    ]
                ];
                $history[] = $out;
            }
            $data = [
                $history
            ];

            // $data = [
            //     $header,
            //     'history'
            // ];
            return response()->json([
                "status"=> 200,
                "message"=> "Data Ditemukan",
                "nomorKontainer"=> $nomorKontainer,
                "data" => $data
            ]);
        }else {
            return response()->json([
                "status"=> 404,
                "message"=> "Nomor kontainer tidak ditemukan",
                "nomorKontainer"=> $nomorKontainer
            ]);
        }


    }

    public function kontainerTanggal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggalMulai' => 'required|date_format:d-m-Y',
            'tanggalAkhir' => 'required|date_format:d-m-Y',
            'page' => 'nullable|integer|min:1',
            'pageSize' => 'nullable|integer|min:1|max:500',
        ]); 

        if ($validator->fails()) {  

            if (
                $validator->errors()->has('tanggalMulai') ||
                $validator->errors()->has('tanggalAkhir')
            ) {
                return response()->json([
                    'error' => 'MISSING_DATE_RANGE',
                    'message' => 'Parameter tanggalMulai dan tanggalAkhir wajib diisi',
                    'timestamp' => now()->format('d-m-Y H:i:s')
                ], 400);
            }   

            return response()->json([
                'error' => 'INVALID_PARAMETER',
                'message' => $validator->errors()->first(),
                'timestamp' => now()->format('d-m-Y H:i:s')
            ], 400);
        }   

        $start = Carbon::createFromFormat(
            'd-m-Y',
            $request->tanggalMulai
        )->startOfDay();    

        $end = Carbon::createFromFormat(
            'd-m-Y',
            $request->tanggalAkhir
        )->endOfDay();  

        $page = $request->page ?? 1;
        $pageSize = $request->pageSize ?? 100;  

        $result = $this->getKontainerTanggalData(
            $start,
            $end,
            $pageSize,
            $page
        );  

        return response()->json([
            'header' => [
                'kodeTps' => '1MUT',
                'kodeGudang' => 'INTI',
                'refNumber' => Str::uuid(),
                'waktuPencatatan' => now()->format('d-m-Y H:i:s'),  

                'totalData' => $result['pagination']['totalData'],
                'page' => $result['pagination']['page'],
                'pageSize' => $result['pagination']['pageSize'],
                'totalPage' => $result['pagination']['totalPage'],
            ],  

            'data' => $result['data']
        ]);
    }

    private function getKontainerTanggalData($start, $end, $pageSize, $page)
    {
        /*
        |--------------------------------------------------------------------------
        | ContF
        |--------------------------------------------------------------------------
        */
        $contF = ContF::with('job')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('tglmasuk', [
                    $start->toDateString(),
                    $end->toDateString()
                ])
                ->orWhereBetween('tglkeluar', [
                    $start->toDateString(),
                    $end->toDateString()
                ]);
            })
            ->get();    
    

        /*
        |--------------------------------------------------------------------------
        | ContL
        |--------------------------------------------------------------------------
        */
        $contL = ContL::with('job')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('tglmasuk', [
                    $start->toDateString(),
                    $end->toDateString()
                ])
                ->orWhereBetween('tglkeluar', [
                    $start->toDateString(),
                    $end->toDateString()
                ]);
            })
            ->get();    
    

        /*
        |--------------------------------------------------------------------------
        | Gabungkan ContF + ContL
        |--------------------------------------------------------------------------
        */
        $containers = $contF->map(function ($container) {
            $container->container_type = 'FCL';
            return $container;
        })
        ->concat(
            $contL->map(function ($container) {
                $container->container_type = 'LCL';
                return $container;
            })
        );  
    

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $totalData = $containers->count();  

        $totalPage = $totalData > 0
            ? (int) ceil($totalData / $pageSize)
            : 0;    

        $containers = $containers
            ->forPage($page, $pageSize)
            ->values(); 
    

        /*
        |--------------------------------------------------------------------------
        | Format Response
        |--------------------------------------------------------------------------
        */
        $data = []; 

        foreach ($containers as $container) {   

            $history = [];  
    

            /*
            |--------------------------------------------------------------------------
            | IN
            |--------------------------------------------------------------------------
            */
            if ($container->tglmasuk !== null) {    

                $in = [
                    "kodeKegiatan" => 5,    

                    "waktuKegiatan" => Carbon::parse(
                        $container->tglmasuk . ' ' . $container->jammasuk
                    )->format('d-m-Y H:i:s'),   

                    "block" => null,
                    "row" => null,
                    "slot" => null,
                    "tier" => null, 

                    "nomorPolisi" => $container->nopol, 

                    "gate" => "IN",
                    "stid" => null, 

                    "dokumen" => [
                        "kodeDokumen" => "3",
                        "nomorDokumen" => $container->job->noplp ?? null,
                        "tanggalDokumen" => $container->job->ttgl_plp ?? null
                    ]
                ];  

                $history[] = $in;
            }   
    

            /*
            |--------------------------------------------------------------------------
            | OUT
            |--------------------------------------------------------------------------
            */
            if ($container->tglkeluar !== null) {   

                $out = [
                    "kodeKegiatan" => 6,    

                    "waktuKegiatan" => Carbon::parse(
                        $container->tglkeluar . ' ' . $container->jamkeluar
                    )->format('d-m-Y H:i:s'),   

                    "block" => null,
                    "row" => null,
                    "slot" => null,
                    "tier" => null, 

                    "nomorPolisi" => $container->nopol_mty, 

                    "gate" => "OUT",
                    "stid" => null, 

                    "dokumen" => [
                        "kodeDokumen" => $container->kd_dok_inout ?? "3",   

                        "nomorDokumen" => $container->no_dok
                            ?? $container->job->noplp
                            ?? null,    

                        "tanggalDokumen" => $container->tgl_dok
                            ?? $container->job->ttgl_plp
                            ?? null
                    ]
                ];  

                $history[] = $out;
            }   
    

            /*
            |--------------------------------------------------------------------------
            | Container
            |--------------------------------------------------------------------------
            */
            $data[] = [
                "kodeTps" => "1MUT",
                "kodeGudang" => "INTI", 

                "nomorKontainer" => $container->nocontainer,    

                "ukuranKontainer" => $container->size,  

                "jenisKontainer" => $container->container_type === 'FCL'
                    ? "7"
                    : "8",  

                "nomorBlAwb" => $container->nobl ?? '',
                "tanggalBlAwb" => $container->tgl_bl_awb ?? '', 

                "history" => [
                    $history
                ]
            ];
        }   
    

        return [
            'data' => $data,    

            'pagination' => [
                'totalData' => $totalData,
                'page' => $page,
                'pageSize' => $pageSize,
                'totalPage' => $totalPage,
            ]
        ];
    }

    public function outstandingContainer(Request $request)
    {
        $contF = ContF::with('job')->whereNotNull('tglmasuk')->whereNull('tglkeluar')->get(); 

        $contL = ContL::with('job')->whereNotNull('tglmasuk')->whereNull('tglkeluar')->get();    
    
        $containers = $contF->map(function ($container) {
            $container->container_type = 'FCL';
            $container->jenis_kontainer = 8;
            return $container;
        })
        ->concat(
            $contL->map(function ($container) {
                $container->container_type = 'LCL';
                $container->jenis_kontainer = 7;
                return $container;
            })
        );

        $page = $request->page ?? 1;
        $pageSize = $request->pageSize ?? 100;

        $totalData = $containers->count();  
        $totalPage = $totalData > 0
            ? (int) ceil($totalData / $pageSize)
            : 0;    
        $containers = $containers->forPage($page, $pageSize)->values(); 

        try {

            $data = [];
            foreach ($containers as $container) {
                $tanggalMasuk = $container->tglmasuk;
                $jamMasuk = !empty($container->jammasuk)
                    ? $container->jammasuk
                    : '00:00:00';
                $waktuInOut = Carbon::parse(
                    $tanggalMasuk . ' ' . $jamMasuk
                )->format('d-m-Y H:i:s');
                $data[] = [
                    [
                      "kodeTps"=> "1MUT",
                      "kodeGudang"=> "INTI",
                      "nomorKontainer"=> $container->nocontainer,
                      "ukuranKontainer"=> $container->size,
                      "jenisKontainer"=> $container->jenis_kontainer,
                      "nomorBlAwb"=> $container->nobl,
                      "tanggalBlAwb"=> Carbon::parse($container->tgl_bl_awb)->format('d-m-Y'),
                      "nomorBc11"=> $container->job->tno_bc11 ?? '',
                      "tanggalBc11"=> $container->job->ttgl_bc11,
                      "kodeDokumen"=> $container->kd_dok_inout ?? '3',
                      "nomorDokumen"=> $container->no_dok ?? $container->job->noplp,
                      "tanggalDokumen"=> $container->tgl_dok ?? $container->job->ttgl_plp,
                      "kodeKegiatan"=> 5,
                      "waktuKegiatan"=> $waktuInOut,
                      "block"=> null,
                      "row"=> null,
                      "slot"=> null,
                      "tier"=> null
                    ],
                ];
            }

             return response()->json([
                'header' => [
                    'totalData' => $totalData,
                    'page' => $page,
                    'pageSize' => $pageSize,
                    'totalPage' => $totalPage,
                ],  

                'data' => $data
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }
}
