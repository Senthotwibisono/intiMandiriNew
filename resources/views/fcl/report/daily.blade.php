@extends('partial.main')
@section('custom_styles')
<style>
    .highlight-yellow {
        background-color: yellow !important;;
    }
</style>

<style>
    .highlight-blue {
        background-color: lightblue !important;;
    }
</style>

<style>
    .highlight-red {
        background-color: red !important;;
    }
</style>

@endsection
@section('content')

<section>
    <div class="card">
        <div class="card-header">
            <header>Generate Report</header>
        </div>
        <form action="{{ route('report.fcl.daily') }}" method="get">
            @csrf
            <div class="card-body">
                <div class="row mt-0">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') ?? $start }}">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') ?? $end }}">
                        </div>
                    </div>
                    <div class="col-sm-4 d-flex align-items-end">
                        <button class="btn btn-success" type="submit">Generate</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<div class="row mt-0">
    <div class="col-sm-12">
        <section>
            <div class="card h-100 justify-content-center mt-0">
                <div class="card-header text-center">
                    <h4>Pemasukan</h4>
                </div>
                <div class="card-body">
                    <div class="table">
                        <table class="table-hover" id="dataReportContMasuk" style="white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center">Action</th>
                                    <th class="text-center">No Job Order</th>
                                    <th class="text-center">Nama Angkut</th>
                                    <th class="text-center">Status Beacukai</th>
                                    <th class="text-center">Segel Merah</th>
                                    <th class="text-center">Bill of Loading No</th>
                                    <th class="text-center">Bill of Loading Date</th>
                                    <th class="text-center">No Container</th>
                                    <th class="text-center">Container Type</th>
                                    <th class="text-center">Class Type</th>
                                    <th class="text-center" style="min-width: 100px;">Size</th>
                                    <th class="text-center" style="min-width: 100px;">ETA</th>
                                    <th class="text-center">TPS Asal</th>
                                    <th class="text-center">Consolidator</th>
                                    <th class="text-center">No PLP</th>
                                    <th class="text-center">Tgl PLP</th>
                                    <th class="text-center">No BC 1.1</th>
                                    <th class="text-center">Tgl BC 1.1</th>
                                    <th class="text-center">Tgl Masuk</th>
                                    <th class="text-center">Jam Masuk</th>
                                    <th class="text-center">Nomor Polisi</th>
                                    <th class="text-center">Tgl Keluar</th>
                                    <th class="text-center">Jam Keluar</th>
                                    <th class="text-center">Nopol Keluar</th>
                                    <th class="text-center">Lama Hari</th>
                                    <th class="text-center">Long Stay</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="card-body h-100 justify-content-center align-items-center mt-0">
                    <div class="table">
                        <table class=" table-striiped">
                            <thead>
                                <tr>
                                    <th>Jumlah</th>
                                    <th>Quantity</th>
                                    <th>Tonase</th>
                                    <th>Teus</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{$jumlahMasuk}}</td>
                                    <td>{{$quantityMasuk}}</td>
                                    <td>{{$tonaseMasuk}}</td>
                                    <td>{{$volumeMasuk}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-sm-12">
        <section>
            <div class="card h-100 justify-content-center mt-0">
                <div class="card-header text-center">
                    <h4>Pengeluaran</h4>
                </div>
                <div class="card-body">
                    <div class="table">
                        <table class="table-hover" id="dataReportContKeluar" style="white-space: nowrap;">
                            <thead>
                                <tr>
                                    <th class="text-center">Action</th>
                                    <th class="text-center">No Job Order</th>
                                    <th class="text-center">Nama Angkut</th>
                                    <th class="text-center">Status Beacukai</th>
                                    <th class="text-center">Segel Merah</th>
                                    <th class="text-center">Bill of Loading No</th>
                                    <th class="text-center">Bill of Loading Date</th>
                                    <th class="text-center">No Container</th>
                                    <th class="text-center">Container Type</th>
                                    <th class="text-center">Class Type</th>
                                    <th class="text-center" style="min-width: 100px;">Size</th>
                                    <th class="text-center" style="min-width: 100px;">ETA</th>
                                    <th class="text-center">TPS Asal</th>
                                    <th class="text-center">Consolidator</th>
                                    <th class="text-center">No PLP</th>
                                    <th class="text-center">Tgl PLP</th>
                                    <th class="text-center">No BC 1.1</th>
                                    <th class="text-center">Tgl BC 1.1</th>
                                    <th class="text-center">Tgl Masuk</th>
                                    <th class="text-center">Jam Masuk</th>
                                    <th class="text-center">Nomor Polisi</th>
                                    <th class="text-center">Tgl Keluar</th>
                                    <th class="text-center">Jam Keluar</th>
                                    <th class="text-center">Nopol Keluar</th>
                                    <th class="text-center">Lama Hari</th>
                                    <th class="text-center">Long Stay</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="card-body h-100 justify-content-center align-items-center mt-0">
                    <div class="table">
                        <table class=" table-striiped">
                            <thead>
                                <tr>
                                    <th>Jumlah</th>
                                    <th>Quantity</th>
                                    <th>Tonase</th>
                                    <th>Teus</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{$jumlahKeluar}}</td>
                                    <td>{{$quantityKeluar}}</td>
                                    <td>{{$tonaseKeluar}}</td>
                                    <td>{{$volumeKeluar}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<section>
    <div class="card h-100 justify-content-center align-items-center mt-0">
        <div class="card-header">
            <h4>Report Total</h4>
        </div>
        <div class="card-body">
            <div class="table">
                <table class=" table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" rowspan="2">Keterangan</th>
                            <th class="text-center" colspan="4">Jumlah</th>
                            <th class="text-center" colspan="4">Tonase</th>
                            <th class="text-center" colspan="4">Teus</th>
                        </tr>
                        <tr>
                            <th text="center">DRY</th>
                            <th text="center">BB</th>
                            <th text="center">OH</th>
                            <th text="center">Total</th>
                            <th text="center">DRY</th>
                            <th text="center">BB</th>
                            <th text="center">OH</th>
                            <th text="center">Total</th>
                            <th text="center">DRY</th>
                            <th text="center">BB</th>
                            <th text="center">OH</th>
                            <th text="center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Awal</th>
                            <td>{{$jumlahAwalDry}}</td>
                            <td>{{$jumlahAwalBB}}</td>
                            <td>{{$jumlahAwalOH}}</td>
                            <td>{{$jumlahAwal}}</td>

                            <td>{{$tonaseAwalDry}}</td>
                            <td>{{$tonaseAwalBB}}</td>
                            <td>{{$tonaseAwalOH}}</td>
                            <td>{{$tonaseAwal}}</td>
                            
                            <td>{{$volumeAwalDry}}</td>
                            <td>{{$volumeAwalBB}}</td>
                            <td>{{$volumeAwalOH}}</td>
                            <td>{{$volumeAwal}}</td>
                        </tr>
                        <tr>
                            <th>Masuk</th>
                            <td>{{$jumlahMasukDry}}</td>
                            <td>{{$jumlahMasukBB}}</td>
                            <td>{{$jumlahMasukOH}}</td>
                            <td>{{$jumlahMasuk}}</td>

                            <td>{{$tonaseMasukDry}}</td>
                            <td>{{$tonaseMasukBB}}</td>
                            <td>{{$tonaseMasukOH}}</td>
                            <td>{{$tonaseMasuk}}</td>
                            
                            <td>{{$volumeMasukDry}}</td>
                            <td>{{$volumeMasukBB}}</td>
                            <td>{{$volumeMasukOH}}</td>
                            <td>{{$volumeMasuk}}</td>
                        </tr>
                        <tr>
                            <th>Keluar</th>
                            <td>{{$jumlahKeluarDry}}</td>
                            <td>{{$jumlahKeluarBB}}</td>
                            <td>{{$jumlahKeluarOH}}</td>
                            <td>{{$jumlahKeluar}}</td>

                            <td>{{$tonaseKeluarDry}}</td>
                            <td>{{$tonaseKeluarBB}}</td>
                            <td>{{$tonaseKeluarOH}}</td>
                            <td>{{$tonaseKeluar}}</td>
                            
                            <td>{{$volumeKeluarDry}}</td>
                            <td>{{$volumeKeluarBB}}</td>
                            <td>{{$volumeKeluarOH}}</td>
                            <td>{{$volumeKeluar}}</td>
                        </tr>
                        <tr>
                            <th>Akhir</th>
                            <td>{{$jumlahAkhirDry}}</td>
                            <td>{{$jumlahAkhirBB}}</td>
                            <td>{{$jumlahAkhirOH}}</td>
                            <td>{{$jumlahAkhir}}</td>

                            <td>{{$tonaseAkhirDry}}</td>
                            <td>{{$tonaseAkhirBB}}</td>
                            <td>{{$tonaseAkhirOH}}</td>
                            <td>{{$tonaseAkhir}}</td>
                            
                            <td>{{$volumeAkhirDry}}</td>
                            <td>{{$volumeAkhirBB}}</td>
                            <td>{{$volumeAkhirOH}}</td>
                            <td>{{$volumeAkhir}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="col-sm-12">
            <section>
                <div class="card h-100 justify-content-center mt-0">
                    <div class="card-header text-center">
                        <h4>Stock Lapangan</h4>
                    </div>
                    <div class="card-body">
                        <div class="table">
                            <table class="table-hover" id="dataReportContAkhir" style="white-space: nowrap;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Action</th>
                                        <th class="text-center">No Job Order</th>
                                        <th class="text-center">Nama Angkut</th>
                                        <th class="text-center">Status Beacukai</th>
                                        <th class="text-center">Segel Merah</th>
                                        <th class="text-center">Bill of Loading No</th>
                                        <th class="text-center">Bill of Loading Date</th>
                                        <th class="text-center">No Container</th>
                                        <th class="text-center">Container Type</th>
                                        <th class="text-center">Class Type</th>
                                        <th class="text-center" style="min-width: 100px;">Size</th>
                                        <th class="text-center" style="min-width: 100px;">ETA</th>
                                        <th class="text-center">TPS Asal</th>
                                        <th class="text-center">Consolidator</th>
                                        <th class="text-center">No PLP</th>
                                        <th class="text-center">Tgl PLP</th>
                                        <th class="text-center">No BC 1.1</th>
                                        <th class="text-center">Tgl BC 1.1</th>
                                        <th class="text-center">Tgl Masuk</th>
                                        <th class="text-center">Jam Masuk</th>
                                        <th class="text-center">Nomor Polisi</th>
                                        <th class="text-center">Tgl Keluar</th>
                                        <th class="text-center">Jam Keluar</th>
                                        <th class="text-center">Nopol Keluar</th>
                                        <th class="text-center">Lama Hari</th>
                                        <th class="text-center">Long Stay</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="button-container">
                            <div class="col-auto">
                                <button class="btn btn-success formatBeacukai"><i class="fa fa-download"></i> Format Beacukai</button>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-success formatStandar"><i class="fa fa-download"></i> Format Standar</button>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </section>
        </div>
    </div>
</section>


@endsection

@section('custom_js')
<script>
    $(document).ready(function(){
        var start = "{{ $start }}";
        var end = "{{ $end }}";
        console.log('tgl ' + start + ' ' + end);
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
        $('#dataReportContMasuk').DataTable({
            dom: 'Bfrtip', // Pastikan ada 'B' untuk menampilkan tombol
            buttons: [
                'copy', 'csv', excel , pdf, 'print'
            ],
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '/fcl/report/dataContDaily',
                type: 'GET',
                data : {filter:'masuk',
                        start: start,
                        end: end,
                },
            },
            columns:[
                { data:'detil', name:'detil', className:'text-center' },
                { data:'jobordr', name:'jobordr', className:'text-center' },
                { data:'nm_angkut', name:'nm_angkut', className:'text-center' },
                { data:'status_bc', name:'status_bc', className:'text-center' },
                { data:'flag_segel_merah', name:'flag_segel_merah', className:'text-center' },
                { data:'nobl', name:'nobl', className:'text-center' },
                { data:'tgl_bl_awb', name:'tgl_bl_awb', className:'text-center' },
                { data:'nocontainer', name:'nocontainer', className:'text-center' },
                { data:'ctrType', name:'ctrType', className:'text-center' },
                { data:'classType', name:'classType', className:'text-center' },
                { data:'size', name:'size', className:'text-center' },
                { data:'eta', name:'eta', className:'text-center' },
                { data:'kd_tps_asal', name:'kd_tps_asal', className:'text-center' },
                { data:'namaconsolidator', name:'namaconsolidator', className:'text-center' },
                { data:'noplp', name:'noplp', className:'text-center' },
                { data:'tglPLP', name:'tglPLP', className:'text-center' },
                { data:'no_bc11', name:'no_bc11', className:'text-center' },
                { data:'tgl_bc11', name:'tgl_bc11', className:'text-center' },
                { data:'tglmasuk', name:'tglmasuk', className:'text-center' },
                { data:'jammasuk', name:'jammasuk', className:'text-center' },
                { data:'nopol', name:'nopol', className:'text-center' },
                { data:'tglkeluar', name:'tglkeluar', className:'text-center' },
                { data:'jamkeluar', name:'jamkeluar', className:'text-center' },
                { data:'nopol_mty', name:'nopol_mty', className:'text-center' },
                { data:'lamaHari', name:'lamaHari', className:'text-center' },
                { data:'longStay', name:'longStay', className:'text-center' },
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.flag_segel_merah === 'Y') {
                    $(row).addClass('highlight-red text-white');
                } else if (data.status_bc === 'HOLD') {
                    $(row).addClass('highlight-yellow');
                } else if (data.status_bc === 'release'){
                    $(row).addClass('highlight-blue');
                }
            },
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
            }
        });
    });
