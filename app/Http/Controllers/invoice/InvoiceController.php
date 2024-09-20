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
use App\Models\Customer;
use App\Models\InvoiceHeader as Header;

class InvoiceController extends Controller
{
    public function unpaidIndex()
    {
        $data['title'] = 'Unpaid Invoice';
        $data['headers'] = Header::where('status', '=', 'N')->orderBy('order_at', 'desc')->get();

        return view('invoice.unpaid.index', $data);
    }

    public function pranotaIndex($id)
    {
        $data['title'] = 'Print Preinvoice';
        $header = Header::find($id);
        $form = Form::where('id', $header->form_id)->first();
        $data['header'] = $header;
        $data['form'] = $form;

        // dd($header);
        $data['tarifs'] = FormT::where('form_id', $form->id)->get();
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
                // Delete the related FormT records
                FormT::where('form_id', $form->id)->delete();

                // Delete the header and form records
                $header->delete();
                $form->delete();

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
            }

            if ($header->invoice_no != null) {
                $noInvoice = $header->invoice_no;
            }else {
                $consolidatorCode = $header->manifest->cont->job->consolidator->code;

                // Get the last two digits of the current year
                $year = Carbon::now()->format('y'); // '24' for 2024

                // Get the last inserted sequential number from the Header table
                $lastInvoice = Header::whereYear('order_at', Carbon::now()->year)
                                     ->orderBy('id', 'desc')
                                     ->first();

                if ($lastInvoice && preg_match('/\d+$/', $lastInvoice->invoice_no, $matches)) {
                    $lastSequence = (int)$matches[0]; // Extract the numeric part
                } else {
                    $lastSequence = 0; // If no previous invoice, start from 0
                }
            
                // Increment the sequence and format as a 6-digit number
                $newSequence = str_pad($lastSequence + 1, 6, '0', STR_PAD_LEFT);
            
                // Construct the new invoice number
                $noInvoice = 'LKB-' . $consolidatorCode . '/' . $year . '/' . $newSequence;
            }

            $header->update([
                'invoice_no' =>$noInvoice,
                'status' => $status,
                'piutang_at' => $timeP,
                'kasir_piutang_id' => $kasirP,
                'lunas_at' => $timeL,
                'kasir_lunas_id' => $kasirL,
            ]);

            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Berhasil di Simpan']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Opss Somtehing Wrong' . $th->getMessage()]);
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
        $data['headers'] = Header::whereNot('status', '=', 'N')->orderBy('order_at', 'desc')->get();

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
}
