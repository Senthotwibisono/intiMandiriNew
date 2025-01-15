@extends('partial.main')
@section('custom_styles')
<style>
    .highlight-yellow {
        background-color: lightyellow !important;
    }
</style>

<style>
    .highlight-blue {
        background-color: lightblue !important;
        color: white;
    }
    .highlight-green {
        background-color: lightgreen !important;
        color: white;
    }
</style>
@endsection
@section('content')
<section>
    <div class="card">
        <!-- <div class="card-header">
            <div class="row">
                <div class="col-auto ms-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual"><i class="fas fa-plus"></i></button>
                </div>
                <div class="col-auto ms-2">
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#excelModal"><i class="fas fa-file-excel"></i></button>
                </div>
            </div>
        </div> -->
        <div class="card-body">
            <div class="table">
                <div class="mb-3">
                    <label for="statusFilter" class="form-label">Filter by Status Behandle:</label>
                    <select id="filterBehandle" class="form-select">
                        <option value="all" selected>Show All</option>
                        <option value="behandled">Behandeled</option>
                        <option value="ready">Ready</option>
                        <option value="proses">On Progress</option>
                        <option value="finish">Finish</option>
                    </select>
                </div>
                <table class="table-hover" id="tableBehandle">
                    <thead>
                        <tr>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Detil</th>
                            <th class="text-center">Behandle Button</th>
                            <th class="text-center">Status Behandle</th>
                            <th class="text-center">No HBL</th>
                            <th class="text-center">Tgl HBL</th>
                            <th class="text-center">No Tally</th>
                            <th class="text-center">Shipper</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Qty Real Time</th>
                            <th class="text-center">Packing</th>
                            <th class="text-center">Kode Kemas</th>
                            <th class="text-center">Desc</th>
                            <th class="text-center">Weight</th>
                            <th class="text-center">Meas</th>
                            <th class="text-center">Packing Tally</th>
                            <th class="text-center">Dok SPJM</th>
                            <th class="text-center">Tgl SPJM</th>
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
            <h4 class="text-center">Checking From</h4>
        </div>
        <form action="{{route('lcl.delivery.updateBehandle')}}" id="updateForm" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row mt-0">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">No Tally</label>
                            <input type="text" name="notally" id="notally_edit" class="form-control" readonly>
                            <input type="hidden" name="id" id="id_edit" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">No HBL</label>
                            <input type="text" name="nohbl" id="nohbl_edit" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Quantity</label>
                            <input type="text" name="quantity" id="quantity_edit" class="form-control" readonly>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Location Behandle</label>
                                    <select name="location_behandle" id="location_behandle_edit" class="js-example-basic-single" style="width: 100%;">
                                        <option disabled selected>Pilih Satu!</option>
                                        @foreach($locs as $loc)
                                            <option value="{{$loc->id}}">{{$loc->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Tier</label>
                                        <select name="tier" class="form-select" id="tier" required>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-5">
                                <div class="form-group">
                                    <label for="">No SPJM</label>
                                    <input type="text" name="no_spjm" id="no_spjm_edit" class="form-control">
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group">
                                    <label for="">Tgl SPJM </label>
                                    <input type="date" name="tgl_spjm" id="tgl_spjm_edit" class="form-control">
                                </div>
                            </div>
                            <div class="col-2">
                                <br>
                                <button class="btn btn-outline-info CheckSPJMDok" type="button">Check</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Tgl Behandle</label>
                                    <input type="date" name="tglbehandle" id="tglbehandle_edit" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Jam Behandle </label>
                                    <input type="time" name="jambehandle" id="jambehandle_edit" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal Ready Behandle</label>
                            <input type="datetime-local" name="date_ready_behandle" id="date_ready_behandle_edit" class="form-control" readonly>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="photos">Pilih Foto-foto</label>
                                    <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Keterangan Photo</label>
                                    <select name="keteranganPhoto" class="js-example-basic-single form-select select2" style="width: 100%;">
                                        <option disabled selected value>Pilih Satu!</option>
                                        @foreach($kets as $ket)
                                            <option value="{{$ket->keterangan}}">{{$ket->keterangan}}</option>
                                        @endforeach
                                    </select>
                                </div>
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
    $(document).ready(function(){
        var filter = $('#filterBehandle').val();
        // console.log('filter Behandle' + filter);
        var table = $('#tableBehandle').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/lcl/delivery/behandle/behandleData',
                type: 'GET',
                data: function (d) {
                    // Add the filter value to the request
                    d.filter = $('#filterBehandle').val();
                }
            },
            scrollX: true,
            columns:[
                { data:'edit', name:'edit', className:'text-center' },
                { data:'detil', name:'detil', className:'text-center' },
                { data:'behandleButton', name:'behandleButton', className:'text-center' },
                { data:'statusBehandle', name:'statusBehandle', className:'text-center' },
                { data:'nohbl', name:'nohbl', className:'text-center' },
                { data:'tgl_hbl', name:'tgl_hbl', className:'text-center' },
                { data:'notally', name:'notally', className:'text-center' },
                { data:'shipper', name:'shipper', className:'text-center' },
                { data:'customer', name:'customer', className:'text-center' },
                { data:'quantity', name:'quantity', className:'text-center' },
                { data:'final_qty', name:'final_qty', className:'text-center' },
                { data:'packingName', name:'packingName', className:'text-center' },
                { data:'packingCode', name:'packingCode', className:'text-center' },
                { data:'desc', name:'desc', className:'text-center' },
                { data:'weight', name:'weight', className:'text-center' },
                { data:'meas', name:'meas', className:'text-center' },
                { data:'packingTally', name:'packingTally', className:'text-center' },
                { data:'noSPJM', name:'noSPJM', className:'text-center' },
                { data:'tglSPJM', name:'tglSPJM', className:'text-center' },
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.highlight === 'highlight-yellow') {
                    $(row).addClass('highlight-yellow');
                } else if (data.highlight === 'highlight-blue') {
                    $(row).addClass('highlight-blue');
                } else if (data.highlight === 'highlight-green') {
                    $(row).addClass('highlight-green');
                }
            }
        })
        $('#filterBehandle').on('change', function () {
            console.log('Filter Behandle changed to: ' + $(this).val());
            table.ajax.reload(); // Reload the table data
        });
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
      url: '/lcl/manifest/edit-' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $("#id_edit").val(response.data.id);
        $("#nohbl_edit").val(response.data.nohbl);
        $("#notally_edit").val(response.data.notally);
        $("#quantity_edit").val(response.data.quantity);
        $("#no_spjm_edit").val(response.data.no_spjm);
        $("#tgl_spjm_edit").val(response.data.tgl_spjm);
        $("#tglbehandle_edit").val(response.data.tglbehandle);
        $("#jambehandle_edit").val(response.data.jambehandle);
        $("#date_ready_behandle_edit").val(response.data.date_ready_behandle);
        $("#location_behandle_edit").val(response.data.location_behandle).trigger('change');
      },
      error: function(data) {
        console.log('error:', data)
      }
    });
  });