</script>

<script>
    $(document).ready(function(){
        var start = "{{ $start }}";
        var end = "{{ $end }}";
        console.log('tgl ' + start + ' ' + end);
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
        $('#dataReportContKeluar').DataTable({
            dom: 'Bfrtip', // Pastikan ada 'B' untuk menampilkan tombol
            buttons: [
                'copy', 'csv', excel , pdf, 'print'
            ],
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '/fcl/report/dataContDaily',
                type: 'GET',
                data : {filter:'keluar',
                        start: start,
                        end: end,
                },
            },
            columns:[
                { data:'detil', name:'detil', className:'text-center' },
                { data:'jobordr', name:'jobordr', className:'text-center' },
                { data:'nm_angkut', name:'nm_angkut', className:'text-center' },
                { data:'status_bc', name:'status_bc', className:'text-center' },
                { data:'flag_segel_merah', name:'flag_segel_merah', className:'text-center' },
                { data:'nobl', name:'nobl', className:'text-center' },
                { data:'tgl_bl_awb', name:'tgl_bl_awb', className:'text-center' },
                { data:'nocontainer', name:'nocontainer', className:'text-center' },
                { data:'ctrType', name:'ctrType', className:'text-center' },
                { data:'classType', name:'classType', className:'text-center' },
                { data:'size', name:'size', className:'text-center' },
                { data:'eta', name:'eta', className:'text-center' },
                { data:'kd_tps_asal', name:'kd_tps_asal', className:'text-center' },
                { data:'namaconsolidator', name:'namaconsolidator', className:'text-center' },
                { data:'noplp', name:'noplp', className:'text-center' },
                { data:'tglPLP', name:'tglPLP', className:'text-center' },
                { data:'no_bc11', name:'no_bc11', className:'text-center' },
                { data:'tgl_bc11', name:'tgl_bc11', className:'text-center' },
                { data:'tglmasuk', name:'tglmasuk', className:'text-center' },
                { data:'jammasuk', name:'jammasuk', className:'text-center' },
                { data:'nopol', name:'nopol', className:'text-center' },
                { data:'tglkeluar', name:'tglkeluar', className:'text-center' },
                { data:'jamkeluar', name:'jamkeluar', className:'text-center' },
                { data:'nopol_mty', name:'nopol_mty', className:'text-center' },
                { data:'lamaHari', name:'lamaHari', className:'text-center' },
                { data:'longStay', name:'longStay', className:'text-center' },
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.flag_segel_merah === 'Y') {
                    $(row).addClass('highlight-red text-white');
                } else if (data.status_bc === 'HOLD') {
                    $(row).addClass('highlight-yellow');
                } else if (data.status_bc === 'release'){
                    $(row).addClass('highlight-blue');
                }
            },
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
            }
        });
    });
