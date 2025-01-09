<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Container as Cont;
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

    public function storeManifest(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();
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
