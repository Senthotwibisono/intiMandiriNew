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
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual">Add Shipping Line</button>
                </div>
            </div>
            <br>
            <table class="tabelCustom table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Shipping Line</th>
                        <th>Vessel</th>
                        <th>Email</th>
                        <th>CC</th>
                        <th>UID</th>
                        <th>Keterangan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ships as $ships)
                    <tr>
                        <td>{{$ships->shipping_line ?? 'Data Belum di Isi'}}</td>
                        <td>{{$ships->vessel->name ?? 'Data Belum di Isi'}}</td>
                        <td>{{$ships->email ?? 'Data Belum di Isi'}}</td>
                        <td>{{$ships->contact ?? 'Data Belum di Isi'}}</td>
                        <td>{{$ships->user->name ?? 'Data Belum di Isi'}}</td>
                        <td><textarea class="form-control" id="exampleFormControlTextarea1" rows="3" readonly>{{$ships->keterangan ?? 'Data Belum di Isi'}}</textarea></td>
                        <td>
                            <button class="btn btn-warning formEdit" data-id="{{ $ships->id }}" id="formEdit"><i class="fa fa-pen"></i></button>
                            <button class="btn btn-danger" data-id="{{ $ships->id }}" id="deleteUser-{{ $ships->id }}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
    
<div class="modal fade" id="addManual" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Add Data Shipping Lines</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('master.shippingLines.post')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Shipping Lines</label>
                                <input type="text" class="form-control" name="shipping_line">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Vessel</label>
                                <select name="vessel_id" id="" class="customSelect form-select select2" style="width: 100%;">
                                    <option value disabled selected>Pilih Satu!</option>
                                    @foreach($vessel as $ves)
                                        <option value="{{$ves->id}}">{{$ves->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input type="text" class="form-control" name="email">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">CC</label>
                                <input type="text" class="form-control" name="contact">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Keterangan</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" name="keterangan" rows="3"></textarea>
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
                <h5 class="modal-title" id="exampleModalCenterTitle">Edit Data Shipping Lines</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('master.shippingLines.update')}}" method="POST" id="updateForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Shipping Lines</label>
                                <input type="text" class="form-control" name="shipping_line" id="shipping_line_edit">
                                <input type="hidden" class="form-control" name="id" id="id_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Vessel</label>
                                <select name="vessel_id" id="vessel_id_edit" id="" class="editSelect form-select select2" style="width: 100%;">
                                    <option value disabled selected>Pilih Satu!</option>
                                    @foreach($vessel as $ves)
                                        <option value="{{$ves->id}}">{{$ves->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input type="text" class="form-control" name="email" id="email_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">CC</label>
                                <input type="text" class="form-control" name="contact" id="cc_edit">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Keterangan</label>
                                <textarea class="form-control" name="keterangan" id="keterangan_edit" rows="3"></textarea>
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
                fetch(`/master/shippingLines-delete${userId}`, {
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
      url: '/master/shippingLines-edit' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $('#editCust').modal('show');
        $("#editCust #shipping_line_edit").val(response.data.shipping_line);
        $("#editCust #id_edit").val(response.data.id);
        $("#editCust #vessel_id_edit").val(response.data.vessel_id).trigger('change');
        $("#editCust #email_edit").val(response.data.email);
        $("#editCust #cc_edit").val(response.data.contact);
        $("#editCust #keterangan_edit").val(response.data.keterangan);
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