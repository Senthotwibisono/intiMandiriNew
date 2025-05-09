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
        <div class="card-body d-flex align-items-center">
            <div class="table">
                <table class="table table-hover" id="tablePaidInvoice">
                    <thead style="white-space: nowrap;">
                        <tr>
                            <th class="text-center">Order No</th>
                            <th class="text-center">Invoice Number</th>
                            <th class="text-center">No HBL</th>
                            <th class="text-center">Tgl. HBL</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Kasir</th>
                            <th class="text-center">Status Pembayaran</th>
                            <th class="text-center">Order At</th>
                            <th class="text-center">Photo KTP</th>
                            <th class="text-center">Gate Pass</th>
                            <th class="text-center">Container Location</th>
                            <!-- <th class="text-center">Pranota</th> -->
                            <th class="text-center">Invoice</th>
                            <th class="text-center">Pay</th>
                            <th class="text-center">Dokumen</th>
                            <th class="text-center">Revisi</th>
                            <th class="text-center">Edit Tanggal</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>


<div class="modal fade" id="editCust" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-sm"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Payment Form</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="/invoice/paid" method="POST" id="updateForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">Order No</label>
                            <input type="text" name="order_no" id="order_no_edit" class="form-control" readonly>
                            <input type="hidden" name="id" id="id_edit" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Grand Total</label>
                            <input type="text" name="grand_total" id="grand_total_edit" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Full/Piutang</label>
                            <select name="status" id="status_edit" class="form-select">
                                <option value="Y">Lunas</option>
                                <option value="P">Piutang</option>
                            </select>
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


<div class="modal fade" id="editTanngal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-sm"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Edit Tanggal Form</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Order At</label>
                    <input type="datetime-local" class="form-control" id="order_at">
                    <input type="hidden" class="form-control" id="idEditTanggal">
                </div>
                <div class="form-group">
                    <label for="">Lunas At</label>
                    <input type="datetime-local" class="form-control" id="lunas_at">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                <button type="button" id="updateTanggal" class="btn btn-primary ml-1" data-bs-dismiss="modal" onClick="updateTanggal()"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dokManifest" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-sm"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Dokumen Keluar</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="/invoice/updateDokumen" method="POST" id="updateForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">Kode Dokumen</label>
                            <select name="kd_dok" id="kd_dok_edit" style="width: 100%; " class="dokSelect form-select select2">
                                <option value disabled selected>Pilih Satu</option>
                                @foreach($doks as $dok)
                                    <option value="{{$dok->kode}}">{{$dok->name}}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="id" id="id_edit" class="form-control">
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="">No Dok</label>
                                <input type="text" name="no_dok" id="no_dok_edit" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="">Tgl Dok </label>
                                <input type="date" name="tgl_dok" id="tgl_dok_edit" class="form-control">
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
    async function editTanggalInvoice(event) {
        Swal.showLoading();
        const id = event.getAttribute('data-id');
        console.log(id);

        const url = "{{route('invoice.lcl.searchForEdit')}}";
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id: id })
        });
        swal.hideLoading();
        if (response.ok) {
            const result = await response.json();
            console.log(result);
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Data Ditemukan',
                }).then(() => {
                    $('#editTanngal').modal('show');
                    $('#editTanngal #order_at').val(result.data.order_at);
                    $('#editTanngal #lunas_at').val(result.data.lunas_at);
                    $('#editTanngal #idEditTanggal').val(result.data.id);
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    text: result.message,
                });
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: response.status,
                text: response.statusText,
            });
        }
    }

    async function updateTanggal() {
        Swal.fire({
            icon: 'warning',
            title: 'Are You Sure?',
        }).then( async(result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Updating...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const order_at = document.getElementById('order_at').value;
                const lunas_at = document.getElementById('lunas_at').value;
                const id = document.getElementById('idEditTanggal').value;
                console.log(order_at, lunas_at);
                const url = '{{route('invoice.lcl.updateTanggal')}}';
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order_at : order_at, lunas_at : lunas_at, id:id})
                });
                if (response.ok) {
                    const result = await response.json();
                    console.log(result);
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data Ditemukan',
                        }).then(() => {
                            location.reload();
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: result.message,
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.status,
                        text: response.statusText,
                    });
                }
            }
        });
    }
