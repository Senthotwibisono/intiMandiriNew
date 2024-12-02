<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{$title}} | Icon Sarana</title>
    <link rel="stylesheet" href="{{asset('dist/assets/css/main/app.css')}}">
    <link rel="shortcut icon" href="{{asset('logo/icon.png')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{asset('logo/icon.png')}}" type="image/png">
</head>
<style>
 .section {
      padding-top: 5%;
    }

    .card {
      margin-bottom: 20px;
    }

    .card-body {
      padding: 15px;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
      margin-right: -15px;
      margin-left: -15px;
    }

    .col-6 {
      flex: 0 0 50%;
      max-width: 50%;
      padding-right: 15px;
      padding-left: 15px;
    }
    body{
        font-family: 'Roboto Condensed', sans-serif;
    }
    .page-break {
                page-break-before: always;
            }

            .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            padding: 5px;
            font-size: 12px;
            background-color: #f9f9f9;
            border-top: 1px solid #ddd;
        }
    .m-0{
        margin: 0px;
    }
    .p-0{
        padding: 0px;
    }
    .pt-5{
        padding-top:5px;
    }
    .mt-10{
        margin-top:10px;
    }
    .text-center{
        text-align:center !important;
    }
    .w-100{
        width: 100%;
    }
    .w-50{
        width:50%;   
    }
    .w-85{
        width:85%;   
    }
    .w-15{
        width:15%;   
    }
    .logo img{
        width:300px;
        height:300px;
        padding-top:30px;
    }
    .logo span{
        margin-left:8px;
        top:19px;
        position: absolute;
        font-weight: bold;
        font-size:25px;
    }
    .gray-color{
        color:#5D5D5D;
    }
    .text-bold{
        font-weight: bold;
    }
    .border{
        border:1px solid black;
    }
    table tr,th,td{
        border: 1px solid #d2d2d2;
        border-collapse:collapse;
        padding:7px 8px;
    }
    table tr th{
        background: #F4F4F4;
        font-size:15px;
    }
    table tr td{
        font-size:13px;
    }
    table{
        border-collapse:collapse;
    }
    .box-text p{
        line-height:10px;
    }
    .float-left{
        float:left;
    }
    .total-part{
        font-size:16px;
        line-height:12px;
    }
    .total-right p{
        padding-right:20px;
    }

    .tier-container {
    display: flex;
    flex-wrap: fixed; /* Mengatur agar kontainer tier bisa terlipat jika ukurannya melebihi lebar kontainer induk */
    gap: 5px; /* Mengatur jarak antar kotak */
}
.kotak {
        height: 5vh; /* Mengurangi tinggi kotak menjadi 5% dari tinggi viewport */
        line-height: 5vh; /* Menyesuaikan line-height agar sama dengan tinggi kotak */
        font-size: 8px; 
        background-color: #fff;
        text-align: center;
        border: 2px solid #000000;
        flex: 1;
        margin: 0px;
        border-radius: 0px;
    }

    .kotak.filled {
        background-color: red;
        color: #fff;
    }
</style>
<style>
    .grid-container {
        display: grid;
        grid-template-columns: repeat(22, 90px);
        gap: 0px;
        scale: 0.75;
    }

    .grid-item {
        width: 90px;
        height: 45px;
        background-color: #f2f2f2;
        border: 1px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .selected {
        background-color: #add8e6 !important;
        color: white;
    }

    .card {
        max-width: 100%;
        overflow-x: auto;
    }
    .bg-white {
    background-color: white;
}

.bg-red {
    background-color: red;
}

.bg-green {
    background-color: green;
}

.bg-yellow {
    background-color: yellow;
}

.bg-light-gray {
    background-color: #f2f2f2; /* Light gray */
}
</style>

<div class="text-center">
    {{$title}}
</div>
<hr>
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<div class="row mt-0">
    <div class="col-sm-12">
        <form action="/master/placementManifest/updateGrid" method="post">
            @csrf
            <section style="">
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">Penggunaan</label>
                            <select name="use_for" class="form-select" id="">
                                <option value="M">Multi Use</option>
                                <option value="D">Danger Item</option>
                                <option value="B">Behandled Rack</option>
                                <option value="L">Long Stay</option>
                                <option value="N">Null</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">Tier</label>
                            <input type="text" name="tier" value="4" class="form-control">
                        </div>
                    </div>
                </div>
                <div id="zoom-container" class="d-flex justify-content-center align-items-center mt-0" style="height: 100vh; overflow-x:auto; overflow-y:auto;">
                    <div class="grid-container">
                    @foreach($gudang as $item)
                        @php
                            // Define a background color based on the `use_for` value
                            $bgColorClass = '';
                            switch ($item->use_for) {
                                case 'M':
                                    $bgColorClass = 'bg-white'; // White background for 'M'
                                    break;
                                case 'D':
                                    $bgColorClass = 'bg-red'; // Red background for 'D'
                                    break;
                                case 'B':
                                    $bgColorClass = 'bg-green'; // Red background for 'D'
                                    break;
                                case 'L':
                                    $bgColorClass = 'bg-yellow'; // Red background for 'D'
                                    break;
                                default:
                                    $bgColorClass = ''; // Default or no additional background color
                                    break;
                            }
                        @endphp
                        <div class="grid-item {{ $bgColorClass }}" data-id="{{ $item->id }}" onclick="toggleSelection(this)">
                            <input type="text" class="form-control" name="name[{{$item->id}}]" value="{{$item->name ?? ''}}" style="width: 70%;">
                        </div>
                    @endforeach
                    </div>
                    <input type="hidden" name="selected_grids" id="selected-grids">
                </div>
                <div class="footer">
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </section>
        </form>
    </div>
</div>

<script>
    let selectedGrids = {};

    function toggleSelection(element) {
        const gridId = element.getAttribute('data-id');
        const gridName = element.querySelector(`input[name="name[${gridId}]"]`).value; // Get the input value
        
        if (element.classList.contains('selected')) {
            element.classList.remove('selected');
            delete selectedGrids[gridId];
        } else {
            element.classList.add('selected');
            selectedGrids[gridId] = gridName;
        }
    
        // Update hidden input with the selected grids' data as JSON
        document.getElementById('selected-grids').value = JSON.stringify(selectedGrids);
    }

</script>

</html>
