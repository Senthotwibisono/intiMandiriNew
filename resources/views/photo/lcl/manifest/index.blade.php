@extends('partial.main')
@section('custom_styles')
<style>
    .highlight-yellow {
        background-color: yellow !important;;
    }
</style>

<style>
    .highlight-blue {
        background-color: blue !important;;
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
            <div style="overflow-x:auto;">
                <table class="tabelCustom">
                    <thead>
                        <tr>
                            <th class="text-center">Action</th>
                            <th class="text-center">No HBL</th>
                            <th class="text-center">Tgl HBL</th>
                            <th class="text-center">No Tally</th>
                            <th class="text-center">Shipper</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Packing</th>
                            <th class="text-center">Kode Kemas</th>
                            <th class="text-center">Stripping</th>
                            <th class="text-center">Behandle</th>
                            <th class="text-center">Gate Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($manifest as $mans)
                            <tr>
                                <td>
                                    <div class="button-container">
                                        <button class="btn btn-warning editButton" data-id="{{$mans->id}}"><i class="fa fa-pencil"></i></button>
                                        <a href="javascript:void(0)" onclick="openWindow('/lcl/report/manifestPhoto{{$mans->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                    </div>
                                </td>
                                <td>{{$mans->nohbl}}</td>
                                <td>{{$mans->tgl_hbl}}</td>
                                <td>{{$mans->notally}}</td>
                                <td>{{$mans->shipperM->name ?? ''}}</td>
                                <td>{{$mans->customer->name ?? ''}}</td>
                                <td>{{$mans->quantity}}</td>
                                <td>{{$mans->packing->name ?? ''}}</td>
                                <td>{{$mans->packing->code ?? ''}}</td>
                                <td>
                                    <a href="javascript:void(0)" onclick="openWindow('/lcl/realisasi/stripping-photoManifest{{$mans->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" onclick="openWindow('/lcl/realisasi/behandle-detail{{$mans->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" onclick="openWindow('/lcl/realisasi/GateOut-detail{{$mans->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-header">
            <h4 class="text-center">Upload Photo From</h4>
        </div>
        <form action="{{route('photo.lcl.storeManifest')}}" id="updateForm" method="post" enctype="multipart/form-data">
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
                        <div class="form-group">
                            <label for="">Kegiatan</label>
                            <select name="action" id="kegiatan" style="width: 100%;" class="js-example-basic-single form-select select2">
                                <option value="stripping">Stripping</option>
                                <option value="behandle">Behandle</option>
                                <option value="gate-out">Gate Out</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Keterangan</label>
                            <select name="detil" id="detilPhoto" style="width:100%;" class="js-example-basic-single select2 form-select">
                                <option disabled selected>Pilih Kegiatan Terlebih Dahulu!</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="photos">Pilih Foto-foto</label>
                            <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-outline-danger" id="cancelButton">Cancel</button>
                <button class="btn btn-outline-success updateButton" id="updateButton">Submit</button>      
            </div>
        </form>
    </div>
</section>
@endsection

@section('custom_js')

<script>
$(document).ready(function(){
    $('#kegiatan').on('change', function(){
        let kegiatan = $(this).val();
        console.log('kegiatan = ' + kegiatan);
        swal.fire({
            title: 'Processing...',
            text: 'Please wait',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
        });
        $.ajax({
            type: 'GET',
            url: '/getManifestLclKeterangan',
            cache: false,
            data: {
              kegiatan: kegiatan
            },
            dataType: 'json',

            success: function(response){
                Swal.close();
                Swal.fire({
                    title: 'success!',
                    text: 'Data di Temukan',
                    icon: 'success',
                    confirmButton: 'Ok',
                })

                console.log(response);
                $('#detilPhoto').empty().append('<option disabled selected>Pilih Satu!</option>');
                    Object.values(response).forEach(function(detil) {
                    $('#detilPhoto').append(`<option value="${detil}">${detil}</option>`);
                });
            },
            error: function(data) {
              console.log('error:', data);
                  Swal.fire({
                      title: 'Error',
                      text: 'Data tidak ditemukan',
                      icon: 'error',
                      confirmButtonText: 'OK'
                  });
            }
        })
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