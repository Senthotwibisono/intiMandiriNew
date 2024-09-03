@extends('partial.main')

@section('custom_styles')
<style>
    .grid-container {
        display: grid;
        grid-template-columns: repeat(22, 90px); /* 5 kolom dengan ukuran 100px */
        gap: 0px; /* Jarak antar kotak */
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
        color: white;
    }

    .bg-green {
        background-color: green;
        color: white;
    }

    .bg-yellow {
        background-color: yellow;
        /* color: white; */
    }

    .bg-light-gray {
        background-color: #f2f2f2; /* Light gray */
    }
</style>
@endsection

@section('content')
<div class="header">
    <a href="javascript:void(0)" onclick="openWindow('/master/placementManifest/createIndex')" class="btn btn-sm btn-info"><i class="fa fa-plus"></i>Buat Layout</a>
</div>
<section style="overflow-x:auto;">
    <div id="zoom-container" class="d-flex justify-content-center align-items-center mt-0" style="height: 100vh;">
        <div class="grid-container" style="border: 1px solid #ccc;"> 
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
                    {{ $item->name ?? '' }}
                </div>
            @endforeach
        </div>
    </div>
    <div class="text-center mt-3">
        <button class="btn btn-primary" onclick="zoomIn()">Zoom In</button>
        <button class="btn btn-primary" onclick="zoomOut()">Zoom Out</button>
        <button class="btn btn-primary" onclick="resetZoom()">Reset Zoom</button>
    </div>

    <form id="selection-form" action=" " method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="selected_grids" id="selected-grids">
    </form>
</section>

@endsection

@section('custom_js')
<script>
    let selectedGrids = [];
    let scale = 1;

    function toggleSelection(element) {
        const gridId = element.getAttribute('data-id');
        const index = selectedGrids.indexOf(gridId);

        if (index === -1) {
            selectedGrids.push(gridId);
            element.classList.add('selected');
        } else {
            selectedGrids.splice(index, 1);
            element.classList.remove('selected');
        }
    }

    function submitSelection() {
        document.getElementById('selected-grids').value = selectedGrids.join(',');
        document.getElementById('selection-form').submit();
    }

    function zoomIn() {
        scale += 0.1;
        document.getElementById('zoom-container').style.transform = `scale(${scale})`;
    }

    function zoomOut() {
        if (scale > 0.1) {
            scale -= 0.1;
            document.getElementById('zoom-container').style.transform = `scale(${scale})`;
        }
    }

    function resetZoom() {
        scale = 1;
        document.getElementById('zoom-container').style.transform = `scale(${scale})`;
    }
</script>

<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=1500,height=1000');
    }
</script>
@endsection