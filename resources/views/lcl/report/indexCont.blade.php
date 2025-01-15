@extends('partial.main')
@section('custom_styles')

<style>
    .table-responsive td,
    .table-responsive th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

@endsection
@section('content')

<section>
    <div class="card">
        <div class="card-header">
            <header>Generate Report</header>
        </div>
        <form action="{{ route('report.lcl.generateCont')}}" method="get">
            @csrf
            <div class="card-body">
                <div class="row mt-0">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Filter By</label>
                            <select name="filter" style="width: 100%;" class="js-example-basic-single">
                                <option disabled selected>Pilih Satu</option>
                                <option value="Tgl PLP">Tgl PLP</option>
                                <option value="Tgl Gate In">Tgl Gate In</option>
                                <option value="Tgl BC 1.1">Tgl BC 1.1</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-success" type="submit">Generate</button>
            </div>
        </form>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-body">
            <div class="table">
                <table class="table-hover" id="dataReportCont">
                    <thead>
                        <tr>
                            <th class="text-center">Action</th>
                            <th class="text-center">No Job Order</th>
                            <th class="text-center">Nama Angkut</th>
                            <th class="text-center">No Container</th>
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
                            <th class="text-center">Tgl Stripping</th>
                            <th class="text-center">Jam Stripping</th>
                            <th class="text-center">Tgl Keluar</th>
                            <th class="text-center">Jam Keluar</th>
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
    $(document).ready(function(){
        $('#dataReportCont').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: '/lcl/report/dataCont',
            columns:[
                { data:'detil', name:'detil', className:'text-center' },
                { data:'jobordr', name:'jobordr', className:'text-center' },
                { data:'nm_angkut', name:'nm_angkut', className:'text-center' },
                { data:'nocontainer', name:'nocontainer', className:'text-center' },
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
                { data:'tglstripping', name:'tglstripping', className:'text-center' },
                { data:'jamstripping', name:'jamstripping', className:'text-center' },
            ]
        })
    })
</script>
<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=1500,height=1000');
    }
</script>
@endsection
