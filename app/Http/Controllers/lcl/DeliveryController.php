<?php

namespace App\Http\Controllers\lcl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Container as Cont;
use App\Models\JobOrder as Job;
use App\Models\Manifest;
use App\Models\Eseal;
use App\Models\User;
use App\Models\Photo;
use App\Models\TpsSPJM as SPJM;
use App\Models\TpsSPPB as SPPB;
use App\Models\TpsSPPBCont as SPPBCont;
use App\Models\TpsSPPBKms as SPPBKms;
use App\Models\KodeDok as Kode;
use App\Models\TpsManual as Manual;
use App\Models\TpsManualCont as ManualCont;
use App\Models\TpsManualKms as ManualKms;
use App\Models\TpsSPPBBC23 as BC23;
use App\Models\TpsSPPBBC23Cont as BC23Cont;
use App\Models\TpsSPPBBC23Kms as BC23Kms;
use App\Models\TpsPabean as Pabean;
use App\Models\TpsPabeanCont as PabeanCont;
use App\Models\TpsPabeanKms as PabeanKms;
use App\Models\BarcodeGate as Barcode;
use App\Models\PlacementManifest as PM;
use App\Models\Item;
use App\Models\RackTier as RT;
use App\Models\InvoiceHeader as Header;
use App\Models\KeteranganPhoto as KP;

use DataTables;

class DeliveryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('manifestBarcode');
    }


    public function indexBehandle()
    {
        $data['title'] = 'Manifest Behandle';
        $data['locs'] = PM::where('use_for', 'B')->get();
        $data['kets'] = KP::where('tipe', 'Manifest')->where('kegiatan', '=', 'behandle')->get();

        return view('lcl.delivery.behandleIndex', $data);
    }

    public function behandleData(Request $request)
    {
        // var_dump($request->all());
        // die;
        switch ($request->filter) {
            case 'all':
                $manifest = Manifest::with(['shipperM', 'customer', 'packing', 'packingTally'])->whereNotNull('tglmasuk')->whereNull('tglbuangmty')->get();
                break;
            case 'behandled':
                $manifest = Manifest::with(['shipperM', 'customer', 'packing', 'packingTally'])->whereNotNull('status_behandle')->get();
                break;
            case 'ready':
                $manifest = Manifest::with(['shipperM', 'customer', 'packing', 'packingTally'])->where('status_behandle', '2')->get();
                break;
            case 'finish':
                $manifest = Manifest::with(['shipperM', 'customer', 'packing', 'packingTally'])->where('status_behandle', '3')->get();
                break;
            case 'proses':
                $manifest = Manifest::with(['shipperM', 'customer', 'packing', 'packingTally'])->where('status_behandle', '1')->get();
                break;
            default:
                $manifest = Manifest::with(['shipperM', 'customer', 'packing', 'packingTally'])->whereNotNull('tglmasuk')->whereNull('tglbuangmty')->get();
                break;
        }
        

        return DataTables::of($manifest)
        ->addColumn('edit', function($manifest){
            return '<button class="btn btn-warning editButton" data-id="'.$manifest->id.'"><i class="fa fa-pencil"></i></button>';
        })
        ->addColumn('detil', function($manifest){
            $herf = '/lcl/realisasi/behandle-detail';
            return '<a href="javascript:void(0)" onclick="openWindow(\''.$herf.$manifest->id.'\')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
        })
        ->addColumn('behandleButton', function($manifest){
            if ($manifest->no_spjm != null) {
                if ($manifest->status_behandle == 1) {
                    return '<button class="btn btn-outline-primary ReadyChcek" data-id="'.$manifest->id.'">Make It Ready</button>';
                }elseif ($manifest->status_behandle == 2) {
                    return '<button class="btn btn-primary FinishBehandle" data-id="'.$manifest->id.'">Finish</button>';
                }
            }else {
                return '';
            }
        })
        ->addColumn('statusBehandle', function($manifest){
            if ($manifest->status_behandle == 1) {
                return '<span class="badge bg-light-warning">On Progress</span>';
            }elseif ($manifest->status_behandle == 2) {
                return '<span class="badge bg-light-success">Ready</span>';
            }elseif ($manifest->status_behandle == 3) {
                return '<span class="badge bg-light-info">Finish</span>';
            }else {
                return '-';
            }
        })
        ->addColumn('nohbl', function($manifest){
            return $manifest->nohbl ?? '-';
        })
        ->addColumn('tgl_hbl', function($manifest){
            return $manifest->tgl_hbl ?? '-';
        })
        ->addColumn('notally', function($manifest){
            return $manifest->notally ?? '-';
        })
        ->addColumn('shipper', function($manifest){
            return $manifest->shipperM->name ?? '-';
        })
        ->addColumn('customer', function($manifest){
            return $manifest->customer->name ?? '-';
        })
        ->addColumn('quantity', function($manifest){
            return $manifest->quantity ?? '-';
        })
        ->addColumn('final_qty', function($manifest){
            return $manifest->final_qty ?? '-';
        })
        ->addColumn('packingName', function($manifest){
            return $manifest->packing->name ?? '-';
        })
        ->addColumn('packingCode', function($manifest){
            return $manifest->packing->code ?? '-';
        })
        ->addColumn('desc', function($manifest){
            $desc = $manifest->descofgoods ?? '-';
            return '<textarea class="form-control" cols="3" readonly>'. $desc .'</textarea>';
        })
        ->addColumn('weight', function($manifest){
            return $manifest->weight ?? '';
        })
        ->addColumn('meas', function($manifest){
            return $manifest->meas ?? '-';
        })
        ->addColumn('packingTally', function($manifest){
            return $manifest->packingTally->name ?? '-';
        })
        ->addColumn('noSPJM', function($manifest){
            return $manifest->no_spjm ?? '-';
        })
        ->addColumn('tglSPJM', function($manifest){
            return $manifest->tgl_spjm ?? '-';
        })
        ->addColumn('highlight', function($manifest){
            $statusClasses = [
                1 => 'highlight-yellow',
                2 => 'highlight-green',
                3 => 'highlight-blue',
            ];
        
            return $statusClasses[$manifest->status_behandle] ?? '';
        })
        ->rawColumns(['behandleButton', 'edit', 'detil', 'statusBehandle', 'desc'])
        ->make(true);
    }

    public function spjmBehandle(Request $request)
    {
        $jenis = $request->jenis_spjm;
        if ($jenis == 'karantina') {
            $manifest = Manifest::where('id', $request->id)->first();
            if ($manifest) {
                $manifest->update([
                    'jenis_spjm' => $request->jenis_spjm,
                    'no_spjm' => $request->no_spjm,
                    'tgl_spjm' => $request->tgl_spjm,
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Data ditemukan',
                ]);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Oopss, Something Wrong',
                ]);
            }
        }elseif ($jenis == 'spjm') {
            $tgl = Carbon::parse($request->tgl_spjm)->format('d/m/Y');
            $spjm = SPJM::where('no_spjm', $request->no_spjm)->where('tgl_pib', $tgl)->first();
            if ($spjm) {
                $manifest = Manifest::where('id', $request->id)->first();
                if ($manifest) {
                    $manifest->update([
                        'jenis_spjm' => $request->jenis_spjm,
                        'no_spjm' => $request->no_spjm,
                        'tgl_spjm' => $request->tgl_spjm,
                    ]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Data ditemukan',
                    ]);
                }else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Oopss, Something Wrong',
                    ]);
                }
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data Tidak ditemukan !!',
                ]);
            }
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Jenis harus di isi !!',
            ]);
        }
    }

    public function behandle(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();
        if ($manifest->no_spjm == null) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Anda Belum Memasukkan Dokumen Behandle']);
        }
        if ($manifest->date_ready_behandle == null) {
            $date_ready_behandle = Carbon::now();
        } else {
            $date_ready_behandle = $manifest->date_ready_behandle;
        }
        try {
            if ($request->location_behandle) {
                $item = Item::where('manifest_id', $manifest->id)->get();
                $oldLokasi = Item::where('manifest_id', $manifest->id)->pluck('lokasi_id')->unique();

                if ($oldLokasi) {
                    foreach ($oldLokasi as $lokasiId) {
                        $itemCount = $item->where('lokasi_id', $lokasiId)->count();
                        $lokasiLama = PM::where('id', $lokasiId)->first();
                        if ($lokasiLama) {
                            $newJumlah = $lokasiLama->jumlah_barang - $itemCount;
                            $lokasiLama->update([
                                'jumlah_barang' => $newJumlah,
                            ]);
                        }
                    }
                }

                foreach ($item as $tem) {
                    $tier = RT::where('rack_id', $tem->lokasi_id)->where('tier', $tem->tier)->first();
                    if ($tier) {
                        $tier->jumlah_barang = $tier->jumlah_barang - 1;
                        $tier->save();
                    }
                }

                $newLokasi = PM::where('id', $request->location_behandle)->first();
                $newTier = RT::where('rack_id', $request->location_behandle)->where('tier', $request->tier)->first();
                if ($newLokasi) {
                    foreach ($item as $barang) {
                        $barang->update([
                            'lokasi_id' => $newLokasi->id,
                            'tier' => $request->tier,
                        ]);
                        if ($newTier) {
                            $newTier->jumlah_barang = $newTier->jumlah_barang + 1;
                            $newTier->save();
                        }
                    }
                    $jumlahBarang = $item->count();
                    $newJumlahBarang = $newLokasi->jumlah_barang + $jumlahBarang;
                    $newLokasi->update([
                        'jumlah_barang' => $newJumlahBarang,
                    ]);
                    
                    $lokasiBehandle = $newLokasi->id;
                } else {
                    $lokasiBehandle = null;
                }
            } else {
                $lokasiBehandle = null;
            }

            $manifest->update([
                'status_behandle' => 1, 
                'location_behandle' => $lokasiBehandle,
            ]);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imagesInt', $fileName, 'public'); 
                    Photo::create([
                        'master_id' => $manifest->id,
                        'type' => 'manifest',
                        'action' => 'behandle',
                        'detil' => $request->keteranganPhoto,
                        'photo' => $fileName,
                    ]);
                }
            }
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Data Berhasil di Update']);

        } catch (\Throwable $e) {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Oopss, Something Wrong: ' . $e->getMessage()]);
        }
    }


    public function detailBehandle($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        $data['title'] = "Photo Behandle Manifest - " . $manifest->notally;
        $data['item'] = $manifest;
        $data['photos'] = Photo::where('master_id', $id)->where('type', '=', 'manifest')->where('action', '=', 'behandle')->get();
        // dd($data['photos']);
        return view('photo.index', $data);
    }

    public function readyCheckBehandle($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        if ($manifest) {
            $manifest->update([
                'status_behandle' => 2,
                'date_ready_behandle' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Manifest siap untuk behandle',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Oopss, Something Wrong!!',
            ]);
        }
    }

    public function finishBehandle($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        if ($manifest) {
            $manifest->update([
                'status_behandle' => 3,
                'tglbehandle' => Carbon::now()->toDateString(), // Format as 'Y-m-d'
                'jambehandle' => Carbon::now()->toTimeString(), // Format as 'H:i:s'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Manifest siap untuk behandle',
            ]);
        }else {
            return response()->json([
                'success' => false,
                'message' => 'Oopss, Something Wrong!!',
            ]);
        }
    }

    public function indexGateOut()
    {
        $data['title'] = 'Manifest Gate Out';
        $data['manifest'] = Manifest::whereNotNull('tglstripping')->get();
        $data['doks'] = Kode::orderBy('kode', 'asc')->get();
        $data['kets'] = KP::where('kegiatan', '=', 'gate-out')->get();
        return view('lcl.delivery.gateOut', $data);
    }

    public function dataGateOut(Request $request)
    {
        $manifest = Manifest::whereNotNull('tglstripping')->get();

        return DataTables::of($manifest)
        ->addColumn('edit', function($manifest){
            return '<button class="btn btn-warning editButton" data-id="'.$manifest->id.'"><i class="fa fa-pencil"></i></button>';
        })
        ->addColumn('detail', function($manifest){
            $herf = '/lcl/realisasi/GateOut-detail';
            return '<a href="javascript:void(0)" onclick="openWindow(\''.$herf.$manifest->id.'\')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
        })
        ->addColumn('barcode', function($manifest){
            if ($manifest->flag_segel_merarh == 'Y') {
               return '<p>Tidak Dapat Mencetak Barcode Ketika Segel Merah</p>';
            }else {
                return '<button class="btn btn-danger printBarcode" data-id="'.$manifest->id.'"><i class="fa fa-print"></i></button>';
            }
        })
        ->addColumn('bonMuat', function($manifest){
            return '<button class="btn btn-danger printBonmuat" data-id="'.$manifest->id.'"><i class="fa fa-print"></i></button>';
        })
        ->addColumn('status_bc', function($manifest){
            return $manifest->status_bc ?? '-';
        })
        ->addColumn('nohbl', function($manifest){
            return $manifest->nohbl ?? '-';
        })
        ->addColumn('tgl_hbl', function($manifest){
            return $manifest->tgl_hbl ?? '-';
        })
        ->addColumn('notally', function($manifest){
            return $manifest->notally ?? '-';
        })
        ->addColumn('shipper', function($manifest){
            return $manifest->shipperM->name ?? '-';
        })
        ->addColumn('customer', function($manifest){
            return $manifest->customer->name ?? '-';
        })
        ->addColumn('quantity', function($manifest){
            return $manifest->quantity ?? '-';
        })
        ->addColumn('final_qty', function($manifest){
            return $manifest->final_qty ?? '-';
        })
        ->addColumn('packingName', function($manifest){
            return $manifest->packing->name ?? '-';
        })
        ->addColumn('packingCode', function($manifest){
            return $manifest->packing->code ?? '-';
        })
        ->addColumn('desc', function($manifest){
            $desc = $manifest->descofgoods ?? '-';
            return '<textarea class="form-control custom-textarea" cols="30" readonly>'. $desc .'</textarea>';
        })
        ->addColumn('weight', function($manifest){
            return $manifest->weight ?? '';
        })
        ->addColumn('meas', function($manifest){
            return $manifest->meas ?? '-';
        })
        ->addColumn('packingTally', function($manifest){
            return $manifest->packingTally->name ?? '-';
        })
        ->addColumn('dokumen', function($manifest){
            return $manifest->dokumen->name ?? '-';
        })
        ->addColumn('no_dok', function($manifest){
            return $manifest->no_dok ?? '-';
        })
        ->addColumn('tglDok', function($manifest){
            return $manifest->tgl_dok ?? '-';
        })
        ->rawColumns(['edit', 'detail', 'barcode', 'desc', 'bonMuat'])
        ->make(true);
    }

    public function dokumenGateOut(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();

        $kdDok = $request->kd_dok;
        $tglDok = Carbon::parse($request->tgl_dok)->format('n/j/Y');
        $tglDokManual = Carbon::parse($request->tgl_dok)->format('d/m/Y');
        $tglDokPabean = Carbon::parse($request->tgl_dok)->format('Ymd');
        // var_dump($tglDok, $request->no_dok, $request->kd_dok);
        // die();
        if ($kdDok == 1) {
            $dok = SPPB::where('no_sppb', $request->no_dok)->where('tgl_sppb', $tglDok)->first();
            if ($dok) {
                if ($dok->no_bl_awb == $manifest->nohbl) {
                    if ($dok->nama_imp != $manifest->customer->name) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Nama Importir Berbeda',
                        ]);
                    }
                    if ($dok->npwp_imp != $manifest->customer->npwp) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Nama Importir Berbeda',
                        ]);
                    }
                    if ($manifest->qty == $manifest->final_qty) {
                        $statusBC = "release";
                    }else {
                        $statusBC = "HOLD";
                    }
                    $manifest->update([
                        'kd_dok_inout' => $kdDok,
                        'no_dok' => $request->no_dok,
                        'tgl_dok' => $request->tgl_dok,
                        'status_bc' => $statusBC,
                    ]);
    
                    return response()->json([
                        'success' => true,
                        'message' => 'Data di temukan',
                    ]);
                }else {
                    return response()->json([
                        'success' => false,
                        'message' => 'No HBL Berbeda',
                    ]);
                }
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak di temukan',
                ]);
            }
        }elseif ($kdDok == 2) {
            $dok = BC23::where('no_sppb', $request->no_dok)->where('tgl_sppb', $tglDok)->first();
            if ($dok) {
                if ($dok->no_bl_awb == $manifest->nohbl) {
                    if ($dok->nama_imp != $manifest->customer->name) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Nama Importir Berbeda',
                        ]);
                    }
                    if ($dok->npwp_imp != $manifest->customer->npwp) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Nama Importir Berbeda',
                        ]);
                    }
                    $manifest->update([
                        'kd_dok_inout' => $kdDok,
                        'no_dok' => $request->no_dok,
                        'tgl_dok' => $request->tgl_dok,
                        'status_bc' => 'HOLD',
                    ]);
    
                    return response()->json([
                        'success' => true,
                        'message' => 'Data di temukan',
                    ]);
                }else {
                    return response()->json([
                        'success' => false,
                        'message' => 'No HBL Berbeda',
                    ]);
                }
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak di temukan',
                ]);
            }
        }elseif (in_array($kdDok, [41, 44])) {
            $dok = Pabean::where('kd_dok_inout', $kdDok)->where('no_dok_inout', $request->no_dok)->where('tgl_dok_inout', $tglDokPabean)->first();
            if ($dok) {
                if ($dok->no_bl_awb != $manifest->nohbl) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No HBL Berbeda',
                    ]);
                }
                $manifest->update([
                    'kd_dok_inout' => $kdDok,
                    'no_dok' => $request->no_dok,
                    'tgl_dok' => $request->tgl_dok,
                    'status_bc' => 'HOLD',
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Data di temukan',
                ]);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak di temukan',
                ]);
            }
        }else {
            $dok = Manual::where('kd_dok_inout', $kdDok)->where('no_dok_inout', $request->no_dok)->where('tgl_dok_inout', $tglDokManual)->first();
            if ($dok) {
                if ($dok->no_bl_awb != $manifest->nohbl) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No HBL Berbeda',
                    ]);
                }
                $manifest->update([
                    'kd_dok_inout' => $kdDok,
                    'no_dok' => $request->no_dok,
                    'tgl_dok' => $request->tgl_dok,
                    'status_bc' => 'HOLD',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Data di temukan',
                ]);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak di temukan',
                ]);
            }
        }
    }

    public function gateOut(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();
        try {
            if ($manifest->status_bc != 'release') {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Status BC Belum Release']);
            }
            $item = Item::where('manifest_id', $manifest->id)->get();
            $oldLokasi = Item::where('manifest_id', $manifest->id)->pluck('lokasi_id')->unique();
            if ($oldLokasi) {
                foreach ($oldLokasi as $lokasiId) {
                    $itemCount = $item->where('lokasi_id', $lokasiId)->count();
                    $lokasiLama = PM::where('id', $lokasiId)->first();
                    if ($lokasiLama) {
                        $newJumlah = $lokasiLama->jumlah_barang - $itemCount;
                        $lokasiLama->update([
                            'jumlah_barang' => $newJumlah,
                        ]);
                    }
                }
            }
            $manifest->update([
                'tglrelease' => $request->tglbuangmty,
                'jamrelease' => $request->jambuangmty,
                'nopol_release' => $request->nopol_release,
            ]);

            // dd($manifest);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $fileName = $photo->getClientOriginalName();
                    $photo->storeAs('imagesInt', $fileName, 'public'); 
                    $newPhoto = Photo::create([
                        'master_id' => $manifest->id,
                        'type' => 'manifest',
                        'action' => 'gate-out',
                        'detil' => $request->keteranganPhoto,
                        'photo' => $fileName,
                    ]);
                }
            }
            return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data Berhasil di Update']);
            
        } catch (\Throwable $e) {
            return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Oopss, Something Wrong'. $e->getMessage()]);
        }
    }

    public function detailGateOut($id)
    {
        $manifest = Manifest::where('id', $id)->first();
        $data['title'] = "Photo Gate Out Manifest - " . $manifest->notally;
        $data['item'] = $manifest;
        $data['photos'] = Photo::where('master_id', $id)->where('type', '=', 'manifest')->where('action', '=', 'gate-out')->get();
        // dd($data['photos']);
        return view('photo.index', $data);
    }

    public function createBarcode(Request $request)
    {
        $manifest = Manifest::where('id', $request->id)->first();
        if ($manifest->flag_segel_merah == 'Y') {
            return response()->json([
                'success' => false,
                'message' => 'Sedang dalam segel merah!',
            ]);
        }
        if ($manifest->no_dok == null) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen belum ada, isi dokumen terlebih dahulu!',
            ]);
        }
        // $header = Header::where('manifest_id', $request->id)->orderBy('expired_date', 'desc')->first();
        // var_dump($header);
        // die;
        // if (empty($header)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Invoice belum terbit',
        //     ]);
        // }

        // if ($header->status== 'N') {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Invoice belum dibayar',
        //     ]);
        // }

        if ($manifest->flag_segel_merah == 'Y') {
            $action = 'holdp2';
        }else {
            if ($manifest->status_bc == 'release') {
                $action = 'active';
            }else {
                $action = 'hold';
            }
        }
        
        if ($manifest->active_to == null) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal rencana keluar belum di atur',
            ]);
        }
        $barcode = Barcode::where('ref_id', $manifest->id)->where('ref_type', '=', 'Manifest')->where('ref_action', 'release')->first();
        if ($barcode) {
            $barcode->update([
                // 'expired'=> $header->expired_date,
                'expired'=> $manifest->active_to,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $barcode,
            ]);
        }else {
            do {
                $uniqueBarcode = Str::random(20);
            } while (Barcode::where('barcode', $uniqueBarcode)->exists());    
            $newBarcode = Barcode::create([
                'ref_id'=>$manifest->id,
                'ref_type'=>'Manifest',
                'ref_action'=> 'release',
                'ref_number'=>$manifest->notally,
                'barcode'=> $uniqueBarcode,
                'status'=> $action,
                // 'expired'=> $header->expired_date,
                'expired'=> $manifest->active_to,
                'uid'=> Auth::user()->id,
                'created_at'=> Carbon::now(),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $newBarcode,
            ]);
        }
    }

    public function manifestBarcode($id)
    {
        $data['barcode'] = Barcode::where('id', $id)->first();
        $barcode = $data['barcode'];
        $data['title'] = "Gate Pass " . $barcode->manifest->notally; 

        return view('barcode.indexManifest', $data);
    }

    public function manifestBonMuat($id)
    {
        $data['barcode'] = Barcode::where('id', $id)->first();
        $barcode = $data['barcode'];
        $data['title'] = "Gate Pass " . $barcode->manifest->notally; 

        return view('barcode.indexManifest', $data);
    }

    public function cetakSuratJalan($id)
    {
        $manifest = Manifest::find($id);

        $data['title'] = "Cetak Surat Jalan Manifest - " . $manifest->nohbl;

        $data['manifest'] = $manifest;

        return view('lcl.delivery.cetakSuratJalan', $data);
    }
}


