@extends('partial.main')

@section('content')
<section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-auto">
                    <button type="button" id ="otomaticButton" class="btn btn-success">get Data</button>
                </div>
                <div class="col-auto ms-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual">SPJM On Demand</button>
                </div>
            </div>
            <br>
            <div style="overflow-x:auto;">
                <div class="table table-responsive">
                    <table class="table table-bordered table-striped" id="tableSPJM">
                        <thead>
                            <tr>
                                <!-- <th>Action</th> -->
                                <th>CAR</th>
                                <th>Kd Kantor</th>
                                <th>Tgl SPJM</th>
                                <th>No SPJM</th>
                                <th>NPWP Importir</th>
                                <th>Nama Importir</th>
                                <th>NPWP PPJK</th>
                                <th>Nama PPJK</th>
                                <th>gudang</th>
                                <th>Jml Cont</th>
                                <th>No BC11</th>
                                <th>Tgl BC11</th>
                                <th>No Pos BC11</th>
                                <th>Fl Karantina</th>
                                <th>Nm Angkut</th>
                                <th>No Voy Flight</th>
                                <th>Tgl Upload</th>
                                <th>Jam Upload</th>
                                <th>No Dok</th>
                                <th>Tgl Dok</th>
                                <th>flag</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="addManual" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">SPJM On Demand</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('dokumen.spjm.onDemand')}}" method="POST" enctype="multipart/form-data" id="createForm">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">No SPJM</label>
                                <input type="text" class="form-control" name="no_spjm" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Tanggal SPJM</label>
                                <input type="date" class="form-control" name="tgl_spjm" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                    <button type="button" id="submitButton" class="btn btn-primary ml-1" data-bs-dismiss="modal"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCust" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Edit Data PPJK</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('master.ppjk.update')}}" method="POST" id="updateForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Name</label>
                                <input type="text" class="form-control" name="name" id="name_edit" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone_edit" required>
                                <input type="hidden" class="form-control" name="id" id="id_edit" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                    <button type="button" id="updateButton" class="btn btn-primary ml-1" data-bs-dismiss="modal"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script>
    $(document).on('click', '#otomaticButton', function () {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to update this record?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Perform the AJAX request
                $.ajax({
                    url: "{{ route('dokumen.spjm.automatic') }}", // Laravel route helper
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}", // Include CSRF token for security
                        // Additional data can be added here
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.success) {
                            Swal.fire('Saved!', '', 'success')
                                .then(() => {
                                    // Memuat ulang halaman setelah berhasil menyimpan data
                                    window.location.reload();
                                });
                        } else {
                            Swal.fire('Error', response.message, 'error')
                                .then(() => {
                                    // Memuat ulang halaman setelah berhasil menyimpan data
                                    window.location.reload();
                                });
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
                            console.log('error:', response);
                        }
                    },
                });
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('submitButton').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to update this record?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    // Submit the form programmatically if confirmed
                    document.getElementById('createForm').submit();
                }
            });
        });
    });
</script>
<script>
    document.querySelectorAll('[id^="deleteUser-"]').forEach(button => {
    button.addEventListener('click', function() {
        var userId = this.getAttribute('data-id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan dapat mengembalikan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/master/ppjk-delete${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => {
                    if (response.ok) {
                        Swal.fire(
                            'Dihapus!',
                            'Data pengguna telah dihapus.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            'Data pengguna tidak dapat dihapus.',
                            'error'
                        );
                    }
                }).catch(error => {
                    Swal.fire(
                        'Gagal!',
                        'Terjadi kesalahan saat menghapus data pengguna.',
                        'error'
                    );
                });
            }
        });
    });
});
</script>

<script>
   $(document).on('click', '.formEdit', function() {
    let id = $(this).data('id');
    $.ajax({
      type: 'GET',
      url: '/master/ppjk-edit' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $('#editCust').modal('show');
        $("#editCust #name_edit").val(response.data.name);
        $("#editCust #id_edit").val(response.data.id);
        $("#editCust #phone_edit").val(response.data.phone);
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
                text: "Do you want to update this record?",
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
    $(document).ready(function(){
        $('#tableSPJM').DataTable({
            processing: true,
            severSide: true,
            ajax: '/dokumen/spjmData',
            columns:[
                {data:'car', name:'car', className:'text-center'},
                {data:'kd_kantor', name:'kd_kantor', className:'text-center'},
                {data:'tgl_pib', name:'tgl_pib', className:'text-center'},
                {data:'no_spjm', name:'no_spjm', className:'text-center'},
                {data:'npwp_imp', name:'npwp_imp', className:'text-center'},
                {data:'nama_imp', name:'nama_imp', className:'text-center'},
                {data:'npwp_ppjk', name:'npwp_ppjk', className:'text-center'},
                {data:'nama_ppjk', name:'nama_ppjk', className:'text-center'},
                {data:'gudang', name:'gudang', className:'text-center'},
                {data:'jml_cont', name:'jml_cont', className:'text-center'},
                {data:'no_bc11', name:'no_bc11', className:'text-center'},
                {data:'tgl_bc11', name:'tgl_bc11', className:'text-center'},
                {data:'no_pos_bc11', name:'no_pos_bc11', className:'text-center'},
                {data:'fl_karantina', name:'fl_karantina', className:'text-center'},
                {data:'nm_angkut', name:'nm_angkut', className:'text-center'},
                {data:'no_voy_flight', name:'no_voy_flight', className:'text-center'},
                {data:'tgl_upload', name:'tgl_upload', className:'text-center'},
                {data:'jam_upload', name:'jam_upload', className:'text-center'},
                {data:'no_dok', name:'no_dok', className:'text-center'},
                {data:'tgl_dok', name:'tgl_dok', className:'text-center'},
                {data:'flag', name:'flag', className:'text-center'},
            ],
            pageLength:25,
        })
    })
</script>
@endsection