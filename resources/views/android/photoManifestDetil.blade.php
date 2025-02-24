@extends('partial.android.main')

@section('content')
<section>
    <div class="card">
        <form action="{{ route('photo.lcl.storeManifest')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">No Tally</label>
                        <input type="text" name="notally" value="{{$manifest->notally}}" id="notally_edit" class="form-control" readonly>
                        <input type="hidden" name="id" value="{{$manifest->id}}" id="id_edit" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">No HBL</label>
                        <input type="text" name="nohbl" value="{{$manifest->nohbl}}" id="nohbl_edit" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Quantity</label>
                        <input type="text" name="quantity" value="{{$manifest->quantity}}" id="quantity_edit" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Danger Label</label>
                        <select class="form-select" name="dg_label" id="dg_label_edit">
                            <option value="N" {{ $manifest->dg_label == 'N' ? 'selected' : '' }}>N</option>
                            <option value="Y" {{ $manifest->dg_label == 'Y' ? 'selected' : '' }}>Y</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-0">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="photos">Pilih Foto-foto</label>
                            <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Kegiatan</label>
                        <select name="action" id="kegiatan" style="width: 100%;" class="js-example-basic-single form-select select2">
                            <option value disabled selected>Pilih Satu!</option>
                            <option value="stripping">Stripping</option>
                            <option value="behandle">Behandle</option>
                            <option value="gate-out">Gate Out</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Keterangan</label>
                        <select name="detil" id="detilPhoto" style="width:100%;" class="js-example-basic-single select2 form-select">
                            <option disabled selected>Pilih Kegiatan Terlebih Dahulu!</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">List Photo Taken</label>
                        <textarea name="" class="form-control" id="photoTaken" cols="30" rows="10" readonly>
                        @foreach($kegiatan as $keg)
                        {{ $keg }}:
                        @foreach($detil as $det)
                        @if($det->action == $keg)
                        - {{ $det->detil }}
                        @endif
                        @endforeach
                        
                        @endforeach
                        </textarea>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="button-container">
                    <button class="btn btn-success" type="submit">Submit</button>
                    <a href="javascript:void(0)" onclick="openWindow('/lcl/report/manifestPhoto{{$manifest->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
@section('custom_js')

<script>
$(document).ready(function(){
    $('#kegiatan').on('change', function(){
        let kegiatan = $(this).val();
        console.log('kegiatan = ' + kegiatan);
        swal.fire({
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
            type: 'GET',
            url: '/getManifestLclKeterangan',
            cache: false,
            data: {
              kegiatan: kegiatan
            },
            dataType: 'json',

            success: function(response){
                Swal.close();
                Swal.fire({
                    title: 'success!',
                    text: 'Data di Temukan',
                    icon: 'success',
                    confirmButton: 'Ok',
                })

                console.log(response);
                $('#detilPhoto').empty().append('<option disabled selected>Pilih Satu!</option>');
                    Object.values(response).forEach(function(detil) {
                    $('#detilPhoto').append(`<option value="${detil}">${detil}</option>`);
                });
            },
            error: function(data) {
              console.log('error:', data);
                  Swal.fire({
                      title: 'Error',
                      text: 'Data tidak ditemukan',
                      icon: 'error',
                      confirmButtonText: 'OK'
                  });
            }
        })
    })
})
</script>

<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=600,height=800');
    }
</script>

@endsection