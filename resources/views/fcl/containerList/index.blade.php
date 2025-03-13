@extends('partial.main')
@section('custom_styles')
<style>
    .highlight-yellow {
        background-color: yellow !important;;
    }
</style>

<style>
    .highlight-blue {
        background-color: lightblue !important;;
    }
</style>

<style>
    .highlight-red {
        background-color: red !important;;
    }
</style>
@endsection
@section('content')
<section>
    <div class="card">
        <div class="card-body">
            <br>
            <div class="table">
                <table class="table table-hover table-stripped" id="tableContainer" style="white-space: nowrap;">  
                    <thead>
                        <tr>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Photo</th>
                            <th class="text-center">Status Beacukai</th>
                            <th class="text-center">Segel Merah</th>
                            <th class="text-center">No Job Order</th>
                            <th class="text-center">No MBL</th>
                            <th class="text-center">No Container</th>
                            <th class="text-center" style="min-width: 100px;">Size</th>
                            <th class="text-center">Container Type</th>
                            <th class="text-center">Type Class</th>
                            <th class="text-center">No BL AWB</th>
                            <th class="text-center">Tgl BL AWB</th>
                            <th class="text-center">Customer Name</th>
                            <th class="text-center">Customer NPWP</th>
                            <th class="text-center">Customer Email</th>
                            <th class="text-center">No Polisi Masuk</th>
                            <th class="text-center">Tgl Masuk</th>
                            <th class="text-center">Jam Masuk</th>
                            <th class="text-center">No Polisi Keluar</th>
                            <th class="text-center">Tgl Keluar</th>
                            <th class="text-center">Jam Keluar</th>
                            <th class="text-center">Jenis Dok</th>
                            <th class="text-center">No Dok</th>
                            <th class="text-center">Tgl Dok</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="card">
        <div class="card-header">
        </div>
        <form action="{{ route('fcl.containerList.update') }}" id="updateForm" method="post" enctype="multipart/form-data">
            <div class="card-body">
                @csrf
                <div class="row mt-2">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">No SPK</label>
                            <input type="text" name="nospk" id="nospk" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Container</label>
                            <input type="text" name="nocontainer" id="nocontainer" class="form-control" readonly>
                            <input type="hidden" name="id" id="id" class="form-control" readonly>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Size</label>
                                    <input type="text" name="size" id="size" class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Weight</label>
                                    <input type="text" name="weight" id="weight" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Tgl Masuk</label>
                                    <input type="date" class="form-control" name="tglmasuk" id="tglmasuk" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="for-group">
                                    <label for="">Jam Masuk</label>
                                    <input type="time" class="form-control" name="jammasuk" id="jammasuk" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Ctr Type</label>
                                <select name="ctr_type" id="ctr_type_edit" type="width:100%;" class="js-example-basic-single form-select select2">
                                     <option disabled selected value>Pilih Satu</option>
                                     <option value="DRY">DRY</option>
                                     <option value="BB">BB</option>
                                     <option value="OH">OH</option>
                                     <!-- <option value="OPEN TOP">OPEN TOP</option> -->
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Jenis Container</label>
                                <select class="js-example-basic-single form-control select2" name="type_class" id="type_class_edit" style="width: 100%;" required>
                                    <option disabled selected value>Choose Jenis Container</option>
                                    <option value="Class BB Standar 3">Class BB Standar 3</option>
                                    <option value="Class BB Standar 8">Class BB Standar 8</option>
                                    <option value="Class BB Standar 9">Class BB Standar 9</option>
                                    <option value="Class BB Standar 4,1">Class BB Standar 4,1</option>
                                    <option value="Class BB Standar 4,2">Class BB Standar 4,2</option>   
				            		<option value="Class BB Standar 4,3">Class BB Standar 4,3</option>   
                                    <option value="Class BB Standar 6">Class BB Standar 6</option>
                                    <option value="Class BB Standar 2,2">Class BB Standar 2,2</option>
                                    <option value="Class BB Standar 2,3">Class BB Standar 2,3</option>    
                                    <option value="Class BB High Class 2,1">Class BB High Class 2,1</option>
                                    <option value="Class BB High Class 5,1">Class BB High Class 5,1</option>
                                    <option value="Class BB High Class 6,1">Class BB High Class 6,1</option>
                                    <option value="Class BB High Class 5,2">Class BB High Class 5,2</option>
                                    <option value="REEFER RF">REEFER RF</option>
                                    <option value="REEFER RECOOLING">REEFER RECOOLING</option>
				            		<option value="REEFER RECOOLING BB 3">REEFER RECOOLING BB 3</option>
				            		<option value="REEFER RECOOLING BB 8">REEFER RECOOLING BB 8</option>                           
				            		<option value="REEFER RECOOLING BB 6">REEFER RECOOLING BB 6</option>\		
				            		<option value="REEFER RECOOLING BB 9">REEFER RECOOLING BB 9</option>
				            		<option value="REEFER RECOOLING BB 2.1">REEFER RECOOLING BB 2.1</option>
				            		<option value="REEFER RECOOLING BB 2.2">REEFER RECOOLING BB 2.2</option>
				            		<option value="REEFER RECOOLING BB 2.3">REEFER RECOOLING BB 2.3</option>			
				            		<option value="REEFER RECOOLING BB 4.1">REEFER RECOOLING BB 4.1</option>
				            		<option value="REEFER RECOOLING BB 4.2">REEFER RECOOLING BB 4.2</option>
                                    <option value="REEFER RECOOLING BB 5.1">REEFER RECOOLING BB 5.1</option>
                                    <option value="REEFER RECOOLING BB 5.2">REEFER RECOOLING BB 5.2</option>
                                    <option value="REEFER RECOOLING BB 6.1">REEFER RECOOLING BB 6.1</option>
				            		<option value="FLAT TRACK RF">FLAT TRACK RF</option>
                                    <option value="FLAT TRACK OH">FLAT TRACK OH</option>
                                    <option value="FLAT TRACK OW">FLAT TRACK OW</option>
                                    <option value="FLAT TRACK OL">FLAT TRACK OL</option>
                                    <option value="DRY">DRY</option>
                                    <option value="OPEN TOP">OPEN TOP</option>
				            		<option value="OH">OH</option>
                                 </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="">Kode Dokumen</label>
                                    <select name="kd_dok_inout" id="kd_dok_edit" style="width: 100%; " class="js-example-basic-single form-select select2">
                                        <option value disabled selected>Pilih Satu</option>
                                        @foreach($doks as $dok)
                                            <option value="{{$dok->kode}}">{{$dok->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="">No Dok</label>
                                    <input type="text" name="no_dok" id="no_dok_edit" class="form-control">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="">Tgl Dok </label>
                                    <input type="date" name="tgl_dok" id="tgl_dok_edit" class="form-control">
                                </div>
                            </div>
                            <div class="col-2">
                                <br>
                                <button class="btn btn-outline-info CheckSPJMDok" type="button">Check</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-outline-danger" id="cancelButton">Cancel</button>
                <button type="button" class="btn btn-outline-success updateButton" id="updateButton">Submit</button>        
            </div>
        </form>
    </div>
</section>
@endsection

@section('custom_js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('updateButton').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Pastikan Data yang Anda Masukkan sudah Benar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    document.getElementById('updateForm').submit();
                }
            });
        });
    });
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
        $('#tableContainer').dataTable({
            dom: 'Bfrtip', // Pastikan ada 'B' untuk menampilkan tombol
            buttons: [
                'copy', 'csv', excel , pdf, 'print'
            ],
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: '/fcl/containerList/dataTable',
            columns: [
                {className:'text-center', data:'edit', name:'edit'},
                {className:'text-center', data:'photo', name:'photo'},
                {className:'text-center', data:'status_bc', name:'status_bc'},
                {className:'text-center', data:'flag_segel_merah', name:'flag_segel_merah'},
                {className:'text-center', data:'nojob', name:'nojob'},
                {className:'text-center', data:'nombl', name:'nombl'},
                {className:'text-center', data:'nocontainer', name:'nocontainer'},
                {className:'text-center', data:'size', name:'size'},
                {className:'text-center', data:'ctr_type', name:'ctr_type'},
                {className:'text-center', data:'type_class', name:'type_class'},
                {className:'text-center', data:'nobl', name:'nobl'},
                {className:'text-center', data:'tglBL', name:'tglBL'},
                {className:'text-center', data:'customer', name:'customer'},
                {className:'text-center', data:'npwp', name:'npwp'},
                {className:'text-center', data:'email', name:'email'},
                {className:'text-center', data:'nopol', name:'nopol'},
                {className:'text-center', data:'tglmasuk', name:'tglmasuk'},
                {className:'text-center', data:'jammasuk', name:'jammasuk'},
                {className:'text-center', data:'nopol_mty', name:'nopol_mty'},
                {className:'text-center', data:'tglkeluar', name:'tglkeluar'},
                {className:'text-center', data:'jamkeluar', name:'jamkeluar'},
                {className:'text-center', data:'kodeDok', name:'kodeDok'},
                {className:'text-center', data:'noDok', name:'noDok'},
                {className:'text-center', data:'tglDok', name:'tglDok'},
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.flag_segel_merah === 'Y') {
                    $(row).addClass('highlight-red text-white');
                } else if (data.status_bc === 'HOLD') {
                    $(row).addClass('highlight-yellow');
                } else if (data.status_bc === 'release'){
                    $(row).addClass('highlight-blue');
                }
            },
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
            }
        })
    })
