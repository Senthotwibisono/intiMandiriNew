<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{asset('logo/IntiMandiri.png')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{asset('logo/IntiMandiri.png')}}" type="image/png">
    <!-- Import Bulma -->
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{asset('bulma/css/bulma.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bulma/1.0.1/css/bulma.min.css" rel="stylesheet" integrity="sha384-u1DpPo/VC1cCewPdLA1ujElPdm1c/ZVa5MNAV6930PlrYYXhoKH/+hui6tE7szxu" crossorigin="anonymous">
    <!-- <link href="https://cdn.datatables.net/v/bm/jq-3.7.0/moment-2.29.4/jszip-3.10.1/dt-2.3.8/af-2.7.1/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cr-2.1.2/cc-1.2.1/date-1.6.3/fc-5.0.5/fh-4.0.6/kt-2.12.2/r-3.0.8/rg-1.6.0/rr-1.5.1/sc-2.4.3/sb-1.8.4/sp-2.3.5/sl-3.1.3/sr-1.4.3/datatables.min.css" rel="stylesheet" integrity="sha384-B/uIdJ4/emCTtuwFl+81s/FoV9QcrhvLpFiVnZlI3UJLLBCAXflc9Gqg8NOwYMc+" crossorigin="anonymous"> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bulma/1.0.1/css/bulma.min.css" rel="stylesheet" integrity="sha384-u1DpPo/VC1cCewPdLA1ujElPdm1c/ZVa5MNAV6930PlrYYXhoKH/+hui6tE7szxu" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/v/bm/dt-2.3.8/af-2.7.1/b-3.2.6/cr-2.1.2/cc-1.2.1/date-1.6.3/fc-5.0.5/fh-4.0.6/kt-2.12.2/r-3.0.8/rg-1.6.0/rr-1.5.1/sc-2.4.3/sb-1.8.4/sp-2.3.5/sl-3.1.3/sr-1.4.3/datatables.min.css" rel="stylesheet" integrity="sha384-AgZYjc71/QLXTHeBj+TLiN0B30R4H1uBxToLERps8kSBnRPrT1a/+RNDv2qQOTa0" crossorigin="anonymous">
 
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }       

        .switch input {
            display: none;
        }       

        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: #dbdbdb;
            border-radius: 34px;
            transition: .3s;
        }       

        .slider:before {
            position: absolute;
            content: "";
            width: 20px;
            height: 20px;
            left: 3px;
            bottom: 3px;
            background: white;
            border-radius: 50%;
            transition: .3s;
        }       

        input:checked + .slider {
            background: #3273dc;
        }       

        input:checked + .slider:before {
            transform: translateX(24px);
        }

        .dashboard-card 
        {
            border-radius:12px !important;
            box-shadow:5px 2px 8px 5px rgba(0,0,0,.08) !important;
        }
        
        .card 
        .card-header 
        .card-content{
            border-radius:12px !important;
            box-shadow:5px 2px 8px 5px rgba(0,0,0,.08) !important;
        }

        .card-line{
            height:5px;
            width:100%;
            border-radius:8px;
            margin-top:10px;
        }
/* 
        #tableBehandle_wrapper .dt-scroll {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,.08);
            border: 1px solid #e5e7eb;
        } */

        .table thead tr:first-child th:first-child {
            border-top-left-radius: 16px;
        }

        .table thead tr:first-child th:last-child {
            border-top-right-radius: 16px;
        
        }

        .table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 16px;
        }
        .table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 16px;
        }
        
        /* #tableBehandle_wrapper .dt-scroll-body {
            border-radius: 0 0 16px 16px;
        } */

        .table tbody td {
            font-family: "Plus Jakarta Sans", sans-serif !important;
            /* font-size: 13px;
            font-weight: 500; */
        }

        .table thead th {
            font-family: "Plus Jakarta Sans", sans-serif !important;
            /* font-size: 13px;
            font-weight: 700; */
        }
    </style>

    <title>{{$title}}</title>
