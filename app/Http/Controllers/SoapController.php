<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TpsDokPKBE;


use Artisaninweb\SoapWrapper\Facades\SoapWrapper;

class SoapController extends Controller
{
    //
    protected $wsdl;
    protected $user;
    protected $password;
    protected $kode;
    protected $response;

    public function __construct() {
        
        $this->wsdl = 'https://tpsonline.beacukai.go.id/tps/service.asmx?WSDL';
        $this->user = 'AIRN';
        $this->password = 'AIRN';
        $this->kode = 'AIRN';
        // KODE_ASP AIRN
        // KODE_GUDANG ARN1 & ARN3
        
    }
    
    public function getXmlDemo()
    {
        
        $url     = "https://tpsonline.beacukai.go.id/tps/service.asmx?WSDL";
        $context = stream_context_create(array(
                'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
        ));


        $client = new \SoapClient($url, array('stream_context' => $context));
        
        
//        echo phpinfo();
        
        /* Initialize webservice with your WSDL */
//        $client = new \SoapClient("https://demo.docusign.net/api/3.0/api.asmx?WSDL");
//        $client = new \SoapClient("http://currencyconverter.kowabunga.net/converter.asmx?WSDL");
//        $client = new \SoapClient("https://tpsonline.beacukai.go.id/tps/service.asmx?WSDL",[
//            'exceptions' => 1,
//            'trace' => TRUE,
//            'local_cert' => url('cert/bc.pem'),
////            'passphrase' => $this->passphrase,
////            'ssl_method' => SOAP_SSL_METHOD_SSLv2, // not work!
////            'authentication' => SOAP_AUTHENTICATION_DIGEST,
//            "soap_version"  => SOAP_1_2,
//            'cache_wsdl' => WSDL_CACHE_NONE,
//            'stream_context' => stream_context_create([
//                'ssl' => [
//                    'cafile' => '/etc/ssl/certs/ca-certificates.crt'
//                ]
//            ]),
//        ]);
//        $client = new \SoapClient("https://www.iatspayments.com/NetGate/CustomerLink.asmx?WSDL");
        
        /* Set your parameters for the request */
//        $params = [
//            'CurrencyFrom' => 'USD',
//            'CurrencyTo'   => 'EUR',
//            'RateDate'     => '2017-06-05',
//            'Amount'       => '1000'
//        ];
        $params = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_Tps' => $this->kode
        ];
//        $params = [
//            'agentCode' => '123',
//            'password' => '123',
//            'customerIPAddress' => '123',
//            'FromDate' => '123',
//            'ToDate' => '123'
//        ];

        /* Invoke webservice method with your parameters, in this case: Function1 */
//        $response = $client->__soapCall("GetConversionAmount", array($params));
        $response = $client->__soapCall("GetSPJM", array($params));
        

        /* Print webservice response */
        var_dump($response);
    }
    
    public function demo()
    {
        
        // Add a new service to the wrapper
        \SoapWrapper::add(function ($service) {
            $service
                ->name('currency')
                ->wsdl('http://currencyconverter.kowabunga.net/converter.asmx?WSDL')
                ->trace(true)                                                   // Optional: (parameter: true/false)
//                ->header()                                                      // Optional: (parameters: $namespace,$name,$data,$mustunderstand,$actor)
//                ->customHeader($customHeader)                                   // Optional: (parameters: $customerHeader) Use this to add a custom SoapHeader or extended class                
//                ->cookie()                                                      // Optional: (parameters: $name,$value)
//                ->location()                                                    // Optional: (parameter: $location)
//                ->certificate()                                                 // Optional: (parameter: $certLocation)
//                ->cache(WSDL_CACHE_NONE)                                        // Optional: Set the WSDL cache
                ->options(['login' => 'username', 'password' => 'password']);   // Optional: Set some extra options
        });
        
        $data = [
            'CurrencyFrom' => 'USD',
            'CurrencyTo'   => 'EUR',
            'RateDate'     => '2014-06-05',
            'Amount'       => '1000'
        ];
        
//        return json_encode($data);
        
        // Using the added service
        \SoapWrapper::service('currency', function ($service) use ($data) {
//            var_dump($service->getFunctions());
            $this->response = $service->call('GetConversionAmount', [$data])->GetConversionAmountResult;
        });
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml  || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        return back()->with('success', $this->response);
        
    }
    
    public function GetResponPLP()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('GetResponPLP')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                   
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_asp' => $this->kode
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnlineSoap', function ($service) use ($data) {        
            $this->response = $service->call('GetResponPLP', [$data])->GetResponPLPResult;      
        });
        
        var_dump($this->response);
        
    }
    
    public function GetResponPLP_onDemand(Request $request)
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnlineSoap')
                ->wsdl($this->wsdl)
                ->trace(true)   
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)  
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                    
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'No_plp' => $request->no_plp,
            'Tgl_plp' => date('dmY', strtotime($request->tgl_plp)),
			'KdGudang' => $request->KODE_GUDANG,			
			//'KdGudang' => $this->kode,
            'RefNumber' => $request->refnumber
        ];
        
        dd($data);

        try{
            \SoapWrapper::service('TpsOnlineSoap', function ($service) use ($data) {        
                $this->response = $service->call('GetResponPlp_onDemands', [$data])->GetResponPlp_onDemandsResult;      
            });
        }catch (\SoapFault $exception){
            var_dump($exception);
        }
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml  || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        $header = array();
        $details = [];
        foreach($xml->children() as $child) {
            foreach($child as $key => $value) {
                if($key == 'header' || $key == 'HEADER'){
                    $header[] = $value;
                }else{
                    foreach ($value as $detail):
                        $details[] = $detail;
                    endforeach;
                }
            }
        }
        
        // INSERT DATA
        $respon = new \App\Models\TpsResponPlp;
        foreach ($header[0] as $key=>$value):
            $respon->$key = $value;
        endforeach;
        $respon->TGL_UPLOAD = date('Y-m-d H:i:s');
        $respon->save();
        
        $plp_id = $respon->tps_responplptujuanxml_pk;

        foreach ($details as $detail):     
            $respon_detail = new \App\Models\TpsResponPlpDetail;
            $respon_detail->tps_responplptujuanxml_fk = $plp_id;
            foreach($detail as $key=>$value):
                $respon_detail->$key = $value;
            endforeach;
            $respon_detail->save();
        endforeach;
        
        return back()->with('success', 'Get Respon PLP On Demand has been success.');
    }
    
    public function GetResponPLP_Tujuan()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnlineSoap')
                ->wsdl($this->wsdl)
                ->trace(true)   
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)  
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                   
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_asp' => $this->kode
        ];
        
        try{
            \SoapWrapper::service('TpsOnlineSoap', function ($service) use ($data) {        
                $this->response = $service->call('GetResponPLP_Tujuan', [$data])->GetResponPLP_TujuanResult;      
            });
        }catch (\SoapFault $exception){
            var_dump($exception);
        }
        
        // Using the added service

        
//        $client = new \SoapClient($this->wsdl, array('soap_version' => SOAP_1_2));
//
//        /* Set your parameters for the request */
//        $params = [
//            'UserName' => $this->user, 
//            'Password' => $this->password,
//            'Kd_asp' => $this->kode
//        ];
//
//        $response = $client->__soapCall("GetResponPLP_Tujuan", array($params));

