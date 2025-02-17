@extends('partial.main')
@section('custom_styles')

<style>
    .draggable-item {
        cursor: pointer;
        margin: 5px 0;
        padding: 10px;
        border: 1px solid #ccc;
    }

    .draggable-item.selected {
        background-color: #d9edf7;
    }

    .dropzone {
        min-height: 200px;
        border: 2px dashed #ccc;
        padding: 10px;
    }
    tr.selected {
        background-color: #d9edf7;
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
                <div class="row mb-0">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="photos">Pilih Foto-foto</label>
                            <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">Keterangan Photo</label>
                            <select name="keteranganPhoto" class="js-example-basic-single form-select select2" style="width: 100%;">
                                <option disabled selected value>Pilih Satu!</option>
                                @foreach($kets as $ket)
                                    <option value="{{$ket->keterangan}}">{{$ket->keterangan}}</option>
                                @endforeach
                            </select>
                        </div>
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
            <div class="table">
                <table class="table-hover" id="tableDetil">
                    <thead>
                        <tr>
                            <th class="text-center">Action</th>
                            <th class="text-center">Barcode Barang</th>
                            <th class="text-center">Name Barang</th>
                            <th class="text-center">Nomor Barang</th>
                            <th class="text-center">Jumlah Barang</th>
                            <th class="text-center">Rack</th>
                            <th class="text-center">Tier</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
                        <div class="unplaced-items">
                            <h3>Unplaced Items</h3>
                            <br>
                            <ul id="item-list" class="dropzone">
                                @foreach($item as $it)
                                    <li class="draggable-item" draggable="true" data-item-id="{{ $it->id }}">
                                        {{ $it->name }} -- {{ $it->barcode }} <!-- Or any other relevant attribute -->
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="rack-area" id="rack-area">
                            <h3>Rack Area</h3>
                            <div class="row mt-0">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Rack</label>
                                        <input type="hidden" name="manifest_id" value="{{$manifest->id}}">
                                        <select name="lokasi_id" class="js-example-basic-single select2 form-select">
                                            <option disabeled selected>Pilih Satu!</option>
                                            @foreach($locs as $loc)
                                                <option value="{{$loc->id}}">{{$loc->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Tier</label>
                                        <select name="tier" class="form-select" id="tier" required>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="rack-dropzone dropzone">
                                <!-- Dropzone where items will be placed -->
                            </div>
                            <button id="update-button" type="button" class="btn btn-primary mt-3">Update Placement</button>
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
    $(document).ready(function(){
        var id = {{$manifest->id}};
        var table = $('#tableDetil').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/lcl/realisasi/racking/itemTableData-' + id,
            columns: [
                { data: 'action', name: 'action', className: 'text-center action-cell' },
                { data: 'barcode', name: 'barcode', className: 'text-center' },
                { data: 'name', name: 'name', className: 'text-center' },
                { data: 'nomor', name: 'nomor', className: 'text-center' },
                { data: 'jumlah_barang', name: 'jumlah_barang', className: 'text-center' },
                { data: 'rack', name: 'rack', className: 'text-center' },
                { data: 'tier', name: 'tier', className: 'text-center' },
            ]
        });

        let lastChecked = null;

        // Handle row selection with Shift key
        $('#tableDetil tbody').on('click', 'tr', function(e) {
            if ($(e.target).closest('td').hasClass('action-cell')) {
                return; // Keluar tanpa memproses seleksi
            }

            if (!lastChecked) {
                lastChecked = this;
                toggleSelection(this);
                return;
            }

            if (e.shiftKey) {
                let rows = $('#tableDetil tbody tr');
                let start = rows.index(this);
                let end = rows.index(lastChecked);
                let range = [start, end].sort((a, b) => a - b);

                rows.each(function(index) {
                    if (index >= range[0] && index <= range[1]) {
                        toggleSelection(this, true);
                    }
                });
            } else {
                toggleSelection(this);
            }

            lastChecked = this;
        });

        // Handle the unPlace action on all selected rows
        $('#tableDetil').on('click', '.unPlace', function(e) {
            e.stopPropagation(); // Prevent row click event
            let selectedRows = $('#tableDetil tbody tr.selected');
            let ids = selectedRows.map(function() {
                return $(this).find('.unPlace').data('id');
            }).get();

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
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait',
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

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
                                        location.reload(); // Reload the DataTable after success
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

        // Function to toggle selection of a row
        function toggleSelection(row, select = null) {
            if (select === null) {
                $(row).toggleClass('selected');
            } else if (select) {
                $(row).addClass('selected');
            } else {
                $(row).removeClass('selected');
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

    let selectedItems = new Set();
    let placedItems = [];
    let lastClickedIndex = null;

    draggables.forEach((draggable, index) => {
        draggable.addEventListener('dragstart', function (e) {
            const selected = Array.from(selectedItems);
            e.dataTransfer.setData('text/plain', JSON.stringify(selected));
        });

        draggable.addEventListener('click', function (e) {
            const itemId = e.target.dataset.itemId;
            if (e.shiftKey && lastClickedIndex !== null) {
                const start = Math.min(lastClickedIndex, index);
                const end = Math.max(lastClickedIndex, index);

                for (let i = start; i <= end; i++) {
                    const item = draggables[i];
                    const itemId = item.dataset.itemId;
                    if (!selectedItems.has(itemId)) {
                        selectedItems.add(itemId);
                        item.classList.add('selected');
                    }
                }
            } else {
                if (selectedItems.has(itemId)) {
                    selectedItems.delete(itemId);
                    e.target.classList.remove('selected');
                } else {
                    selectedItems.add(itemId);
                    e.target.classList.add('selected');
                }
            }
            lastClickedIndex = index;
        });
    });

    function setupDropzone(dropzone) {
        dropzone.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        });

        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            const itemIds = JSON.parse(e.dataTransfer.getData('text/plain'));
            itemIds.forEach(itemId => {
                const item = document.querySelector(`[data-item-id="${itemId}"]`);
                if (item && dropzone !== item.parentNode) {
                    dropzone.appendChild(item);
                    placedItems.push({ item_id: itemId, location_id: lokasiSelect.value });
                }
            });
        });
    }

    setupDropzone(rackDropzone);
    setupDropzone(unplacedDropzone);

    updateButton.addEventListener('click', function () {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to update this record?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            
                // Hapus input yang sudah ada
                document.querySelectorAll('input[name="placements[]"]').forEach(input => input.remove());
            
                // Validasi apakah lokasi dipilih
                if (!lokasiSelect.value) {
                    Swal.fire('Error', 'Please select a location before updating!', 'error');
                    return;
                }
            
                // Tambahkan input tersembunyi
                placedItems.forEach(item => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'placements[]';
                    input.value = JSON.stringify(item);
                    updateButton.closest('form').appendChild(input);
                });
            
                // Submit form
                updateButton.closest('form').submit();
            }
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
