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

class FormFCLController extends Controller
{
    public function dataTable(Request $request)
    {
        $form = Form::whereNot('status', 'Y')->get();
        return DataTables::of($form)
        ->addColumn('action', function($form){
            return '<a href="/invoiceFCL/form/createEdit/Step1/'.$form->id.'" class="btn btn-warning"><i class="fa fa-pencil"></i></a>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    
    public function indexStep1()
    {
        $data['title'] = 'Create Invoice FCL - Step 1';
        $data['customers'] = Customer::get();

        return view('invoiceFCL.form.step1', $data);
    }

    public function editStep1($id)
    {
        $data['title'] = 'Create Invoice FCL - Step 1';
        $data['customers'] = Customer::get();

        $data['form'] = Form::find($id);
        $data['containerInvoice'] = FormC::where('form_id', $id)->get();

        // dd($data);

        return view('invoiceFCL.form.step1Edit', $data);
    }

    public function getBLAWB(Request $request)
    {
        $search = $request->search;
        $page = $request->page;
        $perPage = 10; // Jumlah item per halaman

        $query = ContF::select('nobl')->distinct();

        if ($search) {
            $query->where('nobl', 'like', "%{$search}%");
        }

        $cont = $query->paginate($perPage);

        return response()->json([
            'data' => $cont->items(),
            'more' => $cont->hasMorePages(),
        ]);
    }

    public function getBLData(Request $request)
    {
        try {
            $cont = ContF::whereNotNull('tglmasuk')->where('nobl', $request->bl)->get();
            if ($cont->isEmpty()) {
                return response()->json([
                    'success'=> false,
                    'message'=> 'Tidak ada container yang dapat dipilih !!',
                ]);
            }
            $dateBL = ContF::where('nobl', $request->bl)->value('tgl_bl_awb');
            $custId = ContF::where('nobl', $request->bl)->value('cust_id');
            $customer = Customer::find($custId);

            // var_dump($customer);
            // die();
            
            return response()->json([
                'success' => true,
                'data' => $dateBL,
                'containers' => $cont, // Kirim daftar container ke frontend
                'customer' => $customer,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success'=> false,
                'message'=> $th->getMessage(),
            ]);
        }
    }

    public function postStep1(Request $request)
    {
        try {

            $cont = ContF::whereIn('id', $request->container_id)->get();
            if ($cont->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Container belum bisa di pilih',
                ]);
            }

            $contBB = $cont->where('ctr_type', 'BB');
            if ($contBB->isNotEmpty()) {
                if ($request->type != 'STANDART') {
                    return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Container BB tidak boleh selain standart']);
                }
            }

            $etaValues = $cont->pluck('eta')->unique();
            if ($etaValues->count() > 1) {
                return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Terdapat nilai ETA yang berbeda.']);
            }

            // $checkMasuk = $cont->pluck('tglmasuk')->unique();
            // if ($checkMasuk->count() > 1) {
            //     return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Terdapat nilai Tanggal Masuk yang berbeda.']);
            // }

            // $checkDok = $cont->whereNull('no_dok');
            // if ($checkDok->count() > 1) {
            //     $noContKosong = $checkDok->pluck('nocontainer')->implode(', ');
            //     return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Terdapat container dengan dokumen kosong: ' . $noContKosong]);
            // }

            $eta = $cont->value('eta');
            if ($eta > $request->etd) {
                return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Tanggal Rencana Keluar lebih besar dari Tanggal Masuk.']);
            }

            $singleCont = ContF::where('id', $request->container_id)->first();
            // dd($singleCont);
           
            $form = Form::create([
                'lokasi_sandar_id' => $singleCont->lokasisandar_id,
                'nobl' =>$singleCont->nobl,
                'tgl_bl_awb' =>$singleCont->tgl_bl_awb,
                'cust_id' => $request->cust_id,
                'eta' => $eta,
                'etd' => $request->etd,
                'status' => 'N',
                'type' => $request->type,
                'uid' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
            
            foreach ($cont as $ct) {

                if ($ct->tglbehandle != null) {
                    $statusBehandle = 'Y';
                }else {
                    $statusBehandle = 'N';
                }
                $containerForm = FormC::create([
                    'form_id' => $form->id,
                    'container_id' => $ct->id,
                    'size' => $ct->size,
                    'ctr_type' => $ct->ctr_type,
                    'behandle_yn' => $statusBehandle,
                    'uid' => Auth::user()->id,
                    'created_at' => Carbon::now(),
                    'tglmasuk' => $ct->tglmasuk
                ]);
            }

            return redirect('/invoiceFCL/form/indexStep2/'.$form->id)->with('status', ['type'=>'success', 'message' => 'Data Berhasil Disimpan']);

        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Something Wrong: '. $th->getMessage()]);
        }
    }

    public function updateStep1(Request $request)
    {
        try {
            // Pastikan container_id tidak kosong
            if (!$request->container_id || empty($request->container_id)) {
                return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Silakan pilih setidaknya satu container.']);
            }

            // Ambil data container berdasarkan ID yang dipilih
            $cont = ContF::whereIn('id', $request->container_id)->get();

            // Validasi ETA harus sama
            $etaValues = $cont->pluck('eta')->unique();
            if ($etaValues->count() > 1) {
                return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Terdapat nilai ETA yang berbeda.']);
            }

            // Validasi Tanggal Masuk harus sama
            // $checkMasuk = $cont->pluck('tglmasuk')->unique();
            // if ($checkMasuk->count() > 1) {
            //     return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Terdapat nilai Tanggal Masuk yang berbeda.']);
            // }

            $checkBelumMasuk = $cont->whereNull('tglmasuk');
            if ($checkBelumMasuk->count() > 0) {
                return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Terdapat Container Belum Memiliki tgl Masuk: ' . $checkBelumMasuk->pluck('nocontainer')->implode(', ')]);
            }

            // Ambil ETA yang sudah dipastikan unik
            $eta = $etaValues->first();

            // Validasi ETA tidak boleh lebih besar dari ETD
            if ($eta >= $request->etd) {
                return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Tanggal Rencana Keluar harus lebih besar dari ETA.']);
            }

            // Ambil satu container untuk referensi data lokasi sandar
            $singleCont = $cont->first();

            // Cari form berdasarkan ID
            $form = Form::find($request->id);
            if (!$form) {
                return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Data form tidak ditemukan.']);
            }

            // Update Form
            // dd($cont, $request->container_id);
            $form->update([
                'lokasi_sandar_id' => $singleCont->lokasisandar_id ?? $singleCont->job->lokasisandar_id,
                'nobl' => $singleCont->nobl,
                'tgl_bl_awb' => $singleCont->tgl_bl_awb,
                'cust_id' => $request->cust_id,
                'eta' => $eta,
                'etd' => $request->etd,
                'status' => 'N',
                'type' => $request->type,
                'uid' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);

            // Hapus container lama yang tidak termasuk dalam request terbaru
            FormC::where('form_id', $form->id)
                ->whereNotIn('container_id', $request->container_id)
                ->delete();

            // Loop setiap container untuk disimpan di FormC
            // dd($cont);
            foreach ($cont as $ct) {
                $statusBehandle = $ct->tglbehandle ? 'Y' : 'N';

                // Cek apakah sudah ada di FormC
                $existingContainer = FormC::where('form_id', $form->id)
                                          ->where('container_id', $ct->id)
                                          ->first();
                if ($existingContainer) {
                    $existingContainer->update([
                        'size' => $ct->size,
                        'ctr_type' => $ct->ctr_type,
                        'behandle_yn' => $statusBehandle,
                        'uid' => Auth::user()->id,
                        'created_at' => Carbon::now(),
                        'tglmasuk' => $ct->tglmasuk
                    ]);
                }else {
                    
                    FormC::create([
                        'form_id' => $form->id,
                        'container_id' => $ct->id,
                        'size' => $ct->size,
                        'ctr_type' => $ct->ctr_type,
                        'behandle_yn' => $statusBehandle,
                        'uid' => Auth::user()->id,
                        'created_at' => Carbon::now(),
                        'tglmasuk' => $ct->tglmasuk
                    ]);
                }
            }

            return redirect('/invoiceFCL/form/indexStep2/'.$form->id)->with('status', ['type'=>'success', 'message' => 'Data Berhasil Disimpan']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Something Wrong: '. $th->getMessage()]);
        }
    }

    public function cancelForm($id)
    {
        try {
            FormC::where('form_id', $id)->delete();
            Form::where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data Telah di Hapus'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something Wrong: ' . $th->getMessage()
            ]);
        }
    }

    public function indexStep2($id)
    {
        $data['title'] = "Pre-Invoice FCL";

        $data['form'] = Form::find($id);

        $data['containerInvoice'] = FormC::where('form_id', $id)->get();

        // Checking Master Tarif WMS
        $tarifWMS = TWMS::select('size', 'type')->get()->toArray();
        $invalidContainers = $data['containerInvoice']->filter(function ($container) use ($tarifWMS) {
            return !in_array(['size' => $container->size, 'type' => $container->ctr_type], $tarifWMS);
        });

        if ($invalidContainers->isNotEmpty()) {
            $invalidContainerNumbers = $invalidContainers->pluck('cont.nocontainer')->implode(', ');
            return redirect('/invoiceFCL/form/createEdit/Step1/'. $id)->with('status', ['type'=> 'error', 'message' => 'Tidak ada tarif WMS yang cocok untuk container :' . $invalidContainerNumbers]);
        }

        // Check Tarif TPS
        $tarifTPS = TTPS::where('lokasi_sandar_id', $data['form']->lokasi_sandar_id)->select('size', 'type')->get()->toArray();
        $invalidContainersTPS = $data['containerInvoice']->filter(function ($container) use ($tarifTPS) {
            return !in_array(['size' => $container->size, 'type' => $container->ctr_type], $tarifTPS);
        });
        if ($invalidContainersTPS->isNotEmpty()) {
            $invalidContainerNumbersTPS = $invalidContainersTPS->pluck('cont.nocontainer')->implode(', ');
            return redirect('/invoiceFCL/form/createEdit/Step1/'. $id)->with('status', ['type'=> 'error', 'message' => 'Tidak ada tarif TPS yang cocok untuk container :' . $invalidContainerNumbersTPS]);
        }

        $container = FormC::where('form_id', $id)->get();
        $containerSize = $container->pluck('size')->unique()->toArray();
        $containerType = $container->pluck('ctr_type')->unique()->toArray();
        $tglMasuk = $container->pluck('tglmasuk')->unique()->toArray();
       
        $data['tglMasuk'] = $tglMasuk;
        $data['tglMasukView'] = $container->pluck('tglmasuk')->unique()->implode(', ');

        $wmsPay = TWMS::whereIn('size', $containerSize)->whereIn('type', $containerType)->get();
        $data['tarifWMS'] = $wmsPay;
        $tpsPay = TTPS::where('lokasi_sandar_id', $data['form']->lokasi_sandar_id)->whereIn('size', $containerSize)->whereIn('type', $containerType)->get();
        $data['tarifTPS'] = $tpsPay;
        // dd($tpsPay, $wmsPay, $tarifTPS);

        $singleCont = $container->first();
        $data['singleCont'] = $singleCont;

        $data['jenisContainer'] = $container->pluck('size')->unique()->implode(', ');
        $data['typeContainer'] = $container->pluck('ctr_type')->unique()->implode(', ');

        $data['size'] = $container->pluck('size')->unique();
        $data['type'] = $container->pluck('ctr_type')->unique();
        $data['nocontainer'] = $container->pluck('cont.nocontainer')->implode(', ');

        $tglMasukTerawal = $singleCont->cont->tglmasuk;
        $data['jumlahHariWMS'] = Carbon::parse($tglMasukTerawal)->diffInDays(Carbon::parse($data['form']->etd)) + 1;

        // Hari TPS
        $jumlahHariTPS = Carbon::parse($data['form']->eta)->diffInDays(Carbon::parse($tglMasukTerawal));
        if ($jumlahHariTPS > 1) {
            $massa3 = $jumlahHariTPS - 1;
            $massa2 = 1;
        }elseif ($jumlahHariTPS == 1) {
            $massa3 = 0;
            $massa2 = 1;
        }else {
            $massa2 = 0;
            $massa3 = 0;
        }

        $data['massa2'] = $massa2 ?? 0;
        $data['massa3'] = $massa3 ?? 0;

        // dd($data['massa2'], $data['massa3']);
        // dd($data['form']->eta, $tglMasukTerawal, $jumlahHariTPS);
        // dd($data['jumlahHariWMS']);
        // dd($singleCont);
        // dd($wmsPay);

        return view('invoiceFCL.form.step2', $data);
    }

    public function postStep2(Request $request)
    {
        
        try {
            $job = JobF::find($request->job_id);
            $form = Form::find($request->form_id);
            $formC = FormC::where('form_id', $request->form_id)->get();
            $cust = Customer::find($form->cust_id);
    
            $grandTotal = $request->grand_total;
            if ($grandTotal >= 5000000) {
                $sumGrandTotal = $grandTotal + 10000;
            }else {
                $sumGrandTotal = $grandTotal;
            }
            $form->update([
                'status' => 'Y'
            ]);

            foreach ($formC as $cont) {
                $tcWMS = TWMS::where('size', $cont->size)->where('type', $cont->ctr_type)->first();
                if ($tcWMS) {
                    $cont->update([
                        'tarif_wms_id' => $tcWMS->id
                    ]);
                }
                $tcTPS = TTPS::where('lokasi_sandar_id', $form->lokasi_sandar_id)->where('size', $cont->size)->where('type', $cont->ctr_type)->first();
                if ($tcTPS) {
                    $cont->update([
                        'tarif_tps_id' => $tcTPS->id
                    ]);
                }
            }

            $header = Header::create([
                'proforma_no' => $this->getNextOrderNo(),
                'kd_tps_asal' => $request->kd_tps_asal,
                'form_id' => $form->id,
                'job_id' => $job->id,
                'nobl' => $form->nobl,
                'tgl_bl_awb' => $form->tgl_bl_awb,
                'cust_id' => $cust->id,
                'cust_name' => $cust->name,
                'cust_alamat' => $cust->alamat,
                'cust_npwp'  => $cust->npwp,
                'eta' => $form->eta,
                'tglmasuk' => $request->tglmasuk,
                'etd'=> $form->etd,
                'total_tps' => $request->total_tps,
                'total_wms' => $request->total_wms,
                'total' => $request->total,
                'admin' => $request->admin,
                'ppn' => $request->ppn,
                'grand_total' => $sumGrandTotal,
                'status' => 'N',
                'uidCreate' => Auth::user()->id,
                'created_at' => Carbon::now(),
                'kapal_voy' => $request->kapal_voy,
            ]);
    
            $contFilter = $formC->groupBy(['size', 'ctr_type']);
            $tglMasuks = $formC->pluck('tglmasuk')->unique()->toArray();
    
            foreach ($contFilter as $size => $types) {
                foreach ($types as $ctr_type => $containers) {
                    $jumlah = $containers->count();
                    
                    // TPS Tarif 
                    $tarifTPS = TTPS::where('lokasi_sandar_id', $form->lokasi_sandar_id)->where('size', $size)->where('type', $ctr_type)->first();
                    
                    if ($form->type == 'STANDART' || $form->type == 'BCF') {
                        # code...
                        // Penumpukkan Massa1
                        Detil::create([
                            'form_id' => $form->id,
                            'invoice_id' => $header->id,
                            'tps' => $request->kd_tps_asal,
                            'keterangan' => 'Penumpukkan Massa 1 (' . $size . ' / ' .$ctr_type.' )',
                            'size' => $size,
                            'type' => $ctr_type,
                            'tarif_dasar' => 0,
                            'satuan' => '0',
                            'jumlah' => $jumlah,
                            'jumlah_hari' => 1,
                            'total' => 0,
                        ]);
                        foreach ($tglMasuks as $tglmasuk) {
                            $eta = Carbon::parse($form->eta);
                            $masuk = Carbon::parse($tglmasuk);
                            $jumlahHari = $eta->diffInDays($masuk);
                            // Tentukan massa2TPS dan massa3TPS
                            if ($jumlahHari > 1) {
                                $massa2TPS = 1;
                                $massa3TPS = $jumlahHari - 1;
                            } else {
                                $massa2TPS = 0;
                                $massa3TPS = 0;
                            }
    
                            $jumlahContByMassa = $containers->where('tglmasuk', $tglmasuk)->count();
                            // dd($jumlahContByMassa);
                            $tarifDasarMassa2 = ($tarifTPS->tarif_dasar_massa * $tarifTPS->massa2) / 100;
                            $totalMassa2TPS = $tarifDasarMassa2 * $jumlahContByMassa * $massa2TPS;
                            if ($jumlahContByMassa > 0) {
                                # code...
                                Detil::create([
                                    'form_id' => $form->id,
                                    'invoice_id' => $header->id,
                                    'tps' => $request->kd_tps_asal,
                                    'keterangan' => 'Penumpukkan Massa 2 (' . $size . ' / ' .$ctr_type.' ) masuk pd ' . $masuk->format('Y-m-d'),
                                    'size' => $size,
                                    'type' => $ctr_type,
                                    'tarif_dasar' => $tarifTPS->tarif_dasar_massa,
                                    'satuan' => $tarifTPS->massa2,
                                    'jumlah' => $jumlahContByMassa,
                                    'jumlah_hari' => $massa2TPS,
                                    'total' => $totalMassa2TPS,
                                ]);
                                $tarifDasarMassa3 = ($tarifTPS->tarif_dasar_massa * $tarifTPS->massa3) / 100;
                                $totalMassa3TPS = $tarifDasarMassa3 * $jumlahContByMassa * $massa3TPS;
                                Detil::create([
                                    'form_id' => $form->id,
                                    'invoice_id' => $header->id,
                                    'tps' => $request->kd_tps_asal,
                                    'keterangan' => 'Penumpukkan Massa 3 (' . $size . ' / ' .$ctr_type.' ) masuk pd ' . $masuk->format('Y-m-d'),
                                    'size' => $size,
                                    'type' => $ctr_type,
                                    'tarif_dasar' => $tarifTPS->tarif_dasar_massa,
                                    'satuan' => $tarifTPS->massa3,
                                    'jumlah' => $jumlah,
                                    'jumlah_hari' => $massa3TPS,
                                    'total' => $totalMassa3TPS,
                                ]);
                            }
                        }
                    }

                    if ($form->type == 'STANDART' || $form->type == 'TPP') {
                        // liftOn
                        $totalLiftOn = $tarifTPS->lift_on * $jumlah;
                        Detil::create([
                            'form_id' => $form->id,
                            'invoice_id' => $header->id,
                            'tps' => $request->kd_tps_asal,
                            'keterangan' => 'Lift On (' . $size . ' / ' .$ctr_type.' )',
                            'size' => $size,
                            'type' => $ctr_type,
                            'tarif_dasar' => $tarifTPS->lift_on,
                            'satuan' => '0',
                            'jumlah' => $jumlah,
                            'jumlah_hari' => 0,
                            'total' => $totalLiftOn,
                        ]);
                        // Hyro Scan
                        $totalHyroScan = $tarifTPS->hyro_scan * $jumlah;
                        Detil::create([
                            'form_id' => $form->id,
                            'invoice_id' => $header->id,
                            'tps' => $request->kd_tps_asal,
                            'keterangan' => 'Hyro Scan (' . $size . ' / ' .$ctr_type.' )',
                            'size' => $size,
                            'type' => $ctr_type,
                            'tarif_dasar' => $tarifTPS->hyro_scan,
                            'satuan' => '0',
                            'jumlah' => $jumlah,
                            'jumlah_hari' => 0,
                            'total' => $totalHyroScan,
                        ]);
                        // Perawatan IT
                        $totalPerawatanIT = $tarifTPS->totalPerawatanIT * $jumlah;
                        Detil::create([
                            'form_id' => $form->id,
                            'invoice_id' => $header->id,
                            'tps' => $request->kd_tps_asal,
                            'keterangan' => 'Hyro Scan (' . $size . ' / ' .$ctr_type.' )',
                            'size' => $size,
                            'type' => $ctr_type,
                            'tarif_dasar' => $tarifTPS->totalPerawatanIT,
                            'satuan' => '0',
                            'jumlah' => $jumlah,
                            'jumlah_hari' => 0,
                            'total' => $totalPerawatanIT,
                        ]);
                        // Gate Pass
                        $totalGatePass = $tarifTPS->gate_pass * $jumlah;
                        Detil::create([
                            'form_id' => $form->id,
                            'invoice_id' => $header->id,
                            'tps' => $request->kd_tps_asal,
                            'keterangan' => 'Gate Pass Admin & Pass Truck (' . $size . ' / ' .$ctr_type.' )',
                            'size' => $size,
                            'type' => $ctr_type,
                            'tarif_dasar' => $tarifTPS->gate_pass,
                            'satuan' => '0',
                            'jumlah' => $jumlah,
                            'jumlah_hari' => 0,
                            'total' => $totalGatePass,
                        ]);
                    }
    
                    // Tarif WMS
                    $tarifWMS = TWMS::where('size', $size)->where('type', $ctr_type)->first();
                    
                    if ($form->type == 'STANDART' || $form->type == 'BCF') {
                        foreach ($tglMasuks as $tglmasuk) {
                            $etd = Carbon::parse($form->etd);
                            $masuk = Carbon::parse($tglmasuk);
                            $jumlahHariWMS = $masuk->diffInDays($etd) + 1;
                            $jumlahContByMassaWMS = $containers->where('tglmasuk', $tglmasuk)->count();
    
                            $tarifDasarMassa = ($tarifWMS->tarif_dasar_massa * $tarifWMS->massa)/100;
                            $totalMassaWMS = $tarifDasarMassa*$jumlahContByMassaWMS * $jumlahHariWMS;
                            if ($jumlahContByMassaWMS) {
                                # code...
                                Detil::create([
                                    'form_id' => $form->id,
                                    'invoice_id' => $header->id,
                                    'tps' => 'Depo',
                                    'keterangan' => 'Penumpukan (' . $size . ' / ' .$ctr_type.' ) Masuk pd ' . $masuk->format('Y-m-d'),
                                    'size' => $size,
                                    'type' => $ctr_type,
                                    'tarif_dasar' => $tarifWMS->tarif_dasar_massa,
                                    'satuan' => '0',
                                    'jumlah' => $jumlahContByMassaWMS,
                                    'jumlah_hari' => $jumlahHariWMS,
                                    'total' => $totalMassaWMS,
                                ]);
                            }
                        }
                        // Massa
                    }

                    if ($form->type == 'STANDART' || $form->type == 'TPP') {
                        // PLP
                        $totalPLP = $tarifWMS->paket_plp*$jumlah;
                        Detil::create([
                            'form_id' => $form->id,
                            'invoice_id' => $header->id,
                            'tps' => 'Depo',
                            'keterangan' => 'Paket PLP (' . $size . ' / ' .$ctr_type.' )',
                            'size' => $size,
                            'type' => $ctr_type,
                            'tarif_dasar' => $tarifWMS->paket_plp,
                            'satuan' => '0',
                            'jumlah' => $jumlah,
                            'jumlah_hari' => 0,
                            'total' => $totalPLP,
                        ]);
                        // lift On
                        $totalLiftOnWMS = $tarifWMS->lift_on*$jumlah;
                        Detil::create([
                            'form_id' => $form->id,
                            'invoice_id' => $header->id,
                            'tps' => 'Depo',
                            'keterangan' => 'Lift On (' . $size . ' / ' .$ctr_type.' )',
                            'size' => $size,
                            'type' => $ctr_type,
                            'tarif_dasar' => $tarifWMS->lift_on,
                            'satuan' => '0',
                            'jumlah' => $jumlah,
                            'jumlah_hari' => 0,
                            'total' => $totalLiftOnWMS,
                        ]);
                        // lift Off
                        $totalLiftOffWMS = $tarifWMS->lift_off*$jumlah;
                        Detil::create([
                            'form_id' => $form->id,
                            'invoice_id' => $header->id,
                            'tps' => 'Depo',
                            'keterangan' => 'Lift Off (' . $size . ' / ' .$ctr_type.' )',
                            'size' => $size,
                            'type' => $ctr_type,
                            'tarif_dasar' => $tarifWMS->lift_off,
                            'satuan' => '0',
                            'jumlah' => $jumlah,
                            'jumlah_hari' => 0,
                            'total' => $totalLiftOffWMS,
                        ]);
                    }
    
                    if ($form->type == 'STANDART') {
                        if ($tarifWMS->surcharge != null || $tarifWMS->surcharge != 0) {
                            // lift surcharge
                            $totalSurcharge = (($totalPLP + $totalLiftOffWMS + $totalLiftOnWMS + $totalMassaWMS)*$tarifWMS->surcharge)/100;
                            Detil::create([
                                'form_id' => $form->id,
                                'invoice_id' => $header->id,
                                'tps' => 'Depo',
                                'keterangan' => 'Surcharge (' . $size . ' / ' .$ctr_type.' )',
                                'size' => $size,
                                'type' => $ctr_type,
                                'tarif_dasar' => $tarifWMS->surcharge,
                                'satuan' => '0',
                                'jumlah' => $jumlah,
                                'jumlah_hari' => 0,
                                'total' => $totalSurcharge,
                            ]);
                        }
                    }
                }
            }
            
            return redirect('/invoiceFCL/form/index')->with('status', ['type'=> 'success', 'message' => 'Data Berhasil di Simpan']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=> 'error', 'message' => 'Something gone wrong : ' . $th->getMessage()]);
            //throw $th;
        }
        
    }


    public function indexPerpanjangan()
    {
        $data['title'] = 'Form Invoice - FCL (Perpanjangan)';

        return view('invoiceFCL.formPerpanjangan.index', $data);
    }

    public function dataTablePerpanjangan(Request $request)
    {
        $form = Form::where('type', 'EXTEND')->whereNot('status', 'Y')->get();
        return DataTables::of($form)
        ->addColumn('action', function($form){
            return '<a href="/invoiceFCL/form/extend/createEdit/Step1/'.$form->id.'" class="btn btn-warning"><i class="fa fa-pencil"></i></a>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function indexStep1Perpanjangan()
    {
        $data['title'] = 'Create Invoice FCL (Perpanjangan) - Step 1';
        $data['customers'] = Customer::get();

        return view('invoiceFCL.formPerpanjangan.step1', $data);
    }

    public function editStep1Perpanjangan($id)
    {
        $data['title'] = 'Create Invoice FCL (Perpanjangan)- Step 1';
        $data['customers'] = Customer::get();

        $data['form'] = Form::find($id);
        $data['containerInvoice'] = FormC::where('form_id', $id)->get();

        // dd($data);

        return view('invoiceFCL.formPerpanjangan.step1Edit', $data);
    }

    public function getBLAWBPerpanjangan(Request $request)
    {
        $search = $request->search;
        $page = $request->page;
        $perPage = 10; // Jumlah item per halaman

        $query = Header::where('status', 'Y');

        if ($search) {
            $query->where('nobl', 'like', "%{$search}%");
        }

        $cont = $query->paginate(10);

        return response()->json([
            'data' => $cont->items(),
            'more' => $cont->hasMorePages(),
        ]);
    }

    public function getBLDataPerpanjangan(Request $request)
    {
        try {
            $header = Header::find($request->id);
            $contInvoice = FormC::where('form_id', $header->form_id)->pluck('container_id')->toArray();
            // var_dump($contInvoice);
            $cont = ContF::whereIn('id', $contInvoice)->whereNull('tglkeluar')->get();
            if ($cont->isEmpty()) {
                return response()->json([
                    'success'=> false,
                    'message'=> 'Tidak ada container yang dapat dipilih !!',
                ]);
            }
            $dateBL = $header->nobl;
           
            $customer = Customer::find($header->cust_id);

            // var_dump($customer);
            // die();
            
            return response()->json([
                'success' => true,
                'data' => $dateBL,
                'containers' => $cont, // Kirim daftar container ke frontend
                'customer' => $customer,
                'dataHeader' => $header,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success'=> false,
                'message'=> $th->getMessage(),
            ]);
        }
    }

    public function postStep1Perpanjangan(Request $request)
    {
        try {

            if ($request->etd <= $request->eta) {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Tanggal Rencana Keluar Harus Lebih Besar Dibanding Tanggar Expired Sebelmnya']);
            }
            
            $cont = ContF::whereIn('id', $request->container_id)->get();
            if ($cont->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Container belum bisa di pilih',
                ]);
            }

            $singleCont = ContF::where('id', $request->container_id)->first();
            // dd($singleCont);
           
            $oldHeader = Header::find($request->inv_id);
            $form = Form::create([
                'lokasi_sandar_id' => $singleCont->lokasisandar_id,
                'nobl' =>$singleCont->nobl,
                'tgl_bl_awb' =>$singleCont->tgl_bl_awb,
                'cust_id' => $request->cust_id,
                'eta' => $request->eta,
                'etd' => $request->etd,
                'status' => 'N',
                'uid' => Auth::user()->id,
                'created_at' => Carbon::now(),
                'inv_id' => $oldHeader->id,
                'form_id' => $oldHeader->form_id,
                'type' => 'EXTEND',
            ]);
            
            foreach ($cont as $ct) {

                if ($ct->tglbehandle != null) {
                    $statusBehandle = 'Y';
                }else {
                    $statusBehandle = 'N';
                }
                $containerForm = FormC::create([
                    'form_id' => $form->id,
                    'container_id' => $ct->id,
                    'size' => $ct->size,
                    'ctr_type' => $ct->ctr_type,
                    'behandle_yn' => $statusBehandle,
                    'uid' => Auth::user()->id,
                    'created_at' => Carbon::now(),
                    'tglmasuk' => $ct->tglmasuk
                ]);
            }

            return redirect('/invoiceFCL/form/extend/indexStep2/'.$form->id)->with('status', ['type'=>'success', 'message' => 'Data Berhasil Disimpan']);

        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Something Wrong: '. $th->getMessage()]);
        }
    }

    public function indexStep2Perpanjangan($id)
    {
        $data['title'] = "Pre-Invoice FCL (Perpanjangan)";

        $data['form'] = Form::find($id);

        $data['containerInvoice'] = FormC::where('form_id', $id)->get();
        $oldHeader = Header::find($data['form']->inv_id);
        $oldForm = Form::find($data['form']->form_id);

        // Checking Master Tarif WMS
        $tarifWMS = TWMS::select('size', 'type')->get()->toArray();
        $invalidContainers = $data['containerInvoice']->filter(function ($container) use ($tarifWMS) {
            return !in_array(['size' => $container->size, 'type' => $container->ctr_type], $tarifWMS);
        });

        if ($invalidContainers->isNotEmpty()) {
            $invalidContainerNumbers = $invalidContainers->pluck('cont.nocontainer')->implode(', ');
            return redirect('/invoiceFCL/form/extend/createEdit/Step1/'. $id)->with('status', ['type'=> 'error', 'message' => 'Tidak ada tarif WMS yang cocok untuk container :' . $invalidContainerNumbers]);
        }

        // Check Tarif TPS

        $container = FormC::where('form_id', $id)->get();
        $containerSize = $container->pluck('size')->unique()->toArray();
        $containerType = $container->pluck('ctr_type')->unique()->toArray();
        $tglMasuk = $container->pluck('tglmasuk')->unique()->toArray();
       
        $data['tglMasuk'] = $tglMasuk;
        $data['tglMasukView'] = $data['form']->eta;

        $wmsPay = TWMS::whereIn('size', $containerSize)->whereIn('type', $containerType)->get();
        $data['tarifWMS'] = $wmsPay;

        $singleCont = $container->first();
        $data['singleCont'] = $singleCont;

        $data['jenisContainer'] = $container->pluck('size')->unique()->implode(', ');
        $data['typeContainer'] = $container->pluck('ctr_type')->unique()->implode(', ');

        $data['size'] = $container->pluck('size')->unique();
        $data['type'] = $container->pluck('ctr_type')->unique();
        $data['nocontainer'] = $container->pluck('cont.nocontainer')->implode(', ');

        $data['jumlahHariWMS'] = Carbon::parse($data['form']->eta)->diffInDays(Carbon::parse($data['form']->etd));

        return view('invoiceFCL.formPerpanjangan.step2', $data);
    }

    public function postStep2Perpanjangan(Request $request)
    {
        
        try {
            $job = JobF::find($request->job_id);
            $form = Form::find($request->form_id);
            $formC = FormC::where('form_id', $request->form_id)->get();
            $cust = Customer::find($form->cust_id);
    
            $grandTotal = $request->grand_total;
            if ($grandTotal >= 5000000) {
                $sumGrandTotal = $grandTotal + 10000;
            }else {
                $sumGrandTotal = $grandTotal;
            }
            $form->update([
                'status' => 'Y'
            ]);

            foreach ($formC as $cont) {
                $tcWMS = TWMS::where('size', $cont->size)->where('type', $cont->ctr_type)->first();
                if ($tcWMS) {
                    $cont->update([
                        'tarif_wms_id' => $tcWMS->id
                    ]);
                }
            }

            $header = Header::create([
                'proforma_no' => $this->getNextOrderNo(),
                'kd_tps_asal' => $request->kd_tps_asal,
                'form_id' => $form->id,
                'job_id' => $job->id,
                'nobl' => $form->nobl,
                'tgl_bl_awb' => $form->tgl_bl_awb,
                'cust_id' => $cust->id,
                'cust_name' => $cust->name,
                'cust_alamat' => $cust->alamat,
                'cust_npwp'  => $cust->npwp,
                'eta' => $form->eta,
                'tglmasuk' => $request->tglmasuk,
                'etd'=> $form->etd,
                'total_tps' => $request->total_tps,
                'total_wms' => $request->total_wms,
                'total' => $request->total,
                'admin' => $request->admin,
                'ppn' => $request->ppn,
                'grand_total' => $sumGrandTotal,
                'status' => 'N',
                'uidCreate' => Auth::user()->id,
                'created_at' => Carbon::now(),
                'kapal_voy' => $request->kapal_voy,
                'type' => 'EXTEND',
            ]);
    
            $contFilter = $formC->groupBy(['size', 'ctr_type']);
            $tglMasuks = $formC->pluck('tglmasuk')->unique()->toArray();
    
            foreach ($contFilter as $size => $types) {
                foreach ($types as $ctr_type => $containers) {
                    $jumlah = $containers->count();
                        
                    // Tarif WMS
                    $tarifWMS = TWMS::where('size', $size)->where('type', $ctr_type)->first();
                    // Paket PLP

                        $etd = Carbon::parse($form->etd);
                        $masuk = Carbon::parse($form->eta);
                        $jumlahHariWMS = $masuk->diffInDays($etd);

                        $tarifDasarMassa = ($tarifWMS->tarif_dasar_massa * $tarifWMS->massa)/100;
                        $totalMassaWMS = $tarifDasarMassa*$jumlah * $jumlahHariWMS;
                            # code...
                            Detil::create([
                                'form_id' => $form->id,
                                'invoice_id' => $header->id,
                                'tps' => 'Depo',
                                'keterangan' => 'Penumpukan (' . $size . ' / ' .$ctr_type.' ) Masuk pd ' . $masuk->format('Y-m-d'),
                                'size' => $size,
                                'type' => $ctr_type,
                                'tarif_dasar' => $tarifWMS->tarif_dasar_massa,
                                'satuan' => '0',
                                'jumlah' => $jumlah,
                                'jumlah_hari' => $jumlahHariWMS,
                                'total' => $totalMassaWMS,
                            ]);
                }
            }
            
            return redirect('/invoiceFCL/invoice/index')->with('status', ['type'=> 'success', 'message' => 'Data Berhasil di Simpan']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=> 'error', 'message' => 'Something gone wrong : ' . $th->getMessage()]);
            //throw $th;
        }
        
    }

    private function getNextOrderNo()
    {
        $latestOrder = Header::orderBy('proforma_no', 'desc')->first();

        if ($latestOrder) {
            // Ambil angka terakhir setelah "FCL-"
            $latestNumber = intval(str_replace('FCL-', '', $latestOrder->proforma_no));
            $nextOrderNo = $latestNumber + 1;
        } else {
            $nextOrderNo = 1;
        }

        // Format dengan leading zeros (15 digit)
        return 'FCL-' . str_pad($nextOrderNo, 15, '0', STR_PAD_LEFT);
    }
}
