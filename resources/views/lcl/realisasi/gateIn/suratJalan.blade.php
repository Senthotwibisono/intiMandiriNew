<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{$title}} | Inti Mandiri</title>
    <link rel="stylesheet" href="{{asset('dist/assets/css/main/app.css')}}">
    <link rel="shortcut icon" href="{{asset('logo/icon.png')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{asset('logo/icon.png')}}" type="image/png">

    <style>
        @page {
            size: A4 portrait; /* Ukuran A4 */
            margin: 10mm; /* Margin untuk cetak */
        }

        body {
            font-family: 'Roboto Condensed', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
        }

        .container {
            width: 50%; /* Setengah dari lebar A4 */
            height: 50%; /* Setengah dari tinggi A4 */
            padding: 10px;
            box-sizing: border-box;
            page-break-inside: avoid; /* Hindari pemutusan halaman di tengah */
        }

        .card {
            border: 1px solid #ccc;
            padding: 10px;
            height: 100%;
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .logo {
            flex: 0 0 50px;
        }

        .logo img {
            width: 50px;
            height: auto;
        }

        .card-title {
            flex-grow: 1;
            text-align: center;
            font-weight: bold;
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .col-6 {
            width: 48%;
        }

        .imgeEir{
            width: 230px;
            height: auto;
        }
    </style>
</head>

<body>
    @for ($i = 0; $i < 4; $i++)
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="logo">
                    <img src="/logo/IntiMandiri.PNG" class="img-fluid" alt="">
                </div>
                <div class="card-title">
                    Surat Penarikan Petikemas (SP2)
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <span style="font-size: 9px;">No Container : <strong>{{$cont->nocontainer}}</strong></span> <br>
                    <span style="font-size: 9px;">Container Size : <strong>{{$cont->size}}</strong></span> <br>
                    <span style="font-size: 9px;">Container Type : <strong>{{$cont->ctr_type}}</strong></span> <br>
                </div>
                <div class="col-6">
                    <span style="font-size: 9px;">Active To : <strong>{{$cont->active_to}}</strong></span> <br>
                    <span style="font-size: 9px;">Dokumen : <strong>{{$cont->dokumen_name ?? '-'}}</strong></span> <br>
                    <span style="font-size: 9px;">No Dokumen / Tgl Dokumen : <strong>{{$cont->no_dok ?? '-'}} / {{$cont->tgl_dok ?? '-'}}</strong></span> <br>
                </div>
            </div>
            <div class="text-center">
                <img src="/images/EIR.png" alt="EIR" class="imgeEir">
            </div>
        </div>
    </div>
    @endfor
</body>

</html>