</head>
<body>
    <nav class="navbar" role="navigation" aria-label="main navigation">
      <div class="navbar-brand">
        <a class="navbar-item" href="#">
            <svg width="640" height="160" viewBox="0 0 640 160" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M0 110L10 40L50 0L100 50L70 80L110 120L50 160L0 110Z" fill="#d10000"/>
              <!-- <span class="fw-bold fs-4">INTIMANDIRI</span> -->
            </svg>

        </a>

        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
        </a>
      </div>

      <div id="navbarBasicExample" class="navbar-menu">
        <div class="navbar-start">
          <!-- <a class="navbar-item">
            Home
          </a>

          <a class="navbar-item">
            Documentation
          </a> -->

          <!-- <div class="navbar-item has-dropdown is-hoverable">
            <a class="navbar-link">
              More
            </a>

            <div class="navbar-dropdown">
              <a class="navbar-item">
                About
              </a>
              <a class="navbar-item is-selected">
                Jobs
              </a>
              <a class="navbar-item">
                Contact
              </a>
              <hr class="navbar-divider">
              <a class="navbar-item">
                Report an issue
              </a>
            </div>
          </div> -->
        </div>

        <div class="navbar-end">
          <div class="navbar-item">
            <div class="buttons">
                <div class="theme-toggle is-flex is-align-items-center mt-3">
                    <!-- Sun Icon -->
                    <span class="icon mr-2">
                        <i class="fas fa-sun"></i>
                    </span>

                    <!-- Switch -->
                    <label class="switch mx-2">
                        <input type="checkbox" id="toggle-dark">
                        <span class="slider"></span>
                    </label>

                    <!-- Moon Icon -->
                    <span class="icon ml-2">
                        <i class="fas fa-moon"></i>
                    </span>
                </div>
            </div>
          </div>
        </div>
      </div>
    </nav>
    <section class="section">
        <div class="columns is-multiline mb-4">

            <div class="column">
                <div class="box dashboard-card">
                    <p class="has-text-grey">Semua Data</p>
                    <h2 class="title mb-3" id="totalData">0</h2>
                </div>
            </div>

            <div class="column">
                <div class="box dashboard-card">
                    <p class="has-text-grey">PPK Belum Siap</p>
                    <h2 class="title mb-3" id="statusNull">0</h2>
                    <div class="card-line" style="background:#cc0a0a"></div>
                </div>
            </div>

            <div class="column">
                <div class="box dashboard-card">
                    <p class="has-text-grey">PKB</p>
                    <h2 class="title mb-3" id="statusPKB">0</h2>
                    <div class="card-line has-background-link"></div>
                </div>
            </div>

            <div class="column">
                <div class="box dashboard-card">
                    <p class="has-text-grey">Siap Periksa</p>
                    <h2 class="title mb-3" id="status1">0</h2>
                    <div class="card-line" style="background:#9df5b1"></div>
                </div>
            </div>

            <div class="column">
                <div class="box dashboard-card">
                    <p class="has-text-grey">Sedang Periksa</p>
                    <h2 class="title mb-3" id="status2">0</h2>
                    <div class="card-line" style="background:#7a7f86"></div>
                </div>
            </div>

            <div class="column">
                <div class="box dashboard-card">
                    <p class="has-text-grey">Selesai Periksa</p>
                    <h2 class="title mb-3" id="status3">0</h2>
                    <div class="card-line" style="background:#f5c6cb"></div>
                </div>
            </div>

        </div>
        <div class="card">
            <div class="card-header">
                <h1 class="title">
                    {{$title}}
                </h1>
            </div>
            <div class="card-content">
                <div class="table-wrapper">
                  <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth is-primary" id="tableBehandle">
                    <thead>
                      <tr class="is-primary">
                          <th style="white-space: nowrap;" class="has-text-centered">Photo</th>
                          <th style="white-space: nowrap;" class="has-text-centered">No Container</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Size</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Type</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Type Class</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Vessel</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Voy</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Consignee</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Consignee Address</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Consignee NPWP</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Status Behandle</th>
                          <th style="white-space: nowrap;" class="has-text-centered">No SPJM</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Tgl SPJM</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Tgl Ready Behandle</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Tgl Mulai Behandle </th>
                          <th style="white-space: nowrap;" class="has-text-centered">Deskripsi Behandle</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Tanggal Selesai Behandle</th>
                          <th style="white-space: nowrap;" class="has-text-centered">Deskripsi Selesai Behandle</th>
                      </tr>
                        <tr id="filter-row">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                  </table>
                </div>
            </div>
        </div>
    </section>

