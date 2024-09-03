@extends('partial.android.main')
@section('custom_styles')

<style>
    tr.selected {
        background-color: #d9edf7;
    }

    .draggable-item {
        cursor: pointer;
        margin: 5px 0;
        padding: 8px;
        border: 1px solid #ccc;
        font-size: 14px; /* Adjusted font size for smaller screens */
    }

    .draggable-item.selected {
        background-color: #d9edf7;
    }

    .dropzone {
        min-height: 150px; /* Reduced height for smaller screens */
        border: 2px dashed #ccc;
        padding: 5px; /* Reduced padding */
    }

    .rack-area, .unplaced-items {
        margin-bottom: 15px; /* Reduced margin */
    }

    .form-group label {
        font-size: 14px; /* Adjusted font size for labels */
    }

    .form-group select {
        font-size: 14px; /* Adjusted font size for select options */
    }

    .btn {
        padding: 5px 10px; /* Adjusted padding for buttons */
        font-size: 14px; /* Adjusted font size for buttons */
    }

    h3 {
        font-size: 16px; /* Adjusted font size for headings */
        margin-bottom: 10px; /* Reduced margin */
    }

</style>

@endsection
@section('content')
<section>
    <div class="card">
        <div class="card-header">
            <div class="button-container">
                <a href="javascript:void(0)" onclick="openWindow('/lcl/realisasi/racking/photoPlacement{{$manifest->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
            </div>
        </div>
        <form action="{{ route('lcl.racking.updatePhoto')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row mt-0">
                    <div class="form-group">
                        <label for="">Photo Placement</label>
                        <input type="hidden" name="id" value="{{$manifest->id}}">
                        <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-outline-success">Submit</button>
            </div>
        </form>
    </div>
