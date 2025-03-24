@extends('partial.main')

@section('custom_styles')

@endsection

@section('content')

<section>
    <div class="page-content">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">No SPJM</label>
                                <input type="text" class="form-control" id="no_spjm" name="no_spjm">
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Tgl SPJM</label>
                                <div class="input-group mb-3">
                                    <input type="date" class="form-control" id="tgl_spjm" name="tgl_spjm">
                                    <button class="btn btn-primary" id="searchSPJM"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <select name="container_id[]" id="container_id" class="js-example-basic-multiple select2 form-control" multiple="mutilpe" placeholder="PilihSatu">
                        
                    </select>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('custom_js')

<script>
    $('#searchSPJM').on('click', function(){
        var no_spjm = $('#no_spjm').val();
        var tgl_spjm = $('#tgl_spjm').val();
        console.log('No & Tgl SPJM : ' + no_spjm + ', ' + tgl_spjm);
        Swal.showLoading();

        $.ajax({
            url: '{{ route('invoiceFCL.behandle.getContainer') }}',
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
                no_spjm : no_spjm,
                tgl_spjm : tgl_spjm,
            },
            cache: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                if (response.success) {
                    
                } else {
                    Swal.fire('Error', response.message, 'error')
                    .then(() => {
                        location.reload();
                    });
                }
            },
            error: function(response) {
                swal.fire({
                    icon: 'error',
                    text: 'Something Wrong: ' + response.responseJSON?.message,
                    title: 'Error',
                });
            }
        })
    })
</script>

@endsection