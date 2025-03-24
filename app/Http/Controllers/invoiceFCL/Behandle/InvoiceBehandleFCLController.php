<?php

namespace App\Http\Controllers\invoiceFCL\Behandle;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FCL\Behandle\Form;
use App\Models\ContainerFCL as Cont;
use Auth;
use Carbon\Carbon;

use DataTables;

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
            return '<button class="btn btn-danger"><i class="fas fa-trash"></i></button>';
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

        return view('invoiceFCL.behandle.form.step1', $data);
    }

    public function getContainer(Request $request)
    {
        // dd($request->all());
        
        $cont = Cont::where('no_spjm', $request->no_spjm)->where('tgl_spjm', $request->tgl_spjm)->get();

        if ($cont->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'Tidak ada container yang bisa di pilih'
            ]);
        }

    }
}
