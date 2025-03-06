<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{$title}}</title>
  <link rel="stylesheet" href="{{ asset('dist/assets/css/main/app.css') }}">
  <style>
    @page {
    size: A4;
    margin: 0;
}

body {
    margin-top: 1px;
    margin-bottom: 3px;
    padding: 0;
    background: #eee;
    font-family: Arial, sans-serif;
    font-size: 10px;
}

.container {
    width: 210mm; /* Lebar A4 */
    height: auto; /* Tinggi A4 */
    background: #fff;
    margin-top: 1px;
    margin-bottom: 3px;
}
    .invoice-title h2, .invoice-title .small {
        display: inline-block;
        font-size: 14px; /* Reduced from default size */
    }

    .invoice hr {
        margin-top: 10px;
        border-color: #ddd;
    }

    .invoice .table {
        width: 100%;
        margin-bottom: 15px; /* Reduced from 20px */
    }

    .invoice .table th, .invoice .table td {
        padding: 6px; /* Reduced from 8px */
        border-bottom: 1px solid #ddd;
        font-size: 10px; /* Reduced from default size */
    }

    .invoice .table th {
        background: #f5f5f5;
    }

    .invoice .identity {
        margin-top: 10px;
        font-size: 10px; /* Reduced from 1.1em */
        font-weight: 300;
    }

    .invoice .identity strong {
        font-weight: 600;
    }

    .grid {
        padding: 15px; /* Reduced from 20px */
        margin-bottom: 20px; /* Reduced from 25px */
        border-radius: 2px;
        box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.1);
    }

    .text-right {
        text-align: right;
    }

    .mt-3 {
        margin-top: 0.5rem; /* Reduced from 1rem */
    }

    .p-3 {
        padding: 0.5rem; /* Reduced from 1rem */
    }

    .img {
        max-width: 400px;
        height: auto;
        display: flex;
        justify-content: center;
        align-items: center;
       
    }

    .text-left{
        text-align: left;
    }
    /* Styling untuk tabel */
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
        <div class="card-header">
            <div class="divider divider-center">
                <div class="divider-text d-flex justify-content-center align-items-center">
                    <div class="logo img me-3 text-center">
                        <img src="/logo/IntiMandiriStripping.jpg" class="img-fluid" alt="" style="">
                    </div>
                    <!-- No Job Order : {{$cont->job->nojoborder}} -->
                </div>
            </div>
            <div class="text-center">
                <h4><strong>Izin Stripping</strong></h4> 
            </div>
            <!-- Bagian Kiri & Kanan -->
            
        </div>
        <br>
        <br>
        <div class="card-body">           
            <p style="font-size: 14px;">NO : {{$cont->job->nojoborder ?? '-'}}</p>
            <div class="row">
                <p style="font-size: 14px; text-align: justify;">Kepada Yth,
                    <br>
                    <br>
                    Supervisor Bea & Cukai <br>
                    Di <br>
                    TPS PT. Inti Mandiri Utama Trans
                    <br>
                    <br>
                    Perihal : Permohonan Pembukaan Segel, Izin Stripping Dan Pengawasan Stripping (Bongkar)
                    <br>
                    <br>
                    Dengan Hormat, 
                    <br>
                    <br>
                    Sehubungan dengan pelaksanaan Pindah Lokasi Penimbunan (PLP) dan kdatangan container LCL di gudang INTI, mohon kiranya dapat diberikan izin untuk membuka segel dan melakukan pembongkaran barang (Stripping) atas container dengan data sebagai berikut :
                    
                </p>
                <br>
                <br>
                <div class="table table-resposnive">
                    <table class="tabel-stripped mx-auto">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Container</th>
                                <th>Size</th>
                                <th>No. Segel</th>
                                <th>Vessel/Voyage</th>
                                <th>Eta</th>
                                <th>No PLP</th>
                                <th>Tanggal PLP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>{{$cont->nocontainer ?? '-'}}</td>
                                <td>{{$cont->size ?? '-'}}</td>
                                <td> </td>
                                <td>{{$cont->job->Kapal->name}} / {{$cont->job->voy}}</td>
                                <td>{{$cont->job->eta}}</td>
                                <td>{{$cont->job->noplp}}</td>
                                <td>{{$cont->job->ttgl_plp}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <br>
                <p style="font-size: 14px;">Demikian surat permohonan kami buat, atas perhatian dan kerjasamanya kami ucapkan terimakasih</p>
        </div>
        <div class="card-footer item-align-right">
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-12 text-left">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    Hormat Kami
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <span style="text-decoration: underline;"> (Kepala Gd. PT. INTI MANDIRI UTAMA TRANS)</span>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    (TKBM)
                                   
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                        Mengetahui
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <span style="text-decoration: underline;"> (Hanggar Bea & Cukai)</span>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    (pbb bEA & Cukai)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="divider divider-left">
                    <div class="divider-text">
                        <!-- JL bugis no.15 kebon bawang, jakarta utara T.J Priok -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
