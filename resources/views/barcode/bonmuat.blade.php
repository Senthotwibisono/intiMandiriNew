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
                    {{strtoupper($barcode->ref_type).' - '.strtoupper($barcode->ref_action)}}
                    <small class="pull-right"><strong>Expired Date:</strong> {{ date('d F, Y', strtotime($barcode->expired)) }}</small>
                </div>
                <div class="card-body">
                    <div class="text-center">
                            <td><b>Code</b></td>
                            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                            <td>{{ $barcode->barcode }}</td>
                            <br>
                        {!!QrCode::margin(0)->size(150)->generate($barcode->barcode)!!}
                    </div>
                    <br>
                    <div class="text-center">
                        <tr>
                            <td>Container / BL Awb</td>
                            <td> : </td> 
                            <td>{{$barcode->cont->nocontainer}} / {{$barcode->cont->nobl ?? ''}}</td>
                        </tr>
                        <br>
                        <tr>
                            <td>Size</td>
                            <td> : </td>
                            <td>{{$barcode->cont->size}}</td>
                        </tr>
                        <br>
                        <tr>
                            <td>Ex. Kapal/Voy</td>
                            <td> : </td>
                            <td>{{$barcode->cont->job->kapal->name ?? ''}} / {{$barcode->cont->job->voy ?? '-'}}</td>
                        </tr>
                        <br>
                        <tr>
                            <td>Kode TPS Asal</td>
                            <td> : </td>
                            <td>{{$barcode->cont->job->sandar->kd_tps_asal ?? ''}}</td>
                        </tr>
                        <br>
                        <tr>
                            <td>Consignee/NPWP/FAX/EMAIL</td>
                            <td> : </td>
                            <br>
                            <td>{{$barcode->cont->Customer->name ?? ''}} / {{$barcode->cont->Customer->npwp ?? ''}} / {{$barcode->cont->Customer->fax ?? ''}} / {{$barcode->cont->Customer->email ?? ''}}</td>
                        </tr>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-break">
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
                                BON MUAT
                            </div>
                        </div>
                    </div>
                    {{strtoupper($barcode->ref_type).' - '.strtoupper($barcode->ref_action)}}
                    <small class="pull-right"><strong>Expired Date:</strong> {{ date('d F, Y', strtotime($barcode->expired)) }}</small>
                </div>
                <div class="card-body">
                    <div class="text-center">
                            <td><b>Code</b></td>
                            <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
                            <td>{{ $barcode->barcode }}</td>
                            <br>
                        {!!QrCode::margin(0)->size(150)->generate($barcode->barcode)!!}
                    </div>
                    <br>
                    <div class="text-center">
                        <tr>
                            <td>Container</td>
                            <td> : </td>
                            <td>{{$barcode->cont->nocontainer}}</td>
                        </tr>
                        <br>
                        <tr>
                            <td>Size</td>
                            <td> : </td>
                            <td>{{$barcode->cont->size}}</td>
                        </tr>
                        <br>
                        <tr>
                            <td>Ex. Kapal/Voy</td>
                            <td> : </td>
                            <td>{{$barcode->cont->job->kapal->name ?? ''}} / {{$barcode->cont->job->voy ?? '-'}}</td>
                        </tr>
                        <br>
                        <tr>
                            <td>Kode TPS Asal</td>
                            <td> : </td>
                            <td>{{$barcode->cont->job->sandar->kd_tps_asal ?? ''}}</td>
                        </tr>
                        <br>
                        <tr>
                            <td>Consignee/NPWP/FAX/EMAIL</td>
                            <td> : </td>
                            <br>
                            <td>{{$barcode->cont->Customer->name ?? ''}} / {{$barcode->cont->Customer->npwp ?? ''}} / {{$barcode->cont->Customer->fax ?? ''}} / {{$barcode->cont->Customer->email ?? ''}}</td>
                        </tr>
                        <br>
                        <tr>
                            <td>Kd Dokumen/No Dok In Ou/Tgl Dok In Out</td>
                            <td> : </td>
                            <br>
                            <td>{{$barcode->cont->dokumen->name ?? ''}} / {{$barcode->cont->no_dok ?? ''}} / {{$barcode->cont->tgl_dok ?? ''}}</td>
                        </tr>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>

</html>