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
            <div class="table">
                <table class="table-hover table-stripped" id="tableGateOut">
                    <thead>
                        <tr>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Detil</th>
                            <th class="text-center">Barcode</th>
                            <th class="text-center">Status BC</th>
                            <th class="text-center">No HBL</th>
                            <th class="text-center">Tgl HBL</th>
                            <th class="text-center">No Tally</th>
                            <th class="text-center">Shipper</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Qty Real Time</th>
                            <th class="text-center">Packing</th>
                            <th class="text-center">Kode Kemas</th>
                            <th class="text-center">Desc of Goods</th>
                            <th class="text-center">Weight</th>
                            <th class="text-center">Meas</th>
                            <th class="text-center">Packing Tally</th>
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
            <h4 class="text-center">Gate Out From</h4>
        </div>
        <form action="{{route('lcl.delivery.updateGateOut')}}" id="updateForm" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row mt-1">
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
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="">Kode Dokumen</label>
                                    <select name="kd_dok" id="kd_dok_edit" style="width: 100%; " class="js-example-basic-single form-select select2">
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
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">No Polisi Release</label>
                                <input type="text" class="form-control" name="nopol_release" id="nopol_release_edit">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Tgl Keluar</label>
                                    <input type="date" name="tglbuangmty" id="tglbuangmty_edit" class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Jam Keluar </label>
                                    <input type="time" name="jambuangmty" id="jambuangmty_edit" class="form-control">
                                </div>
                            </div>
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
        $('#tableGateOut').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/lcl/delivery/dataGateOut',
            scrollX: true,
            columns: [
                { data: 'edit', name: 'edit', className: 'text-center' },
                { data: 'detail', name: 'detail', className: 'text-center' },
                { data: 'barcode', name: 'barcode', className: 'text-center' },
                { data: 'status_bc', name: 'status_bc', className: 'text-center' },
                { data: 'nohbl', name: 'nohbl', className: 'text-center' },
                { data: 'tgl_hbl', name: 'tgl_hbl', className: 'text-center' },
                { data: 'notally', name: 'notally', className: 'text-center' },
                { data: 'shipper', name: 'shipper', className: 'text-center' },
                { data: 'customer', name: 'customer', className: 'text-center' },
                { data: 'quantity', name: 'quantity', className: 'text-center' },
                { data: 'final_qty', name: 'final_qty', className: 'text-center' },
                { data: 'packingName', name: 'packingName', className: 'text-center' },
                { data: 'packingCode', name: 'packingCode', className: 'text-center' },
                { data: 'desc', name: 'desc', className: 'text-center' },
                { data: 'weight', name: 'weight', className: 'text-center' },
                { data: 'meas', name: 'meas', className: 'text-center' },
                { data: 'packingTally', name: 'packingTally', className: 'text-center' },
                { data: 'dokumen', name: 'dokumen', className: 'text-center' },
                { data: 'no_dok', name: 'no_dok', className: 'text-center' },
                { data: 'tglDok', name: 'tglDok', className: 'text-center' },
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.highlight === 'highlight-yellow') {
                    $(row).addClass('highlight-yellow');
                } else if (data.highlight === 'highlight-red text-white') {
                    $(row).addClass('highlight-red text-white');
                }
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
                    url: '/lcl/delivery/gateOut-barcodeGate',
                    data: { id: containerId },
                    cache: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Generated!', '', 'success')
                                .then(() => {
                                    var barcodeId = response.data.id;
                                    window.open('/barcode/autoGate-indexManifest' + barcodeId, '_blank', 'width=600,height=800');
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
        $("#no_dok_edit").val(response.data.no_dok);
        $("#tgl_dok_edit").val(response.data.tgl_dok);
        $("#kd_dok_edit").val(response.data.kd_dok_inout).trigger('change');
        $("#tglbuangmty_edit").val(response.data.tglbuangmty);
        $("#jambuangmty_edit").val(response.data.jambuangmty);
        $("#nopol_release_edit").val(response.data.nopol_release);
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
                url: '/lcl/delivery/gateOut/check',
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
        text: 'Apakah Anda yakin ingin aprrove?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
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
                    // Submit the form programmatically if confirmed
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