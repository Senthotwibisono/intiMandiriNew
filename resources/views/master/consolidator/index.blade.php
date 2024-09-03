@extends('partial.main')

@section('content')
<section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#excelModal">Add File</button>
                </div>
                <div class="col-auto ms-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual">Add Consolidator</button>
                </div>
            </div>
            <br>
            <table class="tabelCustom table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Contact</th>
                        <th>No Cano</th>
                        <th>Tgl Akhir Kontrak</th>
                        <th>Kontrak</th>
                        <th>UID</th>
                        <th>NPWP</th>
                        <th>PPN</th>
                        <th>Materai</th>
                        <th>NPPKP</th>
                        <th>Keterangan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consolidators as $cons)
                    <tr>
                        <td>{{$cons->namaconsolidator}}</td>
                        <td>{{$cons->notelp}}</td>
                        <td>{{$cons->contactperson}}</td>
                        <td>{{$cons->nocano}}</td>
                        <td>{{$cons->tglakhirkontrak}}</td>
                        <td>{{$cons->kontrak}}</td>
                        <td>{{$cons->user->name}}</td>
                        <td>{{$cons->npwp}}</td>
                        <td>{{$cons->ppn}}</td>
                        <td>{{$cons->materai}}</td>
                        <td>{{$cons->nppkp}}</td>
                        <td><textarea class="form-control" id="exampleFormControlTextarea1" rows="3" readonly>{{$cons->keterangan}}</textarea></td>
                        <td>
                            <button class="btn btn-warning formEdit" data-id="{{ $cons->id }}" id="formEdit"><i class="fa fa-pen"></i></button>
                            <button class="btn btn-danger" data-id="{{ $cons->id }}" id="deleteUser-{{ $cons->id }}"><i class="fa fa-trash"></i></button>
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

<div class="modal fade" id="addManual" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-focus="false"> 
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Add Data Consolidator</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('master.consolidator.post')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Name</label>
                                <input type="text" class="form-control" name="namaconsolidator">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Phone</label>
                                <input type="text" class="form-control" name="notelp">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Contact</label>
                                <input type="text" class="form-control" name="contactperson">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">No Cano</label>
                                <input type="text" class="form-control" name="nocano">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Kontrak</label>
                                <select name="kontrak" class="customSelect form-select select2" style="width: 100%;">
                                    <option disabled selected>Pilih Satu</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Tanggal Akhir Kontrak</label>
                                <input type="datetime-local" class="form-control flatpickr-range mb-3" name="tglakhirkontrak">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">NPWP</label>
                                <input type="text" class="form-control" name="npwp">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">PPN</label>
                                <input type="text" class="form-control" name="ppn">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Materai</label>
                                <input type="text" class="form-control" name="materai" placeholder="isi dengan 0 jika kosong" required pattern="^\d+([.,]\d+)?$" title="Please enter a valid decimal number (e.g., 12.34 or 12,34)">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">NPPKP</label>
                                <input type="text" class="form-control" name="nppkp">
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
                <h5 class="modal-title" id="exampleModalCenterTitle">Edit Data Consolidator</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('master.consolidator.update')}}" method="POST" id="updateForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                <div class="row mb-5">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Name</label>
                                <input type="text" class="form-control" name="namaconsolidator" id="namaconsolidator_edit">
                                <input type="hidden" class="form-control" name="id" id="id_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Phone</label>
                                <input type="text" class="form-control" name="notelp" id="notelp_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Contact</label>
                                <input type="text" class="form-control" name="contactperson" id="contactperson_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">No Cano</label>
                                <input type="text" class="form-control" name="nocano" id="nocano_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Kontrak</label>
                                <select name="kontrak" id="kontrak_edit" class="editSelect form-select select2" style="width: 100%;">
                                    <option disabled selected>Pilih Satu</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Tanggal Akhir Kontrak</label>
                                <input type="datetime-local" class="form-control flatpickr-range mb-3" name="tglakhirkontrak" id="tglakhirkontrak_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">NPWP</label>
                                <input type="text" class="form-control" name="npwp" id="npwp_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">PPN</label>
                                <input type="text" class="form-control" name="ppn" id="ppn_edit">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Materai</label>
                                <input type="text" class="form-control" name="materai" id="materai_edit" placeholder="isi dengan 0 jika kosong" required pattern="^\d+([.,]\d+)?$" title="Please enter a valid decimal number (e.g., 12.34 or 12,34)">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">NPPKP</label>
                                <input type="text" class="form-control" name="nppkp" id="nppkp_edit">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Keterangan</label>
                                <textarea class="form-control" id="keterangan_edit" name="keterangan" rows="3"></textarea>
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
                fetch(`/master/consolidator-delete${userId}`, {
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
      url: '/master/consolidator-edit' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $('#editCust').modal('show');
        $("#editCust #namaconsolidator_edit").val(response.data.namaconsolidator);
        $("#editCust #id_edit").val(response.data.id);
        $("#editCust #notelp_edit").val(response.data.notelp);
        $("#editCust #contactperson_edit").val(response.data.contactperson);
        $("#editCust #nocano_edit").val(response.data.nocano);
        $("#editCust #kontrak_edit").val(response.data.kontrak).trigger('change');
        $("#editCust #tglakhirkontrak_edit").val(response.data.tglakhirkontrak);
        $("#editCust #npwp_edit").val(response.data.npwp);
        $("#editCust #ppn_edit").val(response.data.ppn);
        $("#editCust #materai_edit").val(response.data.materai);
        $("#editCust #nppkp_edit").val(response.data.nppkp);
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