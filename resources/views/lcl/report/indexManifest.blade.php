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
        <div class="card-body">
            <div class="row mt-0">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="">Filter By</label>
                        <select name="filter" id="filter" style="width: 100%;" class="js-example-basic-single">
                            <option disabled selected>Pilih Satu</option>
                            <option value="Tgl PLP">Tgl PLP</option>
                            <option value="ETA">ETA</option>
                            <option value="Tgl Gate In">Tgl Gate In</option>
                            <option value="Tgl BC 1.1">Tgl BC 1.1</option>
                            <option value="Tgl Release">Tgl Release</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="">Container/No Job Order</label>
                        <select name="container_id" id="container_id" style="width: 100%;" class="js-example-basic-single">
                            <option disabled selected value>Pilih Satu jika Diperlukan</option>
                            @foreach($conts as $cont)
                                <option value="{{$cont->id}}">{{$cont->nocontainer}} - {{$cont->job->nojoborder}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
        <button class="btn btn-success generateFilter" type="button">Generate</button>
        </div>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-body">
            <div class="table">
                <table class="table-hover" id="tableManifest">
                    <thead style="white-space: nowrap;">
                        <tr>
                            <th class="text-center" style="min-width: 100px;">Action</th>
                            <th class="text-center" style="min-width: 100px;">Flag Segel Merah</th>
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
                            <th class="text-center" style="min-width: 100px;">Nomor Polisi Release</th>
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
    </div>
</section>

@endsection

@section('custom_js')
<script>
    $(document).on('click', '.generateFilter', function(){
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
                let table = $('#tableManifest').DataTable();

                // Reload DataTables but pass parameters as 'data'
                table.ajax.reload(null, false);                
                Swal.close();
            }
        });
    })
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
                let container_id = $('#container_id').val();

                // Redirect user to download link
                let url = `/lcl/report/manifestGenerate?filter=${filterBy}&start_date=${startDate}&end_date=${endDate}&container_id=${container_id}`;
                window.location.href = url;
                Swal.close();
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
        $('#tableManifest').DataTable({
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], // Pilihan jumlah data
            pageLength: 25, // Default jumlah data per halaman
            dom: 'lBfrtip', // Pastikan ada 'B' untuk menampilkan tombol
            buttons: [
                'copy', 'csv', excel , pdf, 'print'
            ],
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                url: '/lcl/report/manifestDataTable',
                data: function(d) {
                    d.filter = $('#filter').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.container_id = $('#container_id').val();
                }
            },
            columns : [
                { className:'text-center', data:'detil', name:'detil'},
                { className:'text-center', data:'flag_segel_merah', name:'flag_segel_merah'},
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
                { className:'text-center', data:'tglrelease', name:'tglrelease'},
                { className:'text-center', data:'jamrelease', name:'jamrelease'},
                { className:'text-center', data:'nopol_release', name:'nopol_release'},
                { className:'text-center', data:'dokumen', name:'dokumen'},
                { className:'text-center', data:'no_dok', name:'no_dok'},
                { className:'text-center', data:'tglDok', name:'tglDok'},
                { className:'text-center', data:'location', name:'location'},
                { className:'text-center', data:'lamaTimbun', name:'lamaTimbun'},
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
    function openWindow(url) {
        window.open(url, '_blank', 'width=1500,height=1000');
    }
</script>
@endsection
