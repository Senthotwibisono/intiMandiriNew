@extends('partial.main')

@section('custom_styles')

@endsection

@section('content')
<section>
    <div class="pange-content">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="table">
                        <table class="table-hover table-stripped" id="tableContainer">
                            <thead class="" style="white-space: nowrap;">
                                <th class="text-center"></th>
                                <th class="text-center" style="min-width: 200px;">Action</th>
                                <th class="text-center" style="min-width: 200px;">No PLP</th>
                                <th class="text-center" style="min-width: 200px;">Tgl PLP</th>
                                <th class="text-center" style="min-width: 200px;">No BC11</th>
                                <th class="text-center" style="min-width: 200px;">Tgl BC11</th>
                                <th class="text-center" style="min-width: 200px;">TPS Asal</th>
                                <th class="text-center" style="min-width: 200px;">ETA</th>
                                <th class="text-center" style="min-width: 200px;">Ex Kapal</th>
                                <th class="text-center" style="min-width: 200px;">Voy</th>
                                <th class="text-center" style="min-width: 200px;">Nomor Container</th>
                                <th class="text-center" style="min-width: 200px;">Ukuran Container</th>
                                <th class="text-center" style="min-width: 200px;">No BL Awb</th>
                                <th class="text-center" style="min-width: 200px;">Tgl BL Awb</th>
                                <th class="text-center" style="min-width: 200px;">Tgl Masuk</th>
                                <th class="text-center" style="min-width: 200px;">Jam Masuk</th>
                                <th class="text-center" style="min-width: 200px;">Respon Coari</th>
                                <th class="text-center" style="min-width: 200px;">Last Send</th>
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
    let excel = {
                    extend: 'excelHtml5',
                    autoFilter: true,
                    sheetName: 'Exported data',
                    className: 'btn btn-outline-success',
                };
    let pdf = {
                extend: 'pdfHtml5',
                text: 'Ekspor PDF',
                className: 'btn btn-outline-danger',
                orientation: 'landscape', // Mode lanskap untuk tampilan lebih luas
                pageSize: 'A1', // Pilihan ukuran kertas (bisa A3, A4, A5, dll.)
                download: 'open', // Membuka file langsung tanpa mendownload
                exportOptions: {
                    columns: function (idx, data, node) {
                        return true; // Semua kolom akan diekspor, termasuk yang tersembunyi
                    }
                },
                customize: function (doc) {
                    doc.defaultStyle.fontSize = 8; // Mengatur ukuran font agar semua data muat
                    doc.styles.tableHeader.fontSize = 8; // Mengatur ukuran header tabel
                    doc.styles.title.fontSize = 12; // Ukuran font judul
                    doc.pageMargins = [2, 2, 2, 2]; // Mengatur margin halaman
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split(''); 
                }
            };
    var table = $('#tableContainer').dataTable({
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], // Pilihan jumlah data
        pageLength: 25, // Default jumlah data per halaman
        dom: 'lBfrtip', // Pastikan ada 'B' untuk menampilkan tombol
        buttons: [
            'copy', 'csv', excel , pdf, 'print'
        ],
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: '{{ route('cfs.container.data')}}',
        select: {
            selector: 'td:not(:nth-child(1)):not(:nth-child(2)):not(:nth-child(3))',
        },
        columns: [
            {className:'text-center', data: 'id', orderable: false, searchable: false, render: DataTable.render.select()},
            {className:'text-center', data:'action', name:'action', searchable:false},
            {className:'text-center', data:'job.noplp', name:'job.noplp'},
            {className:'text-center', data:'job.ttgl_plp', name:'job.ttgl_plp'},
            {className:'text-center', data:'job.tno_bc11', name:'job.tno_bc11'},
            {className:'text-center', data:'job.ttgl_bc11', name:'job.ttgl_bc11'},
            {className:'text-center', data:'kd_tps_asal', name:'kd_tps_asal'},
            {className:'text-center', data:'job.eta', name:'job.eta'},
            {className:'text-center', data:'kapal', name:'kapal'},
            {className:'text-center', data:'job.voy', name:'job.voy'},
            {className:'text-center', data:'nocontainer', name:'nocontainer'},
            {className:'text-center', data:'size', name:'size'},
            {className:'text-center', data:'nobl', name:'nobl'},
            {className:'text-center', data:'tgl_bl_awb', name:'tgl_bl_awb'},
            {className:'text-center', data:'tglmasuk', name:'tglmasuk'},
            {className:'text-center', data:'jammasuk', name:'jammasuk'},
            {className:'text-center', data:'coari_cfs_response', name:'coari_cfs_response'},
            {className:'text-center', data:'coari_cfs_at', name:'coari_cfs_at'},
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
            }
        
    });
</script>

<script>
    $('#tableContainer').on('click', '#resend', function () {
        let selectedIds = [];
        let selectedRows = $('#tableContainer').DataTable().rows({ selected: true }).data();
            selectedRows.each(function (rowData) {
            selectedIds.push(rowData.id); 
        });

        console.log(selectedIds); 

        if (selectedIds.length === 0) {
           swal.fire({
                icon: 'error',
                title: 'Pilih Kolom terlebih dahulu!!',
           });
        }
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
                    url: '{{ route('cfs.container.resend') }}',
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

    });
</script>
@endsection