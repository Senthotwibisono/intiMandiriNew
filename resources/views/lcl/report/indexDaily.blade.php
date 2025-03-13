@extends('partial.main')
@section('custom_styles')

@endsection
@section('content')

<section>
    <div class="card">
        <div class="card-header">
            <header>Generate Report</header>
        </div>
        <form action="{{ route('report.lcl.daily') }}" method="get">
            @csrf
            <div class="card-body">
                <div class="row mt-0">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $start }}">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $end }}">
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
                        <table class="table-hover" id="tableManifestMasuk">
                            <thead style="white-space: nowrap">
                                <tr>
                                    <th class="text-center" style="min-width: 100px;">Action</th>
                                    <th class="text-center" style="min-width: 100px;">No Job Order</th>
                                    <th class="text-center" style="min-width: 100px;">Nama Angkut</th>
                                    <th class="text-center" style="min-width: 100px;">No Container</th>
                                    <th class="text-center" style="min-width: 100px;">Size</th>
                                    <th class="text-center" style="min-width: 100px;">ETA</th>
                                    <th class="text-center" style="min-width: 100px;">TPS Asal</th>
                                    <th class="text-center" style="min-width: 100px;">Consolidator</th>
                                    <th class="text-center" style="min-width: 100px;">No HBL</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl HBL</th>
                                    <th class="text-center" style="min-width: 100px;">NO Tally</th>
                                    <th class="text-center" style="min-width: 100px;">Customer</th>
                                    <th class="text-center" style="min-width: 100px;">Quantity</th>
                                    <th class="text-center" style="min-width: 100px;">Quantity Real Time</th>
                                    <th class="text-center" style="min-width: 100px;">Nama Kemas</th>
                                    <th class="text-center" style="min-width: 100px;">Kode Kemas</th>
                                    <th class="text-center" style="min-width: 100px;">Desc of Goods</th>
                                    <th class="text-center" style="min-width: 100px;">Weight</th>
                                    <th class="text-center" style="min-width: 100px;">Meas</th>
                                    <th class="text-center" style="min-width: 100px;">Packing Tally</th>
                                    <th class="text-center" style="min-width: 100px;">No PLP</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl PLP</th>
                                    <th class="text-center" style="min-width: 100px;">No BC 1.1</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl BC 1.1</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl Masuk</th>
                                    <th class="text-center" style="min-width: 100px;">Jam Masuk</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl Stripping</th>
                                    <th class="text-center" style="min-width: 100px;">Jam Stripping</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl Release</th>
                                    <th class="text-center" style="min-width: 100px;">Jam Release</th>
                                    <th class="text-center" style="min-width: 100px;">Kode Dokumen</th>
                                    <th class="text-center" style="min-width: 100px;">Nomor Dokumen</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl Dokumen</th>
                                    <th class="text-center" style="min-width: 100px;">Location</th>
                                    <th class="text-center" style="min-width: 100px;">Lama Timbun</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="card-body text-center">
                    <div class="table">
                        <table class="table-responsive table-striiped">
                            <thead>
                                <tr>
                                    <th>Jumlah</th>
                                    <th>Quantity</th>
                                    <th>Tonase</th>
                                    <th>Voulme</th>
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
                        <table class="table-hover" id="tableManifestKeluar">
                            <thead style="white-space: nowrap;">
                                <tr>
                                    <th class="text-center" style="min-width: 100px;">Action</th>
                                    <th class="text-center" style="min-width: 100px;">No Job Order</th>
                                    <th class="text-center" style="min-width: 100px;">Nama Angkut</th>
                                    <th class="text-center" style="min-width: 100px;">No Container</th>
                                    <th class="text-center" style="min-width: 100px;">Size</th>
                                    <th class="text-center" style="min-width: 100px;">ETA</th>
                                    <th class="text-center" style="min-width: 100px;">TPS Asal</th>
                                    <th class="text-center" style="min-width: 100px;">Consolidator</th>
                                    <th class="text-center" style="min-width: 100px;">No HBL</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl HBL</th>
                                    <th class="text-center" style="min-width: 100px;">NO Tally</th>
                                    <th class="text-center" style="min-width: 100px;">Customer</th>
                                    <th class="text-center" style="min-width: 100px;">Quantity</th>
                                    <th class="text-center" style="min-width: 100px;">Quantity Real Time</th>
                                    <th class="text-center" style="min-width: 100px;">Nama Kemas</th>
                                    <th class="text-center" style="min-width: 100px;">Kode Kemas</th>
                                    <th class="text-center" style="min-width: 100px;">Desc of Goods</th>
                                    <th class="text-center" style="min-width: 100px;">Weight</th>
                                    <th class="text-center" style="min-width: 100px;">Meas</th>
                                    <th class="text-center" style="min-width: 100px;">Packing Tally</th>
                                    <th class="text-center" style="min-width: 100px;">No PLP</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl PLP</th>
                                    <th class="text-center" style="min-width: 100px;">No BC 1.1</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl BC 1.1</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl Masuk</th>
                                    <th class="text-center" style="min-width: 100px;">Jam Masuk</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl Stripping</th>
                                    <th class="text-center" style="min-width: 100px;">Jam Stripping</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl Release</th>
                                    <th class="text-center" style="min-width: 100px;">Jam Release</th>
                                    <th class="text-center" style="min-width: 100px;">Kode Dokumen</th>
                                    <th class="text-center" style="min-width: 100px;">Nomor Dokumen</th>
                                    <th class="text-center" style="min-width: 100px;">Tgl Dokumen</th>
                                    <th class="text-center" style="min-width: 100px;">Location</th>
                                    <th class="text-center" style="min-width: 100px;">Lama Timbun</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="card-body text-center">
                    <div class="table">
                        <table class="table-responsive table-striiped">
                            <thead>
                                <tr>
                                    <th>Jumlah</th>
                                    <th>Quantity</th>
                                    <th>Tonase</th>
                                    <th>Voulme</th>
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
                <table class="table-responsive table-striiped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Jumlah</th>
                            <th>Quantity</th>
                            <th>Tonase</th>
                            <th>Voulme</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Awal</th>
                            <td>{{$jumlahAwal}}</td>
                            <td>{{$quantityAwal}}</td>
                            <td>{{$tonaseAwal}}</td>
                            <td>{{$volumeAwal}}</td>
                        </tr>
                        <tr>
                            <th>Masuk</th>
                            <td>{{$jumlahMasuk}}</td>
                            <td>{{$quantityMasuk}}</td>
                            <td>{{$tonaseMasuk}}</td>
                            <td>{{$volumeMasuk}}</td>
                        </tr>
                        <tr>
                            <th>Keluar</th>
                            <td>{{$jumlahKeluar}}</td>
                            <td>{{$quantityKeluar}}</td>
                            <td>{{$tonaseKeluar}}</td>
                            <td>{{$volumeKeluar}}</td>
                        </tr>
                        <tr>
                            <th>Akhir</th>
                            <td>{{$jumlahAkhir}}</td>
                            <td>{{$quantityAkhir}}</td>
                            <td>{{$tonaseAkhir}}</td>
                            <td>{{$volumeAkhir}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-12">
            <section>
                <div class="card h-100 justify-content-center mt-0">
                    <div class="card-header text-center">
                        <h4>Stock Gudang</h4>
                    </div>
                    <div class="card-body">
                        <div class="table">
                            <table class="table-hover" id="tableManifestAkhir">
                                <thead style="white-space: nowrap;">
                                    <tr>
                                        <th class="text-center" style="min-width: 100px;">Action</th>
                                        <th class="text-center" style="min-width: 100px;">No Job Order</th>
                                        <th class="text-center" style="min-width: 100px;">Nama Angkut</th>
                                        <th class="text-center" style="min-width: 100px;">No Container</th>
                                        <th class="text-center" style="min-width: 100px;">Size</th>
                                        <th class="text-center" style="min-width: 100px;">ETA</th>
                                        <th class="text-center" style="min-width: 100px;">TPS Asal</th>
                                        <th class="text-center" style="min-width: 100px;">Consolidator</th>
                                        <th class="text-center" style="min-width: 100px;">No HBL</th>
                                        <th class="text-center" style="min-width: 100px;">Tgl HBL</th>
                                        <th class="text-center" style="min-width: 100px;">NO Tally</th>
                                        <th class="text-center" style="min-width: 100px;">Customer</th>
                                        <th class="text-center" style="min-width: 100px;">Quantity</th>
                                        <th class="text-center" style="min-width: 100px;">Quantity Real Time</th>
                                        <th class="text-center" style="min-width: 100px;">Nama Kemas</th>
                                        <th class="text-center" style="min-width: 100px;">Kode Kemas</th>
                                        <th class="text-center" style="min-width: 100px;">Desc of Goods</th>
                                        <th class="text-center" style="min-width: 100px;">Weight</th>
                                        <th class="text-center" style="min-width: 100px;">Meas</th>
                                        <th class="text-center" style="min-width: 100px;">Packing Tally</th>
                                        <th class="text-center" style="min-width: 100px;">No PLP</th>
                                        <th class="text-center" style="min-width: 100px;">Tgl PLP</th>
                                        <th class="text-center" style="min-width: 100px;">No BC 1.1</th>
                                        <th class="text-center" style="min-width: 100px;">Tgl BC 1.1</th>
                                        <th class="text-center" style="min-width: 100px;">Tgl Masuk</th>
                                        <th class="text-center" style="min-width: 100px;">Jam Masuk</th>
                                        <th class="text-center" style="min-width: 100px;">Tgl Stripping</th>
                                        <th class="text-center" style="min-width: 100px;">Jam Stripping</th>
                                        <th class="text-center" style="min-width: 100px;">Tgl Release</th>
                                        <th class="text-center" style="min-width: 100px;">Jam Release</th>
                                        <th class="text-center" style="min-width: 100px;">Kode Dokumen</th>
                                        <th class="text-center" style="min-width: 100px;">Nomor Dokumen</th>
                                        <th class="text-center" style="min-width: 100px;">Tgl Dokumen</th>
                                        <th class="text-center" style="min-width: 100px;">Location</th>
                                        <th class="text-center" style="min-width: 100px;">Lama Timbun</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="button-container">
                            <div class="col-auto">
                                <button class="btn btn-success formatBeacukai"><i class="fa fa-download"></i> Excel</button>
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
    $(document).ready(function(){
        var start = "{{ $start }}";
        var end = "{{ $end }}";
        console.log('tgl ' + start + ' ' + end);
        $('#tableManifestMasuk').DataTable({
            dom: 'Bfrtip', // Pastikan ada 'B' untuk menampilkan tombol
            buttons: [
                'copy', 'csv', excel , pdf, 'print'
            ],
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollX: true,
            scrollCollapse: true,
            scrollY: '150vh',
            ajax: {
                url: '/lcl/report/manifestDataTable',
                type: 'GET',
                data : {filter:'masuk',
                        start_date : start,
                        end_date : end,
                },
            },
            columns : [
                { className:'tex-center', data:'detil', name:'detil'},
                { className:'tex-center', data:'joborder', name:'joborder'},
                { className:'tex-center', data:'nm_angkut', name:'nm_angkut'},
                { className:'tex-center', data:'nocontainer', name:'nocontainer'},
                { className:'tex-center', data:'size', name:'size'},
                { className:'tex-center', data:'eta', name:'eta'},
                { className:'tex-center', data:'kd_tps_asal', name:'kd_tps_asal'},
                { className:'tex-center', data:'namaconsolidator', name:'namaconsolidator'},
                { className:'tex-center', data:'nohbl', name:'nohbl'},
                { className:'tex-center', data:'tgl_hbl', name:'tgl_hbl'},
                { className:'tex-center', data:'notally', name:'notally'},
                { className:'tex-center', data:'customer', name:'customer'},
                { className:'tex-center', data:'quantity', name:'quantity'},
                { className:'tex-center', data:'final_qty', name:'final_qty'},
                { className:'tex-center', data:'packingName', name:'packingName'},
                { className:'tex-center', data:'packingCode', name:'packingCode'},
                { className:'tex-center', data:'desc', name:'desc'},
                { className:'tex-center', data:'weight', name:'weight'},
                { className:'tex-center', data:'meas', name:'meas'},
                { className:'tex-center', data:'packingTally', name:'packingTally'},
                { className:'tex-center', data:'noplp', name:'noplp'},
                { className:'tex-center', data:'tglPLP', name:'tglPLP'},
                { className:'tex-center', data:'no_bc11', name:'no_bc11'},
                { className:'tex-center', data:'tgl_bc11', name:'tgl_bc11'},
                { className:'tex-center', data:'tglmasuk', name:'tglmasuk'},
                { className:'tex-center', data:'jammasuk', name:'jammasuk'},
                { className:'tex-center', data:'startstripping', name:'startstripping'},
                { className:'tex-center', data:'endstripping', name:'endstripping'},
                { className:'tex-center', data:'tglbuangmty', name:'tglbuangmty'},
                { className:'tex-center', data:'jambuangmty', name:'jambuangmty'},
                { className:'tex-center', data:'dokumen', name:'dokumen'},
                { className:'tex-center', data:'no_dok', name:'no_dok'},
                { className:'tex-center', data:'tglDok', name:'tglDok'},
                { className:'tex-center', data:'location', name:'location'},
                { className:'tex-center', data:'lamaTimbun', name:'lamaTimbun'},
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
            }
        });
    });
</script>

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
    $(document).ready(function(){
        var start = "{{ $start }}";
    var end = "{{ $end }}";
        console.log('tgl ' + start + ' ' + end);
        $('#tableManifestKeluar').DataTable({
            dom: 'Bfrtip', // Pastikan ada 'B' untuk menampilkan tombol
            buttons: [
                'copy', 'csv', excel , pdf, 'print'
            ],
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollX: true,
            scrollCollapse: true,
            scrollY: '50vh',
            ajax: {
                url: '/lcl/report/manifestDataTable',
                type: 'GET',
                data : {filter:'keluar',
                        start_date : start,
                        end_date : end,
                },
            },
            columns : [
                { className:'text-center', data:'detil', name:'detil'},
                { className:'text-center', data:'joborder', name:'joborder'},
                { className:'text-center', data:'nm_angkut', name:'nm_angkut'},
                { className:'text-center', data:'nocontainer', name:'nocontainer'},
                { className:'text-center', data:'size', name:'size'},
                { className:'text-center', data:'eta', name:'eta'},
                { className:'text-center', data:'kd_tps_asal', name:'kd_tps_asal'},
                { className:'text-center', data:'namaconsolidator', name:'namaconsolidator'},
                { className:'text-center', data:'nohbl', name:'nohbl'},
                { className:'text-center', data:'tgl_hbl', name:'tgl_hbl'},
                { className:'text-center', data:'notally', name:'notally'},
                { className:'text-center', data:'customer', name:'customer'},
                { className:'text-center', data:'quantity', name:'quantity'},
                { className:'text-center', data:'final_qty', name:'final_qty'},
                { className:'text-center', data:'packingName', name:'packingName'},
                { className:'text-center', data:'packingCode', name:'packingCode'},
                { className:'text-center', data:'desc', name:'desc'},
                { className:'text-center', data:'weight', name:'weight'},
                { className:'text-center', data:'meas', name:'meas'},
                { className:'text-center', data:'packingTally', name:'packingTally'},
                { className:'text-center', data:'noplp', name:'noplp'},
                { className:'text-center', data:'tglPLP', name:'tglPLP'},
                { className:'text-center', data:'no_bc11', name:'no_bc11'},
                { className:'text-center', data:'tgl_bc11', name:'tgl_bc11'},
                { className:'text-center', data:'tglmasuk', name:'tglmasuk'},
                { className:'text-center', data:'jammasuk', name:'jammasuk'},
                { className:'text-center', data:'startstripping', name:'startstripping'},
                { className:'text-center', data:'endstripping', name:'endstripping'},
                { className:'text-center', data:'tglbuangmty', name:'tglbuangmty'},
                { className:'text-center', data:'jambuangmty', name:'jambuangmty'},
                { className:'text-center', data:'dokumen', name:'dokumen'},
                { className:'text-center', data:'no_dok', name:'no_dok'},
                { className:'text-center', data:'tglDok', name:'tglDok'},
                { className:'text-center', data:'location', name:'location'},
                { className:'text-center', data:'lamaTimbun', name:'lamaTimbun'},
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
            }
        });
    });
</script>

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
    $(document).ready(function(){
        var start = "{{ $start }}";
        var end = "{{ $end }}";
        console.log('tgl ' + start + ' ' + end);
        $('#tableManifestAkhir').DataTable({
            dom: 'Bfrtip', // Pastikan ada 'B' untuk menampilkan tombol
            buttons: [
                'copy', 'csv', excel , pdf, 'print'
            ],
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollX: true,
            scrollCollapse: true,
            scrollY: '50vh',
            ajax: {
                url: '/lcl/report/manifestDataTable',
                type: 'GET',
                data : {filter:'akhir',
                        start_date: start,
                        end_date : end,
                },
            },
            columns : [
                { className:'text-center', data:'detil', name:'detil'},
                { className:'text-center', data:'joborder', name:'joborder'},
                { className:'text-center', data:'nm_angkut', name:'nm_angkut'},
                { className:'text-center', data:'nocontainer', name:'nocontainer'},
                { className:'text-center', data:'size', name:'size'},
                { className:'text-center', data:'eta', name:'eta'},
                { className:'text-center', data:'kd_tps_asal', name:'kd_tps_asal'},
                { className:'text-center', data:'namaconsolidator', name:'namaconsolidator'},
                { className:'text-center', data:'nohbl', name:'nohbl'},
                { className:'text-center', data:'tgl_hbl', name:'tgl_hbl'},
                { className:'text-center', data:'notally', name:'notally'},
                { className:'text-center', data:'customer', name:'customer'},
                { className:'text-center', data:'quantity', name:'quantity'},
                { className:'text-center', data:'final_qty', name:'final_qty'},
                { className:'text-center', data:'packingName', name:'packingName'},
                { className:'text-center', data:'packingCode', name:'packingCode'},
                { className:'text-center', data:'desc', name:'desc'},
                { className:'text-center', data:'weight', name:'weight'},
                { className:'text-center', data:'meas', name:'meas'},
                { className:'text-center', data:'packingTally', name:'packingTally'},
                { className:'text-center', data:'noplp', name:'noplp'},
                { className:'text-center', data:'tglPLP', name:'tglPLP'},
                { className:'text-center', data:'no_bc11', name:'no_bc11'},
                { className:'text-center', data:'tgl_bc11', name:'tgl_bc11'},
                { className:'text-center', data:'tglmasuk', name:'tglmasuk'},
                { className:'text-center', data:'jammasuk', name:'jammasuk'},
                { className:'text-center', data:'startstripping', name:'startstripping'},
                { className:'text-center', data:'endstripping', name:'endstripping'},
                { className:'text-center', data:'tglbuangmty', name:'tglbuangmty'},
                { className:'text-center', data:'jambuangmty', name:'jambuangmty'},
                { className:'text-center', data:'dokumen', name:'dokumen'},
                { className:'text-center', data:'no_dok', name:'no_dok'},
                { className:'text-center', data:'tglDok', name:'tglDok'},
                { className:'text-center', data:'location', name:'location'},
                { className:'text-center', data:'lamaTimbun', name:'lamaTimbun'},
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
                let filterBy = 'akhir';
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();

                // Redirect user to download link
                let url = `/lcl/report/manifestGenerate?filter=${filterBy}&start_date=${startDate}&end_date=${endDate}`;
                window.location.href = url;
                Swal.close();
            }
        });
    });
</script>
<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=1500,height=1000');
    }
</script>
@endsection
