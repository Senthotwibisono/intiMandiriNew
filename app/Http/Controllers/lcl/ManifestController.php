<?php

namespace App\Http\Controllers\lcl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Imports\ManifestExcel;
use App\Imports\ManifestCont;
use App\Imports\ManifestMaster;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Container as Cont;
use App\Models\JobOrder as Job;
use App\Models\Manifest;
use App\Models\Customer;
use App\Models\Packing;
use App\Models\Item;

use App\Models\TempCont;
use App\Models\TempManifest;
use App\Models\TempBarang;

use PhpOffice\PhpSpreadsheet\IOFactory;


class ManifestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['title'] = "Import LCL - Register";
        $data['conts'] = Cont::where('type', '=', 'lcl')->where('tglkeluar', null )->get();
        
        return view('lcl.manifest.index', $data);
    }

    public function detail($id)
    {
        $cont = Cont::where('id', $id)->first();
        $data['title'] = 'Manifest ' .$cont->nocontainer;
        $data['cont'] = $cont;
        $data['manifest'] = Manifest::where('container_id', $id)->get();
        $data['custs'] = Customer::get();
        $data['packs'] = Packing::get();

        return view('lcl.manifest.detail', $data);
    }

    public function create(Request $request)
    {
        $cont = Cont::where('id', $request->container_id)->first();
        if ($cont) {
            $job = Job::where('id', $cont->joborder_id)->first();

            $lastTally = Manifest::where('joborder_id', $job->id)
                          ->orderBy('id', 'desc')
                          ->first();
        
            if ($lastTally) {
                $lastTallyNumber = intval(substr($lastTally->notally, 12, 3));
                $newTallyNumber = str_pad($lastTallyNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newTallyNumber = '001';
            }
            $noTally = $job->nojoborder . '-' . $newTallyNumber;
            // dd($noTally);

            do {
                $uniqueBarcode = Str::random(19);
            } while (Manifest::where('barcode', $uniqueBarcode)->exists());

            $manifest = Manifest::create([
                'notally'=>$noTally,
                'validasi' => 'N',
                'barcode' => $uniqueBarcode,
                'nohbl'=>$request->nohbl,
                'container_id'=>$request->container_id,
                'joborder_id'=>$job->id,
                'tgl_hbl'=>$request->tgl_hbl,
                'shipper_id'=>$request->shipper_id,
                'customer_id'=>$request->customer_id,
                'notifyparty_id'=>$request->notifyparty_id,
                'marking'=>$request->marking,
                'descofgoods'=>$request->descofgoods,
                'quantity'=>$request->quantity,
                'packing_id'=>$request->packing_id,
                'weight'=>$request->weight,
                'meas'=>$request->meas,
                'packing_tally'=>$request->packing_tally,
                'uid'=>Auth::user()->id,
            ]);

            for ($i = 1; $i < $manifest->quantity+1; $i++) {

                $item= Item::create([
                            'manifest_id'=>$manifest->id,
                            'barcode'=>$manifest->barcode . $i,
                            'nomor'=>$i,
                            'stripping' => 'N',
                            'uid'=> Auth::user()->id,
                        ]);
            }

            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data berhasil di buat']);
        }else {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Something Wrong']);
        }
    }

    public function edit($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        if ($manifest) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $manifest,
            ]);
        }
    }

    public function delete($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        if ($manifest) {
            $items = Item::where('manifest_id', $manifest->id)->get();
            foreach ($items as $item) {
                $item->delete();
            }
            $manifest->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil di hapus',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Something wrong !!',
            ]);
        }
    }

    public function update(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();
        if ($manifest) {
            if ($manifest->quantity != $request->quantity) {
                $items = Item::where('manifest_id', $request->id)->get();
                foreach ($items as $item) {
                    $item->delete();
                }
                for ($i = 1; $i < $request->quantity+1; $i++) {
                    $newItem= Item::create([
                                'manifest_id'=>$manifest->id,
                                'barcode'=>$manifest->barcode . $i,
                                'nomor'=>$i,
                                'stripping' => 'N',
                                'uid'=> Auth::user()->id,
                            ]);
                }
            }

            $manifest->update([                
                'nohbl'=>$request->nohbl,
                'container_id'=>$request->container_id,
                'tgl_hbl'=>$request->tgl_hbl,
                'shipper_id'=>$request->shipper_id,
                'customer_id'=>$request->customer_id,
                'notifyparty_id'=>$request->notifyparty_id,
                'marking'=>$request->marking,
                'descofgoods'=>$request->descofgoods,
                'quantity'=>$request->quantity,
                'packing_id'=>$request->packing_id,
                'weight'=>$request->weight,
                'meas'=>$request->meas,
                'packing_tally'=>$request->packing_tally,
            ]);

            return redirect()->back()->with('ststus', ['type'=>'success', 'message'=>'Data berhasil di update']);
        }else {
            return redirect()->back()->with('ststus', ['type'=>'error', 'message'=>'Oopss, Something wrong!!']);
        }
    }

    public function itemIndex($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        $data['title'] = "Detail LCL Import || Manifest - " . $manifest->notally;
        $data['items'] = Item::where('manifest_id', $id)->get();

        return view('lcl.manifest.item', $data);
    }

    public function itemUpdate(Request $request)
    {
        $items = $request->input('items');
        
        foreach ($items as $itemData) {
            $item = Item::find($itemData['id']);
            
            if ($item) {
                $item->name = $itemData['name'];
                $item->save();
            }
        }
        
        return redirect()->back()->with('success', 'Data berhasil di update');
    }

    public function approve($id){
        $manifest = Manifest::where('id', $id)->first();
        if ($manifest) {
            $manifest->update([
                'validasi'=>'Y',
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Validasi Berhasil',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Something wrong !!',
            ]);
        }
    }

    public function unapprove($id){
        $manifest = Manifest::where('id', $id)->first();
        if ($manifest) {
            $manifest->update([
                'validasi'=>'N',
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Manifest batal approve',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Something wrong !!',
            ]);
        }
    }

    public function barcodeIndex($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        $data['title'] = 'Barcode Packing LCL Manifest || ' . $manifest->notally;
        $data['items'] = Item::where('manifest_id', $manifest->id)->get();

        return view('lcl.manifest.barcode', $data);
    }

    public function excel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv|max:2048',
        ]);
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $path = $file->getPathName();

        try {
            if (in_array($extension, ['xls', 'xlsx'])) {
                $jobId = $request->job_id;
                $manifestExcel = new ManifestMaster($jobId);
                Excel::import($manifestExcel, $path, null, ucfirst($extension));
            } else {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Unsupported file extension.']);
            }

            $jobId = $request->job_id;
            $existingContainers = Cont::where('joborder_id', $jobId)->pluck('nocontainer')->toArray();
            $tempContainers = TempCont::pluck('nocontainer')->toArray();
            $newContainers = array_diff($tempContainers, $existingContainers);

            foreach ($newContainers as $nocontainer) {
                $tempContRecord = TempCont::where('nocontainer', $nocontainer)->first();
                if ($tempContRecord->size == '20') {
                    $teus = 1;
                }elseif ($tempContRecord->size == '40') {
                    $teus = 2;
                }else {
                    $teus = 0;
                }
                $newCont = Cont::create([
                    'nocontainer'=>$tempContRecord->nocontainer,
                    'type'=>'lcl',
                    'joborder_id'=> $jobId,
                    'size'=>$tempContRecord->size,
                    'teus'=>$teus,
                    'uid' => Auth::user()->id,
                ]);
            }
            $detils = TempManifest::get();
            // dd($detils);
            foreach ($detils as $detil) {
                 $tempCont = TempCont::where('detil_id', $detil->detil_id)->first();
                 $cont = Cont::where('nocontainer', $tempCont->nocontainer)->first();
                 $oldManifest = Manifest::where('nohbl', $detil->nohbl)->where('container_id', $cont->id)->first();
                //  dd($oldManifest, $cont->id);
                 $barang = TempBarang::where('detil_id', $detil->detil_id)->first();
                 if (!$oldManifest) {
                    $job = Job::where('id', $cont->joborder_id)->first();

                    $lastTally = Manifest::where('joborder_id', $job->id)
                                  ->orderBy('id', 'desc')
                                  ->first();

                    if ($lastTally) {
                        $lastTallyNumber = intval(substr($lastTally->notally, 12, 3));
                        $newTallyNumber = str_pad($lastTallyNumber + 1, 3, '0', STR_PAD_LEFT);
                    } else {
                        $newTallyNumber = '001';
                    }
                    $noTally = $job->nojoborder . '-' . $newTallyNumber;
                    // dd($noTally);
                
                    do {
                        $uniqueBarcode = Str::random(19);
                        } while (Manifest::where('barcode', $uniqueBarcode)->exists());
                    $newManifest = Manifest::create([
                        'notally'=>$noTally,
                        'validasi' => 'N',
                        'barcode' => $uniqueBarcode,
                        'nohbl'=>$detil->nohbl,
                        'container_id'=>$cont->id,
                        'joborder_id'=>$cont->joborder_id,
                        'tgl_hbl'=>$detil->tgl_hbl,
                        'shipper_id'=>$detil->shipper_id,
                        'customer_id'=>$detil->customer_id,
                        'notifyparty_id'=>$detil->notifyparty_id,
                        'marking'=>$detil->marking,
                        'descofgoods'=>$barang->descofgoods,
                        'quantity'=>$detil->quantity,
                        'packing_id'=>$detil->packing_id,
                        'weight'=>$detil->weight,
                        'meas'=>$detil->meas,
                        'packing_tally'=>$detil->packing_id,
                        'uid'=>Auth::user()->id,
                    ]);
                    for ($i = 1; $i < $newManifest->quantity+1; $i++) {
                        $item= Item::create([
                                    'manifest_id'=>$newManifest->id,
                                    'barcode'=>$newManifest->barcode . $i,
                                    'nomor'=>$i,
                                    'stripping' => 'N',
                                    'uid'=> Auth::user()->id,
                                ]);
                     }
                }
            }
            TempCont::truncate();
            TempBarang::truncate();
            TempManifest::truncate();
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data successfully imported.']);
        } catch (\Exception $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Error importing data: ' . $e->getMessage()]);
        }
        
        // $pathToFile = $request->file('file')->getPathname();
        // $sheetNames = $this->getSheetTitles($pathToFile);

        // dd($sheetNames);
    }

    public function getSheetTitles($pathToFile)
    {
        $spreadsheet = IOFactory::load($pathToFile);
        $sheetNames = $spreadsheet->getSheetNames();

        return $sheetNames;
    }

}
