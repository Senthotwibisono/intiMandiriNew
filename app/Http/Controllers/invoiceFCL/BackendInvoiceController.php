<?php

namespace App\Http\Controllers\invoiceFCL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use DataTables;

use App\Models\Customer;
use App\Models\ContainerFCL as ContF;
use App\Models\JobOrderFCL as JobF;
use App\Models\FCL\FormContainerFCL as FormC;
use App\Models\FCL\FormFCL as Form;
use App\Models\FCL\MTarifTPS as TTPS;
use App\Models\FCL\MTarifWMS as TWMS;
use App\Models\FCL\InvoiceHeader as Header;
use App\Models\FCL\InvoiceDetil as Detil;

class BackendInvoiceController extends Controller
{
    public function dataTable(Request $request)
    {
        $header = Header::get();

        return DataTables::of($header)
        ->addColumn('invoiceNo', function($header){
            return $header->invoice_no ?? 'Anda belum Melakukan Pembayaran';
        })
        ->addColumn('createdBy', function($header){
            return $header->userCreate->name ?? '-';
        })
        ->addColumn('pranota', function($header){
            return '<a type="button" href="/invoiceFCL/invoice/pranota-'.$header->id.'" target="_blank" class="btn btn-sm btn-warning text-white"><i class="fa fa-file"></i></a>';
        })
        ->addColumn('invoice', function($header){
            return '<a type="button" href="/invoiceFCL/invoice/invoice-'.$header->id.'" target="_blank" class="btn btn-sm btn-info text-white"><i class="fa fa-file"></i></a>';
        })
        ->addColumn('action', function($header){
            if ($header->status != 'Y') {
                return '<button class="btn btn-success" id="paidButton" data-id="'.$header->id.'">Action</button>';
            }else {
                return '<span class="badge bg-info text-white">Lunas</span>';
            }
        })
        ->rawColumns(['pranota', 'invoice', 'action'])
        ->make(true);
    }

