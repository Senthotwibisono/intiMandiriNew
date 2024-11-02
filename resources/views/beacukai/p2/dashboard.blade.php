@extends('partial.bc.main')
@section('content')

<div class="page-content">
    <div class="col-12">
        <div class="card">
            <div class="card-content">
                <div class="card-header">
                    <h4>Welcome to IntiMandiri Depo Information System</h4><br>
                    <p>Please make sure the manifest which turn into <span class="badge bg-danger">Red Segel</span> before submit the form!!!</p>
                </div>
                <div class="card-body">
                    <h4>Last Activity</h4>
                    <div class="table table-responsive" style="overflow-x:auto;">
                        <table class="table table-hover table-stripped" id="tableLog">
                            <thead>
                                <tr>
                                    <th>NO HBL</th>
                                    <th>NO Container</th>
                                    <th>Job Order</th>
                                    <th>Tipe</th>
                                    <th>Action</th>
                                    <th>Segel</th>
                                    <th>Alasan</th>
                                    <th>Keterangan</th>
                                    <th>UID</th>
                                    <th>Time Stamp</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('custom_js')

<script>
    $(document).ready(function(){
        $('#tableLog').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/bc-p2/logData',
            columns:[
                {data:'ref_name', name:'ref_name', className:'text-center'},
                {data:'container', name:'container', className:'text-center'},
                {data:'jobOrder', name:'jobOrder', className:'text-center'},
                {data:'ref_type', name:'ref_type', className:'text-center'},
                {data:'action', name:'action', className:'text-center'},
                {data:'no_segel', name:'no_segel', className:'text-center'},
                {data:'alasan', name:'alasan', className:'text-center'},
                {data:'keterangan', name:'keterangan', className:'text-center'},
                {data:'user', name:'user', className:'text-center'},
                {data:'created_at', name:'created_at', className:'text-center'},
            ]
        });
    })
</script>

@endsection