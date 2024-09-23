<?php

namespace App\Http\Controllers\lcl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Container as Cont;
use App\Models\JobOrder as Job;
use App\Models\Manifest;
use App\Models\Eseal;
use App\Models\User;
use App\Models\Photo;
use App\Models\TpsSPJM as SPJM;
use App\Models\TpsSPPB as SPPB;
use App\Models\TpsSPPBCont as SPPBCont;
use App\Models\TpsSPPBKms as SPPBKms;
use App\Models\KodeDok as Kode;
use App\Models\TpsManual as Manual;
use App\Models\TpsManualCont as ManualCont;
use App\Models\TpsManualKms as ManualKms;
use App\Models\TpsSPPBBC23 as BC23;
use App\Models\TpsSPPBBC23Cont as BC23Cont;
use App\Models\TpsSPPBBC23Kms as BC23Kms;
use App\Models\BarcodeGate as Barcode;
use App\Models\PlacementManifest as PM;
use App\Models\Item;

class DeliveryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function indexBehandle()
    {
        $data['title'] = 'Manifest Behandle';
        $data['manifest'] = Manifest::get();
        $data['locs'] = PM::where('use_for', 'B')->get();

        return view('lcl.delivery.behandleIndex', $data);
    }

    public function spjmBehandle(Request $request)
    {
        $tgl = Carbon::parse($request->tgl_spjm)->format('d/m/Y');
        // var_dump($tgl);
        // die;
        $spjm = SPJM::where('no_spjm', $request->no_spjm)->where('tgl_pib', $tgl)->first();
        if ($spjm) {
            $manifest = Manifest::where('id', $request->id)->first();
            if ($manifest) {
                $manifest->update([
                    'no_spjm' => $request->no_spjm,
                    'tgl_spjm' => $request->tgl_spjm,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Data ditemukan',
                ]);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Oopss, Something Wrong',
                ]);
            }
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak ditemukan !!',
            ]);
        }
    }

    public function behandle(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();
        if ($manifest->date_ready_behandle == null) {
            $date_ready_behandle = Carbon::now();
        } else {
            $date_ready_behandle = $manifest->date_ready_behandle;
        }
        try {
            if ($request->location_behandle) {
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

                $newLokasi = PM::where('id', $request->location_behandle)->first();
                if ($newLokasi) {
                    foreach ($item as $barang) {
                        $barang->update([
                            'lokasi_id' => $newLokasi->id,
                        ]);
                    }
                    $jumlahBarang = $item->count();
                    $newJumlahBarang = $newLokasi->jumlah_barang + $jumlahBarang;
                    $newLokasi->update([
                        'jumlah_barang' => $newJumlahBarang,
                    ]);
                    $lokasiBehandle = $newLokasi->id;
                } else {
                    $lokasiBehandle = null;
                }
            } else {
                $lokasiBehandle = null;
            }

            $manifest->update([
                'status_behandle' => 1, 
                'location_behandle' => $lokasiBehandle,
            ]);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imagesInt', $fileName, 'public'); 
                    Photo::create([
                        'master_id' => $manifest->id,
                        'type' => 'manifest',
                        'action' => 'behandle',
                        'photo' => $fileName,
                    ]);
                }
            }
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data Berhasil di Update']);

        } catch (\Throwable $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Oopss, Something Wrong: ' . $e->getMessage()]);
        }
    }


    public function detailBehandle($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        $data['title'] = "Photo Behandle Manifest - " . $manifest->notally;
        $data['item'] = $manifest;
        $data['photos'] = Photo::where('master_id', $id)->where('type', '=', 'manifest')->where('action', '=', 'behandle')->get();
        // dd($data['photos']);
        return view('photo.index', $data);
    }

    public function readyCheckBehandle($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        if ($manifest) {
            $manifest->update([
                'status_behandle' => 2,
                'date_ready_behandle' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Manifest siap untuk behandle',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Oopss, Something Wrong!!',
            ]);
        }
    }

    public function finishBehandle($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        if ($manifest) {
            $manifest->update([
                'status_behandle' => 3,
                'tglbehandle' => Carbon::now()->toDateString(), // Format as 'Y-m-d'
                'jambehandle' => Carbon::now()->toTimeString(), // Format as 'H:i:s'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Manifest siap untuk behandle',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Oopss, Something Wrong!!',
            ]);
        }
    }

    public function indexGateOut()
    {
        $data['title'] = 'Manifest Gate Out';
        $data['manifest'] = Manifest::whereNotNull('tglstripping')->get();
        $data['doks'] = Kode::orderBy('kode', 'asc')->get();

        return view('lcl.delivery.gateOut', $data);
    }

    public function dokumenGateOut(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();

        $kdDok = $request->kd_dok;
        $tglDok = Carbon::parse($request->tgl_dok)->format('n/j/Y');
        $tglDokManual = Carbon::parse($request->tgl_dok)->format('d/m/Y');
        // var_dump($tglDok, $request->no_dok, $request->kd_dok);
        // die();
        if ($kdDok == 1) {
            $dok = SPPB::where('no_sppb', $request->no_dok)->where('tgl_sppb', $tglDok)->first();
            if ($dok) {
                $manifest->update([
                    'kd_dok_inout' => $kdDok,
                    'no_dok' => $request->no_dok,
                    'tgl_dok' => $request->tgl_dok,
                    'status_bc' => 'release',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Data di temukan',
                ]);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak di temukan',
                ]);
            }
        }elseif ($kdDok == 2) {
            $dok = BC23::where('no_sppb', $request->no_dok)->where('tgl_sppb', $tglDok)->first();
            if ($dok) {
                $manifest->update([
                    'kd_dok_inout' => $kdDok,
                    'no_dok' => $request->no_dok,
                    'tgl_dok' => $request->tgl_dok,
                    'status_bc' => 'HOLD',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Data di temukan',
                ]);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak di temukan',
                ]);
            }
        }else {
            $dok = Manual::where('kd_dok_inout', $kdDok)->where('no_dok_inout', $request->no_dok)->where('tgl_dok_inout', $tglDokManual)->first();
            if ($dok) {
                $manifest->update([
                    'kd_dok_inout' => $kdDok,
                    'no_dok' => $request->no_dok,
                    'tgl_dok' => $request->tgl_dok,
                    'status_bc' => 'HOLD',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Data di temukan',
                ]);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak di temukan',
                ]);
            }
        }
    }

    public function gateOut(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();
        try {
            if ($manifest->status_bc != 'release') {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Status BC Belum Release']);
            }
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
                'tglbuangmty' => $request->tglbuangmty,
                'jambuangmty' => $request->jambuangmty,
                'nopol_release' => $request->nopol_release,
            ]);

            // dd($manifest);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imagesInt', $fileName, 'public'); 
                    $newPhoto = Photo::create([
                        'master_id' => $manifest->id,
                        'type' => 'manifest',
                        'action' => 'gate-out',
                        'photo' => $fileName,
                    ]);
                }
            }
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di Update']);
            
        } catch (\Throwable $e) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Something Wrong'. $e->getMessage()]);
        }
    }

    public function detailGateOut($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        $data['title'] = "Photo Gate Out Manifest - " . $manifest->notally;
        $data['item'] = $manifest;
        $data['photos'] = Photo::where('master_id', $id)->where('type', '=', 'manifest')->where('action', '=', 'gate-out')->get();
        // dd($data['photos']);
        return view('photo.index', $data);
    }

    public function createBarcode(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();
        if ($manifest->no_dok == null) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen belum ada, isi dokumen terlebih dahulu!',
            ]);
        }
        if ($manifest->status_bc == 'release') {
            $action = 'release';
        }else {
            $action = 'hold';
        }
        $barcode = Barcode::where('ref_id', $manifest->id)->where('ref_type', '=', 'Manifest')->where('ref_action', $action)->first();
        if ($barcode) {
                $now = Carbon::now();
                if ($barcode->status == 'inactive' || $barcode->expired <= $now) {
                    do {
                        $uniqueBarcode = Str::random(20);
                    } while (Barcode::where('barcode', $uniqueBarcode)->exists());
                    $barcode->update([
                        'barcode'=> $uniqueBarcode,
                        'status'=>'active',
                        'expired'=> Carbon::now()->addDays(3),
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'updated successfully!',
                        'data'    => $barcode,
                    ]);
                }else {
                    return response()->json([
                        'success' => true,
                        'message' => 'updated successfully!',
                        'data'    => $barcode,
                    ]);
                }
        }else {
            do {
                $uniqueBarcode = Str::random(20);
            } while (Barcode::where('barcode', $uniqueBarcode)->exists());    
            $newBarcode = Barcode::create([
                'ref_id'=>$manifest->id,
                'ref_type'=>'Manifest',
                'ref_action'=> $action,
                'ref_number'=>$manifest->notally,
                'barcode'=> $uniqueBarcode,
                'status'=>'active',
                'expired'=> Carbon::now()->addDays(3),
                'uid'=> Auth::user()->id,
                'created_at'=> Carbon::now(),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $newBarcode,
            ]);
        }
    }

    public function manifestBarcode($id)
    {
        $data['barcode'] = Barcode::where('id', $id)->first();
        $barcode = $data['barcode'];
        $data['title'] = "Gate Pass " . $barcode->manifest->notally; 

        return view('barcode.indexManifest', $data);
    }
}