//        var_dump($response);
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml  || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        $header = array();
        $details = [];
        foreach($xml->children() as $child) {
            foreach($child as $key => $value) {
                if($key == 'header' || $key == 'HEADER'){
                    $header[] = $value;
                }else{
                    foreach ($value as $detail):
                        $details[] = $detail;
                    endforeach;
                }
            }
        }
        
        // INSERT DATA
        $respon = new \App\Models\TpsResponPlp;
        foreach ($header[0] as $key=>$value):
            $respon->$key = $value;
        endforeach;
        $respon->TGL_UPLOAD = date('Y-m-d H:i:s');
        $respon->save();
        
        $plp_id = $respon->tps_responplptujuanxml_pk;

        foreach ($details as $detail):     
            $respon_detail = new \App\Models\TpsResponPlpDetail;
            $respon_detail->tps_responplptujuanxml_fk = $plp_id;
            foreach($detail as $key=>$value):
                $respon_detail->$key = $value;
            endforeach;
            $respon_detail->save();
        endforeach;
        
        return back()->with('success', 'Get Respon PLP has been success.');
        
    }
    
    public function GetResponBatalPLP_Tujuan()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetResponBatalPLPTujuan')
                ->wsdl($this->wsdl)
                ->trace(true)   
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)  
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                   
        });
        
        $data = [
            'Username' => $this->user, 
            'Password' => $this->password,
            'Kd_asp' => $this->kode
        ];
        
        try{
            \SoapWrapper::service('TpsOnline_GetResponBatalPLPTujuan', function ($service) use ($data) {        
                $this->response = $service->call('GetResponBatalPLPTujuan', [$data])->GetResponBatalPLPTujuanResult;      
            });
        }catch (\SoapFault $exception){
            var_dump($exception);
        }
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml  || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        $header = array();
        $details = [];
        foreach($xml->children() as $child) {
            foreach($child as $key => $value) {
                if($key == 'header' || $key == 'HEADER'){
                    $header[] = $value;
                }else{
                    foreach ($value as $detail):
                        $details[] = $detail;
                    endforeach;
                }
            }
        }
        
        // INSERT DATA
        $respon = new \App\Models\TpsResponBatalPlp;
        foreach ($header[0] as $key=>$value):
            $respon->$key = $value;
        endforeach;
        $respon->LASTUPDATE = date('Y-m-d H:i:s');
        $respon->save();
        
        $plp_id = $respon->tps_responplpbataltujuanxml_pk;

        foreach ($details as $detail):     
            $respon_detail = new \App\Models\TpsResponBatalPlpDetail;
            $respon_detail->tps_responplpbataltujuanxml_fk = $plp_id;
            $respon_detail->NO_PLP = $respon->NO_PLP;
            $respon_detail->TGL_PLP = $respon->TGL_PLP;
            $respon_detail->NO_BATAL_PLP = $respon->NO_BATAL_PLP;
            $respon_detail->TGL_BATAL_PLP = $respon->TGL_BATAL_PLP;
            foreach($detail as $key=>$value):
                $respon_detail->$key = $value;
            endforeach;
            $respon_detail->save();
        endforeach;
        
        return back()->with('success', 'Get Respon Batal PLP has been success.');
    }
    
    public function GetResponBatalPLP_onDemand(Request $request)
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetResponBatalPlp_onDemands')
                ->wsdl($this->wsdl)
                ->trace(true)   
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)  
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                    
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'KdGudang' => $this->kode,
            'NoBatalplp' => $request->no_batalplp,
            'TglBatalplp' => date('dmY', strtotime($request->tgl_batalplp)),
            'RefNumber' => $request->refnumber
        ];
        
        try{
            \SoapWrapper::service('TpsOnline_GetResponBatalPlp_onDemands', function ($service) use ($data) {        
                $this->response = $service->call('GetResponBatalPlp_onDemands', [$data])->GetResponBatalPlp_onDemandsResult;      
            });
        }catch (\SoapFault $exception){
            var_dump($exception);
        }
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml  || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        $header = array();
        $details = [];
        foreach($xml->children() as $child) {
            foreach($child as $key => $value) {
                if($key == 'header' || $key == 'HEADER'){
                    $header[] = $value;
                }else{
                    foreach ($value as $detail):
                        $details[] = $detail;
                    endforeach;
                }
            }
        }
        
        // INSERT DATA
        $respon = new \App\Models\TpsResponBatalPlp;
        foreach ($header[0] as $key=>$value):
            $respon->$key = $value;
        endforeach;
        $respon->LASTUPDATE = date('Y-m-d H:i:s');
        $respon->save();
        
        $plp_id = $respon->tps_responplpbataltujuanxml_pk;

        foreach ($details as $detail):     
            $respon_detail = new \App\Models\TpsResponBatalPlpDetail;
            $respon_detail->tps_responplpbataltujuanxml_fk = $plp_id;
            $respon_detail->NO_PLP = $respon->NO_PLP;
            $respon_detail->TGL_PLP = $respon->TGL_PLP;
            $respon_detail->NO_BATAL_PLP = $respon->NO_BATAL_PLP;
            $respon_detail->TGL_BATAL_PLP = $respon->TGL_BATAL_PLP;
            foreach($detail as $key=>$value):
                $respon_detail->$key = $value;
            endforeach;
            $respon_detail->save();
        endforeach;
        
        return back()->with('success', 'Get Respon Batal PLP On Demand has been success.');
    }
    
    public function GetOB()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                    
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_ASP' => $this->kode
        ];
        
        
        try{
            \SoapWrapper::service('TpsOnline', function ($service) use ($data) {        
                $this->response = $service->call('GetDataOB', [$data])->GetDataOBResult;      
            });
        }catch (\SoapFault $exception){
            var_dump($exception);
        }
        
//        var_dump($this->response);
//        
//        return false;
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        $ob = array();
        foreach($xml->children() as $child) {
            $ob[] = $child;
        }
        
        // INSERT DATA       
        foreach ($ob as $data):
            $obinsert = new \App\Models\TpsOb;
            foreach ($data as $key=>$value):
                if($key == 'KODE_KANTOR' || $key == 'kode_kantor'){ $key='KD_KANTOR'; }
                $obinsert->$key = $value;
            endforeach;
            $obinsert->save();
        endforeach;
        
        return back()->with('success', 'Get OB has been success.');
        
    }
    
    public function GetSPJM()
    {     
//        $context = stream_context_create(array(
//                'ssl' => array(
//                    'verify_peer' => false,
//                    'verify_peer_name' => false,
//                    'allow_self_signed' => true
//                )
//        ));

//        $client = new \SoapClient($this->wsdl, array('stream_context' => $context));
        
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetSPJM')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate(url('cert/bc.pem'))  
//                ->certificate(url('cert/tpsonlinebc.crt')) 
//                ->certificate(url('cert/trust-ca.crt')) 
//                ->cache(WSDL_CACHE_NONE)
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_Tps' => $this->kode
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetSPJM', function ($service) use ($data) {        
            $this->response = $service->call('GetSPJM', [$data])->GetSPJMResult;      
        });
        
//        $this->response = $client->__soapCall("GetSPJM", array($data));
        
