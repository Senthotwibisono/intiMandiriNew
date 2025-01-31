@extends('partial.main')

@section('content')
<section>
    <div class="card">
        <form action="{{ route('dokumen.bc23.update.detail')}}" method="post" id="createForm">
            @csrf
            <div class="card-body fixed-height">
                <div class="row mt-5">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Id</label>
                            <input type="text" class="form-control" name="id" value="{{$dok->id ?? ' '}}">
                            <input type="hidden" class="form-control" name="idm" value="{{$dok->idm ?? ' '}}">
                        </div>
                        <div class="form-group">
                            <label for="">No Dokumen</label>
                            <input type="text" class="form-control" name="no_dok_inout" value="{{$dok->no_dok_inout ?? ' '}}">
                        </div>
                        <div class="form-group">
                            <label for="">Tgl Dokumen</label>
                            <input type="date" class="form-control" name="tgl_dok_inout" value="{{ !empty($dok->tgl_dok_inout) ?\Carbon\Carbon::createFromFormat('d/m/Y', $dok->tgl_dok_inout)->format('Y-m-d') : ' ' }}">            
                        </div>
                        <div class="form-group">
                            <label for="">No BC11</label>
                            <input type="text" class="form-control" name="no_bc11" value="{{$dok->no_bc11 ?? ' '}}">
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal BC11</label>
                            <input type="date" class="form-control" name="tgl_bc11" value="{{ !empty($dok->tgl_bc11) ?\Carbon\Carbon::parse($dok->tgl_bc11)->format('Y-m-d') : ' '}}">                        
                        </div>
                        <div class="form-group">
                            <label for="">No POS BC 11</label>
                            <input type="text" class="form-control" name="no_pos_bc11" value="{{$dok->no_pos_bc11 ?? ' '}}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Nama Angkut</label>
                            <input type="text" class="form-control" name="nm_angkut" value="{{$dok->nm_angkut ?? ' '}}">
                        </div>
                        <div class="form-group">
                            <label for="">No Voy Filght</label>
                            <input type="text" class="form-control" name="no_voy_flight" value="{{$dok->no_voy_flight ?? ' '}}">
                        </div>
                        <div class="form-group">
                            <label for="">Jumlash Container</label>
                            <input type="number" class="form-control" name="jml_cont" value="{{$dok->jml_cont ?? ' '}}">
                        </div>
                        <div class="form-group">
                            <label for="">No BL AWB</label>
                            <input type="text" class="form-control" name="no_bl_awb" value="{{$dok->no_bl_awb ?? ' '}}">
                        </div>
                        <div class="form-group">
                            <label for="">Tgl BL AWB</label>
                            <input type="date" class="form-control" name="tgl_bl_awb" value="{{ !empty($dok->tgl_bl_awb) ?\Carbon\Carbon::parse($dok->tgl_bl_awb)->format('Y-m-d') : ' ' }}">                        
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('dokumen.sppb.index')}}" class="btn btn-outline-danger">Back</a>
                <button class="btn btn-warning" id="submitButton" type="button">Update</button>
            </div>
        </form>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-header">
            <strong>List Container</strong>
        </div>
        <div class="card-body">
            <table class="tabelCustom table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Container No</th>
                        <th>Ukuran</th>
                        <th>Jenis Muat</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach($conts as $cont)
                   <tr>
                        <td>{{$cont->id}}</td>
                        <td>{{$cont->no_cont}}</td>
                        <td>{{$cont->size}}</td>
                        <td>{{$cont->jns_muat}}</td>
                   </tr>
                   @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-header">
            <strong>List KMS</strong>
        </div>
        <div class="card-body">
            <table class="tabelCustom table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jenis KMS</th>
                        <th>Merk KMS</th>
                        <th>Jumlah KMS</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach($kmss as $kms)
                   <tr>
                        <td>{{$kms->id}}</td>
                        <td>{{$kms->jns_kms}}</td>
                        <td>{{$kms->merk_kms}}</td>
                        <td>{{$kms->jml_kms}}</td>
                   </tr>
                   @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
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