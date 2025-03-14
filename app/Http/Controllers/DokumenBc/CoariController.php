<?php

namespace App\Http\Controllers\DokumenBc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Auth;
use Carbon\Carbon;

use App\Models\Container as Cont;
use App\Models\ContainerFCL as ContF;
use App\Models\Manifest;

// coari
use App\Models\Pengiriman\Coari\CoariCont as CC;
use App\Models\Pengiriman\Coari\CoariContDetil as CD;
use App\Models\Pengiriman\Coari\CoariKms as KC;
use App\Models\Pengiriman\Coari\CoariKmsDetil as KD;

// codeco
use App\Models\Pengiriman\Codeco\CodecoCont;
use App\Models\Pengiriman\Codeco\CodecoContDetil;
use App\Models\Pengiriman\Codeco\CodecoKms;
use App\Models\Pengiriman\Codeco\CodecoKmsDetil;

// Reff Number
use App\Models\Pengiriman\RefNumber as RN;

use DataTables;

class CoariController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function indexContLCL()
    {
        $data['title'] = 'Data Coari Cont LCL';

        return view('pengiriman.lcl.cont.coari', $data);
    }

    public function dataContLCL(Request $request)
    {
        $cont = CD::where('jns_cont', 'L')->get();
        return DataTables::of($cont)
        ->addColumn('action', function($cont){
            return '-';
        })
        ->rawColumns(['action'])
        ->make(true);
    }
}