    public function pranota($id)
    {
        $data['title'] = 'Pranota FCL';
        $data['header'] = Header::find($id);
        
        $container = FormC::where('form_id', $data['header']->form_id)->get();
        $data['jenisContainer'] = $container->pluck('size')->unique()->implode(', ');
        $data['typeContainer'] = $container->pluck('ctr_type')->unique()->implode(', ');

        $data['size'] = $container->pluck('size')->unique();
        $data['type'] = $container->pluck('ctr_type')->unique();
        $data['nocontainer'] = $container->pluck('cont.nocontainer')->implode(', ');

        $data['detilTPS'] = Detil::where('invoice_id', $id)->whereNot('tps', '=', 'Depo')->orderByRaw("CASE 
        WHEN keterangan LIKE 'Penumpukkan Massa 1%' THEN 1
        WHEN keterangan LIKE 'Penumpukkan Massa 2%' THEN 2
        WHEN keterangan LIKE 'Penumpukkan Massa 3%' THEN 3
        ELSE 4 
        END")->orderBy('keterangan', 'desc')->get();
        $data['detilWMS'] = Detil::where('invoice_id', $id)->where('tps', '=', 'Depo')->orderByRaw("CASE 
        WHEN keterangan LIKE 'Penumpukan %' THEN 1
        WHEN keterangan LIKE 'Paket PLP %' THEN 2
        WHEN keterangan LIKE 'Lift On %' THEN 3
        WHEN keterangan LIKE 'Lift Off %' THEN 4
        ELSE 5
        END")->orderBy('keterangan', 'desc')->get();

        $data['terbilang'] = $this->terbilang($data['header']->grand_total);
        // dd($data['terbilang']);

        return view('invoiceFCL.invoice.pranota', $data);
    }

    public function Invoice($id)
    {
        $data['title'] = 'Invoice FCL';
        $data['header'] = Header::find($id);

        if ($data['header']->status != 'Y') {
            return redirect()->back()->with('status', ['type'=> 'error', 'message' => 'Invoice belum di lunasi, anda di larang membuka halaman ini']);
        }
        
        $container = FormC::where('form_id', $data['header']->form_id)->get();
        $data['jenisContainer'] = $container->pluck('size')->unique()->implode(', ');
        $data['typeContainer'] = $container->pluck('ctr_type')->unique()->implode(', ');

        $data['size'] = $container->pluck('size')->unique();
        $data['type'] = $container->pluck('ctr_type')->unique();
        $data['nocontainer'] = $container->pluck('cont.nocontainer')->implode(', ');

        $data['detilTPS'] = Detil::where('invoice_id', $id)->whereNot('tps', '=', 'Depo')->orderByRaw("CASE 
        WHEN keterangan LIKE 'Penumpukkan Massa 1%' THEN 1
        WHEN keterangan LIKE 'Penumpukkan Massa 2%' THEN 2
        WHEN keterangan LIKE 'Penumpukkan Massa 3%' THEN 3
        ELSE 4 
        END")->orderBy('keterangan', 'desc')->get();
        $data['detilWMS'] = Detil::where('invoice_id', $id)->where('tps', '=', 'Depo')->orderByRaw("CASE 
        WHEN keterangan LIKE 'Penumpukan %' THEN 1
        WHEN keterangan LIKE 'Paket PLP %' THEN 2
        WHEN keterangan LIKE 'Lift On %' THEN 3
        WHEN keterangan LIKE 'Lift Off %' THEN 4
        ELSE 5
        END")->orderBy('keterangan', 'desc')->get();

        $data['terbilang'] = $this->terbilang($data['header']->grand_total);
        // dd($data['terbilang']);

        return view('invoiceFCL.invoice.invoice', $data);
    }

    public function paidInvoice(Request $request)
    {
        $year = Carbon::now()->format('y'); // '24' for 2024
        // Get the last inserted sequential number from the Header table
        $lastInvoice = Header::whereYear('created_at', Carbon::now()->year)->whereNotNull('invoice_no')
                             ->orderBy('invoice_no', 'desc')
                             ->first();
                             if ($lastInvoice) {
                                // Remove '-P' if it exists at the end of the invoice number
                                $invoiceNumber = rtrim($lastInvoice->invoice_no, ' -P');
                                
                                // Extract the numeric part from the invoice number
                                if (preg_match('/(\d+)$/', $invoiceNumber, $matches)) {
                                    $lastSequence = (int)$matches[0]; // Extract the numeric part
                                } else {
                                    $lastSequence = 0; // If no valid sequence is found, start from 0
                                }
                            } else {
                                $lastSequence = 0; // If no previous invoice, start from 0
                            }
    
        // dd($lastInvoice,$lastSequence);
        // Increment the sequence and format as a 6-digit number
        $newSequence = str_pad($lastSequence + 1, 6, '0', STR_PAD_LEFT);
    
        // Construct the new invoice number
        $noInvoice = 'ITM-' . 'FCL' . '/' . $year . '/' . $newSequence;

        $header = Header::find($request->id);
        $header->update([
            'invoice_no' => $noInvoice,
            'lunas_at' => Carbon::now(),
            'uidLunas' => Auth::user()->id,
            'status' => 'Y',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice Has Been Paid',
        ]);
    }

    private function terbilang($number)
    {
        $x = abs($number);
        $angka = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");

        $result = "";
        if ($x < 12) {
            $result = " " . $angka[$x];
        } elseif ($x < 20) {
            $result = $this->terbilang($x - 10) . " Belas";
        } elseif ($x < 100) {
            $result = $this->terbilang($x / 10) . " Puluh" . $this->terbilang($x % 10);
        } elseif ($x < 200) {
            $result = " Seratus" . $this->terbilang($x - 100);
        } elseif ($x < 1000) {
            $result = $this->terbilang($x / 100) . " Ratus" . $this->terbilang($x % 100);
        } elseif ($x < 2000) {
            $result = " Seribu" . $this->terbilang($x - 1000);
        } elseif ($x < 1000000) {
            $result = $this->terbilang($x / 1000) . " Ribu" . $this->terbilang($x % 1000);
        } elseif ($x < 1000000000) {
            $result = $this->terbilang($x / 1000000) . " Juta" . $this->terbilang($x % 1000000);
        } elseif ($x < 1000000000000) {
            $result = $this->terbilang($x / 1000000000) . " Milyar" . $this->terbilang(fmod($x, 1000000000));
        } elseif ($x < 1000000000000000) {
            $result = $this->terbilang($x / 1000000000000) . " Trilyun" . $this->terbilang(fmod($x, 1000000000000));
        }

        return $result;
    }
}
