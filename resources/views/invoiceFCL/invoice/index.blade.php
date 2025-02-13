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
                            <th>Tipe Invoice</th>
                            <th>Customer Name</th>
                            <th>Customer NPWP</th>
                            <th>No BL AWB</th>
                            <th>Tgl BL AWB</th>
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

    <div class="modal fade" id="editCust" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-xl"role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Payment Form</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
                </div>
                <form action="/invoiceFCL/invoice/paidInvoice" method="POST" id="updateForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Poforma No</label>
                                    <input type="text" name="order_no" id="order_no_edit" class="form-control" readonly>
                                    <input type="hidden" name="id" id="id_edit" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Grand Total</label>
                                    <input type="text" name="grand_total" id="grand_total_edit" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="">No HP</label>
                                    <input type="text" name="no_hp" id="no_hp_edit" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Foto KTP</label>
                                    <video id="video" width="100%" autoplay></video>
                                    <div id="imagePreview" style="display: none;"></div> <!-- Placeholder for the captured image -->
                                    <button type="button" id="capture" class="btn btn-primary mt-2">Capture</button>
                                    <canvas id="canvas" style="display: none;"></canvas>
                                    <input type="hidden" name="ktp" id="ktp" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                        <button type="button" id="updateButton" class="btn btn-primary ml-1"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

@endsection

@section('custom_js')
<script>

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
                    url: '/invoiceFCL/invoice/cancelInvoice',  // Replace with your endpoint
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
    $(document).on('click', '.deleteInvoice', function(){
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
                    url: '/invoiceFCL/invoice/deleteInvoice',  // Replace with your endpoint
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
            ajax: '/invoiceFCL/invoice/dataTable',
            scrollX: true,
            columns:[
                {data:'proforma_no', name:'proforma_no'},
                {data:'invoiceNo', name:'invoiceNo'},
                {data:'type', name:'type'},
                {data:'cust_name', name:'cust_name'},
                {data:'cust_npwp', name:'cust_npwp'},
                {data:'nobl', name:'nobl'},
                {data:'tgl_bl_awb', name:'tgl_bl_awb'},
                {data:'created_at', name:'created_at'},
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
    $(document).on('click', '#paidButton', function(){
        let id = $(this).data('id');
        $.ajax({
          type: 'GET',
          url: '/invoiceFCL/invoice/getDataInvoice-' + id,
          cache: false,
          data: {
            id: id
          },
              dataType: 'json',

              success: function(response) {

            console.log(response);
            $('#editCust').modal('show');
            $("#editCust #id_edit").val(response.data.id);
            $("#editCust #order_no_edit").val(response.data.proforma_no);
            $("#editCust #grand_total_edit").val(response.data.grand_total);
            $("#editCust #no_hp_edit").val(response.data.no_hp);

          },
          error: function(data) {
            console.log('error:', data)
          }
        });
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const ktpInput = document.getElementById('ktp');
    const captureButton = document.getElementById('capture');
    const imagePreviewContainer = document.getElementById('imagePreview'); // Ensure this exists in your HTML
    let mediaStream = null; // Store the media stream for stopping later

    // Function to start webcam
    function startWebcam() {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(stream) {
                mediaStream = stream; // Store the stream
                video.srcObject = stream;
                video.play();
            })
            .catch(function(error) {
                console.error("Error accessing the camera: ", error);
            });
    }

    // Capture the image
    captureButton.addEventListener('click', function() {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convert the captured image to a data URL
        const dataURL = canvas.toDataURL('image/png');
        ktpInput.value = dataURL; // Store the image data URL in the hidden input

        // Replace the video with the captured image
        imagePreviewContainer.innerHTML = `<img src="${dataURL}" style="width: 100%; display: block; margin: 0 auto;">`;
        imagePreviewContainer.style.display = 'block'; // Ensure the container is visible
        video.style.display = 'none'; // Hide the video element

        // Stop the video stream
        if (mediaStream) {
            const tracks = mediaStream.getTracks();
            tracks.forEach(track => track.stop()); // Stop all tracks
        }
    });

    // Listen for the modal shown event to start the webcam
    $('#editCust').on('shown.bs.modal', function() {
        startWebcam();
        captureButton.disabled = false; // Enable the capture button when modal opens
        imagePreviewContainer.innerHTML = ''; // Clear the previous image
        imagePreviewContainer.style.display = 'none'; // Hide the preview initially
        video.style.display = 'block'; // Show the video element
    });
    
    // Optional: Stop the webcam when the modal is closed
    $('#editCust').on('hidden.bs.modal', function() {
        if (mediaStream) {
            const tracks = mediaStream.getTracks();
            tracks.forEach(track => track.stop());
            video.srcObject = null;
        }
        captureButton.disabled = false; // Re-enable capture button when the modal is closed
        video.style.display = 'block'; // Show the video again for the next use
        imagePreviewContainer.innerHTML = ''; // Clear image preview
        imagePreviewContainer.style.display = 'none'; // Hide the preview
    });
});
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