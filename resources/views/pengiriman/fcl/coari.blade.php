@extends('partial.main')
@section('custom_styles')

@endsection

@section('content')

<section>
    <div class="card">
        <div class="card-header">
            <div class="button-container">
                <button class="btn btn-primary" id="sendCoari">Kirim Coari</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table">
                <table class="table-hover text-center" id="dataCoariCont">
                    <thead style="white-space: nowrap; text-align: center; vertical-align: middle;">
                        <tr>
                            <th class="text-center" style="min-width: 100px">Action</th>
                            <th class="text-center" style="min-width: 100px">Ref Number</th>
                            <th class="text-center" style="min-width: 100px">Nm Angkut</th>
                            <th class="text-center" style="min-width: 100px">No Voy</th>
                            <th class="text-center" style="min-width: 100px">Call Sign</th>
                            <th class="text-center" style="min-width: 100px">No Container</th>
                            <th class="text-center" style="min-width: 100px">Size</th>
                            <th class="text-center" style="min-width: 100px">No Bl Awb</th>
                            <th class="text-center" style="min-width: 100px">Tgl Bl Awb</th>
                            <th class="text-center" style="min-width: 100px">No Master Bl Awb</th>
                            <th class="text-center" style="min-width: 100px">Tgl Master Bl Awb</th>
                            <th class="text-center" style="min-width: 100px">Response</th>
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

@endsection

@section('custom_js')

<script>
        $(document).on('click', '#sendCoari', function(){
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
                        url: '/testCoariContFCL',
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
        $(document).on('click', '#kirimUlang', function(){
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
                        url: '{{ route('pengiriman.lcl.sendContLCL')}}',
                        type: 'POST',
                        data : {
                            _token: "{{ csrf_token() }}",
                            type : 'FCL',
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
            ajax: '{{ route('pengiriman.fcl.data')}}',
            responsive: true,
            columns: [
                {name: 'action', data: 'action'},
                {name: 'ref_number', data: 'ref_number'},
                {name: 'nm_angkut', data: 'nm_angkut'},
                {name: 'no_voy_flight', data: 'no_voy_flight'},
                {name: 'call_sign', data: 'call_sign'},
                {name: 'no_cont', data: 'no_cont'},
                {name: 'uk_cont', data: 'uk_cont'},
                {name: 'no_bl_awb', data: 'no_bl_awb'},
                {name: 'tgl_bl_awb', data: 'tgl_bl_awb'},
                {name: 'no_master_bl_awb', data: 'no_master_bl_awb'},
                {name: 'tgl_master_bl_awb', data: 'tgl_master_bl_awb'},
                {name: 'response', data: 'response'},
                {name: 'wk_inout', data: 'wk_inout'},
                {name: 'tgl_entry', data: 'tgl_entry'},
                {name: 'jam_entry', data: 'jam_entry'},
            ],
            columnDefs: [
                { targets: '_all', style: 'min-width-100' }
            ],
            initComplete: function () {
                var api = this.api();
                
                api.columns().every(function (index) {
                    var column = this;
                    var excludedColumns = [0]; // Kolom yang tidak ingin difilter (detil, flag_segel_merah, lamaHari)
                    
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