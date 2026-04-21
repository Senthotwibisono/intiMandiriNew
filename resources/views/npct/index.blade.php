@extends('partial.main')

@section('content')

<div class="page-content">
    <div class="card">
        <div class="card-header">
                <div class="col-auto ms-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual">Create Yor</button>
                </div>
        </div>
        <div class="card-boody">
            <table class="table-hover table-border" id="tableYor">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Yor</th>
                        <th>Kapasitas</th>
                        <th>Pengirim</th>
                        <th>Di Kirim Pada</th>
                        <th>Response</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addManual" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">YOR NPCT</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="" method="POST" id="createForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-5">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Warehouse Code</label>
                                <input type="text" class="form-control" id="warehouse_code" value="INTI" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Warehouse Type</label>
                                <select  id="warehouse_type" class="customSelect" style="width: 100%;">
                                    <option value="DRY">DRY</option>
                                    <option value="REEFER">REEFER</option>
                                    <option value="IMDG">IMDG</option>
                                    <option value="OOG">OOG</option>
                                    <option value="OTHER">OTHER</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">YOR</label>
                                <input type="number" class="form-control" id="yor" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="">Capacity</label>
                                <input type="number" class="form-control" id="capacity" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                    <button type="button" id="submitButton" class="btn btn-primary ml-1" onClick="submitModal(this)"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('custom_js')
<script>
    $(document).ready(function() {
        $('#tableYor').dataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollY: '50hv',
            ajax: '/npct/data',
            columns: [
                {data:'warehouse_code', name:'warehouse_code', className:'text-center'},
                {data:'warehouse_type', name:'warehouse_type', className:'text-center'},
                {data:'yor', name:'yor', className:'text-center'},
                {data:'capacity', name:'capacity', className:'text-center'},
                {data:'user.name', name:'user.name', className:'text-center'},
                {data:'created_at', name:'created_at', className:'text-center'},
                {data:'response', name:'response', className:'text-center'},
            ],
            pageLength: 25,
        })
    })
</script>

<script>
    async function submitModal(button) {
        const result = await confirmation();
        if (result.isConfirmed) {
            showLoading();
            const data = {
                warehouse_code: document.getElementById('warehouse_code').value,
                warehouse_type: document.getElementById('warehouse_type').value,
                yor: document.getElementById('yor').value,
                capacity: document.getElementById('capacity').value,
            }

            const url = '{{route('npct.post')}}';
            const response = await globalResponse(data, url);
            hideLoading();
             if (response.ok) {
                const hasil = await response.json();
                if (hasil.success) {
                    successHasil(hasil);
                    $('#tableGateOut').DataTable().ajax.reload();
                }else{
                    errorHasil(hasil);
                }
            }else{
                errorResponse(response);
                return;
            }
        }
    }
</script>
@endsection