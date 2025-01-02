@extends('partial.main')
@section('custom_styles')
<style>
    .table-responsive td,
    .table-responsive th {
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
            <div class="row">
                <div class="col-auto ms-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual">Add Data</button>
                </div>
            </div>
            <br>
            <div class="table table-responsive">
                <table class="table table-hover table-striped" id="tableJob">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th class="text-center">No Job Order</th>
                            <th class="text-center">No SPK</th>
                            <th class="text-center">Forwarding</th>
                            <th class="text-center">No Container</th>
                            <th class="text-center">No MBL</th>
                            <th class="text-center">No PLP</th>
                            <th class="text-center">Tgl PLP</th>
                            <th class="text-center">Kd Kantor</th>
                            <th class="text-center">Kd TPS</th>
                            <th class="text-center">Kd TPS Asal</th>
                            <th class="text-center">Kd TPS Tujuan</th>
                            <th class="text-center">Nama Angkut</th>
                            <th class="text-center">No Voy</th>
                            <th class="text-center">No Surat</th>
                            <th class="text-center">No BC 11</th>
                            <th class="text-center">Tgl BC 11</th>
                            <th class="text-center">ETA</th>
                            <th class="text-center">Vessel</th>
                            <th class="text-center">UID</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="addManual" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Add Data Job Order</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('lcl.register.create')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body fixed-height">
                    <div class="row mt-5">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Nomor Job Order</label>
                                <input type="text" class="form-control" placeholder="Di isi otomatis" disabled>
                            </div>
                            <div class="form-group">
                                <label for="">No SPK</label>
                                <input type="text" class="form-control" name="nospk">
                            </div>
                            <div class="form-group">
                                <label for="">Forwarding</label>
                                <select name="forwarding_id" class="customSelect form-select select2" style="width: 100%">
                                    <option disabled selected value>Pilih Satu</option>
                                    @foreach($forwardings as $forwarding)
                                        <option value="{{$forwarding->id}}">{{$forwarding->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">No MBL</label>
                                <input type="text" class="form-control" name="nombl">
                            </div>
                            <div class="form-group">
                                <label for=""> Tanggal MBL</label>
                                <input type="date" class="form-control" name="tgl_master_bl">
                            </div>
                            <div class="form-group">
                                <label for="">Consolidator</label>
                                <select name="consolidator_id" id="" class="customSelect form-select select2" style="width: 100%;">
                                    <option value disabled selected value>Pilih Satu</option>
                                    @foreach($consolidators as $consol)
                                        <option value="{{$consol->id}}">{{$consol->namaconsolidator}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Negara</label>
                                <select name="negara_id" style="width: 100%;" class="customSelect form-select select2">
                                    <option value disabled selected>Pilih Satu</option>
                                    @foreach($negaras as $negara)
                                        <option value="{{$negara->id}}">{{$negara->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Port of Loading</label>
                                <select name="pelabuhan_id" style="width: 100%;" class="customSelect form-select select2">
                                    <option value disabled selected>Pilih Satu</option>
                                    @foreach($ports as $port)
                                        <option value="{{$port->id}}">{{$port->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Vessel</label>
                                <select name="vessel" style="width: 100%;" class="customSelect form-select select2">
                                    <option value disabled selected>Pilih Satu</option>
                                    @foreach($vessel as $ves)
                                        <option value="{{$ves->id}}">{{$ves->name}} -- {{$ves->call_sign}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Voy</label>
                                <input type="text" class="form-control" name="voy">
                            </div>
                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Estimate Arrival</label>
                                        <input type="date" class="form-control" name="eta">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Estimate Departure</label>
                                        <input type="date" class="form-control" name="etd">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Shipping Line</label>
                                <select name="shipping_id" id="" class="customSelect form-select select2" style="width: 100%;">
                                    <option value disabled selected>Pilih Satu!</option>
                                    @foreach($ships as $ship)
                                        <option value="{{$ship->id}}">{{$ship->shipping_line}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Lokasi Sandar</label>
                                <select name="lokasisandar_id" id="" class="customSelect form-select select2" style="width: 100%;">
                                    <option value disabled selected>Pilih Satu!</option>
                                    @foreach($loks as $lok)
                                        <option value="{{$lok->id}}">{{$lok->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Gudang Tujuan</label>
                                <select name="gudang_tujuan" id="" class="customSelect form-select select2" style="width: 100%;">
                                    <option value disabled selected>Pilih Satu!</option>
                                    @foreach($gudangs as $gudang)
                                        <option value="{{$gudang->id}}">{{$gudang->nama_gudang}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Kegiatan</label>
                                <input type="text" name="jeniskegiatan" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Total HBL</label>
                                <input type="text" name="jumlahhbl" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Keterangan</label>
                                <textarea class="form-control" name="keterangan" rows="3"></textarea>
                            </div>
                            <div class="row mt-5">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Pelabuhan Muat</label>
                                        <select name="pel_muat" id="" class="customSelect form-select select2" style="width: 100%;">
                                            <option value disabled selected>Pilih Satu!</option>
                                            @foreach($ports as $port)
                                                <option value="{{$port->id}}">{{$port->kode}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Pelabuhan Bongkar</label>
                                        <select name="pel_bongkar" id="" class="customSelect form-select select2" style="width: 100%;">
                                            <option value disabled selected>Pilih Satu!</option>
                                            @foreach($ports as $port)
                                                <option value="{{$port->id}}">{{$port->kode}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                    <button type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('custom_js')
<script>
    $(document).ready(function(){
        $('#tableJob').DataTable({
            precessing: true,
            serverSide: true,
            ajax: '/lcl/registerData',
            columns: [
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
                { data: 'nojoborder', name: 'nojoborder' },
                { data: 'nospk', name: 'nospk' },
                { data: 'forwarding', name: 'forwarding' },
                { data: 'nocontainer', name: 'nocontainer' },
                { data: 'nombl', name: 'nombl' },
                { data: 'no_plp', name: 'no_plp' },
                { data: 'tgl_plp', name: 'tgl_plp' },
                { data: 'kd_kantor', name: 'kd_kantor' },
                { data: 'kd_tps', name: 'kd_tps' },
                { data: 'kd_tps_asal', name: 'kd_tps_asal' },
                { data: 'kd_tps_tujuan', name: 'kd_tps_tujuan' },
                { data: 'nm_angkut', name: 'nm_angkut' },
                { data: 'no_voy_flight', name: 'no_voy_flight' },
                { data: 'no_surat', name: 'no_surat' },
                { data: 'no_bc11', name: 'no_bc11' },
                { data: 'tgl_bc11', name: 'tgl_bc11' },
                { data: 'eta', name: 'eta' },
                { data: 'Kapal_name', name: 'Kapal_name' },
                { data: 'user_name', name: 'user_name' }
            ]
        })
    });
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
                    url: '/lcl/register/barcodeGate',
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