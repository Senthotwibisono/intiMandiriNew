@extends('partial.main')

@section('content')

<section>
    <div class="card">
        <div class="card-body">
            <table class="tabelCustom table table-bordered table-striped" style="overflow-x:auto;">
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
                    <tbody>
                        @foreach($barcodes as $barcode)
                        <tr>
                            <td class="text-center">
                                <a href="javascript:void(0)" onclick="openWindow('/barcode/autoGate-index{{$barcode->id}}')" class="btn btn-sm btn-danger"><i class="fa fa-print"></i></a>
                            </td>
                            <td>{{$barcode->ref_type}}</td>
                            <td>{{$barcode->ref_action}}</td>
                            <td>{{$barcode->ref_number}}</td>
                            <td>{{$barcode->status}}</td>
                            <td>{{$barcode->expired}}</td>
                            <td>{{$barcode->time_in}}</td>
                            <td>{{$barcode->time_out}}</td>
                            <td class="text-center">
                                <a href="javascript:void(0)" onclick="openWindow('/barcode/autoGate-photoIn{{$barcode->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0)" onclick="openWindow('/barcode/autoGate-photoOut{{$barcode->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                            </td>
                            <td>{{$barcode->created_at}}</td>
                            <td>{{$barcode->updated_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </thead>
            </table>
        </div>
    </div>
</section>

@endsection
@section('custom_js')
<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=600,height=800');
    }
</script>
@endsection