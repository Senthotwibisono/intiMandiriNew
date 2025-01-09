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
            <div style="overflow-x:auto;">
                <table class="tabelCustom table-hover table-stripped">
                    <thead>
                        <tr>
                            <th class="text-center">Action</th>
                            <th class="text-center">Status Behandle</th>
                            <th class="text-center">No Job Order</th>
                            <th class="text-center">No MBL</th>
                            <th class="text-center">Tgl MBL</th>
                            <th class="text-center">No Container</th>
                            <th class="text-center">Size</th>
                            <th class="text-center">Teus</th>
                            <th class="text-center">No PLP</th>
                            <th class="text-center">Tgl PLP</th>
                            <th class="text-center">Tgl Ready Behandle</th>
                            <th class="text-center">Tgl Mulai Behandle </th>
                            <th class="text-center">Deskripsi Behandle</th>
                            <th class="text-center">Tanggal Selesai Behandle</th>
                            <th class="text-center">Deskripsi Selesai Behandle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($containers as $mans)
                            <tr class="{{ $mans->status_behandle == 1 ? 'highlight-yellow' : ($mans->status_behandle == 3 ? 'highlight-blue' : ($mans->status_behandle == 2 ? 'highlight-green' : '')) }}">
                                <td>
                                    <div class="button-container">
                                        <button class="btn btn-warning editButton" data-id="{{$mans->id}}"><i class="fa fa-pencil"></i></button>
                                        <a href="javascript:void(0)" onclick="openWindow('/fcl/delivery/behandleDetil{{$mans->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                            @if($mans->status_behandle == 1)
                                                <button class="btn btn-outline-primary checkProses" data-id="{{$mans->id}}">Checking Proses</button>
                                            @elseif($mans->status_behandle == 2)
                                                @if(Auth::check() && Auth::user()->hasRole('bc'))
                                                <button class="btn btn-primary FinishBehandle" data-id="{{$mans->id}}">Finish</button>
                                                @endif
                                            @elseif($mans->status_behandle == null)
                                                <button class="btn btn-outline-primary ReadyCheck" data-id="{{$mans->id}}">Make It Ready</button>
                                            @endif
                                    </div>
                                </td>
                                <td>
                                    @if($mans->status_behandle == 1)
                                        <!-- <p>On Progress</p> -->
                                        <span class="badge bg-light-warning">Ready</span>
                                        @elseif($mans->status_behandle == 2)
                                        <!-- <p>Ready</p> -->
                                        <span class="badge bg-light-success">On Proggress</span>
                                        @elseif($mans->status_behandle == 3)
                                        <!-- <p>Finish</p> -->
                                        <span class="badge bg-light-info">Finish</span>
                                    @else
                                        {{$mans->status_behandle}}
                                    @endif
                                </td>
                                <td>{{$mans->job->nojoborder}}</td>
                                <td>{{$mans->job->nombl}}</td>
                                <td>{{$mans->tgl_master_bl}}</td>
                                <td>{{$mans->nocontainer}}</td>
                                <td>{{$mans->size ?? ''}}</td>
                                <td>{{$mans->teus}}</td>
                                <td>{{$mans->job->PLP->no_plp ?? ''}}</td>
                                <td>{{$mans->job->PLP->tgl_plp ?? ''}}</td>
                                <td>{{$mans->date_ready_behandle}}</td>
                                <td>{{$mans->date_check_behandle}}</td>
                                <td>{{$mans->desc_check_behandle}}</td>
                                <td>{{$mans->date_finish_behandle}}</td>
                                <td>{{$mans->desc_finish_behandle}}</td>
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
            <h4 class="text-center">Checking From</h4>
        </div>
        <form action="{{route('fcl.delivery.updateBehandle')}}" id="updateForm" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row mt-0">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">No Container</label>
                            <input type="text" name="nocontainer" id="nocontainer_edit" class="form-control" readonly>
                            <input type="hidden" name="id" id="id_edit" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">No Job Order</label>
                            <input type="text" name="nojoborder" id="nojoborder_edit" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Size</label>
                            <input type="text" name="size" id="size_edit" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Yard Block</label>
                            <select name="yard_id" id="yard_id" style="width:100%;" class="js-example-basic-single select2 form-select">
                                <option disabled selected>Pilih Satu!</option>
                                @foreach($yards as $yard)
                                    <option value="{{$yard->id}}">{{$yard->yard_block}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Yard Slot</label>
                            <select name="slot" id="yard_slot" style="width:100%;" class="js-example-basic-single select2 form-select">
                                <option disabled selected>Pilih Block Terlebih Dahulu!</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Yard Row</label>
                            <select name="row" id="yard_row" style="width:100%;" class="js-example-basic-single select2 form-select">
                                <option disabled selected>Pilih Slot Terlebih Dahulu!</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Yard Tier</label>
                            <select name="tier" id="yard_tier" style="width:100%;" class="js-example-basic-single select2 form-select">
                                <option disabled selected>Pilih Row Terlebih Dahulu!</option>
                            </select>
                        </div>
                        
                    </div>
                    <div class="col-sm-6">
                        <!-- <div class="row">
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
                        </div> -->
                        <div class="form-group">
                            <label for="">Tanggal Ready Behandle</label>
                            <input type="datetime-local" name="date_ready_behandle" id="date_ready_behandle_edit" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Tgl Check Behandle</label>
                            <input type="datetime-local" name="date_ready_behandle" id="date_ready_behandle_edit" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Deskripsi Behandle</label>
                            <textarea name="desc_check_behandle" class="form-control" id="desc_check_behandle_edit" cols="30"></textarea>
                        </div>
                        @if(Auth::user()->hasRole('bc'))
                        <div class="form-group">
                            <label for="">Deskripsi Selesai Behandle</label>
                            <textarea name="desc_finish_behandle" class="form-control" id="desc_finish_behandle_edit" cols="30"></textarea>
                        </div>
                        @endif
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
$(document).ready(function() {
    let slotValue, rowValue, tierValue;

    // When Yard Block changes
    $('#yard_id').on('change', function() {
        let yardId = $(this).val();
        $('#yard_slot').empty().append('<option disabled selected>Loading...</option>');
        $('#yard_row').empty().append('<option disabled selected>Pilih Slot Terlebih Dahulu!</option>');
        $('#yard_tier').empty().append('<option disabled selected>Pilih Row Terlebih Dahulu!</option>');

        if (yardId) {
            $.ajax({
                url: '/get/slot',
                type: 'GET',
                data: { yard_id: yardId },
                success: function(response) {
                    $('#yard_slot').empty().append('<option disabled selected>Pilih Satu!</option>');
                    Object.values(response).forEach(function(slot) {
                        $('#yard_slot').append(`<option value="${slot}">${slot}</option>`);
                    });

                    if (slotValue) {
                        $('#yard_slot').val(slotValue).trigger('change');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    });

    // When Yard Slot changes
    $('#yard_slot').on('change', function() {
        let slotId = $(this).val();
        let yardId = $('#yard_id').val();
        $('#yard_row').empty().append('<option disabled selected>Loading...</option>');
        $('#yard_tier').empty().append('<option disabled selected>Pilih Row Terlebih Dahulu!</option>');

        if (slotId) {
            $.ajax({
                url: '/get/row',
                type: 'GET',
                data: { slot: slotId, yard_id: yardId },
                success: function(response) {
                    $('#yard_row').empty().append('<option disabled selected>Pilih Satu!</option>');
                    Object.values(response).forEach(function(row) {
                        $('#yard_row').append(`<option value="${row}">${row}</option>`);
                    });

                    if (rowValue) {
                        $('#yard_row').val(rowValue).trigger('change');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    });

    // When Yard Row changes
    $('#yard_row').on('change', function() {
        let rowId = $(this).val();
        let slotId = $('#yard_slot').val();
        let yardId = $('#yard_id').val();
        $('#yard_tier').empty().append('<option disabled selected>Loading...</option>');

        if (rowId) {
            $.ajax({
                url: '/get/tier',
                type: 'GET',
                data: { slot: slotId, yard_id: yardId, row: rowId },
                success: function(response) {
                    $('#yard_tier').empty().append('<option disabled selected>Pilih Satu!</option>');
                    Object.values(response).forEach(function(tier) {
                        $('#yard_tier').append(`<option value="${tier}">${tier}</option>`);
                    });

                    if (tierValue) {
                        $('#yard_tier').val(tierValue).trigger('change');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    });
    $(document).on('click', '.editButton', function() {
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
        swal.close();

        console.log(response);
        $("#id_edit").val(response.data.id);
        $("#nocontainer_edit").val(response.data.nocontainer);
        $("#nojoborder_edit").val(response.job.nojoborder);
        $("#size_edit").val(response.data.size);
        $("#no_spjm_edit").val(response.data.no_spjm);
        $("#tgl_spjm_edit").val(response.data.tgl_spjm);
        $("#date_ready_behandle_edit").val(response.data.date_ready_behandle);
        $("#date_check_behandle_edit").val(response.data.date_check_behandle);
        $("#desc_check_behandle_edit").val(response.data.desc_check_behandle);
        $("#desc_finish_behandle_edit").val(response.data.desc_finish_behandle);
       
            if (response.data.yard_id) {
                slotValue = response.slot;
                rowValue = response.row;
                tierValue = response.tier;
                $("#yard_id").val(response.data.yard_id).trigger('change');
            } else {
                $('#yard_id').val(null).append('<option disabled selected>Pilih Satu</option>');
                $('#yard_slot').empty().append('<option disabled selected>Pilih Block Terlebih Dahulu!</option>');
                $('#yard_row').empty().append('<option disabled selected>Pilih Slot Terlebih Dahulu!</option>');
                $('#yard_tier').empty().append('<option disabled selected>Pilih Row Terlebih Dahulu!</option>');
            }
      },
      error: function(data) {
        console.log('error:', data)
      }
    });
  });
});
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
$(document).on('click', '.ReadyCheck', function() {
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
                url: '/fcl/delivery/behandleReadyCheck' + id,
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
$(document).on('click', '.checkProses', function() {
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
                url: '/fcl/delivery/prosesCheckBehandle' + id,
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
                url: '/fcl/delivery/finishCheckBehandle' + id,
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