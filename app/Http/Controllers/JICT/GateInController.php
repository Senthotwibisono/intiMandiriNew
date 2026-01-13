<?php

namespace App\Http\Controllers\JICT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Container as ContL;
use App\Models\ContainerFCL as ContF;

class GateInController extends Controller
{
    public function getToken(Request $request)
    {
        // Payload sesuai yang kamu berikan
        $payload = [
            "tokenIn" => [
                "partnerId" => "263386",
                "passphrase" => "apiesealinti",
                "passkey" => "P@ssw0rd123",
                "appname" => "WMS",
                "appversion" => "2.0",
                "apprelease" => "PRD",
                "requestTime" => Carbon::now()->format('Y-m-d H:i:s'),
                "requestTimemilis" => round(microtime(true) * 1000)
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post(
                'https://ws.jict.co.id/antareja-api/pub/order/token',
                $payload
            );

            // Jika response gagal (HTTP != 2xx)
            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'status'  => $response->status(),
                    'message' => 'Request token gagal',
                    'response'=> $response->body(),
                ], $response->status());
            }

            // Ambil response JSON
            $result = $response->json();

            return response()->json([
                'success' => true,
                'data'    => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendContainer()
    {
        $containers = ContL::where('lokasisandar_id', 3)
            ->where('flag_jict', '!=', 'Y')
            ->whereNotNull('tglmasuk')
            ->get();
    
        if ($containers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Container tidak ditemukan'
            ], 404);
        }
    
        $results = [];
    
        foreach ($containers as $container) {
            $payload = [
                "auth" => [
                    "partnerId" => "263386",
                    "passphrase" => "apiesealinti",
                    "passkey" => "P@ssw0rd123",
                    "token" => "307C3641636C6B594F39346970576A78334F5634367750784F756E41646B4A6A583857575047464A44714739593D",
                    "appname" => "WMS",
                    "appversion" => "2.0",
                    "apprelease" => "PRD",
                    "requestTime" => now()->format('Y-m-d H:i:s'),
                    "requestTimemilis" => (string) round(microtime(true) * 1000),
                ],
                "dataGateIn" => [
                    "cntrId" => $container->nocontainer,
                    "customsDocNbr" => $container->job->noplp,
                    "customsDocDate" => $container->job->ttgl_plp,
                    "gateInDateTime" => Carbon::parse(
                        $container->tglmasuk . ' ' . $container->jammasuk
                    )->format('Y-m-d H:i:s')
                ]
            ];
    
            try {
                $response = Http::asJson()->post(
                    'https://ws.jict.co.id/antareja-api/pub/eseal/gatein/depo',
                    $payload
                );
    
                if ($response->failed()) {
                    $results[] = [
                        'container' => $container->nocontainer,
                        'status' => 'FAILED',
                        'response' => $response->json()
                    ];
                    continue; // lanjut container berikutnya
                }
    
                $container->update(['flag_jict' => 'Y']);
    
                $results[] = [
                    'container' => $container->nocontainer,
                    'status' => 'SUCCESS',
                    'response' => $response->json()
                ];
    
            } catch (\Exception $e) {
                $results[] = [
                    'container' => $container->nocontainer,
                    'status' => 'ERROR',
                    'message' => $e->getMessage()
                ];
            }
        }
    
        return response()->json([
            'success' => true,
            'total' => count($results),
            'results' => $results
        ]);
    }

