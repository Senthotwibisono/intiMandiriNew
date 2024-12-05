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
        $data['locs'] = PM::whereNot('use_for', 'B')->orderBy('name', 'asc')->get();
        $data['manifest'] = $manifest;
        $data['placed'] = Item::where('manifest_id', $id)->whereNot('lokasi_id', null)->get();
        $data['item'] = Item::where('manifest_id', $id)->where('lokasi_id', null)->get();
        $data['kets'] = KP::where('kegiatan', '=', 'placement')->get();

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
