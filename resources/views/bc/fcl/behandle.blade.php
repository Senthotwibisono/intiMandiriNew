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
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12">
                <h3>{{ $title }}</h3>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-2">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="card-label">Semua Data</div>
                        <div class="card-value" id="totalData">0</div>
                        <!-- <div class="card-line bg-primary"></div> -->
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-2">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="card-label">PPK Belum Siap</div>
                        <div class="card-value" id="statusNull">0</div>
                        <div class="card-line" style="background:#cc0a0a"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-2">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="card-label">PKB</div>
                        <div class="card-value" id="statusPKB">0</div>
                        <div class="card-line bg-primary"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-2">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="card-label">Siap Periksa</div>
                        <div class="card-value" id="status1">0</div>
                        <div class="card-line" style="background:#9df5b1"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-2">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="card-label">Sedang Periksa</div>
                        <div class="card-value" id="status2">0</div>
                        <div class="card-line" style="background:#7a7f86"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-2">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="card-label">Selesai Periksa</div>
                        <div class="card-value" id="status3">0</div>
                        <div class="card-line" style="background:#f5c6cb"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card main-card">
            <div class="card-header">
                <h5>{{ $title }}</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle w-100" id="tableBehandle">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>PKB Action</th>
                                <th>No Container</th>
                                <th>Size</th>
                                <th>Type</th>
                                <th>Type Class</th>
                                <th>Vessel</th>
                                <th>Voy</th>
                                <th>Consignee</th>
                                <th>Consignee Address</th>
                                <th>Consignee NPWP</th>
                                <th>Status Behandle</th>
                                <th>No SPJM</th>
                                <th>Tgl SPJM</th>
                                <th>Tgl Ready Behandle</th>
                                <th>Tgl Mulai Behandle</th>
                                <th>Deskripsi Behandle</th>
                                <th>Tanggal Selesai Behandle</th>
                                <th>Deskripsi Selesai Behandle</th>
                            </tr>

                            <tr id="filter-row">
                                @for ($i = 0; $i < 19; $i++)
                                    <th></th>
                                @endfor
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
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
                url: "{{ route('bc.fcl.dataBehandle') }}",

                dataSrc: function(json) {
                    $('#totalData').text(json.summary.total);
                    $('#statusNull').text(json.summary.ppk);
                    $('#statusPKB').text(json.summary.pkb);
                    $('#status1').text(json.summary.siap);
                    $('#status2').text(json.summary.proses);
                    $('#status3').text(json.summary.selesai);

                    return json.data;
                }
            },

            columns: [
                {
                    className: 'text-center',
                    data: 'photo',
                    name: 'photo',
                    orderable: false,
                    searchable: false
                },
                {
                    className: 'text-center',
                    data: 'pkb',
                    name: 'pkb',
                    orderable: false,
                    searchable: false
                },
                {
                    className: 'text-center',
                    data: 'nocontainer',
                    name: 'nocontainer'
                },
                {
                    className: 'text-center',
                    data: 'size',
                    name: 'size'
                },
                {
                    className: 'text-center',
                    data: 'ctr_type',
                    name: 'ctr_type'
                },
                {
                    className: 'text-center',
                    data: 'type_class',
                    name: 'type_class'
                },
                {
                    className: 'text-center',
                    data: 'job.ves.name',
                    name: 'job.ves.name'
                },
                {
                    className: 'text-center',
                    data: 'job.voy',
                    name: 'job.voy'
                },
                {
                    className: 'text-center',
                    data: 'cust.name',
                    name: 'cust.name'
                },
                {
                    className: 'text-start',
                    data: 'cust.alamat',
                    name: 'cust.alamat',
                    render: function(data) {
                        return `
                            <textarea
                                class="form-control form-control-sm address-field"
                                rows="3"
                                readonly>${data ?? ''}</textarea>
                        `;
                    }
                },
                {
                    className: 'text-center',
                    data: 'cust.npwp',
                    name: 'cust.npwp'
                },
                {
                    className: 'text-center',
                    data: 'status',
                    name: 'status'
                },
                {
                    className: 'text-center',
                    data: 'no_spjm',
                    name: 'no_spjm',
                    defaultContent: '-'
                },
                {
                    className: 'text-center',
                    data: 'tgl_spjm',
                    name: 'tgl_spjm',
                    defaultContent: '-'
                },
                {
                    className: 'text-center',
                    data: 'date_ready_behandle',
                    name: 'date_ready_behandle'
                },
                {
                    className: 'text-center',
                    data: 'date_check_behandle',
                    name: 'date_check_behandle'
                },
                {
                    className: 'text-center',
                    data: 'desc_check_behandle',
                    name: 'desc_check_behandle'
                },
                {
                    className: 'text-center',
                    data: 'date_finish_behandle',
                    name: 'date_finish_behandle'
                },
                {
                    className: 'text-center',
                    data: 'desc_finish_behandle',
                    name: 'desc_finish_behandle'
                }
            ],

            createdRow: function(row, data) {
                $(row).removeClass(
                    'status-pkk status-pkb status-ready status-progress status-finish'
                );

                if (data.status_behandle === null) {
                    if (data.flag_pkb === 'N') {
                        $(row).addClass('status-pkk');
                    } else {
                        $(row).addClass('status-pkb');
                    }

                    return;
                }

                switch (Number(data.status_behandle)) {
                    case 1:
                        $(row).addClass('status-ready');
                        break;

                    case 2:
                        $(row).addClass('status-progress');
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

                    if (index === 11) {
                        $('<select class="form-select form-select-sm">' +
                            '<option value="">All</option>' +
                            '<option value="PKK">PKK</option>' +
                            '<option value="PKB">PKB</option>' +
                            '<option value="1">Ready</option>' +
                            '<option value="2">On Progress</option>' +
                            '<option value="3">Finish</option>' +
                            '</select>')
                            .appendTo(cell.empty())
                            .on('change', function() {
                                column.search($(this).val()).draw();
                            });

                        return;
                    }

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
    async function makePKB(button) {
        const result = await confirmation();
        if (result.isConfirmed) {
            showLoading();
            const data = {
                id: button.dataset.id,
            }

            const url = '{{route('bc.fcl.pkbFlag')}}';
            const response = await globalResponse(data, url);
            hideLoading();
             if (response.ok) {
                const hasil = await response.json();
                if (hasil.success) {
                    successHasil(hasil);
                    $('#tableBehandle').DataTable().ajax.reload();
                }else{
                    errorHasil(hasil);
                }
            }else{
                errorResponse(response);
                return;
            }
        }else{
            return;
        }
    }

    async function cancelPKB(button) {
        const result = await confirmation();
        if (result.isConfirmed) {
            showLoading();
            const data = {
                id: button.dataset.id,
            }

            const url = '{{route('bc.fcl.pkbCancel')}}';
            const response = await globalResponse(data, url);
            hideLoading();
             if (response.ok) {
                const hasil = await response.json();
                if (hasil.success) {
                    successHasil(hasil);
                    $('#tableBehandle').DataTable().ajax.reload();
                }else{
                    errorHasil(hasil);
                }
            }else{
                errorResponse(response);
                return;
            }
        }else{
            return;
        }
    }
</script>
@endsection