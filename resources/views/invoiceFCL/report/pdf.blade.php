<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title></title>
  <link rel="stylesheet" href="{{ asset('dist/assets/css/main/app.css') }}">
  <style>
    @page {
    size: A4 landscape;
    margin: 10mm;
  }

  body, html {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    font-family: Arial, sans-serif;
    background: #fff;
  }

  .container {
    width: 100%;
    max-width: 100%;
    background: #fff;
    padding: 0px;
  }

  .table {
    width: 100%;
    border-collapse: collapse;
    table-layout: auto; /* Menyesuaikan ukuran kolom dengan isi */
  }

  .table th,
  .table td {
    padding: 4px 6px; /* Sedikit padding agar lebih mudah dibaca */
    border: 1px solid #ddd;
    text-align: center;
    font-size: 10px; /* Menyesuaikan ukuran font */
    word-break: break-word; /* Memastikan teks tetap dalam sel */
    white-space: normal; /* Memungkinkan teks untuk wrap */
  }

  .table th {
    background: #f5f5f5;
    font-weight: bold;
  }

  .text-right {
    text-align: right;
  }

  /* Menghindari pemisahan baris saat print */
  .table tr {
    page-break-inside: avoid;
  }

  /* Mengatur header agar tetap terlihat di setiap halaman cetak */
  thead {
    display: table-header-group;
  }

  /* Opsi jika ingin menyembunyikan elemen tertentu saat print */
  @media print {
    .no-print {
      display: none;
    }
  }

  </style>
</head>

<body>
  <div class="container">
    <div class="text-center">
      {{$judul}}
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th class="text-center" rowspan="2">No</th>
            <th class="text-center" rowspan="2">No Invoice</th>
            <th class="text-center" rowspan="2">Tgl Invoice</th>
            <th class="text-center" colspan="4">Consignee</th>
            <th class="text-center" colspan="4">Container</th>
            <th class="text-center" colspan="4">Harga</th>
            <th class="text-center" colspan="2">Pembayaran</th>
            <th class="text-center" rowspan="2">Keterangan</th>
            <th class="text-center" rowspan="2">Kontak</th>
            <th class="text-center" rowspan="2">URL</th>
          </tr>
          <tr>
            <th class="text-center">Nama</th>
            <th class="text-center">NPWP</th>
            <th class="text-center">Alamat</th>
            <th class="text-center">Fax</th>
            <th class="text-center">20</th>
            <th class="text-center">40</th>
            <th class="text-center">45</th>
            <th class="text-center">KD TPS</th>
            <th class="text-center">DPP</th>
            <th class="text-center">TAX</th>
            <th class="text-center">Materai</th>
            <th class="text-center">Grand Total</th>
            <th class="text-center">Jumlah Dibayarkan</th>
            <th class="text-center">Selisih</th>
          </tr>
        </thead>
        <tbody>
          @foreach($headers as $header)
          <tr>
            <td class="text-center">{{$loop->iteration}}</td>
            @if($header->status == 'Y')
            <td class="text-center">{{$header->invoice_no}}</td>
            @elseif($header->status == 'N')
            <td class="text-center">Belum Melakukan Pembayaran</td>
            @else
            <td class="text-center">Invoice di Batalkan</td>
            @endif
            @php
            $tglInv = $header->created_at ? Carbon\Carbon::parse($header->created_at)->format('d/m/Y') : '-';
            @endphp
            <td class="text-center">{{$tglInv}}</td>
            <td class="text-center">{{$header->cust_name}}</td>
            <td class="text-center">{{$header->cust_npwp}}</td>
            <td class="text-center">{{$header->cust_alamat}}</td>
            <td class="text-center">{{$header->cust_fax}}</td>
            @php
              $cont20 = $detils->where('invoice_id', $header->id)->where('size', '20')->pluck('jumlah')->first() ?? 0;
              $cont40 = $detils->where('invoice_id', $header->id)->where('size', '40')->pluck('jumlah')->first() ?? 0;
              $cont45 = $detils->where('invoice_id', $header->id)->where('size', '45')->pluck('jumlah')->first() ?? 0;
            @endphp
            <td class="text-center">{{$cont20}}</td>
            <td class="text-center">{{$cont40}}</td>
            <td class="text-center">{{$cont45}}</td>
            <td class="text-center">{{$header->kd_tps_asal ?? '-'}}</td>
            <td class="text-center">{{ number_format($header->total, 0) ?? '0'}}</td>
            <td class="text-center">{{ number_format($header->ppn, 0) ?? '0'}}</td>
            @php
              $materai = $header->grand_total >= 5000000 ? 10000 : 0;
            @endphp
            <td class="text-center">{{ number_format($materai, 0) ?? '0'}}</td>
            <td class="text-center">{{ number_format($header->grand_total, 0) ?? '0'}}</td>
            <td class="text-center">{{ number_format($header->jumlah_bayar, 0) ?? '0'}}</td>
            <td class="text-center">{{ number_format(abs($header->selisih_bayar), 0) ?? '0'}}</td>
            @php
              $keterangan = "Pembayaran sudah sesuai";
              if ($header->jumlah_bayar > $header->grand_total) {
                $keterangan = "Kelebihan bayar Rp." . number_format(abs($header->selisih_bayar), 0);
              } elseif ($header->jumlah_bayar < $header->grand_total) {
                $keterangan = "Kekurangan bayar Rp." . number_format(abs($header->selisih_bayar), 0);
              }
            @endphp
            <td class="text-center">{{ $keterangan }}</td>
            <td class="text-center">{{ $header->no_hp ?? '-' }}</td>
            @php
            if ($header->status == 'Y') {
              $url = 'https://inti-mandiri.com/invoiceFCL/invoice/invoice-' . $header->id;
            }elseif ($header->status == 'N') {
                $url = 'https://inti-mandiri.com/invoiceFCL/invoice/pranota-' . $header->id;
            }else {
                $url = 'Incoice Canceled';
            }
            @endphp
            <td class="text-center">
              {!!QrCode::margin(0)->size(50)->generate($url)!!}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>
