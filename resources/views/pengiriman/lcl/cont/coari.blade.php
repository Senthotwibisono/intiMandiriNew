@extends('partial.main')
@section('custom_styles')

@endsection

@section('content')

<section>
    <div class="card">
        <div class="card-body">
            <div class="table">
                <table class="table-hover text-center" id="dataCoariCont">
                    <thead style="white-space: nowrap;">
                        <tr>
                            <th style="min-width: 100px">Action</th>
                            <th style="min-width: 100px">Ref Number</th>
                            <th style="min-width: 100px">No Container</th>
                            <th style="min-width: 100px">Size</th>
                            <th style="min-width: 100px">No Bl Awb</th>
                            <th style="min-width: 100px">Tgl Bl Awb</th>
                            <th style="min-width: 100px">Response</th>
                            <th style="min-width: 100px">Waktu In Out</th>
                            <th style="min-width: 100px">Tgl Kirim</th>
                            <th style="min-width: 100px">Jam Kirim</th>
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
        $('#dataCoariCont').DataTable({
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], // Pilihan jumlah data
            pageLength: 25, // Default jumlah data per halaman
            dom: 'lBfrtip',
            buttons: ['excel', 'pdf'],
            processing: true, 
            serverSide: true, 
            scrollX: true,
            ajax: '{{ route('pengiriman.lcl.containerData')}}',
            columns: [
                {name: 'action', data: 'action'},
                {name: 'ref_number', data: 'ref_number'},
                {name: 'no_cont', data: 'no_cont'},
                {name: 'uk_cont', data: 'uk_cont'},
                {name: 'no_bl_awb', data: 'no_bl_awb'},
                {name: 'tgl_bl_awb', data: 'tgl_bl_awb'},
                {name: 'response', data: 'response'},
                {name: 'wk_inout', data: 'wk_inout'},
                {name: 'tgl_entry', data: 'tgl_entry'},
                {name: 'jam_entry', data: 'jam_entry'},
            ],
            initComplete: function () {
                var api = this.api();
                
                api.columns().every(function (index) {
                    var column = this;
                    var excludedColumns = [0, 1]; // Kolom yang tidak ingin difilter (detil, flag_segel_merah, lamaHari)
                    
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
            }, 
        })
    })
</script>
@endsection