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
        $data['forms'] = Form::where('status', '=', 'N')->where(function ($query) {
            $query->where('type', '!=', 'P')
                  ->orWhereNull('type');
        })->orderBy('created_at', 'desc')->get();

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
        $forwarding = $manifest->cont->job->Forwarding->name ?? '';
        return response()->json([
            'quantity' => $manifest->quantity,
            'weight'   => $manifest->weight,
            'meas'     => $manifest->meas,
            'tglmasuk'     => $manifest->cont->job->eta,
            'cbm'      => $cbm,
            'forwarding'      => $forwarding,
            'cust' => $manifest->customer_id,
            'dg_label' => $manifest->dg_label,
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
        $data['manifest'] = Manifest::get();
        $data['customer'] = Customer::all();

        $data['masterTarif'] = MT::all();
        $data['masterTarifMekanik'] = MT::where(function ($query) {
            $query->where('day', '!=', 'Y')
                  ->orWhereNull('day');
        })->get();
        $data['selectedTarif'] = FormT::where('form_id', $id)->get();

        return view('invoice.form.formIndex', $data);
    }

    public function delete($id)
    {
        $form = Form::where('id', $id)->first();
        $invoice = Header::where('form_id', $id)->get();
        if ($invoice) {
            foreach ($invoice as $inv) {
                $inv->delete();
            }
        }
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
            $masterTarifSelectedPeriod = MT::whereIn('id', $tarifSelected)->where('day', 'Y')->orderBy('period', 'desc')->first();
            // dd($masterTarifSelectedPeriod);
            if (empty($tarifSelected)) {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Anda belum memilih tarif yang akan dikenakan']);
            }
            $form = Form::find($request->id);
            $interval = Carbon::parse($request->time_in)->diff(Carbon::parse($request->expired_date)->addDay(1)) ?? null;
            $jumlahHari = $interval->days;
            if ($masterTarifSelectedPeriod->period == 3) {
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
            }
            if ($masterTarifSelectedPeriod->period == 2) {
                if ($jumlahHari<=5) {
                    $period = 1;
                    $hariPeriod = $jumlahHari;
                }else {
                    $period = 2;
                    $hariPeriod = $jumlahHari - 5;
                }
            }
            if ($masterTarifSelectedPeriod->period == 1) {
                    $period = 1;
                    $hariPeriod = $jumlahHari;
            }
            // dd($jumlahHari, $period, $hariPeriod);
            $form->update([
                'manifest_id'=>$request->manifest_id,
                'customer_id'=>$request->customer_id,
                'forwarding_id'=>$request->forwarding_id,
                'cbm'=>$request->cbm,
                'time_in' => $request->time_in,
                'expired_date' => $request->expired_date,
                'jumlah_hari'=>$jumlahHari,
                'period' => $period,
                'hari_period' => $hariPeriod,
            ]);

            
            $checkTarif = FormT::where('form_id', $form->id)->whereNotIn('tarif_id', $tarifSelected)->where('mekanik_y_n', '=', 'N')->get();
            // dd($tarifSelected, $checkTarif);
            if (!empty($checkTarif)) {
                foreach ($checkTarif as $deleteOld) {
                    $deleteOld->delete();
                }
            }
            foreach ($tarifSelected as $tarif) {
                $oldTarif = FormT::where('form_id', $form->id)->whereNot('mekanik_y_n', '=', 'Y')->where('tarif_id', $tarif)->first();
                if (empty($oldTarif)) {
                   $newTarif =  FormT::create([
                        'form_id' => $form->id,
                        'tarif_id' => $tarif,
                        'manifest_id' => $form->manifest_id,
                        'mekanik_y_n' => 'N',
                    ]);
                }
            }

            $tarifMekanikSelected = $request->tarifM_id;
            if (!empty($tarifMekanikSelected)) {
                $checkTarifMekanik = FormT::where('form_id', $form->id)->where('mekanik_y_n', '=', 'Y')->whereNotIn('tarif_id', $tarifMekanikSelected)->get();
                if (!empty($checkTarifMekanik)) {
                    foreach ($checkTarifMekanik as $deleteOldM) {
                        $deleteOldM->delete();
                    }
                }

                foreach ($tarifMekanikSelected as $tarifMekanik) {
                    $oldTarifM = FormT::where('form_id', $form->id)->where('mekanik_y_n', '=', 'Y')->where('tarif_id', $tarifMekanik)->first();
                    if (empty($oldTarifM)) {
                       $newTarifM =  FormT::create([
                            'form_id' => $form->id,
                            'tarif_id' => $tarifMekanik,
                            'manifest_id' => $form->manifest_id,
                            'mekanik_y_n' => 'Y',
                        ]);
                    }
                }

                $form->update([
                    'mekanik_y_n' => 'Y'
                ]);
            }else {
            
                $oldTarifDetilMekanik = FormT::where('form_id', $form->id)->where('mekanik_y_n', '=', 'Y')->delete();
                $form->update([
                    'mekanik_y_n' => 'N'
                ]);
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
        $data['selectedTarif'] = FormT::where('form_id', $id)->where('mekanik_y_n', '=', 'N')->get();
        $data['selectedTarifMekanik'] = FormT::where('form_id', $id)->where('mekanik_y_n', '=', 'Y')->get();
        // dd($data['selectedTarifMekanik']);

        $hariData = FormT::where('form_id', $id)
        ->whereHas('Tarif', function ($query) {
            $query->whereIn('period', [1, 2, 3]);
        })
        ->get()
        ->groupBy(function ($item) {
            return $item->Tarif->period;
        })
        ->map(function ($group) {
            return $group->pluck('jumlah_hari')->first(); // Ambil nilai pertama jika ada
        });

        $hari1 = $hariData->get(1, null);
        $hari2 = $hariData->get(2, null);
        $hari3 = $hariData->get(3, null);

        $data['hari1'] = $hari1;
        $data['hari2'] = $hari2;
        $data['hari3'] = $hari3;

        $data['jumlahHariTotal'] = $form->jumlah_hari;
        // dd($data['jumlahHariTotal']);

        // dd($hari1, $hari2, $hari3);
        switch ($form->period) {
            case 1:
                $data['periode1'] = $hari1 ?? $form->hari_period;
                $data['periode2'] = $hari2 ?? 0;
                $data['periode3'] = $hari3 ?? 0;
                break;
            case 2:
                $data['periode1'] = $hari1 ?? 5;
                $data['periode2'] = $hari2 ?? $form->hari_period;
                $data['periode3'] = $hari3 ?? 0;
                break;
            case 3:
                $data['periode1'] = $hari1 ?? 5;
                $data['periode2'] = $hari2 ?? 5;
                $data['periode3'] = $hari3 ?? $form->hari_period;
                break;
            
            default:
                $data['periode1'] = null;
                $data['periode2'] = null;
                $data['periode3'] = null;
                break;
        }

        $data['jumlahHari'] = $form->jumlah_hari;
        // dd($data['jumlahHari']);
        return view('invoice.form.step2', $data);
    }

    public function step2Post(Request $request)
    {
        try {
            $form = Form::find($request->id);

            // Non Mekanik Inputs
            $tarifIds = $request->input('tarif_id');
            $hargaSatuan = $request->input('harga_satuan');
            $jumlahVolume = $request->input('jumlah_volume');
            $jumlahHari = $request->input('jumlah_hari');
            $total = $request->input('total');

            if (!empty($jumlahHari)) {
                $totalHari = array_sum($jumlahHari);
            }else {
                $totalHari = 0;
            }

            $totalHariMekanik = 0;

            if ($form->mekanik_y_n === 'Y') {
                $jumlahHariMekanik = $request->input('jumlah_hari_mekanik', []);
                $totalHariMekanik = array_sum($jumlahHariMekanik);
            }

            if (($totalHari + $totalHariMekanik) != $form->jumlah_hari) {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Jumlah Hari berbeda dengan interval expired date']);
            }

            foreach ($tarifIds as $index => $tarifId) {
                $formTarif = FormT::where('form_id', $request->id)->where('tarif_id', $tarifId)->where('mekanik_y_n', '=', 'N')->first();
                $formTarif->update([
                    'harga' => $hargaSatuan[$index],
                    'jumlah' => $jumlahVolume[$index],
                    'jumlah_hari' => $jumlahHari[$index] ?? 0,
                    'total' => $total[$index],
                ]);
            }

            $tarif = FormT::where('form_id', $request->id)->where('mekanik_y_n', '=', 'N')->get();
            $total = $tarif->sum('total') + $request->admin;
            $tarifAfterDiscount = $total - $request->discount;

            $pajakAmount = $tarifAfterDiscount * ($request->pajak/100);
            $grandTotal = $tarifAfterDiscount + $pajakAmount;

            if ($form->mekanik_y_n == 'Y') {
                $tarifIdsMekanik = $request->input('tarif_id_mekanik');
                $hargaSatuanMekanik = $request->input('harga_satuan_mekanik');
                $jumlahVolumeMekanik = $request->input('jumlah_volume_mekanik');
                $jumlahHariMekanik = $request->input('jumlah_hari_mekanik');
                $totalMekanik = $request->input('total_mekanik');
            
                foreach ($tarifIdsMekanik as $index => $tarifId) {
                    $formTarif = FormT::where('form_id', $request->id)->where('tarif_id', $tarifId)->where('mekanik_y_n', '=', 'Y')->first();
                    $formTarif->update([
                        'harga' => $hargaSatuanMekanik[$index], // Use the mechanic variable here
                        'jumlah' => $jumlahVolumeMekanik[$index], // Use the mechanic variable here
                        'jumlah_hari' => $jumlahHariMekanik[$index] ?? 0, // Use the mechanic variable here
                        'total' => $totalMekanik[$index], // Use the mechanic variable here
                    ]);
                }
            
                $tarifMekanik = FormT::where('form_id', $request->id)->where('mekanik_y_n', '=', 'Y')->get();
                $totalMekanik = $tarifMekanik->sum('total') + $request->admin_m;
                $tarifAfterDiscountMekanik = $totalMekanik - $request->discount_m;
            
                $pajakAmountMekanik = $tarifAfterDiscountMekanik * ($request->pajak_m/100);
                $grandTotalMekanik = $tarifAfterDiscountMekanik + $pajakAmountMekanik;
            }else {
                $totalMekanik = null;
                $tarifAfterDiscountMekanik = null; 
                $pajakAmountMekanik = null;
                $grandTotalMekanik = null;
            }
            // dd($tarifIds, $tarif, $total, $tarifAfterDiscount, $pajakAmount, $grandTotal, $totalMekanik, $tarifAfterDiscountMekanik, $pajakAmountMekanik, $grandTotalMekanik);

            // Check New Period

            $formTarifCheckPeriod = FormT::where('form_id', $form->id)->whereNot('jumlah_hari', 0)
            ->join('ttarif', 'invoice_form_tarif.tarif_id', '=', 'ttarif.id')
            ->orderBy('ttarif.period', 'desc')
            ->select('invoice_form_tarif.*') // Ensure you select the fields from `form_t`
            ->first();

            $newPeriod = $formTarifCheckPeriod->Tarif->period;
            $newHari = $formTarifCheckPeriod->jumlah_hari;
            // dd($formTarifCheckPeriod, $newPeriod, $newHari);
           
            $form->update([
                'total' => $tarifAfterDiscount,
                'admin'=> $request->admin,
                'pajak'=> $request->pajak,
                'pajak_amount' => $pajakAmount,
                'discount' => $request->discount,
                'grand_total' => $grandTotal,
                'total_m' => $tarifAfterDiscountMekanik,
                'admin_m'=> $request->admin_m,
                'pajak_m'=> $request->pajak_m,
                'pajak_amount_m' => $pajakAmountMekanik,
                'discount_m' => $request->discount_m,
                'grand_total_m' => $grandTotalMekanik,
                'period' =>$newPeriod,
                'hari_period' =>$newHari,
            ]);
            return redirect()->route('invoice.preinvoice', ['id'=>$form->id])->with('status', ['type'=>'success', 'message'=>'Berhasil di Simpan']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Gagal di Simpan '. $th->getMessage()]);
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
        $data['tarifs'] = FormT::where('form_id', $id)->where('mekanik_y_n', '=', 'N')->get();
        $data['tarifM'] = FormT::where('form_id', $id)->where('mekanik_y_n', '=', 'Y')->get();
        $data['terbilang'] = $this->terbilang($data['form']->grand_total);
        $data['terbilangMekanik'] = $this->terbilang($data['form']->grand_total_m);

        return view('invoice.form.step3', $data);
    }

    public function step3Post(Request $request)
    {
        try {
            $form = Form::find($request->id);
            if (!$form) {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Form not found']);
            }

            // Update or create non-mekanik header
            $this->updateOrCreateHeader($form, false, $request);

            // Update or create mekanik header (if applicable)
            if ($form->mekanik_y_n == 'Y') {
                $this->updateOrCreateHeader($form, true, $request);
            } else {
                // Close existing mekanik headers if mekanik is not active
                Header::where('form_id', $form->id)->where('mekanik_y_n', 'Y')->update(['status' => 'C']);
            }

            // Update form status
            $form->update(['status' => 'Y']);

            return redirect()->route('invoice.unpaid')->with('status', ['type' => 'success', 'message' => 'Berhasil di Simpan']);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function updateOrCreateHeader($form, $isMekanik, $request)
    {
        $mekanik = $isMekanik ? 'Y' : 'N';
        $oldHeader = Header::where('form_id', $form->id)->whereNull('type')->where('mekanik_y_n', $mekanik)->first();

        $data = [
            'form_id' => $form->id,
            'manifest_id' => $form->manifest_id,
            'customer_id' => $form->customer_id,
            'judul_invoice' => $isMekanik ? 'Mekanik ' . $request->judul_invoice : $request->judul_invoice,
            'order_no' => $oldHeader->order_no ?? $this->getNextOrderNo(),
            'time_in' => $form->time_in,
            'expired_date' => $form->expired_date,
            'total' => $isMekanik ? $form->total_m : $form->total,
            'admin' => $isMekanik ? $form->admin_m : $form->admin,
            'discount' => $isMekanik ? $form->discount_m : $form->discount,
            'pajak' => $isMekanik ? $form->pajak_m : $form->pajak,
            'pajak_amount' => $isMekanik ? $form->pajak_amount_m : $form->pajak_amount,
            'grand_total' => $isMekanik ? $form->grand_total_m : $form->grand_total,
            'status' => 'N',
            'order_at' => Carbon::now(),
            'kasir_id' => Auth::user()->id,
            'mekanik_y_n' => $mekanik,
        ];
        
        if ($oldHeader) {
            $oldHeader->update($data);
        } else {
            Header::create($data);
        }
    }


    private function getNextOrderNo()
    {
        $latestOrder = Header::orderBy('order_no', 'desc')->first();
        $nextOrderNo = $latestOrder ? intval($latestOrder->order_no) + 1 : 1;
        return str_pad($nextOrderNo, 6, '0', STR_PAD_LEFT);
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