//        var_dump($this->response);
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        foreach($xml->children() as $child) {
            $header = array();
            $kms = [];
            $dok = [];
            $cont = [];
            foreach($child as $key => $value) {
                if($key == 'header' || $key == 'HEADER'){
                    $header[] = $value;
                }else{
                    foreach ($value as $key => $value):
                        if($key == 'kms' || $key == 'KMS'):
                            $kms[] = $value;
                        elseif($key == 'dok' || $key == 'DOC'):
                            $dok[] = $value;
                        elseif($key == 'cont' || $key == 'CONT'):
                            $cont[] = $value;
                        endif;
                    endforeach;
                }
            }
            
            if(count($header) > 0){
                // INSERT DATA
                $spjm = new \App\Models\TpsSpjm;
                foreach ($header[0] as $key=>$value):
                    if($key == 'tgl_pib' || $key == 'tgl_bc11'){
                        $split_val = explode('/', $value);
                        $value = $split_val[2].'-'.$split_val[1].'-'.$split_val[0];
                    }
                    $spjm->$key = $value;
                endforeach;  
                $spjm->TGL_UPLOAD = date('Y-m-d');
                $spjm->JAM_UPLOAD = date('H:i:s');
                
                // CHECK DATA
                $check = \App\Models\TpsSpjm::where('CAR', $spjm->car)->count();
                if($check > 0) { continue; }

                $spjm->save();   

                $spjm_id = $spjm->TPS_SPJMXML_PK;

                if(count($kms) > 0){
                    $datakms = array();
                    foreach ($kms[0] as $key=>$value):
                        $datakms[$key] = $value;
                    endforeach;
                    $datakms['TPS_SPJMXML_FK'] = $spjm_id;
                    \DB::table('tps_spjmkmsxml')->insert($datakms);
                }
                if(count($dok) > 0){
                    $datadok = array();
                    foreach ($dok[0] as $key=>$value):
                        $datadok[$key] = $value;
                    endforeach;
                    $datadok['TPS_SPJMXML_FK'] = $spjm_id;
                    \DB::table('tps_spjmdokxml')->insert($datadok);
                }
                if(count($cont) > 0){
                    $datacont = array();
                    foreach ($cont[0] as $key=>$value):
                        $datacont[$key] = $value;
                    endforeach;
                    $datacont['TPS_SPJMXML_FK'] = $spjm_id;
                    \DB::table('tps_spjmcontxml')->insert($datacont);
                }
            }
        }
        
        return back()->with('success', 'Get SPJM has been success.');
        
    }
    
    public function GetSPJM_onDemand(Request $request)
    {     
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetSPJM_onDemand')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate(url('cert/bc.pem'))  
//                ->certificate(url('cert/tpsonlinebc.crt')) 
//                ->certificate(url('cert/trust-ca.crt')) 
//                ->cache(WSDL_CACHE_NONE)
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'noPib' => $request->no_pib,
            'tglPib' => $request->tgl_pib,
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetSPJM_onDemand', function ($service) use ($data) {        
            $this->response = $service->call('GetSPJM_onDemand', [$data])->GetSPJM_onDemandResult;      
        });
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        foreach($xml->children() as $child) {
            $header = array();
            $kms = [];
            $dok = [];
            $cont = [];
            foreach($child as $key => $value) {
                if($key == 'header' || $key == 'HEADER'){
                    $header[] = $value;
                }else{
                    foreach ($value as $key => $value):
                        if($key == 'kms' || $key == 'KMS'):
                            $kms[] = $value;
                        elseif($key == 'dok' || $key == 'DOC'):
                            $dok[] = $value;
                        elseif($key == 'cont' || $key == 'CONT'):
                            $cont[] = $value;
                        endif;
                    endforeach;
                }
            }
            
            if(count($header) > 0){
                // INSERT DATA
                $spjm = new \App\Models\TpsSpjm;
                foreach ($header[0] as $key=>$value):
                    if($key == 'tgl_pib' || $key == 'tgl_bc11'){
                        $split_val = explode('/', $value);
                        $value = $split_val[2].'-'.$split_val[1].'-'.$split_val[0];
                    }
                    $spjm->$key = $value;
                endforeach;  
                $spjm->TGL_UPLOAD = date('Y-m-d');
                $spjm->JAM_UPLOAD = date('H:i:s');
                
                // CHECK DATA
                $check = \App\Models\TpsSpjm::where('CAR', $spjm->car)->count();
                if($check > 0) { continue; }

                $spjm->save();   

                $spjm_id = $spjm->TPS_SPJMXML_PK;

                if(count($kms) > 0){
                    $datakms = array();
                    foreach ($kms[0] as $key=>$value):
                        $datakms[$key] = $value;
                    endforeach;
                    $datakms['TPS_SPJMXML_FK'] = $spjm_id;
                    \DB::table('tps_spjmkmsxml')->insert($datakms);
                }
                if(count($dok) > 0){
                    $datadok = array();
                    foreach ($dok[0] as $key=>$value):
                        $datadok[$key] = $value;
                    endforeach;
                    $datadok['TPS_SPJMXML_FK'] = $spjm_id;
                    \DB::table('tps_spjmdokxml')->insert($datadok);
                }
                if(count($cont) > 0){
                    $datacont = array();
                    foreach ($cont[0] as $key=>$value):
                        $datacont[$key] = $value;
                    endforeach;
                    $datacont['TPS_SPJMXML_FK'] = $spjm_id;
                    \DB::table('tps_spjmcontxml')->insert($datacont);
                }
            }
        }
        
        return back()->with('success', 'Get SPJM has been success.');
        
    }
    
    public function GetImporPermit_FASP()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                    
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_ASP' => $this->kode
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline', function ($service) use ($data) {        
            $this->response = $service->call('GetImporPermit_FASP', [$data])->GetImporPermit_FASPResult;      
        });
        
//        var_dump($this->response);
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        foreach ($xml->children() as $data):  
            foreach ($data as $key=>$value):
                if($key == 'HEADER' || $key == 'header'){           
                    $sppb = new \App\Models\TpsSppbPib;
                    foreach ($value as $keyh=>$valueh):
                        if($keyh == 'TG_BL_AWB' || $keyh == 'tg_bl_awb'){ $keyh='TGL_BL_AWB'; }
                        elseif($keyh == 'TG_MASTER_BL_AWB' || $keyh == 'tg_master_bl_awb'){ $keyh='TGL_MASTER_BL_AWB'; }
                        $sppb->$keyh = $valueh;
                    endforeach;
                    $sppb->save();
                    $sppb_id = $sppb->TPS_SPPBXML_PK;
                }elseif($key == 'DETIL' || $key == 'detil'){
                    foreach ($value as $key1=>$value1):
                        if($key1 == 'KMS' || $key1 == 'kms'){
                            $kms = new \App\Models\TpsSppbPibKms;
                            foreach ($value1 as $keyk=>$valuek):
                                $kms->$keyk = $valuek;
                            endforeach;
                            $kms->TPS_SPPBXML_FK = $sppb_id;
                            $kms->save();
                        }elseif($key1 == 'CONT' || $key1 == 'cont'){
                            $cont = new \App\Models\TpsSppbPibCont;
                            foreach ($value1 as $keyc=>$valuec):
                                $cont->$keyc = $valuec;
                            endforeach;
                            $cont->TPS_SPPBXML_FK = $sppb_id;
                            $cont->save();
                        }
                    endforeach;  
                }
            endforeach;
        endforeach;
        
        return back()->with('success', 'Get SPPB PIB has been success.');
        
    }
    
    public function GetImporPermit()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                    
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_Gudang' => $this->kode
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline', function ($service) use ($data) {        
            $this->response = $service->call('GetImporPermit', [$data])->GetImporPermitResult;      
        });
        
