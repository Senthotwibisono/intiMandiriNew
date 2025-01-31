<?php

namespace App\Http\Controllers\invoiceFCL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;

use App\Models\Customer;
use App\Models\ContainerFCL as ContF;
use App\Models\FCL\FormContainerFCL as FormC;
use App\Models\FCL\FormFCL as Form;

class FormFCLController extends Controller
{
    public function indexStep1()
    {
        $data['title'] = 'Create Invoice FCL - Step 1';
        $data['customers'] = Customer::get();

        return view('invoiceFCL.form.step1', $data);
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

    public function getBLData($bl)
    {
        try {
            $cont = ContF::whereNull('tglkeluar')->where('nobl', $bl)->get();
            if (!$cont) {
                return response()->json([
                    'success'=> false,
                    'message'=> 'Tidak ada container yang dapat dipilih !!',
                ]);
            }
            $dateBL = ContF::where('nobl', $bl)->value('tgl_bl_awb');
            $custId = ContF::where('nobl', $bl)->value('cust_id');
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
            $etaValues = $cont->pluck('eta')->unique();
            if ($etaValues->count() > 1) {
                return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Terdapat nilai ETA yang berbeda.']);
            }

            $checkMasuk = $cont->pluck('tglmasuk')->unique();
            if ($checkMasuk->count() > 1) {
                return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Terdapat nilai Tanggal Masuk yang berbeda.']);
            }

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
                ]);
            }

            return redirect()->back()->with('status', ['type'=>'success', 'message' => 'Data Berhasil Disimpan']);

        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Something Wrong: '. $th->getMessage()]);
        }
    }

    public function indexStep2($id)
    {
        $data['title'] = "Create Invoice FCL - Step 1";

        $data['form'] = Form::find($id);

        $data['containerInvoice'] = FormC::where('form_id', $id)->get();

        return view('invoiceFCL.form.step1', $data);
    }
}
