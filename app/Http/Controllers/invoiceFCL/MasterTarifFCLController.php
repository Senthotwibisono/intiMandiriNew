<?php

namespace App\Http\Controllers\invoiceFCL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use DataTables;

use App\Models\LokasiSandar;
use App\Models\FCL\MTarifTPS as TTPS;
use App\Models\FCL\MTarifWMS as TWMS;

class MasterTarifFCLController extends Controller
{
    public function dataTableTPS(Request $request)
    {

        $tarif = TTPS::orderBy('lokasi_sandar_id', 'asc')->get();

        return DataTables::of($tarif)
        ->addColumn('edit', function($tarif){
            return '<button class="btn btn-warning editTarifTPS" id="editTarifTPS" data-id="'.$tarif->id.'"><i class="fa fa-pencil"></i></button>';
        })
        ->addColumn('lokasiSandar', function($tarif){
            return $tarif->LokasiSandar->kd_tps_asal ?? '-';
        })
        ->rawColumns(['edit'])
        ->make(true);

    }

    public function postTarifTPS(Request $request)
    {
        // dd($request->all());
        try {
            $oldTarif = TTPS::where('lokasi_sandar_id', $request->lokasi_sandar_id)->where('size', $request->size)->where('type', $request->type)->first();
            if ($oldTarif) {
                return redirect()->back()->with('status',['type'=>'error', 'message'=>'Data Sudah Tersedia, harap tinjau kembali']);
            }
            $tarif = TTPS::create([
                'lokasi_sandar_id' => $request->lokasi_sandar_id,
                'size' => $request->size,
                'type' => $request->type,
                'tarif_dasar_massa' => $request->tarif_dasar_massa,
                'massa2' => $request->massa2,
                'massa3' => $request->massa3,
                'lift_on' => $request->lift_on,
                'hyro_scan' => $request->hyro_scan,
                'perawatan_it' => $request->perawatan_it,
                'econ' => $request->econ,
                'gate_pass' => $request->gate_pass,
                'refeer' => $request->refeer,
                'monitoring' => $request->monitoring,
                'surcharge' => $request->surcharge,
                'admin' => $request->admin,
                'uid' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);

            return redirect()->back()->with('status',['type'=>'success', 'message'=>'Data Berhasil di Simpan']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status',['type'=>'error', 'message'=>'Something Wrong !! : ' . $th->getMessage()]);
        }
    }

    public function editTarifTPS($id)
    {
        try {
            $tarif = TTPS::find($id);
            return response()->json([
                'success' => true,
                'data' => $tarif,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something Wrong: ' . $th->getMessage(),
            ]);
        }
    }

    public function updateTarifTPS(Request $request)
    {
        try {
            $oldTarif = TTPS::whereNot('id', $request->id)->where('lokasi_sandar_id', $request->lokasi_sandar_id)->where('size', $request->size)->where('type', $request->type)->first();
            if ($oldTarif) {
                return redirect()->back()->with('status',['type'=>'error', 'message'=>'Data Sudah Tersedia, harap tinjau kembali']);
            }
            $tarif = TTPS::find($request->id);

            $tarif->update([
                'lokasi_sandar_id' => $request->lokasi_sandar_id,
                'size' => $request->size,
                'type' => $request->type,
                'tarif_dasar_massa' => $request->tarif_dasar_massa,
                'massa2' => $request->massa2,
                'massa3' => $request->massa3,
                'lift_on' => $request->lift_on,
                'hyro_scan' => $request->hyro_scan,
                'perawatan_it' => $request->perawatan_it,
                'econ' => $request->econ,
                'gate_pass' => $request->gate_pass,
                'refeer' => $request->refeer,
                'monitoring' => $request->monitoring,
                'surcharge' => $request->surcharge,
                'admin' => $request->admin,
                'uid' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
            return redirect()->back()->with('status',['type'=>'success', 'message'=>'Data Berhasil di Simpan']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status',['type'=>'error', 'message'=>'Something Wrong !! : ' . $th->getMessage()]);
        }
    }

    // Master Tarif WMS
    public function dataTableWMS(Request $request)
    {

        $tarif = TWMS::with(['user'])->get();

        return DataTables::of($tarif)
        ->addColumn('edit', function($tarif){
            return '<button class="btn btn-warning editTarifWMS" id="editTarifWMS" data-id="'.$tarif->id.'"><i class="fa fa-pencil"></i></button>';
        })
        ->rawColumns(['edit'])
        ->make(true);

    }

    public function postTarifWMS(Request $request)
    {
        try {
            $oldTarif = TWMS::where('size', $request->size)->where('type', $request->type)->first();
            if ($oldTarif) {
                return redirect()->back()->with('status',['type'=>'error', 'message'=>'Data Sudah Tersedia, harap tinjau kembali']);
            }
            $tarif = TWMS::create([
                'size' => $request->size,
                'type' => $request->type,
                'paket_plp' => $request->paket_plp,
                'behandle' => $request->behandle,
                'tarif_dasar_massa' => $request->tarif_dasar_massa,
                'massa' => $request->massa,
                'lift_on' => $request->lift_on,
                'lift_off' => $request->lift_off,
                'gate_pass' => $request->gate_pass,
                'refeer' => $request->refeer,
                'monitoring' => $request->monitoring,
                'surcharge' => $request->surcharge,
                'admin' => $request->admin,
                'admin_behandle' => $request->admin_behandle,
                'uid' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);

            return redirect()->back()->with('status',['type'=>'success', 'message'=>'Data Berhasil di Simpan']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status',['type'=>'error', 'message'=>'Something Wrong !! : ' . $th->getMessage()]);
        }
    }

    public function editTarifWMS($id)
    {
        try {
            $tarif = TWMS::find($id);
            // var_dump($id, $tarif);
            // die();
            return response()->json([
                'success' => true,
                'data' => $tarif,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Something Wrong: ' . $th->getMessage(),
            ]);
        }
    }

    public function updateTarifWMS(Request $request)
    {
        try {
            $oldTarif = TWMS::whereNot('id', $request->id)->where('size', $request->size)->where('type', $request->type)->first();
            if ($oldTarif) {
                return redirect()->back()->with('status',['type'=>'error', 'message'=>'Data Sudah Tersedia, harap tinjau kembali']);
            }
            $tarif = TWMS::find($request->id);

            $tarif->update([
                'size' => $request->size,
                'type' => $request->type,
                'paket_plp' => $request->paket_plp,
                'behandle' => $request->behandle,
                'tarif_dasar_massa' => $request->tarif_dasar_massa,
                'massa' => $request->massa,
                'lift_on' => $request->lift_on,
                'lift_off' => $request->lift_off,
                'gate_pass' => $request->gate_pass,
                'refeer' => $request->refeer,
                'monitoring' => $request->monitoring,
                'surcharge' => $request->surcharge,
                'admin' => $request->admin,
                'admin_behandle' => $request->admin_behandle,
                'uid' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
            return redirect()->back()->with('status',['type'=>'success', 'message'=>'Data Berhasil di Simpan']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status',['type'=>'error', 'message'=>'Something Wrong !! : ' . $th->getMessage()]);
        }
    }
}
