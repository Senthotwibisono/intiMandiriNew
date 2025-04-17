@extends('partial.main')
@section('custom_styles')
<style>
    #tableManual td, #tableManual th {
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
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual">Dok Manual On Demand</button>
                </div>
            </div>
            <br>
            <div class="table">
                <table class="table table-hover table-stripped" id="tableManual">
                    <thead class="align-item-center">
                        <tr>
                            <th>Action</th>
                            <th>Container List</th>
                            <th>Id</th>
                            <th>Kode Dokumen</th>
                            <th>Nomor Dokumen</th>
                            <th>Tanggal Dokumen</th>
                            <th>No BC11</th>
                            <th>Tanggal BC11</th>
                            <th>Tanggal Upload</th>
                            <th>Jam Upload</th>
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
                <h5 class="modal-title" id="exampleModalCenterTitle">Dokumen Manual On Demand</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('dokumen.manual.onDemand')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Kode Dokumen</label>
                                <select name="kd_dok" id="" style="width: 100%;" class="choices">
                                    <option value disabled selected>Pilih Satu!</option>
                                    @foreach($codes as $code)
                                        <option value="{{$code->kode}}">{{$code->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">No Dokumen Manual</label>
                                <input type="text" class="form-control" name="no_dok" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Tanggal Dokumen Manual</label>
                                <input type="date" class="form-control" name="tgl_dok" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                    <button type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
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
                        <thead>
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
            url: '/dokumen/manualContainer/' + id,
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
                        url: '/dokumen/manualContainer/' + id,
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
                    url: "{{ route('dokumen.manual.auto') }}", // Laravel route helper
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
    $(document).ready(function(){
        $('#tableManual').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/dokumen/manualData',
            columns:[
                {data:'idm', name:'id', className:'text-center',
                    render: function(data,row){
                        return `<a href="/dokumen/manual/detail${data}" class="btn btn-warning"><i class="fa fa-pen"></i></a>`
                    }
                },
                {
                    data:'idm',
                    name: 'detil',
                    className: 'text-center',
                    render: function(data, row){
                        const formId = row.id;
                        return `<button type="button" class="btn btn-info detilContainer" data-id="${data}"><i class="fa fa-eye"></i></button>`;
                    }
                },
                {data:'id', name:'id', className:'text-center'},
                {data:'dokumen.name', name:'dokumen.name', className:'text-center'},
                {data:'no_dok_inout', name:'no_dok_inout', className:'text-center'},
                {data:'tgl_dok_inout', name:'tgl_dok_inout', className:'text-center'},
                {data:'no_bc11', name:'no_bc11', className:'text-center'},
                {data:'tgl_bc11', name:'tgl_bc11', className:'text-center'},
                {data:'tgl_upload', name:'tgl_upload', className:'text-center'},
                {data:'jam_upload', name:'jam_upload', className:'text-center'},
            ]
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
@endsection