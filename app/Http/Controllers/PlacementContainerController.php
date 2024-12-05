<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;

use App\Models\Container as Cont;
use App\Models\JobOrder as Job;
use App\Models\User;
use App\Models\Photo;
use App\Models\YardDesign as YD;
use App\Models\YardDetil as RowTier;
use App\Models\KeteranganPhoto as KP;

class PlacementContainerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function indexLCL()
    {
        $data['title'] = 'LCL || Realisasi - Placement Container';
        $data['conts'] = Cont::where('type', '=', 'lcl')->whereNot('tglmasuk', null)->where('tglkeluar', null )->get();

        $data['yards'] = YD::whereNot('yard_block', null)->get();
        $data['yardDetils'] = RowTier::get();
        $data['kets'] = KP::where('kegiatan', '=', 'placement')->get();
        // dd($data['conts']);

        return view('lcl.realisasi.placement.index', $data);
    }

    public function getSlot(Request $request)
    {
        $yardId = $request->input('yard_id');
        $slots = RowTier::where('yard_id', $yardId)->pluck('slot')->unique();
        return response()->json($slots);
    }

    public function getRow(Request $request)
    {
        $yardId = $request->input('yard_id');
        $rows = RowTier::where('yard_id', $yardId)->where('slot', $request->slot)->pluck('row')->unique();
        return response()->json($rows);
    }

    public function getTier(Request $request)
    {
        $yardId = $request->input('yard_id');
        $tiers = RowTier::where('yard_id', $yardId)->where('slot', $request->slot)->where('row', $request->row)->pluck('tier')->unique();
        return response()->json($tiers);
    }

    public function edit($id)
    {
        $cont = Cont::where('id', $id)->first();
        if ($cont) {
            $job = Job::where('id', $cont->joborder_id)->first();
            $user = Auth::user()->name;
            $userId = Auth::user()->id;
            $uid = User::where('id', $cont->uidmasuk)->first();
            $rowTier = RowTier::where('id', $cont->yard_detil_id)->first();
            // var_dump($cont->yard_detil_id, $rowTier);
            // die;
            if ($rowTier) {
                $slot = $rowTier->slot;
                $row = $rowTier->row;
                $tier = $rowTier->tier;
            } else {
                $slot = null;
                $row = null;
                $tier = null;
            }
            return response()->json([
                'success' => true,
                'data' => $cont,
                'job' =>$job,
                'user' => $user,
                'userId' => $userId,
                'uid' => $uid,
                'slot' => $slot,
                'row' => $row,
                'tier' => $tier,
            ]);
        }
    }

    public function updateLCL(Request $request)
    {
        $cont = Cont::where('id', $request->id)->first();
        if ($cont) {
            $oldYard = RowTier::where('cont_id', $cont->id)->get();
            if ($oldYard) {
                foreach ($oldYard as $old) {
                    $old->update([
                        'cont_id' => null,
                        'active' => 'N',
                    ]);
                }
            }
            $yardDetil = RowTier::where('yard_id', $request->yard_id)->where('slot', $request->slot)->where('row', $request->row)->where('tier', $request->tier)->first();
            if ($yardDetil) {
                if ($yardDetil->cont_id != null && $yardDetil->cont_id != $cont->id) {
                    return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Yard Sudah Terisi, Silahkan pilih yard lain']);
                }
                
                
                $cont->update([
                    'yard_id'=>$request->yard_id,
                    'yard_detil_id'=> $yardDetil->id,
                ]);


                if ($cont->size == '40') {
                    $nextSlot = $request->slot + 1;
                    $nexyard = RowTier::where('yard_id', $request->yard_id)->where('slot', $nextSlot)->where('row', $request->row)->where('tier', $request->tier)->first();
                    $nexyard->update([
                        'cont_id' => $cont->id,
                        'active' => 'Y',
                    ]);
                }
                $yardDetil->update([
                    'cont_id' => $cont->id,
                    'active' => 'Y',
                ]);
    
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $fileName = $photo->getClientOriginalName();
                        $photo->storeAs('imagesInt', $fileName, 'public'); 
                        $newPhoto = Photo::create([
                            'master_id' => $cont->id,
                            'type' => 'lcl',
                            'action' => 'placement',
                            'detil' => $request->keteranganPhoto,
                            'photo' => $fileName,
                        ]);
                    }
                }
                return redirect()->back()->with('status', ['type'=>'success', 'message'=>'Data berhasil di update']);
            }else {
                return redirect()->back()->with('status', ['type'=>'error', 'message'=>'Yard Tidak Ditemukan']);
            }
        }
    }

    public function detail($id)
    {
        $cont = Cont::where('id', $id)->first();
        $data['title'] = "Photo PLacement Container Container - " . $cont->nocontainer;
        $data['item'] = $cont;
        $data['photos'] = Photo::where('master_id', $id)->where('action', '=', 'placement')->get();
        // dd($data['photos']);
        return view('photo.index', $data);
    }
}
