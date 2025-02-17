@extends('partial.main')
<style>
    .table-fixed td,
    .table-fixed th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .justify-text {
        text-align: justify; /* Justify the text */
        white-space: normal; /* Allow text to wrap */
        max-width: 700px; /* Set the maximum width (adjust as needed) */
        word-wrap: break-word; /* Break words if necessary */
        display: inline-block; /* Ensure it behaves like a block for alignment */
    }

</style>
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
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">End Stripping Date</label>
                            <input type="datetime-local" class="form-control" name="endstripping" value="{{$cont->endstripping ?? ''}}">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="photos">Pilih Foto-foto</label>
                            <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                        </div>
                    </div>
                    <div class="col-2">
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
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Validated Stripping</label>
                            <input type="text" value="{{$validateManifest}}" class="form-control" readonly>
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
            <div class="table">
                <table class="table-fixed table-bordered table-hover table-striped" id="tableDetil">
                    <thead>
                        <tr>
                            <th class="text-center">Action</th>
                            <th class="text-center">Ijin BC</th>
                            <th class="text-center">Status</th>
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
                            <label for="">Quantity Real</label>
                            <input type="text" name="final_qty" id="final_qty_edit" class="form-control">
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
                        <div class="form-group">
                            <label for="">Danger Label</label>
                            <select class="form-select" name="dg_label" id="dg_label_edit">
                                <option value="N">N</option>
                                <option value="Y">Y</option>
                            </select>
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
    $(document).ready(function () {
        var Id = {{ $id }}; 
        $('#tableDetil').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollCollapse: true,
            scrollY: '50vh',
            ajax: '/lcl/realisasi/stripping/prosesData-' + Id, // Fix concatenation
            columns: [
                { data: 'action', name: 'action', className: 'text-center' }, // Define the column
                { data: 'detil', name: 'detil', className: 'text-center' }, // Define the column
                { data: 'status', name: 'status', className: 'text-center' }, // Define the column
                { data: 'nohbl', name: 'nohbl', className: 'text-center' }, // Define the column
                { data: 'tgl_hbl', name: 'tgl_hbl', className: 'text-center' }, // Define the column
                { data: 'notally', name: 'notally', className: 'text-center' }, // Define the column
                { data: 'shiper', name: 'shiper', className: 'text-center' }, // Define the column
                { data: 'customer', name: 'customer', className: 'text-center' }, // Define the column
                { data: 'quantity', name: 'quantity', className: 'text-center' }, // Define the column
                { data: 'packN', name: 'packN', className: 'text-center' }, // Define the column
                { data: 'packC', name: 'packC', className: 'text-center' }, // Define the column
                { data: 'descofgoods', name: 'descofgoods', className: 'text-center' }, // Define the column
                { data: 'weight', name: 'weight', className: 'text-center' }, // Define the column
                { data: 'meas', name: 'meas', className: 'text-center' }, // Define the column
                { data: 'startstripping', name: 'startstripping', className: 'text-center' }, // Define the column
                { data: 'endstripping', name: 'endstripping', className: 'text-center' }, // Define the column
            ]
        })
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
        $("#final_qty_edit").val(response.data.final_qty);
        $("#tglstripping_edit").val(response.data.tglstripping);
        $("#jamstripping_edit").val(response.data.jamstripping);
        $("#startstripping_edit").val(response.data.startstripping);
        $("#endstripping_edit").val(response.data.endstripping);
        $("#dg_label_edit").val(response.data.dg_label).trigger('change');
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

            var quantity = document.getElementById('quantity_edit').value;
            var final_qty = document.getElementById('final_qty_edit').value;
            console.log('qty = ' + quantity);
            console.log('final qty = ' + final_qty);
            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Apakah data yang anda masukkan sudah sesuai?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (quantity != final_qty) {
                        Swal.fire({
                            title: "Apakah Anda Yakin?",
                            text: "Quantity yang anda masukkan berbeda dengan quantity flat file. Quantity Flat File : " + quantity + " Quantity yang anda masukkan : " + final_qty, 
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
                                // Submit the form programmatically if confirmed
                                document.getElementById('updateForm').submit(); 
                            }
                        })
                    }else{
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
                        // Submit the form programmatically if confirmed
                        document.getElementById('updateForm').submit();
                    }
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