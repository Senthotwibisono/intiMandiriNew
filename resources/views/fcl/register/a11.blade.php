<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{$title}} | {{$job->nojoborder}}</title>
  <link rel="stylesheet" href="{{ asset('dist/assets/css/main/app.css') }}">
  <style>
    @page {
    size: A4;
    margin: 0;
    margin-top: 1px;
    margin-bottom: 3px;
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
        max-width: 50px;
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
        border-collapse: collapse; /* Menghindari garis ganda */
        text-align: center;
        font-size: 11px; /* Ukuran font lebih kecil agar muat di A4 */
        border: 1px solid black; /* Garis luar tabel */
    }

    /* Header tabel */
    table.tabel-stripped thead th {
        font-weight: bold;
        padding: 5px;
        border: 1px solid black; /* Garis hitam di setiap sel header */
        background-color: #f2f2f2; /* Abu-abu muda untuk membedakan header */
        text-align: center; /* Posisi teks tengah */
        vertical-align: middle; /* Agar teks di tengah untuk rowspan */
    }


    /* Isi tabel */
    table.tabel-stripped tbody tr {
        background-color: #fff;
        transition: background 0.3s ease;
        border: 1px solid black;
    }

    table.tabel-stripped tbody tr:nth-child(even) {
        background-color: #f8f9fa; /* Warna abu muda */
    }

    table.tabel-stripped tbody td {
        padding: 6px;
        border: 1px solid black; /* Pastikan semua sel memiliki garis hitam */
    }

    /* Efek hover */
    table.tabel-stripped tbody tr:hover {
        background-color: #e9ecef;
    }

  </style>
</head>