    public function sendContainerFCL()
    {
        $containers = ContF::where('lokasisandar_id', 3)
            ->where('flag_jict', '!=', 'Y')
            ->whereNotNull('tglmasuk')
            ->get();

        if ($containers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Container tidak ditemukan'
            ], 404);
        }

        $results = [];

        foreach ($containers as $container) {
            $payload = [
                "auth" => [
                    "partnerId" => "263386",
                    "passphrase" => "apiesealinti",
                    "passkey" => "P@ssw0rd123",
                    "token" => "307C3641636C6B594F39346970576A78334F5634367750784F756E41646B4A6A583857575047464A44714739593D",
                    "appname" => "WMS",
                    "appversion" => "2.0",
                    "apprelease" => "PRD",
                    "requestTime" => now()->format('Y-m-d H:i:s'),
                    "requestTimemilis" => (string) round(microtime(true) * 1000),
                ],
                "dataGateIn" => [
                    "cntrId" => $container->nocontainer,
                    "customsDocNbr" => $container->job->noplp,
                    "customsDocDate" => $container->job->ttgl_plp,
                    "gateInDateTime" => Carbon::parse(
                        $container->tglmasuk . ' ' . $container->jammasuk
                    )->format('Y-m-d H:i:s')
                ]
            ];

            try {
                $response = Http::asJson()->post(
                    'https://ws.jict.co.id/antareja-api/pub/eseal/gatein/depo',
                    $payload
                );

                if ($response->failed()) {
                    $results[] = [
                        'container' => $container->nocontainer,
                        'status' => 'FAILED',
                        'response' => $response->json()
                    ];
                    continue; // lanjut container berikutnya
                }

                $container->update(['flag_jict' => 'Y']);

                $results[] = [
                    'container' => $container->nocontainer,
                    'status' => 'SUCCESS',
                    'response' => $response->json()
                ];

            } catch (\Exception $e) {
                $results[] = [
                    'container' => $container->nocontainer,
                    'status' => 'ERROR',
                    'message' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'total' => count($results),
            'results' => $results
        ]);
    }


    // public function sendContainerFCL()
    // {
    //     $containers = ContF::where('lokasisandar_id', 3)->whereNot('flag_jict', 'Y')->whereNotNull('tglmasuk')->get();
    //     if ($containers->isEmpty()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Container tidak ditemukan'
    //         ], 404);
    //     }
    //     foreach ($containers as $container) {
    //         $payload = [
    //             "auth"=> [
    //                 "partnerId" => "263386",
    //                 "passphrase" => "apiesealinti",
    //                 "passkey" => "P@ssw0rd123",
    //                 "token" => "307C3641636C6B594F39346970576A78334F5634367750784F756E41646B4A6A583857575047464A44714739593D",
    //                 "appname" => "WMS",
    //                 "appversion" => "2.0",
    //                 "apprelease" => "PRD",
    //                 "requestTime" => Carbon::now()->format('Y-m-d H:i:s'),
    //                 "requestTimemilis" =>(string) round(microtime(true) * 1000)
    //             ],
    //             "dataGateIn" => [
    //                 "cntrId"=> $container->nocontainer,
    //                 "customsDocNbr"=> $container->job->noplp,// customsdocnbr bukan bc 11
    //                 "customsDocDate"=> $container->job->ttgl_plp,// customsdocdate bukan bc 11
    //                 "gateInDateTime"=> Carbon::parse($container->tglmasuk . ' ' . $container->jammasuk)->format('Y-m-d H:i:s')
    //             ]
    //         ];
    //         // dd($payload);
    
    //          try {
    //             $response = Http::withHeaders([
    //                 'Content-Type'     => 'application/json',
    //                 // 'X-Partner-Id'     => '263386',
    //             ])->post(
    //                 'https://ws.jict.co.id/antareja-api/pub/eseal/gatein/depo',
    //                 $payload
    //             );
    
    //             // Jika response gagal (HTTP != 2xx)
    //             if ($response->failed()) {
    
    //                 $body = $response->json();
    
    //                 $message =
    //                     $body['responseMessage']
    //                     ?? $body['message']
    //                     ?? $body['errorMessage']
    //                     ?? $body['gateInOut']['responseMessage']
    //                     ?? 'Request ke JICT gagal';
    
    //                 return response()->json([
    //                     'success' => false,
    //                     'status'  => $response->status(),
    //                     'message' => $message,
    //                     'response'=> $body ?? $response->body(),
    //                 ], $response->status());
    //             }
    
    //             // Ambil response JSON
    //             $result = $response->json();
    //             $container->update([
    //                 'flag_jict' => 'Y'
    //             ]);
    //             return response()->json([
    //                 'success' => true,
    //                 'data'    => $result
    //             ]);
    
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => $e->getMessage()
    //             ], 500);
    //         }
    //     }

    // }
}
