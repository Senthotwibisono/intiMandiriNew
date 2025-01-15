@extends('partial.main')
@section('custom_styles')

@endsection
@section('content')

<section>
    <div class="card">
    <div class="card-body">
            <div class="table">
                <table class="table-hover table-responsive" id="tableDetil">
                    <thead>
                        <tr>
                            <th class="text-center">Action</th>
                            <th class="text-center">No HBL</th>
                            <th class="text-center">Tgl HBL</th>
                            <th class="text-center">No Tally</th>
                            <th class="text-center">Barcode</th>
                            <th class="text-center">Shipper</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Qty Real Time</th>
                            <th class="text-center">Packing</th>
                            <th class="text-center">Kode Kemas</th>
                            <th class="text-center">Desc</th>
                            <th class="text-center">Weight</th>
                            <th class="text-center">Meas</th>
                            <th class="text-center">Tgl Mulai Stripping</th>
                            <th class="text-center">Tgl Selesai Stripping</th>
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
        $('#tableDetil').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            scrollY: '50vh',
            ajax: '/lcl/realisasi/indexData', // Fix concatenation
            columns: [
                { data: 'action', name: 'action', className: 'text-center' }, // Define the column
                { data: 'nohbl', name: 'nohbl', className: 'text-center' }, // Define the column
                { data: 'tgl_hbl', name: 'tgl_hbl', className: 'text-center' }, // Define the column
                { data: 'notally', name: 'notally', className: 'text-center' }, // Define the column
                { data: 'barcode', name: 'barcode', className: 'text-center' }, // Define the column
                { data: 'shipper', name: 'shipper', className: 'text-center' }, // Define the column
                { data: 'customer', name: 'customer', className: 'text-center' }, // Define the column
                { data: 'quantity', name: 'quantity', className: 'text-center' }, // Define the column
                { data: 'final_qty', name: 'final_qty', className: 'text-center' }, // Define the column
                { data: 'packingName', name: 'packingName', className: 'text-center' }, // Define the column
                { data: 'packingCode', name: 'packingCode', className: 'text-center' }, // Define the column
                { data: 'desc', name: 'desc', className: 'text-center' }, // Define the column
                { data: 'weight', name: 'weight', className: 'text-center' }, // Define the column
                { data: 'meas', name: 'meas', className: 'text-center' }, // Define the column
                { data: 'startStripping', name: 'startStripping', className: 'text-center' }, // Define the column
                { data: 'endstripping', name: 'endstripping', className: 'text-center' }, // Define the column
            ]
        })
    });
</script>
</script>
@endsection