<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=0.1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{$title}} | Icon Sarana</title>
  <link rel="stylesheet" href="{{ asset('dist/assets/css/main/app.css') }}">
</head>

<style>
    @page {
        size: 11in 9.5in;
        margin: 0;
    }

    body {
        margin: 0;
        padding: 0;
        background: #eee;
        font-family: Arial, sans-serif;
        font-size: 10px; /* Reduced from 12px */
    }

    .container {
        width: 100%;
        max-width: 950px;
        margin: 0 auto;
        padding: 20px; /* Reduced from 30px */
        background: #fff;
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
        font-size: 15px; /* Reduced from default size */
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
</style>

<body>
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <div class="grid invoice">
          <div class="grid-body">
            <div class="invoice-title">
              <h2>Order No<span class="small"></span>{{$header->order_no}}</h2><br>
              <h2>Invoice No<span class="small"></span>{{$header->invoice_no}}</h2>
            </div>
            <div class="text-center">
                <div class="invoice-header" style="display: flex; align-items: center; justify-content: center;">
                    <h2 style="margin-right: 10px;">Invoice {{$header->judul_invoice ?? ''}}</h2>
                </div>
            </div>
            <hr>
            <div class="row mt-0">
                <div class="col-sm-3 d-flex align-items-center justify-content-center">
                    <address>
                        <strong>Billed To:</strong> <br>
                        Kepada : {{$form->customer->name ?? ''}}
                        <br>
                        NPWP : {{$form->customer->npwp ?? ''}}
                        <br>
                        Alamat : {{$form->customer->alamat ?? ''}}
                    </address>
                </div>
                <div class="col-sm-3 d-flex align-items-center justify-content-center">
                    <address>
                        <strong>Container:</strong> <br>
                        Container No : {{$form->manifest->cont->nocontainer ?? ''}}
                        <br>
                        MBL : {{$form->manifest->cont->job->nombl ?? ''}}
                        <br>
                        Vessel : {{$form->manifest->cont->job->Kapal->name ?? ''}}
                    </address>
                </div>
                <div class="col-sm-6 d-flex align-items-center justify-content-center">
                    <address>
                        <strong>Manifest:</strong> <br>
                        <div class="row">
                            <div class="col-6">
                                No HBL : {{$form->manifest->nohbl ?? ''}}
                                <br>
                                Quantity : {{$form->manifest->quantity ?? ''}}
                                <br>
                                Tonase : {{$form->manifest->weight ?? ''}}
                            </div>
                            <div class="col-6">
                                Volume : {{$form->manifest->meas ?? ''}}
                                <br>
                                CBM : {{$form->cbm ?? ''}}
                            </div>
                        </div>
                    </address>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-left">
                <address>
                    Tanggal Masuk : <strong>{{$form->time_in}}</strong> -- Rencana Keluar : <strong>{{$form->expired_date}}</strong>
                </address>
            </div>
            <hr>
            <div class="row mt-3 d-flex align-items-center justify-content-center">
              <div class="col-md-12 text-center">
                <h6>PRANOTA SUMMARY</h6>
                <div class="table table-responsive">
                    <table class="tabel-stripped mx-auto" style="width: auto;">
                        <thead>
                            <tr>
                                <th></th>
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
                            <td class="text-right">{{ number_format($tarif->harga, '3', ',' ,'.') }}</td>
                            <td class="text-right">{{ number_format($tarif->jumlah, '3', ',' ,'.') }}</td>
                            <td class="text-right">{{ number_format($tarif->jumlah_hari, '3', ',' ,'.') }}</td>
                            <td class="text-right">{{ number_format($tarif->total, '3', ',' ,'.') }}</td>
                          </tr>
                          @endforeach
                        </tbody>
                    </table>
                </div>
              </div>
            </div>
                <div class="row text-white p-3">
                    <div class="col-6">
                        <h4 class="lead ">Admin</h4>
                        <h4 class="lead ">Discount</h4>
                        <h4 class="lead ">Total</h4>
                        <h4 class="lead ">PPN (%)</h4>
                        <h4 class="lead ">PPN (Amount)</h4>
                        <h4 class="lead ">Grand Total</h4>
                    </div>
                    <div class="col-6" style="text-align:right;">
                        <h4 class="lead "><span id="grand_total_display">{{ number_format($form->admin, '3', ',', '.') ?? ''}}</span></h4>
                        <h4 class="lead "><span id="grand_total_display">{{ number_format($form->discount, '3', ',', '.') ?? ''}}</span></h4>
                        <h4 class="lead "><span id="grand_total_display">{{ number_format($form->total, '3', ',', '.') ?? ''}}</span></h4>
                        <h4 class="lead ">
                            {{$form->pajak ?? ''}} %
                        </h4>
                        <h4 class="lead "><span id="ppn_amount_display">{{number_format($form->pajak_amount, '3', ',', '.') ?? ''}}</span></h4>
                        <h4 class="lead "><span id="ppn_amount_display">{{number_format($form->grand_total, '3', ',', '.') ?? ''}}</span></h4>
                    </div>
                    <hr>
                    <h4 style="text-align:left;">Terbilang : {{$terbilang}}</h4>
                </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
