<!DOCTYPE html>
<html lang="en">
    
    <head>
        <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title> {{$title}} | Icon Sarana</title>
  <link rel="stylesheet" href="{{asset('dist/assets/css/main/app.css')}}">
  <link rel="shortcut icon" href="{{asset('logo/icon.png')}}" type="image/x-icon">
  <link rel="shortcut icon" href="{{asset('logo/icon.png')}}" type="image/png">

  <style>
    /* Set ukuran kertas A5 landscape */
    @page {
      width: 120mm;
      height: 100mm;
      margin: 0cm; /* Tambahkan margin jika diperlukan */
    }

    body {
      
      font-family: Arial, sans-serif;
      font-size: 10pt; /* Ukuran font */
      margin: 0;
      line-height: 1; /* Spasi antar baris */
    }

    .card {
      width: 100%;
      height: 100%;
      margin: auto;
      border: 1px solid black;
    }

    .row {
      display: flex;
      flex-wrap: nowrap;

    }
    
    .col-8 {
        width: 80%; /* 8 dari 12 kolom */
    }
    
    .col-4 {
        width: 20%; /* 4 dari 12 kolom */
    }
    
    .col-45{
        width: 33.33%        
    }

    .col-12 {
        width: 100%; /* Full width */
    }

    .text-center {
      text-align: center;
    }

    .img {
        max-width: 100%;
        height: auto;
       
    }
    
    /* QR Code styling */
    .qr-code {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    </style>
</head>

@foreach($items as $index => $item)
<body>
    <div class="card">
        <!-- Bag Atas -->
        <div class="row" style="border-bottom: 1px solid black;">
            <div class="col-8" style="border-right: 1px solid black;">
                <div class="row">
                    <div class="col-4" style="border-right: 1px solid black;">
                        <div class="img">
                            <img src="/logo/IntiMandiri.png" class="img" alt="">
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    <br>
                                    <span>PT. Inti Mandiri Utama Trans</span>
                                    <hr>
                                    <p>Consolidation Warehoude & Logistic</p>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="border-top: 1px solid black;">
                            <div class="col-12">
                                <div class="text-center">
                                    <span>CONSIGGNEE</span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="border-top: 1px solid black;">
                            <div class="col-12">
                                <div class="text-center">
                                    <span>{{$item->manifest->customer->name ?? ''}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-0 mb-0" style="border-top: 1px solid black;">
                    <div class="col-12">
                        <div class="text-center">
                            <span>ADDRESS</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 qr-code">
                {!!QrCode::margin(0)->generate($item->barcode)!!}
            </div>
        </div>
    
        <!-- Bag Tengah -->
         <div class="row" style="border-bottom: 1px solid black;">
            <div class="col-8" style="border-right: 1px solid black;">
                <div class="row">
                    <div class="col-12">
                        <p>{{$item->manifest->customer->alamat ?? ''}}</p>
                    </div>
                </div>
                <div class="row" style="border-top: 1px solid black;">
                    <div class="col-3" style="border-right: 1px solid black;">
                        <p>Type</p>    
                    </div>
                    <div class="col-3" style="border-right: 1px solid black;">
                        <p>
                            {{$item->manifest->packing->code ?? ''}}
                        </p>
                    </div>
                    <div class="col-3" style="border-right: 1px solid black;">
                        Party
                    </div>
                    <div class="col-3">
                        {{$item->manifest->quantity}}
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="row">
                    <div class="col-12 text-center">
                        <span>ETA</span>
                    </div>
                </div>
                <div class="row">
                <div class="col-12 text-center">
                        <span>{{$item->manifest->cont->job->eta}}</span>
                    </div>
                </div>
            </div>
         </div>
    
         <!-- bawah -->
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-45 text-center" style="border-right: 1px solid black;">
                        <p>Vessel : {{$item->manifest->cont->job->Kapal->name ?? ''}}</p>
                    </div>
                    <div class="col-45 text-center" style="border-right: 1px solid black;">
                        <p>HBL : {{$item->manifest->nohbl ?? ''}} - {{$item->nomor}}</p>
                    </div>
                    <div class="col-45 text-center">
                        <p>Forwarding : {{$item->manifest->cont->job->Forwarding->name ?? ''}}</p>
                    </div>
                </div>
            </div>
        </div>
    
    </div>
    @if($index + 1 < count($items))
        <div style="page-break-after: always;"></div>
    @endif
</body>
<br>
@endforeach
</html>