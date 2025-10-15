<?php

namespace App\Http\Controllers\CFS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Manifest;
use App\Models\Container as cont;
use App\Models\TarifCFS as Tarif;
use App\Models\InvoiceCSF as Header;
use App\Models\InvoiceCSFDetil as Detil;
use App\Models\BarcodeGate as Barcode;

use Auth;
use DataTables;

class InvoiceCSFController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['title'] = 'Invoice CFS Center';

        return view('cfs.invoice.index', $data);
    }

    public function data(Request $request)
    {
        $header = Header::with(['manifest']);

        return DataTables::of($header)
        ->addColumn('print', function($header){
            return '<button class="btn btn-danger" id="print" data-id="'.$header->id.'"><i class="fas fa-print"></i></button>';
        })
        ->addColumn('customerName', function($header){
            return $header->manifest->customer->name ?? '-';
        })
        ->addColumn('customerNPWP', function($header){
            return $header->manifest->customer->npwp ?? '-';
        })
        ->addColumn('jenis_transaksi', function($header){
            return ($header->jenis_transaksi == 'P') ? '<span class="badge bg-warning text-white">Perpanjangan</span>' : '<span class="badge bg-info text-white">Bukan Perpanjangan</span>';
        })
        ->addColumn('status', function($header){
            return ($header->status == 'Y') ? '<span class="badge bg-success text-white">Lunas</span>' : (($header->status == 'C') ? '<span class="badge bg-danger text-white">Cancel</span>' : '<span class="badge bg-warning text-white">Belum Bayar</span>');
        })
        ->rawColumns(['jenis_transaksi', 'status', 'print'])
        ->make(true);
    }

    public function print(Request $request)
    {
        
        $data['headers'] = Header::whereIn('id', explode(',', $request->ids))->get();
        $data['tarifs'] = Detil::whereIn('header_id', $data['headers']->pluck('id'))->get();
        

        return view('cfs.invoice.print',$data, ['terbilang' => [$this,'terbilang']]);
        // dd($header);
    }

    public function terbilang($angka)
    {
        $angka = abs($angka);
        $bilangan = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

        if ($angka < 12) {
            return " " . $bilangan[$angka];
        } elseif ($angka < 20) {
            return $this->terbilang($angka - 10) . " Belas";
        } elseif ($angka < 100) {
            return $this->terbilang(intval($angka / 10)) . " Puluh" . $this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            return " Seratus" . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return $this->terbilang(intval($angka / 100)) . " Ratus" . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return " Seribu" . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return $this->terbilang(intval($angka / 1000)) . " Ribu" . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return $this->terbilang(intval($angka / 1000000)) . " Juta" . $this->terbilang($angka % 1000000);
        }
    }
    
}
