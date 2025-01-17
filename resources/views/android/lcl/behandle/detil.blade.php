@extends('partial.android.main')
@section('custom_styles')

@endsection
@section('content')

<section>
    <div class="card">
        <div class="card-header">
            <h4 class="text-center">Checking From</h4>
                <div class="container-button">
                    @if($manifest->no_spjm != null)
                        @if($manifest->status_behandle == 1)
                            <button class="btn btn-outline-primary ReadyChcek" data-id="{{$manifest->id}}">Make It Ready</button>
                        @elseif($manifest->status_behandle == 2)
                            <button class="btn btn-primary FinishBehandle" data-id="{{$manifest->id}}">Finish</button>
                        @endif
                    @endif
                </div>
        </div>
        <form action="{{route('lcl.delivery.updateBehandle')}}" id="updateForm" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row mt-0">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">No Tally</label>
                            <input type="text" name="notally" value="{{$manifest->notally}}" id="notally_edit" class="form-control" readonly>
                            <input type="hidden" name="id" value="{{$manifest->id}}" id="id_edit" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">No HBL</label>
                            <input type="text" name="nohbl" value="{{$manifest->nohbl}}" id="nohbl_edit" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Quantity</label>
                            <input type="text" name="quantity" value="{{$manifest->quantity}}" id="quantity_edit" class="form-control" readonly>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Location Behandle</label>
                                    <select name="location_behandle" value="{{$manifest->location_behandle}}" id="location_behandle_edit" class="js-example-basic-single" style="width: 100%;">
                                        <option disabled selected>Pilih Satu!</option>
                                        @foreach($locs as $loc)
                                            <option value="{{$loc->id}}" {{$manifest->location_behandle == $loc->id ? 'selected' : ''}}>{{$loc->name}}</option>
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
                                    <input type="text" name="no_spjm" value="{{$manifest->no_spjm}}" id="no_spjm_edit" class="form-control">
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group">
                                    <label for="">Tgl SPJM </label>
                                    <input type="date" name="tgl_spjm" value="{{$manifest->tgl_spjm}}" id="tgl_spjm_edit" class="form-control">
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
                                    <input type="date" name="tglbehandle" value="{{$manifest->tglbehandle}}" id="tglbehandle_edit" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Jam Behandle </label>
                                    <input type="time" name="jambehandle" value="{{$manifest->jambehandle}}" id="jambehandle_edit" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal Ready Behandle</label>
                            <input type="datetime-local" name="date_ready_behandle" value="{{$manifest->date_ready_behandle}}" id="date_ready_behandle_edit" class="form-control" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-outline-success updateButton" id="updateButton">Submit</button>      
            </div>
        </form>
    </div>
</section>

@endsection

@section('custom_js')
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
