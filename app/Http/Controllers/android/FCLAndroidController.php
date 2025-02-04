<?php

namespace App\Http\Controllers\android;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

        $photoTake = Photo::where('type', 'lcl')->where('master_id', $id)->get();
        if ($cont) {
            return response()->json([
                'listPhoto' => $photoTake,
                'data' => $cont,
                'message' => 'Data Ditemukan',
                'success' => true,
            ]);
        }
    }
}
