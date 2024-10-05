<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;

use App\Models\Container as Cont;
use App\Models\JobOrder as Job;
use App\Models\BarcodeGate as Barcode;
use App\Models\Manifest;
use App\Models\Photo;
use App\Models\PlacementManifest as PM;
use App\Models\Item;

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
            $barcode = Barcode::where('ref_id', $cont->id)->where('ref_type', '=', 'LCL')->where('ref_action', 'hold')->first();
            $barcode->update([
                'ref_action' => 'release',
            ]);
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
        if ($manifest) {
            $manifest->update([
                'status_bc'=>'release',
                'release_bc_date' => Carbon::now(),
                'release_bc_uid' => Auth::user()->id,
            ]);
            $barcode = Barcode::where('ref_id', $manifest->id)->where('ref_type', '=', 'Manifest')->where('ref_action', 'hold')->first();
            $barcode->update([
                'ref_action' => 'release',
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
        $data['manifest'] = Manifest::where('validasi', '=', 'Y')->orderBy('validasiBc', 'asc')->orderBy('notally', 'asc')->get();

        $data['user'] = Auth::user()->name;

        return view('bc.lcl.stripping', $data);
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
}
