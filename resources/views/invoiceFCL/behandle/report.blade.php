@extends('partial.main')

@section('custom_styles')

@endsection
@section('content')

<section>
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="">Filter By</label>
                            <select name="filter[]" id="filter" style="width: 100%;" class="js-example-basic-multiple select2 form-select" multiple>
                                <option value="Y">Lunas</option>
                                <option value="N">Belum Lunas</option>
                                <option value="C">Cancel</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group">
                            <label for="">End Date</label>
                            <div class="input-group mb-3">
                                <input type="date" name="end_date" id="end_date" class="form-control">
                                <button type="button" class="btn btn-info applyFilter" id="applyFilter"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table">
                    <table class="table table-hover table-stripped" id="tableReport">
                        <thead style="white-space: nowrap;">
                            <th class="text-center" style="min-width: 150px">Proforma No</th>
                            <th class="text-center" style="min-width: 150px">Invoice No</th>
                            <th class="text-center" style="min-width: 150px">Customer Name</th>
                            <th class="text-center" style="min-width: 150px">Customer NPWP</th>
                            <th class="text-center" style="min-width: 150px">Customer Alamat</th>
                            <th class="text-center" style="min-width: 150px">No SPJM</th>
                            <th class="text-center" style="min-width: 150px">Tgl SPJM</th>
                            <th class="text-center" style="min-width: 150px">Admin</th>
                            <th class="text-center" style="min-width: 150px">Total</th>
                            <th class="text-center" style="min-width: 150px">PPN</th>
                            <th class="text-center" style="min-width: 150px">Grand Total</th>
                            <th class="text-center" style="min-width: 150px">Status</th>
                            <th class="text-center" style="min-width: 150px">Created At</th>
                            <th class="text-center" style="min-width: 150px">Created By</th>
                            <th class="text-center" style="min-width: 150px">Lunas At</th>
                            <th class="text-center" style="min-width: 150px">Lunas By</th>
                            <th class="text-center" style="min-width: 150px">Cancel At</th>
                            <th class="text-center" style="min-width: 150px">Cancel By</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@section('custom_js')
<script>
    $(document).on('click', '.applyFilter', function(){
        Swal.fire({
            title: 'Are you sure?',
            text: "Apakah anda yakin menerapkan filter ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                let filterBy = $('#filter').val();
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();

                // Reload DataTables dengan parameter filter
                $('#tableReport').DataTable().ajax.url('/invoiceFCL/behandle/data-report?filter=' + filterBy + '&start_date=' + startDate + '&end_date=' + endDate).load();
                
                Swal.close();
            }
        });
    })
</script>

<script>
    $(document).ready(function(){
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
        $('#tableReport').DataTable({
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], // Pilihan jumlah data
            pageLength: 25, // Default jumlah data per halaman
            dom: 'lBfrtip', // Pastikan ada 'B' untuk menampilkan tombol
            buttons: [
                'copy', 'csv', excel , pdf, 'print'
            ],
            scrollX: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '/invoiceFCL/behandle/data-report',
                data: function(d) {
                    d.filter = $('#filter').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns:[
                {className:"text-center", data:'proforma_no', name:'proforma_no'},
                {className:"text-center", data:'invoice_no', name:'invoice_no'},
                {className:"text-center", data:'customer_name', name:'customer_name'},
                {className:"text-center", data:'customer_npwp', name:'customer_npwp'},
                {className:"text-center", data:'customer_alamat', name:'customer_alamat'},
                {className:"text-center", data:'no_spjm', name:'no_spjm'},
                {className:"text-center", data:'tgl_spjm', name:'tgl_spjm'},
                {className:"text-center", data:'admin', name:'admin'},
                {className:"text-center", data:'total', name:'total'},
                {className:"text-center", data:'ppn', name:'ppn'},
                {className:"text-center", data:'grand_total', name:'grand_total'},
                {className:"text-center", data:'status', name:'status'},
                {className:"text-center", data:'order_at', name:'order_at'},
                {className:"text-center", data:'order_by', name:'order_by'},
                {className:"text-center", data:'lunas_at', name:'lunas_at'},
                {className:"text-center", data:'lunas_by', name:'lunas_by'},
                {className:"text-center", data:'cancel_at', name:'cancel_at'},
                {className:"text-center", data:'cancel_by', name:'cancel_by'},
            ],
            initComplete: function () {
                var api = this.api();
                
                api.columns().every(function (index) {
                    var column = this;
                    var excludedColumns = [0, 25]; // Kolom yang tidak ingin difilter (detil, flag_segel_merah, lamaHari)
                    
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
        });
    })
</script>
@endsection