</script>

<script>
$(document).ready(function() {
    // When Cancel button is clicked
    $('#cancelButton').click(function() {
        // Reload the current page
        location.reload();
    });
});
</script>

<script>
   $(document).on('click', '.editButton', function() {
    let id = $(this).data('id');
    $.ajax({
      type: 'GET',
      url: '/fcl/realisasi/placementEdit-' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $("#nospk").val(response.job.nospk);
        $("#nocontainer").val(response.data.nocontainer);
        $("#id").val(response.data.id);
        $("#size").val(response.data.size);
        $("#weight").val(response.data.weight);
        $("#tglmasuk").val(response.data.tglmasuk);
        $("#jammasuk").val(response.data.jammasuk);
        $("#ctr_type_edit").val(response.data.ctr_type).trigger('change');
        $("#type_class_edit").val(response.data.type_class).trigger('change');
        $("#kd_dok_edit").val(response.data.kd_dok_inout).trigger('change');
        $("#no_dok_edit").val(response.data.no_dok);
        $("#tgl_dok_edit").val(response.data.tgl_dok);
      },
      error: function(data) {
        console.log('error:', data)
      }
    });
  });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('updateButton').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form programmatically if confirmed
                    document.getElementById('updateForm').submit();
                }
            });
        });
    });
</script>

