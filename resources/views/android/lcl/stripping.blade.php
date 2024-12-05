@extends('partial.android.main')

@section('content')
<section>
    <div class="card">
        <form action="{{ route('lcl.stripping.cont.update')}}" method="post" enctype="multipart/form-data">
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
                <div class="row mt-0">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Tgl Mulai Stripping</label>
                            <input type="date" class="form-control" name="tglstripping" id="tglstripping">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Jam Mulai Stripping</label>
                            <input type="time" class="form-control" name="jamstripping" id="jamstripping">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">End Stripping Date</label>
                            <input type="datetime-local" class="form-control" name="endstripping" id="endstripping">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="photos">Pilih Foto-foto</label>
                            <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Keterangan Photo</label>
                            <select name="keteranganPhoto" class="js-example-basic-single form-select select2" style="width: 100%;">
                                <option disabled selected value>Pilih Satu!</option>
                                @foreach($kets as $ket)
                                    <option value="{{$ket->keterangan}}">{{$ket->keterangan}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="button-container">
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
@section('custom_js')

<script>
$(document).ready(function() {
    // Attach an event handler for the change event on the element with ID #cont
    $(document).on('change', '#cont', function() {
        // Retrieve the ID from the data-id attribute
        let id = $(this).val();

        Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we update the container',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
        $.ajax({
            type: 'GET',
            url: '/android/searchCont' + id, // Correct URL path with a slash before id
            cache: false,
            data: {
                id: id
            },
            dataType: 'json',

            success: function(response) {
                // Log the response to the console
                console.log(response);
                Swal.close();
                if (response.data.status_ijin != 'Y') {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Belum Mendapat Ijin Bea Cukai',
                        icon: 'error',
                    }).then(() => {
                        location.reload();
                    });
                }else{
                    $("#tglstripping").val(response.data.tglstripping);
                    $("#jamstripping").val(response.data.jamstripping);
                    $("#endstripping").val(response.data.endstripping);
                }
            },

            error: function(data) {
              console.log('error:', data)
              let errorMessage = data.responseJSON && data.responseJSON.message 
                    ? data.responseJSON.message 
                    : 'Data Tidak di Temukan'; // Default message if no error message is provided
                        
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                }).then(() => {
                    location.reload(); // Reload the page after the alert is confirmed
                });
            }
        });
    });
});
</script>

@endsection