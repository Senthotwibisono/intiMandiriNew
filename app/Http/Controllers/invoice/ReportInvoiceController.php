<?php

namespace App\Http\Controllers\invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

use App\Models\MasterTarif as MT;
use App\Models\InvoiceForm as Form;
use App\Models\InvoiceFormTarif as FormT;
use App\Models\Manifest;
use App\Models\Item;
use App\Models\Customer;
use App\Models\InvoiceHeader as Header;

// Dok
use App\Models\TpsSPJM as SPJM;
use App\Models\TpsSPPB as SPPB;
use App\Models\TpsSPPBCont as SPPBCont;
use App\Models\TpsSPPBKms as SPPBKms;
use App\Models\KodeDok as Kode;
use App\Models\TpsManual as Manual;
use App\Models\TpsManualCont as ManualCont;
use App\Models\TpsManualKms as ManualKms;
use App\Models\TpsSPPBBC23 as BC23;
use App\Models\TpsSPPBBC23Cont as BC23Cont;
use App\Models\TpsSPPBBC23Kms as BC23Kms;
use App\Models\BarcodeGate as Barcode;
use App\Models\PlacementManifest as PM;

use App\Exports\invoice\ReportInvoice;
use App\Exports\invoice\ReportProforma;

class ReportInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['title'] = 'Report Invoice';

        return view('invoice.report.index', $data);
    }

    public function generateExcel(Request $request)
    {
        try {
            $filter = $request->filter;
              switch ($filter) {
                  case 'N':
                      $headers = Header::where('status', '=', 'N')->whereBetween('order_at', [$request->start_date, $request->end_date])->orderBy('order_no', 'asc')->get();
                        $fileName = 'ReportProforma-' . $request->start_date . '-' . $request->end_date . '.xlsx';
          
                        return Excel::download(new ReportProforma($headers), $fileName);
                      break;
                  
                  case 'L':
                        $headers = Header::whereNot('status', '=', 'N')->where(function($query) use ($request) {
                            $query->whereBetween('lunas_at', [$request->start_date, $request->end_date])
                                  ->orWhereBetween('piutang_at', [$request->start_date, $request->end_date]);
                        })
                        ->orderBy('invoice_no', 'asc')
                        ->get();
                        $fileName = 'ReportInvoice-' . $request->start_date . '-' . $request->end_date . '.xlsx';
                    
                        return Excel::download(new ReportInvoice($headers), $fileName);
                      break;

                  default :  
                      
                      break;
              }
          
              
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Gagal di Simpan '. $th->getMessage()]);
        }
    }

    public function generatePdf(Request $request)
{
    try {
        $filter = strtoupper($request->filter); // Handle case sensitivity
        
        switch ($filter) {
            case 'N':
                $headers = Header::where('status', '=', 'N')
                    ->whereBetween('order_at', [$request->start_date, $request->end_date])
                    ->orderBy('order_no', 'asc')
                    ->get();
                
                $pdf = PDF::loadView('invoice.report.pdf', compact('headers', 'filter')); // Assuming a 'reports.proforma' view exists
                $fileName = 'ReportProforma-' . $request->start_date . '-' . $request->end_date . '.pdf';

                return $pdf->download($fileName);

            case 'L':
                $headers = Header::whereNot('status', '=', 'N')
                    ->where(function($query) use ($request) {
                        $query->whereBetween('lunas_at', [$request->start_date, $request->end_date])
                              ->orWhereBetween('piutang_at', [$request->start_date, $request->end_date]);
                    })
                    ->orderBy('invoice_no', 'asc')
                    ->get();
                
                $pdf = PDF::loadView('invoice.report.pdf', compact('headers', 'filter')); // Assuming a 'reports.invoice' view exists
                $fileName = 'ReportInvoice-' . $request->start_date . '-' . $request->end_date . '.pdf';

                return $pdf->download($fileName);

            default:
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Invalid filter selected.']);
        }

    } catch (\Throwable $th) {
        return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Failed to generate PDF: ' . $th->getMessage()]);
    }
}
}
