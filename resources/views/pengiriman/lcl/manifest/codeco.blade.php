@extends('partial.main')
@section('custom_styles')

@endsection

@section('content')

<section>
    <div class="card">
        <div class="card-header">
            <div class="button-container">
                <div class="col-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addManual">Upload Codeco KMS</button>
                <button type="button" class="btn btn-primary" id="testCoari">Upload Codeco</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table">
                <table class="table-hover text-center" id="dataCoariCont">
                    <thead style="white-space: nowrap;">
                        <tr>
                            <th class="text-center" style="min-width: 100px">Action</th>
                            <th class="text-center" style="min-width: 100px">Kirim Ke CFS</th>
                            <th class="text-center" style="min-width: 100px">Ref Number</th>
                            <th class="text-center" style="min-width: 100px">No Container</th>
                            <th class="text-center" style="min-width: 100px">Size</th>
                            <th class="text-center" style="min-width: 100px">No House Bl Awb</th>
                            <th class="text-center" style="min-width: 100px">Tgl House Bl Awb</th>
                            <th class="text-center" style="min-width: 100px">Consignee</th>
                            <th class="text-center" style="min-width: 100px">Bruto</th>
                            <th class="text-center" style="min-width: 100px">No Master Bl Awb</th>
                            <th class="text-center" style="min-width: 100px">Tgl Master Bl Awb</th>
                            <th class="text-center" style="min-width: 100px">Response</th>
                            <th class="text-center" style="min-width: 100px">Jenis Dok</th>
                            <th class="text-center" style="min-width: 100px">No Dok</th>
                            <th class="text-center" style="min-width: 100px">Tgl Dok</th>
                            <th class="text-center" style="min-width: 100px">Waktu In Out</th>
                            <th class="text-center" style="min-width: 100px">Tgl Kirim</th>
                            <th class="text-center" style="min-width: 100px">Jam Kirim</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="addManual" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Coari KMS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('pengiriman.lcl.kirimManualManifest') }}"  id="uploadForm">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <select name="manifest_id" id="manifest_id" class="customSelect select2 form-selelct" style="width: 100%">
                                <option disabled selected value>Pilih Satu</option>
                                @foreach($manifestes as $mans)
                                    <option value="{{$mans->id}}">{{$mans->nohbl}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" id="submit" class="btn btn-primary">Upload</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script>
        $(document).on('click', '#testCoari', function(){
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

                    $.ajax({
                        url: '/testCodeco',
                        type: 'POST',
                        data : {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response){
                            console.log(response);
                            if (response.success) {
                                Swal.fire('Success', response.message, 'success')
                                .then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error')
                                .then(() => {
                                    // Memuat ulang halaman setelah berhasil menyimpan data
                                    window.location.reload();
                                });
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
                                console.log('error:', response);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    text: response.responseJSON.message,
                                });
                            }
                        },
                    })
                }
            });
        });
</script>
<script>
        $(document).on('click', '#submit', function(){
            let id = $('#manifest_id').val();
            console.log('Data Id :' + id);
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

                    $.ajax({
                        url: '/pengiriman/codeco-lcl/manifest-kirimManual',
                        type: 'POST',
                        data : {
                            _token: "{{ csrf_token() }}",
                            id: id,
                        },
                        success: function(response){
                            console.log(response);
                            if (response.success) {
                                Swal.fire('Success', response.message, 'success')
                                .then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error')
                                .then(() => {
                                    // Memuat ulang halaman setelah berhasil menyimpan data
                                    window.location.reload();
                                });
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
                                console.log('error:', response);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    text: response.responseJSON.message,
                                });
                            }
                        },
                    })
                }
            });
        });
</script>
<script>
        $(document).on('click', '#kirimUlangCoari', function(){
            let id = $(this).data('id');
            console.log('Data Id :' + id);
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

                    $.ajax({
                        url: '/pengiriman/codeco-lcl/manifest-KirimUlang',
                        type: 'POST',
                        data : {
                            _token: "{{ csrf_token() }}",
                            id: id,
                        },
                        success: function(response){
                            console.log(response);
                            if (response.success) {
                                Swal.fire('Success', response.message, 'success')
                                .then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error')
                                .then(() => {
                                    // Memuat ulang halaman setelah berhasil menyimpan data
                                    window.location.reload();
                                });
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
                                console.log('error:', response);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    text: response.responseJSON.message,
                                });
                            }
                        },
                    })
                }
            });
        });
</script>

<script>
    $(document).ready(function(){
        $('#dataCoariCont').DataTable({
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], // Pilihan jumlah data
            pageLength: 25, // Default jumlah data per halaman
            dom: 'lBfrtip',
            buttons: ['excel', 'pdf'],
            processing: true, 
            serverSide: true, 
            scrollX: true,
            ajax: '/pengiriman/codeco-lcl/manifest-data',
            columns: [
                {name: 'action', data: 'action'},
                {name: 'cfs', data: 'cfs'},
                {name: 'ref_number', data: 'ref_number'},
                {name: 'cont', data: 'cont'},
                {name: 'size', data: 'size'},
                {name: 'no_bl_awb', data: 'no_bl_awb'},
                {name: 'tgl_bl_awb', data: 'tgl_bl_awb'},
                {name: 'consignee', data: 'consignee'},
                {name: 'bruto', data: 'bruto'},
                {name: 'no_master_bl_awb', data: 'no_master_bl_awb'},
                {name: 'tgl_master_bl_awb', data: 'tgl_master_bl_awb'},
                {name: 'response', data: 'response'},
                {name: 'jenisDok', data: 'jenisDok'},
                {name: 'nodok', data: 'nodok'},
                {name: 'tglDok', data: 'tglDok'},
                {name: 'wk_inout', data: 'wk_inout'},
                {name: 'tgl_entry', data: 'tgl_entry'},
                {name: 'jam_entry', data: 'jam_entry'},
            ],
            initComplete: function () {
                var api = this.api();
                
                api.columns().every(function (index) {
                    var column = this;
                    var excludedColumns = [0, 1]; // Kolom yang tidak ingin difilter (detil, flag_segel_merah, lamaHari)
                    
                    if (excludedColumns.includes(index)) {
                        $('<th></th>').appendTo(column.header()); // Kosongkan header pencarian untuk kolom yang dikecualikan
                        return;
                    }

                    var $th = $(column.header());
                    var $input = $('<input type="text" class="form-control form-control-sm" placeholder="Search ' + $th.text() + '">')
                        .appendTo($('<th class="text-center"></th>').appendTo($th))
                        .on('keyup change', function () {
                            column.search($(this).val()).draw();
                        });
                });
            }, 
        })
    })
</script>
@endsection