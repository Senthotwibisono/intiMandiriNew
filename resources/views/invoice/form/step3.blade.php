@extends('partial.main')

@section('custom_styles')
<style>
    .draggable-item {
        cursor: pointer;
        margin: 5px 0;
        padding: 10px;
        border: 1px solid #ccc;
    }

    .draggable-item.selected {
        background-color: #d9edf7;
    }

    .dropzone {
        min-height: 200px;
        border: 2px dashed #ccc;
        padding: 10px;
    }
    tr.selected {
        background-color: #d9edf7;
    }
</style>
@endsection
@section('content')

<form action="/invoice/form/submitStep3" method="post" enctype="multipart/form-data">
    @csrf
        <section id="preinvoice-content">
            @include('invoice.form.preinvoice')
        </section>

        <canvas id="invoice-canvas"></canvas>
    <footer>
        <div class="card">
            <div class="button-container">
                <button class="btn btn-success" type="submit">Submit</button>
                <a href="#" class="btn btn-warning" id="back-button" type="button">Back</a>
                <a class="btn btn-danger Delete" data-id="{{$form->id}}" type="button"><i class="fa fa-close"></i> Batal</a>
            </div>
        </div>
    </footer>
</form>

@endsection
@section('custom_js')

<script>
    // SweetAlert for back button confirmation
    document.getElementById('back-button').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent the default action

        var formId = document.getElementById('id').value;
        Swal.fire({
            title: 'Are you sure?',
            text: "Any unsaved changes will be lost!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, go back!',
            cancelButtonText: 'No, stay here'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to the back URL if confirmed
                window.location.href = '/invoice/form/formStep2/' + formId;
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#manifest_id').on('change', function() {
            var manifestId = $(this).val();

            if (manifestId) {
                $.ajax({
                    url: '/get-manifest-data/' + manifestId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Populate the form fields with the returned data
                        $('#quantity').val(data.quantity);
                        $('#weight').val(data.weight);
                        $('#meas').val(data.meas);
                        $('#cbm').val(data.cbm);
                    }
                });
            } else {
                // Clear the fields if no manifest is selected
                $('#quantity').val('');
                $('#weight').val('');
                $('#meas').val('');
                $('#cbm').val('');
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#customer_id').on('change', function() {
            var customerId = $(this).val();

            if (customerId) {
                $.ajax({
                    url: '/get-customer-data/' + customerId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Populate the form fields with the returned data
                        $('#npwp').val(data.npwp);
                        $('#phone').val(data.phone);
                    }
                });
            } else {
                // Clear the fields if no manifest is selected
                $('#npwp').val('');
                $('#phone').val('');
            }
        });
    });
</script>

<script>
$(document).ready(function() {
    $('.Delete').on('click', function() {
        var formId = $(this).data('id'); // Ambil ID dari data-id atribut

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan bisa mengembalikan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/invoice/form/delete-' + formId, // Ganti dengan endpoint penghapusan Anda
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}' // Sertakan token CSRF untuk keamanan
                    },
                    success: function(response) {
                        Swal.fire(
                            'Dihapus!',
                            'Data berhasil dihapus.',
                            'success'
                        ).then(() => {
                            window.location.href = '/invoice/form/index'; // Arahkan ke halaman beranda setelah penghapusan sukses
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat menghapus data.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var content = document.getElementById('preinvoice-content');
        var canvas = document.getElementById('invoice-canvas');
        var ctx = canvas.getContext('2d');

        // Use html2canvas to render the content to the canvas
        html2canvas(content).then(function(snapshot) {
            // Draw the image to the canvas
            var img = new Image();
            img.src = snapshot.toDataURL();
            img.onload = function() {
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);
            };
        });
    });
</script>
@endsection