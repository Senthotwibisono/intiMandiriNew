<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Container as Cont;
use App\Models\ContainerFCL as ContF;
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

    public function indexViewAll($id)
    {
        $cont = Cont::where('joborder_id', $id)->pluck('id');
        // dd($cont);
        $data['barcodes'] = Barcode::whereIn('ref_id', $cont)->where('ref_type', '=', 'FCL')->get();
       
        $data['title'] = "Gate Pass"; 

        return view('barcode.indexPrintAll', $data);
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
                case 'FCL':
                    $item = ContF::find($barcode->ref_id);
                    $tipe = 'fcl';
                    break;
            }

            if ($barcode->ref_action == 'get') {
                $action = 'gate-in';
                $detil = '1. Photo Peti Kemas di Depan Gate by Auto Gate';
            }else {
                $action = ($barcode->type == 'FCL' || $barcode->type == 'Manifest') ? 'gate-out' : 'buang-mty';
                $detil = '1. Photo Peti Kemas di Depan Gate by Auto Gate';
            }

            $data['title'] = "Photo In ".$action." - " . $barcode->ref_number;
            $data['item'] = $item;
            $data['photos'] = Photo::where('master_id', $item->id)->where('type', $tipe)->where('action', $action )->where('tipe_gate', 'in')->where('detil', $detil)->get();
            // dd($tipe);
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
                    $action = 'buang-mty';
                    break;
                case 'Manifest':
                    $item = Manifest::find($barcode->ref_id);
                    $tipe = 'manifest';
                    $action = 'gate-out';
                    break;
                case 'FCL':
                    $item = ContF::find($barcode->ref_id);
                    $tipe = 'fcl';
                    $action = 'gate-out';
                    break;
            }
            if ($barcode->ref_action == 'get') {
                $action = 'gate-in';
                $detil = '1. Photo Peti Kemas di Depan Gate by Auto Gate (keluar)';
            }else {
                $action = ($barcode->type == 'FCL' || $barcode->type == 'Manifest') ? 'gate-out' : 'buang-mty';
                $detil = '1. Photo Peti Kemas di Depan Gate by Auto Gate (keluar)';
            }
            

            $data['title'] = "Photo Out ".$action." - " . $barcode->ref_number;
            $data['item'] = $item;
            $data['photos'] = Photo::where('master_id', $item->id)->where('type', $tipe)->where('action', $action )->where('tipe_gate', '=', 'out')->where('detil', $detil)->get();
            // dd($data['photos']);
            return view('photo.index', $data);
        }
    }

    public function autoGateNotification(Request $request)
    {
        $barcode = $request->barcode;
        $dataBarcode = Barcode::where('barcode', $barcode)->first();
        $tipe = $request->tipe;

        if ($dataBarcode->ref_type == 'LCL') {
            $cont = Cont::find($dataBarcode->ref_id);
            if ($dataBarcode->ref_action == 'get') {
                if ($tipe == 'in' || $tipe == 'In' || $tipe == 'IN') {
                    $cont->update([
                        'tglmasuk'=> carbon::now(),
                        'jammasuk'=> carbon::now(),
                        'uidmasuk'=> 'Autogate',
                    ]);
                    $manifest = Manifest::where('container_id', $cont->id)->update([
                        'tglmasuk'=> carbon::now(),
                        'jammasuk'=> carbon::now(),
                    ]);
                    if ($request->hasFile('fileKamera')) {
                        $photos = $request->file('fileKamera');
                        foreach ($photos as $photo) {
                            $fileName = $photo->getClientOriginalName();
                            $photo->storeAs('imagesInt', $fileName, 'public'); 
                            $newPhoto = Photo::create([
                                'master_id' => $cont->id,
                                'type' => 'lcl',
                                'tipe_gate' => 'in',
                                'action' => 'gate-in',
                                'detil' => '1. Photo Peti Kemas di Depan Gate by Auto Gate',
                                'photo' => $fileName,
                            ]);
                        }
                    }
                }elseif ($tipe == 'out' || $tipe == 'Out' || $tipe == 'OUT') {
                    if ($request->hasFile('fileKamera')) {
                        $photos = $request->file('fileKamera');
                        foreach ($photos as $photo) {
                            $fileName = $photo->getClientOriginalName();
                            $photo->storeAs('imagesInt', $fileName, 'public'); 
                            $newPhoto = Photo::create([
                                'master_id' => $cont->id,
                                'type' => 'lcl',
                                'tipe_gate' => 'out',
                                'action' => 'gate-in',
                                'detil' => '1. Photo Peti Kemas di Depan Gate by Auto Gate (keluar)',
                                'photo' => $fileName,
                            ]);
                        }
                    }
                }
            }elseif ($dataBarcode->ref_action == 'release') {
                if ($tipe == 'in' || $tipe == 'In' || $tipe == 'IN') {
                    if ($request->hasFile('fileKamera')) {
                        $photos = $request->file('fileKamera');
                        foreach ($photos as $photo) {
                            $fileName = $photo->getClientOriginalName();
                            $photo->storeAs('imagesInt', $fileName, 'public'); 
                            $newPhoto = Photo::create([
                                'master_id' => $cont->id,
                                'type' => 'lcl',
                                'tipe_gate' => 'in',
                                'action' => 'buang-mty',
                                'detil' => '1. Photo Peti Kemas di Depan Gate by Auto Gate',
                                'photo' => $fileName,
                            ]);
                        }
                    }
                }elseif ($tipe == 'out' || $tipe == 'Out' || $tipe == 'OUT') {
                    $cont->update([
                        'tglkeluar'=> date('Y-m-d', strtotime($dataBarcode->time_out)),
                        'jamkeluar'=> date('H:i:s', strtotime($dataBarcode->time_out)),
                        'uidmty'=>'Autogate',
                    ]);
                    if ($request->hasFile('fileKamera')) {
                        $photos = $request->file('fileKamera');
                        foreach ($photos as $photo) {
                            $fileName = $photo->getClientOriginalName();
                            $photo->storeAs('imagesInt', $fileName, 'public'); 
                            $newPhoto = Photo::create([
                                'master_id' => $cont->id,
                                'type' => 'lcl',
                                'tipe_gate' => 'out',
                                'action' => 'buang-mty',
                                'detil' => '1. Photo Peti Kemas di Depan Gate by Auto Gate (keluar)',
                                'photo' => $fileName,
                            ]);
                        }
                    }
                }
            }else {
                return 'Status BC is HOLD or FLAGING, please unlock!!!';
            }
        }elseif ($dataBarcode->ref_type == 'Manifest') {
            $manifest = Manifest::find($dataBarcode->ref_id);
            if ($dataBarcode->ref_action == 'release') {
                if ($tipe == 'in' || $tipe == 'In' || $tipe == 'IN') {
                    if ($request->hasFile('fileKamera')) {
                        $photos = $request->file('fileKamera');
                        foreach ($photos as $photo) {
                            $fileName = $photo->getClientOriginalName();
                            $photo->storeAs('imagesInt', $fileName, 'public'); 
                            $newPhoto = Photo::create([
                                'master_id' => $manifest->id,
                                'type' => 'manifest',
                                'tipe_gate' => 'in',
                                'action' => 'gate-out',
                                'detil' => 'Truck Masuk',
                                'photo' => $fileName,
                            ]);
                        }
                    }
                }elseif ($tipe == 'out' || $tipe == 'Out' || $tipe == 'OUT') {
                    $manifest->update([
                        'tglbuangmty'=> date('Y-m-d', strtotime($dataBarcode->time_out)),
                        'jambuangmty'=> date('H:i:s', strtotime($dataBarcode->time_out)),
                        'uidrelease' => 'Autogate',
                    ]);
                    if ($request->hasFile('fileKamera')) {
                        $photos = $request->file('fileKamera');
                        foreach ($photos as $photo) {
                            $fileName = $photo->getClientOriginalName();
                            $photo->storeAs('imagesInt', $fileName, 'public'); 
                            $newPhoto = Photo::create([
                                'master_id' => $manifest->id,
                                'type' => 'manifest',
                                'tipe_gate' => 'out',
                                'action' => 'gate-out',
                                'detil' => 'Truck Keluar',
                                'photo' => $fileName,
                            ]);
                        }
                    }
                }
            }
        }elseif ($dataBarcode->ref_type == 'FCL') {
            $cont = ContF::find($dataBarcode->ref_id);
            if ($dataBarcode->ref_action == 'get') {
                if ($tipe == 'in' || $tipe == 'In' || $tipe == 'IN') {
                    $cont->update([
                        'tglmasuk'=> carbon::now(),
                        'jammasuk'=> carbon::now(),
                        'uidmasuk'=> 'Autogate',
                    ]);
                    if ($request->hasFile('fileKamera')) {
                        $photos = $request->file('fileKamera');
                        foreach ($photos as $photo) {
                            $fileName = $photo->getClientOriginalName();
                            $photo->storeAs('imagesInt', $fileName, 'public'); 
                            $newPhoto = Photo::create([
                                'master_id' => $cont->id,
                                'type' => 'fcl',
                                'tipe_gate' => 'in',
                                'action' => 'gate-in',
                                'detil' => '1. Photo Peti Kemas di Depan Gate by Auto Gate',
                                'photo' => $fileName,
                            ]);
                        }
                    }
                }elseif ($tipe == 'out' || $tipe == 'Out' || $tipe == 'OUT') {
                    if ($request->hasFile('fileKamera')) {
                        $photos = $request->file('fileKamera');
                        foreach ($photos as $photo) {
                            $fileName = $photo->getClientOriginalName();
                            $photo->storeAs('imagesInt', $fileName, 'public'); 
                            $newPhoto = Photo::create([
                                'master_id' => $cont->id,
                                'type' => 'fcl',
                                'tipe_gate' => 'out',
                                'action' => 'gate-in',
                                'detil' => '1. Photo Peti Kemas di Depan Gate by Auto Gate (keluar)',
                                'photo' => $fileName,
                            ]);
                        }
                    }
                }
            }elseif ($dataBarcode->ref_action == 'release') {
                if ($tipe == 'in' || $tipe == 'In' || $tipe == 'IN') {
                    if ($request->hasFile('fileKamera')) {
                        $photos = $request->file('fileKamera');
                        foreach ($photos as $photo) {
                            $fileName = $photo->getClientOriginalName();
                            $photo->storeAs('imagesInt', $fileName, 'public'); 
                            $newPhoto = Photo::create([
                                'master_id' => $cont->id,
                                'type' => 'fcl',
                                'tipe_gate' => 'in',
                                'action' => 'gate-out',
                                'detil' => '1. Photo Peti Kemas di Depan Gate by Auto Gate',
                                'photo' => $fileName,
                            ]);
                        }
                    }
                }elseif ($tipe == 'out' || $tipe == 'Out' || $tipe == 'OUT') {
                    $cont->update([
                        'tglkeluar'=> date('Y-m-d', strtotime($dataBarcode->time_out)),
                        'jamkeluar'=> date('H:i:s', strtotime($dataBarcode->time_out)),
                        'uidmty'=>'Autogate',
                    ]);
                    if ($request->hasFile('fileKamera')) {
                        $photos = $request->file('fileKamera');
                        foreach ($photos as $photo) {
                            $fileName = $photo->getClientOriginalName();
                            $photo->storeAs('imagesInt', $fileName, 'public'); 
                            $newPhoto = Photo::create([
                                'master_id' => $cont->id,
                                'type' => 'fcl',
                                'tipe_gate' => 'out',
                                'action' => 'gate-out',
                                'detil' => '1. Photo Peti Kemas di Depan Gate by Auto Gate (keluar)',
                                'photo' => $fileName,
                            ]);
                        }
                    }
                }
            }else {
                return 'Status BC is HOLD or FLAGING, please unlock!!!';
            }
        }
    }
}
