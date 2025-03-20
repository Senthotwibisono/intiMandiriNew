@extends('partial.main')

@section('custom_styles')

@endsection

@section('content')

<section>
    <div class="page-content">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="table">
                        <table class="table-hover table-stripped" id="tableManifest">
                            <thead style="white-space: nowrap;">
                                <tr>
                                    <th class="text-center" style="min-width: 50px;" rowspan="2">-</th>
                                    <th class="text-center" style="min-width: 600px;" colspan="3">Action</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">No PLP</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Tgl PLP</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">No BC11</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Tgl BC11</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">No Container</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Size</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Kapal</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Voy</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Eta</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">TPS Asal</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">No HBL</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Tgl HBL</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Tgl Masuk</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Jam Masuk</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Tgl Stripping</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Jam Stripping</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Tgl Keluar</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Jam Keluar</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Respon Coari</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Last Send Coari</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Respom Codeco</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Last Send Codeco</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Respom Detail</th>
                                    <th class="text-center" style="min-width: 150px;" rowspan="2">Last Send Detail</th>
                                </tr>
                                <tr>
                                    <th>Coari</th>
                                    <th>Codeco</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('custom_js')
<script>
    $('#tableManifest').dataTable({
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], // Pilihan jumlah data
        pageLength: 25, // Default jumlah data per halaman
        dom: 'lBfrtip', // Pastikan ada 'B' untuk menampilkan tombol
        processing: true,
        serverSide: true,
        scrollX: true,
        select: {
            selector: 'td:not(:nth-child(2)):not(:nth-child(3)):not(:nth-child(4))',
        },
        orderCellsTop: true, // Memastikan DataTables membaca multi-header
        fixedHeader: true, // Menjaga header tetap di atas saat scroll
        ajax: '{{ route('cfs.manifest.data') }}',
        columns: [
            {className:'text-center', data: 'id', orderable: false, searchable: false, render: DataTable.render.select()},
            {className:'text-center', data:'coari', name:'coari', orderable: false, searchable: false},
            {className:'text-center', data:'codeco', name:'codeco', orderable: false, searchable: false},
            {className:'text-center', data:'detail', name:'detail', orderable: false, searchable: false},
            {className:'text-center', data:'job.noplp', name:'job.noplp'},
            {className:'text-center', data:'job.ttgl_plp', name:'job.ttgl_plp'},
            {className:'text-center', data:'job.tno_bc11', name:'job.tno_bc11'},
            {className:'text-center', data:'job.ttgl_bc11', name:'job.ttgl_bc11'},
            {className:'text-center', data:'cont.nocontainer', name:'cont.nocontainer'},
            {className:'text-center', data:'cont.size', name:'cont.size'},
            {className:'text-center', data:'kapal', name:'kapal'},
            {className:'text-center', data:'job.voy', name:'job.voy'},
            {className:'text-center', data:'job.eta', name:'job.eta'},
            {className:'text-center', data:'kd_tps_asal', name:'kd_tps_asal'},
            {className:'text-center', data:'nohbl', name:'nohbl'},
            {className:'text-center', data:'tgl_hbl', name:'tgl_hbl'},
            {className:'text-center', data:'tglmasuk', name:'tglmasuk'},
            {className:'text-center', data:'jammasuk', name:'jammasuk'},
            {className:'text-center', data:'tglstripping', name:'tglstripping'},
            {className:'text-center', data:'jamstripping', name:'jamstripping'},
            {className:'text-center', data:'tglrelease', name:'tglrelease'},
            {className:'text-center', data:'jamrelease', name:'jamrelease'},
            {className:'text-center', data:'coari_cfs_response', name:'coari_cfs_response'},
            {className:'text-center', data:'coari_cfs_at', name:'coari_cfs_at'},
            {className:'text-center', data:'codeco_cfs_response', name:'codeco_cfs_response'},
            {className:'text-center', data:'codeco_cfs_at', name:'codeco_cfs_at'},
            {className:'text-center', data:'detil_hbl_cfs_response', name:'detil_hbl_cfs_response'},
            {className:'text-center', data:'detil_hbl_cfs_at', name:'detil_hbl_cfs_at'},
        ],
        initComplete: function () {
            var api = this.api();
            
            api.columns().every(function (index) {
                var column = this;
                var excludedColumns = [0, 1,2,3]; // Kolom yang tidak ingin difilter (detil, flag_segel_merah, lamaHari)
                
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
        }
    });
</script>

<script>
    $('#tableManifest').on('click', '#coariResend', function(){
        let selectedIds = [];
        let selectedRows = $('#tableManifest').DataTable().rows({ selected: true }).data();
            selectedRows.each(function (rowData) {
            selectedIds.push(rowData.id); 
        });
        console.log(selectedIds);

        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
          },
          buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
          title: "Are you sure?",
          text: "Beberapa data akan terupdate jika anda melakukan pengiriman ulang!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, send it!",
          cancelButtonText: "No, cancel!",
          reverseButtons: true
        }).then((result) => {
          if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mengirim ulang...',
                    html: 'Harap tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading(); // Menampilkan loading animasi
                    }
                });
                $.ajax({
                    url: '{{ route('cfs.manifest.coari') }}',
                    type: 'POST',
                    data: {
                        ids: selectedIds,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.success == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Behasil!',
                                text: response.message,
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message,
                            });
                        }
                    },
                    error: function (response) {
                        console.log(response.responseJSON.message);
                        var errorMessages = response.responseJSON.message;
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Opss something wrong! : ' + errorMessages,
                        });
                    }
                    
                });
          } else if (
              result.dismiss === Swal.DismissReason.cancel
            ) {
              swalWithBootstrapButtons.fire({
                title: "Cancelled",
                text: "Pengiriman data dibatalkan",
                icon: "error"
              }).then(() => {
                location.reload();
              });
            }
        });
    })
