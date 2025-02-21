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
        
        $this->token = '$U2FsdGVkX18WeMoBpJoh/Fklqv+HfHHjmT1pMz3sbJX6SHIJJvoDdImZEr+GQwDLWmXxVXJB4cp9iQuiESKK6A==';
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

        
       
        
        if ($header['authorization'][0] != $this->token) {
            return response()->json([
                'status' => 0,
                'success' => false,
                'message' => 'Invalid Token',
            ]);
        }
        
        $plp = $request->plpNumber;
        $contNumber = $request->containerNumber;
        $cont = ContF::where('nocontainer', $contNumber)
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
