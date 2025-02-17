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

    /* Your existing styles here */
    .barcode-scanner {
        margin-bottom: 15px;
    }
</style>

@endsection
@section('content')


<section>
    <div class="card">
        <form action="{{ route('photo.lcl.storeManifest')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">No HBL</label>
                        <input type="text" name="nohbl" value="{{$item->manifest->nohbl}}" id="nohbl_edit" class="form-control" readonly>
                        <input type="hidden" name="id" value="{{$item->id}}" id="id_edit" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Nomor Palet</label>
                        <input type="text" name="quantity" value="{{$item->nomor}}" id="quantity_edit" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Jumlah Dalam Palet</label>
                        <input type="text" name="quantity" value="{{$item->jumlah_barang}} / {{$item->manifest->quantity}}" id="quantity_edit" class="form-control" readonly>
                    </div>
                </div>
                <div class="card-body text-center">
                    <div id="qr-reader-container">
                        <video id="video" autoplay playsinline></video>
                        <canvas id="canvas" hidden></canvas>
                    </div>
                </div>
                
            </div>
            <div class="card-footer">
                <div class="button-container">
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </div>
        </form>
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

<!-- <script>
  document.addEventListener('DOMContentLoaded', (event) => {
    const scanButton = document.getElementById('scan-button');
    const rackSelect = document.getElementById('rack-select');
    const scanModal = new bootstrap.Modal(document.getElementById('scanModal'));

    scanButton.addEventListener('click', () => {
      scanModal.show();


      const html5QrCode = new Html5Qrcode("reader");

      html5QrCode.start(
        { facingMode: "environment" }, 
        {
          fps: 10,    
          qrbox: 250  
        },
        (decodedText, decodedResult) => {
          const barcodeId = decodedText;

          const optionExists = Array.from(rackSelect.options).some(option => option.value === barcodeId);

          if (optionExists) {
            rackSelect.value = barcodeId;

            const event = new Event('change');
            rackSelect.dispatchEvent(event);

            html5QrCode.stop().then(() => {
              scanModal.hide();
            }).catch((err) => {
              console.log("Failed to stop scanning.", err);
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Barcode Not Found',
              text: 'The scanned barcode does not match any available rack.',
            });
          }
        },
        (errorMessage) => {
          console.log("QR code scanning error:", errorMessage);
        }
      ).catch((err) => {
        console.log("Failed to stop scanning.", err);
      });
    });
  });
</script> -->

<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
<script>
   const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const canvasContext = canvas.getContext('2d');

// Fungsi untuk memulai kamera
async function startCamera() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        video.srcObject = stream;
        video.setAttribute('playsinline', true);
        video.play();
        scanQRCode();
    } catch (err) {
        console.error('Error accessing camera:', err);
        Swal.fire('Error', 'Kamera tidak dapat diakses. Pastikan izin kamera telah diberikan.', 'error');
    }
}

// Fungsi untuk memindai QR Code
function scanQRCode() {
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
        const qrCode = jsQR(imageData.data, canvas.width, canvas.height);

        if (qrCode) {
            Swal.fire({
                title: 'Loading...',
                text: 'Memproses data, harap tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            let formData = {
                qr_code: qrCode.data,
                id: document.querySelector('#id_edit').value
            };

            fetch('/android/lcl/rackingAndroid', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.message); });
                }
                return response.json();
            })
            .then(result => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Data berhasil diproses!',
                    showConfirmButton: true
                }).then(() => {
                    window.location.href = '/android/lcl/racking';
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan: ' + error.message
                });
                console.error('Error:', error);
            });

            stopCamera();
        }
    }
    requestAnimationFrame(scanQRCode);
}

// Fungsi untuk menghentikan kamera
function stopCamera() {
    if (video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
        video.srcObject = null;
    }
}

// Mulai kamera saat halaman dimuat
startCamera();

</script>



@endsection
