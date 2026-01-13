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

<body>
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <div class="logo img me-3">
                                <img src="/logo/IntiMandiri.PNG" class="img-fluid" alt="" style="">
                            </div>
                            <div class="text-center flex-grow-1">
                                {{$title}}
                            </div>
                        </div>
                    </div>
                    {{strtoupper($manifest->nohbl).' - Ex. Container : '.strtoupper($manifest->cont->nocontainer)}}
                    <small class="pull-right"><strong>Stripping Date:</strong> {{ date('d F, Y', strtotime($manifest->tglstripping)) }}</small>
                    <small class="pull-right"><strong>Expired:</strong> {{ date('d F, Y', strtotime($manifest->active_to)) }}</small>
                </div>
                <div class="card-body">
                    <div class="text-center">
                            <td><b>Code</b></td>
                            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                            <td>{{ $manifest->barcode }}</td>
                            <br>
                        {!!QrCode::margin(0)->size(150)->generate($manifest->barcode)!!}
                    </div>
                    <br>
                    <div class="text-center">
                        <tr>
                            <td>Dokumen/No Dokumen/Tgl Dok</td>
                            <td> : </td>
                            <td>{{$manifest->dokumen->name ?? '-'}}/{{$manifest->no_dok}}/{{$manifest->tgl_dok}}</td>
                        </tr>
                        <tr>
                            <td>Consignee/NPWP/FAX/EMAIL</td>
                            <td> : </td>
                            <br>
                            <td>{{$manifest->Customer->name ?? ''}} / {{$manifest->Customer->npwp ?? ''}} / {{$manifest->Customer->fax ?? ''}} / {{$manifest->Customer->email ?? ''}}</td>
                        </tr>
                        <hr>
                        <p>Location</p>
                        <table style="margin: auto;">
                            <thead>
                                <tr>
                                    <th>Rack</th>
                                    <th>Tier</th>
                                    <th>Jumlah Brang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td>{{$item->Rack->name ?? '-'}}</td>
                                    <td>{{$item->Tier->tier ?? '-'}}</td>
                                    <td>{{$item->jumlah_barang ?? '-'}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>

</html>