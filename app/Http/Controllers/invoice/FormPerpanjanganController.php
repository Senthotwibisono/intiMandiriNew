<?php

namespace App\Http\Controllers\invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\MasterTarif as MT;
use App\Models\InvoiceFormPerpanjangan as Form;
use App\Models\InvoiceFormTarifPerpanjangan as FormT;
use App\Models\Manifest;
use App\Models\Customer;
use App\Models\InvoiceHeader as Header;

class FormPerpanjanganController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['title'] = 'List Form Perpanjangan';
        $data['forms'] = Form::where('status', '=', 'N')->orderBy('created_at', 'desc')->get();

        return view('invoice.perpanjangan.form.index', $data);
    }

    public function create(Request $request)
    {
        $form = Form::create([
            'created_at' => Carbon::now(),
            'uid' => Auth::user()->id,
            'status' => 'N',
            'type'=>'P',
        ]);

        return response()->json(['id' => $form->id]);
    }

    public function getOldInvoiceData($id)
    {
        // Find the manifest by ID
        $header = Header::find($id);
        $manifest = Manifest::find($header->manifest_id);
        $customer = Customer::find($header->customer_id);

        // Return the manifest data as JSON
        $cbm = ceil($manifest->meas);
        $data = [
            'nohbl' => $manifest->nohbl,
            'quantity' => $manifest->quantity,
            'weight'   => $manifest->weight,
            'meas'     => $manifest->meas,
            'tglmasuk'     => $header->expired_date,
            'cbm'      => $cbm,
            'customer_id'=>$customer->id,
            'npwp'=>$customer->npwp,
            'phone'=>$customer->phone,
        ];
        return response()->json([
            'success' => true,
            'message' => 'updated successfully!',
            'data'    => $data,
        ]);
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

    public function formIndex($id)
    {
        $data['title'] = 'Create Form || Step 1';
        $data['form'] = Form::find($id);
        $data['manifest'] = Manifest::whereNull('tglrelease')->get();
        $data['customer'] = Customer::all();

        $data['oldInvoices'] = Header::whereNot('status', '=', 'N')->get();

        $data['masterTarif'] = MT::all();
        $data['selectedTarif'] = FormT::where('form_id', $id)->get();

        return view('invoice.perpanjangan.form.formIndex', $data);
    }

    public function step1Post(Request $request)
    {
        try {
            $tarifSelected = $request->tarif_id;
            $tarifMekanikSelected = $request->tarifM_id;
            $masterTarifSelectedPeriod = MT::whereIn('id', $tarifSelected)->where('day', 'Y')->orderBy('period', 'desc')->first();

            // dd($tarifSelected);
            if (empty($tarifSelected)) {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Anda belum memilih tarif yang akan dikenakan']);
            }
            $header = Header::find($request->old_invoice_id);
            if (!$header) {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Terjadi Kesalaham, hubugni admin']);
            }
            if ($request->time_in > $request->expired_date) {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Tanggal Expired lebih kecil dari tanggal awal']);
            }
            $form = Form::find($request->id);
            $interval = Carbon::parse($request->time_in)->diff(Carbon::parse($request->expired_date)) ?? null;
            $jumlahHari = $interval->days;

            $lastPeriod = $header->Form->period;
            $lastHari = $header->Form->hari_period;
            $sisaPeriod1 = 5;  // Quota for Period 1
            $sisaPeriod2 = 5;  // Quota for Period 2
            
            switch ($lastPeriod) {
                case '1':
                    // Calculate remaining days in Period 1
                    $sisaPeriod = $sisaPeriod1 - $lastHari;
            
                    if ($jumlahHari <= $sisaPeriod) {
                        // All remaining days fit into Period 1
                        $usedPeriod1 = $jumlahHari;
                        $usedPeriod2 = 0;
                        $usedPeriod3 = 0;

                        $period = 1;
                        $hariPeriod = $jumlahHari;
                        $massa1 = $jumlahHari;
                        $massa2 = 0;
                        $massa3 = 0;
                    } else {
                        // Overflow into Period 2 and possibly Period 3
                        $usedPeriod1 = $sisaPeriod;
                        $remainingDays = $jumlahHari - $sisaPeriod;
            
                        if ($remainingDays <= $sisaPeriod2) {
                            // All remaining days fit into Period 2
                            $usedPeriod2 = $remainingDays;
                            $usedPeriod3 = 0;
                            $period = 2;
                            $hariPeriod = $remainingDays;
                            $massa1 = $sisaPeriod;
                            $massa2 = $hariPeriod;
                            $massa3 = 0;
                        } else {
                            // Overflow into Period 3 (unlimited)
                            $usedPeriod2 = $sisaPeriod2;
                            $usedPeriod3 = $remainingDays - $sisaPeriod2;
                            $period = 3;
                            $hariPeriod = $usedPeriod3;

                            $massa1 = $sisaPeriod;
                            $massa2 = $usedPeriod2;
                            $massa3 = $hariPeriod;
                        }
                    }
                    break;
            
                case '2':
                    // If already in Period 2, calculate similarly
                    $sisaPeriod = $sisaPeriod2 - $lastHari;
            
                    if ($jumlahHari <= $sisaPeriod) {
                        // All remaining days fit into Period 2
                        $usedPeriod2 = $jumlahHari;
                        $usedPeriod3 = 0;
                        $period = 2;
                        $hariPeriod = $jumlahHari;

                        $massa1 = 0;
                        $massa2 = $hariPeriod;
                        $massa3 = 0;
                    } else {
                        // Overflow into Period 3
                        $usedPeriod2 = $sisaPeriod;
                        $usedPeriod3 = $jumlahHari - $sisaPeriod;
                        $period = 3;
                        $hariPeriod = $usedPeriod3;
                        $massa1 = 0;
                        $massa2 = $usedPeriod2;
                        $massa3 = $hariPeriod;
                    }
                    break;
            
                case '3':
                    // Period 3 has no limit, all days fit here
                    $usedPeriod1 = 0;
                    $usedPeriod2 = 0;
                    $usedPeriod3 = $jumlahHari;
                    $period = 3;
                    $hariPeriod = $usedPeriod3;
                        $massa1 = 0;
                        $massa2 = 0;
                        $massa3 = $hariPeriod;
                    break;
            
                default:
                    if ($jumlahHari<=5) {
                        $period = 1;
                        $hariPeriod = $jumlahHari;
                        $massa1 = $hariPeriod;
                        $massa2 = 0;
                        $massa3 = 0;
                    }elseif ($jumlahHari >= 6 && $jumlahHari <= 10) {
                        $period = 2;
                        $hariPeriod = $jumlahHari - 5;
                        $massa1 = 5;
                        $massa2 = $hariPeriod;
                        $massa3 = 0;
                    }elseif ($jumlahHari >= 11) {
                        $period = 3;
                        $hariPeriod = $jumlahHari - 10;
                        $massa1 = 5;
                        $massa2 = 5;
                        $massa3 = $hariPeriod;
                    }
                    break;
            }
            // dd($jumlahHari, $lastHari, $lastPeriod, $period, $hariPeriod, $massa1, $massa2, $massa3);
            
            $form->update([
                'old_invoice_id' => $header->id,
                'manifest_id'=>$header->manifest_id,
                'customer_id'=>$request->customer_id,
                'cbm'=>$request->cbm,
                'time_in' => $request->time_in,
                'expired_date' => $request->expired_date,
                'jumlah_hari'=>$jumlahHari,
                'period' => $masterTarifSelectedPeriod->period,
                'hari_period' => $hariPeriod,
                'massa1' => $massa1,
                'massa2' => $massa2,
                'massa3' => $massa3,
                'forwarding_id' => $header->Form->forwarding_id,
            ]);

            $allTarifSelected = array_merge((array) $tarifSelected, (array) $tarifMekanikSelected);

            // check Massa 1
            $checkAvalibleMassa = MT::whereIn('id', $allTarifSelected)
                        ->where('day', '=', 'Y')
                        ->get();
            if ($checkAvalibleMassa->isEmpty()) {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Anda belum memilih tarif untuk perpanjangan']);
            }
            
            
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
                $form->update([
                    'mekanik_y_n' => 'N'
                ]);
            }
            return redirect()->route('invoice.perpanjangan.step2', ['id'=>$form->id])->with('status', ['type'=>'success', 'message'=>'Berhasil di Simpan']);
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

        $data['periode1'] = $form->massa1;
        $data['periode2'] = $form->massa2;
        $data['periode3'] = $form->massa3;

        return view('invoice.perpanjangan.form.step2', $data);
    }

    public function step2Post(Request $request)
    {
        // dd($request->id);
        try {
            $form = Form::find($request->id);
            // dd($form);
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
            // dd($tarif);
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
            ->join('ttarif', 'invoice_form_tarif_perpanjangan.tarif_id', '=', 'ttarif.id')
            ->orderBy('ttarif.period', 'desc')
            ->select('invoice_form_tarif_perpanjangan.*') // Ensure you select the fields from `form_t`
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
            return redirect()->route('invoice.perpanjangan.preinvoice', ['id'=>$form->id])->with('status', ['type'=>'success', 'message'=>'Berhasil di Simpan']);
        } catch (\Throwable $e) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Gagal di Simpan '. $e->getMessage()]);
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

        return view('invoice.perpanjangan.form.step3', $data);
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

            return redirect()->route('invoice.perpanjangan.unpaid')->with('status', ['type' => 'success', 'message' => 'Berhasil di Simpan']);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function updateOrCreateHeader($form, $isMekanik, $request)
    {
        $mekanik = $isMekanik ? 'Y' : 'N';
        $oldHeader = Header::where('form_id', $form->id)->where('type', 'P')->where('mekanik_y_n', $mekanik)->first();

        $data = [
            'form_id' => $form->id,
            'type' => 'P',
            'manifest_id' => $form->manifest_id,
            'customer_id' => $form->customer_id,
            'judul_invoice' => $isMekanik ? 'Perpanjangan Mekanik ' . $request->judul_invoice : 'Perpanjangan ' . $request->judul_invoice,
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
