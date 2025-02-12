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
        $header = Header::orderBy('created_at', 'desc')->orderBy('proforma_no', 'desc')->orderBy('invoice_no', 'desc');

        return DataTables::of($header)
        ->addColumn('invoiceNo', function($header){
            if ($header->status == 'C') {
                return '<span class="badge bg-danger text-white">Canceled</span>';
            }else {
                # code...
                return $header->invoice_no ?? 'Anda belum Melakukan Pembayaran';
            }
        })
        ->addColumn('createdBy', function($header){
            return $header->userCreate->name ?? '-';
        })
        ->addColumn('pranota', function($header){
            if ($header->status == 'C') {
                return '<span class="badge bg-danger text-white">Canceled</span>';
            }else {
                # code...
                return '<a type="button" href="/invoiceFCL/invoice/pranota-'.$header->id.'" target="_blank" class="btn btn-sm btn-warning text-white"><i class="fa fa-file"></i></a>';
            }
        })
        ->addColumn('invoice', function($header){
            if ($header->status == 'C') {
                return '<span class="badge bg-danger text-white">Canceled</span>';
            }else {
                # code...
                return '<a type="button" href="/invoiceFCL/invoice/invoice-'.$header->id.'" target="_blank" class="btn btn-sm btn-info text-white"><i class="fa fa-file"></i></a>';
            }
        })
        ->addColumn('action', function($header){
            if ($header->status == 'Y') {
                return '<span class="badge bg-info text-white">Lunas</span>';
            }elseif ($header->status == 'C') {
                return '<span class="badge bg-danger text-white">Canceled</span>';
            } else {
                return '<button class="btn btn-success" id="paidButton" data-id="'.$header->id.'">Action</button>';
            }
        })
        ->addColumn('deleteOrCancel', function($header){
            if ($header->status == 'Y') {
                return '<button type="button" data-id="'.$header->id.'" class="btn btn-danger cancelButton">Cancel</button>';
            }elseif ($header->status == 'C') {
                return '<span class="badge bg-danger text-white">Canceled</span>';
            }else {
                return '<button type="button" data-id="'.$header->id.'" class="btn btn-danger deleteInvoice"><i class="fa fa-trash"></i></button>';
            }
        })
        ->addColumn('edit', function($header){
            if ($header->status == 'Y') {
                return '<a href="/invoiceFCL/invoice/edit/'.$header->id.'" class="btn btn-info editInvoice"><i class="fa fa-pencil"></i></a>';
            }elseif ($header->status == 'C') {
                return '<span class="badge bg-danger text-white">Canceled</span>';
            } else {
                return '<span class="badge bg-warning text-white">Belum Lunas</span>';
            }
        })
        ->rawColumns(['invoiceNo', 'pranota', 'invoice', 'action', 'deleteOrCancel', 'edit'])
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

        if ($data['header']->type == 'EXTEND') {
            return view('invoiceFCL.invoice.pranotaExtend', $data);
        }else {
            return view('invoiceFCL.invoice.pranota', $data);
        }
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
        if ($data['header']->type == 'EXTEND') {
            return view('invoiceFCL.invoice.invoiceExtend', $data);
        }else {
            return view('invoiceFCL.invoice.invoice', $data);
        }
        
    }

    public function paidInvoice(Request $request)
    {
        $year = Carbon::now()->format('y'); // Misalnya '24' untuk tahun 2024

        // Cari invoice terakhir di tahun yang sama
        $lastInvoice = Header::whereYear('created_at', Carbon::now()->year)
                             ->whereNotNull('invoice_no')
                             ->orderBy('invoice_no', 'desc')
                             ->first();
        
        if ($lastInvoice) {
            // Hapus "-P" di akhir invoice_no jika ada
            $invoiceNumber = preg_replace('/-P$/', '', $lastInvoice->invoice_no);
        
            // Ambil angka terakhir dari invoice_number
            if (preg_match('/(\d+)$/', $invoiceNumber, $matches)) {
                $lastSequence = (int) $matches[0];
            } else {
                $lastSequence = 0; // Jika tidak ditemukan angka, mulai dari 0
            }
        
            // Cek apakah tahun pada invoice berbeda dengan tahun sekarang
            $invoiceYear = substr($lastInvoice->invoice_no, 8, 2); // Ambil dua digit pertama (misal '24')
        
            if ($invoiceYear != $year) {
                $lastSequence = 0; // Reset jika tahun berbeda
            }
        } else {
            $lastSequence = 0; // Jika tidak ada invoice sebelumnya, mulai dari 0
        }
        
        // Tambahkan 1 dan format menjadi 6 digit
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

        $form = Form::find($header->form_id);
       

        $formCont = FormC::where('form_id', $form->id)->get();
        // var_dump($formCont);

        foreach ($formCont as $fc) {
            $cont = ContF::find($fc->container_id);

            $cont->update([
                'active_to' => $form->etd,
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'Invoice Has Been Paid',
        ]);
    }

    public function cancelInvoice(Request $request)
    {
        try {
            $header = Header::find($request->id);

            $header->update([
                'status' => 'C'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Invoice Has Been Canceled',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Opsss Somtehing wrong: '. $th->getMessage(),
            ]);
        }
    }

    public function deleteInvoice(Request $request)
    {
        try {
            $header = Header::find($request->id);

            $form = Form::find($header->form_id);

            FormC::where('form_id', $form->id)->delete();
            Detil::where('invoice_id', $header->id)->delete();
            $header->delete();
            $form->delete();

            return response()->json([
                'success' => true,
                'message' => 'Invoice Has Been Deleted',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Opsss Somtehing wrong: '. $th->getMessage(),
            ]);
        }
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

    public function editInvoice($id)
    {
        $header = Header::find($id);
        $data['title'] = 'Edit Invoice -'.$header->invoice_no;
        $data['header'] = $header;

        $data['formC'] = FormC::where('form_id', $header->form_id)->get();

        return view('invoiceFCL.invoice.editInvoice', $data);
    }

    public function updateInvoice(Request $request)
    {
        $header = Header::find($request->id);

        try {
            $header->update([
                'created_at' => $request->created_at,
                'lunas_at' => $request->lunas_at,
            ]);

            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data berhasil di update']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Opss Somethingwrong: '. $th->getMessage()]);
            //throw $th;
        }
    }
}
