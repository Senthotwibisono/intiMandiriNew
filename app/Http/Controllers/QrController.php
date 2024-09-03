<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Item;

class QrController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $data['title'] = 'Testing Qr Reader';

        return view('qr.index', $data);
    }

    public function detail($qr)
    {
        $item = Item::with('manifest')->where('barcode', $qr)->first();
        $manifest = $item->manifest;
        
        // dd($manifest, $item);

        $data['selectedItem'] = $item;
        $data['item'] = Item::where('manifest_id', $manifest->id)->get();
        $data['title'] = 'Manifest || ' .$manifest->notally . ' Barang Nomor ' . $item->nomor;
        return view('qr.detail', $data);

    }
}