@extends('partial.main')
@section('custom_styles')
<style>
    .dashboard-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,.07);
        height: 100%;
        transition: .2s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,.1);
    }

    .dashboard-card .card-label {
        color: #6c757d;
        font-size: .85rem;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .dashboard-card .card-value {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .card-line {
        width: 100%;
        height: 5px;
        border-radius: 8px;
    }

    .main-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,.07);
        overflow: hidden;
    }

    .main-card .card-header {
        background: transparent;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 1.25rem;
    }

    .main-card .card-header h5 {
        margin: 0;
        font-weight: 600;
    }

    #tableBehandle {
        width: 100% !important;
        font-family: "Plus Jakarta Sans", sans-serif;
    }

    #tableBehandle thead th {
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
        font-size: 13px;
        font-weight: 600;
    }

    #tableBehandle tbody td {
        vertical-align: middle;
        font-size: 13px;
    }

    #tableBehandle .address-field {
        min-width: 220px;
        resize: vertical;
        font-size: 12px;
        background: transparent;
        border: 1px solid #dee2e6;
    }

    #filter-row th {
        padding: 6px;
        background: #fff;
    }

    #filter-row input,
    #filter-row select {
        min-width: 100px;
        font-size: 12px;
    }

    .dt-scroll-body {
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .dataTables_wrapper .dt-buttons .btn {
        margin-right: 4px;
    }

    .dataTables_wrapper .dt-search input {
        margin-left: 6px;
    }

    .status-o td {
        background-color: #FF8C00 !important;
        color: #fff !important;
    }

    .status-pkk td {
        background-color: #cc0a0a !important;
        color: #fff !important;
    }

    .status-pkb td {
        background-color: #3273dc !important;
        color: #fff !important;
    }

    .status-ready td {
        background-color: #9df5b1 !important;
        color: #155724 !important;
    }

    .status-progress td {
        background-color: #7a7f86 !important;
        color: #242525 !important;
    }

    .status-finish td {
        background-color: #f5c6cb !important;
        color: #721c24 !important;
    }

    .summary-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(13,110,253,.1);
        color: #0d6efd;
        font-size: 18px;
    }

    @media (max-width: 768px) {
        .dashboard-card .card-value {
            font-size: 1.3rem;
        }
    }
</style>
@endsection
@section('content')

