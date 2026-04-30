<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use DataTables;

use App\Models\KapasitasNPCT;
use GuzzleHttp\Client;

use Artisaninweb\SoapWrapper\Facades\SoapWrapper;
// use Artisaninweb\SoapWrapper\Facades\SoapWrapper;


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

    // public function post(Request $request)
    // {
    //     $wsdl = 'https://api.npct1.co.id/services/index.php/Line2?wsdl';    

    //     // $xml = new \SimpleXMLElement('<yor/>');

    //     // $xml->addChild('username', 'lini2');
    //     // $xml->addChild('password', 'lini2@2026');
    //     // $xml->addChild('warehouse_type', $request->warehouse_type);
    //     // $xml->addChild('warehouse_code', $request->warehouse_code);
    //     // $xml->addChild('yor', $request->yor);
    //     // $xml->addChild('capacity', $request->capacity);
        
    //     // $xmlBody = $xml->asXML(); 

    //     // $params = [
    //     //     'username' => 'lini2',
    //     //     'password' => 'lini2@2026',
    //     //     'warehouse_type' => $request->warehouse_type,
    //     //     'warehouse_code' => $request->warehouse_code,
    //     //     'yor' => (int)$request->yor,
    //     //     'capacity' => (int)$request->capacity,
    //     // ];

    //     $params = new \stdClass();
    //     $params->username = 'lini2';
    //     $params->password = 'lini2@2026';
    //     $params->warehouse_type = $request->warehouse_type;
    //     $params->warehouse_code = $request->warehouse_code;
    //     $params->yor = (int)$request->yor;
    //     $params->capacity = (int)$request->capacity;
        
    //     try {   

    //         $ip = file_get_contents('https://api.ipify.org');

    //         // var_dump($ip, $xmlBody);
    //         // die;
    //         $client = new \SoapClient($wsdl, [
    //             'trace' => true,
    //             'exceptions' => true,
    //             'cache_wsdl' => WSDL_CACHE_NONE,
    //             'soap_version' => SOAP_1_1,
    //             'stream_context' => stream_context_create([
    //                 'ssl' => [
    //                     'verify_peer' => false,
    //                     'verify_peer_name' => false,
    //                 ]
    //             ])
    //         ]); 

    //         // ✅ PARAMETER HARUS STRING XML
    //         // $response = $client->__soapCall('yor', [$params]);\
    //         // $response = $client->__soapCall('yor', [$xmlBody]);
    //         $response = $client->__soapCall('yor', [$params]);
    //         var_dump($response);
    //         die;
    //         KapasitasNPCT::create([
    //             'user_id' => Auth::user()->id,
    //             'warehouse_type' => $request->warehouse_type, 
    //             'warehouse_code' => $request->warehouse_code,   
    //             'yor' => $request->yor,   
    //             'capacity' => $request->capacity, 
    //             'response' => $response
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => $response
    //         ]);
    //         // var_dump(
    //         //     $response,
    //         //     $client->__getLastRequest(),
    //         //     $client->__getLastResponse()
    //         // );  
    //         // die;

    //     } catch (\SoapFault $e) {
    //         return response()->json([
    //                 'success' => false,
    //                 'message' =>  $e->getMessage()
    //             ]
    //         );
    //         // dd($e->getMessage());
    //     }
    // }

    // public function post(Request $request)
    // {
    //     $wsdl = 'https://api.npct1.co.id/services/index.php/Line2?wsdl';    

    //     try {   

    //         /*
    //         |--------------------------------------------------------------------------
    //         | REGISTER SOAP SERVICE
    //         |--------------------------------------------------------------------------
    //         */
    //         SoapWrapper::add(function ($service) use ($wsdl) {  

    //             $service
    //                 ->name('yorRequestNpct')
    //                 ->wsdl($wsdl)
    //                 ->trace(true)
    //                 ->cache(WSDL_CACHE_NONE)
    //                 ->options([
    //                     'soap_version' => SOAP_1_1,
    //                     'exceptions' => true,
    //                     'style' => SOAP_RPC,
    //                     'use' => SOAP_ENCODED,
    //                     'stream_context' => stream_context_create([
    //                         'ssl' => [
    //                             'verify_peer' => false,
    //                             'verify_peer_name' => false,
    //                             'allow_self_signed' => true
    //                         ]
    //                     ]),
    //                 ]);
    //         }); 

    //         /*
    //         |--------------------------------------------------------------------------
    //         | REQUEST DATA (CASE SENSITIVE!)
    //         |--------------------------------------------------------------------------
    //         */
    //         $reqData = [
    //             'username'        => 'lini2',
    //             'password'        => 'lini2@2026',
    //             'warehouse_type'  => $request->warehouse_type,
    //             'warehouse_code'  => $request->warehouse_code,
    //             'yor'             => (int)$request->yor,
    //             'capacity'        => (int)$request->capacity,
    //         ];  

    //         /*
    //         |--------------------------------------------------------------------------
    //         | CALL SOAP NPCT1
    //         |--------------------------------------------------------------------------
    //         */
    //         $response = SoapWrapper::service('yorRequestNpct', function ($service) use ($reqData) { 

    //             return $service->call('yor', [$reqData]);   

    //         }); 

    //         /*
    //         |--------------------------------------------------------------------------
    //         | SAVE RESPONSE
    //         |--------------------------------------------------------------------------
    //         */
    //         KapasitasNPCT::create([
    //             'user_id' => Auth::user()->id,
    //             'warehouse_type' => $request->warehouse_type,
    //             'warehouse_code' => $request->warehouse_code,
    //             'yor' => $request->yor,
    //             'capacity' => $request->capacity,
    //             'response' => json_encode($response)
    //         ]); 

    //         return response()->json([
    //             'success' => true,
    //             'data' => $response
    //         ]); 

    //     } catch (\SoapFault $e) {   

    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    public function post(Request $request)
    {
        $wsdl = 'https://api.npct1.co.id/services/index.php/Line2?wsdl';    

        SoapWrapper::add(function ($service) use ($wsdl) {
            $service->name('yorRequestNpct')
                ->wsdl($wsdl)
                ->trace(true)
                ->cache(WSDL_CACHE_NONE)
                ->options([
                    'soap_version' => SOAP_1_1,
                    'stream_context' => stream_context_create([
                        'ssl'=>[
                            'verify_peer'=>false,
                            'verify_peer_name'=>false
                        ]
                    ])
                ]);
        }); 

        // $xmlBody = '
        // <yor>
        //     <username>lini2</username>
        //     <password>lini2@2026</password>
        //     <warehouse_type>'.$request->warehouse_type.'</warehouse_type>
        //     <warehouse_code>'.$request->warehouse_code.'</warehouse_code>
        //     <yor>'.$request->yor.'</yor>
        //     <capacity>'.$request->capacity.'</capacity>
        // </yor>';  
        $reqData = [
            'username' => 'lini2',
            'password' => 'lini2@2026',
            'warehouse_type' => $request->warehouse_type,
            'warehouse_code' => $request->warehouse_code,
            'yor' => (int)$request->yor,
            'capacity' => (int)$request->capacity,
        ];      


        try {   

            // $response = SoapWrapper::service('yorRequestNpct', function ($service) use ($xmlBody) {
            //     return $service->call('yor', [$xmlBody]);
            // }); 

            $response = null;

            SoapWrapper::service('yorRequestNpct', function ($service) use ($reqData, &$response) {
                $response = $service->call('yor', $reqData);
            });
            
            $xmlResponse = is_object($response)
                ? ($response->return ?? '')
                : $response;

            KapasitasNPCT::create([
                'user_id' => Auth::user()->id,
                'warehouse_type' => $request->warehouse_type, 
                'warehouse_code' => $request->warehouse_code,   
                'yor' => $request->yor,   
                'capacity' => $request->capacity, 
                'response' => $xmlResponse
            ]);

            return response()->json([
                'success' => true,
                'message' => $response
            ]);

            // var_dump($response);
            // die;  

        } catch (\SoapFault $e) {
            // dd($e->getMessage());
             return response()->json([
                    'success' => false,
                    'message' =>  $e->getMessage()
                ]
            );
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
