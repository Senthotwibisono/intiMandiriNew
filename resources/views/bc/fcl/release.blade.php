@extends('partial.main')
@section('custom_styles')
<style>
    .highlight-yellow {
        background-color: yellow !important;
        color: black !important; 
    }
</style>

<style>
    .highlight-blue {
        background-color: lightblue !important;
        color: white !important;
    }
</style>

<style>
    .highlight-red {
        background-color: red !important;
    }
</style>
@endsection
@section('content')
<section>
    <div class="card">
        <div class="card-body">
            <br>
            <table class="table table-hover table-stripped" id="tableContainer" style="white-space: nowrap;">
                <thead>
                    <tr>
                        <th>Hold</th>
                        <th>Photo</th>
                        <th>No Job Order</th>
                        <th>Tgl Release</th>
                        <th>Release By</th>
                        <th>No MBL</th>
                        <th>No Container</th>
                        <th>Size</th>
                        <th>Container Type</th>
                        <th>No BL AWB</th>
                        <th>Tgl BL AWB</th>
                        <th>Tgl Masuk</th>
                        <th>Jam Masuk</th>
                        <th>Tgl Keluar</th>
                        <th>Jam Keluar</th>
                        <th>Jenis Dok</th>
                        <th>No Dok</th>
                        <th>Tgl Dok</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>
@endsection

@section('custom_js')
<script>
    $(document).on('click', '.holdButton', function() {
        let id = $(this).data('id'); // Ambil ID dari tombol

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menahan container ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Release!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Memproses...",
                    text: "Mohon tunggu",
                    icon: "info",
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading(); // Tampilkan loading
                    }
                });

                // Kirim AJAX request
                $.ajax({
                    url: '/bc/fcl/holdFCLCont', // Ganti dengan URL endpoint
                    type: 'POST',
                    data: { id: id, _token: '{{ csrf_token() }}' }, // Kirim ID & CSRF token
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Berhasil!",
                                text: "Container berhasil di HOLD.",
                                icon: "success"
                            }).then(() => {
                                location.reload(); // Reload halaman setelah sukses
                            });
                        } else {
                            Swal.fire({
                                title: "Gagal!",
                                text: response.message || "Terjadi kesalahan!",
                                icon: "error"
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: "Error!",
                            text: "Terjadi kesalahan server.",
                            icon: "error"
                        });
                    }
                });
            }
        });
    });
</script>

<script>
    $(document).ready(function(){
        $('#tableContainer').dataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: '/bc/fcl/releaseContainerDataTable',
            columns: [
                {data:'hold', name:'hold'},
                {data:'photo', name:'photo'},
                {data:'nojob', name:'nojob'},
                {data:'release_bc_date', name:'release_bc_date'},
                {data:'release_bc_uid', name:'release_bc_uid'},
                {data:'nombl', name:'nombl'},
                {data:'nocontainer', name:'nocontainer'},
                {data:'size', name:'size'},
                {data:'ctr_type', name:'ctr_type'},
                {data:'nobl', name:'nobl'},
                {data:'tglBL', name:'tglBL'},
                {data:'tglmasuk', name:'tglmasuk'},
                {data:'jammasuk', name:'jammasuk'},
                {data:'tglkeluar', name:'tglkeluar'},
                {data:'jamkeluar', name:'jamkeluar'},
                {data:'kodeDok', name:'kodeDok'},
                {data:'noDok', name:'noDok'},
                {data:'tglDok', name:'tglDok'},
            ],
        })
    })
</script>
<script>
$(document).ready(function() {
    $('#cancelButton').click(function() {
        location.reload();
    });
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
        $("#id_edit").val(response.data.id);
        $("#nohbl_edit").val(response.data.nohbl);
        $("#notally_edit").val(response.data.notally);
        $("#quantity_edit").val(response.data.quantity);
        $("#no_dok_edit").val(response.data.no_dok);
        $("#tgl_dok_edit").val(response.data.tgl_dok);
        $("#kd_dok_edit").val(response.data.kd_dok_inout).trigger('change');
        $("#tglbuangmty_edit").val(response.data.tglbuangmty);
        $("#jambuangmty_edit").val(response.data.jambuangmty);
      },
      error: function(data) {
        console.log('error:', data)
      }
    });
  });
</script>

<script>
$(document).on('click', '.CheckSPJMDok', function() {
    var data = {
          'id' : $('#id_edit').val(),
          'kd_dok' : $('#kd_dok_edit').val(),
          'no_dok' : $('#no_dok_edit').val(),
          'tgl_dok' : $('#tgl_dok_edit').val(),
        }
    Swal.fire({
        title: 'Konfirmasi',
        text: '',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'POST',
                url: '/lcl/delivery/gateOut/check',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                data: data,
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        Swal.fire({
                            title: 'Sukses!',
                            text: response.message,
                            icon: 'success',
                        }).then(() => {
                            // location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error',
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(data) {
                    console.log('error:', data);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan',
                        icon: 'error',
                    }).then(() => {
                            location.reload();
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
                url: '/bc/lcl/delivery/gateOutapprove-' + id,
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
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('endButton').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Halaman ini tidak bisa di buka kembali ketika anda mengakhiri sesi ini",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form programmatically if confirmed
                    document.getElementById('endForm').submit();
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