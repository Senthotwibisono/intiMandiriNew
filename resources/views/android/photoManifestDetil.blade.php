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
                </div>
                <div class="row mt-0">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="photos">Pilih Foto-foto</label>
                            <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Kegiatan</label>
                            <select name="action" id="" style="width: 100%;" class="js-example-basic-single form-select select2">
                                <option value="stripping">Stripping</option>
                                <option value="placement">Placement</option>
                                <option value="gate_in">Gate In</option>
                                <option value="gate_out">Gate Out</option>
                            </select>
                        </div>
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
    function openWindow(url) {
        window.open(url, '_blank', 'width=600,height=800');
    }
</script>

@endsection