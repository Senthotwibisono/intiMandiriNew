@extends('partial.main')

@section('content')
<section>
    <div class="card">
        <form action="{{ route('dokumen.sppb.update.detail')}}" method="post" id="createForm">
            @csrf
            <div class="card-body fixed-height">
                <div class="row mt-5">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Car</label>
                            <input type="text" class="form-control" name="car" value="{{$dok->car}}">
                            <input type="hidden" class="form-control" name="id" value="{{$dok->id}}">
                        </div>
                        <div class="form-group">
                            <label for="">No SPPB</label>
                            <input type="text" class="form-control" name="no_sppb" value="{{$dok->no_sppb}}">
                        </div>
                        <div class="form-group">
                            <label for="">Tgl SPPB</label>
                            <input type="date" class="form-control" name="tgl_sppb" value="{{ \Carbon\Carbon::parse($dok->tgl_sppb)->format('Y-m-d') }}">                        
                        </div>
                        <div class="form-group">
                            <label for="">No SPK</label>
                            <input type="text" class="form-control" name="no_spk" value="{{$dok->no_spk}}">
                        </div>
                        <div class="form-group">
                            <label for="">Kode Kantor Pengawas</label>
                            <input type="text" class="form-control" name="kd_kantor_pengawas" value="{{$dok->kd_kantor_pengawas}}">
                        </div>
                        <div class="form-group">
                            <label for="">Kode Kantor Bongkar</label>
                            <input type="text" class="form-control" name="kd_kantor_bongkar" value="{{$dok->kd_kantor_bongkar}}">
                        </div>
                        <div class="form-group">
                            <label for="">No PIB</label>
                            <input type="text" class="form-control" name="no_pib" value="{{$dok->no_pib}}">
                        </div>
                        <div class="form-group">
                            <label for="">Tgl PIB</label>
                            <input type="date" class="form-control" name="tgl_pib" value="{{ \Carbon\Carbon::parse($dok->tgl_pib)->format('Y-m-d') }}">                        
                        </div>
                        <div class="form-group">
                            <label for="">Nama Importir</label>
                            <input type="text" class="form-control" name="nama_imp" value="{{$dok->nama_imp}}">
                        </div>
                        <div class="form-group">
                            <label for="">NPWP Importir</label>
                            <input type="text" class="form-control" name="npwp_imp" value="{{$dok->npwp_imp}}">
                        </div>
                        <div class="form-group">
                            <label for="">Alamat Importir</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" name="alamat_imp" rows="3">{{$dok->alamat_imp}}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="">Nama PPJK</label>
                            <input type="text" class="form-control" name="nama_ppjk" value="{{$dok->nama_ppjk}}">
                        </div>
                        <div class="form-group">
                            <label for="">NPWP PPJK</label>
                            <input type="text" class="form-control" name="npwp_ppjk" value="{{$dok->npwp_ppjk}}">
                        </div>
                        <div class="form-group">
                            <label for="">Alamat PPJK</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" name="alamat_ppjk" rows="3">{{$dok->alamat_ppjk}}</textarea>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Nama Angkut</label>
                            <input type="text" class="form-control" name="nm_angkut" value="{{$dok->nm_angkut}}">
                        </div>
                        <div class="form-group">
                            <label for="">No Voy Filght</label>
                            <input type="text" class="form-control" name="no_voy_flight" value="{{$dok->no_voy_flight}}">
                        </div>
                        <div class="form-group">
                            <label for="">Bruto</label>
                            <input type="number" class="form-control" name="bruto" value="{{$dok->bruto}}">
                        </div>
                        <div class="form-group">
                            <label for="">Netto</label>
                            <input type="number" class="form-control" name="netto" value="{{$dok->netto}}">
                        </div>
                        <div class="form-group">
                            <label for="">Gudang</label>
                            <input type="text" class="form-control" name="gudang" value="{{$dok->gudang}}">
                        </div>
                        <div class="form-group">
                            <label for="">Status Jalur</label>
                            <input type="text" class="form-control" name="status_jalur" value="{{$dok->status_jalur}}">
                        </div>
                        <div class="form-group">
                            <label for="">Jumlash Container</label>
                            <input type="number" class="form-control" name="jml_cont" value="{{$dok->jml_cont}}">
                        </div>
                        <div class="form-group">
                            <label for="">No BC11</label>
                            <input type="text" class="form-control" name="no_bc11" value="{{$dok->no_bc11}}">
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal BC11</label>
                            <input type="date" class="form-control" name="tgl_bc11" value="{{ \Carbon\Carbon::parse($dok->tgl_bc11)->format('Y-m-d') }}">                        
                        </div>
                        <div class="form-group">
                            <label for="">No POS BC 11</label>
                            <input type="text" class="form-control" name="no_pos_bc11" value="{{$dok->no_pos_bc11}}">
                        </div>
                        <div class="form-group">
                            <label for="">No BL AWB</label>
                            <input type="text" class="form-control" name="no_bl_awb" value="{{$dok->no_bl_awb}}">
                        </div>
                        <div class="form-group">
                            <label for="">Tgl BL AWB</label>
                            <input type="date" class="form-control" name="tgl_bl_awb" value="{{ \Carbon\Carbon::parse($dok->tgl_bl_awb)->format('Y-m-d') }}">                        
                        </div>
                        <div class="form-group">
                            <label for="">No MBL AWB</label>
                            <input type="text" class="form-control" name="no_master_bl_awb" value="{{$dok->no_master_bl_awb}}">
                        </div>
                        <div class="form-group">
                            <label for="">Tgl MBL AWB</label>
                            <input type="date" class="form-control" name="tgl_master_bl_awb" value="{{ \Carbon\Carbon::parse($dok->tgl_master_bl_awb)->format('Y-m-d') }}">                        
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('dokumen.bc23.index')}}" class="btn btn-outline-danger">Back</a>
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
                        <th>CAR</th>
                        <th>Container No</th>
                        <th>Ukuran</th>
                        <th>Jenis Muat</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach($conts as $cont)
                   <tr>
                        <td>{{$cont->car}}</td>
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
                        <th>CAR</th>
                        <th>Jenis KMS</th>
                        <th>Merk KMS</th>
                        <th>Jumlah KMS</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach($kmss as $kms)
                   <tr>
                        <td>{{$kms->car}}</td>
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