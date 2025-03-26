@extends('partial.main')

@section('custom_styles')

@endsection

@section('content')

<body>
    <div class="card">
        <div class="card-body">
            <div class="table">
                <table class="table-hover" id="tableInvoice" style="white-space: nowrap;">
                    <thead>
                        <tr>
                            <th>Proforma No</th>
                            <th>Invoice No</th>
                            <th>Customer Name</th>
                            <th>Customer NPWP</th>
                            <th>No SPJM</th>
                            <th>Tgl SPJM</th>
                            <th>Created At</th>
                            <th>Created By</th>
                            <th>Pranota</th>
                            <th>Invoice</th>
                            <th>Action</th>
                            <th>Delete or Cancel</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</body>

@endsection

@section('custom_js')
<script>
    $(document).ready(function(){
        $('#tableInvoice').on('click', '#cancelButton', function(){
            var id = $(this).data('id');
            Swal.fire({
                icon: 'warning',
                title: 'Yakin menghapus data ini?',
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading(); // Menampilkan loading animasi
                        }
                    });

                    $.ajax({
                        url: '{{ route('invoiceFCL.behandle.delete') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id:id
                        },
                        cache: false,
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            if (response.success == true) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Behasil!',
                                    text: response.message,
                                }).then(() => {
                                    Swal.fire({
                                        title: 'Mengirim ulang...',
                                        html: 'Harap tunggu...',
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);
                                });
                            }else
                                swal.fire({
                                    icon: 'error',
                                    text: 'Something Wrong: ' + response.message,
                                    title: 'Error',
                                });
                        },
                        error: function(response){
                            swal.fire({
                                icon: 'error',
                                text: 'Something Wrong: ' + response.responseJSON?.message,
                                title: 'Error',
                            });
                        }
                    })

                }
            });
        })
    })
</script>
<script>
    $(document).on('click', '.cancelButton', function(){
        let id = $(this).data('id');  // Assuming 'data-id' attribute holds the value

        console.log('Id Header = ' + id);

        // SweetAlert2 confirmation before proceeding
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to process this canceliation payment?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show SweetAlert processing message
                Swal.fire({
                    title: "Processing...",
                    text: "Please wait while we process your request.",
                    icon: "info",
                    showConfirmButton: false
                });

                // Perform AJAX request
                $.ajax({
                    url: '{{ route('invoiceFCL.behandle.invoiceCancel') }}',  // Replace with your endpoint
                    method: 'POST',
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'  // Add CSRF token for security in Laravel
                    },
                    success: function(response) {
                        // Close the SweetAlert
                        Swal.close();

                        // Check for success response
                        if(response.success) {
                            // Show success alert
                            Swal.fire("Success!", "The canceliation payment has been processed.", "success");
                            location.reload();
                        } else {
                            // Show error alert if there’s any issue
                            Swal.fire("Error!", "There was an issue processing the payment.", "error");
                        }
                    },
                    error: function(xhr, status, error) {
                        // Close the SweetAlert on error
                        Swal.close();

                        // Show error alert on failure
                        Swal.fire("Error!", "An error occurred while processing your request.", "error");
                    }
                });
            } else {
                // If user cancels the action
                Swal.fire("Cancelled", "The payment was not processed.", "info");
            }
        });
    })
</script>
<script>
    $(document).ready(function(){
        $('#tableInvoice').dataTable({
            processing: true,
            serverSide: true,
            ajax: '{{route('invoiceFCL.behandle.invoiceData')}}',
            scrollX: true,
            columns:[
                {data:'proforma_no', name:'proforma_no'},
                {data:'invoiceNo', name:'invoiceNo'},
                {data:'customer_name', name:'customer_name'},
                {data:'customer_npwp', name:'customer_npwp'},
                {data:'no_spjm', name:'no_spjm'},
                {data:'tgl_spjm', name:'tgl_spjm'},
                {data:'order_at', name:'order_at'},
                {data:'createdBy', name:'createdBy'},
                {data:'pranota', name:'pranota'},
                {data:'invoice', name:'invoice'},
                {data:'action', name:'action'},
                {data:'deleteOrCancel', name:'deleteOrCancel'},
                {data:'edit', name:'edit'},
            ]
        })
    })
</script>

<script>
    $(document).ready(function(){
        $('#tableInvoice').on('click', '#payButton', function(){
            var id = $(this).data('id');
            console.log('id : ' + id);
            Swal.fire({
                icon: 'warning',
                title: 'Anda yakin melakukan pembayaran di akun ini',
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    $.ajax({
                        url: '{{ route('invoiceFCL.behandle.invoicePay') }}',
                        type: 'POST',
                        data: {
                            _token:'{{ csrf_token() }}',
                            id:id,
                        },
                        cache: false,
                        dataType: 'json',
                        success: function(response){
                            if (response.success == true) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                }).then( ()=> {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Opss Something wrong, ',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Opss Something wrong, ',
                                text: response.responseJSON?.message,
                            });
                        }
                    })
                }
            });
        })
    })
</script>
<script> 
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('updateButton').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Get the No HP input field
            var noHpField = document.getElementById('no_hp_edit');

            // Check if the No HP field is valid
            if (!noHpField.value.trim()) {
                // Show validation error message
                Swal.fire({
                    title: 'Validation Error',
                    text: "No HP field cannot be empty!",
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return; // Stop the execution if validation fails
            }

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
                        text: 'Please wait',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById('updateForm').submit();
                }
            });
        });
    });
</script>


@endsection