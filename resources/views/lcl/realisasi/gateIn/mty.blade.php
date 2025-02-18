@extends('partial.main')
@section('custom_styles')
<style>
    .highlight-yellow {
        background-color: yellow !important;
    }
</style>
@endsection
@section('content')
<section>
    <div class="card">
        <div class="card-body fixed-height-cardBody">
            <br>
            <div class="table"></div>
            <table class="table-hover" id="tableDetil">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Detil</th>
                        <th>Barcode</th>
                        <th>Status BC</th>
                        <th>No Job Order</th>
                        <th>No SPK</th>
                        <th>No Container</th>
                        <th>No MBL</th>
                        <th>Tgl Masuk</th>
                        <th>Jam Masuk</th>
                        <th>Tgl Keluar</th>
                        <th>Jam Keluar</th>
                        <th>UID</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>
<section>
    <div class="card">
        <div class="card-header">
            <strong>Form Input Gate In Data</strong>
        </div>
        <form action="{{ route('lcl.mty.update')}}" id="updateForm" method="post" enctype="multipart/form-data">
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
                                    <label for="">Tgl Keluar</label>
                                    <input type="date" class="form-control" name="tglkeluar" id="tglkeluar">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="for-group">
                                    <label for="">Jam Keluar</label>
                                    <input type="time" class="form-control" name="jamkeluar" id="jamkeluar">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Nomor Polisi</label>
                            <input type="text" name="nopol_mty" id="nopol_mty" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Tujuan MTY</label>
                            <select name="tujuan_mty" id="tujuan_mty" class="js-example-basic-single form-select select2" style="width: 100%;">
                                <option disabled selected value>Pilih Satu!</option>
                                @foreach($deps as $dep)
                                    <option value="{{$dep->id}}">{{$dep->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Petugas Lapangan</label>
                            <input type="text" id="nameUid"value="{{$user}}" class="form-control" readonly>
                            <input type="hidden" name="uidmty" id="uidmty" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-outline-danger" id="cancelButton">Cancel</button>
                <button type="button" class="btn btn-outline-success updateButton" id="updateButton">Submit</button>        
                <button type="button" class="btn btn-outline-warning" id="suratJalanButton">Cetak Surat Jalan</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('custom_js')
<script>
    $(document).ready(function(){
        $('#tableDetil').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/lcl/realisasi/emptyTable',
            columns: [
                {data:'edit', name:'edit', className:'text-center'},
                {data:'detil', name:'detil', className:'text-center'},
                {data:'barcode', name:'barcode', className:'text-center'},
                {data:'status_bc', name:'status_bc', className:'text-center'},
                {data:'joborder', name:'joborder', className:'text-center'},
                {data:'nospk', name:'nospk', className:'text-center'},
                {data:'nocontainer', name:'nocontainer', className:'text-center'},
                {data:'nombl', name:'nombl', className:'text-center'},
                {data:'tglmasuk', name:'tglmasuk', className:'text-center'},
                {data:'jammasuk', name:'jammasuk', className:'text-center'},
                {data:'tglkeluar', name:'tglkeluar', className:'text-center'},
                {data:'jamkeluar', name:'jamkeluar', className:'text-center'},
                {data:'user', name:'user', className:'text-center'},
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.highlight === 'highlight-yellow') {
                    $(row).addClass('highlight-yellow');
                }
            }
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
    $(document).ready(function(){
        $('#suratJalanButton'). click(function(){
            var containerId =  $("#id").val();
            console.log("Id Container untuk Surat Jalan = " + containerId);

            if (containerId == null || containerId == '') {
                Swal.fire('Error', 'Anda belum memilih container, harap pilih container terlebih dahulu', 'error');
            } else{
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Apakah anda yakin menerbitakn surat jalan untuk container ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
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
    
                        window.open('/lcl/realisasi/suratJalan-BuangEmpty' + containerId, "preview bon muat","width=600,height=600,menubar=no,status=no,scrollbars=yes");
                        swal.close();
                    }
                });
            }

        })
    })
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
        $("#tglkeluar").val(response.data.tglkeluar);
        $("#jamkeluar").val(response.data.jamkeluar);
        $("#nopol_mty").val(response.data.nopol_mty);
        $("#tujuan_mty").val(response.data.tujuan_mty).trigger('change');
        $("#uidmty").val(response.data.uid.id ?? response.userId);
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