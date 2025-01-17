<!DOCTYPE html>
<html lang="en">
    
    <head>
        <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title> {{$title}} | Icon Sarana</title>
  <link rel="shortcut icon" href="{{asset('logo/icon.png')}}" type="image/x-icon">
  <link rel="shortcut icon" href="{{asset('logo/icon.png')}}" type="image/png">

  <style>
    /* Set ukuran kertas A5 landscape */
    @page {
      width: 137mm;
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
      width: 137mm;
      height: auto;
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

    .col-6 {
        width: 50%
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
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <!-- Logo -->
                                    <div class="col-4" style="border-right: 1px solid black;">
                                        <div class="img">
                                            <img src="/logo/IntiMandiri.png" class="img" alt="">
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <div class="text-center">
                                            <br>
                                            <span>PT. Inti Mandiri Utama Trans</span>
                                            <hr>
                                            <p>Consolidation Warehoude & Logistic</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="border-top: 1px solid black;">
                            <div class="col-12 text-center" style="line-height: 1.5;">
                                <span>CONSIGGNEE</span>
                            </div>
                        </div>
                        <div class="row" style="border-top: 1px solid black;">
                            <div class="col-12 text-center" style="line-height: 1.5;">
                                <span>{{$item->manifest->customer->name ?? ''}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-0 mb-0" style="border-top: 1px solid black;">
                    <div class="col-12 text-center" style="line-height: 1.5;">
                        <span>ADDRESS</span>
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
                    <div class="col-12" style="margin-left: 5px;">
                        <p style="font-size: 10px;">{{$item->manifest->customer->alamat ?? ''}}</p>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row" style="border-top: 1px solid black;">
                        <div class="col-45" style="border-right: 1px solid black;">
                            <p>Type</p>    
                        </div>
                        <div class="col-45 text-center" style="border-right: 1px solid black;">
                            <p>
                                {{$item->manifest->packing->code ?? ''}}
                            </p>
                        </div>
                        <div class="col-45" style="border-right: 1px solid black;">
                            <p>
                                Party
                            </p>
                        </div>
                        <div class="col-45 text-center">
                            <p>
                                {{$item->manifest->quantity}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="row">
                    <div class="col-12 text-center">
                        <span>ETA</span>
                    </div>
                </div>
                <hr>
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
                    <div class="col-8" style="border-right: 1px solid black;">
                        <div class="row">
                            <div class="col-6 text-center" style="border-right: 1px solid black;">
                                <p>Vessel</p>
                            </div>
                            <div class="col-6 text-center">
                                <p>HBL</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 text-center">                       
                        <p>Forwarding</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12" style="border-top: 1px solid black;">
                <div class="row">
                    <div class="col-8" style="border-right: 1px solid black;">
                        <div class="row">
                            <div class="col-6 text-center" style="border-right: 1px solid black;">
                                <p>{{$item->manifest->cont->job->Kapal->name ?? ''}}</p>
                            </div>
                            <div class="col-6 text-center">
                                <p>{{$item->manifest->nohbl ?? ''}} - {{$item->nomor}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <p>{{$item->manifest->cont->job->Forwarding->name ?? ''}}</p>
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