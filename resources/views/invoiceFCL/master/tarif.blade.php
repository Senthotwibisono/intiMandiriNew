@extends('partial.main')

@section('custom_styles');
<style>
    
</style>
@endsection

@section('content')

<body>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="heder text-center">
                        <h4>Tarif Terminal</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tabel">
                        <table class="table table-hover" id="tarifTerminal">
                            <thead style="white-space: nowrap;">
                                <tr>
                                    <th>Edit</th>
                                    <th>TPS</th>
                                    <th>Size Container</th>
                                    <th>Type Container</th>
                                    <th>Tarif Dasar Penumpukan</th>
                                    <th>Massa II</th>
                                    <th>Massa III</th>
                                    <th>Lift On</th>
                                    <th>Hydro Scan</th>
                                    <th>Nota dan Perawatan IT</th>
                                    <th>Econ</th>
                                    <th>Gate Pass</th>
                                    <th>Pelayanan Tambahan</th>
                                    <th>Refeer (Power for Refeer)</th>
                                    <th>Refeer (Monitooring)</th>
                                    <th>Surcharge</th>
                                    <th>Admin</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Last Update</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="container-button">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addManual" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Add Data Tarif Terminal</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('invoice.fcl.createTarifTPS') }}" method="POST" enctype="multipart/form-data" id="tarifTPSForm">
                        @csrf
                    <div class="row">
                        <div class="divider divider-left">
                            <div class="divider-text">
                                <h6>Data Container</h6>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="">Lokasi Sandar</label>
                                <select name="lokasi_sandar_id" id="" class="customSelect select2 form-select" style="width:100%" required> 
                                    <option disabled selected value>Pilih Satu!!</option>
                                    @foreach($lokasiSandar as $ls)
                                        <option value="{{$ls->id}}">{{$ls->kd_tps_asal}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="">Container Size</label>
                                <select name="size" id="" class="customSelect select2 form-select" style="width:100%" required> 
                                    <option disabled selected value>Pilih Satu!!</option>
                                    <option value="20">20</option>
                                    <option value="40">40</option>
                                    <option value="45">45</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Jenis Container</label>
                                <select class="customSelect form-control select2" name="type" style="width: 100%;" required>
                                    <option disabled selected value>Choose Jenis Container</option>
                                    <option value="DRY">DRY</option>
                                    <option value="BB">BB</option>
                                    <option value="OH">OH</option>
                                 </select>
                            </div>
                        </div>
                    </div>
                    <!-- Input Tarif -->
                     <div class="row">
                        <div class="divider divider-left">
                            <div class="divider-text">
                                <h6>Data Tarif</h6>
                            </div>
                        </div>
                        <!-- penumpukkan -->
                        <div class="row">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    Tarif Penumpukkan
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Tarif Dasar Penumpukkan</label>
                                    <input type="number" name="tarif_dasar_massa" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Persentase Massa II</label>
                                    <input type="number" name="massa2" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Persentase Massa III</label>
                                    <input type="number" name="massa3" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                        </div>

                        <!-- Refeer-->
                        <div class="row">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    Tarif Refeer & Surcharge
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Refeer</label>
                                    <input type="number" name="refeer" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Monitoring</label>
                                    <input type="number" name="monitoring" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Shurcharge</label>
                                    <input type="number" name="surcharge" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                        </div>

                        <!-- Umum -->
                         <div class="row">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    Tarif Umum
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Lift On</label>
                                    <input type="number" name="lift_on" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Hyro Scan</label>
                                    <input type="number" name="hyro_scan" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Nota & Perawatan IT</label>
                                    <input type="number" name="perawatan_it" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Econ</label>
                                    <input type="number" name="econ" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Paket Pelayanan Tambahan</label>
                                    <input type="number" name="pelayanan_tambahan" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Gate Pass</label>
                                    <input type="number" name="gate_pass" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Admin</label>
                                    <input type="number" name="admin" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                         </div>
                     </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row mb-5">
                        <div class="container-button">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                            <button type="button" id="postTarifTPS" class="btn btn-primary ml-1"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCust" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Update Data Tarif Terminal</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('invoice.fcl.updateTarifTPS') }}" method="POST" enctype="multipart/form-data" id="tarifTPSUpdateForm">
                        @csrf
                    <div class="row">
                        <div class="divider divider-left">
                            <div class="divider-text">
                                <h6>Data Container</h6>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="">Lokasi Sandar</label>
                                <select name="lokasi_sandar_id" id="lokasi_sandar_id_edit" class="editSelect select2 form-select" style="width:100%" required> 
                                    <option disabled selected value>Pilih Satu!!</option>
                                    @foreach($lokasiSandar as $ls)
                                        <option value="{{$ls->id}}">{{$ls->kd_tps_asal}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="id" id="id_edit" readonly>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="">Container Size</label>
                                <select name="size" id="size_edit" class="editSelect select2 form-select" style="width:100%" required> 
                                    <option disabled selected value>Pilih Satu!!</option>
                                    <option value="20">20</option>
                                    <option value="40">40</option>
                                    <option value="45">45</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>Jenis Container</label>
                                <select class="editSelect form-control select2" name="type" id="type_edit" style="width: 100%;" required>
                                    <option disabled selected value>Choose Jenis Container</option>
                                    <option value="DRY">DRY</option>
                                     <option value="BB">BB</option>
                                     <option value="OH">OH</option>
                                 </select>
                            </div>
                        </div>
                    </div>
                    <!-- Input Tarif -->
                     <div class="row">
                        <div class="divider divider-left">
                            <div class="divider-text">
                                <h6>Data Tarif</h6>
                            </div>
                        </div>
                        <!-- penumpukkan -->
                        <div class="row">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    Tarif Penumpukkan
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Tarif Dasar Penumpukkan</label>
                                    <input type="number" name="tarif_dasar_massa" id="tarif_dasar_massa_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Persentase Massa II</label>
                                    <input type="number" name="massa2" id="massa2_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Persentase Massa III</label>
                                    <input type="number" name="massa3" id="massa3_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                        </div>

                        <!-- Refeer-->
                        <div class="row">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    Tarif Refeer
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Refeer</label>
                                    <input type="number" name="refeer" id="refeer_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Monitoring</label>
                                    <input type="number" name="monitoring" id="monitoring_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Surcharge</label>
                                    <input type="number" name="surcharge" id="surcharge_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                        </div>

                        <!-- Umum -->
                         <div class="row">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    Tarif Umum
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Lift On</label>
                                    <input type="number" name="lift_on" id="lift_on_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Hyro Scan</label>
                                    <input type="number" name="hyro_scan" id="hyro_scan_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Nota & Perawatan IT</label>
                                    <input type="number" name="perawatan_it" id="perawatan_it_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Econ</label>
                                    <input type="number" name="econ" id="econ_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Paket Pelayanan Tambahan</label>
                                    <input type="number" name="pelayanan_tambahan" id="pelayanan_tambahan_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Gate Pass</label>
                                    <input type="number" name="gate_pass" id="gate_pass_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Admin</label>
                                    <input type="number" name="admin" id="admin_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                         </div>
                     </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row mb-5">
                        <div class="container-button">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                            <button type="button" id="updateTarifTPS" class="btn btn-primary ml-1"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarif WMS -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="heder text-center">
                        <h4>Tarif WMS</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tabel">
                        <table class="table table-hover" id="tarifWMS">
                            <thead style="white-space: nowrap;">
                                <tr>
                                    <th>Edit</th>
                                    <th>Size Container</th>
                                    <th>Type Container</th>
                                    <th>Paket PLP</th>
                                    <th>Behandle</th>
                                    <th>Tarif Dasar Penumpukan</th>
                                    <th>Massa</th>
                                    <th>Lift On</th>
                                    <th>Lift Off</th>
                                    <th>Gate Pass</th>
                                    <th>Refeer (Power for Refeer)</th>
                                    <th>Refeer (Monitooring)</th>
                                    <th>Surcharge</th>
                                    <th>Admin</th>
                                    <th>Admin Behandle</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Last Update</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="container-button">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTarifWMS"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addTarifWMS" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Add Data Tarif WMS</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('invoice.fcl.createTarifWMS') }}" method="POST" enctype="multipart/form-data" id="tarifWMSForm">
                        @csrf
                    <div class="row">
                        <div class="divider divider-left">
                            <div class="divider-text">
                                <h6>Data Container</h6>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Container Size</label>
                                <select name="size" id="" class="customSelectWMS select2 form-select" style="width:100%" required> 
                                    <option disabled selected value>Pilih Satu!!</option>
                                    <option value="20">20</option>
                                    <option value="40">40</option>
                                    <option value="45">45</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Jenis Container</label>
                                <select class="customSelectWMS form-control select2" name="type" style="width: 100%;" required>
                                <option disabled selected value>Choose Jenis Container</option>
                                    <option value="DRY">DRY</option>
                                    <option value="BB">BB</option>
                                    <option value="OH">OH</option>
                                 </select>
                            </div>
                        </div>
                    </div>
                    <!-- Input Tarif -->
                     <div class="row">
                        <div class="divider divider-left">
                            <div class="divider-text">
                                <h6>Data Tarif</h6>
                            </div>
                        </div>
                        <!-- penumpukkan -->
                        <div class="row">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    Tarif Penumpukkan
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Tarif Dasar Penumpukkan</label>
                                    <input type="number" name="tarif_dasar_massa" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Persentase Massa</label>
                                    <input type="number" name="massa" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                        </div>

                        <!-- Refeer-->
                        <div class="row">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    Tarif Refeer
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Refeer</label>
                                    <input type="number" name="refeer" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Monitoring</label>
                                    <input type="number" name="monitoring" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Surcharge</label>
                                    <input type="number" name="surcharge" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                        </div>

                        <!-- Umum -->
                         <div class="row">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    Tarif Umum
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Paket PLP</label>
                                    <input type="number" name="paket_plp" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Behandle</label>
                                    <input type="number" name="behandle" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Lift On</label>
                                    <input type="number" name="lift_on" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Lift Off</label>
                                    <input type="number" name="lift_off" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Gate Pass</label>
                                    <input type="number" name="gate_pass" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Admin</label>
                                    <input type="number" name="admin" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Admin Behandle</label>
                                    <input type="number" name="admin_behandle" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                         </div>
                     </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row mb-5">
                        <div class="container-button">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                            <button type="button" id="postTarifWMS" class="btn btn-primary ml-1"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editCustWMS" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Edit Data Tarif WMS</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('invoice.fcl.updateTarifWMS') }}" method="POST" enctype="multipart/form-data" id="tarifWMSUpdateForm">
                        @csrf
                    <div class="row">
                        <div class="divider divider-left">
                            <div class="divider-text">
                                <h6>Data Container</h6>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Container Size</label>
                                <select name="size" id="size_edit" class="editelectWMS select2 form-select" style="width:100%" required> 
                                    <option disabled selected value>Pilih Satu!!</option>
                                    <option value="20">20</option>
                                    <option value="40">40</option>
                                    <option value="45">45</option>
                                </select>
                                <input type="hidden" name="id" id="id_edit" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Jenis Container</label>
                                <select class="editelectWMS form-control select2" name="type" id="type_edit" style="width: 100%;" required>
                                    <option disabled selected value>Choose Jenis Container</option>
                                    <option value="DRY">DRY</option>
                                    <option value="BB">BB</option>
                                    <option value="OH">OH</option>
                                 </select>
                            </div>
                        </div>
                    </div>
                    <!-- Input Tarif -->
                     <div class="row">
                        <div class="divider divider-left">
                            <div class="divider-text">
                                <h6>Data Tarif</h6>
                            </div>
                        </div>
                        <!-- penumpukkan -->
                        <div class="row">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    Tarif Penumpukkan
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Tarif Dasar Penumpukkan</label>
                                    <input type="number" name="tarif_dasar_massa" id="tarif_dasar_massa_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Persentase Massa</label>
                                    <input type="number" name="massa" id="massa_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                        </div>

                        <!-- Refeer-->
                        <div class="row">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    Tarif Refeer
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Refeer</label>
                                    <input type="number" name="refeer" id="refeer_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Monitoring</label>
                                    <input type="number" name="monitoring" id="monitoring_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Surcharge</label>
                                    <input type="number" name="surcharge" id="surcharge_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                        </div>

                        <!-- Umum -->
                         <div class="row">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    Tarif Umum
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Paket PLP</label>
                                    <input type="number" name="paket_plp" id="paket_plp_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Behandle</label>
                                    <input type="number" name="behandle" id="behandle_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Lift On</label>
                                    <input type="number" name="lift_on" id="lift_on_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Lift Off</label>
                                    <input type="number" name="lift_off" id="lift_off_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Gate Pass</label>
                                    <input type="number" name="gate_pass" id="gate_pass_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Admin</label>
                                    <input type="number" name="admin" id="admin_edit" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Admin Behandle</label>
                                    <input type="number" name="admin_behandle" id="admin_behandle" class="form-control" step="0.001" min="0" max="9999999999.999">
                                </div>
                            </div>
                         </div>
                     </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="row mb-5">
                        <div class="container-button">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                            <button type="button" id="updateTarifWMS" class="btn btn-primary ml-1"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

