@extends('partial.main')
<style>
    .table-fixed td,
    .table-fixed th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@section('content')
<section>
    <div class="card">
        <div class="card-header">
            <div class="text-center">
               <div class="button-container">
                    <h4><strong>Detail Container</strong></h4>
                    <a href="javascript:void(0)" onclick="openWindow('/lcl/realisasi/stripping-photoCont{{$cont->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
               </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-body">
            <div class="table">
                <table class="table-fixed" id="tableDetil">
                    <thead>
                        <tr>
                            <th class="text-center">-</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">No HBL</th>
                            <th class="text-center">Tgl HBL</th>
                            <th class="text-center">No Tally</th>
                            <th class="text-center">Shipper</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Packing</th>
                            <th class="text-center">Kode Kemas</th>
                            <th class="text-center">Desc</th>
                            <th class="text-center">Weight</th>
                            <th class="text-center">Meas</th>
                            <th class="text-center">Tgl Mulai Stripping</th>
                            <th class="text-center">Tgl Selesai Stripping</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-outline-info approve" id="approve" type="button">Approve</button>
        </div>
    </div>
</section>
@endsection

@section('custom_js')

<script>
    $(document).ready(function () {
        let lastChecked = null;
        var Id = {{ $id }}; 
        $('#tableDetil').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: '/bc/lcl/realisasi/stripping/detilData-' + Id, // Fix concatenation
            columns: [
                { data: 'check', name: 'check', className: 'text-center' }, // Define the column
                { data: 'detil', name: 'detil', className: 'text-center' }, // Define the column
                { data: 'nohbl', name: 'nohbl', className: 'text-center' }, // Define the column
                { data: 'tgl_hbl', name: 'tgl_hbl', className: 'text-center' }, // Define the column
                { data: 'notally', name: 'notally', className: 'text-center' }, // Define the column
                { data: 'shiper', name: 'shiper', className: 'text-center' }, // Define the column
                { data: 'customer', name: 'customer', className: 'text-center' }, // Define the column
                { data: 'quantity', name: 'quantity', className: 'text-center' }, // Define the column
                { data: 'packN', name: 'packN', className: 'text-center' }, // Define the column
                { data: 'packC', name: 'packC', className: 'text-center' }, // Define the column
                { data: 'descofgoods', name: 'descofgoods', className: 'text-center' }, // Define the column
                { data: 'weight', name: 'weight', className: 'text-center' }, // Define the column
                { data: 'meas', name: 'meas', className: 'text-center' }, // Define the column
                { data: 'startstripping', name: 'startstripping', className: 'text-center' }, // Define the column
                { data: 'endstripping', name: 'endstripping', className: 'text-center' }, // Define the column
            ]
        })
        $('#tableDetil').on('click', '.select-cont', function (e) {
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
                            url: '/bc/lcl/realisasi/stripping/manifest/approve',
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
        $("#tglstripping_edit").val(response.data.tglstripping);
        $("#jamstripping_edit").val(response.data.jamstripping);
        $("#startstripping_edit").val(response.data.startstripping);
        $("#endstripping_edit").val(response.data.endstripping);
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