//        var_dump($this->response);
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        foreach ($xml->children() as $data):  
            foreach ($data as $key=>$value):
                if($key == 'HEADER' || $key == 'header'){           
                    $sppb = new \App\Models\TpsSppbPib;
                    foreach ($value as $keyh=>$valueh):
                        if($keyh == 'TG_BL_AWB' || $keyh == 'tg_bl_awb'){ $keyh='TGL_BL_AWB'; }
                        elseif($keyh == 'TG_MASTER_BL_AWB' || $keyh == 'tg_master_bl_awb'){ $keyh='TGL_MASTER_BL_AWB'; }
                        $sppb->$keyh = $valueh;
                    endforeach;
                    $sppb->save();
                    $sppb_id = $sppb->TPS_SPPBXML_PK;
                }elseif($key == 'DETIL' || $key == 'detil'){
                    foreach ($value as $key1=>$value1):
                        if($key1 == 'KMS' || $key1 == 'kms'){
                            $kms = new \App\Models\TpsSppbPibKms;
                            foreach ($value1 as $keyk=>$valuek):
                                $kms->$keyk = $valuek;
                            endforeach;
                            $kms->TPS_SPPBXML_FK = $sppb_id;
                            $kms->save();
                        }elseif($key1 == 'CONT' || $key1 == 'cont'){
                            $cont = new \App\Models\TpsSppbPibCont;
                            foreach ($value1 as $keyc=>$valuec):
                                $cont->$keyc = $valuec;
                            endforeach;
                            $cont->TPS_SPPBXML_FK = $sppb_id;
                            $cont->save();
                        }
                    endforeach;  
                }
            endforeach;
        endforeach;
        
        return back()->with('success', 'Get SPPB PIB has been success.');
        
    }
    

    public function GetImpor_SPPB(Request $request)
    {
        $jenis_dok = $request->jenis_dok;
//        return $request->all();
        if ($jenis_dok === "1") {
    # code...

        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                   
        });
        
		
			if($request->thn_sppb<='2022'){
			$sppbkode = '/KPU.01/';
            }else{
			$sppbkode = '/KPU.1/';            
			}
		
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
			'No_Sppb' => $request->No_Sppb, //063484/KPU.01/2017	
			'Tgl_Sppb' => $request->Tgl_Sppb, //09022017
            'NPWP_Imp' => $request->NPWP_IMB //033153321035000
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline', function ($service) use ($data) {        
            $this->response = $service->call('GetImpor_Sppb', [$data])->GetImpor_SppbResult;      
        });
        
//        var_dump($this->response);
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ditemukan',
            ]);
        }
        
        foreach ($xml->children() as $data):  
            foreach ($data as $key=>$value):
                if($key == 'HEADER' || $key == 'header'){           
                    $sppb = new \App\Models\TpsSppbPib;
                    foreach ($value as $keyh=>$valueh):
                        if($keyh == 'TG_BL_AWB' || $keyh == 'tg_bl_awb'){ $keyh='TGL_BL_AWB'; }
                        elseif($keyh == 'TG_MASTER_BL_AWB' || $keyh == 'tg_master_bl_awb'){ $keyh='TGL_MASTER_BL_AWB'; }
                        $sppb->$keyh = $valueh;
                    endforeach;
                    $sppb->save();
                    $sppb_id = $sppb->TPS_SPPBXML_PK;
                }elseif($key == 'DETIL' || $key == 'detil'){
                    foreach ($value as $key1=>$value1):
                        if($key1 == 'KMS' || $key1 == 'kms'){
                            $kms = new \App\Models\TpsSppbPibKms;
                            foreach ($value1 as $keyk=>$valuek):
                                $kms->$keyk = $valuek;
                            endforeach;
                            $kms->TPS_SPPBXML_FK = $sppb_id;
                            $kms->save();
                        }elseif($key1 == 'CONT' || $key1 == 'cont'){
                            $cont = new \App\Models\TpsSppbPibCont;
                            foreach ($value1 as $keyc=>$valuec):
                                $cont->$keyc = $valuec;
                            endforeach;
                            $cont->TPS_SPPBXML_FK = $sppb_id;
                            $cont->save();
                        }
                    endforeach;  
                }
            endforeach;
        endforeach;
        
        return response()->json([
            'success' => 400,
            'message' => 'Data Ditemukan',
           
          ]);
    }else {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetSppb_Bc23')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'No_Sppb' => $request->No_Sppb, //063484/KPU.01/2017	
			'Tgl_Sppb' => $request->Tgl_Sppb, //09022017
            'NPWP_Imp' => $request->NPWP_IMB //033153321035000
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetSppb_Bc23', function ($service) use ($data) {        
            $this->response = $service->call('GetSppb_Bc23', [$data])->GetSppb_Bc23Result;      
        });
        
//        var_dump($this->response);
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ditemukan',
            ]);
        }
        
        foreach ($xml->children() as $data):  
            foreach ($data as $key=>$value):
                if($key == 'HEADER' || $key == 'header'){           
                    $sppb = new \App\Models\TpsSppbBc;
                    foreach ($value as $keyh=>$valueh):
                        if($keyh == 'TG_BL_AWB' || $keyh == 'tg_bl_awb'){ $keyh='TGL_BL_AWB'; }
                        elseif($keyh == 'TG_MASTER_BL_AWB' || $keyh == 'tg_master_bl_awb'){ $keyh='TGL_MASTER_BL_AWB'; }
                        elseif($keyh == 'BRUTTO' || $keyh == 'brutto'){ $keyh='BRUTO'; }
                        $sppb->$keyh = $valueh;
                    endforeach;
                    $sppb->save();
                    $sppb_id = $sppb->TPS_SPPBXML_PK;
                }elseif($key == 'DETIL' || $key == 'detil'){
                    foreach ($value as $key1=>$value1):
                        if($key1 == 'KMS' || $key == 'kms'){
                            $kms = new \App\Models\TpsSppbBcKms;
                            foreach ($value1 as $keyk=>$valuek):
                                $kms->$keyk = $valuek;
                            endforeach;
                            $kms->TPS_SPPBXML_FK = $sppb_id;
                            $kms->save();
                        }elseif($key1 == 'CONT' || $key == 'cont'){
                            $cont = new \App\Models\TpsSppbBcCont;
                            foreach ($value1 as $keyc=>$valuec):
                                $cont->$keyc = $valuec;
                            endforeach;
                            $cont->TPS_SPPBXML_FK = $sppb_id;
                            $cont->save();
                        }
                    endforeach;  
                }
            endforeach;
        endforeach;
        
        return response()->json([
            'success' => 400,
            'message' => 'Data Ditemukan',
           
          ]);
    }
    }
    
    public function GetBC23Permit()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_Gudang' => $this->kode
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline', function ($service) use ($data) {        
            $this->response = $service->call('GetBC23Permit', [$data])->GetBC23PermitResult;      
        });
        
