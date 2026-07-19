@extends('partial.android.main')

@section('content')

<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h4>Pilih Container Terlebih Dahulu!!!</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-8">
                    <select name="" id="container_id" class="js-example-basic-single" style="width: 100%; height:100%">
                        <option disabled selected value>Pilih Satu</option>
                        @foreach($containers as $cont)
                            <option value="{{$cont->id}}"  {{ $cont->id == $behandle->id ? 'selected' : '' }}>{{$cont->nocontainer}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4">
                    <button type="button" class="btn btn-info" onClick="renderCard()"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            <h4>Data Container {{$behandle->nocontainer ?? ''}}</h4>
        </div>
        <div class="card-body">
            <div class="row mb-5">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Nomor Container</label>
                        <input type="text" name="nocontainer" id="nocontainer" class="form-control" readonly value="{{$behandle->nocontainer}}">
                        <input type="hidden" name="id" id="id_container" class="form-control" value="{{$behandle->id}}">
                    <div class="form-group">
                        <label for="">Date Ready Behandle</label>
                        <div class="input-group">
                            <input type="datetime-local" class="form-control" name="date_ready_behandle" id="date_ready_behandle" value="{{$behandle->date_ready_behandle}}">
                            <button type="button" class="btn btn-warning" onclick="$('#date_ready_behandle').val('')"> Clear </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Date Check Behandle</label>
                                <div class="input-group">
                                    <input type="datetime-local" class="form-control" id="date_check_behandle" value="{{$behandle->date_check_behandle}}">
                                    <button type="button" class="btn btn-warning" onclick="$('#date_check_behandle').val('')"> Clear </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Desc Check Behandle</label>
                                <textarea class="form-control" name="" id="desc_check_behandle" cols="30" rows="10">
                                    {{$behandle->desc_check_behandle}}
                                </textarea>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Date Finish Behandle</label>
                                <div class="input-group">
                                    <input type="datetime-local" class="form-control" id="date_finish_behandle" value="{{$behandle->date_finish_behandle}}">
                                    <button type="button" class="btn btn-warning" onclick="$('#date_finish_behandle').val('')"> Clear </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">Desc Finish Behandle</label>
                                <textarea class="form-control" name="" id="desc_finish_behandle" cols="30" rows="10">
                                    {{$behandle->desc_finish_behandle}}
                                </textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-0">
                        <div class="form-group">
                            <label for="photos">Pilih Foto-foto</label>
                            <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="">Keterangan</label>
                            <select name="detil" id="detilPhoto" style="width:100%;" class="js-example-basic-single select2 form-select">
                                <option disabled selected value>Pilih Satu</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">List Photo Taken</label>
                            <textarea name="" class="form-control" id="photoTaken" cols="30" rows="10" readonly>
                                @foreach($take as $tk)
                                    {{$tk}}
                                @endforeach
                            </textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-success" onClick="submitBehandle()">Submit</button>
            <a href="javascript:void(0)" class="btn btn-sm btn-info photo"><i class="fa fa-eye"></i></a>
        </div>
    </div>
</div>

@endsection

@section('custom_js')
<script>
    async function renderCard() {
        showLoading()
        const container_id = document.getElementById('container_id').value;
        var hasil;
        if (!container_id) {
            hasil = {
                message: 'Pilih Container Terlebih Dahulu',
            };
            hideLoading();
            return errorHasil(hasil);
        }
        const data = {container_id};
        const url = "{{ route('android.behandle.searchContainer') }}";
        const response = await globalResponse(data, url);
        hideLoading();
        if (response.ok) {
            const hasil = await response.json();
            if (hasil.success) {
                showLoading();
                window.location.href = "/android/fcl/behandle/detil/" + container_id;
            }else{
                return errorHasil(hasil);
            }
        }else{
            return errorResponse(response);
        }
    }
</script>

<script>
$(document).ready(function(){
    getContainerLclKeterangan();
})

function getContainerLclKeterangan() {
    let kegiatan = 'behandle';

    console.log('kegiatan = ' + kegiatan);

    Swal.fire({
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

        success: function(response) {
            Swal.close();

            Swal.fire({
                title: 'Success!',
                text: 'Data ditemukan',
                icon: 'success',
                confirmButtonText: 'OK'
            });

            $('#detilPhoto')
                .empty()
                .append('<option disabled selected>Pilih Satu!</option>');

            Object.values(response).forEach(function(detil) {
                $('#detilPhoto').append(
                    `<option value="${detil}">${detil}</option>`
                );
            });
        },

        error: function(data) {
            console.log(data);

            Swal.fire({
                title: 'Error',
                text: 'Data tidak ditemukan',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
}
</script>

<script>
    async function submitBehandle() {
        const result = await confirmation();
        if (result.isConfirmed) {
            showLoading();
            const data = new FormData();

            data.append('nocontainer', document.getElementById('nocontainer').value);
            data.append('id', document.getElementById('id_container').value);
            data.append('date_ready_behandle', document.getElementById('date_ready_behandle').value);
            data.append('date_check_behandle', document.getElementById('date_check_behandle').value);
            data.append('desc_check_behandle', document.getElementById('desc_check_behandle').value);
            data.append('date_finish_behandle', document.getElementById('date_finish_behandle').value);
            data.append('desc_finish_behandle', document.getElementById('desc_finish_behandle').value);
            data.append('detilPhoto', document.getElementById('detilPhoto').value);

            const photos = document.getElementById('photos').files;

            for (let i = 0; i < photos.length; i++) {
                data.append('photos[]', photos[i]);
            }

            const url = "{{ route('android.behandle.post') }}";
            const response = await globalResponse(data, url);
            hideLoading();
            if (response.ok) {
                const hasil = await response.json();
                if (hasil.success) {
                    return successHasil(hasil);
                }else{
                    return errorHasil(hasil);
                }
            }else{
                errorResponse(response);
            }
        }else{
            return;
        }
    }

    $(document).on('click', '.photo', function () {
        const id = $('#id_container').val();
    
        if (!id) {
            return Swal.fire({
                icon: 'warning',
                title: 'Container belum dipilih'
            });
        }
    
        window.open('/fcl/behandle-detail/' + id, '_blank', 'width=600,height=800');
    });
</script>
@endsection