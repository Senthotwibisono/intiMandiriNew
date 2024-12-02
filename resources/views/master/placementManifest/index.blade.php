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
<div class="header d-flex align-items-center justify-content-between">
    <div>
        <a href="javascript:void(0)" onclick="openWindow('/master/placementManifest/createIndex')" class="btn btn-sm btn-info">
            <i class="fa fa-plus"></i>Buat Layout
        </a>
        <button class="btn btn-sm btn-warning printBarcode">Cetak Barcode</button>
        <button class="btn btn-sm btn-primary tierView">Tier View</button>
    </div>
    
    <form action="/master/placementManifest/kapasitas" method="post" class="d-flex align-items-center">
        @csrf
        <div class="row align-items-center">
            <div class="form-group mb-0">
                <label for="kapasitas">Kapasitas Total</label>
                <input type="number" name="kapasitas" class="form-control" value="{{$kg->kapasitas}}">
            </div>
            <button class="btn btn-success ml-2" type="submit">Submit</button>
        </div>
    </form>
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
    // Deklarasi selectedGrids di luar fungsi untuk memastikan global
    let selectedGrids = [];
    let scale = 1;

    function toggleSelection(element) {
        const gridId = element.getAttribute('data-id');
        const name = element.textContent.trim(); // Cek apakah elemen memiliki nama

        if (name === '') {
            return; // Jika nama kosong, jangan lakukan apa-apa
        }
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

    $(document).on('click', '.printBarcode', function(e) {
        e.preventDefault();

        // Ambil ID item yang dipilih (selectedGrids harus diakses secara global)
        if (selectedGrids.length === 0) {
            Swal.fire('Error', 'Pilih minimal satu item untuk mencetak barcode.', 'error');
            return;
        }

        // Setup token CSRF untuk keamanan
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Konfirmasi dari SweetAlert
        Swal.fire({
            icon: 'question',
            title: 'Do you want to generate the barcode?',
            showCancelButton: true,
            confirmButtonText: 'Generate',
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX untuk memproses cetak barcode
                $.ajax({
                    type: 'POST',
                    url: '/master/placementManifest/barcodeCreate', // URL ke route untuk mencetak barcode
                    data: { selected_grids: selectedGrids }, // Kirim item yang dipilih
                    cache: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Generated!', '', 'success')
                                .then(() => {
                                    var encodedGrids = encodeURIComponent(selectedGrids.join(','));

                                    // Buka jendela baru dengan selected_grids sebagai parameter query
                                    window.open('/master/placementManifest/barcodeView?selected_grids=' + encodedGrids, '_blank', 'width=600,height=800');
                                });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(response) {
                        var errors = response.responseJSON.errors;
                        if (errors) {
                            var errorMessage = '';
                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: errorMessage,
                            });
                        } else {
                            Swal.fire('Error', 'An error occurred while processing your request', 'error');
                        }
                    },
                });
            }
        });
    });

    $(document).on('click', '.tierView', function(e) {
        e.preventDefault();
        if (selectedGrids.length === 0) {
            Swal.fire('Error', 'Pilih minimal satu item untuk mencetak barcode.', 'error');
            return;
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        Swal.fire({
            icon: 'question',
            title: 'Do you want to view the Tier?',
            showCancelButton: true,
            confirmButtonText: 'Generate',
        }).then((result) => {
            if (result.isConfirmed) {
                const url = `/master/placementManifest/tierView?selected_grids=`+selectedGrids;
                const newWindow = window.open(url, '_blank', 'width=500,height=800,resizable=yes,scrollbars=yes');
                if (!newWindow) {
                    Swal.fire('Error', 'Popup blocked. Please allow popups for this site.', 'error');
                }
            }
        });
    });
</script>

<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=1500,height=1000');
    }
</script>
@endsection