<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

use App\Models\ContainerFCL as ContF;

use GuzzleHttp\Client;

class ApiController extends Controller
{
    protected $client;
    protected $token;

    public function __construct()
    {
        $this->now = Carbon::now()->format('Ymd');
        
        $this->token = $this->now . 'ENVLG';
        $this->client = new Client(); // Inisialisasi Guzzle Client
    }

    public function envilogGateService(Request $request)
    {
        // dd($request->header(), $this->token);

        $header = $request->header();

        if (!isset($header['authorization'])) {
            return response()->json([
                'status' => 0,
                'message' => 'Authorization header is missing',
            ]);
        }

        $plp = $request->plpNumber;
        $cont = $request->containerNumber;

        $token = $this->token.$plp.$cont;

        if ($header['authorization'][0] != $token) {
            return response()->json([
                'status' => 0,
                'success' => false,
                'message' => 'Invalid Token',
            ]);
        }

        $cont = ContF::where('nocontainer', $cont)
            ->whereHas('job', function ($query) use ($plp) {
                $query->where('noplp', $plp);
            })->first();

        if ($cont) {

            $data = [
                'gateInDate' => ($cont->tglmasuk && $cont->jammasuk) ? Carbon::parse($cont->tglmasuk . ' ' . $cont->jammasuk)->format('Y-m-d H:i:s') : 'Belum Masuk',
                'gateOutDate' => ($cont->tglkeluar && $cont->jamkeluar) ? Carbon::parse($cont->tglkeluar . ' ' . $cont->jamkeluar)->format('Y-m-d H:i:s') : 'Belum Keluar',
            ];
            return response()->json([
                'status' => 1,
                'sucsess' => true,
                'message' => 'Data Ditemukan!',
                'data' => $data
            ]);
        }else {
            return response()->json([
                'status' => 0,
                'success' => false,
                'message' => 'Data Tidak Ditemukan !!',
            ]);
        }
    }
}