</section>
<section>
    <div class="card">
        <div class="card-body fixed-height-cardBody">
            <table class="tabelCustom">
                <thead>
                    <tr>
                        <th class="text-center">Action</th>
                        <th class="text-center">Barcode Barang</th>
                        <th class="text-center">Name Barang</th>
                        <th class="text-center">Nomor Barang</th>
                        <th class="text-center">Rack</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($placed as $plc)
                        <tr>
                            <td>
                                <div class="button-container">
                                    <button class="btn btn-outline-danger unPlace" data-id="{{$plc->id}}">Batal Placement</button>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0)" onclick="openWindow('/lcl/realisasi/racking/itemBarcode-{{$plc->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                            </td>
                            <td class="text-center">{{$plc->name}}</td>
                            <td class="text-center">{{$plc->nomor}}</td>
                            <td class="text-center">{{$plc->Rack->name ?? ''}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('lcl.racking.update') }}" method="POST">
                @csrf
                <div class="row mt-0">
                    <div class="col-sm-6">
                        <div class="rack-area" id="rack-area">
                            <h3>Rack Area</h3>
                            <div class="form-group">
                                <label for="">Rack</label>
                                <input type="hidden" name="manifest_id" value="{{$manifest->id}}">
                                <select name="lokasi_id" class="js-example-basic-single select2 form-select">
                                    <option disabled selected>Pilih Satu!</option>
                                    @foreach($locs as $loc)
                                        <option value="{{$loc->id}}">{{$loc->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="rack-dropzone dropzone">
                                <!-- Dropzone where items will be placed -->
                            </div>
                            <button id="update-button" class="btn btn-primary mt-3">Update Placement</button>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="unplaced-items">
                            <h3>Unplaced Items</h3>
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="select-all">
                                <label class="form-check-label" for="select-all">Select All</label>
                            </div>
                            <ul id="item-list" class="dropzone">
                                @foreach($item as $it)
                                    <li class="draggable-item" draggable="true" data-item-id="{{ $it->id }}">
                                        {{ $it->nomor }} -- {{ $it->barcode }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection

@section('custom_js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let lastChecked = null;

    // Handle row selection with Shift key
    document.querySelectorAll('tbody tr').forEach((row) => {
        row.addEventListener('click', function(e) {
            if (!lastChecked) {
                lastChecked = this;
                toggleSelection(this);
                return;
            }

            if (e.shiftKey) {
                let start = Array.from(document.querySelectorAll('tbody tr')).indexOf(this);
                let end = Array.from(document.querySelectorAll('tbody tr')).indexOf(lastChecked);
                let range = [start, end].sort((a, b) => a - b);

                document.querySelectorAll('tbody tr').forEach((r, i) => {
                    if (i >= range[0] && i <= range[1]) {
                        toggleSelection(r, true);
                    }
                });
            } else {
                toggleSelection(this);
            }

            lastChecked = this;
        });
    });

    // Handle the unPlace action on all selected rows
    document.querySelectorAll('.unPlace').forEach((button) => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent row click event
            let selectedRows = document.querySelectorAll('tr.selected');
            let ids = Array.from(selectedRows).map(row => row.querySelector('.unPlace').dataset.id);

            if (ids.length > 0) {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: `Apakah Anda yakin ingin membatalkan placement untuk ${ids.length} item?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request to unPlace the selected items
                        $.ajax({
                            type: 'POST',
                            url: '/lcl/realisasi/racking/unPlace',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            cache: false,
                            data: {
                                ids: ids
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Sukses!',
                                        text: response.message,
                                        icon: 'success',
                                    }).then(() => {
                                        location.reload(); // Reload the page after success
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: response.message,
                                        icon: 'error',
                                    });
                                }
                            },
                            error: function(data) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan saat menghapus data.',
                                    icon: 'error',
                                });
                            }
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: 'Pilih item terlebih dahulu',
                    text: 'Silakan pilih item yang ingin di unPlace.',
                    icon: 'info',
                });
            }
        });
    });

    // Function to toggle selection of a row
    function toggleSelection(row, select = null) {
        if (select === null) {
            row.classList.toggle('selected');
        } else if (select) {
            row.classList.add('selected');
        } else {
            row.classList.remove('selected');
        }
    }
});

</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const draggables = document.querySelectorAll('.draggable-item');
    const unplacedDropzone = document.querySelector('#item-list');
    const rackDropzone = document.querySelector('.rack-dropzone');
    const lokasiSelect = document.querySelector('select[name="lokasi_id"]');
    const updateButton = document.getElementById('update-button');
    const selectAllCheckbox = document.getElementById('select-all');

    let selectedItems = new Set();
    let placedItems = [];

    function selectItem(item, index) {
        const itemId = item.dataset.itemId;

        if (!selectedItems.has(itemId)) {
            selectedItems.add(itemId);
            item.classList.add('selected');
            rackDropzone.appendChild(item);
            placedItems.push({ item_id: itemId, location_id: lokasiSelect.value });
        }
    }

    function deselectItem(item, index) {
        const itemId = item.dataset.itemId;

        if (selectedItems.has(itemId)) {
            selectedItems.delete(itemId);
            item.classList.remove('selected');
            unplacedDropzone.appendChild(item);
            placedItems = placedItems.filter(item => item.item_id !== itemId);
        }
    }

    draggables.forEach((draggable, index) => {
        draggable.addEventListener('click', function (e) {
            const parentDropzone = e.target.parentNode;

            if (selectedItems.has(draggable.dataset.itemId)) {
                deselectItem(draggable, index);
            } else {
                selectItem(draggable, index);
            }
        });
    });

    selectAllCheckbox.addEventListener('change', function () {
        if (selectAllCheckbox.checked) {
            draggables.forEach(selectItem);
        } else {
            draggables.forEach(deselectItem);
        }
    });

    updateButton.addEventListener('click', function () {
        // Remove existing hidden inputs
        document.querySelectorAll('input[name="placements[]"]').forEach(input => input.remove());

        // Append hidden inputs with the placed items
        placedItems.forEach(item => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'placements[]';
            input.value = JSON.stringify(item);
            updateButton.closest('form').appendChild(input);
        });
    });
});
</script>

<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=600,height=800');
    }
</script>
@endsection
