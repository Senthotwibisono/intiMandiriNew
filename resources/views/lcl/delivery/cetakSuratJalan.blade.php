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
        }

        body {
            font-family: 'Roboto Condensed', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
        }

        .container {
            width: 100%; /* Setengah dari lebar A4 */
            height: 100%; /* Setengah dari tinggi A4 */
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
        .table-responsive {
        width: 100%;
        overflow-x: auto; /* Agar tabel bisa di-scroll jika terlalu lebar */
    }

    table.tabel-stripped {
        width: 90%;
        border-collapse: collapse;
        text-align: center;
        font-size: 11px; /* Ukuran font lebih kecil agar muat di A4 */
    }

    /* Header tabel */
    table.tabel-stripped thead {
        background-color: #007bff; /* Warna biru Bootstrap */
        color: white;
        font-weight: bold;
    }

    table.tabel-stripped thead th {
        padding: 5px;
        border: 1px solid #ddd;
    }

    /* Isi tabel */
    table.tabel-stripped tbody tr {
        background-color: #fff;
        transition: background 0.3s ease;
    }

    table.tabel-stripped tbody tr:nth-child(even) {
        background-color: #f8f9fa; /* Warna abu muda */
    }

    table.tabel-stripped tbody td {
        padding: 6px;
        border: 1px solid #ddd;
    }

    /* Efek hover */
    table.tabel-stripped tbody tr:hover {
        background-color: #e9ecef;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="row">
                <div class="col-8">
                    <img src="/logo/IntiMandiri.png" class="img" style="width:20%;" alt="">
                </div>
                <div class="col-4">
                    <span style="font-size: 20px;">{{$manifest->cont->job->nojoborder ?? ''}}</span>
                </div>
            </div>
            <br>
            <div class="col-12 text-center">
                <h4>SURAT JALAN</h4>
            </div>
            <div class="row">
                <div class="col-8">
                    <div class="row">
                        <div class="col-5">
                            <span>Dari Gd/Lap</span><br>
                            <span>Eks Kapal</span><br>
                            <span>Tgl Tiba</span><br>
                            <span>No Pol</span><br>
                            <span>Pemilik Angkutan</span><br>
                            <span>No BL. AWB</span><br>
                            <span>Bea dan Cukai</span><br>
                        </div>
                        <div class="col-7">
                            <span>: Inti Mandiri (1MUT)</span><br>
                            <span>: {{$manifest->cont->job->Kapal->name}}</span><br>
                            <span>: {{$manifest->cont->job->eta ?? '-'}}</span><br>
                            <span>: {{$manifest->cont->nopol_release ?? '-'}}</span><br>
                            <span>: LKB</span><br>
                            <span>: {{$manifest->cont->nobl ?? '-'}}</span><br>
                            <span>: -</span><br>
                        </div>
                    </div>
                </div>
                <div class="col-4 text-center">
                <!-- <span style="border: 1px solid black; padding: 2px 5px; display: inline-block; font-size: 20px;">
                    <strong>EMPTY</strong>
                </span><br> -->
                <span>Dikirim Kepada</span>
                <br>
                <br>
                <span style="text-decoration: underline; font-size: 14px;"><strong>{{$manifest->customer->name ?? '-'}}</strong></span><br>
                <span style="text-decoration: underline; font-size: 14px;"><strong>{{$manifest->customer->alamat ?? '-'}}</strong></span>
                </div>
            </div>
            <br>
            <div class="col-12">
                <div class="table table-responsive">
                    <table class="tabel-stripped mx-auto">
                        <thead>
                            <tr>
                                <th rowspan="2">Host BL AWB</th>
                                <th rowspan="2">Ukuran Container</th>
                                <th colspan="2">Jumlah Barang</th>
                                <th rowspan="2">Keterangan</th>
                            </tr>
                            <tr>
                                <th>Coly</th>
                                <th>Ton</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" style="font-size: 14px;">{{$manifest->nohbl}}</td>
                                <td class="text-center" style="font-size: 14px;"> {{$manifest->final_qty}}</td>
                                <td class="text-center" style="font-size: 14px;"> {{ number_format($manifest->weight, '2')}}</td>
                                <td class="text-center" style="font-size: 14px;">{{ number_format($manifest->meas, '2')}}</td>
                                <td class="text-center" style="font-size: 14px;">Ex. Warehouse</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <div class="col-xs-12 col-10 text-end">
                <strong>Tanjung Priuk</strong>, 
                <span style="border-bottom: 2px dashed black; padding-bottom: 2px;">
                    {{ Carbon\Carbon::now()->format('d-m-Y / H:i:s') }}
                </span>
            </div>
            <div class="row">
                <div class="col-4 text-center">
                    Penerima,
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <span style="border-bottom: 2px dashed black; display: block; padding-bottom: 5px;"></span>
                </div>
                <div class="col-4 text-center">
                    Supir Truck,
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <span style="border-bottom: 2px dashed black; display: block; padding-bottom: 5px;"></span>
                </div>
                <div class="col-4 text-center">
                    Petugas,
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    {{ Auth::user()->name }}
                    <span style="border-bottom: 2px dashed black; display: block; padding-bottom: 5px;"></span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
