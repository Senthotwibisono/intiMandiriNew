<?php

namespace App\Http\Controllers\invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

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

use DataTables;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function unpaidIndex()
    {
        $data['title'] = 'Unpaid Invoice';
        $data['headers'] = Header::where('status', 'N')
        ->where('type', null)
        ->orderBy('order_at', 'desc')
        ->get();

        return view('invoice.unpaid.index', $data);
    }

    // public function unpaidData(Request $request)
    // {
    //     $invoice = Header::with(['manifest', 'customer', 'kasir'])->where('status', 'N')->orderBy('order_at', 'desc')->get();
    //     // dd($invoice);

    //     return DataTables::of($invoice)
    //     ->addColumn('orderNo', function ($invoice) {
    //         return $invoice->order_no ?? '-';
    //     })
    //     ->addColumn('hbl', function ($invoice) {
    //         return $invoice->manifest->nohbl ?? '-';
    //     })
    //     ->addColumn('tglHBL', function ($invoice) {
    //         return $invoice->manifest->tgl_hbl ?? '-';
    //     })
    //     ->addColumn('quantity', function ($invoice) {
    //         return $invoice->manifest->quantity ?? '-';
    //     })
    //     ->addColumn('customer', function ($invoice) {
    //         return $invoice->customer->name ?? '-';
    //     })
    //     ->addColumn('kasir', function ($invoice) {
    //         return $invoice->kasir->name ?? '-';
    //     })
    //     ->addColumn('orderAt', function ($invoice) {
    //         return $invoice->order_at ?? '-';
    //     })
    //     ->addColumn('pranota', function ($invoice) {
    //         $disabled = $invoice->status === 'C' ? 'disabled' : '';
    //         return '<a type="button" href="/invoice/pranota-' . $invoice->id . '" target="_blank" class="btn btn-sm btn-warning text-white ' . $disabled . '">
    //                     <i class="fa fa-file"></i>
    //                 </a>';
    //     })
    //     ->addColumn('photoKTP', function ($invoice) {
    //         $disabled = $invoice->status === 'C' ? 'disabled' : '';
    //         return '<a href="javascript:void(0)" onclick="openWindow(\'/invoice/photoKTP-' . $invoice->id . '\')" class="btn btn-sm btn-info ' . $disabled . '">
    //                     <i class="fa fa-eye"></i>
    //                 </a>';
    //     })
    //     ->addColumn('action', function ($invoice) {
    //         $disabled = $invoice->status === 'C' ? 'disabled' : '';
    //         return '<div class="button-container text-center">
    //                     <button class="btn btn-danger cancelInvoice" data-id="' . $invoice->id . '" ' . $disabled . '>
    //                         <i class="fa fa-trash"></i>
    //                     </button>
    //                     <button type="button" id="pay" data-id="' . $invoice->id . '" class="btn btn-sm btn-success pay ' . $disabled . '">
    //                         <i class="fa fa-cogs"></i>
    //                     </button>
    //                     <a href="" class="btn btn-primary ' . $disabled . '">Revisi</a>
    //                 </div>';
    //     })
    //     ->rawColumns(['pranota', 'photoKTP', 'action']) // Render HTML pada kolom ini
    //     ->make(true);
    // }

    public function pranotaIndex($id)
    {
        $data['title'] = 'Print Preinvoice';
        $header = Header::find($id);
        $form = Form::where('id', $header->form_id)->first();
        $data['header'] = $header;
        $data['form'] = $form;

        // dd($header);
        if ($header->mekanik_y_n =='N') {
            $data['tarifs'] = FormT::where('form_id', $form->id)->where('mekanik_y_n', '=', 'N')->get();
        }else {
            $data['tarifs'] = FormT::where('form_id', $form->id)->where('mekanik_y_n', '=', 'Y')->get();
        }
        $data['terbilang'] = $this->terbilang($header->grand_total);

        return view('invoice.pranota', $data);
    }

    public function deleteInvoice($id)
    {
        // Find the header record
        $header = Header::find($id);

        if ($header) {
            // Find the related form record
            $form = Form::find($header->form_id);

            if ($form) {
                $allHeader = Header::where('form_id', $form->id)->get();
                foreach ($allHeader as $header) {
                    $header->update([
                        'status' => 'C',
                        'cancel_at' => carbon::now(),
                        'cancel_id' => Auth::user()->name,
                    ]);
                }

                return response()->json(['success' => 'Invoice deleted successfully']);
            } else {
                return response()->json(['error' => 'Form not found'], 404);
            }
        } else {
            return response()->json(['error' => 'Header not found'], 404);
        }
    }

    public function invoiceGetData($id)
    {
        $header = Header::find($id);
        if ($header) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $header,
            ]);
        }
    }

    public function invoicePaid(Request $request)
{
    try {
       

        // Check if the 'ktp' input is present
        if ($request->has('ktp')) {
            // Get the array of base64 strings from the request
            $base64Image = $request->input('ktp');

            // Loop through each base64 string
           
                // Remove the "data:image/png;base64," part (if necessary)
                $image = str_replace('data:image/png;base64,', '', $base64Image);
                $image = str_replace(' ', '+', $image); // Ensure there are no spaces

                // Decode the base64 image
                $imageData = base64_decode($image);

                // Generate a filename
                $fileName = 'ktp_' . time() . '_' . uniqid() . '.png'; // Unique filename

                // Specify the path to save the image in storage/app/public/ktp
                $path = storage_path('app/public/ktp/' . $fileName);

                // Store the image in the storage directory
                file_put_contents($path, $imageData);
        } else {
            $fileName = null; // Handle the case where no file was uploaded
        }

        $header = Header::find($request->id);
        switch ($request->status) {
            case 'P':
                $kasirP = Auth::user()->id;
                $timeP = Carbon::now();
                $kasirL = null;
                $timeL = null;
                $status = 'P';
                break;

            case 'Y':
                $kasirP = null;
                $timeP = null;
                $kasirL = Auth::user()->id;
                $timeL = Carbon::now();
                $status = 'Y';
                break;

            default:
                $kasirP = null;
                $timeP = null;
                $kasirL = null;
                $timeL = null;
                $status = 'N';
                break;
        }

        if ($status == 'N') {
            $noInvoice = null;
        }else {
            if ($header->invoice_no != null) {
                $noInvoice = $header->invoice_no;
            }else {
                $consolidatorCode = $header->manifest->cont->job->consolidator->code;

                // Get the last two digits of the current year
                $year = Carbon::now()->format('y'); // '24' for 2024

                // Get the last inserted sequential number from the Header table
                $lastInvoice = Header::whereYear('order_at', Carbon::now()->year)->whereNotNull('invoice_no')
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
                $noInvoice = 'LKB-' . $consolidatorCode . '/' . $year . '/' . $newSequence;
            }
        }
        // dd($noInvoice);

        $header->update([
            'invoice_no' => $noInvoice,
            'status' => $status,
            'piutang_at' => $timeP,
            'kasir_piutang_id' => $kasirP,
            'lunas_at' => $timeL,
            'kasir_lunas_id' => $kasirL,
            'ktp' => $fileName, // Save all filenames as JSON if multiple
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Berhasil di Simpan']);
    } catch (\Throwable $th) {
        return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Opss Somtehing Wrong: ' . $th->getMessage()]);
    }
}


    public function invoiceIndex($id)
    {
        $data['title'] = 'Print Invoice';
        $header = Header::find($id);
        $form = Form::where('id', $header->form_id)->first();
        $data['header'] = $header;
        $data['form'] = $form;

        // dd($header);
        $data['tarifs'] = FormT::where('form_id', $form->id)->get();
        $data['terbilang'] = $this->terbilang($header->grand_total);

        return view('invoice.invoice', $data);
    }

    public function paidIndex()
    {
        $data['title'] = 'List Invoice Paid';
        $data['headers'] = Header::whereNot('status', '=', 'N')->where('type', null)->orderBy('order_at', 'desc')->get();
        $data['doks'] = Kode::orderBy('kode', 'asc')->get();

        return view('invoice.paid.index', $data);
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

    public function photoKTP($id)
    {
        $header = Header::where('id', $id)->first();
        // dd($manifest);
        $data['title'] = "Photo KTP - " . $header->customer->name;
        $data['header'] = $header;

        return view('invoice.ktp', $data);
    }

    public function barcodeIndex($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        $data['title'] = 'Barcode Packing LCL Manifest || ' . $manifest->notally;
        $data['item'] = Item::where('manifest_id', $manifest->id)->first();

        return view('invoice.manifest.barcode', $data);
    }

    public function invoiceGetManifestData($id)
    {
        $manifest = Manifest::find($id);
        if ($manifest) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $manifest,
            ]);
        }
    }

    public function invoiceUpdateDokumen(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();

        $kdDok = $request->kd_dok;
        $tglDok = Carbon::parse($request->tgl_dok)->format('n/j/Y');
        $tglDokManual = Carbon::parse($request->tgl_dok)->format('d/m/Y');
        // var_dump($tglDok, $request->no_dok, $request->kd_dok);
        // die();
        if ($kdDok == 1) {
            $dok = SPPB::where('no_sppb', $request->no_dok)->where('tgl_sppb', $tglDok)->first();
            if ($dok) {
                $manifest->update([
                    'kd_dok_inout' => $kdDok,
                    'no_dok' => $request->no_dok,
                    'tgl_dok' => $request->tgl_dok,
                    'status_bc' => 'release',
                ]);

                return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data ditemukan, Status Dokumen : ' . $manifest->status_bc]);
            }else {
                return redirect()->back()->with('status', ['type' => 'success', 'error' => 'Data tidak ditemukan']);
            }
        }elseif ($kdDok == 2) {
            $dok = BC23::where('no_sppb', $request->no_dok)->where('tgl_sppb', $tglDok)->first();
            if ($dok) {
                $manifest->update([
                    'kd_dok_inout' => $kdDok,
                    'no_dok' => $request->no_dok,
                    'tgl_dok' => $request->tgl_dok,
                    'status_bc' => 'HOLD',
                ]);

                return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data ditemukan, Status Dokumen : ' . $manifest->status_bc]);
            }else {
                return redirect()->back()->with('status', ['type' => 'success', 'error' => 'Data tidak ditemukan']);
            }
        }else {
            $dok = Manual::where('kd_dok_inout', $kdDok)->where('no_dok_inout', $request->no_dok)->where('tgl_dok_inout', $tglDokManual)->first();
            if ($dok) {
                $manifest->update([
                    'kd_dok_inout' => $kdDok,
                    'no_dok' => $request->no_dok,
                    'tgl_dok' => $request->tgl_dok,
                    'status_bc' => 'HOLD',
                ]);
                return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data ditemukan, Status Dokumen : ' . $manifest->status_bc]);
            }else {
                return redirect()->back()->with('status', ['type' => 'success', 'error' => 'Data tidak ditemukan']);
            }
        }
    }

}