<div class="page-content">
    <div class="card">
        <div class="card-header">
            <form action="{{ route('lcl.behandle.report-behandle') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="from_date" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="from_date" name="from_date"
                            value="{{ request('from_date') }}">
                    </div>
        
                    <div class="col-md-4">
                        <label for="to_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="to_date" name="to_date"
                            value="{{ request('to_date') }}">
                    </div>
        
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table">
                <table class="table-stripped table-hover" id="tableBehandle">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Jenis Dok</th>
                            <th>No Pendfataran PIB</th>
                            <th>Tanggal SPJM</th>
                            <th>Tanggal Pemeriksaan</th>
                            <th>Petugas Pemeriksaan</th>
                            <th>No Container</th>
                            <th>No HBL</th>
                            <th>Customer</th>
                            <th>Quantity</th>
                            <th>Jenis Kemasan</th>
                            <th>Deskripsi</th>
                        </tr>
                        <tr id="filter-row">
                           @for ($i = 0; $i < 12; $i++)
                                <th></th>
                            @endfor
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="card">
                <div class="card-header">
                    Behandle Form
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">No HBl</label>
                                        <select class="form-select js-example-basic-single" id="manifest_id" data-placeholder="Pilih Satu!">
                                            <option value=""></option>
                                            @foreach($manifestes as $data)
                                                <option value="{{$data->id}}">{{$data->nohbl}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="">Jenis SPJM</label>
                                            <select name="jenis_spjm" id="jenis_spjm" class="js-example-basic-single select2 form-select" style="width: 100%">
                                                <option disabled selected value>Pilih Satu</option>
                                                <option value="spjm">SPJM</option>
                                                <option value="karantina">Karantina</option>
                                                <option value="nhi">NHI</option>
                                                <option value="pibk">PIBK</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="">No SPJM</label>
                                            <input type="text" name="no_spjm" id="no_spjm" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="">Tgl SPJM</label>
                                            <div class="input-group mb-3">
                                                <input type="date" name="tgl_spjm" id="tgl_spjm" class="form-control">
                                                <button class="btn btn-outline-info" onClick="searchSPJM(this)" type="button">Check</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="">Petugas Behandle</label>
                                        <input type="text" name="" id="petugas_behandle" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Date Ready Behandle</label>
                                <div class="input-group">
                                    <input type="datetime-local" class="form-control" name="date_ready_behandle" id="date_ready_behandle">
                                    <button type="button" class="btn btn-warning" onclick="$('#date_ready_behandle').val('')"> Clear </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Date Check Behandle</label>
                                        <div class="input-group">
                                            <input type="datetime-local" class="form-control" id="date_check_behandle">
                                            <button type="button" class="btn btn-warning" onclick="$('#date_check_behandle').val('')"> Clear </button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Desc Check Behandle</label>
                                        <textarea class="form-control" name="" id="desc_check_behandle" cols="30" rows="10">
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Date Finish Behandle</label>
                                        <div class="input-group">
                                            <input type="datetime-local" class="form-control" id="date_finish_behandle">
                                            <button type="button" class="btn btn-warning" onclick="$('#date_finish_behandle').val('')"> Clear </button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Desc Finish Behandle</label>
                                        <textarea class="form-control" name="" id="desc_finish_behandle" cols="30" rows="10">
                                        </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-success" onClick="submitBehandle()">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('custom_js')
<script>
    let excel = {
        extend: 'excelHtml5',
        autoFilter: true,
        sheetName: 'Exported data',
        className: 'btn btn-outline-success'
    };

    let pdf = {
        extend: 'pdfHtml5',
        text: 'Ekspor PDF',
        className: 'btn btn-outline-danger',
        orientation: 'landscape',
        pageSize: 'A1',
        download: 'open',
        exportOptions: {
            columns: function() {
                return true;
            }
        },
        customize: function(doc) {
            doc.defaultStyle.fontSize = 8;
            doc.styles.tableHeader.fontSize = 8;
            doc.styles.title.fontSize = 12;
            doc.pageMargins = [2, 2, 2, 2];
            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length).fill('*');
        }
    };

    $(document).ready(function() {
        $('#tableBehandle').DataTable({
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'All']
            ],
            pageLength: 25,

            layout: {
                topStart: ['pageLength', 'buttons'],
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging'
            },

            buttons: [
                'copy',
                'csv',
                excel,
                pdf,
                'print'
            ],

            orderCellsTop: true,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollY: '50vh',

            ajax: {
                url: "{{ route('lcl.behandle.data-behandle') }}",
            },

            columns: [
                {className: 'text-center', data: 'edit', name: 'edit'},
                {className: 'text-center', data: 'jenis_spjm', name: 'jenis_spjm'},
                {className: 'text-center', data: 'no_spjm', name: 'no_spjm'},
                {className: 'text-center', data: 'tgl_spjm', name: 'tgl_spjm'},
                {className: 'text-center', data: 'date_check_behandle', name: 'date_check_behandle'},
                {className: 'text-center', data: 'petugas_behandle', name: 'petugas_behandle'},
                {className: 'text-center', data: 'cont.nocontainer', name: 'cont.nocontainer'},
                {className: 'text-center', data: 'nohbl', name: 'nohbl'},
                {className: 'text-center', data: 'customer.name', name: 'customer.name'},
                {className: 'text-center', data: 'quantity', name: 'quantity'},
                {className: 'text-center', data: 'packing.code', name: 'packing.code'},
                {className: 'text-center', data: 'descofgoods', name: 'descofgoods'},
            ],

            createdRow: function(row, data) {
                $(row).removeClass(
                    'status-pkk status-pkb status-ready status-progress status-finish'
                );

                if (data.status_behandle === null) {
                    if (data.flag_pkb === 'N') {
                        $(row).addClass('status-o');
                    } else {
                        $(row).addClass('status-pkk');
                    }

                    return;
                }

                switch (Number(data.status_behandle)) {
                    case 1:
                        $(row).addClass('status-ready');
                        break;

                    case 2:
                        $(row).addClass('status-pkb');
                        break;

                    case 3:
                        $(row).addClass('status-finish');
                        break;
                }
            },

            initComplete: function() {
                let api = this.api();

                api.columns().every(function(index) {
                    if (index === 0) {
                        return;
                    }

                    let column = this;
                    let cell = $('#filter-row th').eq(index);

                    // if (index === 13) {
                    //     $('<select class="form-select form-select-sm">' +
                    //         '<option value="">All</option>' +
                    //         '<option value="PKK">IN 1MUT</option>' +
                    //         '<option value="PKB">PKK Belum Siap</option>' +
                    //         '<option value="1">Ready</option>' +
                    //         '<option value="2">On Progress</option>' +
                    //         '<option value="3">Finish</option>' +
                    //         '</select>')
                    //         .appendTo(cell.empty())
                    //         .on('change', function() {
                    //             column.search($(this).val()).draw();
                    //         });

                    //     return;
                    // }

                    $('<input type="text" class="form-control form-control-sm">')
                        .appendTo(cell.empty())
                        .on('keyup change', function() {
                            column.search($(this).val()).draw();
                        });
                });
            }
        });

        $('#tableBehandle').on('init.dt', function() {
            setTimeout(function() {
                $('#tableBehandle').DataTable().columns.adjust();
            }, 100);
        });
    });

    function openPhoto(id) {
        window.open(
            '/fcl/behandle-detail/' + id,
            'behandlePhoto',
            'width=900,height=700,left=200,top=100,resizable=yes,scrollbars=yes'
        );
    }
