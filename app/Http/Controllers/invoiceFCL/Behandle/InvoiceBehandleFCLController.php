<?php

namespace App\Http\Controllers\invoiceFCL\Behandle;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;
use DataTables;

use App\Models\FCL\Behandle\Form;
use App\Models\FCL\Behandle\FormContainer as FormC;
use App\Models\FCL\Behandle\Header;
use App\Models\FCL\Behandle\Detil;
use App\Models\FCL\Behandle\Cancel;
use App\Models\Customer;
use App\Models\ContainerFCL as Cont;
use App\Models\FCL\MTarifWMS as TWMS;

class InvoiceBehandleFCLController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->password = 'kaloKenaAuditSayaGakIkutan!!';
        
    }

    public function formIndex()
    {
        $data['title'] = 'From Behandle';
        
        return view('invoiceFCL.behandle.form.index', $data);
    }

    public function formData()
    {
        $form = Form::with(['user'])->where('status', 'N')->get();

        return DataTables::of($form)
        ->addColumn('action', function($form){
            return '<a href="/invoiceFCL/behandle/form-step1/'.$form->id.'" class="btn btn-warning"><i class="fas fa-pencil"></i></a>';
        })
        ->addColumn('delete', function($form){
            return '<button class="btn btn-danger" type="button" data-id="'.$form->id.'" id="cancelButton"><i class="fas fa-trash"></i></button>';
        })
        ->rawColumns(['action', 'delete'])
        ->make(true);
    }

    public function formCreate(Request $request)
    {
        try {
            $form = Form::create([
                'created_at' => Carbon::now(),
                'uid' => Auth::user()->id,
                'status' => 'N',
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Berhasil',
                'id' => $form->id
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Opss Something Wrong : ' . $th->getMessage(),
            ]);
        }
    }

    public function indexStep1($id)
    {
        $data['title'] = 'Index Step-1';
        $data['customers'] = Customer::get(); 
        $data['form'] = Form::find($id);

        $data['selectedContainers'] = FormC::where('form_id', $id)->get();

        return view('invoiceFCL.behandle.form.step1', $data);
    }

    public function getContainer(Request $request)
    {
        // dd($request->all());
        $rules = [
            'no_spjm'  => 'required',
            'tgl_spjm'  => 'required',
        ];

        $messages = [
            'no_spjm'  => 'No SPJM Tidak Boleh Null',
            'tgl_spjm'  => 'Tgl SPJM Tidak Boleh Null',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(fn($error) => $error[0])->toArray();
            return response()->json([
                'status'  => false,
                'success' => false,
                'message' => implode(", ", $errors),
                'errors'  => $validator->errors(),
            ], 400);
        }
        
        $cont = Cont::where('no_spjm', $request->no_spjm)->where('tgl_spjm', $request->tgl_spjm)->get();

        if ($cont->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'Tidak ada container yang bisa di pilih'
            ]);
        }

        $singleCont = $cont->first();
        $customer = Customer::find($singleCont->cust_id);

        return response()->json([
            'success' => true,
            'containers' => $cont,
            'customer_id' => $customer->id,
        ]);

    }

    public function delete(Request $request)
    {
        $form = Form::find($request->id);
        try {
            $container = FormC::where('form_id', $form->id)->delete();
            $form->delete();

            $header = Header::where('form_id', $request->id)->get();
            if ($header->isNotEmpty()) {
                $detil = Detil::whereIn('invoice_id', $header->pluck('id'))->delete();
                Header::where('form_id', $request->id)->delete();
            }
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function postStep1(Request $request)
    {
        // dd($request->all());
        $rules = [
            'no_spjm'  => 'required',
            'tgl_spjm'  => 'required',
            'container_id' => 'required',
            'customer_id' => 'required',
        ];

        $messages = [
            'no_spjm'  => 'No SPJM Tidak Boleh Null',
            'tgl_spjm'  => 'Tgl SPJM Tidak Boleh Null',
            'container_id' => 'Setidaknya mengandung satu Container',
            'customer' => 'Customer tidak boleh null',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(fn($error) => $error[0])->toArray();
           return redirect()->back()->with('status', ['type'=>'error', 'message'=>implode(', ', $errors)]);
        }

        $conts = Cont::whereIn('id', $request->container_id)->get();
        // dd($conts);
        $form = Form::find($request->id);  
        $oldContainer = FormC::where('form_id', $form->id)->get();  
        if ($oldContainer->isNotEmpty()) {
            $oldContainer->each->delete();
        }

        try {
            $form->update([
                'customer_id' => $request->customer_id,
                'no_spjm' => $request->no_spjm,
                'tgl_spjm' => $request->tgl_spjm,
            ]);

            foreach ($conts as $cont) {
                $formContainer = FormC::create([
                    'form_id' => $form->id,
                    'container_id' => $cont->id,
                    'nocontainer' => $cont->nocontainer,
                    'size' => $cont->size,
                    'ctr_type' => $cont->ctr_type,
                    'no_bl_awb' => $cont->nobl,
                    'tgl_bl_awb' => $cont->tgl_bl_awb,
                ]);
            }

            return redirect(route('invoiceFCL.behandel.preinvoice', ['id' => $form->id]))->with('status', ['type' => 'success', 'message' => 'Data Berhasil di Simpan']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Somethingwrong in: ' . $th->getMessage()]);
            //throw $th;
        }
    }

    public function preinvoice($id)
    {
        $data['title'] = 'Preinvoice FCL-Behandle';

        $data['form'] = Form::find($id);
        $data['containers'] = FormC::where('form_id', $id)->get(); 
        
        $tarifWMS = TWMS::select('size', 'type')->get()->toArray();
        $invalidContainers = $data['containers']->filter(function ($container) use ($tarifWMS) {
            return !in_array(['size' => $container->size, 'type' => $container->ctr_type], $tarifWMS);
        });
        
        if ($invalidContainers->isNotEmpty()) {
            $invalidContainerNumbers = $invalidContainers->pluck('nocontainer')->implode(', ');
            return redirect(route('invoiceFCL.behandle.step1', ['id' => $data['form']->id]))->with('status', ['type'=> 'error', 'message' => 'Tidak ada tarif WMS yang cocok untuk container :' . $invalidContainerNumbers]);
        }
        
        $data['tarifs'] = TWMS::get(); 
        $data['singleCont'] = $data['containers']->first();
        $data['jenisContainer'] = $data['containers']->pluck('size')->unique()->implode(', ');
        $data['typeContainer'] = $data['containers']->pluck('ctr_type')->unique()->implode(', ');
        $data['nocontainer'] = $data['containers']->pluck('nocontainer')->implode(', ');

        $data['size'] = $data['containers']->pluck('size')->unique();
        $data['type'] = $data['containers']->pluck('ctr_type')->unique();

        return view('invoiceFCL.behandle.form.preinvoice', $data)->with('status', ['type'=>'success', 'message'=>'Silahkan menuju step selanjutnya']);
    }

    public function createInvoice(Request $request)
    {
        // dd($request->all());
        $form = Form::find($request->form_id);

        $containers = FormC::where('form_id', $form->id)->get();

        $grandTotal = $request->grandTotal;
        if ($grandTotal >= 5000000) {
            $grandTotal += 10000;
        }

        try {
            $header = Header::create([
                'form_id' => $form->id,
                'proforma_no' => $this->getNextOrderNo(),
                'no_spjm' => $form->no_spjm,
                'tgl_spjm' => $form->tgl_spjm,
                'customer_id' => $form->customer_id,
                'customer_name' => $form->cust->name,
                'customer_alamat' => $form->cust->alamat,
                'customer_npwp' => $form->cust->npwp,
                'status' => 'N',
                'admin' => $request->admin,
                'total' => $request->total,
                'ppn' => $request->ppn,
                'grand_total' => $grandTotal,
                'order_by' => Auth::user()->id,
                'order_at' => Carbon::now(),
            ]);
    
            $groupCont = $containers->groupBy(['size', 'ctr_type']);
    
            foreach ($groupCont as $size => $types) {
                foreach ($types as $ctr_type => $contGroup) {
                    $jumlahCont = $contGroup->count();
                    $tarif = TWMS::where('size', $size)->where('type', $ctr_type)->first();
                    // dd($tarif, $jumlahCont);
                    $total = $jumlahCont * $tarif->behandle;
                    $detil = Detil::create([
                        'invoice_id' => $header->id,
                        'size' => $size,
                        'type' => $ctr_type,
                        'tarif' => $tarif->behandle,
                        'jumlah' => $jumlahCont,
                        'total' => $total,
                    ]); 
                }
            }
            $form->update(['status' => 'Y']);
            return redirect(route('invoiceFCL.behandle.invoiceIndex'))->with('status', ['type'=>'success', 'message'=>'Invoice berhasil dibuat ']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Something wrong : ' . $th->getMessage()]);
        }
    }

    private function getNextOrderNo()
    {
        $latestOrder = Header::orderBy('proforma_no', 'desc')->first();

        if ($latestOrder) {
            // Ambil angka terakhir setelah "FCL-"
            $latestNumber = intval(str_replace('BHD-', '', $latestOrder->proforma_no));
            $nextOrderNo = $latestNumber + 1;
        } else {
            $nextOrderNo = 1;
        }

        // Format dengan leading zeros (15 digit)
        return 'BHD-' . str_pad($nextOrderNo, 15, '0', STR_PAD_LEFT);
    }

    // Invoice

    public function invoiceIndex()
    {
        $data['title'] = 'Invoice FCL-Behandle';

        return view('invoiceFCL.behandle.invoice.index', $data);
    }

    public function invoiceData(Request $request)
    {
        $header = Header::orderBy('order_at', 'desc')->orderBy('proforma_no', 'desc')->orderBy('invoice_no', 'desc')->get();

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
            return $header->order->name ?? '-';
        })
        ->addColumn('pranota', function($header){
            if ($header->status == 'C') {
                return '<span class="badge bg-danger text-white">Canceled</span>';
            }else {
                # code...
                return '<a type="button" href="/invoiceFCL/behandle/invoice-pranota/'.$header->id.'" target="_blank" class="btn btn-sm btn-warning text-white"><i class="fa fa-file"></i></a>';
            }
        })
        ->addColumn('invoice', function($header){
            if ($header->status == 'C') {
                return '<span class="badge bg-danger text-white">Canceled</span>';
            }else {
                if ($header->status == 'Y') {
                    # code...
                    return '<a type="button" href="/invoiceFCL/behandle/invoice-invoice/'.$header->id.'" target="_blank" class="btn btn-sm btn-info text-white"><i class="fa fa-file"></i></a>';
                } else {
                    return '<span class="badge bg-danger text-white">Belum Lunas</span>';
                }
            }
        })
        ->addColumn('action', function($header){
            if ($header->status == 'Y') {
                return '<span class="badge bg-info text-white">Lunas</span>';
            }elseif ($header->status == 'C') {
                return '<span class="badge bg-danger text-white">Canceled</span>';
            } else {
                return '<button class="btn btn-success" id="payButton" data-id="'.$header->id.'">Pay</button>';
            }
        })
        ->addColumn('deleteOrCancel', function($header){
            if ($header->status == 'Y') {
                return '<button type="button" data-id="'.$header->id.'" class="btn btn-danger cancelButton">Cancel</button>';
            }elseif ($header->status == 'C') {
                return '<span class="badge bg-danger text-white">Canceled</span>';
            }else {
                return '<button type="button" data-id="'.$header->form_id.'" class="btn btn-danger" id="cancelButton"><i class="fa fa-trash"></i></button>';
            }
        })
        ->addColumn('edit', function($header){
            if ($header->status == 'Y') {
                $url = route('invoiceFCL.behandle.editInvoice', ['id' => $header->id]);
                return '<a href="'.$url.'" class="btn btn-info editInvoice"><i class="fa fa-pencil"></i></a>';
            }elseif ($header->status == 'C') {
                return '<span class="badge bg-danger text-white">Canceled</span>';
            } else {
                return '<span class="badge bg-warning text-white">Belum Lunas</span>';
            }
        })
        ->rawColumns(['invoiceNo', 'pranota', 'invoice', 'action', 'deleteOrCancel', 'edit'])
        ->make(true);
    }

    public function invoicePranota($id)
    {
        $header = Header::find($id);
        $detil = Detil::where('invoice_id', $header->id)->get();
        $data['title'] = 'Pranota Invoice Behandle-'.$header->proforma_no ?? '-';

        $data['header'] = $header;
        $data['detils'] = $detil;

        $containers = FormC::where('form_id', $header->form_id)->get();
        $data['singeCont'] = $containers->first();
       $data['jenisContainer'] = $containers->pluck('size')->unique()->implode(', ');
       $data['typeContainer'] = $containers->pluck('ctr_type')->unique()->implode(', ');
       $data['nocontainer'] = $containers->pluck('nocontainer')->unique()->implode(', ');
       $data['terbilang'] = $this->terbilang($header->grand_total);
        return view('invoiceFCL.behandle.invoice.pranota', $data);
    }

    public function invoiceInvoice($id)
    {
        $header = Header::find($id);
        $detil = Detil::where('invoice_id', $header->id)->get();
        $data['title'] = 'Pranota Invoice Behandle-'.$header->proforma_no ?? '-';

        $data['header'] = $header;
        $data['detils'] = $detil;

        $containers = FormC::where('form_id', $header->form_id)->get();
        $data['singeCont'] = $containers->first();
       $data['jenisContainer'] = $containers->pluck('size')->unique()->implode(', ');
       $data['typeContainer'] = $containers->pluck('ctr_type')->unique()->implode(', ');
       $data['nocontainer'] = $containers->pluck('nocontainer')->unique()->implode(', ');
       $data['terbilang'] = $this->terbilang($header->grand_total);
        return view('invoiceFCL.behandle.invoice.invoice', $data);
    }

    public function invoicePay(Request $request)
    {
        $header = Header::find($request->id);
        $cancel = Cancel::orderBy('invoice_no', 'asc')->first();
        if ($cancel) {
            $noInvoice = $cancel->invoice_no;
        }else {
            # code...
            $year = Carbon::now()->format('y'); // Misalnya '24' untuk tahun 2024
    
            // Cari invoice terakhir di tahun yang sama
            $lastInvoice = Header::whereYear('order_at', Carbon::now()->year)
                                 ->whereNotNull('invoice_no')
                                 ->orderBy('invoice_no', 'desc')
                                 ->whereNot('flag_hidden', 'Y')
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
            $noInvoice = 'ITM-' . 'BHD' . '/' . $year . '/' . $newSequence;
        }
        try {
            $header->update([
                'invoice_no' => $noInvoice,
                'lunas_at' => Carbon::now(),
                'lunas_by' => Auth::user()->id,
                'status' => 'Y',
            ]);

            if ($cancel) {
                $cancel->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran Berhasil !',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function invoiceCancel(Request $request)
    {
        $header = Header::find($request->id);
        try {
            if ($header->hidden_flag == 'N' || $header->hidden_flag == null || $header->hidden_flag == '') {
                $cancel = Cancel::create([
                    'invoice_no' => $header->invoice_no,
                ]);

            }
            $header->update([
                'status' => 'C',
                'cancel_at' => Carbon::now(),
                'cancel_by' => Auth::user()->id,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Berhasil Cancel Invoice',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function editInvoice($id)
    {
        $header = Header::find($id);
        $data['header'] = $header;

        $data['title'] = 'Edit Invoice - ' . ($header->invoice_no) ? $header->invoice_no : $header->proforma_no; 
        return view('invoiceFCL.behandle.invoice.edit', $data);
    }

    public function hiddenInvoice(Request $request)
    {
        try {
            $header = Header::find($request->id);
            if (!$header) {
                return response()->json([
                    'status' => 404,
                    'success' => false,
                    'message' => 'Error, Data tidak ditemukan',
                ]);
            }
            $oldCancel = Cancel::where('invoice_no', $header->invoice_no)->first();
            if (!$oldCancel) {
                $noInvoice = Cancel::create([
                    'invoice_no' => $header->invoice_no,
                ]);
            }
            $header->update([
                'invoice_no' => $header->invoice_no . '-R',
                'flag_hidden' => 'Y',
                'hidden_by' => Auth::user()->id,
                'hidden_at' => Carbon::now(),
            ]);

            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Data berhasil di Simpan',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }

    public function updateInvoice(Request $request)
    {
        // dd($request->all());
        $header = Header::find($request->id);
        if ($header) {
            try {
                $header->update([
                    'customer_name' => $request->customer_name,
                    'customer_npwp' => $request->customer_npwp,
                    'customer_fax' => $request->customer_fax,
                    'customer_alamat' => $request->customer_alamat,
                    'total' => $request->total,
                    'admin' => $request->admin,
                    'ppn' => $request->ppn,
                    'grand_total' => $request->grand_total,
                    'order_at' => $request->order_at,
                    'lunas_at' => $request->lunas_at,
                ]);
                return redirect()->back()->with('status',['type'=>'success', 'message'=>'Berhasil di Simpan']);
            } catch (\Throwable $th) {
                return redirect()->back()->with('status',['type'=>'error', 'message'=>$th->getMessage()]);
            }
        }
    }

    // Repprt
    public function indexReport()
    {
        $data['title'] = 'Report Invoice Behandle';

        return view('invoiceFCL.behandle.report', $data);
    }

    public function dataReport(Request $request)
    {
        $data = Header::where('flag_hidden', 'N');
        if ($request->has('filter')) {
            $data = $data->whereIn('status', $request->filter);
        }

        if ($request->has('start_date') && $request->start_date != null) {
            $data->whereDate('order_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != null) {
            $data->whereDate('order_at', '<=', $request->end_date);
        }

        $header = $data->get();
        return DataTables::of($header)
        ->addColumn('status', function($header){
           if ($header->status == 'C') {
                return '<span class="badge bg-danger text-white">Canceled</span>';
            }elseif ($header->status == 'Y') {
               return '<span class="badge bg-success text-white">Lunas</span>';
            }else {
               return '<span class="badge bg-warning text-white">Baelum Bayar</span>';
           }
        })
        ->addColumn('order_by', function($header){
            return $header->order->name ?? '-';
        })
        ->addColumn('cancel_by', function($header){
            return $header->cancel->name ?? '-';
        })
        ->addColumn('lunas_by', function($header){
            return $header->lunas->name ?? '-';
        })
        ->addColumn('admin', function($header){
            return number_format($header->admin, '0');
        })
        ->addColumn('total', function($header){
            return number_format($header->total, '0');
        })
        ->addColumn('ppn', function($header){
            return number_format($header->ppn, '0');
        })
        ->addColumn('grand_total', function($header){
            return number_format($header->grand_total, '0');
        })
        ->rawColumns(['status'])
        ->make(true);
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
