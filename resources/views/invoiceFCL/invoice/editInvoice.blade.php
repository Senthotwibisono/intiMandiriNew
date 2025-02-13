@extends('partial.main')

@section('content')

<body>
    <div class="card">
        <div class="card-header">
    
        </div>
        <div class="card-body">
            <form action="/invoiceFCL/invoice/updateInvoice" method="post" id="submitForm">
                @csrf
                <div class="divider divider-center">
                    <div class="divider-text">
                        General Information
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">Invoice No</label>
                            <input type="text" class="form-control" value="{{$header->invoice_no ?? '-'}}" readonly>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">Proforma No</label>
                            <input type="text" class="form-control" value="{{$header->proforma_no ?? '-'}}" readonly>
                        </div>
                    </div>
                    <!-- Cust -->
                     <div class="divider divider-left">
                        <div class="divider-text">
                            Customer Information
                        </div>
                     </div>
                     <div class="col-3">
                        <div class="form-group">
                            <label for="">Customer Name</label>
                            <input type="text" class="form-control" value="{{$header->cust_name ?? '-'}}" name="cust_name">
                        </div>
                     </div>
                     <div class="col-3">
                        <div class="form-group">
                            <label for="">Customer NPWP</label>
                            <input type="text" class="form-control" value="{{$header->cust_npwp ?? '-'}}" name="cust_npwp">
                        </div>
                     </div>
                     <div class="col-3">
                        <div class="form-group">
                            <label for="">Customer Fax</label>
                            <input type="text" class="form-control" value="{{$header->cust_fax ?? '-'}}" name="cust_fax">
                        </div>
                     </div>
                     <div class="col-3">
                        <div class="form-group">
                            <label for="">Customer Alamat</label>
                            <textarea name="" class="form-control" id="" name="cust_alamat">{{$header->cust_alamat ?? '-'}}</textarea>
                        </div>
                     </div>
                     <div class="divider divider-left">
                        <div class="divider-text">
                            Cash Information
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                            <label for="">Total TPS ({{$header->kd_tps_asal}})</label>
                            <input type="text" class="form-control" value="{{ number_format($header->total_tps, 2) }}" readonly>
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                            <label for="">Total WMS</label>
                            <input type="text" class="form-control" value="{{ number_format($header->total_wms, 2) }}" readonly>
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                            <label for="">Total</label>
                            <input type="text" class="form-control" value="{{ number_format($header->total, 2) }}" readonly>
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                            <label for="">Admin</label>
                            <input type="text" class="form-control" value="{{ number_format($header->admin, 0) }}" readonly>
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                            <label for="">PPN</label>
                            <input type="text" class="form-control" value="{{ number_format($header->ppn, 0) }}" readonly>
                        </div>
                     </div>
                     <div class="col-4">
                        <div class="form-group">
                            <label for="">Grand Total</label>
                            <input type="text" class="form-control" value="{{ number_format($header->grand_total, 0) }}" readonly>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="form-group">
                            <label for="">Jumlah Bayar</label>
                            <input type="text" name="jumlah_bayar" value="{{number_format($header->jumlah_bayar, 0 ?? '0')}}" class="form-control">
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="form-group">
                            <label for="">Keterangan</label>
                            @if($header->grand_total == $header->jumlah_bayar)
                                <textarea readonly class="form-control" id="" cols="30" rows="10">Pembayaran Sudah Sesuai</textarea>
                            @else
                                @if($header->grand_total > $header->jumlah_bayar)
                                    <textarea readonly class="form-control" id="" cols="30" rows="10">Pembayaran kurang sebesar : {{number_format(abs($header->sisa_bayar), 0 ?? '0')}}</textarea>
                                @else
                                    <textarea readonly class="form-control" id="" cols="30" rows="10">Pembayaran lebih sebesar : {{number_format(abs($header->sisa_bayar), 0 ?? '0')}}</textarea>
                                @endif
                            @endif
                        </div>
                     </div>
                     <div class="divider divider-left">
                        <div class="divider-text">
                            Edit Able
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="form-group">
                            <label for="">Create At</label>
                            <input type="datetime-local" class="form-control" value="{{$header->created_at}}" name="created_at">
                            <input type="hidden" name="id" value="{{$header->id}}">
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="form-group">
                            <label for="">Lunas At</label>
                            <input type="datetime-local" class="form-control" value="{{$header->lunas_at}}" name="lunas_at">
                        </div>
                     </div>
                     <div class="divider divider-left">
                        <div class="divider-text">
                            EMKL Information
                        </div>
                     </div>
                     <div class="col-3">
                        <div class="form-group">
                            <label for="">Nomor Hand Phone</label>
                            <input type="text" name="no_hp" value="{{$header->no_hp}}" placeholder="Belum Di Isi" class="form-control">
                        </div>
                     </div>
                     <div class="col-9 text-center">
                        <div class="form-group">
                            <label for="">Photo KTP</label>
                            <br>
                            @if($header->ktp != null)
                            <img src="{{ asset('storage/ktpFCL/' . $header->ktp) }}" alt="Photo" class="img-fluid" style="width: 400px; height: 400px; object-fit: cover;">
                            <br>
                            <br>
                            <button class="btn btn-danger deletePhoto" type="button" data-id="{{$header->id}}">Hapus Photo KTP</button>
                            @else
                            <div id="cameraSection">
                                <video id="video" width="400" height="400" autoplay style="border: 1px solid black;"></video>
                                <canvas id="canvas" style="display: none;"></canvas>
                                <br>
                                <button class="btn btn-primary" type="button" id="capture">Capture</button>
                                <button class="btn btn-success" type="button" id="uploadFromGallery">Pilih dari Galeri</button>
                                <input type="file" id="fileInput" accept="image/*" style="display: none;">
                                <form id="uploadForm" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="image" id="capturedImage">
                                </form>
                            </div>
                            @endif
                        </div>
                     </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <div class="button-container">
                <div class="col-auto">
                    <a href="/invoiceFCL/invoice/index" class="btn btn-warning">Back</a>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" type="button" id="submitButton">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table">
                <table class="table-custom table-responsive" style="white-space: nowrap;">
                    <thead>
                        <tr>
                            <th>No Container</th>
                            <th>No BL AWB</th>
                            <th>Tgl BL AWB</th>
                            <th>Size</th>
                            <th>Type</th>
                            <th>Kode Dok In Out</th>
                            <th>No Dok In Out</th>
                            <th>Tgl Dok In Out</th>
                            <th>Tgl Masuk</th>
                            <th>Cetak Bon Muat</th>
                            <th>Cetak SP2</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($formC as $cont)
                            <tr>
                                <td>{{$cont->cont->nocontainer ?? '-'}}</td>
                                <td>{{$cont->cont->nobl ?? '-'}}</td>
                                <td>{{$cont->cont->tgl_bl_awb ?? '-'}}</td>
                                <td>{{$cont->size ?? '-'}}</td>
                                <td>{{$cont->ctr_type ?? '-'}}</td>
                                <td>{{$cont->dokumen->name ?? '-'}}</td>
                                <td>{{$cont->no_dok_inout ?? '-'}}</td>
                                <td>{{$cont->tgl_dok_inout ?? '-'}}</td>
                                <td>{{$cont->tglmasuk ?? '-'}}</td>
                                <td>
                                    <button class="btn btn-danger printBarcode" data-id="{{$cont->cont->id}}"><i class="fa fa-print"></i></button>
                                </td>
                                <td>
                                    <a href="/fcl/sp2/{{$cont->cont->id}}" target="blank" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

