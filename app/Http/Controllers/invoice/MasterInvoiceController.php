<?php

namespace App\Http\Controllers\invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\MasterTarif as MT;

class MasterInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function tarifIndex()
    {
        $data['title'] = 'Master Tarif';
        $data['mtarif'] = MT::get();

        return view('invoice.master.tarif', $data);
    }

    public function tarifPost(Request $request)
    {
        try {
           $mt = MT::create([
                'kode_tarif'=>$request->kode_tarif,
                'nama_tarif'=>$request->nama_tarif,
                'tarif_dasar'=>$request->tarif_dasar,
                'jenis_storage'=>$request->jenis_storage,
                'day'=>$request->day,
                'period'=>$request->period,
                'created_by'=>Auth::user()->id,
                'created_at'=>Carbon::now(),
           ]);
           return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di Simpan!!']);
        } catch (\Throwable $e) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Something Wrong'. $e->getMessage()]);
        }
    }

    public function tarifEdit($id)
    {
        $tarif = MT::where('id', $id)->first();
        if ($tarif) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $tarif,
            ]);
        }
    }

    public function tarifUpdate(Request $request)
    {
        try {
            $tarif = MT::find($request->id);
            $tarif->update([
                'kode_tarif'=>$request->kode_tarif,
                'nama_tarif'=>$request->nama_tarif,
                'tarif_dasar'=>$request->tarif_dasar,
                'jenis_storage'=>$request->jenis_storage,
                'day'=>$request->day,
                'period'=>$request->period,
                'updated_by'=>Auth::user()->id,
            ]);
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di Simpan!!']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Something Wrong'. $th->getMessage()]);
        }
    }

    public function tarifDelete($id)
    {
        $tarif = MT::where('id', $id)->first();
        if ($tarif) {
            $tarif->delete();
            return response()->json(['success' => 'Lokasi Sandar deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }
}
