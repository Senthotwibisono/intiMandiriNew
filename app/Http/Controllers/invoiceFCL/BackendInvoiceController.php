<?php

namespace App\Http\Controllers\invoiceFCL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\Storage;
use App\Exports\fcl\invoice\ReportInvoiceFCL;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Customer;
use App\Models\ContainerFCL as ContF;
use App\Models\JobOrderFCL as JobF;
use App\Models\FCL\FormContainerFCL as FormC;
use App\Models\FCL\FormFCL as Form;
use App\Models\FCL\MTarifTPS as TTPS;
use App\Models\FCL\MTarifWMS as TWMS;
use App\Models\FCL\InvoiceHeader as Header;
use App\Models\FCL\InvoiceDetil as Detil;
use App\Models\FCL\CanceledInvoice as InvCancel;

class BackendInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->password = '4646';
        
    }

    public function dataTable(Request $request)
    {
        $header = Header::orderBy('created_at', 'desc')->orderBy('proforma_no', 'desc')->orderBy('invoice_no', 'desc')->get();

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
        ->addColumn('tranparansi', function($header){
            if ($header->status == 'C') {
                return '<span class="badge bg-danger text-white">Canceled</span>';
            }else {
                # code...
                return '<a type="button" href="/invoiceFCL/invoice/tranparansi-'.$header->id.'" target="_blank" class="btn btn-sm btn-danger text-white"><i class="fa fa-file"></i></a>';
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
                if ($header->kd_tps_asal == 'PLDC') {
                    return '<a href="/invoiceFCL/invoice/edit/'.$header->id.'" class="btn btn-info editInvoice"><i class="fa fa-pencil"></i></a>';
                } else {
                    return '<span class="badge bg-warning text-white">Belum Lunas</span>';
                }
            }
        })
        ->addColumn('flagSP2', function($header){
            $formC = FormC::where('form_id', $header->form_id)->get();
            $conts = ContF::whereIn('id', $formC->pluck('container_id'))->get();
            $countSP2 = $conts->where('flag_sp2', 'N')->count();
            if ($countSP2 > 0) {
                $flagSP2 = 'N';
            } else {
                $flagSP2 = 'Y';
            }
            return $flagSP2;
        })
        ->rawColumns(['invoiceNo', 'pranota', 'invoice', 'action', 'deleteOrCancel', 'edit', 'tranparansi'])
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

        $data['detilTPS'] = Detil::where('invoice_id', $id)->where('total', '>', 0)->whereNot('tps', '=', 'Depo')->orderByRaw("CASE 
        WHEN keterangan LIKE 'Penumpukkan Massa 1%' THEN 1
        WHEN keterangan LIKE 'Penumpukkan Massa 2%' THEN 2
        WHEN keterangan LIKE 'Penumpukkan Massa 3%' THEN 3
        ELSE 4 
        END")->orderBy('keterangan', 'desc')->get();
        $data['detilWMS'] = Detil::where('invoice_id', $id)->where('tps', '=', 'Depo')->where('total', '>', 0)->orderByRaw("CASE 
        WHEN keterangan LIKE 'Penumpukan %' THEN 1
        WHEN keterangan LIKE 'Paket PLP %' THEN 2
        WHEN keterangan LIKE 'Lift On %' THEN 3
        WHEN keterangan LIKE 'Lift Off %' THEN 4
        ELSE 5
        END")->orderBy('keterangan', 'desc')->get();

        $data['terbilang'] = $this->terbilang(ceil($data['header']->grand_total));
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

        $data['terbilang'] = $this->terbilang(ceil($data['header']->grand_total));
        // dd($data['terbilang']);
        if ($data['header']->type == 'EXTEND') {
            return view('invoiceFCL.invoice.invoiceExtend', $data);
        }else {
            return view('invoiceFCL.invoice.invoice', $data);
        }
        
    }

    public function paidInvoice(Request $request)
    {
        // dd($request->all());
        $cancel = invCancel::orderBy('invoice_no', 'asc')->first();
        if ($cancel) {
            $noInvoice = $cancel->invoice_no;
        }else {
            # code...
            $year = Carbon::now()->format('y'); // Misalnya '24' untuk tahun 2024
    
            // Cari invoice terakhir di tahun yang sama
            $lastInvoice = Header::whereYear('created_at', Carbon::now()->year)
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
            $noInvoice = 'ITM-' . 'FCL' . '/' . $year . '/' . $newSequence;
        }

        try {
            //code...
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
                    $path = storage_path('app/public/ktpFCL/' . $fileName);
    
                    // Store the image in the storage directory
                    file_put_contents($path, $imageData);
            } else {
                $fileName = null; // Handle the case where no file was uploaded
            }
    
            $header = Header::find($request->id);
            $header->update([
                'invoice_no' => $noInvoice,
                'lunas_at' => Carbon::now(),
                'uidLunas' => Auth::user()->id,
                'status' => 'Y',
                'no_hp' => $request->no_hp,
                'ktp' => $fileName,
                'jumlah_bayar' => $request->jumlah_bayar,
            ]);
            if ($cancel) {
                $cancel->delete();
            }
    
            $form = Form::find($header->form_id);
           
    
            $formCont = FormC::where('form_id', $form->id)->get();
            // var_dump($formCont);
    
            foreach ($formCont as $fc) {
                $cont = ContF::find($fc->container_id);
    
                $cont->update([
                    'active_to' => $form->etd,
                ]);
            }
    
    
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Invoice Has Been Paid']);
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Something Wrong: ' . $th->getMessage()]);
        }
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Invoice Has Been Paid',
        // ]);
    }

    public function cancelInvoice(Request $request)
    {
        try {
            $header = Header::find($request->id);

            $header->update([
                'status' => 'C'
            ]);

            if ($header->flag_hidden == 'N') {
                # code...
                $noInvoice = invCancel::create([
                    'invoice_no' => $header->invoice_no,
                ]);
            }

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

    public function getDataInvoice($id)
    {
        try {
            $header = Header::find($id);

            if ($header) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Ditemukan',
                    'data' => $header,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something Wrong: ' . $th->getMessage(),
            ]);
        }
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
                'invoice_no' => ($header->flag_hidden == 'Y') ? ($request->invoice_no ? $request->invoice_no : $header->Invoice_no) : $header->invoice_no,
                'created_at' => $request->created_at,
                'lunas_at' => $request->lunas_at,
                'cust_name' => $request->cust_name,
                'cust_npwp' => $request->cust_npwp,
                'cust_fax' => $request->cust_fax,
                'cust_alamat' => $request->cust_alamat,
                'jumlah_bayar' => $request->jumlah_bayar,
                'no_hp' => $request->no_hp,
                'total_tps' => $request->total_tps,
                'total_wms' => $request->total_wms,
                'total' => $request->total,
                'admin' => $request->admin,
                'ppn' => $request->ppn,
                'grand_total' => $request->grand_total,
            ]);

            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data berhasil di update']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Opss Somethingwrong: '. $th->getMessage()]);
            //throw $th;
        }
    }

    public function hiddenInvoice(Request $request)
    {
        // var_dump($request->all());
        // die();
        $password = $request->password;
        if ($password != $this->password) {
            return response()->json([
                'success' => false,
                'message' => 'Password Salah',
            ]);
        }

        try {
            $header = Header::find($request->id);

            if ($header) {
                $noInvoice = invCancel::create([
                    'invoice_no' => $header->invoice_no,
                ]);
                $header->update([
                    'invoice_no' => $header->invoice_no . '-R',
                    'flag_hidden' => 'Y',
                    'hidden_by' => Auth::user()->id,
                    'hidden_at' => Carbon::now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice Berhasil di Sembunyikan',
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => 'Opss Something Wrong : ' . $th->getMessage(),
            ]);
        }
    }

    public function hapusPhotoKTP($id, Request $request)
    {
        $header = Header::findOrFail($id);

        if ($header->ktp) {
            Storage::delete('public/ktpFCL/' . $header->ktp);
            $header->ktp = null;
            $header->save();
    
            return response()->json(['success' => 'Foto KTP berhasil dihapus']);
        }
    
        return response()->json(['error' => 'Foto KTP tidak ditemukan'], 404);
    }

    public function uploadKtp(Request $request)
    {
        $header = Header::find($request->id); // Ganti sesuai kebutuhan

        if ($request->has('image')) {
            $base64Image = $request->input('image');

            // Cek format base64 dengan regex
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
                $imageType = strtolower($matches[1]); // Ambil format file (png, jpg, jpeg)

                // Hanya izinkan PNG, JPG, JPEG
                if (!in_array($imageType, ['png', 'jpg', 'jpeg'])) {
                    return response()->json(['error' => 'Format gambar tidak didukung'], 400);
                }

                // Hapus prefix base64
                $image = substr($base64Image, strpos($base64Image, ',') + 1);
                $image = str_replace(' ', '+', $image); // Hindari karakter tidak valid

                // Decode base64 ke binary
                $imageData = base64_decode($image);

                // Pastikan hasil decode valid
                if ($imageData === false) {
                    return response()->json(['error' => 'Gagal mendekode gambar'], 400);
                }

                // Buat nama file unik
                $fileName = 'ktp_' . time() . '_' . uniqid() . '.' . $imageType;

                // Path penyimpanan di storage Laravel
                $path = storage_path('app/public/ktpFCL/' . $fileName);

                // Simpan file ke storage
                file_put_contents($path, $imageData);

                // Update database dengan nama file baru
                $header->ktp = $fileName;
                $header->save();

                return response()->json(['success' => 'Foto KTP berhasil diunggah', 'file' => $fileName]);
            } else {
                return response()->json(['error' => 'Format base64 tidak valid'], 400);
            }
        }

        return response()->json(['error' => 'Tidak ada gambar yang dikirim'], 400);
    }

    public function indexReport()
    {
        $data['title'] = 'Report Invoice FCL';

        return view('invoiceFCL.report.index', $data);
    }

    public function excelReport(Request $request)
    {
        try {
            // Pastikan nilai $request->tanggal ada dan valid
            $column = in_array($request->tanggal, ['created_at', 'lunas_at']) ? $request->tanggal : 'created_at';
    
            // Query berdasarkan filter, type, dan tanggal yang dipilih
            $headers = Header::where('flag_hidden', 'N' )->whereIn('status', $request->filter)
                ->whereIn('type', $request->type)
                ->whereBetween($column, [$request->start_date, $request->end_date])
                ->orderBy('invoice_no', 'asc')
                ->get();
    
            // Menampilkan hasil query (untuk testing)
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $tanggalJudul =  $this->formatDateRange($start_date, $end_date);
            if (!$tanggalJudul) {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Something Wrong : Sepertinya anda belum memasukkan tanggal']);
            }
    

            // dd($tanggalJudul);
    
            $judul = 'Laporan Invoice '. $tanggalJudul;
            // dd($judul);
    
            $fileName = 'Report-Invoice-FCL-'.$start_date.'-'.$end_date.'.xlsx' ;
            return Excel::download(new ReportInvoiceFCL($headers, $judul), $fileName);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Gagal di Simpan '. $th->getMessage()]);
        }
    }
    
    public function pdfReport(Request $request)
    {
        try {
            // Pastikan nilai $request->tanggal ada dan valid
            $column = in_array($request->tanggal, ['created_at', 'lunas_at']) ? $request->tanggal : 'created_at';
    
            // Query berdasarkan filter, type, dan tanggal yang dipilih
            $headers = Header::where('flag_hidden', 'N' )->whereIn('status', $request->filter)
                ->whereIn('type', $request->type)
                ->whereBetween($column, [$request->start_date, $request->end_date])
                ->orderBy('invoice_no', 'asc')
                ->get();
    
            // Menampilkan hasil query (untuk testing)
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $tanggalJudul =  $this->formatDateRange($start_date, $end_date);
            if (!$tanggalJudul) {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Something Wrong : Sepertinya anda belum memasukkan tanggal']);
            }
    

            // dd($tanggalJudul);

            $idHeader = $headers->pluck('id')->unique()->toArray();
            // dd($idHeader);
    
            $judul = 'Laporan Invoice '. $tanggalJudul;
            $detils = Detil::whereIn('invoice_id', $idHeader)->get();
            // dd($judul);
            return view('invoiceFCL.report.pdf', compact('headers', 'judul', 'detils'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Gagal di Simpan '. $th->getMessage()]);
        }
    }

    private function formatDateRange($start_date, $end_date)
    {
        if (!$start_date && !$end_date) {
            return null; // Jika keduanya kosong, abaikan
        }

        // Jika salah satu kosong, gunakan yang tersedia
        if (!$start_date) {
            return Carbon::parse($end_date)->translatedFormat('j F Y');
        }
        if (!$end_date) {
            return Carbon::parse($start_date)->translatedFormat('j F Y');
        }

        $start = Carbon::parse($start_date);
        $end = Carbon::parse($end_date);

        if ($start->year === $end->year) {
            if ($start->month === $end->month) {
                return $start->format('j') . ' - ' . $end->translatedFormat('j F Y');
            }
            return $start->translatedFormat('j F') . ' - ' . $end->translatedFormat('j F Y');
        }

        return $start->translatedFormat('j F Y') . ' - ' . $end->translatedFormat('j F Y');
    }

    public function Tranparansi($id)
    {

        $data['title'] = 'Invoice FCL';
        $data['header'] = Header::find($id);

        if ($data['header']->status != 'Y') {
            return redirect()->back()->with('status', ['type'=> 'error', 'message' => 'Invoice belum di lunasi, anda di larang membuka halaman ini']);
        }
        
        $container = FormC::where('form_id', $data['header']->form_id)->get();
        $data['containers'] = $container;
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

        if ($data['header']->type == 'EXTEND') {
            return view('invoiceFCL.invoice.tranparansiExtend', $data);
        }else {
            # code...
            return view('invoiceFCL.invoice.tranparansi', $data);
        }
    }
    
}
