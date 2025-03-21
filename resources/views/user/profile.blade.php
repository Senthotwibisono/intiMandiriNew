@extends('partial.main')
@section('custom_styles')
<style>
    .filepond--drop-label {
    	color: #4c4e53;
    }

    .filepond--label-action {
    	text-decoration-color: #babdc0;
    }

    .filepond--panel-root {
    	background-color: #edf0f4;
    }
    .filepond--root {
        max-width: 400px; /* Sesuaikan ukuran */
        height: 400px;
    }
</style>
@endsection

@section('content')
@vite(['resources/js/app.js'])
<section>
    <div class="row">
        <div class="col-4">
            <input type="file" class="filepond" name="filepond" accept="image/png, image/jpeg, image/gif" style="max-width: 150px; height: 150px;" />
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ \Auth::user()->name ?? '-'}}">
                    </div>
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="text" class="form-control" name="email" id="email" value="{{ \Auth::user()->email ?? '-'}}">
                    </div>
                    <div class="form-group">
                        <label for="">Pasword</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="********">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-footer">
                    <div class="container-button">
                        <button class="btn btn-success" id="submit">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('custom_js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        FilePond.registerPlugin(
            FilePondPluginFileValidateType,
            FilePondPluginImageExifOrientation,
            FilePondPluginImagePreview,
            FilePondPluginImageCrop,
            FilePondPluginImageResize,
            FilePondPluginImageTransform,
            FilePondPluginImageEdit
        );

        // Ambil elemen filepond
        const fileInput = document.querySelector('.filepond');
        const pond = FilePond.create(fileInput, {
            labelIdle: `Drag & Drop your picture or <span class="filepond--label-action">Browse</span>`,
            imagePreviewHeight: 170,
            imageCropAspectRatio: '1:1',
            imageResizeTargetWidth: 200,
            imageResizeTargetHeight: 200,
            stylePanelLayout: 'compact circle',
            styleLoadIndicatorPosition: 'center bottom',
            styleProgressIndicatorPosition: 'right bottom',
            styleButtonRemoveItemPosition: 'left bottom',
            styleButtonProcessItemPosition: 'right bottom',
        });

        // Cek apakah user memiliki foto profil
        var existingPhotoUrl = "{{ Auth::user()->profile ? asset('storage/profil/'.Auth::user()->profile) : '' }}";
        console.log("Photo URL:", existingPhotoUrl); // Debugging

        if (existingPhotoUrl && existingPhotoUrl !== '') {
            pond.addFile(existingPhotoUrl);
        }
    });
</script>

<script>
    $('#submit').on('click', function(){
        var pond = FilePond.find(document.querySelector('.filepond'));
        var files = pond.getFiles().map(fileItem => fileItem.file);
        var formData = new FormData();
        formData.append('name', $('#name').val());
        formData.append('email', $('#email').val());
        formData.append('password', $('#password').val());

        // Ambil file pertama jika ada
        if (files.length > 0) {
            formData.append('file', files[0]); 
        }

        Swal.fire({
          title: "Do you want to save the changes?",
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: "Save",
          denyButtonText: `Don't save`
        }).then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) { 
                Swal.fire({
                    title: 'Mengirim ulang...',
                    html: 'Harap tunggu...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading(); // Menampilkan loading animasi
                    }
                });
                $.ajax({
                    url: '{{ route('user.profile.update')}}',
                    type: 'POST',
                    data: formData,
                    processData: false,  // Mencegah jQuery mengubah data
                    contentType: false,  // Mencegah penggunaan content-type default
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(response) {
                        console.log(response);
                        if (response.success == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Behasil!',
                                text: response.message,
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message,
                            });
                        }
                    },
                    error: function (response) {
                        console.log(response.responseJSON.message);
                        var errorMessages = response.responseJSON.message;
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Opss something wrong! : ' + errorMessages,
                        });
                    }
                });
            // Swal.fire("Saved!", "", "success");
          } else if (result.isDenied) {
            Swal.fire("Changes are not saved", "", "info");
          }
        });
    })
</script>
@endsection