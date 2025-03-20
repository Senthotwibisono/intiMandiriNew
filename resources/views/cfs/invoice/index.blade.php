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
                        <table class="table-hover table-stripped" id="tableInvoiceCFS">
                            <thead style="white-space: nowrap;">
                                <tr>
                                    <th class="text-center" rowspan="2"></th>
                                    <th class="text-center" style="min-width: 50px" rowspan="2">Print</th>
                                    <th class="text-center" style="min-width: 150px" rowspan="2">No Order</th>
                                    <th class="text-center" style="min-width: 150px" rowspan="2">No Invoice</th>
                                    <th class="text-center" style="min-width: 150px" colspan="7">House BL</th>
                                    <th class="text-center" style="min-width: 150px" colspan="3">Ketereangan Invoice</th>
                                    <th class="text-center" style="min-width: 150px" colspan="2">Consignee CFS</th>
                                    <th class="text-center" style="min-width: 150px" colspan="2">Customer</th>
                                    <th class="text-center" style="min-width: 150px" colspan="3">Harga</th>
                                    <th class="text-center" style="min-width: 150px" rowspan="2">Status</th>
                                    <th class="text-center" style="min-width: 150px" rowspan="2">Rencana Keluar</th>
                                    <th class="text-center" style="min-width: 150px" rowspan="2">Tanngal Order</th>
                                    <th class="text-center" style="min-width: 150px" rowspan="2">Tanngal Cancel</th>
                                    <th class="text-center" style="min-width: 150px" rowspan="2">Tanngal Lunas</th>
                                </tr>
                                <tr>
                                    <th class="text-center" style="min-width: 100px;">No HBL AWB</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl HBL AWB</th>
                                    <th class="text-center" style="min-width: 100px;">Weight</th>
                                    <th class="text-center" style="min-width: 100px;">Meas</th>
                                    <th class="text-center" style="min-width: 100px;">Jenis Kemas</th>
                                    <th class="text-center" style="min-width: 100px;">Merk Kemas</th>
                                    <th class="text-center" style="min-width: 100px;">Jumlah Kemas</th>
                                    <th class="text-center" style="min-width: 100px;">Jenis Billing</th>
                                    <th class="text-center" style="min-width: 100px;">Jenis Bayar</th>
                                    <th class="text-center" style="min-width: 100px;">Jenis Jenis Transaksi</th>
                                    <th class="text-center" style="min-width: 100px;">Nama</th>
                                    <th class="text-center" style="min-width: 100px;">NPWP</th>
                                    <th class="text-center" style="min-width: 100px;">Nama</th>
                                    <th class="text-center" style="min-width: 100px;">NPWP</th>
                                    <th class="text-center" style="min-width: 100px;">Subtotal</th>
                                    <th class="text-center" style="min-width: 100px;">PPN</th>
                                    <th class="text-center" style="min-width: 100px;">Total</th>
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
    $('#tableInvoiceCFS').dataTable({
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], // Pilihan jumlah data
        pageLength: 25, // Default jumlah data per halaman
        dom: 'lBfrtip', // Pastikan ada 'B' untuk menampilkan tombol
        buttons: [
            'copy', 'csv', excel , pdf, 'print'
        ],
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: '{{ route('cfs.invoice.data')}}',
        select: {
            selector: 'td:not(:nth-child(2))',
        },
        columns: [
            {className:'text-center', data: 'id', orderable: false, searchable: false, render: DataTable.render.select()},
            {className:'text-center', data: 'print', name: 'print'},
            {className:'text-center', data: 'no_order', name: 'no_order'},
            {className:'text-center', data: 'no_invoice', name: 'no_invoice'},
            {className:'text-center', data: 'no_bl_awb', name: 'no_bl_awb'},
            {className:'text-center', data: 'manifest.tgl_hbl', name: 'manifest.tgl_hbl'},
            {className:'text-center', data: 'weight', name: 'weight'},
            {className:'text-center', data: 'measure', name: 'measure'},
            {className:'text-center', data: 'jns_kms', name: 'jns_kms'},
            {className:'text-center', data: 'merk_kms', name: 'merk_kms'},
            {className:'text-center', data: 'jml_kms', name: 'jml_kms'},
            {className:'text-center', data: 'jenis_billing', name: 'jenis_billing'},
            {className:'text-center', data: 'jenis_bayar', name: 'jenis_bayar'},
            {className:'text-center', data: 'jenis_transaksi', name: 'jenis_transaksi'},
            {className:'text-center', data: 'consignee', name: 'consignee'},
            {className:'text-center', data: 'npwp_consignee', name: 'npwp_consignee'},
            {className:'text-center', data: 'customerName', name: 'customerName'},
            {className:'text-center', data: 'customerNPWP', name: 'customerNPWP'},
            {
                className: 'text-center', 
                data: 'subtotal', 
                name: 'subtotal',
                render: function(data, type, row) {
                    return data ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data) : 'Rp 0';
                }
            },
            {
                className: 'text-center', 
                data: 'ppn', 
                name: 'ppn',
                render: function(data, type, row) {
                    return data ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data) : 'Rp 0';
                }
            },
            {
                className: 'text-center', 
                data: 'total', 
                name: 'total',
                render: function(data, type, row) {
                    return data ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data) : 'Rp 0';
                }
            },
            {className:'text-center', data: 'status', name: 'status'},
            {className:'text-center', data: 'rencana_keluar', name: 'rencana_keluar'},
            {className:'text-center', data: 'created_at', name: 'created_at'},
            {className:'text-center', data: 'cancel_at', name: 'cancel_at'},
            {className:'text-center', data: 'lunas_at', name: 'lunas_at'},
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
    $('#tableInvoiceCFS').on('click', '#print', function () {
        let selectedIds = [];
        let selectedRows = $('#tableInvoiceCFS').DataTable().rows({ selected: true }).data();
            selectedRows.each(function (rowData) {
            selectedIds.push(rowData.id); 
        });

        console.log(selectedIds); 

        if (selectedIds.length === 0) {
           swal.fire({
                icon: 'error',
                title: 'Pilih Kolom terlebih dahulu!!',
           });
        } else {

            let url = `/invoice/cfs/print?ids=${selectedIds.join(',')}`;
    
            // Buka halaman baru dengan target _blank
            window.open(url, '_blank');
        }


    });
</script>

@endsection