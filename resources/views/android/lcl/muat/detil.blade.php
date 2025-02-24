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
        <div class="card-body">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="">No HBL/Customer</label>
                    <input type="text" name="nohbl" value="{{($manifest->nohbl) .' / '. ($manifest->customer->name ?? '-')}}" id="nohbl_edit" class="form-control" readonly>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="">Quantity/Palet</label>
                    <input type="text" name="nohbl" value="{{($manifest->quantity) .' / '. ($manifest->palet ?? '-')}}" id="nohbl_edit" class="form-control" readonly>
                </div>
            </div>
            @if($manifest->mulai_muat == null)
            <div class="container-button">
                <div class="col-auto ms-2">
                    <button class="btn btn-primary startMuat" type="button" data-id="{{$manifest->id}}">Mulai Muat</button>
                </div>
            </div>
            @else
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="">Mulai Muat</label>
                    <input type="text" name="nohbl" value="{{($manifest->mulai_muat ?? '-')}}" id="nohbl_edit" class="form-control" readonly>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="">UID Muat</label>
                    <input type="text" name="nohbl" value="{{($manifest->uidMuat->name ?? '-')}}" id="nohbl_edit" class="form-control" readonly>
                </div>
            </div>
            @endif
            @if($manifest->mulai_muat != null)
            <div class="table">
                <table class="table-responsive table-hover" style="margin: auto;">
                    <thead>
                        <tr>
                            <th class="text-center">Jumlah Barang</th>
                            <th class="text-center">Rack</th>
                            <th class="text-center">Tier</th>
                            <th class="text-center">Scan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td class="text-center">{{$item->jumlah_barang}}</td>
                            <td class="text-center">{{$item->Rack->name ?? '-'}}</td>
                            <td class="text-center">{{$item->Tier->tier ?? '-'}}</td>
                            <td class="text-center">
                                @if($item->waktu_muat == null)
                                <div class="col-auto ms-2">
                                    <button class="btn btn-primary muat" data-id="{{$item->id}}" type="button">Scan</button>
                                </div>
                                @else
                                <span>{{$item->waktu_muat}}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        <div class="card-footer">
            <div class="button-container">
                <button class="btn btn-success selesai" data-id="{{$manifest->id}}" type="button">Selesai</button>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scanModalLabel">Scan Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>ID:</strong> <span id="modalItemId"></span></p>
                <input type="hidden" name="id" id="idItem">
                <div id="qr-reader-container">
                    <video id="video" autoplay playsinline></video>
                    <canvas id="canvas" hidden></canvas>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('custom_js')
