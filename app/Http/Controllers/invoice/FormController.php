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

class FormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['title'] = 'List Form';
        $data['forms'] = Form::where('status', '=', 'N')->orderBy('created_at', 'desc')->get();

        return view('invoice.form.index', $data);
    }

    public function create(Request $request)
    {
        $form = Form::create([
            'created_at' => Carbon::now(),
            'uid' => Auth::user()->id,
            'status' => 'N',
        ]);

        return response()->json(['id' => $form->id]);
    }
    
    public function getManifestData($id)
    {
        // Find the manifest by ID
        $manifest = Manifest::find($id);

        // Return the manifest data as JSON
        $cbm = ceil($manifest->meas);
        return response()->json([
            'quantity' => $manifest->quantity,
            'weight'   => $manifest->weight,
            'meas'     => $manifest->meas,
            'tglmasuk'     => $manifest->tglmasuk,
            'cbm'      => $cbm,
        ]);
    }

    public function getCustomerData($id)
    {
        // Find the manifest by ID
        $cust = Customer::find($id);

        return response()->json([
            'npwp' => $cust->npwp,
            'phone'   => $cust->phone,
        ]);
    }

    public function formIndex($id)
    {
        $data['title'] = 'Create Form || Step 1';
        $data['form'] = Form::find($id);
        $data['manifest'] = Manifest::whereNull('tglrelease')->get();
        $data['customer'] = Customer::all();

        $data['masterTarif'] = MT::all();
        $data['selectedTarif'] = FormT::where('form_id', $id)->get();

        return view('invoice.form.formIndex', $data);
    }

    public function delete($id)
    {
        $form = Form::where('id', $id)->first();
        if ($form) {
        $form->delete();
        $tarif = FormT::where('form_id', $id)->delete();
        return response()->json(['message' => 'Data berhasil dihapus.']);
        }else {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }
       
    }

    public function step1Post(Request $request)
    {
        try {
            $tarifSelected = $request->tarif_id;

            // dd($tarifSelected);
            if (empty($tarifSelected)) {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Anda belum memilih tarif yang akan dikenakan']);
            }
            $form = Form::find($request->id);
            $interval = Carbon::parse($request->time_in)->diff(Carbon::parse($request->expired_date)->addDay(1)) ?? null;
            $jumlahHari = $interval->days;
            if ($jumlahHari<=5) {
                $period = 1;
                $hariPeriod = $jumlahHari;
            }elseif ($jumlahHari >= 6 && $jumlahHari <= 10) {
                $period = 2;
                $hariPeriod = $jumlahHari - 5;
            }elseif ($jumlahHari >= 11) {
                $period = 3;
                $hariPeriod = $jumlahHari - 10;
            }
            // dd($jumlahHari, $period, $hariPeriod);
            $form->update([
                'manifest_id'=>$request->manifest_id,
                'customer_id'=>$request->customer_id,
                'cbm'=>$request->cbm,
                'time_in' => $request->time_in,
                'expired_date' => $request->expired_date,
                'jumlah_hari'=>$jumlahHari,
                'period' => $period,
                'hari_period' => $hariPeriod,
            ]);

            $checkTarif = FormT::where('form_id', $form->id)->whereNotIn('tarif_id', $tarifSelected)->get();
            if (!empty($checkTarif)) {
                foreach ($checkTarif as $deleteOld) {
                    $deleteOld->delete();
                }
            }
            foreach ($tarifSelected as $tarif) {
                $oldTarif = FormT::where('form_id', $form->id)->where('tarif_id', $tarif)->first();
                if (empty($oldTarif)) {
                   $newTarif =  FormT::create([
                        'form_id' => $form->id,
                        'tarif_id' => $tarif,
                        'manifest_id' => $form->manifest_id,
                    ]);
                }
            }
            return redirect()->route('invoice.step2', ['id'=>$form->id])->with('status', ['type'=>'success', 'message'=>'Berhasil di Simpan']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Opps Something Wrong'.$th->getMessage()]);
        }
    }

    public function step2Index($id)
    {
        $data['title'] = 'Create Form || Step 2';
        $form = Form::find($id);
        $data['form'] = Form::find($id);
        $data['manifest'] = Manifest::whereNull('tglrelease')->get();
        $data['customer'] = Customer::all();

        $data['masterTarif'] = MT::all();
        $data['selectedTarif'] = FormT::where('form_id', $id)->get();

        switch ($form->period) {
            case 1:
                $data['periode1'] = $form->hari_period;
                $data['periode2'] = 0;
                $data['periode3'] = 0;
                break;
            case 2:
                $data['periode1'] = 5;
                $data['periode2'] = $form->hari_period;
                $data['periode3'] = 0;
                break;
            case 3:
                $data['periode1'] = 5;
                $data['periode2'] = 5;
                $data['periode3'] = $form->hari_period;
                break;
            
            default:
                $data['periode1'] = null;
                $data['periode2'] = null;
                $data['periode3'] = null;
                break;
        }

        return view('invoice.form.step2', $data);
    }

    public function step2Post(Request $request)
    {
        try {
            $tarifIds = $request->input('tarif_id');
            $hargaSatuan = $request->input('harga_satuan');
            $jumlahVolume = $request->input('jumlah_volume');
            $jumlahHari = $request->input('jumlah_hari');
            $total = $request->input('total');

            foreach ($tarifIds as $index => $tarifId) {
                // Simpan data per tarif berdasarkan index
                // $data = [
                //     'tarif_id' => $tarifId,
                    
                // ];
                $formTarif = FormT::where('form_id', $request->id)->where('tarif_id', $tarifId)->first();
                $formTarif->update([
                    'harga' => $hargaSatuan[$index],
                    'jumlah' => $jumlahVolume[$index],
                    'jumlah_hari' => $jumlahHari[$index] ?? 0,
                    'total' => $total[$index],
                ]);
            }

            $tarif = FormT::where('form_id', $request->id)->get();
            $total = $tarif->sum('total') + $request->admin;
            $tarifAfterDiscount = $total - $request->discount;

            $pajakAmount = $tarifAfterDiscount * ($request->pajak/100);
            $grandTotal = $tarifAfterDiscount + $pajakAmount;
            // dd($tarif, $total, $tarifAfterDiscount, $pajakAmount, $grandTotal);

            $form = Form::find($request->id);
            $form->update([
                'total' => $total,
                'admin'=> $request->admin,
                'pajak'=> $request->pajak,
                'pajak_amount' => $pajakAmount,
                'discount' => $request->discount,
                'grand_total' => $grandTotal,
            ]);
            return redirect()->route('invoice.preinvoice', ['id'=>$form->id])->with('status', ['type'=>'success', 'message'=>'Berhasil di Simpan']);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function preinvoice($id)
    {
        $data['title'] = 'Create Form || Step 3 - Preinvoice';
        $form = Form::find($id);
        $data['form'] = Form::find($id);
        $data['manifest'] = Manifest::whereNull('tglrelease')->get();
        $data['customer'] = Customer::all();

        $data['masterTarif'] = MT::all();
        $data['tarifs'] = FormT::where('form_id', $id)->get();
        $data['terbilang'] = $this->terbilang($data['form']->grand_total);

        return view('invoice.form.step3', $data);
    }

    public function step3Post(Request $request)
    {
        try {
            $form = Form::find($request->id);

            $latestOrder = Header::orderBy('id', 'desc')->first();
        
            // Calculate the next order number
            $nextOrderNo = $latestOrder ? intval($latestOrder->order_no) + 1 : 1;
            $formattedOrderNo = str_pad($nextOrderNo, 6, '0', STR_PAD_LEFT); // Ensure it's a 6-digit number

            $header = Header::create([
                'form_id' => $form->id,
                'manifest_id' => $form->manifest_id,
                'customer_id' => $form->customer_id,
                'judul_invoice' => $request->judul_invoice,
                'invoice_no' => $form->invoice_no,
                'order_no' => $formattedOrderNo,
                'time_in' => $form->time_in,
                'expired_date' => $form->expired_date,
                'total' => $form->total,
                'admin' => $form->admin,
                'pajak' => $form->pajak,
                'pajak_amount' => $form->pajak_amount,
                'grand_total' => $form->grand_total,
                'status' => 'N',
                'order_at' => Carbon::now(),
                'kasir_id' => Auth::user()->id,
            ]);

            $form->update([
                'status' => 'Y'
            ]);
            return redirect()->route('invoice.unpaid')->with('status', ['type'=>'success', 'message'=>'Berhasil di Simpan']);
        } catch (\Throwable $th) {
            //throw $th;
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
}
