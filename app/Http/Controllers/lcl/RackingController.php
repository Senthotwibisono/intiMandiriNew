<?php

namespace App\Http\Controllers\lcl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;

use App\Models\Manifest;
use App\Models\Item;
use App\Models\PlacementManifest as PM;
use App\Models\RackTier as RT;
use App\Models\RackingDetil as Rack;
use App\Models\Photo;
use App\Models\KeteranganPhoto as KP;

use DataTables;

class RackingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['title'] = "Racking Manifest";

        return view('lcl.realisasi.racking.index', $data);
    }

    public function indexTable(Request $request)
    {
        $manifest = Manifest::with(['customer', 'shipperM', 'packing'])->whereNot('jamstripping', null)->where('tglbuangmty', null)->get();

        return DataTables::of($manifest)
        ->addColumn('action', function($manifest){
            return '<a href="/lcl/realisasi/racking/detail-'.$manifest->id.'" class="btn btn-warning editButton"><i class="fa fa-pencil"></i></a>';
        })
        ->addColumn('nohbl', function($manifest){
            return $manifest->nohbl ?? '-';
        })
        ->addColumn('tgl_hbl', function($manifest){
            return $manifest->tgl_hbl ?? '-';
        })
        ->addColumn('notally', function($manifest){
            return $manifest->notally ?? '-';
        })
        ->addColumn('barcode', function($manifest){
            return $manifest->barcode ?? '-';
        })
        ->addColumn('shipper', function($manifest){
            return $manifest->shipperM->name ?? '-';
        })
        ->addColumn('customer', function($manifest){
            return $manifest->customer->name ?? '-';
        })
        ->addColumn('quantity', function($manifest){
            return $manifest->quantity ?? '-';
        })
        ->addColumn('final_qty', function($manifest){
            return $manifest->final_qty ?? '-';
        })
        ->addColumn('packingName', function($manifest){
            return $manifest->packing->name ?? '-';
        })
        ->addColumn('packingCode', function($manifest){
            return $manifest->packing->code ?? '-';
        })
        ->addColumn('desc', function($manifest){
            $desc = $manifest->descofgoods ?? '-';
            return '<textarea class="form-control" cols="3" readonly>'.$desc.'</textarea>';
        })
        ->addColumn('weight', function($manifest){
            return $manifest->weight ?? '-';
        })
        ->addColumn('meas', function($manifest){
            return $manifest->meas ?? '-';
        })
        ->addColumn('startStripping', function($manifest){
            return $manifest->startstripping ?? '-';
        })
        ->addColumn('endstripping', function($manifest){
            return $manifest->endstripping ?? '-';
        })
        ->rawColumns(['action', 'desc'])
        ->make(true);
    }

    public function detail($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        $item = Item::where('manifest_id', $id)->get();
        $data['title'] = "Racking Manifest || " . $manifest->nohbl;
        $data['locs'] = PM::whereNot('use_for', 'B')->orderBy('name', 'asc')->get();
        $data['manifest'] = $manifest;
        $data['placed'] = Item::where('manifest_id', $id)->whereNot('lokasi_id', null)->get();
        $data['item'] = Item::where('manifest_id', $id)->where('lokasi_id', null)->get();
        $data['kets'] = KP::where('kegiatan', '=', 'placement')->get();

        return view('lcl.realisasi.racking.detail', $data);
    }

    public function itemTableData($id,Request $request)
    {
        $item = Item::where('manifest_id', $id)->whereNot('lokasi_id', null)->get();
        
        return DataTables::of($item)
        ->addColumn('action', function($item){
            return '<button class="btn btn-outline-danger unPlace" data-id="'.$item->id.'">Batal Placement</button>';
        })
        ->addColumn('barcode', function($item){
            $herf = '/lcl/realisasi/racking/itemBarcode-'; 
            return '<a href="javascript:void(0)" onclick="openWindow(\''.$herf.$item->id.'\')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
        })
        ->addColumn('name', function($item){
            return $item->name ?? '-';
        })
        ->addColumn('nomor', function($item){
            return $item->nomor ?? '-';
        })
        ->addColumn('rack', function($item){
            return $item->Rack->name ?? '-';
        })
        ->addColumn('tier', function($iten){
            return $item->tier ?? '-';
        })
        ->rawColumns(['action', 'barcode'])
        ->make(true);
    }

    public function itemBarcode($id)
    {
        $data['item'] = Item::where('id', $id)->first();
        $data['title'] = 'Barcode Packing LCL Manifest || ' . $data['item']->name;

        return view('item.barcodeSingle', $data);
    }

    public function update(Request $request)
    {
        $placements = $request->input('placements', []);
    
        // Decode JSON strings into arrays
        $decodedPlacements = array_map(function ($placement) {
            return json_decode($placement, true);
        }, $placements);
        $jumlahItem = count($decodedPlacements);
        // dd($decodedPlacements, $request->lokasi_id, $jumlahItem);

        $lokasi = PM::where('id', $request->lokasi_id)->first();
        $tier = RT::where('rack_id', $lokasi->id)->where('tier', $request->tier)->First();
        // dd($tier);
        if (!$tier) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Tier Tidak Ditemukan']);
        }
        if ($lokasi) {
            foreach ($decodedPlacements as $drag) {
                $item = Item::where('id', $drag['item_id'])->first();
                if ($item) {
                    $item->update([
                        'lokasi_id' => $request->lokasi_id,
                        'tier' => $request->tier,
                    ]);
                    $tier->jumlah_barang = $tier->jumlah_barang + 1;
                    $tier->save();
                    $lokasi->increment('jumlah_barang', 1);
                }
            }
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data updated!!']);
        }else {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Rack Tidak Ditemukan']);
        }
    }
    
    public function unPlace(Request $request)
    {
        $ids = $request->input('ids', []); // Get the array of IDs from the request

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No items selected for unPlace.',
            ]);
        }

        foreach ($ids as $id) {
            $item = Item::find($id);

            if ($item) {
                $lokasi = PM::find($item->lokasi_id);
                $tier = RT::where('rack_id', $lokasi->id)->where('tier', $item->tier)->first();

                if ($lokasi) {
                    $lokasi->decrement('jumlah_barang', 1);
                    $tier->jumlah_barang = $tier->jumlah_barang - 1;
                    $tier->save();

                    $item->update([
                        'lokasi_id' => null,
                        'tier' => null
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Lokasi not found for item ID: ' . $id,
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found with ID: ' . $id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Selected items have been unPlaced successfully.',
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();
        if ($manifest) {
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imagesInt', $fileName, 'public'); 
                    $newPhoto = Photo::create([
                        'master_id' => $manifest->id,
                        'type' => 'manifest',
                        'action' => 'placement',
                        'detil' => $request->keteranganPhoto,
                        'photo' => $fileName,
                    ]);
                }
                return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data berhasil di update']);
            }
        }
    }

    public function photoPlacement(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();
        // dd($manifest, $request->id);
        $data['title'] = "Photo Placement Manifest - " . $manifest->notally ?? '';
        $data['item'] = $manifest;
        $data['photos'] = Photo::where('master_id', $manifest->id)->where('type', '=', 'manifest')->where('action', '=', 'placement')->get();
        // dd($data['photos']);
        return view('photo.index', $data);
    }
}
