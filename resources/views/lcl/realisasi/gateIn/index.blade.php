@extends('partial.main')
@section('custom_style')
<style>
    .table-fixed td,
    .table-fixed th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection
@section('content')
<section>
    <div class="card">
        <div class="card-body">
            <br>
            <div class="table table-fixed">
                <table class="table table-hover table-stripped" id="tableGateIn">
                    <thead>
                        <tr>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Detil</th>
                            <th class="text-center">No Job Order</th>
                            <th class="text-center">No Container</th>
                            <th class="text-center">No SPK</th>
                            <th class="text-center">No MBL</th>
                            <th class="text-center">DO</th>
                            <th class="text-center">Tgl Disapatche</th>
                            <th class="text-center">Jam Disapatche</th>
                            <th class="text-center">ETA</th>
                            <th class="text-center">Vessel</th>
                            <th class="text-center">Seal</th>
                            <th class="text-center">UID</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="card">
        <div class="card-header">
            <strong>Form Input Gate In Data</strong>
        </div>
        <form action="{{ route('lcl.gateIn.update')}}" id="updateForm" method="post" enctype="multipart/form-data">
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
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="photos">Pilih Foto-foto</label>
                                    <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                                </div>
                            </div>
                            <div class="col-6">
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
                    <div class="col-6">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Tgl Masuk</label>
                                    <input type="date" class="form-control" name="tglmasuk" id="tglmasuk">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="for-group">
                                    <label for="">Jam Masuk</label>
                                    <input type="time" class="form-control" name="jammasuk" id="jammasuk">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Nomor Polisi</label>
                            <input type="text" name="nopol" id="nopol" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Seal</label>
                            <select name="no_seal" id="no_seal"  class="js-example-basic-single form-select select2" style="width: 100%;">
                                <option value disabled selected>Pilih Satu</option>
                                @foreach($seals as $seal)
                                    <option value="{{$seal->id}}">{{$seal->code}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Petugas Lapangan</label>
                            <input type="text" id="nameUid"value="{{$user}}" class="form-control" readonly>
                            <input type="hidden" name="uidmasuk" id="uidmasuk" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-outline-danger" id="cancelButton">Cancel</button>
                <button type="button" class="btn btn-outline-success updateButton" id="updateButton">Submit</button>        
            </div>
        </form>
    </div>
</section>
@endsection

@section('custom_js')

<script>
    $(document).ready(function(){
        $('#tableGateIn').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/lcl/realisasi/dataSeal',
            columns: [
                {data:'edit', name: 'edit', className: 'text-center'},
                {data:'detil', name: 'detil', className: 'text-center'},
                {data:'joborder', name: 'joborder', className: 'text-center'},
                {data:'nocontainer', name: 'nocontainer', className: 'text-center'},
                {data:'nospk', name: 'nospk', className: 'text-center'},
                {data:'nombl', name: 'nombl', className: 'text-center'},
                {data:'doId', name: 'doId', className: 'text-center'},
                {data:'tglDispatche', name: 'tglDispatche', className: 'text-center'},
                {data:'jam_dispatche', name: 'jam_dispatche', className: 'text-center'},
                {data:'eta', name: 'eta', className: 'text-center'},
                {data:'nameKapal', name: 'nameKapal', className: 'text-center'},
                {data:'code', name: 'code', className: 'text-center'},
                {data:'name', name: 'name', className: 'text-center'},

            ]
        })
    });
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
    $.ajax({
      type: 'GET',
      url: '/lcl/realisasi/gateIn-edt' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $("#nospk").val(response.job.nospk);
        $("#nocontainer").val(response.data.nocontainer);
        $("#id").val(response.data.id);
        $("#size").val(response.data.size);
        $("#weight").val(response.data.weight);
        $("#tglmasuk").val(response.data.tglmasuk);
        $("#jammasuk").val(response.data.jammasuk);
        $("#nopol").val(response.data.nopol);
        $("#no_seal").val(response.data.no_seal).trigger('change');
        $("#uidmasuk").val(response.data.uid.id ?? response.userId);
        $("#nameUid").val(response.uid.name ?? response.user);
      },
      error: function(data) {
        console.log('error:', data)
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
@endsection