<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | Inti Mandiri</title>
</head>

<style>
    @page {
        size: A5 portrait;
        margin: 0;
    }

    html, body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Roboto Condensed', sans-serif;
    }

    .container {
        width: 100%;
        height: 100%;
        padding: 0mm;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        width: 100%;
        height: 100%;
        border: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .card-header h1 {
        font-size: 16px;
        margin: 0 0 5px 0;
    }

    .card-body {
        width: 100%;
        padding: 0;
    }

    table {
        margin: 0 auto 10px auto;
        font-size: 12px;
    }

    .qr {
        margin: 10px 0;
    }

    .info {
        font-size: 12px;
        line-height: 1.4;
    }

    .page-break {
        page-break-before: always;
    }
</style>

<body>
    @foreach($items as $item)
        @php
            $itemTiers = $tiers->where('rack_id', $item->id)->sortBy('tier')->values();
        @endphp

        @if($itemTiers->count())
            @foreach($itemTiers as $tier)
                <div class="container">
                    <div class="card">
                        <div class="card-header">
                            <h1>{{ $item->name }}</h1>
                        </div>
                        <div class="card-body">
                            <table>
                                <tr>
                                    <td><strong>Code</strong></td>
                                    <td>: {{ $tier->barcode }}</td>
                                </tr>
                            </table>

                            <div class="qr">
                                {!! QrCode::margin(0)->size(200)->generate($tier->barcode) !!}
                            </div>

                            <div class="info">
                                <div>No Rack: {{ $item->name }}</div>
                                <div>Tier: {{ $tier->tier }}</div>
                                <div>
                                    Fungsi Rack: 
                                    @switch($item->use_for)
                                        @case('M') Multi use @break
                                        @case('D') Danger Item @break
                                        @case('B') Behandle Rack @break
                                        @case('L') Long Stay @break
                                        @default -
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="page-break"></div>
            @endforeach
        @endif
    @endforeach
</body>

</html>