@endsection

@section('custom_js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('postTarifTPS').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Pastikan Data yang Anda Masukkan sudah Benar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    document.getElementById('tarifTPSForm').submit();
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('updateTarifTPS').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Pastikan Data yang Anda Masukkan sudah Benar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    document.getElementById('tarifTPSUpdateForm').submit();
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('postTarifWMS').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Pastikan Data yang Anda Masukkan sudah Benar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    document.getElementById('tarifWMSForm').submit();
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('updateTarifWMS').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Pastikan Data yang Anda Masukkan sudah Benar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    document.getElementById('tarifWMSUpdateForm').submit();
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function(){
        $('#tarifTerminal').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollY: "500px", // Atur tinggi tabel sesuai kebutuhan
            scrollCollapse: true,
            ajax: '/invoiceFCL/masterTarif/dataTarifTPS',
            columns: [
                {data:'edit', name:'edit'},
                {data:'lokasiSandar', name:'lokasiSandar'},
                {data:'size', name:'size'},
                {data:'type', name:'type'},
                {data:'tarif_dasar_massa', name:'tarif_dasar_massa', className:'nowrap', render: formatRupiah},
                {data:'massa2', name:'massa2', className:'nowrap', render: formatPersen},
                {data:'massa3', name:'massa3', className:'nowrap', render: formatPersen},
                {data:'lift_on', name:'lift_on', className:'nowrap', render: formatRupiah},
                {data:'hyro_scan', name:'hyro_scan', className:'nowrap', render: formatRupiah},
                {data:'perawatan_it', name:'perawatan_it', className:'nowrap', render: formatRupiah},
                {data:'econ', name:'econ', className:'nowrap', render: formatRupiah},
                {data:'gate_pass', name:'gate_pass', className:'nowrap', render: formatRupiah},
                {data:'pelayanan_tambahan', name:'pelayanan_tambahan', className:'nowrap', render: formatRupiah},
                {data:'refeer', name:'refeer', className:'nowrap', render: formatRupiah},
                {data:'monitoring', name:'monitoring', className:'nowrap', render: formatRupiah},
                {data:'surcharge', name:'surcharge', className:'nowrap', render: formatPersen},
                {data:'admin', name:'admin', className:'nowrap', render: formatRupiah},
                {data:'uid', name:'uid'},
                {data:'created_at', name:'created_at'},
                {data:'last_update', name:'last_update'},
            ],
        });
    });
