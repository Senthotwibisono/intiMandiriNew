<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use DataTables;

use App\Models\KapasitasNPCT;
use GuzzleHttp\Client;


class NpctController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['title'] = 'Sor Yor NPCT';

        return view('npct.index', $data);
    }

    public function data()
    {
        $data = KapasitasNPCT::with(['user'])->orderBy('created_at', 'desc')->get();

        return DataTables::of($data)->make(true);
    }

    // public function post(Request $request)
    // {
    //     $data = [
    //         'username'        => 'lini2',
    //         'password'        => 'lini2@2018',
    //         'warehouse_type'  => $request->warehouse_type,
    //         'warehouse_code'  => $request->warehouse_code,
    //         'yor'             => (int)$request->yor,
    //         'capacity'        => (int)$request->capacity,
    //     ];

    //     $json = json_encode($data);

    //     // var_dump($json);
    //     // die;

    //     $bodyXml = $this->jsonToXml($json);

    //     $soapXml = '
    //     <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    //        <soapenv:Header/>
    //        <soapenv:Body>
    //            '.$bodyXml.'
    //        </soapenv:Body>
    //     </soapenv:Envelope>';

    //     $client = new Client();

    //     $response = $client->post(
    //         'https://api.npct1.co.id/services/index.php/Line2?wsdl',
    //         [
    //             'headers' => [
    //                 'Content-Type' => 'text/xml; charset=utf-8',
    //             ],
    //             'body' => $json
    //         ]
    //     );      

    //     $result = $response->getBody()->getContents();

    //     // dd($result);
    //     var_dump($result);
    //     die;
    // }

    public function post(Request $request)
    {
        $wsdl = 'https://api.npct1.co.id/services/index.php/Line2?wsdl';    

        $params = [
            'username'        => 'lini2',
            'password'        => 'lini2@2018',
            'warehouse_type'  => $request->warehouse_type,
            'warehouse_code'  => $request->warehouse_code,
            'yor'             => (int)$request->yor,
            'capacity'        => (int)$request->capacity,
        ];  

        try {   

            $client = new \SoapClient($wsdl, [
                'trace' => true,
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'soap_version' => SOAP_1_1,
                'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ]
                ])
            ]); 

            // CALL METHOD YOR
            $response = $client->__soapCall('yor', [$params]);  

            // Debug request & response
            var_dump(
                $response,
                $client->__getLastRequest(),
                $client->__getLastResponse()
            );
            die();

        } catch (\SoapFault $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            // var_dump($e->getMessage());
            // die;
        }
    }

    private function jsonToXml($json)
    {
        $data = json_decode($json, true);

        $xml = new \SimpleXMLElement('<yor/>');

        foreach ($data as $key => $value) {
            $xml->addChild($key, $value);
        }

        return $xml->asXML();
    }
}
