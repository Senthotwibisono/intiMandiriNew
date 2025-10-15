<?php

namespace App\Http\Controllers\beaCukai;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;

use App\Models\Container as Cont;
use App\Models\JobOrder as Job;
use App\Models\ContainerFCL as ContF;
use App\Models\JobOrderFCL as JobF;
use App\Models\BarcodeGate as Barcode;
use App\Models\Manifest;
use App\Models\Photo;
use App\Models\PlacementManifest as PM;
use App\Models\Item;

use DataTables;

class BeaCukaiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:bc');
    }

    public function home()
    {
        $data['title'] = "Dashboard Bea Cukai";
        $data['contRemaining'] = Cont::whereNotNull('endstripping')->where('status_bc', null)->count();
        $data['behandle'] = Manifest::whereNotNull('status_behandle')->whereNot('status_behandle', 'release')->count();
        $data['GateOut'] = Manifest::whereNotNull('status_bc')->whereNot('status_bc', 'release')->count();

        return view('bc.dashboard', $data);
    }
    
    public function buangMt()
    {
        $data['title'] = "LCL Import || Realisasi - Buang Empty";
        $data['conts'] = Cont::whereNotNull('endstripping')->where('status_bc', null)->get();
        // dd($data['conts']);
        $data['user'] = Auth::user()->name;
        return view('bc.lcl.mty', $data);
    }

    public function buangMtPost($id){
        $cont = Cont::where('id', $id)->first();
        if ($cont) {
            $cont->update([
                'status_bc'=>'release',
            ]);
            $barcode = Barcode::where('ref_id', $cont->id)->where('ref_type', '=', 'LCL')->where('ref_action', 'release')->first();
            if ($barcode) {
                $barcode->update([
                    'status' => 'active',
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Container diperbolehkan keluar',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Something wrong !!',
            ]);
        }
    }

    public function behandle()
    {
        $data['title'] = "LCL Import || Delivery - Behandle";
        $data['manifest'] = Manifest::whereNotNull('status_behandle')->whereNot('status_behandle', 'release')->get();

        return view('bc.lcl.behandle', $data);
    }

    public function behandleUpdate(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();
        try {
            if ($request->status_behandle == 3 || $request->status_behandle == 4) {
                $item = Item::where('manifest_id', $manifest->id)->get();
                $oldLokasi = Item::where('manifest_id', $manifest->id)->pluck('lokasi_id')->unique();
                if ($oldLokasi) {
                    foreach ($oldLokasi as $lokasiId) {
                        $itemCount = $item->where('lokasi_id', $lokasiId)->count();
                        $lokasiLama = PM::where('id', $lokasiId)->first();
                        if ($lokasiLama) {
                            $newJumlah = $lokasiLama->jumlah_barang - $itemCount;
                            $lokasiLama->update([
                                'jumlah_barang' => $newJumlah,
                            ]);
                            foreach ($item as $barang) {
                                $barang->update([
                                    'lokasi_id' => null,
                                ]);
                            }
                        }
                    }
                }
            }
            $manifest->update([
                'date_check_behandle' => $request->date_check_behandle,
                'desc_check_behandle' => $request->desc_check_behandle,
                'status_behandle' => $request->status_behandle,
            ]);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imagesInt', $fileName, 'public'); 
                    $newPhoto = Photo::create([
                        'master_id' => $manifest->id,
                        'type' => 'manifest',
                        'action' => 'behandle',
                        'photo' => $fileName,
                    ]);
                }
            }

            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di Update']);
        } catch (\Throwable $e) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Ooppss, Something Wrong'. $e->getMessage()]);
        }
    }

    public function approveBehandle($id){
        $manifest = Manifest::where('id', $id)->first();
        if ($manifest) {
                $item = Item::where('manifest_id', $manifest->id)->get();
                $oldLokasi = Item::where('manifest_id', $manifest->id)->pluck('lokasi_id')->unique();
                if ($oldLokasi) {
                    foreach ($oldLokasi as $lokasiId) {
                        $itemCount = $item->where('lokasi_id', $lokasiId)->count();
                        $lokasiLama = PM::where('id', $lokasiId)->first();
                        if ($lokasiLama) {
                            $newJumlah = $lokasiLama->jumlah_barang - $itemCount;
                            $lokasiLama->update([
                                'jumlah_barang' => $newJumlah,
                            ]);
                        }
                    }
                }
            $manifest->update([
                'status_behandle'=>'release',
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Behandle Selesai',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Something wrong !!',
            ]);
        }
    }
    
    public function gateOut()
    {
        $data['manifest'] = Manifest::whereNotNull('status_bc')->whereNot('status_bc', 'release')->get();
        $data['title'] = "LCL Import || Delivery - Gate Out";
        
        return view('bc.lcl.gateOut', $data);
    }

    public function approveGateOut($id){
        $manifest = Manifest::where('id', $id)->first();
        if ($manifest->status_bc == 'HOLDP2') {
            return response()->json([
                'success' => false,
                'message' => 'Harap Hubungi P2 !!',
            ]);
        }
        if ($manifest) {
            $barcode = Barcode::where('ref_id', $manifest->id)->where('ref_type', '=', 'Manifest')->where('status', 'hold')->first();
            if ($barcode) {
                $barcode->update([
                    'status' => 'active',
                ]);
            }
            $manifest->update([
                'status_bc'=>'release',
                'release_bc_date' => Carbon::now(),
                'release_bc_uid' => Auth::user()->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Manifest Persilahkan Keluar',
            ]);
            
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Something wrong !!',
            ]);
        }
    }

    // stripping
    public function strippingIndex()
    {
        $data['title'] = "Stripping Approve";
        $data['user'] = Auth::user()->name;

        return view('bc.lcl.stripping', $data);
    }

    public function strippingIndexData(Request $request)
    {
        $cont = Cont::with(['job', 'user'])->where('type', '=', 'lcl')->whereNot('tglmasuk', null)->orderBy('endstripping', 'asc')->get();
        
        return DataTables::of($cont)
        ->addColumn('check', function($cont){
         
                return '<input type="checkbox" class="form-check-input form-check-glow select-cont" value="' . $cont->id . '">';
            
        })
        ->addColumn('action', function($cont){
            return '<a href="/bc/lcl/realisasi/stripping/detil-' . $cont->id . '" class="btn btn-warning"><i class="fa fa-pen"></i></a>';
        })
        ->addColumn('photo', function($cont){
            return '<a href="javascript:void(0)" onclick="openWindow(\'/lcl/realisasi/stripping-photoCont' . $cont->id . '\')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
        })
        ->addColumn('detil', function($cont){
            if ($cont->status_ijin == 'Y') {
                return '<span class="badge bg-light-success">Approved</span>';
            } else {
                return '<span class="badge bg-light-danger">Unapprove</span>';
            }
        })
        ->addColumn('status', function($cont){
            if ($cont->endstripping != null) {
                return '<span class="badge bg-light-danger">Finished</span>';
            }else {
                return '<span class="badge bg-light-success">On Proggress</span>';
            }
        })
        ->addColumn('kapal', function($cont){
            return $cont->job->Kapal->name ?? '-';
        })
        ->addColumn('no_plp', function($cont){
            return $cont->job->PLP->no_plp ?? '-';
        })
        ->addColumn('tgl_plp', function($cont){
            return $cont->job->PLP->tgl_plp ?? '-';
        })
        ->addColumn('kd_kantor', function($cont){
            return $cont->job->PLP->kd_kantor ?? '-';
        })
        ->addColumn('kd_tps', function($cont){
            return $cont->job->PLP->kd_tps ?? '-';
        })
        ->addColumn('kd_tps_asal', function($cont){
            return $cont->job->PLP->kd_tps_asal ?? '-';
        })
        ->addColumn('kd_tps_tujuan', function($cont){
            return $cont->job->PLP->kd_tps_tujuan ?? '-';
        })
        ->addColumn('nm_angkut', function($cont){
            return $cont->job->PLP->nm_angkut ?? '-';
        })
        ->addColumn('no_voy_flight', function($cont){
            return $cont->job->PLP->no_voy_flight ?? '-';
        })
        ->addColumn('no_surat', function($cont){
            return $cont->job->PLP->no_surat ?? '-';
        })
        ->addColumn('no_bc11', function($cont){
            return $cont->job->PLP->no_bc11 ?? '-';
        })
        ->addColumn('tgl_bc11', function($cont){
            return $cont->job->PLP->tgl_bc11 ?? '-';
        })
        ->rawColumns(['check', 'action', 'detil', 'status', 'photo'])
        ->make(true);
    }

    public function strippingApproveCont(Request $request)
    {
        $ids = $request->input('ids');
        // var_dump($ids);
        // die;
        try {
            $conts = Cont::whereIn('id', $ids)->get();
            foreach ($conts as $cont) {
                if ($cont->status_ijin != 'Y') {
                    $cont->update([
                        'status_ijin' => 'Y',
                        'tgl_ijin_stripping' => Carbon::now()->format('Y-m-d'),
                        'jam_ijin_stripping' => Carbon::now()->format('H:i:s'),
                        'ijin_stripping_by' => Auth::user()->id,
                    ]);
                }
            }
            return response()->json([
                 'success' => true,
                 'message' => 'Data success updated',
            ]);
        } catch (\Throwable $th) {
           return response()->json([
                'success' => false,
                'message' => 'Something Wrong' . $th->getMessage(),
           ]);
        }
    }

    public function strippingBatalApproveCont(Request $request)
    {
        $ids = $request->input('ids');
        // var_dump($ids);
        // die;
        try {
            $conts = Cont::whereIn('id', $ids)->get();
            foreach ($conts as $cont) {
                if ($cont->status_ijin == 'Y') {
                    $cont->update([
                        'status_ijin' => 'N',
                    ]);
                }
            }
            return response()->json([
                 'success' => true,
                 'message' => 'Data success updated',
            ]);
        } catch (\Throwable $th) {
           return response()->json([
                'success' => false,
                'message' => 'Something Wrong' . $th->getMessage(),
           ]);
        }
    }
    
    public function strippingDetail($id)
    {
        $cont = Cont::where('id', $id)->first();
        $data['title'] = "Stripping Proccess Container || " . $cont->nocontainer;
        $data['cont'] = $cont;
        $data['id'] = $id;

        return view('bc.lcl.strippingDetil', $data);
    }

    public function strippingDetailData($id, Request $request)
    {
        $manifest = Manifest::with(['shipperM', 'customer', 'packing'])->where('container_id', $id)->get();

        return DataTables::of($manifest)
        ->addColumn('check', function($manifest){
         
                return '<input type="checkbox" class="form-check-input form-check-glow select-cont" value="' . $manifest->id . '">';
            
        })
        ->addColumn('detil', function($manifest){
            if ($manifest->ijin_stripping == 'Y') {
                return '<span class="badge bg-light-success">Approved</span>';
            } else {
                return '<span class="badge bg-light-danger">Unapprove</span>';
            }
        })
        ->addColumn('nohbl', function ($manifest) {
            return $manifest->nohbl ?? '-'; // Replace with proper column name
        })
        ->addColumn('tgl_hbl', function ($manifest) {
            return $manifest->tgl_hbl ?? '-'; // Replace with proper column name
        })
        ->addColumn('notally', function ($manifest) {
            return $manifest->notally ?? '-'; // Replace with proper column name
        })
        ->addColumn('shiper', function ($manifest) {
            return $manifest->shiperM->name ?? '-'; // Replace with proper column name
        })
        ->addColumn('customer', function ($manifest) {
            return $manifest->customer->name ?? '-'; // Replace with proper column name
        })
        ->addColumn('quantity', function ($manifest) {
            return $manifest->quantity ?? '-'; // Replace with proper column name
        })
        ->addColumn('packN', function ($manifest) {
            return $manifest->packing->name ?? '-'; // Replace with proper column name
        })
        ->addColumn('packC', function ($manifest) {
            return $manifest->packing->code ?? '-'; // Replace with proper column name
        })
        ->addColumn('descofgoods', function ($manifest) {
            return '<textarea class="form-control" cols="3" readonly>'. $manifest->descofgoods .'</textarea>'; // Replace with proper column name
        })
        ->addColumn('weight', function ($manifest) {
            return $manifest->weight ?? '-'; // Replace with proper column name
        })
        ->addColumn('meas', function ($manifest) {
            return $manifest->meas ?? '-'; // Replace with proper column name
        })
        ->addColumn('startstripping', function ($manifest) {
            return $manifest->startstripping ?? '-'; // Replace with proper column name
        })
        ->addColumn('endstripping', function ($manifest) {
            return $manifest->endstripping ?? '-'; // Replace with proper column name
        })
        ->rawColumns(['descofgoods', 'check', 'detil'])
        ->make(true);
    }

    public function approveStrippingManifest(Request $request)
    {
        $ids = $request->input('ids');
        // var_dump($ids);
        // die;
        try {
            $manifest = Manifest::whereIn('id', $ids)->get();
            foreach ($manifest as $man) {
                if ($man->ijin_stripping != 'Y') {
                    $man->update([
                        'ijin_stripping' => 'Y',
                        'ijin_stripping_at' => Carbon::now(),
                        'ijin_stripping_by' => Auth::user()->id,
                    ]);
                }
            }
            return response()->json([
                 'success' => true,
                 'message' => 'Data success updated',
            ]);
        } catch (\Throwable $th) {
           return response()->json([
                'success' => false,
                'message' => 'Something Wrong' . $th->getMessage(),
           ]);
        }
    }

    public function BatalapproveStrippingManifest(Request $request)
    {
        $ids = $request->input('ids');
        // var_dump($ids);
        // die;
        try {
            $manifest = Manifest::whereIn('id', $ids)->get();
            foreach ($manifest as $man) {
                if ($man->ijin_stripping == 'Y') {
                    $man->update([
                        'ijin_stripping' => 'N',
                    ]);
                }
            }
            return response()->json([
                 'success' => true,
                 'message' => 'Data success updated',
            ]);
        } catch (\Throwable $th) {
           return response()->json([
                'success' => false,
                'message' => 'Something Wrong' . $th->getMessage(),
           ]);
        }
    }

    public function strippingApprove($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        if ($manifest) {
            $manifest->update([
                'validasiBc' => 'Y',
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Manifest Approved!!',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Something wrong !!',
            ]);
        }
    }

    public function strippingApproveAll()
    {
        $manifest = Manifest::where('validasi', '=', 'Y')->where('validasiBc', '=', null)->get();
        // dd($manifest);
        foreach ($manifest as $mans) {
            $mans->update([
                'validasiBc' => 'Y',
            ]);
        }
        return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di Update']);
    }

    public function aproveContainerHouseBl(Request $request)
    {
        $ids = $request->input('ids');
        // var_dump($ids);
        // die;
        try {
            $conts = Cont::whereIn('id', $ids)->get();
            foreach ($conts as $cont) {
                if ($cont->status_ijin != 'Y') {
                    $cont->update([
                        'status_ijin' => 'Y',
                        'tgl_ijin_stripping' => Carbon::now()->format('Y-m-d'),
                        'jam_ijin_stripping' => Carbon::now()->format('H:i:s'),
                        'ijin_stripping_by' => Auth::user()->id,
                    ]);

                }
                $manifest = Manifest::where('container_id', $cont->id)->whereNot('flag_segel_merah', 'Y')->get();
                // var_dump($manifest);
                // die();
                foreach ($manifest as $man) {
                    if ($man->ijin_stripping != 'Y') {
                        $man->update([
                            'ijin_stripping' => 'Y',
                            'ijin_stripping_at' => Carbon::now(),
                            'ijin_stripping_by' => Auth::user()->id,
                        ]);
                    }
                }
            }
            return response()->json([
                 'success' => true,
                 'message' => 'Data success updated',
            ]);
        } catch (\Throwable $th) {
           return response()->json([
                'success' => false,
                'message' => 'Something Wrong' . $th->getMessage(),
           ]);
        }
    }

    public function HoldContainerIndex()
    {
        $data['title'] = 'Hold Contianer- FCL';

        return view('bc.fcl.hold', $data);
    }

    public function holdContainerDataTable(Request $request)
    {
        $cont = ContF::where('status_bc', 'HOLD')->get();

        return DataTables::of($cont)
        ->setRowClass('highlight-yellow') // Menjadikan seluruh row berwarna kuning
        ->addColumn('release', function($cont){
            return '<button type="button" class="btn btn-info releaseButton" id="releaseButton" data-id="'.$cont->id.'">Release</button>';
        })
        ->addColumn('photo', function($cont){
            return '<button class="btn btn-outline-info photoButton" data-id="'.$cont->id.'"><i class="fa fa-camera"></i></button>';
        })
        ->addColumn('nojob', function($cont){
            return $cont->job->nojoborder ?? '-';
        })
        ->addColumn('nombl', function($cont){
            return $cont->job->nombl ?? '-';
        })
        ->addColumn('nocontainer', function($cont){
            return $cont->nocontainer ?? '-';
        })
        ->addColumn('nobl', function($cont){
            return $cont->nobl ?? '-';
        })
        ->addColumn('tglBL', function($cont){
            return $cont->tgl_bl_awb ?? '-';
        })
        ->addColumn('tglmasuk', function($cont){
            return $cont->tglmasuk ?? 'Belum Masuk';
        })
        ->addColumn('jammasuk', function($cont){
            return $cont->jammasuk ?? 'Belum Masuk';
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
        ->rawColumns(['release', 'photo'])
        ->make(true);
    }

    public function releaseFCLCont(Request $request)
    {
        $cont = ContF::find($request->id);
        if ($cont->flag_segel_merah == 'Y') {
            return response()->json([
                'success' => false,
                'message' => 'Masih dalam Segel Merah !!',
            ]);
        }
        try {
            $cont->update([
                'status_bc' => 'release',
                'release_bc_date' => Carbon::now(),
                'release_bc_uid' =>  Auth::user()->name,
            ]);


            $barcode = Barcode::where('ref_id', $cont->id)->where('ref_type', '=', 'FCL')->where('ref_action', 'release')->first();
            if ($barcode) {
                $barcode->update([
                    'status' => 'active',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil di Update',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Oops Something Wrong: ' . $th->getMessage(),
            ]);
        }
    }

    public function ReleaseContainerIndex()
    {
        $data['title'] = 'Release Contianer- FCL';

        return view('bc.fcl.release', $data);
    }

    public function releaseContainerDataTable(Request $request)
    {
        $cont = ContF::where('status_bc', 'release')->get();

        return DataTables::of($cont)
        ->setRowClass('highlight-blue') // Menjadikan seluruh row berwarna kuning
        ->addColumn('hold', function($cont){
            return '<button type="button" class="btn btn-danger holdButton" id="holdButton" data-id="'.$cont->id.'">Hold</button>';
        })
        ->addColumn('photo', function($cont){
            return '<button class="btn btn-outline-info photoButton" data-id="'.$cont->id.'"><i class="fa fa-camera"></i></button>';
        })
        ->addColumn('nojob', function($cont){
            return $cont->job->nojoborder ?? '-';
        })
        ->addColumn('nombl', function($cont){
            return $cont->job->nombl ?? '-';
        })
        ->addColumn('nocontainer', function($cont){
            return $cont->nocontainer ?? '-';
        })
        ->addColumn('nobl', function($cont){
            return $cont->nobl ?? '-';
        })
        ->addColumn('tglBL', function($cont){
            return $cont->tgl_bl_awb ?? '-';
        })
        ->addColumn('tglmasuk', function($cont){
            return $cont->tglmasuk ?? 'Belum Masuk';
        })
        ->addColumn('jammasuk', function($cont){
            return $cont->jammasuk ?? 'Belum Masuk';
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
        ->rawColumns(['hold', 'photo'])
        ->make(true);
    }

    public function holdFCLCont(Request $request)
    {
        $cont = ContF::find($request->id);
        try {
            $cont->update([
                'status_bc' => 'HOLD',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil di Update',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Oops Something Wrong: ' . $th->getMessage(),
            ]);
        }
    }
}
