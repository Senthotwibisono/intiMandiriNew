@extends('partial.main')
@section('custom_styles')
<style>
    .highlight-yellow {
        background-color: yellow !important;;
    }
</style>

<style>
    .highlight-blue {
        background-color: lightblue !important;;
    }
</style>

<style>
    .highlight-red {
        background-color: red !important;;
    }
</style>
@endsection
@section('content')
<section>
    <div class="card">
        <div class="card-body">
            <br>
            <div class="table">
                <table class="table-hover table-stripped" id="tableGateOut">
                    <thead style="white-space: nowrap;">
                        <tr>
                            <th>Edit</th>
                            <th>Photo</th>
                            <th>Barcode</th>
                            <th>Status BC</th>
                            <th>Segel Merah</th>
                            <th>Alasan Segel</th>
                            <th>Waktu Segel</th>
                            <th>Alasan Lepas Segel</th>
                            <th>Waktu Lepas Segel</th>
                            <th>No Job Order</th>
                            <th>No SPK</th>
                            <th>No Container</th>
                            <th>No BL</th>
                            <th>Tgl BL</th>
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
    </div>
</section>
<section>
    <div class="card">
        <div class="card-header">
            <strong>Form Input Gate In Data</strong>
        </div>
        <form action="{{ route('fcl.delivery.gateOutFCL')}}" id="updateForm" method="post" enctype="multipart/form-data">
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
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Kode Dokumen</label>
                                    <select name="kd_dok" id="kd_dok" class="js-example-basic-single form-select select2" style="width: 100%;">
                                        <option disabeled selected value>Pilih Satu!</option>
                                        @foreach($doks as $dok)
                                            <option value="{{$dok->id}}">{{$dok->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">No Dokumen</label>
                                    <input type="text" name="no_dok" id="no_dok" class="form-control">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Tgl Dokumen</label>
                                    <div class="input-group">
                                        <input type="date" name="tgl_dok" id="tgl_dok" class="form-control">
                                        <button type="button" class="btn btn-primary" id="" onclick="searchDok()"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
            </div>
        </form>
    </div>
</section>
@endsection

@section('custom_js')

<script>
    function searchDok() {
        const id = document.getElementById('id').value;
        const kode = document.getElementById('kd_dok').value;
        const noDok = document.getElementById('no_dok').value; 
        const tglDok = document.getElementById('tgl_dok').value; 
        console.log(id + kode + noDok + tglDok);
        const fields = [
            { id: 'id', message: 'Pilih Container Terlebih Dahulu!' },
            { id: 'kd_dok', message: 'Kode Dokumen harus diisi!' },
            { id: 'no_dok', message: 'Nomor Dokumen harus diisi!' },
            { id: 'tgl_dok', message: 'Tanggal Dokumen harus diisi!' }
        ];

        for (let field of fields) {
            const value = document.getElementById(field.id).value.trim();
            if (!value) {
                Swal.fire({
                    icon: 'error',
                    title: field.message,
                });
                return; // Hentikan eksekusi jika ada yang kosong
            }
        }

        Swal.fire({
            icon: 'warning',
            title: 'Apakah anda sudah yakin?',
            showCancelButton: true,
        }).then( (result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'in Progress',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: async () => {
                        Swal.showLoading();
                        try {
                            let response = await fetch('{{route('fcl.delivery.searchDokumenGate')}}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    id: id,
                                    kode: kode,
                                    noDok:noDok,
                                    tglDok:tglDok,
                                })
                            });
                            console.log(response);

                            if (response.ok) {
                               const result = await response.json();
                               console.log(result);
                               if (result.success == true) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: result.message,
                                    }).then(() => {
                                        location.reload();
                                    });
                               }else{
                                    Swal.fire({
                                        icon: 'error',
                                        title: result.message,
                                    }).then(() => {
                                        location.reload();
                                    });
                               }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Opss Something wrong ...',
                                    text: 'error in : ' + response.status + ' ' + response.statusText,
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        } catch (error) {
                            console.error(error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan!',
                                text: 'Tidak dapat menghubungi server. Coba lagi nanti.',
                            });
                        }
                    }
                })
            }
        })
    }
</script>

<script>
    $(document).ready(function(){
        $('#tableGateOut').dataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: '{{route('fcl.delivery.dataGateOutFCL')}}',
            columns: [
                {className:'text-center', name:'edit', data:'edit', searchable:false, orderable:false},
                {className:'text-center', name:'detil', data:'detil', searchable:false, orderable:false},
                {className:'text-center', name:'printBarcode', data:'printBarcode', searchable:false, orderable:false},
                {className:'text-center', name:'status_bc', data:'status_bc'},
                {className:'text-center', data:'flag_segel_merah', name:'flag_segel_merah'},
                {className:'text-center', data:'alasan_segel', name:'alasan_segel'},
                {className:'text-center', data:'tanggal_segel_merah', name:'tanggal_segel_merah'},
                {className:'text-center', data:'alasan_lepas_segel', name:'alasan_lepas_segel'},
                {className:'text-center', data:'tanggal_lepas_segel_merah', name:'tanggal_lepas_segel_merah'},
                {className:'text-center', name:'job.nojoborder', data:'job.nojoborder'},
                {className:'text-center', name:'job.nospk', data:'job.nospk'},
                {className:'text-center', name:'nocontainer', data:'nocontainer'},
                {className:'text-center', name:'nobl', data:'nobl'},
                {className:'text-center', name:'tgl_bl_awb', data:'tgl_bl_awb'},
                {className:'text-center', name:'tglmasuk', data:'tglmasuk'},
                {className:'text-center', name:'jammasuk', data:'jammasuk'},
                {className:'text-center', name:'tglkeluar', data:'tglkeluar'},
                {className:'text-center', name:'jamkeluar', data:'jamkeluar'},
                {className:'text-center', name:'user.name', data:'user.name'},
            ],
            rowCallback: function (row, data, dataIndex) {
                if (data.flag_segel_merah === 'Y') {
                    $(row).addClass('highlight-red text-white');
                } else if (data.status_bc === 'HOLD') {
                    $(row).addClass('highlight-yellow');
                } else if (data.status_bc === 'release'){
                    $(row).addClass('highlight-blue');
                }
            },
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
    $.ajax({
      type: 'GET',
      url: '/fcl/realisasi/placementEdit-' + id,
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
        $("#kd_dok").val(response.data.kd_dok_inout).trigger('change');
        $("#no_dok").val(response.data.no_dok);
        $("#tgl_dok").val(response.data.tgl_dok);
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
                    url: '/fcl/delivery/gatePassBonMuat',
                    data: { id: containerId },
                    cache: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Generated!', '', 'success')
                                .then(() => {
                                    var barcodeId = response.data.id;
                                    window.open('/barcode/autoGate-bonmuat' + barcodeId, '_blank', 'width=600,height=800');
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