</script>

<script>
    function limitDecimal(input) {
        if (input.value.includes(".")) {
            let parts = input.value.split(".");
            if (parts[1].length > 3) {
                input.value = parseFloat(input.value).toFixed(3);
            }
        }
    }

    function formatRupiah(data, type, row) {
        if (type === 'display' || type === 'filter') {
            if (!data) return 'Rp. 0,00'; // Jika data kosong
            return 'Rp. ' + parseFloat(data).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        return data; // Untuk sorting tetap menggunakan angka asli
    }

    function formatPersen(data, type, row) {
        if (type === 'display' || type === 'filter') {
            if (!data) return '0,00%'; // Jika data kosong
            return parseFloat(data).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%';
        }
        return data;
    }

    $('<style>')
    .prop('type', 'text/css')
    .html('.nowrap { white-space: nowrap; }')
    .appendTo('head');
</script>

<script>
    $(document).on('click', '.editTarifTPS', function(){
        let id = $(this).data('id');
        console.log('ID terpilih adalah ' + id);
        swal.showLoading();
        $.ajax({
            type: 'GET',
            url: '/invoiceFCL/masterTarif/editTarif' + id,
            cache: false,
            data: {
              id: id
            },
            dataType: 'json',
            
            success: function(response){
                swal.close();

                console.log(response);
                $('#editCust').modal('show');
                $('#lokasi_sandar_id_edit').val(response.data.lokasi_sandar_id).trigger('change');
                $('#id_edit').val(response.data.id);
                $('#size_edit').val(response.data.size).trigger('change');
                $('#type_edit').val(response.data.type).trigger('change');
                $('#tarif_dasar_massa_edit').val(response.data.tarif_dasar_massa);
                $('#massa2_edit').val(response.data.massa2);
                $('#massa3_edit').val(response.data.massa3);
                $('#refeer_edit').val(response.data.refeer);
                $('#surcharge_edit').val(response.data.surcharge);
                $('#monitoring_edit').val(response.data.monitoring);
                $('#lift_on_edit').val(response.data.lift_on);
                $('#hyro_scan_edit').val(response.data.hyro_scan);
                $('#perawatan_it_edit').val(response.data.perawatan_it);
                $('#econ_edit').val(response.data.econ);
                $('#gate_pass_edit').val(response.data.gate_pass);
                $('#pelayanan_tambahan_edit').val(response.data.pelayanan_tambahan);
                $('#admin_edit').val(response.data.admin);
            },
            error: function(response){
                swal.fire({
                    icon: 'error',
                    text: 'Something Wrong: ' + response.responseJSON?.message,
                    title: 'Error',
                });
            }
        })
    })
</script>

<script>
    $(document).ready(function(){
        $('#tarifWMS').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollY: "500px", // Atur tinggi tabel sesuai kebutuhan
            scrollCollapse: true,
            ajax: '/invoiceFCL/masterTarif/dataTarifWMS',
            columns: [
                {data:'edit', name:'edit'},
                {data:'size', name:'size'},
                {data:'type', name:'type'},
                {data:'paket_plp', name:'paket_plp', className:'nowrap', render: formatRupiah},
                {data:'behandle', name:'behandle', className:'nowrap', render: formatRupiah},
                {data:'tarif_dasar_massa', name:'tarif_dasar_massa', className:'nowrap', render: formatRupiah},
                {data:'massa', name:'massa', className:'nowrap', render: formatPersen},
                {data:'lift_on', name:'lift_on', className:'nowrap', render: formatRupiah},
                {data:'lift_off', name:'lift_off', className:'nowrap', render: formatRupiah},
                {data:'gate_pass', name:'gate_pass', className:'nowrap', render: formatRupiah},
                {data:'refeer', name:'refeer', className:'nowrap', render: formatRupiah},
                {data:'monitoring', name:'monitoring', className:'nowrap', render: formatRupiah},
                {data:'surcharge', name:'surcharge', className:'nowrap', render: formatPersen},
                {data:'admin', name:'admin', className:'nowrap', render: formatRupiah},
                {data:'admin_behandle', name:'admin_behandle', className:'nowrap', render: formatRupiah},
                {data:'user.name', name:'user.name'},
                {data:'created_at', name:'created_at'},
                {data:'last_update', name:'last_update'},
            ],
        });
    });
