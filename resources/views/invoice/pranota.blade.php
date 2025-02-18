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
      size: A5 portrait; /* Set page size to A5 */
      margin: 5mm; /* Smaller margin for A5 */
    }

    body {
      width: 100%;
      height: auto;
      overflow: hidden;
      font-family: Arial, sans-serif; /* Ensure a clean font */
      font-size: 10px; /* Decrease default font size */
    }

    .container {
      width: 100%;
      background: #fff;
      padding: 5px; /* Reduced padding */
    }

    .invoice-title h2,
    .invoice-title .small {
      display: inline-block;
      font-size: 14px; /* Reduced font size */
    }

    .invoice hr {
      margin-top: 5px;
      border-color: #ddd;
    }

    .invoice .table {
      width: 100%;
      margin-bottom: 10px; /* Reduced margin */
    }

    .invoice .table th,
    .invoice .table td {
      padding: 4px; /* Further reduced padding */
      border-bottom: 1px solid #ddd;
      font-size: 10px; /* Smaller font size for tables */
    }

    .invoice .table th {
      background: #f5f5f5;
    }

    .identity {
      margin-top: 5px; /* Reduced margin */
      font-size: 9px; /* Smaller font size */
      font-weight: 300;
    }

    .identity strong {
      font-weight: 600;
    }
    .img {
        max-width: 30%;
        height: auto;
        display: flex;
        justify-content: center;
        align-items: center;
       
    }

    .grid {
      padding: 10px; /* Reduced padding */
      margin-bottom: 15px; /* Reduced margin */
      border-radius: 2px;
      box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.1); /* Reduced shadow */
    }

    .text-right {
      text-align: right;
    }

    .lead {
      margin: 0; /* Remove margin for consistent spacing */
      font-size: 10px; /* Consistent smaller size */
    }

    .mt-3 {
      margin-top: 0.5rem;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="grid invoice">
      <div class="grid-body">
        <div class="img">
            <img src="/logo/IntiMandiri.png" class="img" alt="">
        </div>
        <div class="text-center">
          <h2 style="margin: 5px 0;">Invoice {{$header->judul_invoice ?? ''}}</h2>
        </div>
        <div class="invoice-title">
          <h2>Order No<br><span class="small"></span>{{$header->order_no}}</h2>
        </div>
        <hr>
        <div class="row mt-0">
          <div class="col-sm-4 d-flex align-items-center justify-content-center">
            <address>
              <strong>Billed To:</strong><br>
              {{$form->customer->name ?? ''}}<br>
              NPWP: {{$form->customer->npwp ?? ''}}<br>
              Alamat: {{$form->customer->alamat ?? ''}}
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
              Tonase: {{number_format($form->manifest->weight, '0', ',', '.') ?? ''}}<br>
              Volume: {{number_format($form->manifest->meas, '0', ',', '.') ?? ''}}<br>
              CBM: {{$form->cbm ?? ''}}
            </address>
          </div>
        </div>
        <hr>
        <div class="d-flex align-items-center justify-content-left">
          <address>
            Kasir : <strong>{{ Auth::user()->name }}</strong>
          </address>
        </div>
        <hr>
        <div class="d-flex align-items-center justify-content-left">
          <address>
            ETA : <strong>{{$form->time_in}}</strong> -- Rencana Keluar: <strong>{{$form->expired_date}}</strong>
          </address>
        </div>
        <hr>
        <div class="row mt-3 d-flex align-items-center justify-content-center">
          <div class="col-md-12 text-center">
            <h6>PRANOTA SUMMARY</h6>
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
            <!-- <h4 class="lead">Discount</h4> -->
            <h4 class="lead">Total</h4>
            @if ($header->mekanik_y_n == 'N')
            <h4 class="lead">PPN ({{ number_format($form->pajak, '0', ',', '.') ?? '' }}%)</h4>
            @else
            <h4 class="lead">PPN ({{ number_format($form->pajak_m, '0', ',', '.') ?? '' }}%)</h4>
            @endif
            <h4 class="lead">Grand Total</h4>
          </div>
          <div class="col-6 text-right">
            @if($header->mekanik_y_n == 'N')
            <h4 class="lead">{{ number_format($form->admin, '0', ',', '.') ?? '' }}</h4>
            <!-- <h4 class="lead">{{ number_format($form->discount, '0', ',', '.') ?? '' }}</h4> -->
            <h4 class="lead">{{ number_format($form->total, '0', ',', '.') ?? '' }}</h4>
            <!-- <h4 class="lead">{{ number_format($form->pajak, '0', ',', '.') ?? '' }} %</h4> -->
            <h4 class="lead">{{ number_format($form->pajak_amount, '0', ',', '.') ?? '' }}</h4>
            <h4 class="lead">{{ number_format($form->grand_total, '0', ',', '.') ?? '' }}</h4>
            @else
            <h4 class="lead">{{ number_format($form->admin_m, '0', ',', '.') ?? '' }}</h4>
            <!-- <h4 class="lead">{{ number_format($form->discount_m, '0', ',', '.') ?? '' }}</h4> -->
            <h4 class="lead">{{ number_format($form->total_m, '0', ',', '.') ?? '' }}</h4>
            <!-- <h4 class="lead">{{ number_format($form->pajak_m, '0', ',', '.') ?? '' }} %</h4> -->
            <h4 class="lead">{{ number_format($form->pajak_amount_m, '0', ',', '.') ?? '' }}</h4>
            <h4 class="lead">{{ number_format($form->grand_total_m, '0', ',', '.') ?? '' }}</h4>
            @endif
          </div>
        </div>
        <hr>
        <h4 style="text-align:left;">Terbilang: {{$terbilang}}</h4>
      </div>
    </div>
  </div>
</body>

</html>
