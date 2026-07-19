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
                            <option value="{{$cont->id}}">{{$cont->nocontainer}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4">
                    <button type="button" class="btn btn-info" onClick="renderCard()"><i class="fas fa-search"></i></button>
                </div>
            </div>
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
@endsection