</script>

<script>
    $(document).on('click', '.editTarifWMS', function(){
        let id = $(this).data('id');
        console.log('ID terpilih adalah ' + id);
        swal.showLoading();
        $.ajax({
            type: 'GET',
            url: '/invoiceFCL/masterTarif/editTarifWMS/' + id,
            cache: false,
            data: {
              id: id
            },
            dataType: 'json',
            
            success: function(response){
                swal.close();

                console.log(response);
                $('#editCustWMS').modal('show');
                $('#editCustWMS #size_edit').val(response.data.size).trigger('change');
                $('#editCustWMS #id_edit').val(response.data.id);
                $('#editCustWMS #type_edit').val(response.data.type).trigger('change');
                $('#editCustWMS #tarif_dasar_massa_edit').val(response.data.tarif_dasar_massa);
                $('#editCustWMS #massa_edit').val(response.data.massa);
                $('#editCustWMS #refeer_edit').val(response.data.refeer);
                $('#editCustWMS #surcharge_edit').val(response.data.surcharge);
                $('#editCustWMS #monitoring_edit').val(response.data.monitoring);
                $('#editCustWMS #paket_plp_edit').val(response.data.paket_plp);
                $('#editCustWMS #behandle_edit').val(response.data.behandle);
                $('#editCustWMS #lift_on_edit').val(response.data.lift_on);
                $('#editCustWMS #lift_off_edit').val(response.data.lift_off);
                $('#editCustWMS #gate_pass_edit').val(response.data.gate_pass);
                $('#editCustWMS #admin_edit').val(response.data.admin);
                $('#editCustWMS #admin_behandle').val(response.data.admin_behandle);
            },
            error: function(response){
                swal.fire({
                    icon: 'error',
                    text: 'Something Wrong: ' + response.responseJSON?.message,
                    title: 'Error',
                });
            }
        })
    })
</script>
@endsection