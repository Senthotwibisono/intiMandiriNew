@extends('partial.main')

@section('content')
<section>
    <div class="card">
        <div class="card-body">
            <table class="tabelCustom table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Guard</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>{{$role->name}}</td>
                        <td>{{$role->guard_name}}</td>
                        <td>
                            <button class="btn btn-warning formEdit" data-id="{{ $role->id }}" id="formEdit"><i class="fa fa-pen"></i></button>
                            <button class="btn btn-danger" data-id="{{ $role->id }}" id="deleteRole-{{ $role->id }}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<div class="row">
    <div class="col-6">
        <section>
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('role.create') }}" method="POST" id="createForm">
                        @csrf
                    <p><strong>Form Tambah Role</strong></p> 
                        <div class="row">
                            <div class="col-10">
                                <div class="row mt-5">
                                <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Role Name</label>
                                            <input type="text" class="form-control" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Guard Name</label>
                                            <input type="text" class="form-control" name="guard_name" value="web" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="row mt-5 h-100">
                                    <div class="col-sm-12 d-flex align-items-center">
                                        <button type="button" id="submitButton" class="btn btn-success">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
    <div class="col-6">
    <section>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('role.update') }}" method="POST" id="updateForm">
                    @csrf
                <p><strong>Form Edit Role</strong></p> 
                    <div class="row">
                        <div class="col-10">
                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Role Name</label>
                                        <input type="text" class="form-control" id="name_edit" name="name" required>
                                        <input type="hidden" class="form-control" id="id_edit" name="id" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Guard Name</label>
                                        <input type="text" class="form-control" id="guard_edit" name="guard_name" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="row mt-5 h-100">
                                <div class="col-sm-12 d-flex align-items-center">
                                    <button type="button" id="updateButton" class="btn btn-warning">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
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
    document.querySelectorAll('[id^="deleteRole-"]').forEach(button => {
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
                fetch(`/role/delete${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => response.json().then(data => {
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
                            data.message,
                            'error'
                        );
                    }
                })).catch(error => {
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
      url: '/role/edit-' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $("#name_edit").val(response.data.name);
        $("#id_edit").val(response.data.id);
        $("#guard_edit").val(response.data.guard_name);
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