<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=600,height=800');
    }
</script>

<script>
    $(document).on('click', '.printBarcode', function(e) {
        e.preventDefault();
        var containerId = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        Swal.fire({
            icon: 'question',
            title: 'Do you want to generate the barcode?',
            showCancelButton: true,
            confirmButtonText: 'Generate',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '/lcl/realisasi/mty-barcodeGate',
                    data: { id: containerId },
                    cache: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Generated!', '', 'success')
                                .then(() => {
                                    var barcodeId = response.data.id;
                                    window.open('/barcode/autoGate-index' + barcodeId, '_blank', 'width=600,height=800');
                                });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(response) {
                        var errors = response.responseJSON.errors;
                        if (errors) {
                            var errorMessage = '';
                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: errorMessage,
                            });
                        } else {
                            Swal.fire('Error', 'An error occurred while processing your request', 'error');
                        }
                    },
                });
            }
        });
    });
</script>

<script>
$(document).on('click', '.CheckSPJMDok', function() {
    var data = {
          'id' : $('#id_edit').val(),
          'kd_dok' : $('#kd_dok_edit').val(),
          'no_dok' : $('#no_dok_edit').val(),
          'tgl_dok' : $('#tgl_dok_edit').val(),
        }
    Swal.fire({
        title: 'Konfirmasi',
        text: '',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '/fcl/containerList/dataDok',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                data: data,
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        Swal.fire({
                            title: 'Sukses!',
                            text: response.message,
                            icon: 'success',
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(data) {
                    console.log('error:', data);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus data.',
                        icon: 'error',
                    }).then(() => {
                            location.reload();
                        });
                }
            });
        }
    });
});
</script>
@endsection