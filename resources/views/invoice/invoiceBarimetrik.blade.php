<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{$title}} | {{$header->order_no}}</title>
  <link rel="stylesheet" href="{{ asset('dist/assets/css/main/app.css') }}">
  <style>
.clearfix:after {
  content: "";
  display: table;
  clear: both;
}

.left {
    float: left;
}

.right {
    float: right;
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}

.text-left {
    text-align: left;
}

a {
  color: #0087C3;
  text-decoration: none;
}

body {
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif !important;
        color: #000 !important;
    }

    body * {
        color: #000 !important;
    }

#header {
  padding: 10px 0;
  margin-bottom: 20px;
  border-bottom: 1px solid #AAAAAA;
}

#title {
    font-size: 20px;
    text-align: center;
    margin-bottom: 20px;
}

#logo {
  float: left;
  margin-top: 8px;
}

#logo img {
  height: 70px;
}

#company {
  float: right;
  text-align: right;
}


#details {
  /*margin-bottom: 20px;*/
}

#client {
  padding-left: 6px;
  border-left: 6px solid #0087C3;
  float: left;
}

#client .to {
  color: #777777;
}

h2.name {
  font-size: 1.4em;
  font-weight: normal;
  margin: 0;
}

#invoice {
  float: right;
  text-align: right;
}

#invoice h1 {
  color: #0087C3;
  font-size: 2.4em;
  line-height: 1em;
  font-weight: normal;
  margin: 0  0 10px 0;
}

#invoice .date {
  font-size: 1.1em;
  color: #777777;
}

table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 20px;
  font-size: 12px;
  font-weight: bold;
}

table th,
table td {
  padding: 2px 0;
/*  background: #EEEEEE;*/
  /*text-align: center;*/
  border-bottom: 1px solid #FFFFFF;
}

table th {
  white-space: nowrap;        
  font-weight: normal;
  padding: 5px;
    border-bottom: 1px solid;
    font-weight: bold;
}

table td {
  text-align: left;
  padding: 3px;
}

table.grid td {
    border-right: 1px solid;
}

table td.padding-10 {
    padding: 0 10px;
}

table td h3{
  color: #57B223;
  font-size: 1.2em;
  font-weight: normal;
  margin: 0 0 0.2em 0;
}

table .no {
  color: #FFFFFF;
  font-size: 1.6em;
  background: #57B223;
}

table .desc {
  text-align: left;
}

table .unit {
  background: #DDDDDD;
}

table .qty {
}

table .total {
  background: #57B223;
  color: #FFFFFF;
}

table td.unit,
table td.qty,
table td.total {
  font-size: 1.2em;
}

table tbody tr:last-child td {
  border-bottom: none;
}

table tfoot td {
  padding: 10px 20px;
  background: #FFFFFF;
  border-bottom: none;
  font-size: 1.2em;
  white-space: nowrap; 
  border-top: 1px solid #AAAAAA; 
}

table tfoot tr:first-child td {
  border-top: none; 
}

table tfoot tr:last-child td {
  color: #57B223;
  font-size: 1.4em;
  border-top: 1px solid #57B223; 

}

table tfoot tr td:first-child {
  border: none;
}

#thanks{
  font-size: 2em;
  margin-bottom: 50px;
}

#notices{
  padding-left: 6px;
  border-left: 6px solid #0087C3;  
}

#notices .notice {
  font-size: 1.2em;
}

#footer {
  /*color: #777777;*/
  width: 100%;
  /*height: 30px;*/
  position: absolute;
  bottom: 0;
  border-top: 1px solid #AAAAAA;
  padding: 8px 0;
  text-align: center;
}

    @media print {
        body {
            color: #000;
            background: #fff;
        }
        @page {
            size: auto;   /* auto is the initial value */
            margin-top: 114px;
            margin-bottom: 90px;
            margin-left: 38px;
            margin-right: 75px;
            font-weight: bold;
        }
        .print-btn {
            display: none;
        }
    }

    .p-2 p {
    margin: 1px 0 !important;
    line-height: 1.1;
    font-size: 11px;
}

.p-2 hr {
    margin: 3px 0 !important;
    border-top: 1px dashed #000;
}
.flex-line {
    display: flex;
    justify-content: flex-end; 
    gap: 10px;
}

.flex-line .label {
    width: 200px;
    text-align: right;
}

.flex-line .rp {
    width: 60px;
    text-align: right;
}

.flex-line .value {
    width: 120px;
    text-align: right;
}
</style>
</head>

