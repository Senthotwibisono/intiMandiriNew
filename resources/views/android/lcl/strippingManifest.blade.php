@extends('partial.android.main')

@section('content')
<section>
    <div class="card">
        <form action="{{ route('lcl.stripping.store')}}" id="updateForm" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Select Manifest</label>
                        <select name="id" id="cont" style="width:100%;" class="js-example-basic-single">
                            <option disabled selected>Pilih HBL Dahulu !!</option>
                            @foreach($mans as $man)
                                <option value="{{$man->id}}">{{$man->nohbl}} // {{$man->cont->nocontainer}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-0">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Start Stripping Date</label>
                            <input type="datetime-local" name="startstripping" id="startstripping_edit" class="form-control">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">Tgl Stripping</label>
                            <input type="date" name="tglstripping" id="tglstripping_edit" class="form-control">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">Jam Stripping</label>
                            <input type="time" name="jamstripping" id="jamstripping_edit" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">End Stripping Date</label>
                            <input type="datetime-local" name="endstripping" id="endstripping_edit" class="form-control">
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
                    <button class="btn btn-success" type="button" id="updateButton">Submit</button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
@section('custom_js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('updateButton').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Data detail barang akan reset ketika Quantity berubah Value",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
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
                    // Submit the form programmatically if confirmed
                    document.getElementById('updateForm').submit();
                }
            });
        });
    });
</script>
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
            url: '/lcl/manifest/edit-' + id, // Correct URL path with a slash before id
            cache: false,
            data: {
                id: id
            },
            dataType: 'json',

            success: function(response) {
                // Log the response to the console
                console.log(response);
                Swal.close();
                if (response.success == true) {
                    if (response.data.ijin_stripping != 'Y') {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Belum Mendapat Ijin Bea Cukai',
                            icon: 'error',
                        }).then(() => {
                            location.reload();
                        });
                    }else{
                        $("#nohbl_edit").val(response.data.nohbl);
                        $("#notally_edit").val(response.data.notally);
                        $("#quantity_edit").val(response.data.quantity);
                        $("#tglstripping_edit").val(response.data.tglstripping);
                        $("#jamstripping_edit").val(response.data.jamstripping);
                        $("#startstripping_edit").val(response.data.startstripping);
                        $("#endstripping_edit").val(response.data.endstripping);
                    }
                }else{
                    Swal.fire({
                            title: 'Error!',
                            text: 'Data Tidak di Temukan',
                            icon: 'error',
                        }).then(() => {
                            location.reload();
                        });
                }
            },

            error: function(data) {
              console.log('error:', data)
                Swal.fire({
                    title: 'Error!',
                    text: 'Data Tidak di Temukan',
                    icon: 'error',
                }).then(() => {
                    location.reload();
                });
            }
        });
    });
});
</script>

@endsection