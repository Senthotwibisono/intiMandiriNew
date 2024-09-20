@extends('partial.main')
@section('custom_styles')

<style>
    .table-responsive td,
    .table-responsive th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

@endsection
@section('content')
<section>
    <div class="card">
        <form action="/invoice/master/tarif-Post" method="post">
            @csrf
            <div class="card-body">
                <div class="row mt-0">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Kode</label>
                            <input type="text" class="form-control" name="kode_tarif" required>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Nama</label>
                            <input type="text" class="form-control" name="nama_tarif" required>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Jenis Storage</label>
                            <select name="jenis_storage" id="" class="js-example-basic-single select2 form-select" style="width: 100%;" required>
                                <option disabled selected value>Pilih Satu</option>
                                <option value="Admin">Admin</option>
                                <option value="Non Admin">Non Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Count Day</label>
                            <select name="day" id="" class="js-example-basic-single select2 form-select" style="width: 100%;" required>
                                <option disabled selected value>Pilih Satu</option>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Period</label>
                            <select name="period" id="" class="form-select">
                                <option disabled selected value>Pilih Satu</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Tarif Dasar</label>
                            <input type="number" class="form-control" name="tarif_dasar" value="0" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
        </form>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-body justify-content-center">
            <table class="tabelCustom">
                <thead>
                    <tr>
                        <th>Kode Tarif</th>
                        <th>Nama Tarif</th>
                        <th>Jenis Storage</th>
                        <th>Day</th>
                        <th>Period</th>
                        <th>Nilai Dasar</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mtarif as $tarif)
                        <tr>
                            <td>{{$tarif->kode_tarif}}</td>
                            <td>{{$tarif->nama_tarif}}</td>
                            <td>{{$tarif->jenis_storage}}</td>
                            <td>{{$tarif->day}}</td>
                            <td>{{$tarif->period}}</td>
                            <td>{{$tarif->tarif_dasar}}</td>
                            <td>
                                <button class="btn btn-warning formEdit" data-id="{{ $tarif->id }}" id="formEdit"><i class="fa fa-pen"></i></button>
                                <button class="btn btn-danger" data-id="{{ $tarif->id }}" id="deleteUser-{{ $tarif->id }}"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<div class="modal fade" id="editCust" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Edit Data Tarif</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="/invoice/master/tarif-Update" method="POST" id="updateForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Kode</label>
                                <input type="text" class="form-control" name="kode_tarif" id="kode_tarif_edit">
                                <input type="hidden" class="form-control" name="id" id="id_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Nama</label>
                                <input type="text" class="form-control" name="nama_tarif" id="nama_tarif_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Jenis Storage</label>
                                <select name="jenis_storage" id="jenis_storage_edit" class="editSelect select2 form-select" style="width: 100%;" required>
                                    <option disabled selected value>Pilih Satu</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Non Admin">Non Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="">Day</label>
                                <select name="day" id="day_edit" class="editSelect select2 form-select" style="width: 100%;" required>
                                    <option disabled selected value>Pilih Satu</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="">Period</label>
                                <select name="period" id="period_edit" class="form-select">
                                    <option disabled selected value>Pilih Satu</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="">Tarif Dasar</label>
                                <input type="number" class="form-control" name="tarif_dasar" id="tarif_dasar_edit" required>
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
                fetch(`/invoice/master/tarif-Delete${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => {
                    if (response.ok) {
                        Swal.fire(
                            'Dihapus!',
                            'Data tarif telah dihapus.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            'Data tarif tidak dapat dihapus.',
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
      url: '/invoice/master/tarif-Edit' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $('#editCust').modal('show');
        $("#editCust #kode_tarif_edit").val(response.data.kode_tarif);
        $("#editCust #id_edit").val(response.data.id);
        $("#editCust #nama_tarif_edit").val(response.data.nama_tarif);
        $("#editCust #jenis_storage_edit").val(response.data.jenis_storage).trigger('change');
        $("#editCust #day_edit").val(response.data.day).trigger('change');
        $("#editCust #period_edit").val(response.data.period ?? null);
        $("#editCust #tarif_dasar_edit").val(response.data.tarif_dasar);
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