//        var_dump($this->response);
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        foreach ($xml->children() as $data):  
            foreach ($data as $key=>$value):
                if($key == 'HEADER' || $key == 'header'){           
                    $sppb = new \App\Models\TpsSppbBc;
                    foreach ($value as $keyh=>$valueh):
                        if($keyh == 'TG_BL_AWB' || $keyh == 'tg_bl_awb'){ $keyh='TGL_BL_AWB'; }
                        elseif($keyh == 'TG_MASTER_BL_AWB' || $keyh == 'tg_master_bl_awb'){ $keyh='TGL_MASTER_BL_AWB'; }
                        elseif($keyh == 'BRUTTO' || $keyh == 'brutto'){ $keyh='BRUTO'; }
                        $sppb->$keyh = $valueh;
                    endforeach;
                    $sppb->save();
                    $sppb_id = $sppb->TPS_SPPBXML_PK;
                }elseif($key == 'DETIL' || $key == 'detil'){
                    foreach ($value as $key1=>$value1):
                        if($key1 == 'KMS' || $key == 'kms'){
                            $kms = new \App\Models\TpsSppbBcKms;
                            foreach ($value1 as $keyk=>$valuek):
                                $kms->$keyk = $valuek;
                            endforeach;
                            $kms->TPS_SPPBXML_FK = $sppb_id;
                            $kms->save();
                        }elseif($key1 == 'CONT' || $key == 'cont'){
                            $cont = new \App\Models\TpsSppbBcCont;
                            foreach ($value1 as $keyc=>$valuec):
                                $cont->$keyc = $valuec;
                            endforeach;
                            $cont->TPS_SPPBXML_FK = $sppb_id;
                            $cont->save();
                        }
                    endforeach;  
                }
            endforeach;
        endforeach;
        
        return back()->with('success', 'Get SPPB BC23 has been success.');
        
    }
    
    public function GetBC23Permit_FASP()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_ASP' => $this->kode
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline', function ($service) use ($data) {        
            $this->response = $service->call('GetBC23Permit_FASP', [$data])->GetBC23Permit_FASPResult;      
        });
        
//        var_dump($this->response);
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        foreach ($xml->children() as $data):  
            foreach ($data as $key=>$value):
                if($key == 'HEADER' || $key == 'header'){           
                    $sppb = new \App\Models\TpsSppbBc;
                    foreach ($value as $keyh=>$valueh):
                        if($keyh == 'TG_BL_AWB' || $keyh == 'tg_bl_awb'){ $keyh='TGL_BL_AWB'; }
                        elseif($keyh == 'TG_MASTER_BL_AWB' || $keyh == 'tg_master_bl_awb'){ $keyh='TGL_MASTER_BL_AWB'; }
                        elseif($keyh == 'BRUTTO' || $keyh == 'brutto'){ $keyh='BRUTO'; }
                        $sppb->$keyh = $valueh;
                    endforeach;
                    $sppb->save();
                    $sppb_id = $sppb->TPS_SPPBXML_PK;
                }elseif($key == 'DETIL' || $key == 'detil'){
                    foreach ($value as $key1=>$value1):
                        if($key1 == 'KMS' || $key == 'kms'){
                            $kms = new \App\Models\TpsSppbBcKms;
                            foreach ($value1 as $keyk=>$valuek):
                                $kms->$keyk = $valuek;
                            endforeach;
                            $kms->TPS_SPPBXML_FK = $sppb_id;
                            $kms->save();
                        }elseif($key1 == 'CONT' || $key == 'cont'){
                            $cont = new \App\Models\TpsSppbBcCont;
                            foreach ($value1 as $keyc=>$valuec):
                                $cont->$keyc = $valuec;
                            endforeach;
                            $cont->TPS_SPPBXML_FK = $sppb_id;
                            $cont->save();
                        }
                    endforeach;  
                }
            endforeach;
        endforeach;
        
        return back()->with('success', 'Get SPPB BC23 has been success.');
        
    }
    
    public function GetSppb_Bc23(Request $request)
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetSppb_Bc23')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'No_Sppb' => $request->no_sppb,
            'Tgl_Sppb' => $request->tgl_sppb, //09022017
            'NPWP_Imp' => $request->npwp_imp //033153321035000
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetSppb_Bc23', function ($service) use ($data) {        
            $this->response = $service->call('GetSppb_Bc23', [$data])->GetSppb_Bc23Result;      
        });
        
//        var_dump($this->response);
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        foreach ($xml->children() as $data):  
            foreach ($data as $key=>$value):
                if($key == 'HEADER' || $key == 'header'){           
                    $sppb = new \App\Models\TpsSppbBc;
                    foreach ($value as $keyh=>$valueh):
                        if($keyh == 'TG_BL_AWB' || $keyh == 'tg_bl_awb'){ $keyh='TGL_BL_AWB'; }
                        elseif($keyh == 'TG_MASTER_BL_AWB' || $keyh == 'tg_master_bl_awb'){ $keyh='TGL_MASTER_BL_AWB'; }
                        elseif($keyh == 'BRUTTO' || $keyh == 'brutto'){ $keyh='BRUTO'; }
                        $sppb->$keyh = $valueh;
                    endforeach;
                    $sppb->save();
                    $sppb_id = $sppb->TPS_SPPBXML_PK;
                }elseif($key == 'DETIL' || $key == 'detil'){
                    foreach ($value as $key1=>$value1):
                        if($key1 == 'KMS' || $key == 'kms'){
                            $kms = new \App\Models\TpsSppbBcKms;
                            foreach ($value1 as $keyk=>$valuek):
                                $kms->$keyk = $valuek;
                            endforeach;
                            $kms->TPS_SPPBXML_FK = $sppb_id;
                            $kms->save();
                        }elseif($key1 == 'CONT' || $key == 'cont'){
                            $cont = new \App\Models\TpsSppbBcCont;
                            foreach ($value1 as $keyc=>$valuec):
                                $cont->$keyc = $valuec;
                            endforeach;
                            $cont->TPS_SPPBXML_FK = $sppb_id;
                            $cont->save();
                        }
                    endforeach;  
                }
            endforeach;
        endforeach;
        
        return back()->with('success', 'Get SPPB BC23 has been success.');
        
    }
    
    public function GetInfoNomorBc(Request $request)
    {
//        return $request->all();
        
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetInfoNomorBC11')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                                                                 
//                ->cache(WSDL_CACHE_NONE)
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]); 
        });
        
        $data = [
            'Username' => $this->user, 
            'Password' => $this->password,
            'TglTibaAwal' => date('dmY', strtotime($request->TglTibaAwal)),
            'TglTibaAkhir' => date('dmY', strtotime($request->TglTibaAkhir))
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetInfoNomorBC11', function ($service) use ($data) {        
            $this->response = $service->call('GetInfoNomorBC11', [$data])->GetInfoNomorBC11Result;      
        });

        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
