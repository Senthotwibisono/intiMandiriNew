@extends('partial.main')
@section('custom_styles')

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
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') ?? $start }}">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') ?? $end }}">
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
                                    <th class="text-center">Size</th>
                                    <th class="text-center">ETA</th>
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
                        <table class="table-responsive table-striiped">
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
                                    <th class="text-center">Size</th>
                                    <th class="text-center">ETA</th>
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
                        <table class="table-responsive table-striiped">
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
                <table class="table-responsive table-striped table-bordered">
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
    </div>
</section>


@endsection

@section('custom_js')
<script>
    $(document).ready(function(){
        var start = "{{ $start }}";
        var end = "{{ $end }}";
        console.log('tgl ' + start + ' ' + end);
        $('#dataReportContMasuk').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
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
            }
        });
    });
</script>

<script>
    $(document).ready(function(){
        var start = "{{ $start }}";
        var end = "{{ $end }}";
        console.log('tgl ' + start + ' ' + end);
        $('#dataReportContKeluar').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollX: true,
            scrollCollapse: true,
            scrollY: '30vh',
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