@endsection

@section('custom_js')
<script>
    $(document).ready(function() {
        let video = document.getElementById('video');
        let canvas = document.getElementById('canvas');
        let context = canvas.getContext('2d');
        let capturedImageInput = document.getElementById('capturedImage');
        let id = {{$header->id}};

        // Aktifkan Kamera
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
                video.srcObject = stream;
                video.play();
            }).catch(function(error) {
                console.error("Kamera tidak dapat diakses", error);
            });
        }

        // Capture Foto dari Kamera
        $("#capture").click(function() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            let imageData = canvas.toDataURL("image/png"); // Konversi ke base64
            capturedImageInput.value = imageData;
            uploadImage(imageData);
        });

        // Upload dari Galeri
        $("#uploadFromGallery").click(function() {
            $("#fileInput").click();
        });

        $("#fileInput").change(function(event) {
            let file = event.target.files[0];
            let reader = new FileReader();
            reader.onload = function(e) {
                uploadImage(e.target.result);
            };
            reader.readAsDataURL(file);
        });

        // Fungsi Upload Foto
        function uploadImage(imageData) {
            $.ajax({
                url: "/invoiceFCL/invoice/uploadKTP",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    image: imageData,
                    id : id
                },
                success: function(response) {
                    Swal.fire("Berhasil!", "Foto KTP berhasil diunggah!", "success");
                    location.reload();
                },
                error: function(error) {
                    Swal.fire("Error!", "Gagal mengunggah foto KTP.", "error");
                }
            });
        }
    });
</script>
<script>
    $(document).on('click', '.deletePhoto', function(){
        let id = $(this).data('id');

        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Foto KTP akan dihapus secara permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/invoiceFCL/invoice/deleteKPT/" + id, 
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire("Terhapus!", "Foto KTP berhasil dihapus.", "success");
                        location.reload(); 
                    },
                    error: function() {
                        Swal.fire("Error!", "Gagal menghapus foto KTP.", "error");
                    }
                });
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('submitButton').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Pastikan Data yang Anda Masukkan sudah Benar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    document.getElementById('submitForm').submit();
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

<script>
    $(document).on('click', '.printBarcode', function(e) {
        e.preventDefault();
        var containerId = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        Swal.fire({
            icon: 'question',
            title: 'Do you want to generate the barcode?',
            showCancelButton: true,
            confirmButtonText: 'Generate',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '/fcl/delivery/gatePassBonMuat',
                    data: { id: containerId },
                    cache: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Generated!', '', 'success')
                                .then(() => {
                                    var barcodeId = response.data.id;
                                    window.open('/barcode/autoGate-bonmuat' + barcodeId, '_blank', 'width=600,height=800');
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
</script>
@endsection
