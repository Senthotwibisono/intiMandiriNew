@extends('partial.main')
@section('custom_styles')

@endsection

@section('content')

<section>
    <div class="card">
        <div class="card-body">
            <table class="tabelCustom">
                <thead>
                    <tr>
                        <th class="text-center">Order No</th>
                        <th class="text-center">No HBL</th>
                        <th class="text-center">Tgl. HBL</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Customer</th>
                        <th class="text-center">Kasir</th>
                        <th class="text-center">Order At</th>
                        <th class="text-center">Pranota</th>
                        <th class="text-center">Photo KTP</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($headers as $form)
                        <tr>
                            <td class="text-center">{{$form->order_no}}</td>
                            <td class="text-center">{{$form->manifest->nohbl ?? ''}}</td>
                            <td class="text-center">{{$form->manifest->tgl_hbl ?? ''}}</td>
                            <td class="text-center">{{$form->manifest->quantity ?? ''}}</td>
                            <td class="text-center">{{$form->customer->name ?? ''}}</td>
                            <td class="text-center">{{$form->kasir->name ?? ''}}</td>
                            <td class="text-center">{{$form->order_at}}</td>
                            <td class="text-center">
                                <a type="button" href="/invoice/pranota-{{$form->id}}" target="_blank" class="btn btn-sm btn-warning text-white"><i class="fa fa-file"></i></a>
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0)" onclick="openWindow('/invoice/photoKTP-{{$form->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                            </td>
                            <td class="text-center">
                                <div class="button-container">
                                    <button class="btn btn-danger" data-id="{{ $form->id }}" id="deleteUser-{{ $form->id }}"><i class="fa fa-trash"></i></button>
                                    <button type="button" id="pay" data-id="{{$form->id}}" class="btn btn-sm btn-success pay"><i class="fa fa-cogs"></i></button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>


<div class="modal fade" id="editCust" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable modal-xl"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Payment Form</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="/invoice/paid" method="POST" id="updateForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
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
                                    <option value="N">Only Update Photo</option>
                                    <option value="Y">Lunas</option>
                                    <option value="P">Piutang</option>
                                </select>
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
@endsection

@section('custom_js')

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
    function openWindow(url) {
        window.open(url, '_blank', 'width=600,height=800');
    }
</script>



@endsection