</script>

<script>
$(document).on('click', '.CheckSPJMDok', function() {
    var data = {
          'id' : $('#id_edit').val(),
          'no_spjm' : $('#no_spjm_edit').val(),
          'tgl_spjm' : $('#tgl_spjm_edit').val(),
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
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we update the container',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            $.ajax({
                type: 'POST',
                url: '/lcl/delivery/behandle/spjmCheck',
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
                            // location.reload();
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

<script>
$(document).on('click', '.ReadyChcek', function() {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah anda yakin, proses behandle akan dilakukan?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we update the container',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            $.ajax({
                type: 'POST',
                url: '/lcl/delivery/behandle/readyCheck-' + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        Swal.fire({
                            title: 'Sukses!',
                            text: response.message,
                            icon: 'success',
                        }).then(() => {
                            location.reload(); // Reload halaman setelah sukses
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                        });
                    }
                },
                error: function(data) {
                    console.log('error:', data);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus data.',
                        icon: 'error',
                    });
                }
            });
        }
    });
});
</script>

<script>
$(document).on('click', '.FinishBehandle', function() {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah anda yakin?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we update the container',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            $.ajax({
                type: 'POST',
                url: '/lcl/delivery/behandle/finishCheck-' + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        Swal.fire({
                            title: 'Sukses!',
                            text: response.message,
                            icon: 'success',
                        }).then(() => {
                            location.reload(); // Reload halaman setelah sukses
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                        });
                    }
                },
                error: function(data) {
                    console.log('error:', data);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus data.',
                        icon: 'error',
                    });
                }
            });
        }
    });
});
</script>

<script>
$(document).on('click', '.unapproveButton', function() {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin membatalkan approve?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '/lcl/manifest/unapprove-' + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        Swal.fire({
                            title: 'Sukses!',
                            text: response.message,
                            icon: 'success',
                        }).then(() => {
                            location.reload(); // Reload halaman setelah sukses
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                        });
                    }
                },
                error: function(data) {
                    console.log('error:', data);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus data.',
                        icon: 'error',
                    });
                }
            });
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
                text: "Data detail barang akan reset ketika Quantity berubah Value",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we update the container',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                    document.getElementById('updateForm').submit();
                }
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('endButton').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Halaman ini tidak bisa di buka kembali ketika anda mengakhiri sesi ini",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form programmatically if confirmed
                    document.getElementById('endForm').submit();
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

@endsection