</script>

<script>
    $(document).ready(function () {
        $('#tablePaidInvoice').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: '{{route('invoice.lcl.paidData')}}',
            columns: [
                {data:'order_no', name:'order_no', className:'text-center'},
                {data:'invoice_no', name:'invoice_no', className:'text-center'},
                {data:'manifest.nohbl', name:'manifest.nohbl', className:'text-center'},
                {data:'manifest.tgl_hbl', name:'manifest.tgl_hbl', className:'text-center'},
                {data:'manifest.quantity', name:'manifest.quantity', className:'text-center'},
                {data:'customer.name', name:'customer.name', className:'text-center'},
                {data:'kasir.name', name:'kasir.name', className:'text-center'},
                {data:'status', name:'status', className:'text-center'},
                {data:'order_at', name:'order_at', className:'text-center'},
                {data:'ktp', name:'ktp', className:'text-center'},
                {data:'gatePass', name:'gatePass', className:'text-center'},
                {data:'containerLocation', name:'containerLocation', className:'text-center'},
                // {data:'pranota', name:'pranota', className:'text-center'},
                {data:'invoice', name:'invoice', className:'text-center'},
                {data:'pay', name:'pay', className:'text-center'},
                {data:'dok', name:'dok', className:'text-center'},
                {data:'revisi', name:'revisi', className:'text-center'},
                {data:'editTanggal', name:'editTanggal', className:'text-center'},
            ],
        });
    })
</script>

<script>
    $(document).ready(function() {
        // Attach click event to dynamically created buttons
        $(document).on('click', '.revisiInvoice', function() {
            let id = $(this).data('id'); // Get the form ID from data attribute
            console.log("logId: " + id);
            
            Swal.fire({
                title: 'Are you sure?',
                text: "Apakah anda yakin untuk melakukan revisi pada invoice ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    // Redirect to the specified URL with form ID
                    window.location.href = '/invoice/form/formStep1/' + id;
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(".dokSelect").select2({
        dropdownParent: $('#dokManifest .modal-content')
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

<script>
    document.getElementById('createForm').addEventListener('click', function() {
        fetch('/invoice/form/create', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({})
        }).then(response => response.json())
        .then(data => {
            if (data.id) {
                // Redirect to invoice step1 with the form ID
                window.location.href = `/invoice/form/formStep1/${data.id}`;
            }
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
                fetch(`/invoice/deleteHeader-${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => {
                    if (response.ok) {
                        Swal.fire(
                            'Dihapus!',
                            'Data invoice telah dihapus.',
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
   $(document).on('click', '.pay', function() {
    let id = $(this).data('id');
    $.ajax({
      type: 'GET',
      url: '/invoice/actionButton-' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $('#editCust').modal('show');
        $("#editCust #id_edit").val(response.data.id);
        $("#editCust #order_no_edit").val(response.data.order_no);
        $("#editCust #grand_total_edit").val(response.data.grand_total);
        
      },
      error: function(data) {
        console.log('error:', data)
      }
    });
  });
</script>

<script>
   $(document).on('click', '.dok', function() {
    let id = $(this).data('id');
    $.ajax({
      type: 'GET',
      url: '/invoice/dokButton-' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $('#dokManifest').modal('show');
        $("#dokManifest #kd_dok_edit").val(response.data.kd_dok_inout).trigger('change');
        $("#dokManifest #id_edit").val(response.data.id);
        $("#dokManifest #no_dok_edit").val(response.data.no_dok);
        $("#dokManifest #tgl_dok_edit").val(response.data.tgl_dok);
        
      },
      error: function(data) {
        console.log('error:', data)
      }
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
                    url: '/lcl/delivery/gateOut-barcodeGate',
                    data: { id: containerId },
                    cache: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Generated!', '', 'success')
                                .then(() => {
                                    var barcodeId = response.data.id;
                                    window.open('/barcode/autoGate-indexManifest' + barcodeId, '_blank', 'width=600,height=800');
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