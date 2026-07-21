<?php

namespace App\Http\Controllers\android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\ContainerFCL as Cont;
use App\Models\Manifest;
use App\Models\YardDesign as YD;
use App\Models\YardDetil as RowTier;
use App\Models\Item;
use App\Models\PlacementManifest as PM;
use App\Models\RackingDetil as Rack;
use App\Models\KeteranganPhoto as KP;
use App\Models\Photo;


class FCLAndroidController extends Controller
{
    public function __construct() {
        $this->middleware('auth')->except(['detailBehandle']);
    }
    public function photoCont()
    {
        $data['title'] = 'Photo Container';

        $data['conts'] = Cont::orderBy('id', 'asc')->get();

        return view('android.photoContFCL', $data);
    }

    public function searchCont($id)
    {
        // var_dump($id);
        // die;
        $cont = Cont::find($id);

        $photoTake = Photo::where('type', 'FCL')->where('master_id', $id)->get();
        if ($cont) {
            return response()->json([
                'listPhoto' => $photoTake,
                'data' => $cont,
                'message' => 'Data Ditemukan',
                'success' => true,
            ]);
        }
    }

    public function indexBehandle()
    {
        $data['title'] = 'Behandle FCL Index';

        $data['containers'] = Cont::whereNotNull('no_spjm')->whereNull('tglkeluar')->get();

        return view('android.behandle-fcl.index', $data);
    }

    public function detilBehandle($id)
    {
        $data['title'] = 'Behandle FCL Index';

        $data['containers'] = Cont::whereNotNull('no_spjm')->whereNull('tglkeluar')->get();
        $data['behandle'] = Cont::find($id);

        $photoTake = Photo::where('type', 'FCL')->where('master_id', $data['behandle']->id)->where('action', 'behandle')->get();
        $data['take'] = $photoTake->pluck('detil')->unique();

        return view('android.behandle-fcl.card-body', $data);
    }

    public function searchContainer(Request $request)
    {
        try {
            $container = Cont::find($request->container_id);
            if ($container) {
                return response()->json([
                    'data' => $container,
                    'message' => 'Data Ditemukan',
                    'success' => true,
                ]);
            }else {
                return response()->json([
                    "success" => false,
                    "message" => 'Data tidak ditemukan'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => $th->getMessage()
            ]);
        }
    }

    public function post(Request $request)
    {
        // var_dump($request->all());
        // die;
        try {
            db::transaction(function() use($request){
                $cont = Cont::find($request->id);
                $status = null;

                if (!empty($request->date_finish_behandle)) {
                    $status = 3;
                } elseif (!empty($request->date_check_behandle)) {
                    $status = 2;
                } elseif (!empty($request->date_ready_behandle)) {
                    $status = 1;
                }
                $cont->update([
                    'date_ready_behandle' => $request->date_ready_behandle,
                    'date_check_behandle' => $request->date_check_behandle,
                    'date_finish_behandle' => $request->date_finish_behandle,
                    'desc_check_behandle' => $request->desc_check_behandle,
                    'desc_finish_behandle' => $request->desc_finish_behandle,
                    'status_behandle' => $status,
                ]);

                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $fileName = $photo->getClientOriginalName();
                        $photo->storeAs('imagesInt', $fileName, 'public'); 
                        $newPhoto = Photo::create([
                            'master_id' => $cont->id,
                            'type' => 'fcl',
                            'action' => 'behandle',
                            'detil' => $request->detilPhoto,
                            'photo' => $fileName,
                        ]);
                    }
                }
            });

            return response()->json([
                "success" => true,
                "message" => 'Aksi Berhasil'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => $th->getMessage()
            ]);
        }
    }

    public function detailBehandle($id)
    {
        $manifest = Cont::where('id', $id)->first();
        $data['title'] = "Photo Behandle Container - ";
        $data['item'] = $manifest;
        $data['photos'] = Photo::where('master_id', $id)->where('type', '=', 'FCL')->where('action', '=', 'behandle')->get();
        // dd($data['photos']);
        return view('photo.index', $data);
    }
}