</script>

<script>
    $('#manifest_id').on('change', async function() {
        let id = $(this).val();

        showLoading();
        const data = {
            id
        };
        const url = '{{route('lcl.behandle.data-manifest')}}';
        const response = await globalResponse(data, url);
        hideLoading();
        if (response.ok) {
            const hasil = await response.json();
            if (hasil.success) {
                $('#jenis_spjm').val(hasil.data.jenis_spjm).trigger('change');
                $('#no_spjm').val(hasil.data.no_spjm);
                $('#tgl_spjm').val(hasil.data.tgl_spjm);
                $('#petugas_behandle').val(hasil.data.petugas_behandle);
                $('#date_ready_behandle').val(hasil.data.date_ready_behandle);
                $('#date_check_behandle').val(hasil.data.date_check_behandle);
                $('#desc_check_behandle').val(hasil.data.desc_check_behandle);
                $('#date_finish_behandle').val(hasil.data.date_finish_behandle);
                $('#desc_finish_behandle').val(hasil.data.desc_finish_behandle);
            }else{
                return errorHasil(hasil);
            }
        }else{
            return errorResponse(response);
        }
    });
</script>

<script>
    async function editData(button) {
        const id = button.dataset.id;
        const label = button.dataset.label;
        console.log(id, label);

        const select = $('#manifest_id');

        // Kalau option belum ada, tambahkan
        if (select.find('option[value="' + id + '"]').length === 0) {
            const option = new Option(label, id, true, true);
            select.append(option);
        } else {
            select.val(id);
        }

        // Trigger change supaya async AJAX kamu otomatis jalan
        select.trigger('change');
    }
</script>

<script>
    async function searchSPJM() {
        const result = await confirmation();
        if (result.isConfirmed) {
            showLoading();
            const data = {
                id: document.getElementById('manifest_id').value,
                no_spjm: document.getElementById('no_spjm').value,
                tgl_spjm: document.getElementById('tgl_spjm').value,
                jenis_spjm: document.getElementById('jenis_spjm').value
            };

            const url = "{{ route('lcl.behandle.data-spjm') }}";
            const response = await globalResponse(data, url);
            hideLoading();
            if (response.ok) {
                const hasil = await response.json();
                if (hasil.success) {
                    return successHasil(hasil);
                }else{
                    return errorHasil(hasil);
                }
            }else{
                errorResponse(response);
            }
        }else{
            return;
        }
    }
</script>

<script>
    async function submitBehandle() {
        const result = await confirmation();
        if (result.isConfirmed) {
            showLoading();
            const data = new FormData();

            data.append('id', document.getElementById('manifest_id').value);
            data.append('petugas_behandle', document.getElementById('petugas_behandle').value);
            data.append('date_ready_behandle', document.getElementById('date_ready_behandle').value);
            data.append('date_check_behandle', document.getElementById('date_check_behandle').value);
            data.append('desc_check_behandle', document.getElementById('desc_check_behandle').value);
            data.append('date_finish_behandle', document.getElementById('date_finish_behandle').value);
            data.append('desc_finish_behandle', document.getElementById('desc_finish_behandle').value);
            // data.append('detilPhoto', document.getElementById('detilPhoto').value);

            // const photos = document.getElementById('photos').files;

            // for (let i = 0; i < photos.length; i++) {
            //     data.append('photos[]', photos[i]);
            // }

            const url = "{{ route('lcl.behandle.submit-behandle') }}";
            const response = await globalResponse(data, url);
            hideLoading();
            if (response.ok) {
                const hasil = await response.json();
                if (hasil.success) {
                    return successHasil(hasil);
                }else{
                    return errorHasil(hasil);
                }
            }else{
                errorResponse(response);
            }
        }else{
            return;
        }
    }
</script>
@endsection