<script>
    $(document).ready(function () {
        $('.startMuat').click(function () {
            var manifestId = $(this).data('id'); // Ambil ID dari tombol

            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin memulai muat?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Mulai",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading sebelum mengarahkan
                    Swal.fire({
                        title: "Memproses...",
                        text: "Silakan tunggu...",
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Redirect ke URL dengan ID
                    $.ajax({
                        url: "/android/muat/mulaiMuat",
                        type: "POST",
                        data: {
                            id: manifestId,
                            _token: $('meta[name="csrf-token"]').attr('content') // Kirim CSRF token
                        },
                        success: function (response) {
                            Swal.fire({
                                title: "Berhasil!",
                                text: "Proses muat berhasil dimulai.",
                                icon: "success",
                                // timer: 2000,
                                showConfirmButton: true
                            }).then(() => {
                                location.reload(); // Refresh halaman setelah sukses
                            });
                        },
                        error: function () {
                            Swal.fire({
                                title: "Gagal!",
                                text: "Terjadi kesalahan. Silakan coba lagi.",
                                icon: "error",
                                confirmButtonText: "Tutup"
                            });
                        }
                    });
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('.selesai').click(function () {
            var manifestId = $(this).data('id'); // Ambil ID dari tombol

            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin mengakhiri proses muat?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Mulai",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading sebelum mengarahkan
                    Swal.fire({
                        title: "Memproses...",
                        text: "Silakan tunggu...",
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Redirect ke URL dengan ID
                    $.ajax({
                        url: "/android/muat/selesaiMuat",
                        type: "POST",
                        data: {
                            id: manifestId,
                            _token: $('meta[name="csrf-token"]').attr('content') // Kirim CSRF token
                        },
                        success: function (response) {
                            Swal.fire({
                                title: "Berhasil!",
                                text: "Proses muat Selesai.",
                                icon: "success",
                                // timer: 2000,
                                showConfirmButton: true
                            }).then(() => {
                                location.reload(); // Refresh halaman setelah sukses
                            });
                        },
                        error: function () {
                            Swal.fire({
                                title: "Gagal!",
                                text: "Terjadi kesalahan. Silakan coba lagi.",
                                icon: "error",
                                confirmButtonText: "Tutup"
                            });
                        }
                    });
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('.startMuat').click(function () {
            var manifestId = $(this).data('id'); // Ambil ID dari tombol

            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin memulai muat?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Mulai",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading sebelum mengarahkan
                    Swal.fire({
                        title: "Memproses...",
                        text: "Silakan tunggu...",
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Redirect ke URL dengan ID
                    $.ajax({
                        url: "/android/muat/mulaiMuat",
                        type: "POST",
                        data: {
                            id: manifestId,
                            _token: $('meta[name="csrf-token"]').attr('content') // Kirim CSRF token
                        },
                        success: function (response) {
                            Swal.fire({
                                title: "Berhasil!",
                                text: "Proses muat berhasil dimulai.",
                                icon: "success",
                                // timer: 2000,
                                showConfirmButton: true
                            }).then(() => {
                                location.reload(); // Refresh halaman setelah sukses
                            });
                        },
                        error: function () {
                            Swal.fire({
                                title: "Gagal!",
                                text: "Terjadi kesalahan. Silakan coba lagi.",
                                icon: "error",
                                confirmButtonText: "Tutup"
                            });
                        }
                    });
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
$(document).ready(function () {
    $('.muat').click(function () {
        var itemId = $(this).data('id'); // Ambil ID dari tombol
        $('#idItem').val(itemId); // Set ID ke dalam modal
        $('#scanModal').modal('show'); // Tampilkan modal

        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const canvasContext = canvas.getContext('2d');

        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' } // Gunakan kamera belakang
                });
                video.srcObject = stream;
                video.setAttribute('playsinline', true);
                video.play();
                scanQRCode(); // Mulai pemindaian QR Code
            } catch (err) {
                console.error('Error accessing camera:', err);
                Swal.fire('Gagal', 'Kamera tidak dapat diakses. Pastikan izin kamera diberikan.', 'error');
            }
        }

        function scanQRCode() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);

                const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
                const qrCode = jsQR(imageData.data, canvas.width, canvas.height);

                if (qrCode) {
                    stopCamera(); // Hentikan kamera setelah scan berhasil

                    Swal.fire({
                        title: 'Loading...',
                        text: 'Memproses data, harap tunggu...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim data QR Code melalui AJAX
                    $.ajax({
                        url: '/android/muat/muatItem',
                        type: 'POST',
                        data: {
                            qr_code: qrCode.data,
                            id: itemId,
                            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                        },
                        success: function (response) {
                            console.log('Response:', response);
                            
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                }).then(() => {
                                    location.reload(); // Refresh halaman
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: 'Server Error: ' + xhr.responseText
                            });
                        }
                    });

                    $('#scanModal').modal('hide'); // Tutup modal setelah scan
                }
            }
            requestAnimationFrame(scanQRCode); // Lakukan pemindaian terus-menerus
        }

        function stopCamera() {
            const stream = video.srcObject;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
            }
        }

        // Hentikan kamera saat modal ditutup
        $('#scanModal').on('hidden.bs.modal', function () {
            stopCamera();
        });

        startCamera(); // Mulai kamera saat modal dibuka
    });
});

</script>

<script>
    // const video = document.getElementById('video');
    // const canvas = document.getElementById('canvas');
    // const canvasContext = canvas.getContext('2d');

    // // Fungsi untuk memulai kamera
    // async function startCamera() {
    //     try {
    //         const stream = await navigator.mediaDevices.getUserMedia({
    //             video: { facingMode: 'environment' }, // Gunakan kamera belakang
    //         });
    //         video.srcObject = stream;
    //         video.setAttribute('playsinline', true); // Untuk iOS
    //         video.play();
    //         scanQRCode();
    //     } catch (err) {
    //         console.error('Error accessing camera:', err);
    //         alert('Kamera tidak dapat diakses. Pastikan izin kamera telah diberikan.');
    //     }
    // }

    // Fungsi untuk memindai QR Code
    // function scanQRCode() {
    //     if (video.readyState === video.HAVE_ENOUGH_DATA) {
    //         canvas.width = video.videoWidth;
    //         canvas.height = video.videoHeight;

    //         // Gambar frame video ke canvas
    //         canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);

    //         // Ambil data gambar dari canvas
    //         const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);

    //         // Dekode QR Code menggunakan jsQR
    //         const qrCode = jsQR(imageData.data, canvas.width, canvas.height);

    //         if (qrCode) {
    //             // Redirect ke URL berdasarkan hasil scan
    //             window.location.href = '/android/muat/detil/' + qrCode.data;

    //             // Hentikan kamera setelah menemukan QR Code
    //             stopCamera();
    //         }
    //     }

    //     // Lakukan pemindaian secara terus-menerus
    //     requestAnimationFrame(scanQRCode);
    // }

    // Fungsi untuk menghentikan kamera
    function stopCamera() {
        const stream = video.srcObject;
        const tracks = stream.getTracks();

        tracks.forEach((track) => track.stop());
        video.srcObject = null;
    }

    // Mulai kamera saat halaman dimuat
    startCamera();
</script>



@endsection