</script>

<script>
    $(document).ready(function(){
        var start = "{{ $start }}";
        var end = "{{ $end }}";
        console.log('tgl ' + start + ' ' + end);
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
        $('#dataReportContAkhir').DataTable({
            dom: 'Bfrtip', // Pastikan ada 'B' untuk menampilkan tombol
            buttons: [
                'copy', 'csv', excel , pdf, 'print'
            ],
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '/fcl/report/dataContDaily',
                type: 'GET',
                data : {filter:'total',
                        start: start,
                        end: end,
                },
            },
            columns:[
                { data:'detil', name:'detil', className:'text-center' },
                { data:'jobordr', name:'jobordr', className:'text-center' },
                { data:'nm_angkut', name:'nm_angkut', className:'text-center' },
                { data:'status_bc', name:'status_bc', className:'text-center' },
                { data:'flag_segel_merah', name:'flag_segel_merah', className:'text-center' },
                { data:'nobl', name:'nobl', className:'text-center' },
                { data:'tgl_bl_awb', name:'tgl_bl_awb', className:'text-center' },
                { data:'nocontainer', name:'nocontainer', className:'text-center' },
                { data:'ctrType', name:'ctrType', className:'text-center' },
                { data:'classType', name:'classType', className:'text-center' },
                { data:'size', name:'size', className:'text-center' },
                { data:'eta', name:'eta', className:'text-center' },
                { data:'kd_tps_asal', name:'kd_tps_asal', className:'text-center' },
                { data:'namaconsolidator', name:'namaconsolidator', className:'text-center' },
                { data:'noplp', name:'noplp', className:'text-center' },
                { data:'tglPLP', name:'tglPLP', className:'text-center' },
                { data:'no_bc11', name:'no_bc11', className:'text-center' },
                { data:'tgl_bc11', name:'tgl_bc11', className:'text-center' },
                { data:'tglmasuk', name:'tglmasuk', className:'text-center' },
                { data:'jammasuk', name:'jammasuk', className:'text-center' },
                { data:'nopol', name:'nopol', className:'text-center' },
                { data:'tglkeluar', name:'tglkeluar', className:'text-center' },
                { data:'jamkeluar', name:'jamkeluar', className:'text-center' },
                { data:'nopol_mty', name:'nopol_mty', className:'text-center' },
                { data:'lamaHari', name:'lamaHari', className:'text-center' },
                { data:'longStay', name:'longStay', className:'text-center' },
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.flag_segel_merah === 'Y') {
                    $(row).addClass('highlight-red text-white');
                } else if (data.status_bc === 'HOLD') {
                    $(row).addClass('highlight-yellow');
                } else if (data.status_bc === 'release'){
                    $(row).addClass('highlight-blue');
                }
            },
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
            }
        });
    });
</script>

<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=1500,height=1000');
    }
</script>

<script>
    $(document).on('click', '.formatStandar', function(){
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
                let noPlp = $('#noplp').val();
                let noBc11 = $('#nobc_11').val();

                // Redirect user to download link
                let url = `/fcl/report/formatStandarAkhir?start_date=${startDate}&end_date=${endDate}`;
                window.location.href = url;
                Swal.close();
            }
        });
    });
</script>

<script>
    $(document).on('click', '.formatBeacukai', function(){
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
                let noPlp = $('#noplp').val();
                let noBc11 = $('#nobc_11').val();

                // Redirect user to download link
                let url = `/fcl/report/formatBeacukaiAkhir?start_date=${startDate}&end_date=${endDate}`;
                window.location.href = url;
                Swal.close();
            }
        });
    });
</script>

@endsection
