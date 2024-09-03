@extends('partial.main')

@section('content')
<section>
    <div class="card">
        <form action="{{ route('dokumen.plp.update.detail')}}" method="post" id="createForm">
            @csrf
            <div class="card-body fixed-height">
                <div class="row mt-5">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Tanggal Upload</label>
                            <input type="date" class="form-control" name="upload_date" value="{{$plp->upload_date}}" disabled>
                        </div>
                        <div class="form-group">
                            <label for="">Kode Kantor</label>
                            <input type="text" class="form-control" name="kd_kantor" value="{{$plp->kd_kantor}}">
                            <input type="hidden" class="form-control" name="id" value="{{$plp->id}}">
                        </div>
                        <div class="form-group">
                            <label for="">No Surat PLP</label>
                            <input type="text" class="form-control" name="no_surat" value="{{$plp->no_surat}}">
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal Surat PLP</label>
                            <input type="date" class="form-control" name="tgl_surat" value="{{ \Carbon\Carbon::parse($plp->tgl_surat)->format('Y-m-d') }}">                        
                        </div>
                        <div class="form-group">
                            <label for="">No BC 11</label>
                            <input type="text" class="form-control" name="no_bc11" value="{{$plp->no_bc11}}">                        
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal BC11</label>
                            <input type="date" class="form-control" name="tgl_bc11" value="{{ \Carbon\Carbon::parse($plp->tgl_bc11)->format('Y-m-d') }}">                        
                        </div>
                        <div class="form-group">
                            <label for="">No PLP</label>
                            <input type="text" class="form-control" name="no_plp" value="{{$plp->no_plp}}">                        
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal  PLP</label>
                            <input type="date" class="form-control" name="tgl_plp" value="{{ \Carbon\Carbon::parse($plp->tgl_plp)->format('Y-m-d') }}">                        
                        </div>
                        <div class="form-group">
                            <label for="">Kode TPS Asal</label>
                            <input type="text" class="form-control" name="kd_tps_asal" value="{{$plp->kd_tps_asal}}">                        
                        </div>
                        <div class="form-group">
                            <label for="">Yor/SOR (%)</label>
                            <input type="number" class="form-control" name="yor_tps_asal" value="{{$plp->yor_tps_asal}}">                        
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Gudang Tujuan</label>
                            <input type="text" class="form-control" name="gudang_tujuan" value="{{$plp->gudang_tujuan}}">
                        </div>
                        <div class="form-group">
                            <label for="">Alasan Pindah</label>
                            <input type="text" class="form-control" name="alasan_reject" value="{{$plp->alasan_reject}}">
                        </div>
                        <div class="form-group">
                            <label for="">Alasan</label>
                            <textarea class="form-control" name="alasan" id="exampleFormControlTextarea1" rows="3">{{$plp->alasan}}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="">Lampiran</label>
                            <input type="text" class="form-control" name="lampiran" value="{{$plp->lampiran}}">
                        </div>
                        <div class="form-group">
                            <label for="">No Voy Flight</label>
                            <input type="text" class="form-control" name="no_voy_flight" value="{{$plp->no_voy_flight}}">
                        </div>
                        <div class="form-group">
                            <label for="">Call Sign</label>
                            <input type="text" class="form-control" name="call_sign" value="{{$plp->call_sign}}">
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal Tiba</label>
                            <input type="text" class="form-control" name="tgl_tiba" value="{{ \Carbon\Carbon::parse($plp->tgl_tiba)->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('dokumen.plp.index')}}" class="btn btn-outline-danger">Back</a>
                <button class="btn btn-warning" id="submitButton" type="button">Update</button>
            </div>
        </form>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-body">
            <table class="tabelCustom table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Container No</th>
                        <th>Ukuran</th>
                        <th>No Pos</th>
                        <th>Jenis</th>
                        <th>Customer</th>
                        <th>BL AWB</th>
                        <th>Tgl BL AWB</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $detail)
                    <tr>
                        <td>{{$detail->no_cont}}</td>
                        <td>{{$detail->uk_cont}}</td>
                        <td>{{$detail->no_pos_bc11}}</td>
                        <td>{{$detail->jns_cont}}</td>
                        <td>{{$detail->consignee}}</td>
                        <td>{{$detail->no_bl_awb}}</td>
                        <td>{{$detail->tgl_bl_awb}}</td>
                        <td>
                            <button class="btn btn-warning formEdit" data-id="{{ $detail->id }}" id="formEdit"><i class="fa fa-pen"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

@if($plp->joborder_id == null)
    <section>
       <div class="card text-center">
            <form action="{{ route('dokumen.plp.cetakJob') }}" method="POST">
                 @csrf
                 <input type="hidden" name="id" value="{{ $plp->id }}">
                 <button class="btn btn-outline-info" type="submit">
                     <i class="fas fa-print" style="width: 400px;height: 100px;"></i>
                     <h1>Cetak Job Order</h1>
                 </button>
             </form>
       </div>
    </section>
@endif

<div class="modal fade" id="editCust" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Edit Data Container PLP</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('dokumen.plp.update.cont')}}" method="POST" id="updateForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Cotnainer No</label>
                                <input type="text" class="form-control" id="no_cont_edit" readonly>
                            </div>
                            <div class="form-group">
                                <input type="hidden" id="id_edit" name="id">
                                <label for="">Jenis Container</label>
                                <select name="jns_cont" id="jns_cont_edit" class="js-example-basic-single form-select select2" style="width: 100%;">
                                    <option disabled selected>Pilih Satu</option>
                                    <option value="F">F</option>
                                    <option value="L">L</option>
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
      url: '/dokumen/plp/cont' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $('#editCust').modal('show');
        $("#editCust #id_edit").val(response.data.id);
        $("#editCust #no_cont_edit").val(response.data.no_cont);
        $("#editCust #jns_cont_edit").val(response.data.jns_cont).trigger('change');
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