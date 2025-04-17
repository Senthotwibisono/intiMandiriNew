@extends('partial.main')
@section('custom_styles')
<style>
    #tableSPPB td, #tableSPPB th {
        white-space: nowrap; /* Membuat teks tetap dalam satu baris */
    }
</style>
@endsection
@section('content')
<section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-auto">
                    <button type="button" class="btn btn-success" id="otomaticButton">get Data</button>
                </div>
                <div class="col-auto ms-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual">SPPB On Demand</button>
                </div>
            </div>
            <br>
            
            <div class="table">
                <table class="table-hover table-stripped" id="tableSPPB">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>List Container</th>
                            <th>car</th>
                            <th>no_sppb</th>
                            <th>tgl_sppb</th>
                            <th>nojoborder</th>
                            <th>no_pib</th>
                            <th>tgl_pib</th>
                            <th>nama_imp</th>
                            <th>npwp_imp</th>
                            <th>alamat_imp</th>
                        </tr>
                    </thead>
                </table>
            </div>
           
        </div>
    </div>
</section>

<div class="modal fade" id="addManual" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">SPPB BC23 On Demand</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('dokumen.sppb.onDemand')}}" method="POST" id="createForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">No SPPB BC23</label>
                                <input type="text" class="form-control" name="no_sppb" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Tanggal SPPB BC23</label>
                                <input type="date" class="form-control" name="tgl_sppb" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">NPWP Importir</label>
                                <input type="text" class="form-control" name="npwp_imp" required>
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

<div class="modal fade" id="containerListModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noDokumen"></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <div class="modal-body">
                <div class="table">
                    <table id="containerTable" class="table table-striped table-bordered">
                        <thead style="white-space: nowrap;">
                            <tr>
                                <th>No Kontainer</th>
                                <th>Ukuran Dok</th>
                                <th>Ukuran Asli</th>
                                <th>Tanggal Masuk</th>
                                <th>Tanggal Keluar</th>
                                <th>Lama Hari</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script>
    $(document).on('click', '.detilContainer', function(){
        let id = $(this).data('id');
            // console.log("Id Dokumen yg dipilih = " + id); // Untuk mengecek nilai di console
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

        $.ajax({
            url: '/dokumen/sppbContainer/' + id,
            type: 'GET',
            data: {
                _token: "{{ csrf_token() }}",
                id : id,
            },

            success: function(response) {
                swal.close();
                if (response.success) {
                    console.log(response);
                    $('#containerListModal').modal('show');
                    $('#containerListModal #noDokumen').text(response.noDokumen);
                    if ($.fn.DataTable.isDataTable('#containerTable')) {
                    $('#containerTable').DataTable().destroy();
                }

                // Inisialisasi ulang DataTable dengan data baru
                $('#containerTable').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollY: true,
                    paging: false,      // Disable pagination
                    searching: false,
                    ajax: {
                        url: '/dokumen/sppbContainer/' + id,
                        type: 'GET'
                    },
                    columns: [
                        { data: 'noCont', name: 'noCont' },
                        { data: 'ukuranDok', name: 'ukuranDok' },
                        { data: 'sizeCont', name: 'sizeCont' },
                        { data: 'tglMasuk', name: 'tglMasuk' },
                        { data: 'tglKeluar', name: 'tglKeluar' },
                        { data: 'lamaHari', name: 'lamaHari' },
                    ]
                });
                   
                } else {
                    Swal.fire('Error', response.message, 'error')
                        .then(() => {
                            // Memuat ulang halaman setelah berhasil menyimpan data
                            window.location.reload();
                        });
                }
            }
        })
    })
</script>
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
                    url: "{{ route('dokumen.sppb.import') }}", // Laravel route helper
                    type: 'GET',
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
        $('#tableSPPB').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: '/dokumen/sppbData',
            columns:[
                {data:'id', name:'id', className:'text-center',
                    render: function(data, row){
                        return `<a href="/dokumen/sppb/detail${data}" class="btn btn-warning"><i class="fa fa-pen"></i></a>
`
                    }
                },
                {
                    data:'id',
                    name: 'detil',
                    className: 'text-center',
                    render: function(data, row){
                        const formId = row.id;
                        return `<button type="button" class="btn btn-info detilContainer" data-id="${data}"><i class="fa fa-eye"></i></button>`;
                    }
                },
                {data:'car', name:'car', className:'text-center'},
                {data:'no_sppb', name:'no_sppb', className:'text-center'},
                {data:'tgl_sppb', name:'tgl_sppb', className:'text-center'},
                {data:'nojoborder', name:'nojoborder', className:'text-center'},
                {data:'no_pib', name:'no_pib', className:'text-center'},
                {data:'tgl_pib', name:'tgl_pib', className:'text-center'},
                {data:'nama_imp', name:'nama_imp', className:'text-center'},
                {data:'npwp_imp', name:'npwp_imp', className:'text-center'},
                {data:'alamat_imp', name:'alamat_imp', className:'text-center',
                    render: function(data,row){
                        return `<textarea class="form-control" id="exampleFormControlTextarea1" rows="3" readonly>${data}</textarea>
`
                    }
                },
            ],
            pageLength: 25,
        })
    })
</script>
@endsection