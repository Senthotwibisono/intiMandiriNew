@extends('partial.bc.main')
@section('custom_styles')
<style>
    .highlight-yellow {
        background-color: yellow !important;
    }
    .highlight-blue {
        background-color: lightblue !important;
    }
    .highlight-red {
        background-color: red !important;
    }
</style>
@endsection

@section('content')
<div class="page-content">
    <div class="col-12">
        <div class="card">
            <div class="card-content">
                <div class="card-header">
                    <p>Please make sure the manifest which turns into <span class="badge bg-danger">Red Segel</span> before submitting the form!!!</p>
                </div>
                <div class="card-body">
                    <h4>Last Activity</h4>
                    <div class="table table-responsive" style="overflow-x:auto;">
                        <table class="table table-hover table-stripped" id="tableListManifest">
                            <thead>
                                <tr>
                                    <th class="text-center">Action</th>
                                    <th class="text-center">Status BeaCukai</th>
                                    <th class="text-center">NO Container</th>
                                    <th class="text-center">Job Order</th>
                                    <th class="text-center">NO HBL</th>
                                    <th class="text-center">Tgl HBL</th>
                                    <th class="text-center">No Tally</th>
                                    <th class="text-center">Shipper</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Packing</th>
                                    <th class="text-center">Kode Kemas</th>
                                    <th class="text-center">Weight</th>
                                    <th class="text-center">Meas</th>
                                    <th class="text-center">Jenis Dok</th>
                                    <th class="text-center">No Dok</th>
                                    <th class="text-center">Tgl Dok</th>
                                    <th class="text-center">Gudang Asal</th>
                                    <th class="text-center">ETA</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade text-left" id="lockModal" tabindex="-1" role="dialog"aria-labelledby="myModalLabel17" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title" id="myModalLabel17"><i class="fa fa-lock"></i>Lock Modal</h4>
                <button type="button" class="close" data-bs-dismiss="modal"aria-label="Close"><i data-feather="x"></i></button>
            </div>
            <form action="/bc-p2/lcl/list-manifest/lockSubmit" id="createForm" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">No HBL/Container</label>
                            <input type="text" class="form-control" id="label" readonly>
                            <input type="hidden" name="id" id="id">
                        </div>
                        <div class="form-group">
                            <label for="">No Segel</label>
                            <input type="text" name="no_segel" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Alasan Segel</label>
                            <select name="alasan_segel"  style="width:100%" class="alasanSegel form-select">
                                <option disabled selected value>Pilih Satu!</option>
                                @foreach($alasan as $als)
                                    <option value="{{$als->name}}">{{$als->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Keterangan</label>
                            <textarea name="keterangan" class="form-control" id="" cols="30"></textarea>
                        </div>
                        <div class="form-goup">
                            <label for="">Photo</label>
                            <input type="file" class="form-control" id="photos" name="photos[]" multiple accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary"data-bs-dismiss="modal"><i class="bx bx-x d-block d-sm-none"></i><span class="d-none d-sm-block">Close</span></button>
                    <button type="button" class="btn btn-primary ml-1" id="submitButton"><i class="bx bx-check d-block d-sm-none"></i><span class="d-none d-sm-block">Submit</span></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
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
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while the update is being processed.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    // Submit the form programmatically if confirmed
                    document.getElementById('createForm').submit();
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function(){
        $('#tableListManifest').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/bc-p2/lcl/list-manifest/data',
            columns:[
                {data:'id', name:'id', className:'text-center',
                    render: function(data, row){
                        return `<button type="button" class="btn btn-info holdP2" id="holdP2" data-id="${data}"><i class="fa fa-lock"></i></button>`;
                    }
                },
                {data:'status_bc', name:'status_bc', className:'text-center'},
                {data:'container', name:'container', className:'text-center'},
                {data:'jobOrder', name:'jobOrder', className:'text-center'},
                {data:'nohbl', name:'nohbl', className:'text-center'},
                {data:'tgl_hbl', name:'tgl_hbl', className:'text-center'},
                {data:'notally', name:'notally', className:'text-center'},
                {data:'shipper', name:'shipper', className:'text-center'},
                {data:'customer', name:'customer', className:'text-center'},
                {data:'quantity', name:'quantity', className:'text-center'},
                {data:'packingName', name:'packingName', className:'text-center'},
                {data:'packingCode', name:'packingCode', className:'text-center'},
                {data:'weight', name:'weight', className:'text-center'},
                {data:'meas', name:'meas', className:'text-center'},
                {data:'dokumenName', name:'dokumenName', className:'text-center'},
                {data:'no_dok', name:'no_dok', className:'text-center'},
                {data:'tgl_dok', name:'tgl_dok', className:'text-center'},
                {data:'gudangAsal', name:'gudangAsal', className:'text-center'},
                {data:'eta', name:'eta', className:'text-center'},
            ],
            createdRow: function(row, data, dataIndex) {
                if (data.status_bc === 'HOLD') {
                    $(row).addClass('highlight-yellow text-white');
                } else if (data.status_bc === 'HOLDP2') {
                    $(row).addClass('highlight-red text-white');
                } else if (data.status_bc === 'release'){
                    $(row).addClass('highlight-blue');
                }
            }
        });
    });
</script>

<script>
   $(document).on('click', '#holdP2', function() {
    let id = $(this).data('id');
    $.ajax({
      type: 'GET',
      url: '/bc-p2/lcl/list-manifest/lockModal' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $('#lockModal').modal('show');
        $("#lockModal #id").val(response.data.id);
        $("#lockModal #label").val(response.label);
      },
      error: function(data) {
        console.log('error:', data)
      }
    });
  });
</script>
@endsection
