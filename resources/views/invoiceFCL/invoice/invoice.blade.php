<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{$title}} | {{$header->order_no}}</title>
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
                        <img src="/logo/IntiMandiri.png" class="img-fluid" alt="" style="">
                    </div>
                    No Job Order : {{$header->job->nojoborder}}
                </div>
                <hr>
            </div>
            <div class="text-center">
                <h4><strong>Nota dan Perhitungan Pelayanan Jasa :</strong> Penumpukan dan Pergerakan Ekstra</h4> 
            </div>
            <strong>Di Bayar pada {{$header->lunas_at}}</strong>
            <!-- Bagian Kiri & Kanan -->
            <div class="row">
                <!-- Kiri -->
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-3"><strong>Perusahaan</strong></div>
                        <div class="col-8">: {{$header->cust_name ?? '-'}}</div>
                    </div>
                    <div class="row">
                        <div class="col-3"><strong>Alamat</strong></div>
                        <div class="col-8">: {{$header->cust_alamat ?? '-'}}</div>
                    </div>
                    <div class="row">
                        <div class="col-3"><strong>Kapal</strong></div>
                        <div class="col-8">: {{$header->kapal_voy}}</div>
                    </div>
                    <div class="row">
                        <div class="col-3"><strong>Ukuran Container</strong></div>
                        <div class="col-8">: {{$jenisContainer}}</div>
                    </div>
                    <div class="row">
                        <div class="col-3"><strong>Tipe Container</strong></div>
                        <div class="col-8">: {{$typeContainer}}</div>
                    </div>
                </div>
                <!-- Kanan -->
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-3"><strong>Invoice No</strong></div>
                        <div class="col-8">: {{$header->invoice_no}}</div>
                    </div>
                    <div class="row">
                        <div class="col-3"><strong>Nomor BL</strong></div>
                        <div class="col-8">: {{$header->nobl}}</div>
                    </div>
                    <div class="row">
                        <div class="col-3"><strong>Tanggal BL AWB</strong></div>
                        <div class="col-8">: {{$header->tgl_bl_awb}}</div>
                    </div>
                    <div class="row">
                        <div class="col-3"><strong>ETA</strong></div>
                        <div class="col-8">: {{$header->eta}}</div>
                    </div>
                    <div class="row">
                        <div class="col-3"><strong>Tgl Masuk TPS</strong></div>
                        <div class="col-8">: {{$header->tglmasuk ?? '-'}}</div>
                        <input type="hidden" name="tglmasuk" value="{{$singleCont->cont->tglmasuk ?? '-'}}">
                        <input type="hidden" name="kd_tps_asal" value="{{$form->LokasiSandar->kd_tps_asal ?? '-'}}">
                    </div>
                    <div class="row">
                        <div class="col-3"><strong>Rencana Keluar</strong></div>
                        <div class="col-8">: {{$header->etd ?? '-'}}</div>
                    </div>
                    <div class="row">
                        <div class="col-3"><strong>No. Container</strong></div>
                        <div class="col-8">: {{$nocontainer ?? '-'}}</div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <br>
        <div class="card-body">
            <div class="divider divider-left">
                <div class="divider-text">
                    <strong>Tarif TPS ASAL {{$header->kd_tps_asal}}</strong>
                </div>
            </div>
            <div class="table table-responsive">
                <table class="tabel-stripped mx-auto">
                    <thead>
                        <tr>
                            <th>Keterangan</th>
                            <th>Tarif Dasar</th>
                            <th>Jumlah Container</th>
                            <th>Jumlah Hari</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detilTPS as $tps)
                        <tr>
                            <td class="text-left">{{$tps->keterangan}}</td>
                            <td>{{ number_format($tps->tarif_dasar, 2)}}</td>
                            <td>{{$tps->jumlah}}</td>
                            <td>{{$tps->jumlah_hari}}</td>
                            <td>{{ number_format($tps->total, 2)}}</td>
                        </tr>   
                        @endforeach
                        <tr>
                            <td>Total</td>
                            <td colspan="4">Rp. {{ number_format($header->total_tps, 2)}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="divider divider-left">
                <div class="divider-text">
                    <strong>Tarif Inti Mandiri</strong>
                </div>
            </div>
            <div class="table table-responsive">
                <table class="tabel-stripped mx-auto">
                    <thead>
                        <tr>
                            <th>Keterangan</th>
                            <th>Tarif Dasar</th>
                            <th>Jumlah Container</th>
                            <th>Jumlah Hari</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($detilWMS as $wms)
                        <tr>
                            <td class="text-left">{{$wms->keterangan}}</td>
                            <td>{{ number_format($wms->tarif_dasar, 2)}}</td>
                            <td>{{$wms->jumlah}}</td>
                            <td>{{$wms->jumlah_hari}}</td>
                            <td>{{ number_format($wms->total, 2)}}</td>
                        </tr>   
                        @endforeach
                        <tr>
                            <td>Total</td>
                            <td colspan="4">Rp. {{ number_format($header->total_wms, 2)}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row text-right p-2">
                <div class="col-12 text-right">
                    <p class=""><strong>Admin</strong> : {{ number_format($header->admin, 2) }}</p>
                    <p class=""><strong>Total</strong> : {{ number_format($header->total, 2) }}</p>
                    <hr>
                    <p class=""><strong>PPN (11%)</strong> : {{ number_format($header->ppn, 2) }}</p>
                    @if($header->grand_total >= 5000000)
                    <p class=""><strong>Materai</strong> : 10.000</p>
                    @endif
                    <hr>
                    <div class="row">
                        <div class="col-8">
                            <p class=""><strong>Terbilang</strong> : {{ $terbilang }}</p>
                        </div>
                        <div class="col-4 text-right">
                            <p class=""><strong>Grand Total</strong> : {{ number_format($header->grand_total, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer item-align-right">
            <div class="divider divider-left">
                <div class="divider-text">
                    Ketentuan
                </div>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-6 text-left">
                        1. Dalam waktu 8 hari kerja setelah nota ini diterima, tidak ada pengajuan keberatan saudara dianggap setuju. <br>
                        2. Terhadapt nota yang diajukan koreksi harus dilunasi terlebih dahulu. <br>
                        3. Tidak dibenarkan memberi imbalan kepada petugas. <br>
                        4. Nota ini berlaku sebagai bukti pembayaran. <br>
                        5. Untuk permintaan faktur pajak dapat email ke tps@intimandiri.com
                    </div>
                    <div class="col-6 text-right">
                        <div class="text-center">
                            Jakarta, {{Carbon\Carbon::parse($header->created_at)->format('Y-m-d')}} 
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <span style="text-decoration: underline;">{{ $header->userCreate->name }}</span>
                            <br>
                            Kasir
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
