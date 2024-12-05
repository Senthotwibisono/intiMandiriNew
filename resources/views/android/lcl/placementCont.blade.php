@extends('partial.android.main')

@section('content')
<section>
    <div class="card">
        <form action="{{ route('placementCont.lcl.update')}}" method="post" enctype="multipart/form-data">
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
                            <label for="">No SPK</label>
                            <input type="text" name="nospk" id="nospk" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Size</label>
                            <input type="text" name="size" id="size" class="form-control" readonly>
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
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Yard Block</label>
                                <select name="yard_id" id="yard_id" style="width:100%;" class="js-example-basic-single select2 form-select">
                                    <option disabled selected>Pilih Satu!</option>
                                    @foreach($yards as $yard)
                                        <option value="{{$yard->id}}">{{$yard->yard_block}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Yard Slot</label>
                                <select name="slot" id="yard_slot" style="width:100%;" class="js-example-basic-single select2 form-select">
                                    <option disabled selected>Pilih Block Terlebih Dahulu!</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Yard Row</label>
                                <select name="row" id="yard_row" style="width:100%;" class="js-example-basic-single select2 form-select">
                                    <option disabled selected>Pilih Slot Terlebih Dahulu!</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Yard Tier</label>
                                <select name="tier" id="yard_tier" style="width:100%;" class="js-example-basic-single select2 form-select">
                                    <option disabled selected>Pilih Row Terlebih Dahulu!</option>
                                </select>
                            </div>
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
    let slotValue, rowValue, tierValue;

    // When Yard Block changes
    $('#yard_id').on('change', function() {
        let yardId = $(this).val();
        $('#yard_slot').empty().append('<option disabled selected>Loading...</option>');
        $('#yard_row').empty().append('<option disabled selected>Pilih Slot Terlebih Dahulu!</option>');
        $('#yard_tier').empty().append('<option disabled selected>Pilih Row Terlebih Dahulu!</option>');

        if (yardId) {
            $.ajax({
                url: '/get/slot',
                type: 'GET',
                data: { yard_id: yardId },
                success: function(response) {
                    $('#yard_slot').empty().append('<option disabled selected>Pilih Satu!</option>');
                    Object.values(response).forEach(function(slot) {
                        $('#yard_slot').append(`<option value="${slot}">${slot}</option>`);
                    });

                    if (slotValue) {
                        $('#yard_slot').val(slotValue).trigger('change');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    });

    // When Yard Slot changes
    $('#yard_slot').on('change', function() {
        let slotId = $(this).val();
        let yardId = $('#yard_id').val();
        $('#yard_row').empty().append('<option disabled selected>Loading...</option>');
        $('#yard_tier').empty().append('<option disabled selected>Pilih Row Terlebih Dahulu!</option>');

        if (slotId) {
            $.ajax({
                url: '/get/row',
                type: 'GET',
                data: { slot: slotId, yard_id: yardId },
                success: function(response) {
                    $('#yard_row').empty().append('<option disabled selected>Pilih Satu!</option>');
                    Object.values(response).forEach(function(row) {
                        $('#yard_row').append(`<option value="${row}">${row}</option>`);
                    });

                    if (rowValue) {
                        $('#yard_row').val(rowValue).trigger('change');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    });

    // When Yard Row changes
    $('#yard_row').on('change', function() {
        let rowId = $(this).val();
        let slotId = $('#yard_slot').val();
        let yardId = $('#yard_id').val();
        $('#yard_tier').empty().append('<option disabled selected>Loading...</option>');

        if (rowId) {
            $.ajax({
                url: '/get/tier',
                type: 'GET',
                data: { slot: slotId, yard_id: yardId, row: rowId },
                success: function(response) {
                    $('#yard_tier').empty().append('<option disabled selected>Pilih Satu!</option>');
                    Object.values(response).forEach(function(tier) {
                        $('#yard_tier').append(`<option value="${tier}">${tier}</option>`);
                    });

                    if (tierValue) {
                        $('#yard_tier').val(tierValue).trigger('change');
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    });
    

    // Edit button click event
    $(document).on('change', '#cont', function() {
        let id = $(this).val();

        $.ajax({
            type: 'GET',
            url: '/lcl/realisasi/placementEdit-' + id,
            cache: false,
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                $("#nospk").val(response.job.nospk);
                $("#size").val(response.data.size);
                $("#weight").val(response.data.weight);

                if (response.data.yard_id) {
                    slotValue = response.slot;
                    rowValue = response.row;
                    tierValue = response.tier;

                    $("#yard_id").val(response.data.yard_id).trigger('change');
                } else {
                    $('#yard_id').val(null).append('<option disabled selected>Pilih Satu</option>');
                    $('#yard_slot').empty().append('<option disabled selected>Pilih Block Terlebih Dahulu!</option>');
                    $('#yard_row').empty().append('<option disabled selected>Pilih Slot Terlebih Dahulu!</option>');
                    $('#yard_tier').empty().append('<option disabled selected>Pilih Row Terlebih Dahulu!</option>');
                }
            },
            error: function(data) {
                console.log('error:', data);
            }
        });
    });
});
</script>

@endsection