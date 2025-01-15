@extends('partial.main')

@section('content')

<section>
    <div class="card">
        <div class="card-body">
           <div class="table table-responsive">
                <table class="table table-hover table-stripped" id="tableBarcode" style="overflow-x:auto;">
                     <thead>
                         <tr>
                             <th>Print Barcode</th>
                             <th>Ref Type</th>
                             <th>Ref Action</th>
                             <th>Ref Number</th>
                             <th>Status</th>
                             <th>Expired</th>
                             <th>Time In</th>
                             <th>Time Out</th>
                             <th>Photo In</th>
                             <th>Photo Out</th>
                             <th>Created At</th>
                             <th>Last Update</th>
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
        $('#tableBarcode').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/autoGate-barcode/data',
            columns: [
                {data:'id', name:'id', className:'text0center', 
                    render: function(data, row){
                        return `<a href="javascript:void(0)" onclick="openWindow('/barcode/autoGate-index${data}')" class="btn btn-sm btn-danger"><i class="fa fa-print"></i></a>`
                    }
                },
                {data:'ref_type', name:'ref_type', className:'text-center'},
                {data:'ref_action', name:'ref_action', className:'text-center'},
                {data:'ref_number', name:'ref_number', className:'text-center'},
                {data:'status', name:'status', className:'text-center'},
                {data:'expired', name:'expired', className:'text-center'},
                {data:'time_in', name:'time_in', className:'text-center'},
                {data:'time_out', name:'time_out', className:'text-center'},
                {data:'id', name:'id', className:'text-center',
                    render: function(data, row){
                        return ` <a href="javascript:void(0)" onclick="openWindow('/barcode/autoGate-photoIn${data}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>`
                    }
                },
                {data:'id', name:'id', className:'text-center',
                    render: function(data, row){
                        return ` <a href="javascript:void(0)" onclick="openWindow('/barcode/autoGate-photoOut${data}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>`
                    }
                },
                {data:'created_at', name:'created_at', className:'text-center'},
                {data:'updated_at', name:'updated_at', className:'text-center'},
            ]
        })
    });
</script>
<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=600,height=800');
    }
</script>
@endsection