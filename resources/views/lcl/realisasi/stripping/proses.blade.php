@extends('partial.main')

@section('content')
<section>
    <div class="card">
        <div class="card-header">
            <div class="text-center">
               <div class="button-container">
                    <h4><strong>Detail Container</strong></h4>
                    <a href="javascript:void(0)" onclick="openWindow('/lcl/realisasi/stripping-photoCont{{$cont->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
               </div>
            </div>
        </div>
        @if(Auth::check() && !Auth::user()->hasRole('bc'))
        <form action="{{ route('lcl.stripping.cont.update')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row mt-1">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Tgl Mulai Stripping</label>
                            <input type="date" class="form-control" name="tglstripping" value="{{$cont->tglstripping ?? ''}}">
                            <input type="hidden" class="form-control" name="id" value="{{$cont->id}}">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Jam Mulai Stripping</label>
                            <input type="time" class="form-control" name="jamstripping" value="{{$cont->jamstripping ?? ''}}">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="">End Stripping Date</label>
                            <input type="datetime-local" class="form-control" name="endstripping" value="{{$cont->endstripping ?? ''}}">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="photos">Pilih Foto-foto</label>
                            <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Validated Stripping</label>
                            <input type="text" value="{{$validateManifest}}" class="form-control" readoly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="button-container">
                    <button class="btn btn-warning" type="submit">Update</button>
                </div>
            </div>
        </form>
        @endif
    </div>
</section>

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
                            <th class="text-center">Aprrove</th>
                            <th class="text-center">No HBL</th>
                            <th class="text-center">Tgl HBL</th>
                            <th class="text-center">No Tally</th>
                            <th class="text-center">Shipper</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Packing</th>
                            <th class="text-center">Kode Kemas</th>
                            <th class="text-center">Desc</th>
                            <th class="text-center">Weight</th>
                            <th class="text-center">Meas</th>
                            <th class="text-center">Tgl Mulai Stripping</th>
                            <th class="text-center">Tgl Selesai Stripping</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($manifest as $mans)
                            <tr>
                                <td>
                                    <div class="button-container">
                                        <button class="btn btn-warning editButton" data-id="{{$mans->id}}"><i class="fa fa-pencil"></i></button>
                                        <a href="javascript:void(0)" onclick="openWindow('/lcl/realisasi/stripping-photoManifest{{$mans->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                    </div>
                                </td>
                                <td>
                                    @if($mans->validasi == 'N')
                                        <button class="btn btn-outline-danger approveButton" data-id="{{$mans->id}}">Approve</button>
                                    @else
                                    <span class="badge bg-light-success">Approved</span>
                                    @endif
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
                                    <textarea class="form-control" cols="3" readonly>{{$mans->descofgoods}}</textarea>
                                </td>
                                <td>{{$mans->weight}}</td>
                                <td>{{$mans->meas}}</td>
                                <td>{{$mans->startstripping}}</td>
                                <td>{{$mans->endstripping}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@if(Auth::check() && !Auth::user()->hasRole('bc'))
<section>
    <div class="card">
        <div class="card-header">
            <h4 class="text-center">Stripping Form</h4>
        </div>
        <form action="{{ route('lcl.stripping.store')}}" id="updateForm" method="post" enctype="multipart/form-data">
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
                        <div class="form-group">
                            <label for="photos">Pilih Foto-foto</label>
                            <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Tgl Stripping</label>
                                    <input type="date" name="tglstripping" id="tglstripping_edit" class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Jam Stripping</label>
                                    <input type="time" name="jamstripping" id="jamstripping_edit" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Start Stripping Date</label>
                            <input type="datetime-local" name="startstripping" id="startstripping_edit" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">End Stripping Date</label>
                            <input type="datetime-local" name="endstripping" id="endstripping_edit" class="form-control">
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

@if($cont->endstripping == null)
<section>
   <div class="card text-center">
        <form action="{{ route('lcl.stripping.end') }}" method="POST" id="endForm"> 
             @csrf
             <input type="hidden" name="id" value="{{ $cont->id }}">
             <button class="btn btn-danger" type="button" id="endButton"><h2 class="text-white">End Stripping</h2></button>
         </form>
   </div>
</section>
@endif
@endif
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
        $("#tglstripping_edit").val(response.data.tglstripping);
        $("#jamstripping_edit").val(response.data.jamstripping);
        $("#startstripping_edit").val(response.data.startstripping);
        $("#endstripping_edit").val(response.data.endstripping);
      },
      error: function(data) {
        console.log('error:', data)
      }
    });
  });
</script>

<script>
$(document).on('click', '.deleteButton', function() {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin menghapus data ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '/lcl/manifest/delete-' + id,
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
$(document).on('click', '.approveButton', function() {
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
                url: '/lcl/manifest/approve-' + id,
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