<?php

namespace App\Http\Controllers\lcl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;

use App\Models\Manifest;
use App\Models\Item;
use App\Models\PlacementManifest as PM;
use App\Models\RackingDetil as Rack;
use App\Models\Photo;



class RackingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['title'] = "Racking Manifest";
        $data['manifest'] = Manifest::whereNot('jamstripping', null)->where('tglbuangmty', null)->get();

        return view('lcl.realisasi.racking.index', $data);
    }

    public function detail($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        $item = Item::where('manifest_id', $id)->get();
        $data['title'] = "Racking Manifest || " . $manifest->nohbl;
        $data['locs'] = PM::whereNot('use_for', 'B')->get();
        $data['manifest'] = $manifest;
        $data['placed'] = Item::where('manifest_id', $id)->whereNot('lokasi_id', null)->get();
        $data['item'] = Item::where('manifest_id', $id)->where('lokasi_id', null)->get();

        return view('lcl.realisasi.racking.detail', $data);
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
        if ($lokasi) {
            foreach ($decodedPlacements as $drag) {
                $item = Item::where('id', $drag['item_id'])->first();
                if ($item) {
                    $item->update([
                        'lokasi_id' => $request->lokasi_id,
                    ]);
                }
            }
            $lokasi->update([
                'jumlah_barang'=> $jumlahItem
            ]);
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

                if ($lokasi) {
                    $lokasi->decrement('jumlah_barang', 1);

                    $item->update([
                        'lokasi_id' => null
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
