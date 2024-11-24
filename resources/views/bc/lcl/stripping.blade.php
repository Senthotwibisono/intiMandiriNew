@extends('partial.main')
@section('custom_styles')
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
                <table class="table-fixed" id="tableCont">
                    <thead>
                        <tr>
                            <th class="text-center">-</th>
                            <th class="text-center">Action</th>
                            <th class="text-center">Status Ijin</th>
                            <th class="text-center">No Job Order</th>
                            <th class="text-center">No SPK</th>
                            <th class="text-center">No Container</th>
                            <th class="text-center">No MBL</th>
                            <th class="text-center">ETA</th>
                            <th class="text-center">Vessel</th>
                            <th class="text-center">Status</th>
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
                            <th>UID</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="container">
                    <button class="btn btn-outline-info approve" id="approve" type="button">Approve</button>
            </div>
        </div>
    </div>
</section>
@endsection

@section('custom_js')
<script>
    $(document).ready(function () {
        let lastChecked = null;
        $('#tableCont').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: '/bc/lcl/realisasi/stripping/data',
            columns: [
                {data:'check', name:'check', className:'text-center'},
                {data:'action', name:'action', className:'text-center'},
                {data:'detil', name:'detil', className:'text-center'},
                {data:'job.nojoborder', name:'job.nojoborder', className:'text-center'},
                {data:'job.nospk', name:'job.nospk', className:'text-center'},
                {data:'nocontainer', name:'nocontainer', className:'text-center'},
                {data:'job.nombl', name:'job.nombl', className:'text-center'},
                {data:'job.eta', name:'job.eta', className:'text-center'},
                {data:'kapal', name:'kapal', className:'text-center'},
                {data:'status', name:'status', className:'text-center'},
                {data:'no_plp', name:'no_plp', className:'text-center' },
                {data:'tgl_plp', name:'tgl_plp', className:'text-center' },
                {data:'kd_kantor', name:'kd_kantor', className:'text-center' },
                {data:'kd_tps', name:'kd_tps', className:'text-center' },
                {data:'kd_tps_asal', name:'kd_tps_asal', className:'text-center' },
                {data:'kd_tps_tujuan', name:'kd_tps_tujuan', className:'text-center' },
                {data:'nm_angkut', name:'nm_angkut', className:'text-center' },
                {data:'no_voy_flight', name:'no_voy_flight', className:'text-center' },
                {data:'no_surat', name:'no_surat', className:'text-center' },
                {data:'no_bc11', name:'no_bc11', className:'text-center' },
                {data:'tgl_bc11', name:'tgl_bc11', className:'text-center' },
                {data:'user.name', name:'user.name', className:'text-center'},
            ]
        })
        $('#tableCont').on('click', '.select-cont', function (e) {
            const checkboxes = $('.select-cont:not(:disabled)');
            const currentIndex = checkboxes.index(this);
            
            // Jika menekan tombol Shift
            if (e.shiftKey && lastChecked !== null) {
                const lastIndex = checkboxes.index(lastChecked);
            
                // Menentukan rentang checkbox yang akan dicentang
                const start = Math.min(lastIndex, currentIndex);
                const end = Math.max(lastIndex, currentIndex);
            
                // Centang semua checkbox di antara rentang
                checkboxes.slice(start, end + 1).prop('checked', lastChecked.checked);
            }
        
            // Simpan checkbox terakhir yang dicentang
            lastChecked = this;
        });
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
        $("#tglkeluar").val(response.data.tglkeluar);
        $("#jamkeluar").val(response.data.jamkeluar);
        $("#nopol_mty").val(response.data.nopol_mty);
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
    $('#approve').on('click', function () {
        var selected = [];
        $('.select-cont:checked').each(function () {
            selected.push($(this).val());
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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
                    Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we update the container',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    if (selected.length > 0) {
                        // Kirim data ke server
                        $.ajax({
                            url: '/bc/lcl/realisasi/stripping/approveCont',
                            method: 'POST',
                            data: { ids: selected },
                            success: function (response) {
                                console.log(response);
                                if (response.success) {
                                    Swal.fire('Saved!', '', 'success')
                                        .then(() => {
                                            // Memuat ulang halaman setelah berhasil menyimpan data
                                            window.location.reload();
                                        });
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function (error) {
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
                                    console.log('error:', response);
                                }
                            }
                        });
                    } else {
                        Swal.fire('Error!', 'Nothing Selected', 'error')
                        .then(() => {
                        });
                    }
                } else if (result.isDenied) {
                    Swal.fire('Changes are not saved', '', 'info')
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
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('approvedAll').addEventListener('click', function (e) {
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
                    document.getElementById('approveAllForm').submit();
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
                url: '/bc/lcl/realisasi/stripping/approve' + id,
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
@endsection