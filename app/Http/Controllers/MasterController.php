<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\customerExcel;
use App\Imports\consolidatorExcel;
use App\Imports\EsealExcel;
use App\Imports\negaraExcel;
use App\Imports\packingExcel;
use App\Imports\perusahaanExcel;
use App\Imports\ppjkExcel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Customer;
use App\Models\Consolidator;
use App\Models\Eseal;
use App\Models\Negara;
use App\Models\Packing;
use App\Models\Perusahaan;
use App\Models\PPJK;
use App\Models\Gudang;
use App\Models\DepoMty;
use App\Models\Pelabuhan;
use App\Models\LokasiSandar as LS;
use App\Models\Vessel;
use App\Models\ShippingLine as SL;
use App\Models\PlacementManifest as PM;
use App\Models\RackTier as RT;
use App\Models\YardDesign as YD;
use App\Models\YardDetil as RowTier;
use App\Models\JobOrder as Job;
use App\Models\KapasitasGudang as KG;
use App\Models\KeteranganPhoto as Photo;

use Auth;
use Carbon\Carbon;

class MasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // customer
    public function CustomerIndex()
    {
        $data['title'] = 'Master Customer';
        $data['customers'] = Customer::get();

        return view('master.customer.index', $data);
    }

    public function CustomerExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv|max:2048',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->getPathName();

        try {
            if (in_array($extension, ['xls', 'xlsx'])) {
                Excel::import(new customerExcel, $path, null, ucfirst($extension));
            } else {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Unsupported file extension.']);
            }
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully imported.']);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
        }
    }

    public function CustomerPost(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'npwp' => 'required',
        ]);
        
        try {
            $customer = Customer::where('name', $request->name)
            ->where('email', $request->email)
            ->where('alamat', $request->alamat)
            ->where('code', $request->code)
            ->where('fax', $request->fax)
            ->where('phone', $request->phone)
            ->where('npwp', $request->npwp)
            ->first();
            if (!$customer) {
                $newCust = Customer::create([
                    'name'=>$request->name,
                    'code'=>$request->code,
                    'alamat'=>$request->alamat,
                    'npwp'=>$request->npwp,
                    'email'=>$request->email,
                    'fax'=>$request->fax,
                    'phone'=>$request->phone,
                ]);
                return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully added.']);
            }else {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Data customer sudah ada.']);
            }
            
        } catch (\Throwable $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
        }
    }

    public function CustomerDelete($id)
    {
        $customer = Customer::where('id', $id)->first();
        if ($customer) {
            $customer->delete();
            return response()->json(['success' => 'Customer deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }

    public function CustomerEdit($id)
    {
        $customer = Customer::where('id', $id)->first();
        if ($customer) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $customer,
            ]);
        }
    }

    public function CustomerUpdate(Request $request)
    {
        $customer = Customer::where('id', $request->id)->first();
        if ($customer) {
            $customer->update([
                'name'=>$request->name,
                'code'=>$request->code,
                'alamat'=>$request->alamat,
                'npwp'=>$request->npwp,
                'email'=>$request->email,
                'fax'=>$request->fax,
                'phone'=>$request->phone,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully added.']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
    }


    // Consolidator
    public function consolidatorIndex()
    {
        $data['title'] = 'Consolidator Menu';
        $data['consolidators'] = Consolidator::get();

        return view('master.consolidator.index', $data);
    }

    public function consolidatorExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv|max:2048',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->getPathName();

        try {
            if (in_array($extension, ['xls', 'xlsx'])) {
                Excel::import(new consolidatorExcel, $path, null, ucfirst($extension));
            } else {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Unsupported file extension.']);
            }
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully imported.']);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
        }
    }

    public function consolidatorPost(Request $request)
    {
        $uid = Auth::id();
        try {
            $cons = Consolidator::create([
                'namaconsolidator'=>$request->namaconsolidator,
                'code'=>$request->code,
                'notelp'=>$request->notelp,
                'contactperson'=>$request->contactperson,
                'nocano'=>$request->nocano,
                'tglakhirkontrak'=>$request->tglakhirkontrak,
                'kontrak'=>$request->kontrak,
                'uid'=>$uid,
                'npwp'=>$request->npwp,
                'ppn'=>$request->ppn,
                'materai'=>$request->materai,
                'nppkp'=>$request->nppkp,
                'keterangan'=>$request->keterangan,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully imported.']);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
        }
    }

    public function consolidatorEdit($id)
    {
        $consolidator = Consolidator::where('id', $id)->first();
        if ($consolidator) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $consolidator,
            ]);
        }
    }

    public function consolidatorUpdate(Request $request)
    {
        $uid = Auth::id();
        $cons = Consolidator::where('id', $request->id)->first();
        if ($cons) {
            $cons->update([
                'namaconsolidator'=>$request->namaconsolidator,
                'code'=>$request->code,
                'notelp'=>$request->notelp,
                'contactperson'=>$request->contactperson,
                'nocano'=>$request->nocano,
                'tglakhirkontrak'=>$request->tglakhirkontrak,
                'kontrak'=>$request->kontrak,
                'uid'=>$uid,
                'npwp'=>$request->npwp,
                'ppn'=>$request->ppn,
                'materai'=>$request->materai,
                'nppkp'=>$request->nppkp,
                'keterangan'=>$request->keterangan,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully update.']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Somethings Wrong, call the admin.']);
        }
    }

    public function consolidatorDelete($id)
    {
        $consolidator = Consolidator::where('id', $id)->first();
        if ($consolidator) {
            $consolidator->delete();
            return response()->json(['success' => 'consolidator deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }


    // Eseal
    public function esealIndex()
    {
        $data['title'] = 'Master E-Seal';
        $data['seals'] = Eseal::get();

        return view('master.eseal.index', $data);
    }

    public function esealExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv|max:2048',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->getPathName();

        try {
            if (in_array($extension, ['xls', 'xlsx'])) {
                Excel::import(new EsealExcel, $path, null, ucfirst($extension));
            } else {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Unsupported file extension.']);
            }
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully imported.']);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
        }
    }

    public function esealPost(Request $request)
    {
        try {
            $oldSeal = Eseal::where('code', $request->code)->first();
            if (!$oldSeal) {
                $seal = Eseal::create([
                    'code'=>$request->code,
                    'keterangan'=>$request->keterangan,
                    'uid'=>Auth::user()->id,
                ]);
                return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully imported.']);
            } else {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Data sudah ada.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
        }
    }

    public function esealEdit($id)
    {
        $eseal = Eseal::where('id', $id)->first();
        if ($eseal) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $eseal,
            ]);
        }
    }

    public function esealUpdate(Request $request)
    {
        $eseal = Eseal::where('id', $request->id)->first();
        if ($eseal) {
            $eseal->update([
                'code'=>$request->code,
                'keterangan'=>$request->keterangan,
                'uid'=>Auth::user()->id,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully update.']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Something wrong.']);
        }
    }

    public function esealDelete($id)
    {
        $seal = Eseal::where('id', $id)->first();
        if ($seal) {
            $seal->delete();
            return response()->json(['success' => 'seal deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }

    // Negara
    public function negaraIndex()
    {
        $data['title'] = "Master Negara";
        $data ['countries'] = Negara::orderBy('code', 'asc')->get();

        return view('master.negara.index', $data);
    }

    public function negaraExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv|max:2048',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->getPathName();

        try {
            if (in_array($extension, ['xls', 'xlsx'])) {
                Excel::import(new negaraExcel, $path, null, ucfirst($extension));
            } else {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Unsupported file extension.']);
            }
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully imported.']);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
        }
    }

    public function negaraPost(Request $request)
    {
        $nameCheck = Negara::where('name', $request->name)->first();
        $codeCheck = Negara::where('code', $request->code)->first();

        if (!$nameCheck || !$codeCheck) {
            $negara =  Negara::create([
                'name' => $request->name,
                'code' => $request->code,
                'uid' => Auth::user()->name,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully stored.']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Code and Name could not be same.']);
        }
    }

    public function negaraEdit($id)
    {
        $negara = Negara::where('id', $id)->first();
        if ($negara) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $negara,
            ]);
        }
    }

    public function negaraUpdate(Request $request)
    {
        $negara = Negara::where('id', $request->id)->first();

        if (!$negara) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Negara not found.']);
        }

        $nameCheck = Negara::where('id', '!=', $request->id)->where('name', $request->name)->first();
        $codeCheck = Negara::where('id', '!=', $request->id)->where('code', $request->code)->first();

        if ($nameCheck || $codeCheck) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Code and Name must be unique.']);
        }

        $negara->update([
            'name' => $request->name,
            'code' => $request->code,
            'uid' => Auth::user()->name,
        ]);

        return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully updated.']);
    }

    public function negaraDelete($id)
    {
        $negara = Negara::where('id', $id)->first();
        if ($negara) {
            $negara->delete();
            return response()->json(['success' => 'negara deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }

    // Packing
    public function packingIndex()
    {
        $data['title'] = "Master Packing";
        $data ['packs'] = Packing::orderBy('code', 'asc')->get();

        return view('master.packing.index', $data);
    }

    public function packingExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv|max:2048',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->getPathName();

        try {
            if (in_array($extension, ['xls', 'xlsx'])) {
                Excel::import(new packingExcel, $path, null, ucfirst($extension));
            } else {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Unsupported file extension.']);
            }
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully imported.']);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
        }
    }

    public function packingPost(Request $request)
    {
        $nameCheck = Packing::where('name', $request->name)->first();
        $codeCheck = Packing::where('code', $request->code)->first();

        if (!$nameCheck || !$codeCheck) {
            $packing =  Packing::create([
                'name' => $request->name,
                'code' => $request->code,
                'uid' => Auth::user()->name,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully stored.']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Code and Name could not be same.']);
        }
    }

    public function packingEdit($id)
    {
        $packing = Packing::where('id', $id)->first();
        if ($packing) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $packing,
            ]);
        }
    }

    public function packingUpdate(Request $request)
    {
        $packing = Packing::where('id', $request->id)->first();

        if (!$packing) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Negara not found.']);
        }

        $nameCheck = Packing::where('id', '!=', $request->id)->where('name', $request->name)->first();
        $codeCheck = Packing::where('id', '!=', $request->id)->where('code', $request->code)->first();

        if ($nameCheck || $codeCheck) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Code and Name must be unique.']);
        }

        $packing->update([
            'name' => $request->name,
            'code' => $request->code,
            'uid' => Auth::user()->name,
        ]);

        return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully updated.']);
    }

    public function packingDelete($id)
    {
        $packing = Packing::where('id', $id)->first();
        if ($packing) {
            $packing->delete();
            return response()->json(['success' => 'neagra deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }

    // Perusahaan
    public function perusahaanIndex()
    {
        $data['title'] = "Master Perusahaan";
        $data ['perusahaan'] = Perusahaan::orderBy('name', 'asc')->get();

        return view('master.perusahaan.index', $data);
    }

    public function perusahaanExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv|max:2048',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->getPathName();

        try {
            if (in_array($extension, ['xls', 'xlsx'])) {
                Excel::import(new perusahaanExcel, $path, null, ucfirst($extension));
            } else {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Unsupported file extension.']);
            }
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully imported.']);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
        }
    }

    public function perusahaanPost(Request $request)
    {
        $nameCheck = Perusahaan::where('name', $request->name)->first();

        if (!$nameCheck) {
            $perusahaan =  Perusahaan::create([
                'name' => $request->name,
                'alamat' => $request->alamat,
                'kota' => $request->kota,
                'phone' => $request->phone,
                'fax' => $request->fax,
                'email' => $request->email,
                'cp' => $request->cp,
                'roles' => $request->roles,
                'npwp' => $request->npwp,
                'ppn' => $request->ppn,
                'materai' => $request->materai,
                'nppkp' => $request->nppkp,
                'uid' => Auth::user()->id,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully stored.']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Code and Name could not be same.']);
        }
    }

    public function perusahaanEdit($id)
    {
        $perusahaan = Perusahaan::where('id', $id)->first();
        if ($perusahaan) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $perusahaan,
            ]);
        }
    }

    public function perusahaanUpdate(Request $request)
    {
        $perusahaan = Perusahaan::where('id', $request->id)->first();

        if (!$perusahaan) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Negara not found.']);
        }

        $nameCheck = Perusahaan::where('id', '!=', $request->id)->where('name', $request->name)->first();

        if ($nameCheck) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Code and Name must be unique.']);
        }

        $perusahaan->update([
            'name' => $request->name,
            'alamat' => $request->alamat,
            'kota' => $request->kota,
            'phone' => $request->phone,
            'fax' => $request->fax,
            'email' => $request->email,
            'cp' => $request->cp,
            'roles' => $request->roles,
            'npwp' => $request->npwp,
            'ppn' => $request->ppn,
            'materai' => $request->materai,
            'nppkp' => $request->nppkp,
            'uid' => Auth::user()->name,
        ]);

        return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully updated.']);
    }

    public function perusahaanDelete($id)
    {
        $perusahaan = Perusahaan::where('id', $id)->first();
        if ($perusahaan) {
            $perusahaan->delete();
            return response()->json(['success' => 'Perusahaan deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }

    // PPJK
    public function ppjkIndex()
    {
        $data['title'] = "Master PPJK";
        $data ['ppjks'] = PPJK::orderBy('name', 'asc')->get();

        return view('master.PPJK.index', $data);
    }

    public function ppjkExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv|max:2048',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->getPathName();

        try {
            if (in_array($extension, ['xls', 'xlsx'])) {
                Excel::import(new ppjkExcel, $path, null, ucfirst($extension));
            } else {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Unsupported file extension.']);
            }
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully imported.']);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
        }
    }

    public function ppjkPost(Request $request)
    {
        $nameCheck = PPJK::where('name', $request->name)->first();

        if (!$nameCheck) {
            $ppjk =  PPJK::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'uid' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully stored.']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Code and Name could not be same.']);
        }
    }

    public function ppjkEdit($id)
    {
        $ppjk = PPJK::where('id', $id)->first();
        if ($ppjk) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $ppjk,
            ]);
        }
    }

    public function ppjkUpdate(Request $request)
    {
        $ppjk = PPJK::where('id', $request->id)->first();

        if (!$ppjk) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Negara not found.']);
        }

        $nameCheck = PPJK::where('id', '!=', $request->id)->where('name', $request->name)->first();

        if ($nameCheck) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Code and Name must be unique.']);
        }

        $ppjk->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'uid' => Auth::user()->id,
        ]);

        return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully updated.']);
    }

    public function ppjkDelete($id)
    {
        $ppjk = PPJK::where('id', $id)->first();
        if ($ppjk) {
            $ppjk->delete();
            return response()->json(['success' => 'neagra deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }

    // Gudang
    public function gudangIndex()
    {
        $data['title'] = "Master Gudang";
        $data['gudangs'] = Gudang::get();

        return view('master.gudang.index', $data);
    }

    public function gudangPost(Request $request)
    {
        $nameCheck = Gudang::where('nama_gudang', $request->nama_gudang)->first();

        if (!$nameCheck) {
            $ppjk =  Gudang::create([
                'nama_gudang' => $request->nama_gudang,
                'kode_kantor' => $request->kode_kantor,
                'kode_gudang' => $request->kode_gudang,
                'uid' => Auth::user()->id,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully stored.']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Code and Name could not be same.']);
        }
    }

    public function gudangEdit($id)
    {
        $gudang = Gudang::where('id', $id)->first();
        if ($gudang) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $gudang,
            ]);
        }
    }

    public function gudangUpdate(Request $request)
    {
        $gudang = Gudang::where('id', $request->id)->first();

        if (!$gudang) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Gudang not found.']);
        }

        $nameCheck = Gudang::where('id', '!=', $request->id)->where('nama_gudang', $request->nama_gudang)->first();

        if ($nameCheck) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Code and Name must be unique.']);
        }

        $gudang->update([
            'nama_gudang' => $request->nama_gudang,
            'kode_kantor' => $request->kode_kantor,
            'kode_gudang' => $request->kode_gudang,
            'uid' => Auth::user()->id,
        ]);

        return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully updated.']);
    }

    public function gudangDelete($id)
    {
        $gudang = Gudang::where('id', $id)->first();
        if ($gudang) {
            $gudang->delete();
            return response()->json(['success' => 'neagra deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }

    // DepoMTY
    public function depoMTIndex()
    {
        $data['title'] = "Depo Empty";
        $data['depos'] = DepoMTy::get();

        return view('master.depoMT.index', $data);
    }

    public function depoMTPost(Request $request)
    {
        $nameCheck = DepoMty::where('name', $request->name)->first();

        if (!$nameCheck) {
            $depoMT =  DepoMty::create([
                'name' => $request->name,
                'uid' => Auth::user()->id,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully stored.']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Code and Name could not be same.']);
        }
    }

    public function depoMTEdit($id)
    {
        $depoMT = DepoMTy::where('id', $id)->first();
        if ($depoMT) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $depoMT,
            ]);
        }
    }

    public function depoMTUpdate(Request $request)
    {
        $depoMT = DepoMty::where('id', $request->id)->first();

        if (!$depoMT) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Gudang not found.']);
        }

        $nameCheck = DepoMty::where('id', '!=', $request->id)->where('name', $request->name)->first();

        if ($nameCheck) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Code and Name must be unique.']);
        }

        $depoMT->update([
            'name' => $request->name,
            'uid' => Auth::user()->id,
        ]);

        return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully updated.']);
    }

    public function depoMTDelete($id)
    {
        $depoMT = DepoMty::where('id', $id)->first();
        if ($depoMT) {
            $depoMT->delete();
            return response()->json(['success' => 'neagra deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }

    public function pelabuhanIndex()
    {
        $data['title'] = "Master Pelabuhan";
        $data['pelabuhans'] = Pelabuhan::get();
        $data['negaras'] = Negara::orderBy('name', 'asc')->get();

        return view('master.pelabuhan.index', $data);
    }

    public function pelabuhanPost(Request $request)
    {
        $old = Pelabuhan::where('name', $request->name)->where('kode', $request->kode)->first();
        if ($old) {
            return redirect()->back()->with('status', ['type' => 'error', 'message'=>'Data sudah tersedia, harap masukan data lain']);
        }

        $pelabuhan = Pelabuhan::create([
            'name' => $request->name,
            'kode' => $request->kode,
            'jenis' => $request->jenis,
            'negara_id' => $request->negara_id,
            'uid' => Auth::user()->id,
        ]);

        return redirect()->back()->with('status', ['type' => 'success', 'message'=>'Data berhasil dibuat']);
    }

    public function pelabuhanEdit($id)
    {
        $pelabuhan = Pelabuhan::where('id', $id)->first();
        if ($pelabuhan) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $pelabuhan,
            ]);
        }
    }
    
    public function pelabuhanUpdate(Request $request)
    {
        $old = Pelabuhan::whereNot('id', $request->id)->where('name', $request->name)->where('kode', $request->kode)->first();
        if ($old) {
            return redirect()->back()->with('status', ['type' => 'error', 'message'=>'Data sudah tersedia, harap masukan data lain']);
        }

        $pelabuhan = Pelabuhan::where('id', $request->id)->first();
        if ($pelabuhan) {
            $pelabuhan->update([
                'name' => $request->name,
                'kode' => $request->kode,
                'jenis' => $request->jenis,
                'negara_id' => $request->negara_id,
                'uid' => Auth::user()->id,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message'=>'Data berhasil di update']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message'=>'Something Wrong']);
        }
    }

    public function pelabuhanDelete($id)
    {
        $pelabuhan = Pelabuhan::where('id', $id)->first();
        if ($pelabuhan) {
            $pelabuhan->delete();
            return response()->json(['success' => 'pelabuhan deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }

    public function lokasiSandarIndex()
    {
        $data['title'] = 'Master Data Lokasi Sandar';
        $data['loks'] = LS::get();
        $data['perusahaans'] = Perusahaan::get();
        $data['negaras'] = Negara::orderBy('name', 'asc')->get();
        $data['pelabuhans'] = Pelabuhan::get();

        return view('master.lokasiSandar.index', $data);
    }

    public function lokasiSandarPost(Request $request)
    {
        $lokasiSandar = LS::create([
            'name'=>$request->name,
            'kd_tps_asal'=>$request->kd_tps_asal,
            'jabatan'=>$request->jabatan,
            'perusahaan_id'=>$request->perusahaan_id,
            'pelabuhan_id'=>$request->pelabuhan_id,
            'kota'=>$request->kota,
            'negara_id'=>$request->negara_id,
            'uid'=> Auth::user()->id,
        ]);
        return redirect()->back()->with('status', ['type' => 'success', 'message'=>'Data berhasil di buat']);
    }
    
    public function lokasiSandarEdit($id)
    {
        $lokasiSandar = LS::where('id', $id)->first();
        if ($lokasiSandar) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $lokasiSandar,
            ]);
        }
    }

    public function lokasiSandarUpdate(Request $request)
    {
        $lokasiSandar = LS::where('id', $request->id)->first();
        if ($lokasiSandar) {
            $lokasiSandar->update([
                'name'=>$request->name,
                'kd_tps_asal'=>$request->kd_tps_asal,
                'jabatan'=>$request->jabatan,
                'perusahaan_id'=>$request->perusahaan_id,
                'pelabuhan_id'=>$request->pelabuhan_id,
                'kota'=>$request->kota,
                'negara_id'=>$request->negara_id,
                'uid'=> Auth::user()->id,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message'=>'Data berhasil di Update']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message'=>'Something Wrong']);
        }
    }

    public function lokasiSandarDelete($id)
    {
        $lokasiSandar = LS::where('id', $id)->first();
        if ($lokasiSandar) {
            $lokasiSandar->delete();
            return response()->json(['success' => 'Lokasi Sandar deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }

    public function vesIndex()
    {
        $data['title'] = "Master Data Kapal";
        $data['vessel'] = Vessel::get();
        $data['negaras'] = Negara::orderBy('name', 'asc')->get();

        return view('master.vessel.index', $data);
    }

    public function vesPost(Request $request)
    {
        $old = Vessel::where('name', $request->name)->where('code', $request->code)->first();
        if ($old) {
            return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Data sudah tersedia, masukkan data baru']);
        }

        $ves = Vessel::create([
            'name'=>$request->name,
            'code'=>$request->code,
            'call_sign'=>$request->call_sign,
            'negara_id'=>$request->negara_id,
            'uid'=> Auth::user()->id,
        ]);
        return redirect()->back()->with('status', ['type'=>'success', 'message' => 'Data berhasil di upload']);
    }

    public function vesEdit($id)
    {
        $ves = Vessel::where('id', $id)->first();
        if ($ves) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $ves,
            ]);
        }
    }

    public function vesUpdate(Request $request)
    {
        $old = Vessel::where('id', $request->id)->where('name', $request->name)->where('code', $request->code)->first();
        if ($old) {
            return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Data sudah tersedia, masukkan data baru']);
        }

        $ves = Vessel::where('id', $request->id)->first();
        if ($ves) {
            $ves->update([
                'name'=>$request->name,
                'code'=>$request->code,
                'call_sign'=>$request->call_sign,
                'negara_id'=>$request->negara_id,
                'uid'=> Auth::user()->id,
            ]);
            return redirect()->back()->with('status', ['type'=>'success', 'message' => 'Data berhasil di update']);
        }else {
            return redirect()->back()->with('status', ['type'=>'error', 'message' => 'Something Wrong']);
        }
    }

    public function vesDelete($id)
    {
        $ves = Vessel::where('id', $id)->first();
        if ($ves) {
            $ves->delete();
            return response()->json(['success' => 'Lokasi Sandar deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }

    public function shippingLinesIndex()
    {
        $data['title'] = 'Master Data Shipping Lines';
        $data['ships'] = SL::get();
        $data['vessel'] = Vessel::get();

        return view('master.shippingLines.index', $data);
    }

    public function shippingLinesPost(Request $request)
    {
        try {
            $ship = SL::create([
                'shipping_line'=>$request->shipping_line,
                'vessel_id'=>$request->vessel_id,
                'email'=>$request->email,
                'cc'=>$request->contact,
                'keterangan'=>$request->keterangan,
                'uid'=> Auth::user()->id,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data berhasil du Update']);
        } catch (\Throwable $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);

        }
    }

    public function shippingLinesEdit($id)
    {
        $ship = SL::where('id', $id)->first();
        if ($ship) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $ship,
            ]);
        }
    }

    public function shippingLinesUpdate(Request $request)
    {
        $ship = SL::where('id', $request->id)->first();
        if ($ship) {
           $ship->update([
                'shipping_line'=>$request->shipping_line,
                'vessel_id'=>$request->vessel_id,
                'email'=>$request->email,
                'contact'=>$request->contact,
                'keterangan'=>$request->keterangan,
                'uid'=> Auth::user()->id,
           ]);
           return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di Update']);
        }else {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Something Wrong']);
        }
    }

    public function shippingLinesDelete($id)
    {
        $ship = SL::where('id', $id)->first();
        if ($ship) {
            $ship->delete();
            return response()->json(['success' => 'Lokasi Sandar deleted successfully']);
        }else {
            return response()->json(['error' => 'Something Wrong']);
        }
    }

    public function placementManifestIndex()
    {
        $data['title'] = 'Master Rack';
        $data['kg'] = KG::find(1);
        $data['gudang'] = PM::orderBy('nomor', 'asc')->get();
        // dd($data['gudang']->nomor);

        return view('master.placementManifest.index', $data);
    }

    public function kapasitasGudang(Request $request)
    {
        $kg = KG::find(1);
        $kg->update([
            'kapasitas'=> $request->kapasitas,
        ]);

        return redirect()->back()->with('success', 'Kapasitas updated successfully!');
    }

    public function pmCreateIndex()
    {
        $data['title'] = 'Master Rack || Create Index';
        $data['gudang'] = PM::orderBy('nomor', 'asc')->get();


        return view('master.placementManifest.create', $data);
    }

    public function pmUpdateGrid(Request $request)
    {
        $selectedGrids = json_decode($request->input('selected_grids'), true);
        $names = $request->input('name'); // This will retrieve the name array
        // dd($request->tier);

        // dd($names, $selectedGrids);
        if ($request->use_for === 'N') {
            // Update all selected grids to have null values for name and use_for
            foreach ($selectedGrids as $gridId => $gridName) {
                $grid = PM::find($gridId);
                if ($grid) {
                    $grid->update([
                        'name' => null,
                        'use_for' => null,
                    ]);

                    $tier = RT::where('rack_id', $grid->id)->delete();
                }
            }
        } else {
            // Update grid items based on the provided names and use_for
            foreach ($selectedGrids as $gridId => $gridName) {
                $grid = PM::find($gridId);
                if ($grid) {
                    $grid->update([
                        'name' => $names[$gridId] ?? null,
                        'use_for' => $request->use_for,
                    ]);
                    $oldTier = RT::where('tier', '>', $request->tier)->delete();

                    for ($i=1; $i <= $request->tier ; $i++) { 
                        $tier = RT::where('rack_id', $grid->id)->where('tier', $i)->first();
                        if (!$tier) {
                            $newTier = RT::create([
                                'rack_id' => $grid->id, 
                                'tier' => $i, 
                            ]);
                        }
                    }                    
                }
            }
        }
    
        return redirect()->back()->with('success', 'Grid updated successfully!');
    }

    public function pmCreateBarcode(Request $request)
    {
        $selectedGrids = $request->input('selected_grids'); // Tidak perlu explode jika data sudah berbentuk array
        $items = PM::whereIn('id', $selectedGrids)->get();

        foreach ($items as $item) {
            do {
                $uniqueBarcode = Str::random(19);
                } while (PM::where('barcode', $uniqueBarcode)->exists());
            $item->update([
                'barcode'=> $uniqueBarcode,
            ]);
            $tiers = RT::where('rack_id', $item->id)->get();
            foreach ($tiers as $tier) {
                $tier->update([
                    'barcode' => $item->barcode . $tier->tier,
                ]);
            }
        }

        $data['items'] = $items;

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Mencetak Barcode',
            'data' => $data,
        ]);
    }

    public function pmViewBarcode(Request $request)
    {
        $data['title'] = "Barcode Rack "; 
        $selectedGrids = $request->input('selected_grids');

        // Jika selected_grids masih dalam bentuk string, ubah menjadi array
        if (is_string($selectedGrids)) {
            $selectedGrids = explode(',', $selectedGrids);
        }
    
        // Query item berdasarkan selected grids
        $items = PM::whereIn('id', $selectedGrids)->get();
        $data['items'] = $items;
        // dd($selectedGrids);

        return view('master.placementManifest.barcode', $data);
    }

    public function tierView(Request $request)
    {
        $data['title'] = 'Rack Tier';
        $selectedGrids = $request->input('selected_grids');
        // dd($selectedGrids);

        // Jika selected_grids masih dalam bentuk string, ubah menjadi array
        if (is_string($selectedGrids)) {
            $selectedGrids = explode(',', $selectedGrids);
        }
    
        // Query item berdasarkan selected grids
        $items = PM::whereIn('id', $selectedGrids)->orderBy('name', 'asc')->get();
        // dd($selectedGrids,$items);
        $data['tiers'] = RT::whereIn('rack_id', $selectedGrids)->orderBy('tier', 'desc')->get();
        $data['items'] = $items;
        // dd($selectedGrids);

        return view('master.placementManifest.tier', $data);

    }

    public function yardIndex()
    {
        $data['title'] = 'Master Yard';
        $data['yard'] = YD::get();

        return view('master.yard.index', $data);
    }

    public function yardDetail($id)
    {
        $yard = YD::where('id', $id)->first();
        if ($yard) {
            $route = '/master/yard-view'.$yard->id;
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $yard,
                'route'    => $route,
            ]);
        }
    }

    public function yardUpdate(Request $request)
    {
        $yard = YD::where('id', $request->id)->first();
        if ($yard) {
            try {
                $yard->update([
                    'yard_block' => $request->yard_block,
                    'max_slot' => $request->max_slot,
                    'max_row' => $request->max_row,
                    'max_tier' => $request->max_tier,
                ]);
                $yardRot = RowTier::where('yard_id', $request->id)->get();
                if ($yardRot) {
                    foreach ($yardRot as $detil) {
                        $detil->delete();
                    }
                }
                $i=0;
                for($i = 1; $i <= $yard->max_slot; $i++)
                {
                   $r=0;  
                  for($r = 1; $r <= $yard->max_row; $r++)
                     {     
                         $t=0;
                         for($t = 1; $t <= $yard->max_tier; $t++)
                         {                  
                          $block = RowTier::create([
                             'yard_id'=> $yard->id,
                             'slot'=> $i,
                             'row'=> $r,
                             'tier'=> $t,
                             'active' => 'N', 
                            ]);
                         }        
                     }
                 }
                return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully imported.']);
            } catch (\Throwable $e) {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
            }
        }
    }

    public function yardReset(Request $request)
    {
        $yard = YD::where('id', $request->id)->first();
        if ($yard) {
            try {
                $yard->update([
                    'yard_block' => null,
                    'max_slot' => null,
                    'max_row' => null,
                    'max_tier' => null,
                ]);
                $yardRot = RowTier::where('yard_id', $request->id)->get();
                if ($yardRot) {
                    foreach ($yardRot as $detil) {
                        $detil->delete();
                    }
                }
               return response()->json([
                'success' => true,
                'message' => 'Yard Hasbeen Reset',
               ]);
            } catch (\Throwable $e) {
                // return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error importing data: ' . $e->getMessage(),
                   ]);
            }
        }
    }

    public function yardView($id)
    {
        $yard = YD::where('id', $id)->first();
        $data['title'] = 'Detil Yard || ' . $yard->yard_block;
        $data['yard'] = $yard;
        $data['rowTiers'] = RowTier::where('yard_id', $id)->get();
        $data['slots'] = RowTier::where('yard_id', $id)->pluck('slot')->unique();

        return view('master.yard.detil', $data);
    }

    public function rowTierView(Request $request)
    {
        $slot = $request->input('slot');
        $rowTiers = RowTier::with('cont')->where('yard_id', $request->yard_id)->where('slot', $slot)->get();
        // $job = Job
        // var_dump($request->yard_id);
        // die;
        return response()->json($rowTiers);
    }

    public function photoIndex()
    {
        $data['title'] = 'Keterangan Photo';
        $data['photos'] = Photo::all();

        return view('master.photo.index', $data);
    }

    public function photoPost(Request $request)
    {
        try {
            if ($request->has('id')) {
                $photo = Photo::findOrFail($request->id);
                $photo->update([
                    'tipe' => $request->tipe, 
                    'kegiatan' => $request->kegiatan, 
                    'keterangan' => $request->keterangan, 
                ]);
            }else {
                $photo = Photo::create([
                    'tipe' => $request->tipe, 
                    'kegiatan' => $request->kegiatan, 
                    'keterangan' => $request->keterangan, 
                ]);
            }

            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully imported.']);
        } catch (\Throwable $th) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Something Wrong: '. $th->getMessage()]);
        }
    }

    public function photoData($id)
    {
        $photo = Photo::findOrFail($id);

        if ($photo) {
            return response()->json([
                'success' => true,
                'data' => $photo,
            ]);
        }
    }
}
