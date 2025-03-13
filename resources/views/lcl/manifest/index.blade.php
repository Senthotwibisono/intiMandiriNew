@extends('partial.main')
@section('custom_styles')

@endsection
@section('content')
<section>
    <div class="card">
        <div class="card-body">
            <!-- <div class="row">
                <div class="col-auto ms-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual">Add Data</button>
                </div>
            </div> -->
            <br>
            <div class="table table-fixed">
                <table class="table table-hover table-stripped" id="tableManifest">
                    <thead style="white-space: nowrap;">
                        <tr>
                            <th class="text-center" style="min-width: 100px;">Action</th>
                            <th class="text-center" style="min-width: 100px;">No Job Order</th>
                            <th class="text-center" style="min-width: 100px;">No SPK</th>
                            <th class="text-center" style="min-width: 100px;">No Container</th>
                            <th class="text-center" style="min-width: 100px;">No MBL</th>
                            <th class="text-center" style="min-width: 100px;">No PLP</th>
                            <th class="text-center" style="min-width: 100px;">Tgl PLP</th>
                            <th class="text-center" style="min-width: 100px;">Kd Kantor</th>
                            <th class="text-center" style="min-width: 100px;">Kd TPS</th>
                            <th class="text-center" style="min-width: 100px;">Kd TPS Asal</th>
                            <th class="text-center" style="min-width: 100px;">Kd TPS Tujuan</th>
                            <th class="text-center" style="min-width: 100px;">Nama Angkut</th>
                            <th class="text-center" style="min-width: 100px;">No Voy</th>
                            <th class="text-center" style="min-width: 100px;">No Surat</th>
                            <th class="text-center" style="min-width: 100px;">No BC 11</th>
                            <th class="text-center" style="min-width: 100px;">Tgl BC 11</th>
                            <th class="text-center" style="min-width: 100px;">ETA</th>
                            <th class="text-center" style="min-width: 100px;">Vessel</th>
                            <th class="text-center" style="min-width: 100px;">UID</th>
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
    $(document).ready(function () {
        $('#tableManifest').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: '/lcl/manifest/data',
            columns: [
                {data:'id', name:'id', className:'text-center',
                    render: function(data, row){
                        return `<a href="/lcl/manifest/detail-${data}" class="btn btn-warning"><i class="fa fa-pen"></i></a>`
                    }
                },
                {data:'job.nojoborder', name:'job.nojoborder', className:'text-center'},
                {data:'job.nospk', name:'job.nospk', className:'text-center'},
                {data:'nocontainer', name:'nocontainer', className:'text-center'},
                {data:'job.nombl', name:'job.nombl', className:'text-center'},
                {data:'no_plp', name:'no_plp', className:'text-center' },
                {data:'tgl_plp', name:'tgl_plp', className:'text-center' },
                {data:'kd_kantor', name:'kd_kantor', className:'text-center' },
                {data:'kd_tps', name:'kd_tps', className:'text-center' },
                {data:'kd_tps_asal', name:'kd_tps_asal', className:'text-center' },
                {data:'kd_tps_tujuan', name:'kd_tps_tujuan', className:'text-center' },
                {data:'nm_angkut', name:'nm_angkut', className:'text-center' },
                {data:'no_voy_flight', name:'no_voy_flight', className:'text-center' },
                {data:'no_surat', name:'no_surat', className:'text-center' },
                {data:'no_bc11', name:'no_bc11', className:'text-center' },
                {data:'tgl_bc11', name:'tgl_bc11', className:'text-center' },
                {data:'job.eta', name:'job.eta', className:'text-center'},
                {data:'kapal_cont', name:'kapal_cont', className:'text-center'},
                {data:'user.name', name:'user.name', className:'text-center'},
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
        })
    });
</script>
@endsection