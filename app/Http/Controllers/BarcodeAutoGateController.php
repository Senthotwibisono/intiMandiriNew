<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Container as Cont;
use App\Models\Manifest;
use App\Models\BarcodeGate as Barcode;
use App\Models\Photo;
use App\Models\YardDetil as RowTier;

use DataTables;

class BarcodeAutoGateController extends Controller
{
    public function __construct()
    {
        // Apply the 'auth' middleware to all methods except 'autoGateNotification'
        $this->middleware('auth')->except('autoGateNotification');
    }

    public function index($id)
    {
        $data['barcode'] = Barcode::where('id', $id)->first();
        $barcode = $data['barcode'];
        $data['title'] = "Gate Pass " . $barcode->cont->nocontainer; 

        return view('barcode.index', $data);
    }

    public function manifest($id)
    {
        $data['barcode'] = Barcode::where('id', $id)->first();
        $barcode = $data['barcode'];
        $data['title'] = "Gate Pass " . $barcode->manifest->notally; 

        return view('barcode.indexManifest', $data);
    }

    public function indexAll()
    {
        $data['title'] = "Barcode Autogate";

        return view('barcode.indexAll', $data);
    }

    public function indexData(Request $request)
    {
        $barcode = Barcode::orderBy('status', 'asc')->orderBy('created_at', 'desc')->get();

        return DataTables::of($barcode)->make(true);
    }

    public function photoIn($id)
    {
        $barcode = Barcode::where('id', $id)->first();
        if ($barcode) {
            switch ($barcode->ref_type) {
                case 'LCL':
                    $item = Cont::find($barcode->ref_id);
                    $tipe = 'lcl';
                    break;
                case 'Manifest':
                    $item = Manifest::find($barcode->ref_id);
                    $tipe = 'manifest';
                    break;
            }

            if ($barcode->ref_action == 'get') {
                $action = 'gate-in';
            }else {
                $action = 'gate-out';
            }

            $data['title'] = "Photo In ".$action." - " . $barcode->ref_number;
            $data['item'] = $item;
            $data['photos'] = Photo::where('master_id', $id)->where('type', $tipe)->where('action', $action )->where('tipe_gate', '=', 'in')->get();
            // dd($data['photos']);
            return view('photo.index', $data);
        }
    }

    public function photoOut($id)
    {
        $barcode = Barcode::where('id', $id)->first();
        if ($barcode) {
            switch ($barcode->ref_type) {
                case 'LCL':
                    $item = Cont::find($barcode->ref_id);
                    $tipe = 'lcl';
                    break;
                case 'Manifest':
                    $item = Manifest::find($barcode->ref_id);
                    $tipe = 'manifest';
                    break;
            }

            if ($barcode->ref_action == 'get') {
                $action = 'gate-in';
            }else {
                $action = 'gate-out';
            }

            $data['title'] = "Photo Out ".$action." - " . $barcode->ref_number;
            $data['item'] = $item;
            $data['photos'] = Photo::where('master_id', $id)->where('type', $tipe)->where('action', $action )->where('tipe_gate', '=', 'out')->get();
            // dd($data['photos']);
            return view('photo.index', $data);
        }
    }

    public function autoGateNotification(Request $request)
    {
        $barcode = $request->barcode;
        $dataBarcode = Barcode::where('barcode', $barcode)->first();
        $tipe = $request->tipe;

        if ($dataBarcode) {
            switch ($dataBarcode->ref_type) {
                case 'LCL':
                        $data = Cont::find($dataBarcode->ref_id);
                        $photoType = 'lcl';
                    break;
                case 'Manifest':
                        $data = Manifest::find($dataBarcode->ref_id);
                        $photoType = 'manifest';
                    break;
            }

            if ($data) {
                
                if ($dataBarcode->ref_action == 'get') {
                    $data->update([
                        'tglmasuk'=> date('Y-m-d', strtotime($dataBarcode->time_in)),
                        'jammasuk'=> date('H:i:s', strtotime($dataBarcode->time_in)),
                        'uidmasuk'=> 'Autogate',
                    ]);
                    if ($request->hasFile('fileKamera')) {
                        foreach ($request->file('fileKamera') as $photo) {
                            $fileName = $photo->getClientOriginalName();
                            $photo->storeAs('imagesInt', $fileName, 'public'); 
                            $newPhoto = Photo::create([
                                'master_id' => $data->id,
                                'type' => $photoType,
                                'tipe_gate' => 'in',
                                'action' => 'gate-in',
                                'photo' => $fileName,
                            ]);
                        }
                    }

                    return "Data updated";
                }elseif ($dataBarcode->ref_action == 'release') {
                    if ($dataBarcode->ref_type == 'manifest') {
                        $data->update([
                            'tglbuangmty'=> date('Y-m-d', strtotime($dataBarcode->time_out)),
                            'jambuangmty'=> date('H:i:s', strtotime($dataBarcode->time_out)),
                        ]);
                    }elseif ($dataBarcode->ref_type == 'lcl') {
                        $data->update([
                            'tglkeluar'=> date('Y-m-d', strtotime($dataBarcode->time_out)),
                            'jamkeluar'=> date('H:i:s', strtotime($dataBarcode->time_out)),
                            'uidmty'=>'Autogate',
                        ]);
                        $oldYard = RowTier::where('cont_id', $data->id)->get();
                        if ($oldYard) {
                            foreach ($oldYard as $old) {
                                $old->update([
                                    'cont_id' => null,
                                    'active' => 'N',
                                ]);
                            }
                        }
                    }
                    if ($request->hasFile('fileKamera')) {
                        foreach ($request->file('fileKamera') as $photo) {
                            $fileName = $photo->getClientOriginalName();
                            $photo->storeAs('imagesInt', $fileName, 'public'); 
                            $newPhoto = Photo::create([
                                'master_id' => $data->id,
                                'type' => $photoType,
                                'tipe_gate' => 'out',
                                'action' => 'gate-out',
                                'photo' => $fileName,
                            ]);
                        }
                    }
                }else {
                    return 'Status BC is HOLD or FLAGING, please unlock!!!';
                }

            }else {
                return 'Something wrong in Model!!!';
            }
        }else {
            return 'Barcode not found !!!';
        }

    }
}
