<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title> {{$title}} | Inti Mandiri</title>
  <link rel="stylesheet" href="{{asset('dist/assets/css/main/app.css')}}">
  <link rel="shortcut icon" href="{{asset('logo/icon.png')}}" type="image/x-icon">
  <link rel="shortcut icon" href="{{asset('logo/icon.png')}}" type="image/png">
</head>


<style>
    @page {
      width: 100mm;
      height: auto;
      margin: 0cm; /* Tambahkan margin jika diperlukan */
    }
 .section {
      padding-top: 5%;
    }

    .card {
      margin-bottom: 20px;
    }

    .card-body {
      padding: 15px;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
      margin-right: -15px;
      margin-left: -15px;
    }

    .col-6 {
      flex: 0 0 50%;
      max-width: 50%;
      padding-right: 15px;
      padding-left: 15px;
    }
    body{
        font-family: 'Roboto Condensed', sans-serif;
    }
    .page-break {
                page-break-before: always;
            }

            .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            padding: 5px;
            font-size: 12px;
            background-color: #f9f9f9;
            border-top: 1px solid #ddd;
        }
    .m-0{
        margin: 0px;
    }
    .p-0{
        padding: 0px;
    }
    .pt-5{
        padding-top:5px;
    }
    .mt-10{
        margin-top:10px;
    }
    .text-center{
        text-align:center !important;
    }
    .w-100{
        width: 100%;
    }
    .w-50{
        width:50%;   
    }
    .w-85{
        width:85%;   
    }
    .w-15{
        width:15%;   
    }
    .logo img{
        width:50px;
        height:auto;
        padding-top:30px;
    }
    .logo span{
        margin-left:8px;
        top:19px;
        position: absolute;
        font-weight: bold;
        font-size:25px;
    }
    .gray-color{
        color:#5D5D5D;
    }
    .text-bold{
        font-weight: bold;
    }
    .border{
        border:1px solid black;
    }
    table tr,th,td{
        border: 1px solid #d2d2d2;
        border-collapse:collapse;
        padding:7px 8px;
    }
    table tr th{
        background: #F4F4F4;
        font-size:15px;
    }
    table tr td{
        font-size:13px;
    }
    table{
        border-collapse:collapse;
    }
    .box-text p{
        line-height:10px;
    }
    .float-left{
        float:left;
    }
    .total-part{
        font-size:16px;
        line-height:12px;
    }
    .total-right p{
        padding-right:20px;
    }

    .tier-container {
    display: flex;
    flex-wrap: fixed; /* Mengatur agar kontainer tier bisa terlipat jika ukurannya melebihi lebar kontainer induk */
    gap: 5px; /* Mengatur jarak antar kotak */
}
.kotak {
        height: 5vh; /* Mengurangi tinggi kotak menjadi 5% dari tinggi viewport */
        line-height: 5vh; /* Menyesuaikan line-height agar sama dengan tinggi kotak */
        font-size: 8px; 
        background-color: #fff;
        text-align: center;
        border: 2px solid #000000;
        flex: 1;
        margin: 0px;
        border-radius: 0px;
    }

    .kotak.filled {
        background-color: red;
        color: #fff;
    }
</style>

<!-- @foreach($items as $item)
    @foreach($tiers as $tier)
        @if($tier->rack_id == $item->id)
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                            <td><b>Code</b></td>
                            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                            <td>{{ $item->barcode }}</td>
                            <br>
                        {!!QrCode::margin(0)->size(500)->generate($tier->barcode)!!}
                    </div>
                    <br>
                    <div class="text-center">
                        <tr>
                            <td>No Rack  : {{$item->name}}</td>
                            <td> || </td>
                            <td>Tier : {{$tier->tier}}</td>
                        </tr>
                        <br>
                        <tr>
                            <td>Fungsi Rack</td>
                            <td> : </td>
                            <td>
                                @if($item->use_for == 'M')
                                    Multi use
                                @elseif($item->use_for == 'D')
                                    Danger Item
                                @elseif($item->use_for == 'B')
                                    Behandle Rack
                                @elseif($item->use_for == 'L')
                                    Long Stay
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach
@endforeach -->

@foreach($items as $item)
    @php
        $itemTiers = $tiers->where('rack_id', $item->id)->sortBy('tier')->values();
    @endphp

    @if($itemTiers->count())
    <div class="container mb-4">
        <div class="card">
            <div class="card-header">
                <h1>{{$item->name}}</h1>
            </div>
            <div class="card-body">
                {{-- Tampilkan Tier Dua-Dua --}}
                @foreach($itemTiers->chunk(2) as $chunk)
                    <div class="d-flex justify-content-center mb-3">
                        @foreach($chunk as $tier)
                            <div class="text-center mx-2" style="border: 1px solid black; padding: 15px; border-radius: 8px;">
                                <div class="text-center mb-3">
                                    <table style="margin: 0 auto;">
                                        <tr>
                                            <td><b>Code</b></td>
                                            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                                            <td>{{ $tier->barcode }}</td>
                                        </tr>
                                    </table>
                                    <br>
                                </div>
                                {!! QrCode::margin(0)->size(500)->generate($tier->barcode) !!}
                                <div class="mt-2">
                                    <div>No Rack: {{ $item->name }}</div>
                                    <div>Tier: {{ $tier->tier }}</div>
                                    <div>
                                        Fungsi Rack: 
                                        @if($item->use_for == 'M')
                                            Multi use
                                        @elseif($item->use_for == 'D')
                                            Danger Item
                                        @elseif($item->use_for == 'B')
                                            Behandle Rack
                                        @elseif($item->use_for == 'L')
                                            Long Stay
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

            </div>
        </div>
    </div>
    @endif
    <div class="page-break"></div>
@endforeach


</html>