</body>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script src="{{asset('fontawesome/js/all.js')}}"></script>
    <script src="{{asset('fontawesome/js/all.min.js')}}"></script>

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script> -->
    <!-- <script src="https://cdn.datatables.net/v/bm/jq-3.7.0/moment-2.29.4/jszip-3.10.1/dt-2.3.8/af-2.7.1/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cr-2.1.2/cc-1.2.1/date-1.6.3/fc-5.0.5/fh-4.0.6/kt-2.12.2/r-3.0.8/rg-1.6.0/rr-1.5.1/sc-2.4.3/sb-1.8.4/sp-2.3.5/sl-3.1.3/sr-1.4.3/datatables.min.js" integrity="sha384-3JvK1IEpe2+JBJuiXODRigrsZ0jM3IxX8VaTb23Q8hLb4Jti85kzDpxkwao/fQjY" crossorigin="anonymous"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/bm/jszip-3.10.1/dt-2.3.8/af-2.7.1/b-3.2.6/b-colvis-3.2.6/b-html5-3.2.6/b-print-3.2.6/cr-2.1.2/cc-1.2.1/date-1.6.3/fc-5.0.5/fh-4.0.6/kt-2.12.2/r-3.0.8/rg-1.6.0/rr-1.5.1/sc-2.4.3/sb-1.8.4/sp-2.3.5/sl-3.1.3/sr-1.4.3/datatables.min.js" integrity="sha384-Wg8mIRRkQRSWPky/YkHODkSArbmkuUicyI04zA5aeHsdlmC6L+M6XIqoao0OwvHP" crossorigin="anonymous"></script>

    <script>
    console.log('jQuery =', $.fn.jquery);
    console.log('Select2 =', $.fn.select2);
    </script>
    <script>
        let excel = {
                        extend: 'excelHtml5',
                        autoFilter: true,
                        sheetName: 'Exported data',
                        className: 'btn btn-outline-success',
                    };
        let pdf = {
                    extend: 'pdfHtml5',
                    text: 'Ekspor PDF',
                    className: 'btn btn-outline-danger',
                    orientation: 'landscape', // Mode lanskap untuk tampilan lebih luas
                    pageSize: 'A1', // Pilihan ukuran kertas (bisa A3, A4, A5, dll.)
                    download: 'open', // Membuka file langsung tanpa mendownload
                    exportOptions: {
                        columns: function (idx, data, node) {
                            return true; // Semua kolom akan diekspor, termasuk yang tersembunyi
                        }
                    },
                    customize: function (doc) {
                        doc.defaultStyle.fontSize = 8; // Mengatur ukuran font agar semua data muat
                        doc.styles.tableHeader.fontSize = 8; // Mengatur ukuran header tabel
                        doc.styles.title.fontSize = 12; // Ukuran font judul
                        doc.pageMargins = [2, 2, 2, 2]; // Mengatur margin halaman
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split(''); 
                    }
                };
        $(document).ready(function(){
            $('#tableBehandle').dataTable({
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], // Pilihan jumlah data
                pageLength: 25, // Default jumlah data per halaman
                layout: {
                    topStart: [
                        'pageLength',
                        'buttons'
                    ],
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                buttons: [
                    'copy', 'csv', excel , pdf, 'print'
                ],
                orderCellsTop: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('layanan.behandle.dataFCL') }}",
                    dataSrc: function (json) {
                
                        $('#totalData').text(json.summary.total);
                        $('#statusNull').text(json.summary.ppk);
                        $('#statusPKB').text(json.summary.pkb);
                        $('#status1').text(json.summary.siap);
                        $('#status2').text(json.summary.proses);
                        $('#status3').text(json.summary.selesai);
                
                        return json.data;
                    }
                },
                scrollX: true,
                scrollY: '50vh',
                columns: [
                    {className:'has-text-centered', data:'photo', name:'photo', orderable:false, sortable: false},
                    {className:'has-text-centered', data:'nocontainer', name:'nocontainer'},
                    {className:'has-text-centered', data:'size', name:'size'},
                    {className:'has-text-centered', data:'ctr_type', name:'ctr_type'},
                    {className:'has-text-centered', data:'type_class', name:'type_class'},
                    {className:'has-text-centered', data:'job.ves.name', name:'job.ves.name'},
                    {className:'has-text-centered', data:'job.voy', name:'job.voy'},
                    {className:'has-text-centered', data:'cust.name', name:'cust.name'},
                    {
                        className: 'has-text-left',
                        data: 'cust.alamat',
                        name: 'cust.alamat',
                        render: function(data) {
                            return `<textarea
                                        class="textarea is-small"
                                        rows="3"
                                        readonly
                                        style=""
                                    >${data ?? ''}</textarea>`;
                        }
                    },
                    {className:'has-text-centered', data:'cust.npwp', name:'cust.npwp'},
                    {className:'has-text-centered', data:'status', name:'status'},
                    {className:'has-text-centered', data:'no_spjm', name:'no_spjm',  defaultContent: '-'},
                    {className:'has-text-centered', data:'tgl_spjm', name:'tgl_spjm',  defaultContent: '-'},
                    {className:'has-text-centered', data:'date_ready_behandle', name:'date_ready_behandle'},
                    {className:'has-text-centered', data:'date_check_behandle', name:'date_check_behandle'},
                    {className:'has-text-centered', data:'desc_check_behandle', name:'desc_check_behandle'},
                    {className:'has-text-centered', data:'date_finish_behandle', name:'date_finish_behandle'},
                    {className:'has-text-centered', data:'desc_finish_behandle', name:'desc_finish_behandle'},
                ],
                createdRow: function(row, data) {
                    if (data.status_behandle === null) {
                        if (data.flag_pkb === 'N') {
                            $(row).find('td').attr(
                                'style',
                                'background-color:#cc0a0a !important; color:#ffffff !important;'
                            );
                            return;
                        } else {
                            $(row).find('td').attr(
                                'style',
                                'background-color: #3273dc !important; color:#ffffff !important;'
                            );
                            return;

                        }
                    }               
                    switch (Number(data.status_behandle)) {
                        case 1:
                            $(row).css({
                                'background-color': '#9df5b1',
                                'color': '#155724'
                            });
                            break;              

                        case 2:
                            $(row).css({
                                'background-color': '#7a7f86',
                                'color': '#242525'
                            });
                            break;              

                        case 3:
                            $(row).css({
                                'background-color': '#f5c6cb',
                                'color': '#721c24'
                            });
                            break;
                    }
                },
                initComplete: function () {             

                    var api = this.api();               
                        
                    api.columns().every(function(index) {               
                        if (index === 0) {
                            return;
                        }
                        var column = this;
                        var cell = $('#filter-row th').eq(index);               

                        if (index == 10) {               

                            $('<select class="input is-small">' +
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

                        $('<input type="text" class="input is-small">')
                            .appendTo(cell.empty())
                            .on('keyup change', function() {
                                column.search($(this).val()).draw();
                            });
                    });
                }
            })
        })

        $('#tableBehandle').on('init.dt', function () {
            setTimeout(function () {
                $('#tableBehandle').DataTable().columns.adjust();
            }, 100);
        });
    </script>
    <script>
        const toggle = document.getElementById('toggle-dark');
        const html = document.documentElement;      

        toggle.addEventListener('change', function() {
            const theme = this.checked ? 'dark' : 'light';      

            html.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
        });     

        const savedTheme = localStorage.getItem('theme') || 'light';        

        html.setAttribute('data-theme', savedTheme);
        toggle.checked = savedTheme === 'dark';

        function openPhoto(id) {
            window.open(
                '/fcl/behandle-detail/' + id,
                'behandlePhoto',
                'width=900,height=700,left=200,top=100,resizable=yes,scrollbars=yes'
            );
        }
    </script>

    <script>
        const scrollBody = document.querySelector('.dt-scroll-body');
        const headers = document.querySelectorAll('.dt-scroll-head th');

        function updateRadius() {

            headers.forEach(th => {
                th.classList.remove('radius-left', 'radius-right');
            });

            const scrollLeft = scrollBody.scrollLeft;
            const width = scrollBody.clientWidth;

            headers.forEach((th, i) => {

                const left = th.offsetLeft;
                const right = left + th.offsetWidth;

                if (left >= scrollLeft) {
                    th.classList.add('radius-left');
                    return false;
                }

            });

            for (let i = headers.length - 1; i >= 0; i--) {

                const th = headers[i];

                const left = th.offsetLeft;

                if (left < scrollLeft + width) {
                    th.classList.add('radius-right');
                    break;
                }
            }
        }

        scrollBody.addEventListener('scroll', updateRadius);

        updateRadius();
    </script>

   
</html>