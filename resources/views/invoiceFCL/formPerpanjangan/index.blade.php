@extends('partial.main')

@section('content')
    <body>
        <div class="card">
            <div class="card-header">
                <div class="button-cotnainer">
                    <button type="button" class="btn btn-primary createForm"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="table">
                    <table class="table-hover" id="tableForm">
                        <thead>
                            <tr>
                                <th>No Bl AWB</th>
                                <th>Tgl Bl AWB</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </body>
@endsection

@section('custom_js')
<script>
    $(document).ready(function(){
        $('#tableForm').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/invoiceFCL/form/extend/dataTable',
            columns: [
                {data:'nobl', name:'nobl'},
                {data:'tgl_bl_awb', name:'tgl_bl_awb'},
                {data:'action', name:'action'},
            ]
        })
    })
</script>
<script>
    $(document).on('click', '.createForm', function(){
        Swal.fire({
                title: 'Are you sure?',
                text: "Apakah anda yakin ingin membuat invoice?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    window.location.href = '/invoiceFCL/form/extend/createIndex/Step1';
                }
            });
    })
</script>

@endsection