@extends('partial.main')

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
            <div class="table table-responsive">
                <table class="table table-hover table-stripped" id="tableManifest">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>No Job Order</th>
                            <th>No SPK</th>
                            <th>No Container</th>
                            <th>No MBL</th>
                            <th>ETA</th>
                            <th>Vessel</th>
                            <th>UID</th>
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
                {data:'job.eta', name:'job.eta', className:'text-center'},
                {data:'kapal_cont', name:'kapal_cont', className:'text-center'},
                {data:'user.name', name:'user.name', className:'text-center'},
            ]
        })
    });
</script>
@endsection