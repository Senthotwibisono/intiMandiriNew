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

    public function unpaidData(Request $request)
    {
        $header = Header::with(['manifest', 'customer', 'kasir'])->where('status', 'N')
        ->where('type', null)
        ->orderBy('order_at', 'desc')
        ->get();

        return DataTables::of($header)
        ->addColumn('order_no', function($header){
            return $header->order_no ?? '-';
        })
        ->addColumn('nohbl', function($header){
            return $header->manifest->nohbl ?? '-';
        })
        ->addColumn('tgl_hbl', function($header){
            return $header->manifest->tgl_hbl ?? '-';
        })
        ->addColumn('quantity', function($header){
            return $header->manifest->quantity ??  '-';
        })
        ->addColumn('customerName', function($header){
            return $header->customer->name ?? '-';
        })
        ->addCOlumn('kasir', function($header){
            return $header->kasir->name ?? '-';
        })
        ->addColumn('orderAt', function($header){
            return $header->order_at ?? '-';
        })
        ->addColumn('pranota', function($header){
            return '<a type="button" href="/invoice/pranota-'. $header->id .'" target="_blank" class="btn btn-sm btn-warning text-white"><i class="fa fa-file"></i></a>';
        })
        ->addColumn('ktp', function($header){
            $herf = '/invoice/photoKTP-' . $header->id;
            return '<a href="javascript:void(0)" onclick="openWindow(\''.$herf.'\')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
        })
        ->addColumn('cancel', function($header){
            return '<button class="btn btn-danger cancelInvoice" data-id="'.$header->id.'"><i class="fa fa-trash"></i></button>';
        })
        ->addColumn('pay', function($header){
            return '<button type="button" id="pay" data-id="'.$header->id.'" class="btn btn-sm btn-success pay"><i class="fa fa-cogs"></i></button>';
        })
        ->addColumn('revisi', function($header){
            return '<button class="btn btn-primary revisiInvoice" data-id="'.$header->form_id.'">Revisi</button>';
        })
        ->rawColumns(['pranota', 'ktp', 'cancel', 'pay', 'revisi'])
        ->make(true);
    }

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

            $allHeader = Header::where('form_id', $header->form_id)->get();
            foreach ($allHeader as $header) {
                $header->update([
                    'status' => 'C',
                    'cancel_at' => carbon::now(),
                    'cancel_id' => Auth::user()->name,
                ]);
            }
            return response()->json(['success' => 'Invoice deleted successfully']);
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
                // dd($header->Form->Forwarding->code);
                $forwardingCode = substr($header->Form->Forwarding->code, 0, 3);
                if (!$forwardingCode) {
                    return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Forwarding belum memiliki code, harap lengkapi terlebih dahulu']);
                }

                // Get the last two digits of the current year
                $year = Carbon::now()->format('y'); // '24' for 2024

                // Get the last inserted sequential number from the Header table
               // Ambil invoice terakhir berdasarkan tahun order
                $lastInvoice = Header::whereYear('order_at', Carbon::now()->year)
                ->whereNotNull('invoice_no')
                ->orderByRaw("CAST(REGEXP_SUBSTR(invoice_no, '[0-9]+$') AS UNSIGNED) DESC")
                ->first();
                            
                if ($lastInvoice) {
                // Hapus '-P' jika ada
                $invoiceNumber = str_replace('-P', '', $lastInvoice->invoice_no);
                
                // Ambil angka terakhir dari invoice
                if (preg_match('/(\d+)$/', $invoiceNumber, $matches)) {
                    $lastSequence = (int)$matches[0];
                } else {
                    $lastSequence = 0; // Jika tidak ditemukan angka, mulai dari 0
                }
                } else {
                $lastSequence = 0; // Jika belum ada invoice, mulai dari 0
                }
                
                // Tambah 1 ke sequence terakhir dan format menjadi 6 digit angka
                $newSequence = str_pad($lastSequence + 1, 6, '0', STR_PAD_LEFT);
                
                // Ambil kode forwarding (pastikan tidak null)
                $forwardingCode = substr($header->Form->Forwarding->code ?? 'XXX', 0, 3);
                if (!$forwardingCode) {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Forwarding belum memiliki kode!']);
                }
                
                // Ambil 2 digit terakhir dari tahun saat ini
                $year = Carbon::now()->format('y');
                
                // Buat nomor invoice baru
                $noInvoice = "LKB-$forwardingCode/IGM/$year/$newSequence";
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
