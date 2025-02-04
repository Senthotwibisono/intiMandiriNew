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
        <div class="card-header">
            <h4><strong>Main Dokumen</strong></h4>
        </div>
        <form action="{{ route('fcl.register.update')}}" method="post">
            @csrf
            <div class="card-body fixed-height-card">
                <div class="row mt-5">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Nomor Job Order</label>
                            <input type="text" class="form-control" value="{{$job->nojoborder}}" disabled>
                            <input type="hidden" class="form-control" name="id" value="{{$job->id}}">
                        </div>
                        <div class="form-group">
                            <label for="">No SPK</label>
                            <input type="text" class="form-control" name="nospk" value="{{$job->nospk}}">
                        </div>
                        <div class="form-group">
                            <label for="">Forwarding</label>
                            <select name="forwarding_id" class="js-example-basic-single form-select select2" style="width: 100%">
                                <option disabled selected value>Pilih Satu</option>
                                @foreach($forwardings as $forwarding)
                                    <option value="{{$forwarding->id}}" {{$job->forwarding_id == $forwarding->id ? 'selected' : ''}}>{{$forwarding->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">No MBL</label>
                            <input type="text" class="form-control" name="nombl" value="{{$job->nombl}}">
                        </div>
                        <div class="form-group">
                            <label for=""> Tanggal MBL</label>
                            <input type="date" class="form-control" name="tgl_master_bl" value="{{$job->tgl_master_bl}}">
                        </div>
                        <div class="form-group">
                            <label for="">Consolidator</label>
                            <select name="consolidator_id" value="{{$job->consolidator_id}}" id="" class="js-example-basic-single form-select select2" style="width: 100%;">
                                <option value disabled selected value>Pilih Satu</option>
                                @foreach($consolidators as $consol)
                                    <option value="{{$consol->id}}" {{$job->consolidator_id == $consol->id ? 'selected' : ''}}>{{$consol->namaconsolidator}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Negara</label>
                            <select name="negara_id" value="{{$job->negara_id}}" style="width: 100%;" class="js-example-basic-single form-select select2">
                                <option value disabled selected>Pilih Satu</option>
                                @foreach($negaras as $negara)
                                    <option value="{{$negara->id}}" {{$job->negara_id == $negara->id ? 'selected' : ''}}>{{$negara->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Port of Loading</label>
                            <select name="pelabuhan_id" value="{{$job->pelabuhan_id}}" style="width: 100%;" class="js-example-basic-single form-select select2">
                                <option value disabled selected>Pilih Satu</option>
                                @foreach($ports as $port)
                                    <option value="{{$port->id}}" {{$job->pelabuhan_id == $port->id ? 'selected' : ''}}>{{$port->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Vessel</label>
                            <select name="vessel" value="{{$job->vessel_id}}" style="width: 100%;" class="js-example-basic-single form-select select2">
                                <option value disabled selected>Pilih Satu</option>
                                @foreach($vessel as $ves)
                                    <option value="{{$ves->id}}" {{$job->vessel == $ves->id ? 'selected' : ''}}>{{$ves->name}} -- {{$ves->call_sign}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row mt-5">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Voy</label>
                                    <input type="text" class="form-control" name="voy" value="{{$job->PLP->voy ?? $job->voy}}" placeholder="Data Belum Di isi">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Call Sign</label>
                                    <input type="text" class="form-control" value="{{$job->vessel->call_sign ?? $job->PLP->call_sign ?? $job->call_sign }}" placeholder="Data Belum Di isi">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Estimate Arrival</label>
                                    <input type="date" class="form-control" name="eta" value="{{$job->eta}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Estimate Departure</label>
                                    <input type="date" class="form-control" name="etd" value="{{$job->etd}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Shipping Line</label>
                            <select name="shipping_id" value="{{$job->shipping_id}}" id="" class="js-example-basic-single form-select select2" style="width: 100%;">
                                <option value disabled selected>Pilih Satu!</option>
                                @foreach($ships as $ship)
                                    <option value="{{$ship->id}}" {{$job->shipping_id == $ship->id ? 'selected' : ''}}>{{$ship->shipping_line}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Lokasi Sandar</label>
                            <select name="lokasisandar_id" value="{{$job->lokasisandar_id}}" id="" class="js-example-basic-single form-select select2" style="width: 100%;">
                                <option value disabled selected>Pilih Satu!</option>
                                @foreach($loks as $lok)
                                    <option value="{{$lok->id}}" {{$job->lokasisandar_id == $lok->id ? 'selected' : ''}}>{{$lok->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Gudang Tujuan</label>
                            <select name="gudang_tujuan" value="{{$job->gudang_tujuan}}" id="" class="js-example-basic-single form-select select2" style="width: 100%;">
                                <option value disabled selected>Pilih Satu!</option>
                                @foreach($gudangs as $gudang)
                                    <option value="{{$gudang->id}}" {{$job->gudang_tujuan == $gudang->id ? 'selected' : ''}}>{{$gudang->nama_gudang}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Kegiatan</label>
                            <input type="text" name="jeniskegiatan" value="{{$job->jeniskegiatan}}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Total HBL</label>
                            <input type="text" name="jumlahhbl" value="{{$job->jumlahhbl}}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="3">{{$job->keterangan}}</textarea>
                        </div>
                        <div class="row mt-5">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Pelabuhan Muat</label>
                                    <select name="pel_muat" value="{{$job->pel_muat}}" id="" class="js-example-basic-single form-select select2" style="width: 100%;">
                                        <option value disabled selected>Pilih Satu!</option>
                                        @foreach($ports as $port)
                                            <option value="{{$port->id}}" {{$job->pel_muat == $port->id ? 'selected' : ''}}>{{$port->kode}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Pelabuhan Bongkar</label>
                                    <select name="pel_bongkar" value="{{$job->pel_bongkar}}" id="" class="js-example-basic-single form-select select2" style="width: 100%;">
                                        <option value disabled selected>Pilih Satu!</option>
                                        @foreach($ports as $port)
                                            <option value="{{$port->id}}" {{$job->pel_bongkar == $port->id ? 'selected' : ''}}>{{$port->kode}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('fcl.register.index')}}" class="btn btn-outline-danger"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span></a>
                <button type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
            </div>
        </form>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-header">
            <h4>Dokumen PLP</h4>
        </div>
        <form action="{{ route('fcl.register.updatePLP')}}" method="post">
            @csrf
            <div class="card-body fixed-height-samll-card">
                <div class="row mt-5">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">No PLP</label>
                            <input type="text" name="noplp" value="{{$job->noplp}}" placeholder="Belum Diisi" class="form-control">
                            <input type="hidden" name="id" value="{{$job->id}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Tgl PLP</label>
                            <input type="date" name="ttgl_plp" value="{{$job->ttgl_plp ?? ''}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">No Bc11</label>
                            <input type="text" name="tno_bc11" value="{{$job->tno_bc11}}" placeholder="Di Isi Otomatis" class="form-control" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-header">
                <button type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
            </div>
        </form>
    </div>
</section>

<!-- Container -->
 <section>
    <div class="card">
        <div class="card-header">
            <h4><strong>Container Job</strong></h4>
        </div>
        <div class="card-body fixed-header-card">
            <table class="table table-hover" id="tableContainer">
                <thead>
                    <tr>
                        <th>Edit</th>
                        <th>Delete</th>
                        <th>No BL AWB</th>
                        <th>Tgl BL AWB</th>
                        <th>Container No</th>
                        <th>Container Size</th>
                        <th>Container Type</th>
                        <th>Teus</th>
                        <th>Seal</th>
                        <th>Weight</th>
                        <th>Measurement</th>
                        <th>Customer</th>
                        <th>Tgl Entry</th>
                        <th>UID</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual"><i class="fas fa-plus"></i></button>
                </div>
                <div class="col-auto">
                    <a href="/fcl/register/generateExcelPLP/{{$job->id}}" class="btn btn-success"><i class="fas fa-file-excel"></i> || Export to Excel</a>
                </div>
                <!-- <div class="col-auto">
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#excelModal"><i class="fas fa-file-excel"></i></button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#newFormatModal"><i class="fas fa-file-excel"></i> || New Format</button>
                </div> -->
            </div>
        </div>
    </div>
</section>

<!-- modal Container -->
<div class="modal fade" id="addManual" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-md"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Add Data Container</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <div class="modal-body">
                    <form action="/fcl/register/createContainer" method="POST" enctype="multipart/form-data" id="createContianerForm">
                        @csrf
                    <div class="row mb-5">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Nomor Container</label>
                                <input type="text" name="nocontainer" class="form-control">
                                <input type="hidden" name="joborder_id" value="{{$job->id}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Size</label>
                                <select name="size" id="" class="customSelect form-select select2" style="width: 100%;">
                                    <option value="20">20</option>
                                    <option value="40">40</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jenis Container</label>
                                <select class="customSelect form-control select2" name="ctr_type" style="width: 100%;" required>
                                    <option disabled selected value>Choose Jenis Container</option>
                                    <option value="Class BB Standar 3">Class BB Standar 3</option>
                                    <option value="Class BB Standar 8">Class BB Standar 8</option>
                                    <option value="Class BB Standar 9">Class BB Standar 9</option>
                                    <option value="Class BB Standar 4,1">Class BB Standar 4,1</option>
                                    <option value="Class BB Standar 4,2">Class BB Standar 4,2</option>   
				            		<option value="Class BB Standar 4,3">Class BB Standar 4,3</option>   
                                    <option value="Class BB Standar 6">Class BB Standar 6</option>
                                    <option value="Class BB Standar 2,2">Class BB Standar 2,2</option>
                                    <option value="Class BB Standar 2,3">Class BB Standar 2,3</option>    
                                    <option value="Class BB High Class 2,1">Class BB High Class 2,1</option>
                                    <option value="Class BB High Class 5,1">Class BB High Class 5,1</option>
                                    <option value="Class BB High Class 6,1">Class BB High Class 6,1</option>
                                    <option value="Class BB High Class 5,2">Class BB High Class 5,2</option>
                                    <option value="REEFER RF">REEFER RF</option>
                                    <option value="REEFER RECOOLING">REEFER RECOOLING</option>
				            		<option value="REEFER RECOOLING BB 3">REEFER RECOOLING BB 3</option>
				            		<option value="REEFER RECOOLING BB 8">REEFER RECOOLING BB 8</option>                           
				            		<option value="REEFER RECOOLING BB 6">REEFER RECOOLING BB 6</option>\		
				            		<option value="REEFER RECOOLING BB 9">REEFER RECOOLING BB 9</option>
				            		<option value="REEFER RECOOLING BB 2.1">REEFER RECOOLING BB 2.1</option>
				            		<option value="REEFER RECOOLING BB 2.2">REEFER RECOOLING BB 2.2</option>
				            		<option value="REEFER RECOOLING BB 2.3">REEFER RECOOLING BB 2.3</option>			
				            		<option value="REEFER RECOOLING BB 4.1">REEFER RECOOLING BB 4.1</option>
				            		<option value="REEFER RECOOLING BB 4.2">REEFER RECOOLING BB 4.2</option>
                                    <option value="REEFER RECOOLING BB 5.1">REEFER RECOOLING BB 5.1</option>
                                    <option value="REEFER RECOOLING BB 5.2">REEFER RECOOLING BB 5.2</option>
                                    <option value="REEFER RECOOLING BB 6.1">REEFER RECOOLING BB 6.1</option>
				            		<option value="FLAT TRACK RF">FLAT TRACK RF</option>
                                    <option value="FLAT TRACK OH">FLAT TRACK OH</option>
                                    <option value="FLAT TRACK OW">FLAT TRACK OW</option>
                                    <option value="FLAT TRACK OL">FLAT TRACK OL</option>
                                    <option value="DRY">DRY</option>
                                    <option value="OPEN TOP">OPEN TOP</option>
				            		<option value="OH">OH</option>
                                 </select>
                            </div>
                            <div class="form-group">
                                <label for="">Seal</label>
                                <input type="text" class="form-control" name="no_seal">
                            </div>
                            <div class="form-group">
                                <label for="">Weight</label>
                                <input type="text" class="form-control" name="weight">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Measurement</label>
                            <input type="text" class="form-control" name="meas">
                        </div>
                        <div class="form-group">
                            <label for="">No BL AWB</label>
                            <input type="text" name="nobl" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Tgl BL AWB</label>
                            <input type="date" name="tgl_bl_awb" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">ETA</label>
                            <input type="datetime-local" class="form-control" name="eta" id="">
                        </div>
                        <div class="form-group">
                            <label for="">Customer</label>
                            <select name="cust_id" id="" class="customSelect form-select selcet2" style="width: 100%;">
                                <option disabled selected value>Pilih Satu!</option>
                                @foreach($customer as $cust)
                                    <option value="{{$cust->id}}">{{$cust->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                <button type="button" id="createContianerButton" class="btn btn-primary ml-1" data-bs-dismiss="modal"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editCust" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Edit Data Container</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <div class="modal-body">
                    <form action="{{ route('fcl.register.updateCont')}}" method="POST" id="updateForm" enctype="multipart/form-data">
                        @csrf
                    <div class="row mb-5">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Nomor Container</label>
                                <input type="text" name="nocontainer" id="nocontainer_edit" class="form-control">
                                <input type="hidden" name="id" id="id_edit" class="form-control">
                                <input type="hidden" name="joborder_id" value="{{$job->id}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Size</label>
                                <select name="size" id="size_edit" class="editSelect form-select select2" style="width: 100%;">
                                    <option value="20">20</option>
                                    <option value="40">40</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jenis Container</label>
                                <select class="editSelect form-control select2" id="ctr_type_edit" name="ctr_type" style="width: 100%;" required>
                                    <option disabled selected value>Choose Jenis Container</option>
                                    <option value="Class BB Standar 3">Class BB Standar 3</option>
                                    <option value="Class BB Standar 8">Class BB Standar 8</option>
                                    <option value="Class BB Standar 9">Class BB Standar 9</option>
                                    <option value="Class BB Standar 4,1">Class BB Standar 4,1</option>
                                    <option value="Class BB Standar 4,2">Class BB Standar 4,2</option>   
				            		<option value="Class BB Standar 4,3">Class BB Standar 4,3</option>   
                                    <option value="Class BB Standar 6">Class BB Standar 6</option>
                                    <option value="Class BB Standar 2,2">Class BB Standar 2,2</option>
                                    <option value="Class BB Standar 2,3">Class BB Standar 2,3</option>    
                                    <option value="Class BB High Class 2,1">Class BB High Class 2,1</option>
                                    <option value="Class BB High Class 5,1">Class BB High Class 5,1</option>
                                    <option value="Class BB High Class 6,1">Class BB High Class 6,1</option>
                                    <option value="Class BB High Class 5,2">Class BB High Class 5,2</option>
                                    <option value="REEFER RF">REEFER RF</option>
                                    <option value="REEFER RECOOLING">REEFER RECOOLING</option>
				            		<option value="REEFER RECOOLING BB 3">REEFER RECOOLING BB 3</option>
				            		<option value="REEFER RECOOLING BB 8">REEFER RECOOLING BB 8</option>                           
				            		<option value="REEFER RECOOLING BB 6">REEFER RECOOLING BB 6</option>\		
				            		<option value="REEFER RECOOLING BB 9">REEFER RECOOLING BB 9</option>
				            		<option value="REEFER RECOOLING BB 2.1">REEFER RECOOLING BB 2.1</option>
				            		<option value="REEFER RECOOLING BB 2.2">REEFER RECOOLING BB 2.2</option>
				            		<option value="REEFER RECOOLING BB 2.3">REEFER RECOOLING BB 2.3</option>			
				            		<option value="REEFER RECOOLING BB 4.1">REEFER RECOOLING BB 4.1</option>
				            		<option value="REEFER RECOOLING BB 4.2">REEFER RECOOLING BB 4.2</option>
                                    <option value="REEFER RECOOLING BB 5.1">REEFER RECOOLING BB 5.1</option>
                                    <option value="REEFER RECOOLING BB 5.2">REEFER RECOOLING BB 5.2</option>
                                    <option value="REEFER RECOOLING BB 6.1">REEFER RECOOLING BB 6.1</option>
				            		<option value="FLAT TRACK RF">FLAT TRACK RF</option>
                                    <option value="FLAT TRACK OH">FLAT TRACK OH</option>
                                    <option value="FLAT TRACK OW">FLAT TRACK OW</option>
                                    <option value="FLAT TRACK OL">FLAT TRACK OL</option>
                                    <option value="DRY">DRY</option>
                                    <option value="OPEN TOP">OPEN TOP</option>
				            		<option value="OH">OH</option>
                                 </select>
                            </div>
                            <div class="form-group">
                                <label for="">Seal</label>
                                <input type="text" class="form-control" name="no_seal" id="no_seal_edit">
                            </div>
                            <div class="form-group">
                                <label for="">Weight</label>
                                <input type="text" class="form-control" name="weight" id="weight_edit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Measurement</label>
                            <input type="text" class="form-control" name="meas" id="meas_edit">
                        </div>
                        <div class="form-group">
                            <label for="">No BL AWB</label>
                            <input type="text" name="nobl" id="nobl_edit" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Tgl BL AWB</label>
                            <input type="date" name="tgl_bl_awb" id="tgl_bl_awb_edit" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">ETA</label>
                            <input type="date" class="form-control" name="eta" id="eta_edit">
                        </div>
                        <div class="form-group">
                            <label for="">Customer</label>
                            <select name="cust_id" id="customerEdit" class="editSelect form-select selcet2" style="width: 100%;">
                                <option disabled selected value>Pilih Satu!</option>
                                @foreach($customer as $cust)
                                    <option value="{{$cust->id}}">{{$cust->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                 <button type="button" id="updateButton" class="btn btn-primary ml-1" data-bs-dismiss="modal"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
             </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')

<script>
    $(document).ready(function (){
        var id = {{$job->id}};
        $('#tableContainer').DataTable({
            processing: true,
            serverSide: true,
            ajax : '/fcl/register/detailDataContainer-' + id,
            columns : [
                {data: 'edit', name: 'edit'},
                {data: 'delete', name: 'delete'},
                {data: 'nobl', name: 'nobl'},
                {data: 'tgl_bl_awb', name: 'tgl_bl_awb'},
                {data: 'nocontainer', name: 'nocontainer'},
                {data: 'size', name: 'size'},
                {data: 'ctr_type', name: 'ctr_type'},
                {data: 'teus', name: 'teus'},
                {data: 'no_seal', name: 'no_seal'},
                {data: 'weight', name: 'weight'},
                {data: 'meas', name: 'meas'},
                {data: 'customer', name: 'customer'},
                {data: 'eta', name: 'eta'},
                {data: 'user', name: 'user'},
            ]
        });
    })
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('createContianerButton').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to update this record?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form programmatically if confirmed
                    document.getElementById('createContianerForm').submit();
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('submitButton').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to update this record?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form programmatically if confirmed
                    document.getElementById('createForm').submit();
                }
            });
        });
    });
</script>
<script>
    document.querySelectorAll('[id^="deleteUser-"]').forEach(button => {
    button.addEventListener('click', function() {
        var userId = this.getAttribute('data-id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan dapat mengembalikan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/fcl/register/containerDelete${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => {
                    if (response.ok) {
                        Swal.fire(
                            'Dihapus!',
                            'Data pengguna telah dihapus.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            'Data pengguna tidak dapat dihapus.',
                            'error'
                        );
                    }
                }).catch(error => {
                    Swal.fire(
                        'Gagal!',
                        'Terjadi kesalahan saat menghapus data pengguna.',
                        'error'
                    );
                });
            }
        });
    });
});
</script>

<script>
   $(document).on('click', '.formEdit', function() {
    let id = $(this).data('id');
    $.ajax({
      type: 'GET',
      url: '/fcl/register/containerEdit' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $('#editCust').modal('show');
        $("#editCust #nocontainer_edit").val(response.data.nocontainer);
        $("#editCust #id_edit").val(response.data.id);
        $("#editCust #size_edit").val(response.data.size).trigger('change');
        $("#editCust #ctr_type_edit").val(response.data.ctr_type).trigger('change');
        $("#editCust #weight_edit").val(response.data.weight);
        $("#editCust #no_seal_edit").val(response.data.no_seal);
        $("#editCust #meas_edit").val(response.data.meas);
        $("#editCust #nobl_edit").val(response.data.nobl);
        $("#editCust #tgl_bl_awb_edit").val(response.data.tgl_bl_awb);
        $("#editCust #eta_edit").val(response.data.eta);
        $("#editCust #customerEdit").val(response.data.cust_id).trigger('change');
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
                text: "Do you want to update this record?",
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
@endsection