//        var_dump($xml->children());
        
        foreach ($xml->children() as $data):  
            $info = new \App\Models\TpsGetInfoNomorBc;
            foreach ($data as $key=>$value):  
                $info->$key = $value;
                $info->save();
            endforeach;
        endforeach;
        
        return back()->with('success', 'Get Info Nomor BC11 has been success.');
    }


    public function GetDokumenManual()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetDokumenManual')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_Tps' => $this->kode
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetDokumenManual', function ($service) use ($data) {        
            $this->response = $service->call('GetDokumenManual', [$data])->GetDokumenManualResult;      
        });
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        $docmanual_id = 0;
        foreach ($xml->children() as $data):  
            foreach ($data as $key=>$value):
                if($key == 'HEADER' || $key == 'header'){           
                    $docmanual = new \App\Models\TpsDokManual;
                    foreach ($value as $keyh=>$valueh):
                        if($keyh == 'tg_bl_awb' || $keyh == 'TG_BL_AWB'){ $keyh='TGL_BL_AWB'; }
                        $docmanual->$keyh = $valueh;
                    endforeach;
                    $docmanual->TGL_UPLOAD = date('Y-m-d');
                    $docmanual->JAM_UPLOAD = date('H:i:s');
                    $docmanual->save();
                    $docmanual_id = $docmanual->TPS_DOKMANUALXML_PK;
                }elseif($key == 'DETIL' || $key == 'detil'){
                    foreach ($value as $key1=>$value1):
                        if($key1 == 'KMS' || $key1 == 'kms'){
                            $kms = new \App\Models\TpsDokManualKms;
                            foreach ($value1 as $keyk=>$valuek):
                                $kms->$keyk = $valuek;
                            endforeach;
                            $kms->TPS_DOKMANUALXML_FK = $docmanual_id;
                            $kms->save();
                        }elseif($key1 == 'CONT' || $key1 == 'cont'){
                            $cont = new \App\Models\TpsDokManualCont;
                            foreach ($value1 as $keyc=>$valuec):
                                $cont->$keyc = $valuec;
                            endforeach;
                            $cont->TPS_DOKMANUALXML_FK = $docmanual_id;
                            $cont->save();
    }
                    endforeach;  
                }
            endforeach;
        endforeach;
    
        return back()->with('success', 'Get Dokumen Manual has been success.');
        
    }
    
    public function GetDokumenManual_OnDemand(Request $request)
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetDokumenManual_OnDemand')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => 'MAL0', 
            'Password' => 'MAL0',
            'KdDok' => $request->kode_dok,
            'NoDok' => $request->no_dok,
            'TglDok' =>$request->tgl_dok
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetDokumenManual_OnDemand', function ($service) use ($data) {        
            $this->response = $service->call('GetDokumenManual_OnDemand', [$data])->GetDokumenManual_OnDemandResult;      
        });
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ditemukan',
            ]);
        }
        
        $docmanual_id = 0;
        foreach ($xml->children() as $data):  
            foreach ($data as $key=>$value):
                if($key == 'HEADER' || $key == 'header'){           
                    $docmanual = new \App\Models\TpsDokManual;
                    foreach ($value as $keyh=>$valueh):
                        if($keyh == 'tg_bl_awb' || $keyh == 'TG_BL_AWB'){ $keyh='TGL_BL_AWB'; }
                        $docmanual->$keyh = $valueh;
                    endforeach;
                    $docmanual->TGL_UPLOAD = date('Y-m-d');
                    $docmanual->JAM_UPLOAD = date('H:i:s');
                    $docmanual->save();
                    $docmanual_id = $docmanual->TPS_DOKMANUALXML_PK;
                }elseif($key == 'DETIL' || $key == 'detil'){
                    foreach ($value as $key1=>$value1):
                        if($key1 == 'KMS' || $key1 == 'kms'){
                            $kms = new \App\Models\TpsDokManualKms;
                            foreach ($value1 as $keyk=>$valuek):
                                $kms->$keyk = $valuek;
                            endforeach;
                            $kms->TPS_DOKMANUALXML_FK = $docmanual_id;
                            $kms->save();
                        }elseif($key1 == 'CONT' || $key1 == 'cont'){
                            $cont = new \App\Models\TpsDokManualCont;
                            foreach ($value1 as $keyc=>$valuec):
                                $cont->$keyc = $valuec;
                            endforeach;
                            $cont->TPS_DOKMANUALXML_FK = $docmanual_id;
                            $cont->save();
    }
                    endforeach;  
                }
            endforeach;
        endforeach;
    
        return response()->json([
            'success' => 400,
            'message' => 'Data Ditemukan',
            'data' =>$data,
          ]);
          if ($cont->isEmpty()) {
            \SoapWrapper::add(function ($service) {
                $service
                    ->name('TpsOnline_GetDokumenPabean_OnDemand')
                    ->wsdl($this->wsdl)
                    ->trace(true)                                                                                                  
    //                ->certificate()                                                 
    //                ->cache(WSDL_CACHE_NONE)                                        
                    ->options([
                        'stream_context' => stream_context_create([
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        ])
                    ]);                                                     
            });
            
            $data = [
                'UserName' => 'MAL0', 
                'Password' => 'MAL0',
                'KdDok' => $request->kode_dok,
                'NoDok' => $request->no_dok,
                'TglDok' =>$request->tgl_dok
            ];
            
            // Using the added service
            \SoapWrapper::service('TpsOnline_GetDokumenPabean_OnDemand', function ($service) use ($data) {        
                $this->response = $service->call('GetDokumenPabean_OnDemand', [$data])->GetDokumenPabean_OnDemandResult;      
            });
            
    //        var_dump($this->response);return false;
            
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($this->response);
            if(!$xml || !$xml->children()){
                return response()->json([
                    'success' => false,
                    'message' => 'Data Tidak Ditemukan',
                ]);
            }
            
            $docmanual_id = 0;
            foreach ($xml->children() as $data):  
                foreach ($data as $key=>$value):
                    if($key == 'HEADER' || $key == 'header'){           
                        $docmanual = new \App\Models\TpsDokPabean;
                        foreach ($value as $keyh=>$valueh):
                            $docmanual->$keyh = $valueh;
                        endforeach;
                        $docmanual->TGL_UPLOAD = date('Y-m-d');
                        $docmanual->JAM_UPLOAD = date('H:i:s');
                        $docmanual->save();
                        $docmanual_id = $docmanual->TPS_DOKPABEANXML_PK;
                    }elseif($key == 'DETIL' || $key == 'detil'){
                        foreach ($value as $key1=>$value1):
                            if($key1 == 'KMS' || $key1 == 'kms'){
                                $kms = new \App\Models\TpsDokPabeanKms;
                                foreach ($value1 as $keyk=>$valuek):
                                    $kms->$keyk = $valuek;
                                endforeach;
                                $kms->TPS_DOKPABEANXML_FK = $docmanual_id;
                                $kms->save();
                            }elseif($key1 == 'CONT' || $key1 == 'cont'){
                                $cont = new \App\Models\TpsDokPabeanCont;
                                foreach ($value1 as $keyc=>$valuec):
                                    $cont->$keyc = $valuec;
                                endforeach;
                                $cont->TPS_DOKPABEANXML_FK = $docmanual_id;
                                $cont->save();
                            }
                        endforeach;  
                    }
                endforeach;
            endforeach;
        
            return response()->json([
                'success' => 400,
                'message' => 'Data Ditemukan',
               
              ]);
        }
        
    }
    
    public function GetDokumenPabean()
    {      
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetDokumenPabeanPermit_FASP')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_Tps' => $this->kode
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetDokumenPabeanPermit_FASP', function ($service) use ($data) {        
            $this->response = $service->call('GetDokumenPabeanPermit_FASP', [$data])->GetDokumenPabeanPermit_FASPResult;      
        });
        
