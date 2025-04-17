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
    }

    body {
      font-family: Arial, sans-serif;
      font-size: 10px;
    }

    .container {
      width: 100%;
      background: #fff;
      padding: 10px;
    }

    .table {
      width: 100%;
      margin: 0 auto;
      border-collapse: collapse;
    }

    .table th,
    .table td {
      padding: 5px;
      border: 1px solid #ddd;
      text-align: center;
      font-size: 10px;
    }

    .table th {
      background: #f5f5f5;
      font-weight: bold;
    }

    .table td {
      white-space: nowrap;
    }

    .text-right {
      text-align: right;
    }

    .identity {
      margin-top: 5px;
      font-size: 9px;
    }

    .img {
      width: 100%;
      max-width: 100%;
      height: auto;
    }

    .grid {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 2px;
      box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            @if($filter == 'L')
              <th>Invoice No</th>
              <th>Payment Date</th>
            @else
              <th>Order No</th>
              <th>Payment Date</th>
            @endif
            <th>Container No</th>
            <th>HBL</th>
            <th>CBM</th>
            <th>Quantity</th>
            <th>Hari</th>
            <th>Total</th>
            <th>Admin</th>
            <th>Discount</th>
            <th>Pajak Amount</th>
            <th>Grand Total</th>
            <th>Customer</th>
          </tr>
        </thead>
        <tbody>
          @foreach($headers as $header)
            <tr>
              @if($filter == 'L')
                <td>{{ $header->invoice_no }}</td>
                <td>{{ $header->lunas_at ?? $header->piutang_at }}</td>
              @else
                <td>{{ $header->order_no }}</td>
                <td>{{ $header->created_at }}</td>
              @endif
              <td>{{ $header->manifest->cont->nocontainer }}</td>
              <td>{{ $header->manifest->nohbl }}</td>
              <td>{{ $header->Form->cbm }}</td>
              <td>{{ $header->manifest->quantity }}</td>
              <td>{{ $header->Form->jumlah_hari }}</td>
              <td class="text-right">{{ number_format($header->total, 2) }}</td>
              <td class="text-right">{{ number_format($header->admin, 2) }}</td>
              <td class="text-right">{{ number_format($header->discount, 2) }}</td>
              <td class="text-right">{{ number_format($header->pajak_amount, 2) }}</td>
              <td class="text-right">{{ number_format($header->grand_total, 2) }}</td>
              <td>{{ $header->customer->name ?? '-' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>