</script>


<script>
    $('#tableManifest').on('click', '#codecoResend', function(){
        let selectedIds = [];
        let selectedRows = $('#tableManifest').DataTable().rows({ selected: true }).data();
            selectedRows.each(function (rowData) {
            selectedIds.push(rowData.id); 
        });
        console.log(selectedIds);

        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
          },
          buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
          title: "Are you sure?",
          text: "Beberapa data akan terupdate jika anda melakukan pengiriman ulang!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, send it!",
          cancelButtonText: "No, cancel!",
          reverseButtons: true
        }).then((result) => {
          if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mengirim ulang...',
                    html: 'Harap tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading(); // Menampilkan loading animasi
                    }
                });
                $.ajax({
                    url: '{{ route('cfs.manifest.codeco') }}',
                    type: 'POST',
                    data: {
                        ids: selectedIds,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.success == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Behasil!',
                                text: response.message,
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message,
                            });
                        }
                    },
                    error: function (response) {
                        console.log(response.responseJSON.message);
                        var errorMessages = response.responseJSON.message;
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Opss something wrong! : ' + errorMessages,
                        });
                    }
                    
                });
          } else if (
              result.dismiss === Swal.DismissReason.cancel
            ) {
              swalWithBootstrapButtons.fire({
                title: "Cancelled",
                text: "Pengiriman data dibatalkan",
                icon: "error"
              }).then(() => {
                location.reload();
              });
            }
        });
    })
</script>

<script>
    $('#tableManifest').on('click', '#detailResend', function(){
        let selectedIds = [];
        let selectedRows = $('#tableManifest').DataTable().rows({ selected: true }).data();
            selectedRows.each(function (rowData) {
            selectedIds.push(rowData.id); 
        });
        console.log(selectedIds);

        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
          },
          buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
          title: "Are you sure?",
          text: "Beberapa data akan terupdate jika anda melakukan pengiriman ulang!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, send it!",
          cancelButtonText: "No, cancel!",
          reverseButtons: true
        }).then((result) => {
          if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mengirim ulang...',
                    html: 'Harap tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading(); // Menampilkan loading animasi
                    }
                });
                $.ajax({
                    url: '{{ route('cfs.manifest.detil') }}',
                    type: 'POST',
                    data: {
                        ids: selectedIds,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.success == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Behasil!',
                                text: response.message,
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message,
                            });
                        }
                    },
                    error: function (response) {
                        console.log(response.responseJSON.message);
                        var errorMessages = response.responseJSON.message;
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Opss something wrong! : ' + errorMessages,
                        });
                    }
                    
                });
          } else if (
              result.dismiss === Swal.DismissReason.cancel
            ) {
              swalWithBootstrapButtons.fire({
                title: "Cancelled",
                text: "Pengiriman data dibatalkan",
                icon: "error"
              }).then(() => {
                location.reload();
              });
            }
        });
    })
</script>
@endsection