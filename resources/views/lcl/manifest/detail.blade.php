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
            <div class="text-center">
                <h4><strong>Detail Container</strong></h4>
            </div>
            <div class="row mt-5">
                <div class="col-auto">
                    <div class="form-group">
                        <label for="">Container/Size/No Segel</label>
                        <input type="text" value="{{$cont->nocontainer}}/{{$cont->size}}/{{$cont->nosegel}}" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="">No Job Order</label>
                        <input type="text" value="{{$cont->job->nojoborder}}" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="">Vessel</label>
                        <input type="text" value="{{$cont->job->kapal->name ?? ''}}" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="">Voy</label>
                        <input type="text" value="{{$cont->job->voy ?? ''}}" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="">Pelabuhan</label>
                        <input type="text" value="{{$cont->job->port->name ?? ''}}" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="">Pelabuhan</label>
                        <input type="text" value="{{$cont->job->eta ?? ''}}" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="">No BC 11</label>
                        <input type="text" value="{{$cont->job->tno_bc11 ?? ''}}" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="">Tgl BC 11</label>
                        <input type="date" value="{{$cont->job->ttgl_bc11 ?? ''}}" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="">Tgl BC 11</label>
                        <input type="date" value="{{$cont->job->eta ?? ''}}" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="">No MBL</label>
                        <input type="text" value="{{$cont->job->nombl ?? ''}}" class="form-control" readonly>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group">
                        <label for="">Consolidator</label>
                        <input type="text" value="{{$cont->job->consolidator->namaconsolidator ?? ''}}" class="form-control" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-auto ms-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual"><i class="fas fa-plus"></i></button>
                </div>
                <!-- <div class="col-auto ms-2">
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#excelModal"><i class="fas fa-file-excel"></i></button>
                </div> -->
            </div>
        </div>
        <div class="card-body">
            <div style="overflow-x:auto;">
                <table class="tabelCustom table-responsive">
                    <thead>
                        <tr>
                            <th class="text-center">Action</th>
                            <!-- <th class="text-center">Approve</th>
                            <th class="text-center">Validasi</th>
                            <th class="text-center">Validasi Bc</th> -->
                            <th class="text-center">No HBL</th>
                            <th class="text-center">Tgl HBL</th>
                            <th class="text-center">No Tally</th>
                            <th class="text-center">Shipper</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Packing</th>
                            <th class="text-center">Kode Kemas</th>
                            <th class="text-center">Desc</th>
                            <th class="text-center">Tonase</th>
                            <th class="text-center">Volume</th>
                            <th class="text-center">Packing Tally</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($manifest as $mans)
                            <tr>
                                <td>
                                    <div class="button-container">
                                        <button class="btn btn-danger deleteButton" data-id="{{$mans->id}}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <button class="btn btn-warning editButton" data-id="{{$mans->id}}">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <a href="javascript:void(0)" onclick="openWindow('/lcl/manifest/item-{{$mans->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                        <a href="javascript:void(0)" onclick="openWindow('/lcl/manifest/barcode-{{$mans->id}}')" class="btn btn-sm btn-danger"><i class="fa fa-print"></i></a>
                                    </div>
                                </td>
                                <!-- <td>
                                    @if($mans->validasi == 'N')
                                    <button class="btn btn-outline-danger approveButton" data-id="{{$mans->id}}">Approve</button>
                                    @else
                                    <div class="button-container">
                                        <button class="btn btn-outline-success unapproveButton" data-id="{{$mans->id}}">Unapprove</button>
                                        <a href="javascript:void(0)" onclick="openWindow('/lcl/manifest/barcode-{{$mans->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                    </div>
                                    @endif
                                </td>
                                <td>{{$mans->validasi}}</td>
                                <td>{{$mans->validasiBc ?? 'N'}}</td> -->
                                <td>{{$mans->nohbl}}</td>
                                <td>{{$mans->tgl_hbl}}</td>
                                <td>{{$mans->notally}}</td>
                                <td>{{$mans->shipperM->name ?? ''}}</td>
                                <td>{{$mans->customer->name ?? ''}}</td>
                                <td>{{$mans->quantity}}</td>
                                <td>{{$mans->packing->name ?? ''}}</td>
                                <td>{{$mans->packing->code ?? ''}}</td>
                                <td>
                                    <textarea class="form-control" cols="3" readonly>{{$mans->descofgoods}}</textarea>
                                </td>
                                <td>{{$mans->weight}}</td>
                                <td>{{$mans->meas}}</td>
                                <td>{{$mans->packingTally->name ?? ''}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal Excel -->
<div class="modal fade" id="excelModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Upload Data Excel</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('lcl.manifest.excel')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p> Upload Your File </p>
                    <input type="file" name="file" class="form-control" id="inputGroupFile01">
                    <input type="hidden" name="container_id" value="{{$cont->id}}" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                    <button type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manual -->
<div class="modal fade" id="addManual" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-xl"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Add Data Manifest</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('lcl.manifest.create')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                       <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">No. HBL</label>
                                <input type="text" class="form-control" name="nohbl">
                                <input type="hidden" class="form-control" name="container_id" value="{{$cont->id}}">
                            </div>
                            <div class="form-group">
                                <label for="">Tgl HBL</label>
                                <input type="date" class="form-control" name="tgl_hbl">
                            </div>
                            <div class="form-group">
                                <label for="">Shipper</label>
                                <select name="shipper_id" style="width: 100%;" class="customSelect form-select select2">
                                    <option value disabled selected>Pilih Satu!</option>
                                    @foreach($custs as $cust)
                                        <option value="{{$cust->id}}">{{$cust->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Customer</label>
                                <select name="customer_id" style="width: 100%;" class="customSelect form-select select2">
                                    <option value disabled selected>Pilih Satu!</option>
                                    @foreach($custs as $cust)
                                        <option value="{{$cust->id}}">{{$cust->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Notify Party</label>
                                <select name="notifyparty_id" style="width: 100%;" class="customSelect form-select select2">
                                    <option value disabled selected>Pilih Satu!</option>
                                    @foreach($custs as $cust)
                                        <option value="{{$cust->id}}">{{$cust->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Marking</label>
                                <textarea name="marking" class="form-control" cols="6"></textarea>
                            </div>
                       </div>
                       <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Desc of Gods</label>
                                <textarea name="descofgoods" class="form-control" cols="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="">Quantity</label>
                                <input type="number" name="quantity" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Packing</label>
                                <select name="packing_id" style="width: 100%;" class="customSelect form-select select2">
                                    <option value disabled selected>Pilih Satu</option>
                                    @foreach($packs as $pack)
                                        <option value="{{$pack->id}}">{{$pack->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Tonase</label>
                                <input type="text" name="weight" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Volume</label>
                                <input type="text" name="meas" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Packing Tally</label>
                                <select name="packing_tally" style="width: 100%;" class="customSelect form-select selec2">
                                    <option value disabled selected>Pilih Satu</option>
                                    @foreach($packs as $pack)
                                        <option value="{{$pack->id}}">{{$pack->name}}</option>
                                    @endforeach
                                </select>
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

<div class="modal fade" id="editCust" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Edit Data Manifest</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('lcl.manifest.update')}}" method="POST" id="updateForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                       <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">No. HBL</label>
                                <input type="text" class="form-control" name="nohbl" id="nohbl_edit">
                                <input type="hidden" class="form-control" name="container_id" id="container_id_edit" value="{{$cont->id}}">
                                <input type="hidden" class="form-control" name="id" id="id_edit" value="{{$cont->id}}">
                            </div>
                            <div class="form-group">
                                <label for="">Tgl HBL</label>
                                <input type="date" class="form-control" name="tgl_hbl" id="tgl_hbl_edit">
                            </div>
                            <div class="form-group">
                                <label for="">Shipper</label>
                                <select name="shipper_id" id="shipper_id_edit" style="width: 100%;" class="editSelect form-select select2">
                                    <option value disabled selected>Pilih Satu!</option>
                                    @foreach($custs as $cust)
                                        <option value="{{$cust->id}}">{{$cust->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Customer</label>
                                <select name="customer_id" id="customer_id_edit" style="width: 100%;" class="editSelect form-select select2">
                                    <option value disabled selected>Pilih Satu!</option>
                                    @foreach($custs as $cust)
                                        <option value="{{$cust->id}}">{{$cust->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Notify Party</label>
                                <select name="notifyparty_id" id="notifyparty_id_edit" style="width: 100%;" class="editSelect form-select select2">
                                    <option value disabled selected>Pilih Satu!</option>
                                    @foreach($custs as $cust)
                                        <option value="{{$cust->id}}">{{$cust->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Marking</label>
                                <textarea name="marking" id="marking_edit" class="form-control" cols="6"></textarea>
                            </div>
                       </div>
                       <div class="col-sm-6">
                            <div class="form-group">
                                <label for="">Desc of Gods</label>
                                <textarea name="descofgoods" id="descofgoods_edit" class="form-control" cols="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="">Quantity</label>
                                <input type="number" name="quantity" id="quantity_edit" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Packing</label>
                                <select name="packing_id" id="packing_id_edit"  style="width: 100%;" class="editSelect form-select select2">
                                    <option value disabled selected>Pilih Satu</option>
                                    @foreach($packs as $pack)
                                        <option value="{{$pack->id}}">{{$pack->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Tonase</label>
                                <input type="text" name="weight" id="weight_edit" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Volume</label>
                                <input type="text" name="meas" id="meas_edit" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Packing Tally</label>
                                <select name="packing_tally" id="packing_tally_edit" style="width: 100%;" class="editSelect form-select selec2">
                                    <option value disabled selected>Pilih Satu</option>
                                    @foreach($packs as $pack)
                                        <option value="{{$pack->id}}">{{$pack->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                       </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                    <button type="button" id="updateButton" class="btn btn-primary ml-1" data-bs-dismiss="modal"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
<script>
   $(document).on('click', '.editButton', function() {
    let id = $(this).data('id');
    $.ajax({
      type: 'GET',
      url: '/lcl/manifest/edit-' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $('#editCust').modal('show');
        $("#editCust #id_edit").val(response.data.id);
        $("#editCust #nohbl_edit").val(response.data.nohbl);
        $("#editCust #container_id_edit").val(response.data.container_id);
        $("#editCust #tgl_hbl_edit").val(response.data.tgl_hbl);
        $("#editCust #shipper_id_edit").val(response.data.shipper_id).trigger('change');
        $("#editCust #customer_id_edit").val(response.data.customer_id).trigger('change');
        $("#editCust #notifyparty_id_edit").val(response.data.notifyparty_id).trigger('change');
        $("#editCust #marking_edit").val(response.data.marking);
        $("#editCust #descofgoods_edit").val(response.data.descofgoods);
        $("#editCust #quantity_edit").val(response.data.quantity);
        $("#editCust #packing_id_edit").val(response.data.packing_id).trigger('change');
        $("#editCust #weight_edit").val(response.data.weight);
        $("#editCust #meas_edit").val(response.data.meas);
        $("#editCust #packing_tally_edit").val(response.data.packing_tally).trigger('change');
      },
      error: function(data) {
        console.log('error:', data)
      }
    });
  });
</script>

<script>
$(document).on('click', '.deleteButton', function() {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin menghapus data ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '/lcl/manifest/delete-' + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        Swal.fire({
                            title: 'Sukses!',
                            text: response.message,
                            icon: 'success',
                        }).then(() => {
                            location.reload(); // Reload halaman setelah sukses
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                        });
                    }
                },
                error: function(data) {
                    console.log('error:', data);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus data.',
                        icon: 'error',
                    });
                }
            });
        }
    });
});
</script>

<script>
$(document).on('click', '.approveButton', function() {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin aprrove?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '/lcl/manifest/approve-' + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        Swal.fire({
                            title: 'Sukses!',
                            text: response.message,
                            icon: 'success',
                        }).then(() => {
                            location.reload(); // Reload halaman setelah sukses
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                        });
                    }
                },
                error: function(data) {
                    console.log('error:', data);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus data.',
                        icon: 'error',
                    });
                }
            });
        }
    });
});
</script>

<script>
$(document).on('click', '.unapproveButton', function() {
    let id = $(this).data('id');
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin membatalkan approve?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '/lcl/manifest/unapprove-' + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        Swal.fire({
                            title: 'Sukses!',
                            text: response.message,
                            icon: 'success',
                        }).then(() => {
                            location.reload(); // Reload halaman setelah sukses
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                        });
                    }
                },
                error: function(data) {
                    console.log('error:', data);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus data.',
                        icon: 'error',
                    });
                }
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
                text: "Data detail barang akan reset ketika Quantity berubah Value",
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