@extends('partial.main')
@section('custom_styles')
<style>
    .highlight-yellow {
        background-color: yellow !important;;
    }
</style>
@endsection
@section('content')
<section>
    <div class="card">
        <div class="card-body fixed-height-cardBody">
            <br>
            <table class="tabelCustom" style="overflow-x:auto;">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>No Job Order</th>
                        <th>No SPK</th>
                        <th>No Container</th>
                        <th>No MBL</th>
                        <th>Gate In</th>
                        <th>Stripping</th>
                        <th>Buang Empty</th>
                    </tr>
                    <tbody>
                        @foreach($conts as $cont)
                            <tr>
                                <td>
                                    <div class="button-container">
                                        <buttpn class="btn btn-outline-warning editButton" data-id="{{$cont->id}}"><i class="fa fa-pen"></i></buttpn>
                                        <a href="javascript:void(0)" onclick="openWindow('/lcl/report/contPhoto{{$cont->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                    </div>
                                </td>
                                <td>{{$cont->job->nojoborder}}</td>
                                <td>{{$cont->job->nospk}}</td>
                                <td>{{$cont->nocontainer}}</td>
                                <td>{{$cont->job->nombl}}</td>
                                <td>
                                    <a href="javascript:void(0)" onclick="openWindow('/lcl/realisasi/gateIn-detail{{$cont->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" onclick="openWindow('/lcl/realisasi/stripping-photoCont{{$cont->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" onclick="openWindow('/lcl/realisasi/mty-detail{{$cont->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </thead>
            </table>
        </div>
    </div>
</section>
<section>
    <div class="card">
        <div class="card-header text-center">
            <strong>Photo Container Form</strong>
        </div>
        <form action="{{ route('photo.fcl.storeContainer')}}" id="updateForm" method="post" enctype="multipart/form-data">
            <div class="card-body">
                @csrf
                <div class="row mt-2">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">No SPK</label>
                            <input type="text" name="nospk" id="nospk" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Container</label>
                            <input type="text" name="nocontainer" id="nocontainer" class="form-control" readonly>
                            <input type="hidden" name="id" id="id" class="form-control" readonly>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Size</label>
                                    <input type="text" name="size" id="size" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Weight</label>
                                    <input type="text" name="weight" id="weight" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
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
                            <label for="photos">Pilih Foto-foto</label>
                            <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-outline-danger" id="cancelButton">Cancel</button>
                <button class="btn btn-outline-success updateButton" id="updateButton">Submit</button>        
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
    // When Cancel button is clicked
    $('#cancelButton').click(function() {
        // Reload the current page
        location.reload();
    });
});
</script>

<script>
   $(document).on('click', '.editButton', function() {
    let id = $(this).data('id');
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
      url: '/fcl/realisasi/gateIn-edt' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {
        Swal.close();

        Swal.fire({
            title: 'Success!',
            text: 'Data berhasil diambil.',
            icon: 'success',
            confirmButtonText: 'OK'
        });

        console.log(response);
        $("#nospk").val(response.job.nospk);
        $("#nocontainer").val(response.data.nocontainer);
        $("#id").val(response.data.id);
        $("#size").val(response.data.size);
        $("#weight").val(response.data.weight);
        $("#tglkeluar").val(response.data.tglkeluar);
        $("#jamkeluar").val(response.data.jamkeluar);
        $("#nopol_mty").val(response.data.nopol_mty);
        $("#uidmty").val(response.data.uid.id ?? response.userId);
        $("#nameUid").val(response.uid.name ?? response.user);
      },
      error: function(data) {
        console.log('error:', data);
            Swal.fire({
                title: 'Error',
                text: 'Failed to fetch data',
                icon: 'error',
                confirmButtonText: 'OK'
            });
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
                text: "",
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

<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=600,height=800');
    }
</script>

<script>
    $(document).on('click', '.printBarcode', function(e) {
        e.preventDefault();
        var containerId = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        Swal.fire({
            icon: 'question',
            title: 'Do you want to generate the barcode?',
            showCancelButton: true,
            confirmButtonText: 'Generate',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '/lcl/realisasi/mty-barcodeGate',
                    data: { id: containerId },
                    cache: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Generated!', '', 'success')
                                .then(() => {
                                    var barcodeId = response.data.id;
                                    window.open('/barcode/autoGate-index' + barcodeId, '_blank', 'width=600,height=800');
                                });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(response) {
                        var errors = response.responseJSON.errors;
                        if (errors) {
                            var errorMessage = '';
                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: errorMessage,
                            });
                        } else {
                            Swal.fire('Error', 'An error occurred while processing your request', 'error');
                        }
                    },
                });
            }
        });
    });
</script>
@endsection