//        var_dump($this->response);return false;
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        $docmanual_id = 0;
        foreach ($xml->children() as $data):  
            foreach ($data as $key=>$value):
                if($key == 'HEADER' || $key == 'header'){           
                    $docmanual = new \App\Models\TpsDokPabean;
                    foreach ($value as $keyh=>$valueh):
                        $docmanual->$keyh = $valueh;
                    endforeach;
                    $docmanual->TGL_UPLOAD = date('Y-m-d');
                    $docmanual->JAM_UPLOAD = date('H:i:s');
                    $docmanual->save();
                    $docmanual_id = $docmanual->TPS_DOKPABEANXML_PK;
                }elseif($key == 'DETIL' || $key == 'detil'){
                    foreach ($value as $key1=>$value1):
                        if($key1 == 'KMS' || $key1 == 'kms'){
                            $kms = new \App\Models\TpsDokPabeanKms;
                            foreach ($value1 as $keyk=>$valuek):
                                $kms->$keyk = $valuek;
                            endforeach;
                            $kms->TPS_DOKPABEANXML_FK = $docmanual_id;
                            $kms->save();
                        }elseif($key1 == 'CONT' || $key1 == 'cont'){
                            $cont = new \App\Models\TpsDokPabeanCont;
                            foreach ($value1 as $keyc=>$valuec):
                                $cont->$keyc = $valuec;
                            endforeach;
                            $cont->TPS_DOKPABEANXML_FK = $docmanual_id;
                            $cont->save();
                        }
                    endforeach;  
                }
            endforeach;
        endforeach;
    
        return back()->with('success', 'Get Dokumen Pabean has been success.');
    }
    
    public function GetDokumenPabean_OnDemand(Request $request)
    {      
        \SoapWrapper::add(function ($service) {
            $service
                ->name('TpsOnline_GetDokumenPabean_OnDemand')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'KdDok' => $request->kode_dok,
            'NoDok' => $request->no_dok,
            'TglDok' =>$request->tgl_dok
        ];
        
        // Using the added service
        \SoapWrapper::service('TpsOnline_GetDokumenPabean_OnDemand', function ($service) use ($data) {        
            $this->response = $service->call('GetDokumenPabean_OnDemand', [$data])->GetDokumenPabean_OnDemandResult;      
        });
        
//        var_dump($this->response);return false;
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ditemukan',
            ]);
        }
        
        $docmanual_id = 0;
        foreach ($xml->children() as $data):  
            foreach ($data as $key=>$value):
                if($key == 'HEADER' || $key == 'header'){           
                    $docmanual = new \App\Models\TpsDokPabean;
                    foreach ($value as $keyh=>$valueh):
                        $docmanual->$keyh = $valueh;
                    endforeach;
                    $docmanual->TGL_UPLOAD = date('Y-m-d');
                    $docmanual->JAM_UPLOAD = date('H:i:s');
                    $docmanual->save();
                    $docmanual_id = $docmanual->TPS_DOKPABEANXML_PK;
                }elseif($key == 'DETIL' || $key == 'detil'){
                    foreach ($value as $key1=>$value1):
                        if($key1 == 'KMS' || $key1 == 'kms'){
                            $kms = new \App\Models\TpsDokPabeanKms;
                            foreach ($value1 as $keyk=>$valuek):
                                $kms->$keyk = $valuek;
                            endforeach;
                            $kms->TPS_DOKPABEANXML_FK = $docmanual_id;
                            $kms->save();
                        }elseif($key1 == 'CONT' || $key1 == 'cont'){
                            $cont = new \App\Models\TpsDokPabeanCont;
                            foreach ($value1 as $keyc=>$valuec):
                                $cont->$keyc = $valuec;
                            endforeach;
                            $cont->TPS_DOKPABEANXML_FK = $docmanual_id;
                            $cont->save();
                        }
                    endforeach;  
                }
            endforeach;
        endforeach;
    
        return response()->json([
            'success' => 400,
            'message' => 'Data Ditemukan',
           
          ]);
    }
    
    public function GetRejectData()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('GetRejectData')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                   
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Kd_Tps' => $this->kode
        ];
        
        // Using the added service
        \SoapWrapper::service('GetRejectData', function ($service) use ($data) {        
            $this->response = $service->call('GetRejectData', [$data])->GetRejectDataResult;      
        });
        
        //var_dump($this->response);
       
       libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
           return back()->with('error', $this->response);
        }
        
        $ob = array();
        foreach($xml->children() as $child) {
            $reject[] = $child;
        }
        
        // INSERT DATA       
        foreach ($reject as $data):
            $rejectinsert = new \App\Models\TpsReject;
            foreach ($data as $key=>$value):
                //if($key == 'KODE_KANTOR' || $key == 'kode_kantor'){ $key='KD_KANTOR'; }
                $rejectinsert->$key = $value;
            endforeach;
			$rejectinsert->tgl_upload = date('Y-m-d');
			$rejectinsert->jam_upload = date('H:i:s');
            $rejectinsert->save();
        endforeach;
        
        return back()->with('success', 'Get data reject has been success.');


	   
    }
    
    public function CekDataGagalKirim(Request $request)
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('CekDataGagalKirim')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                    
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Tgl_Awal' => date('d-m-Y', strtotime($request->tgl_awal)),
            'Tgl_Akhir' => date('d-m-Y', strtotime($request->tgl_akhir))
        ];
        
        // Using the added service
        \SoapWrapper::service('CekDataGagalKirim', function ($service) use ($data) {        
            $this->response = $service->call('CekDataGagalKirim', [$data])->CekDataGagalKirimResult;      
        });
        
        var_dump($this->response);
        
    }
    
    public function CekDataTerkirim(Request $request)
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('CekDataTerkirim')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' => $this->user, 
            'Password' => $this->password,
            'Tgl_Awal' => date('d-m-Y', strtotime($request->tgl_awal)),
            'Tgl_Akhir' => date('d-m-Y', strtotime($request->tgl_akhir))
        ];
        
        // Using the added service
        \SoapWrapper::service('CekDataTerkirim', function ($service) use ($data) {        
            $this->response = $service->call('CekDataTerkirim', [$data])->CekDataTerkirimResult;      
        });
        
        var_dump($this->response);
        
    }
    
    public function postCoCoCont_Tes()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('CoCoCont_Tes')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'Username' => $this->user, 
            'Password' => $this->password,
            'fStream' => ''
        ];
        
        // Using the added service
        \SoapWrapper::service('CoCoCont_Tes', function ($service) use ($data) {        
            $this->response = $service->call('CoCoCont_Tes', [$data])->CoCoCont_TesResult;      
        });
        
        var_dump($this->response);
    }
    
    public function postCoCoKms_Tes()
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('CoCoKms_Tes')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                    
        });
        
        $data = [
            'Username' => $this->user, 
            'Password' => $this->password,
            'fStream' => ''
        ];
        
        // Using the added service
        \SoapWrapper::service('CoCoKms_Tes', function ($service) use ($data) {        
            $this->response = $service->call('CoCoKms_Tes', [$data])->CoCoKms_TesResult;      
        });
        
        var_dump($this->response);
    }
    
	//On Deman NPE
	
	public function GetEkspor_NPE(Request $request)
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('GetEkspor_NPE')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' 	=> $this->user, 
            'Password' 	=> $this->password,
			'No_PE' 	=> $request->NO_PE,
            'npwp' 	    => $request->NPWP_NPE,           
            'kdKantor' 	=>$request->KD_KANTOR
        ];
        
        // Using the added service
        \SoapWrapper::service('GetEkspor_NPE', function ($service) use ($data) {        
            $this->response = $service->call('GetEkspor_NPE', [$data])->GetEkspor_NPEResult;      
        });
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ditemukan',
            ]);
        }
        
        $docmanual_id = 0;
        foreach ($xml->children() as $data):  
            foreach ($data as $key=>$value):
                if($key == 'HEADER' || $key == 'header'){           
                   
                    foreach ($value as $keyh=>$valueh):
                        if($keyh == 'kd_kantor' || $keyh == 'KD_KANTOR'){ $KD_KANTOR=$valueh; }
                        if($keyh == 'no_daftar' || $keyh == 'NO_DAFTAR'){ $NO_DAFTAR=$valueh; }
                        if($keyh == 'tgl_daftar' || $keyh == 'TGL_DAFTAR'){ $TGL_DAFTAR=$valueh; }
                        if($keyh == 'nonpe' || $keyh == 'NONPE'){ $NONPE=$valueh; }
                        if($keyh == 'tglnpe' || $keyh == 'TGLNPE'){ $TGLNPE=$valueh; }
                        if($keyh == 'npwp_eks' || $keyh == 'NPWP_EKS'){ $NPWP_EKS=$valueh; }
                        if($keyh == 'nama_eks' || $keyh == 'NAMA_EKS'){ $NAMA_EKS=$valueh; }
                        if($keyh == 'fl_segel' || $keyh == 'FL_SEGEL'){ $FL_SEGEL=$valueh; }
                        
						//$docmanual->$keyh = $valueh;
                    endforeach;
                   
                }elseif($key == 'DETIL' || $key == 'detil'){
					
                    foreach ($value as $key1=>$value1):
                        $docmanual = new \App\Models\TpsDokNPE;
                        if($key1 == 'CONT' || $key1 == 'cont'){
					       //reset
                           $SERI_CONT = '';
                           $NO_CONT = '';
                           $SIZE = '';
                           
                           foreach ($value1 as $keyk=>$valuek):
						      if($keyk == 'SERI_CONT' || $keyk == 'seri_cont'){ $SERI_CONT=$valuek; }
						      if($keyk == 'NO_CONT' || $keyk == 'no_cont'){ $NO_CONT=$valuek; }
						      if($keyk == 'SIZE' || $keyk == 'size'){ $SIZE=$valuek; }
						   endforeach;
						      
						      //$docmanual->$keyk = $valuek;
                            /*
                             * SELECT `TPS_DOKNPE_PK`, `KD_KANTOR`, `NO_DAFTAR`, `NONPE`, `TGL_DAFTAR`, 
                             * `TGLNPE`, `NPWP_EKS`, `NAMA_EKS`, `FL_SEGEL`, `SERI_CONT`, `NO_CONT`, `SIZE`, 
                             * `TGL_UPLOAD`, `JAM_UPLOAD` FROM `tps_doknpexml` WHERE 1
                             */
						      if ( !empty($SERI_CONT)
						          && !empty($NO_CONT)
						          && !empty($SIZE) ) {
                              $docmanual->KD_KANTOR= $KD_KANTOR;
    						  $docmanual->NO_DAFTAR= $NO_DAFTAR;
    						  $docmanual->NONPE= $NONPE;
    						  $docmanual->TGL_DAFTAR= $TGL_DAFTAR;
    						  $docmanual->TGLNPE= $TGLNPE;
    						  $docmanual->NPWP_EKS= $NPWP_EKS;
    						  $docmanual->NAMA_EKS= $NAMA_EKS;
    						  $docmanual->FL_SEGEL= $FL_SEGEL;
    						  //container
    						  $docmanual->SERI_CONT= $SERI_CONT;
    						  $docmanual->NO_CONT= $NO_CONT;
    						  $docmanual->SIZE= $SIZE;
    						  //-_-
    						  $docmanual->TGL_UPLOAD = date('Y-m-d');
    						  $docmanual->JAM_UPLOAD = date('H:i:s');
    							
    						  $docmanual->save();
						      }
		              }
					endforeach;  
                }
            endforeach;
        endforeach;
    
        return response()->json([
            'success' => 400,
            'message' => 'Data Ditemukan',
           
          ]);
        
    }
	

    public function GetEkspor_PKBE(Request $request)
    {
        \SoapWrapper::add(function ($service) {
            $service
                ->name('GetEkspor_PKBE')
                ->wsdl($this->wsdl)
                ->trace(true)                                                                                                  
//                ->certificate()                                                 
//                ->cache(WSDL_CACHE_NONE)                                        
                ->options([
                    'stream_context' => stream_context_create([
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    ])
                ]);                                                     
        });
        
        $data = [
            'UserName' 	=> 'MAL0', 
            'Password' 	=> 'MAL0',
			'No_PKBE' 	=> $request->no_pkbe,
            'TGL_PKBE' 	    => $request->tgl_pkbe,           
            'kdKantor' 	=>$request->kd_kantor
        ];
        
        // Using the added service
        \SoapWrapper::service('GetEkspor_PKBE', function ($service) use ($data) {        
            $this->response = $service->call('GetEkspor_PKBE', [$data])->GetEkspor_PKBEResult;      
        });
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->response);
        if(!$xml || !$xml->children()){
            return response()->json([
                'success' => false,
                'message' => 'Data Tidak Ditemukan',
            ]);
        }
        
        \DB::listen(function ($query) {
            \Log::debug("Executed SQL: " . $query->sql);
            \Log::debug("Bindings: " . json_encode($query->bindings));
            \Log::debug("Time: " . $query->time . "ms");
        });
    
        $pkbe = new \App\Models\TpsDokPKBE;
        foreach ($xml->children() as $data):
            foreach ($data as $key => $value):
                if ($key == 'CAR' || $key == 'car') {
                        if($key == 'CAR' || $key == 'CAR')
                        { $CAR=$value; }
                              $pkbe->CAR= $CAR;
                    }
                if ($key == 'KD_KANTOR' || $key == 'KD_KANTOR') {
                    if($key == 'KD_KANTOR' || $key == 'KD_KANTOR')
                    { $KD_KANTOR=$value; }
                          $pkbe->KD_KANTOR= $KD_KANTOR;
                }
                if ($key == 'NOPKBE' || $key == 'NOPKBE') {
                    if($key == 'NOPKBE' || $key == 'NOPKBE')
                    { $NOPKBE=$value; }
                          $pkbe->NOPKBE= $NOPKBE;
                }
                if ($key == 'TGLPKBE' || $key == 'TGLPKBE') {
                    if($key == 'TGLPKBE' || $key == 'TGLPKBE')
                    { $TGLPKBE=$value; }
                          $pkbe->TGLPKBE= $TGLPKBE;
                }
                if ($key == 'NPWP_EKS' || $key == 'NPWP_EKS') {
                    if($key == 'NPWP_EKS' || $key == 'NPWP_EKS')
                    { $NPWP_EKS=$value; }
                          $pkbe->NPWP_EKS= $NPWP_EKS;
                }
                if ($key == 'NAMA_EKS' || $key == 'NAMA_EKS') {
                    if($key == 'NAMA_EKS' || $key == 'NAMA_EKS')
                    { $NAMA_EKS=$value; }
                          $pkbe->NAMA_EKS= $NAMA_EKS;
                }
                if ($key == 'NO_CONT' || $key == 'NO_CONT') {
                    if($key == 'NO_CONT' || $key == 'NO_CONT')
                    { $NO_CONT=$value; }
                          $pkbe->NO_CONT= $NO_CONT;
                }
                if ($key == 'SIZE' || $key == 'SIZE') {
                    if($key == 'SIZE' || $key == 'SIZE')
                    { $SIZE=$value; }
                          $pkbe->SIZE= $SIZE;
                }
            endforeach;
        endforeach;
        $pkbe->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Data Ditemukan',
            'data' => $data,
        ]);
        
    }
	
    public function postCoarriCodeco_Container()
    {
        
    }
    
    public function postCoarriCodeco_Kemasan()
    {
        
    }
    
    
}
