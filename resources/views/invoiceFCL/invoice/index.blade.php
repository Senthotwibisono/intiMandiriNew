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
                            <th>No BL AWB</th>
                            <th>Tgl BL AWB</th>
                            <th>Created At</th>
                            <th>Created By</th>
                            <th>Pranota</th>
                            <th>Invoice</th>
                            <th>Action</th>
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
        $('#tableInvoice').dataTable({
            processing: true,
            serverSide: true,
            ajax: '/invoiceFCL/invoice/dataTable',
            scrollX: true,
            columns:[
                {data:'proforma_no', name:'proforma_no'},
                {data:'invoiceNo', name:'invoiceNo'},
                {data:'cust_name', name:'cust_name'},
                {data:'cust_npwp', name:'cust_npwp'},
                {data:'nobl', name:'nobl'},
                {data:'tgl_bl_awb', name:'tgl_bl_awb'},
                {data:'created_at', name:'created_at'},
                {data:'createdBy', name:'createdBy'},
                {data:'pranota', name:'pranota'},
                {data:'invoice', name:'invoice'},
                {data:'action', name:'action'},
            ]
        })
    })
</script>

<script>
    $(document).on('click', '#paidButton', function(){
        let id = $(this).data('id');  // Assuming 'data-id' attribute holds the value

        console.log('Id Header = ' + id);

        // SweetAlert2 confirmation before proceeding
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to process this payment?",
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
                    url: '/invoiceFCL/invoice/paidInvoice',  // Replace with your endpoint
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
                            Swal.fire("Success!", "The payment has been processed.", "success");
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
    });
</script>


@endsection