<body>
    <div class="container">
        <!-- <div class="card-header">
            <div class="divider divider-center">
                <div class="divider-text d-flex justify-content-center align-items-center">
                    <div class="logo img me-3 text-center">
                        <img src="/logo/IntiMandiri.png" class="img-fluid" alt="" style="">
                    </div>
                    No Job Order : {{$job->nojoborder}}
                </div>
                <hr>
            </div>
            <div class="text-center">
            </div>
        </div> -->
        <br>
        <br>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="row mb-2">
                        <div class="col-8 d-flex">
                            <div class="col-3" style="font-size: 14px;">Nomor</div>
                            <div class="col-1" style="font-size: 14px;">:</div>
                            <div class="col-6" style="font-size: 14px;">{{$job->PLP->no_surat ?? '-'}}</div>
                        </div>
                        <div class="col-4 text-end" style="font-size: 14px;">
                            <strong>{{ \Carbon\Carbon::parse($job->ttgl_plp)->format('j-M-Y') }}</strong>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-2" style="font-size: 14px;">Lampiran</div>
                        <div class="col-1" style="font-size: 14px;">:</div>
                        <div class="col-8" style="font-size: 14px;"></div>
                    </div>
                    <div class="row">
                        <div class="col-2" style="font-size: 14px;">Hal</div>
                        <div class="col-1" style="font-size: 14px;">:</div>
                        <div class="col-8" style="font-size: 14px;">
                            <strong>Permohonan Pindah Penimbunan</strong>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <p style="font-size: 14px;">Yth, Kepala kantor Pelayanan Utama <br>
                u.p. Kepala Seksi Administrasi Manifest
            <br>
            <br>
                Dengan ini Kami mengajukan permoonan Pindah Lokasi Penimbunan barang impor yang belum diselesaikan kewajiban pabeannya (PLP) sebagai berikut: </p>

                <br>
                <br>
                <div style="font-size: 14px;" class="row">
                    <div class="col-4">
                        BC.11 Nomor {{$job->tno_bc11 ?? '-'}}
                    </div>
                    <div class="col-4">
                        Tanggal {{$job->ttgl_bc11 ?? '-'}}
                    </div>
                    <div class="col-4">
                        {{$job->Kapal->name ?? '-'}} {{$job->voy ?? '-'}}
                    </div>
                </div>
                <br>
                <div class="table table-responsive">
                    <table class="tabel-stripped mx-auto">
                        <thead>
                            <tr>
                                <th rowspan="2">No Urut</th>
                                <th colspan="3">Peti Kemas</th>
                                <th rowspan="2">Keputusan Pejabat BC</th>
                            </tr>
                            <tr>
                                <th class="text-center">Nomor</th>
                                <th class="text-center">Ukuran</th>
                                <th class="text-center">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($conts as $cont)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$cont->nocontainer ?? '-'}}</td>
                                    <td>{{$cont->size ?? '-'}}</td>
                                    <td>-</td>
                                    <td>Disetujui</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-6">
                    <div class="row mb-2">
                        <div class="col-3">TPS Asal</div>
                        <div class="col-1">:</div>
                        <div class="col-8">{{$job->sandar->kd_tps_asal ?? '-'}}</div>
                    </div>
                    <div class="row">
                        <div class="col-3">TPS Tujuan</div>
                        <div class="col-1">:</div>
                        <div class="col-8">Inti Mandiri Utama Trans</div>
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div class="col-6">
                    <div class="row mb-2">
                        <div class="col-3">Kode TPS</div>
                        <div class="col-1">:</div>
                        <div class="col-8">{{$job->sandar->kd_tps_asal ?? '-'}}</div>
                    </div>
                    <div class="row">
                        <div class="col-3">Kode TPS</div>
                        <div class="col-1">:</div>
                        <div class="col-8">1MUT</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-1">Alasan</div>
                <div class="col-1">:</div>
                <div class="col-9">
                    Berdasarkan pertimbangan Kepala Kantor Pabean dimungkinkan terjadi stagnasi setelah mendapatkan masukan dari pengusaha TPS Asal
                </div>
            </div>

            <br>
            <br>
            <p style="font-size: 12px;">Keputusan Pejabat Bea dan Cukai</p>
            <br>
            <br>
            <p>Nomor   : {{$job->noplp ?? '-'}}</p>
            <p>Tanggal : {{$job->ttgl_plp ?? '-'}}</p>
                <div class="row">
                    <div class="col-6 text-left">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    an. Kepala Kantor, <br> Kepala Seksi Administrasi Manifest
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <span style="display: inline-block; width: 100px; border-bottom: 2px solid black;"></span>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 text-right">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    Pemohonan
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <span style="display: inline-block; width: 100px; border-bottom: 2px solid black;"></span>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <br>
                    <p>NIP :</p>
                </div>
                <div class="row">
                    <div class="col-6 text-center"  style="border: 1px solid black;">
                        <p>Pemgeluaran TPS Asal</p>
                        <div class="row text-left">
                            <div class="col-3">Tanggal</div>
                            <div class="col-1">:</div>
                        </div>
                        <div class="row text-left">
                            <div class="col-3">Pukul</div>
                            <div class="col-1">:</div>
                        </div>
                        <br>
                        <p>Pejabat Bea dan Cukai</p>
                        <div class="row text-left">
                            <div class="col-3">Nama</div>
                            <div class="col-1">:</div>
                        </div>
                        <div class="row text-left">
                            <div class="col-3">NIP</div>
                            <div class="col-1">:</div>
                        </div>
                    </div>
                    <div class="col-6 text-center"  style="border: 1px solid black;">
                        <p>Pemasukan TPS Tujuan</p>
                        <div class="row text-left">
                            <div class="col-3">Tanggal</div>
                            <div class="col-1">:</div>
                        </div>
                        <div class="row text-left">
                            <div class="col-3">Pukul</div>
                            <div class="col-1">:</div>
                        </div>
                        <br>
                        <p>Pejabat Bea dan Cukai</p>
                        <div class="row text-left">
                            <div class="col-3">Nama</div>
                            <div class="col-1">:</div>
                        </div>
                        <div class="row text-left">
                            <div class="col-3">NIP</div>
                            <div class="col-1">:</div>
                        </div>
                    </div>
                </div>
                <p>*) Coret yang tidak perlu/diisi oleh Pejabat Bea dan Cukai</p>
        </div>


        <br>
        <br>
    </div>

</body>

</html>