<body>
  <div class="container">
    <div class="grid invoice">
      <div class="grid-body">
        <div class="row">
            <div class="col-xs-12 col-8 my-auto">
                <!-- <span class="small">Proforma No. # {{$header->order_no}}</span><br> -->
                <span class="small">Invoice No. # {{$header->invoice_no}}</span><br>
            </div>
            <div class="col-xs-12 col-4 text-center">
            <!-- <div class="img">
                <img src="/logo/lkbLogo.png" class="img" style="width:90%;" alt="">
            </div> -->
                <!-- <img src="/images/paid.png" class="img" style="width:50%;" alt=""> -->
            </div>
        </div>
        <hr>
        <div class="text-center">
            <div class="invoice-header" style="display: flex; align-items: center; justify-content: center;">
                <h2 style="margin-right: 10px;">Invoice {{$header->judul_invoice ?? ''}}</h2>
            </div>
        </div>
        <hr>
        <div class="row mt-0">
          <div class="col-sm-4 d-flex align-items-center justify-content-center">
            <address>
              <strong>Billed To:</strong><br>
              {{$form->customer->name ?? ''}}<br>
              NPWP: {{$form->customer->npwp ?? ''}}<br>
            </address>
          </div>
          <div class="col-sm-4 d-flex align-items-center justify-content-center">
            <address>
              <strong>Container:</strong><br>
              Container No: {{$form->manifest->cont->nocontainer ?? ''}}<br>
              MBL: {{$form->manifest->cont->job->nombl ?? ''}}<br>
              Vessel: {{$form->manifest->cont->job->Kapal->name ?? ''}}
            </address>
          </div>
          <div class="col-sm-4 d-flex align-items-center justify-content-center">
            <address>
              <strong>Manifest:</strong><br>
              No HBL: {{$form->manifest->nohbl ?? ''}}<br>
              Quantity: {{$form->manifest->quantity ?? ''}}<br>
              Tonase: {{$form->manifest->weight ?? ''}}<br>
              Volume: {{$form->manifest->meas ?? ''}}<br>
              CBM: {{$form->cbm ?? ''}}
            </address>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-left">
          <address>
            Tanggal Masuk: <strong>{{$form->time_in}}</strong> -- Rencana Keluar: <strong>{{$form->expired_date}}</strong>
          </address>
        </div>
        <hr>
        <div class="row mt-3 d-flex align-items-center justify-content-center">
          <div class="col-md-12 text-center">
            <h6>DETIL SUMMARY</h6>
            <div class="table table-responsive">
              <table class="tabel-stripped mx-auto">
                <thead>
                  <tr>
                    <th>Tarif</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah (Volume)</th>
                    <th>Jumlah Hari</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($tarifs as $tarif)
                  <tr>
                    <td class="text-right">{{ $tarif->Tarif->nama_tarif }}</td>
                    <td class="text-right">{{ number_format($tarif->harga, '0', ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tarif->jumlah, '0', ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tarif->jumlah_hari, '0', ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tarif->total, '0', ',', '.') }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="row text-white p-2"> <!-- Adjusted padding -->
          <div class="col-6">
            <h4 class="lead">Admin</h4>
            <h4 class="lead">Discount</h4>
            <h4 class="lead">Total</h4>
            <h4 class="lead">PPN (%)</h4>
            <h4 class="lead">PPN (Amount)</h4>
            <h4 class="lead">Grand Total</h4>
          </div>
          <div class="col-6 text-right">
            @if($header->mekanik_y_n == 'N')
            <h4 class="lead">{{ number_format($form->admin, '0', ',', '.') ?? '' }}</h4>
            <h4 class="lead">{{ number_format($form->discount, '0', ',', '.') ?? '' }}</h4>
            <h4 class="lead">{{ number_format($form->total, '0', ',', '.') ?? '' }}</h4>
            <h4 class="lead">{{ $form->pajak ?? '' }} %</h4>
            <h4 class="lead">{{ number_format($form->pajak_amount, '0', ',', '.') ?? '' }}</h4>
            <h4 class="lead">{{ number_format($form->grand_total, '0', ',', '.') ?? '' }}</h4>
            @else
            <h4 class="lead">{{ number_format($form->admin_m, '0', ',', '.') ?? '' }}</h4>
            <h4 class="lead">{{ number_format($form->discount_m, '0', ',', '.') ?? '' }}</h4>
            <h4 class="lead">{{ number_format($form->total_m, '0', ',', '.') ?? '' }}</h4>
            <h4 class="lead">{{ $form->pajak_m ?? '' }} %</h4>
            <h4 class="lead">{{ number_format($form->pajak_amount_m, '0', ',', '.') ?? '' }}</h4>
            <h4 class="lead">{{ number_format($form->grand_total_m, '0', ',', '.') ?? '' }}</h4>
            @endif
          </div>
        </div>
        <hr>
        <h4 style="text-align:left;">Terbilang: {{$terbilang}} Rupiah</h4>
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
                        5. Untuk permintaan faktur pajak dapat email ke lkb.tax22@gmail.com
                    </div>
                    <div class="col-6 text-right">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    Jakarta, {{Carbon\Carbon::parse($header->lunas_at)->format('Y-m-d')}} 
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <span style="text-decoration: underline;">{{ $header->kasirL->name }}</span>
                                    <br>
                                    Kasir
                                </div>
                            </div>
                            @if($header->grand_total >= 5000000)
                            <div class="col-6">
                                <div class="text-center">
                                        -
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <span style="text-decoration: underline;">Materai</span>
                                    <br>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="divider divider-left">
                    <div class="divider-text">
                        JL bugis no.15 kebon bawang, jakarta utara T.J Priok
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</body>

</html>
