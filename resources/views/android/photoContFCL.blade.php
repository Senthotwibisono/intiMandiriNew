@extends('partial.android.main')

@section('content')
<section>
    <div class="card">
        <form action="{{ route('photo.fcl.storeContainer')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Select Container</label>
                        <select name="id" id="cont" style="width:100%;" class="js-example-basic-single">
                            <option disabled selected>Pilih Container Dahulu !!</option>
                            @foreach($conts as $cont)
                                <option value="{{$cont->id}}">{{$cont->nocontainer}} -- {{$cont->job->nojoborder}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="">Nopol Masuk</label>
                        <input type="text" name="nopol" id="nopol" class="form-control">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="">Status Beacukai</label>
                        <input type="text" id="status_bc" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="">Nopol Keluar</label>
                        <input type="text" name="nopol_mty" id="nopol_mty" class="form-control">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="">Tanggal Keluar</label>
                        <input type="date" name="tglkeluar" id="tglkeluar" class="form-control">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="">Jam Keluar</label>
                        <input type="time" name="jamkeluar" id="jamkeluar" class="form-control">
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
                                <option disabled selected value>Pilih Satu Kegiatan!</option>
                                <option value="gate-in">Gate In</option>
                                <option value="gate-out">Gate Out</option>
                                <option value="stripping">stripping</option>
                                <option value="placement">placement</option>
                            </select>
                        </div>
                    <div class="form-group">
                        <label for="">Keterangan</label>
                        <select name="detil" id="detilPhoto" style="width:100%;" class="js-example-basic-single select2 form-select">
                            <option disabled selected>PIlih Kegiatan Terlebih Dahulu!</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">List Photo Taken</label>
                        <textarea name="" class="form-control" id="photoTaken" cols="30" rows="10" readonly>

                        </textarea>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="button-container">
                    <button class="btn btn-success" type="submit">Submit</button>
                    <!-- <a href="javascript:void(0)" class="btn btn-sm btn-info photo"><i class="fa fa-eye"></i></a> -->
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
            url: '/getContainerLclKeterangan',
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
$(document).ready(function() {
    // Attach an event handler for the change event on the element with ID #cont
    $(document).on('change', '#cont', function() {
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
        // Retrieve the ID from the data-id attribute
        let id = $(this).val();
        $('.photo').attr('onclick', `openWindow('/lcl/report/contPhoto${id}')`);

        // Perform an AJAX GET request
        $.ajax({
            type: 'GET',
            url: '/android/fcl/searchCont' + id, // Correct URL path with a slash before id
            cache: false,
            data: {
                id: id
            },
            dataType: 'json',

            success: function(response) {
                Swal.close();
                Swal.fire({
                    title: 'success!',
                    text: 'Data di Temukan',
                    icon: 'success',
                    confirmButton: 'Ok',
                })
                // Log the response to the console
                console.log(response);

                // Populate form fields with data from the response
                $("#tglstripping").val(response.data.tglstripping);
                $("#jamstripping").val(response.data.jamstripping);
                $("#endstripping").val(response.data.endstripping);
                $("#nopol").val(response.data.nopol);
                $("#nopol_mty").val(response.data.nopol_mty);
                $("#jamkeluar").val(response.data.jamkeluar);
                $("#tglkeluar").val(response.data.tglkeluar);
                $("#status_bc").val(response.data.status_bc);
                let photoList = response.listPhoto.map(photo => photo.detil).join('\n');
                $("#photoTaken").val(photoList);
            },

            error: function(data) {
              console.log('error:', data)
            }
        });
    });
});
</script>

<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=600,height=800');
    }
</script>

@endsection