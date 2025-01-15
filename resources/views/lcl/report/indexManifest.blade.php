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
        <form action="{{ route('report.lcl.generateManifest')}}" method="get">
            @csrf
            <div class="card-body">
                <div class="row mt-0">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Filter By</label>
                            <select name="filter" style="width: 100%;" class="js-example-basic-single">
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
                <table class="table-hover" id="tableManifest">
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
                            <th class="text-center">No HBL</th>
                            <th class="text-center">Tgl HBL</th>
                            <th class="text-center">NO Tally</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Quantity Real Time</th>
                            <th class="text-center">Nama Kemas</th>
                            <th class="text-center">Kode Kemas</th>
                            <th class="text-center">Desc of Goods</th>
                            <th class="text-center">Weight</th>
                            <th class="text-center">Meas</th>
                            <th class="text-center">Packing Tally</th>
                            <th class="text-center">No PLP</th>
                            <th class="text-center">Tgl PLP</th>
                            <th class="text-center">No BC 1.1</th>
                            <th class="text-center">Tgl BC 1.1</th>
                            <th class="text-center">Tgl Masuk</th>
                            <th class="text-center">Jam Masuk</th>
                            <th class="text-center">Tgl Stripping</th>
                            <th class="text-center">Jam Stripping</th>
                            <th class="text-center">Tgl Release</th>
                            <th class="text-center">Jam Release</th>
                            <th class="text-center">Kode Dokumen</th>
                            <th class="text-center">Nomor Dokumen</th>
                            <th class="text-center">Tgl Dokumen</th>
                            <th class="text-center">Location</th>
                            <th class="text-center">Lama Timbun</th>
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
        $('#tableManifest').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: '/lcl/report/manifestDataTable',
            columns : [
                { data:'detil', name:'detil'},
                { data:'joborder', name:'joborder'},
                { data:'nm_angkut', name:'nm_angkut'},
                { data:'nocontainer', name:'nocontainer'},
                { data:'size', name:'size'},
                { data:'eta', name:'eta'},
                { data:'kd_tps_asal', name:'kd_tps_asal'},
                { data:'namaconsolidator', name:'namaconsolidator'},
                { data:'nohbl', name:'nohbl'},
                { data:'tgl_hbl', name:'tgl_hbl'},
                { data:'notally', name:'notally'},
                { data:'customer', name:'customer'},
                { data:'quantity', name:'quantity'},
                { data:'final_qty', name:'final_qty'},
                { data:'packingName', name:'packingName'},
                { data:'packingCode', name:'packingCode'},
                { data:'desc', name:'desc'},
                { data:'weight', name:'weight'},
                { data:'meas', name:'meas'},
                { data:'packingTally', name:'packingTally'},
                { data:'noplp', name:'noplp'},
                { data:'tglPLP', name:'tglPLP'},
                { data:'no_bc11', name:'no_bc11'},
                { data:'tgl_bc11', name:'tgl_bc11'},
                { data:'tglmasuk', name:'tglmasuk'},
                { data:'jammasuk', name:'jammasuk'},
                { data:'startstripping', name:'startstripping'},
                { data:'endstripping', name:'endstripping'},
                { data:'tglbuangmty', name:'tglbuangmty'},
                { data:'jambuangmty', name:'jambuangmty'},
                { data:'dokumen', name:'dokumen'},
                { data:'no_dok', name:'no_dok'},
                { data:'tglDok', name:'tglDok'},
                { data:'location', name:'location'},
                { data:'lamaTimbun', name:'lamaTimbun'},
            ]
        });
    });
</script>
<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=1500,height=1000');
    }
</script>
@endsection
