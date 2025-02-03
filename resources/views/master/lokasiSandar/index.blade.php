@extends('partial.main')

@section('content')
<section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- <div class="col-auto">
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#excelModal">Add File</button>
                </div> -->
                <div class="col-auto ms-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual">Add Lokasi Sandar</button>
                </div>
            </div>
            <br>
            <table class="tabelCustom table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Kode TPS Asal</th>
                        <th>Jabatan</th>
                        <th>Perusahaan</th>
                        <th>Pelabuhan</th>
                        <th>Kota</th>
                        <th>Negara</th>
                        <th>UID</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loks as $lok)
                    <tr>
                        <td>{{$lok->name ?? ' '}}</td>
                        <td>{{$lok->kd_tps_asal ?? ' '}}</td>
                        <td>{{$lok->jabatan ?? 'Data Belum di Isi'}}</td>
                        <td>{{$lok->perusahaan->name ?? 'Data Belum di Isi'}}</td>
                        <td>{{$lok->pelabuhan->name ?? 'Data Belum di Isi'}}</td>
                        <td>{{$lok->kota ?? ' '}}</td>
                        <td>{{$lok->negara->name ?? ''}}</td>
                        <td>{{$lok->user->name ?? ''}}</td>
                        <td>
                            <button class="btn btn-warning formEdit" data-id="{{ $lok->id }}" id="formEdit"><i class="fa fa-pen"></i></button>
                            <button class="btn btn-danger" data-id="{{ $lok->id }}" id="deleteUser-{{ $lok->id }}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
    
<!-- Modal Excel -->
<div class="modal fade" id="excelModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Upload Data Consolidator</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="/master/consolidator-excel" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p> Upload Your File </p>
                    <input type="file" name="file" class="form-control" id="inputGroupFile01">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                    <button type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addManual" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Add Data Consolidator</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('master.lokasiSandar.post')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Name</label>
                                <input type="text" class="form-control" name="name">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Kode TPS Asal</label>
                                <input type="text" class="form-control" name="kd_tps_asal">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Jabatan</label>
                                <input type="text" class="form-control" name="Jabatan">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Perusahaan</label>
                                <select name="perusahaan_id" style="width: 100%;" class="customSelect form-select select2">
                                    <option value="" disabled selected>Pilih Satu</option>
                                    @foreach($perusahaans as $perusahaan)
                                        <option value="{{$perusahaan->id}}">{{$perusahaan->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Pelabuhan</label>
                                <select name="pelabuhan_id"  class="customSelect form-select select2" style="width: 100%;"> 
                                    <option value="" disabled selected>Pilih Satu</option>
                                    @foreach($pelabuhans as $pelabuhan)
                                        <option value="{{$pelabuhan->id}}">{{$pelabuhan->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Kota</label>
                                <input type="text" class="form-control" name="kota">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Negara</label>
                                <select name="negara_id" style="width: 100%;" class="customSelect form-select select2">
                                    <option value="" disabled selected>Pilih Satu</option>
                                    @foreach($negaras as $negara)
                                        <option value="{{$negara->id}}">{{$negara->name}}</option>
                                    @endforeach
                                </select>
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

<div class="modal fade" id="editCust" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Edit Data Consolidator</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('master.lokasiSandar.update')}}" method="POST" id="updateForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Name</label>
                                <input type="text" class="form-control" name="name" id="name_edit">
                                <input type="hidden" class="form-control" name="id" id="id_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Kode TPS Asal</label>
                                <input type="text" class="form-control" name="kd_tps_asal" id="kd_tps_asal_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Jabatan</label>
                                <input type="text" class="form-control" name="Jabatan" id="jabatan_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Perusahaan</label>
                                <select name="perusahaan_id" id="perusahaan_id_edit" class="editSelect form-select select2" style="width: 100%;"> 
                                    <option value="" disabled selected>Pilih Satu</option>
                                    @foreach($perusahaans as $perusahaan)
                                        <option value="{{$perusahaan->id}}">{{$perusahaan->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Pelabuhan</label>
                                <select name="pelabuhan_id" id="pelabuhan_id_edit" class="editSelect form-select select2" style="width: 100%;"> 
                                    <option value="" disabled selected>Pilih Satu</option>
                                    @foreach($pelabuhans as $pelabuhan)
                                        <option value="{{$pelabuhan->id}}">{{$pelabuhan->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Kota</label>
                                <input type="text" class="form-control" name="kota" id="kota_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Negara</label>
                                <select name="negara_id" id="negara_id_edit" class="editSelect form-select select2" style="width: 100%;"> 
                                    <option value="" disabled selected>Pilih Satu</option>
                                    @foreach($negaras as $negara)
                                        <option value="{{$negara->id}}">{{$negara->name}}</option>
                                    @endforeach
                                </select>
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
                fetch(`/master/lokasiSandar-delete${userId}`, {
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
      url: '/master/lokasiSandar-edit' + id,
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
        $("#editCust #kd_tps_asal_edit").val(response.data.kd_tps_asal);
        $("#editCust #jabatan_edit").val(response.data.jabatan);
        $("#editCust #perusahaan_id_edit").val(response.data.perusahaan_id).trigger('change');
        $("#editCust #pelabuhan_id_edit").val(response.data.pelabuhan_id).trigger('change');
        $("#editCust #kota_edit").val(response.data.kota);
        $("#editCust #negara_id_edit").val(response.data.negara_id).trigger('change');
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