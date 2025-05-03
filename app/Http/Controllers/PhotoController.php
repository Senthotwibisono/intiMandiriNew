<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Container as Cont;
use App\Models\ContainerFCL as ContF;
use App\Models\JobOrder as Job;
use App\Models\Manifest;
use App\Models\Photo;
use App\Models\KeteranganPhoto as Ket;

class PhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexLclManifest()
    {
        $data['title'] = "Photo Manifest";
        $data['manifest'] = Manifest::orderBy('notally', 'asc')->get();

        return view('photo.lcl.manifest.index', $data);
    }

    public function indexLclContainer()
    {
        $data['title'] = "Photo Container";
        $data['conts'] = Cont::orderBy('joborder_id', 'asc')->get();

        return view('photo.lcl.cont.index', $data);
    }

    public function indexFclContainer()
    {
        $data['title'] = "Photo Container";
        $data['conts'] = ContF::orderBy('joborder_id', 'asc')->get();

        return view('photo.fcl.index', $data);
    }

    public function storeManifest(Request $request)
    {
       
        $manifest = Manifest::where('id', $request->id)->first();
        $manifest->update([
                'tglrelease' => $request->tglrelease,
                'jamrelease' => $request->jamrelease,
                'nopol_release' => $request->nopol_release,
                'dg_label' => $request->dg_label,
        ]);

        if ($request->has('final_qty')) {
            // dd($manifest->quantity, $request->final_qty);
            if ($manifest->quantity == $request->final_qty) {
                if ($manifest->kd_dok_inout == 1) {
                    $statusBC = 'release';
                    $alasan = $manifest->alasan_hold;
                }else {
                    if ($manifest->release_bc_uid != null) {
                        $statusBC = 'release';
                        $alasan = $manifest->alasan_hold;
                    }else {
                        $statusBC = $manifest->status_bc;
                        $alasan = $manifest->alasan_hold;
                    }
                }
                
            }else {
                if ($manifest->status_bc == 'release') {
                    $statusBC = 'HOLD';
                    $alasan = $manifest->alasan_hold . ', Jumlah Quantity Real Berbeda';
                }else {
                    $statusBC = $manifest->status_bc;
                    $alasan = $manifest->alasan_hold . ', Jumlah Quantity Real Berbeda';
                }
            }

            $manifest->update([
                'final_qty' => $request->final_qty,
                'status_bc' => $statusBC,
                'alasan_hold' => $alasan,
            ]); 
        }

        try {
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imagesInt', $fileName, 'public'); 
                    $newPhoto = Photo::create([
                        'master_id' => $manifest->id,
                        'type' => 'manifest',
                        'action' => $request->action,
                        'photo' => $fileName,
                        'detil'=> $request->detil,
                    ]);
                }
            }
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di Update']);
            
        } catch (\Throwable $e) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Something Wrong'. $e->getMessage()]);
        }
    }

    public function storeContainer(Request $request)
    {
        $cont = Cont::where('id', $request->id)->first();
        $cont->update([
            'nopol' => $request->nopol,
            'nopol_mty' => $request->nopol_mty,
            'tglkeluar' => $request->tglkeluar,
            'jamkeluar' => $request->jamkeluar,
        ]);
        try {
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imagesInt', $fileName, 'public'); 
                    $newPhoto = Photo::create([
                        'master_id' => $cont->id,
                        'type' => 'lcl',
                        'action' => $request->action,
                        'photo' => $fileName,
                        'detil'=> $request->detil,
                    ]);
                }
            }

            if ($request->has('tglmasuk')) {
                if ($request->tglmasuk != null) {
                    $cont->update([
                        'tglmasuk' => $request->tglmasuk,
                        'jammasuk' => $request->jammasuk,
                    ]);

                    $manifests = Manifest::where('container_id', $cont->id)->get();
                    if ($manifests->isNotEmpty()) {
                        foreach ($manifests as $manifest) {
                            $manifest->update([
                                'tglmasuk' => $request->tglmasuk,
                                'jammasuk' => $request->jammasuk,
                            ]);
                        }
                    }
                }
            }
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di Update']);
            
        } catch (\Throwable $e) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Something Wrong'. $e->getMessage()]);
        }
    
    }
    public function storeContainerFcl(Request $request)
    {
        $cont = ContF::where('id', $request->id)->first();
        try {
            if ($cont) {
                $cont->update([
                    'nopol' => $request->nopol,
                    'nopol_mty' => $request->nopol_mty,
                    'tglkeluar' => $request->tglkeluar,
                    'jamkeluar' => $request->jamkeluar,
                ]);
            }
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imagesInt', $fileName, 'public'); 
                    $newPhoto = Photo::create([
                        'master_id' => $cont->id,
                        'type' => 'fcl',
                        'action' => $request->action,
                        'photo' => $fileName,
                        'detil'=> $request->detil,
                    ]);
                }
            }

            if ($request->has('tglmasuk')) {
                if ($request->tglmasuk != null) {
                    $cont->update([
                        'tglmasuk' => $request->tglmasuk,
                        'jammasuk' => $request->jammasuk,
                    ]);
                }
            }
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di Update']);
            
        } catch (\Throwable $e) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Something Wrong'. $e->getMessage()]);
        }
    }

    public function getKeteranganContainerLcl(Request $request)
    {
        // var_dump($request->kegiatan);
        // die();
        $kegiatan = $request->kegiatan;
        $detils = Ket::where('tipe', 'Container')->where('kegiatan', $kegiatan)->pluck('keterangan');
        return response()->json($detils);
    }

    public function getKeteranganManifestLcl(Request $request)
    {
        $kegiatan =  $request->kegiatan;
        $detils = Ket::where('tipe', 'Manifest')->where('kegiatan', $kegiatan)->pluck('keterangan');
        return response()->json($detils);
    }
}
