@extends('partial.main')
@section('custom_styles')

@endsection

@section('content')

<section>
    <div class="page-content">
        <div class="card">
            <div class="card-content">
                <div class="card-header">
                    <div class="col-auto">
                        <button class="btn btn-success" id="createButton"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table">
                        <table class="table-hover table-stripped" id="tableForm">
                            <thead>
                                <tr>
                                    <th class="text-center">Action</th>
                                    <th class="text-center">No SPJM</th>
                                    <th class="text-center">Tgl SPJM</th>
                                    <th class="text-center">Created At</th>
                                    <th class="text-center">User</th>
                                    <th class="text-center">Cancel</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('custom_js')
<script>
    $("#tableForm").dataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('invoiceFCL.behandle.formData') }}',
        columns: [
            {className:'text-center', data:'action', name: 'action', orderable:false, searchable:false},
            {className:'text-center', data:'no_spjm', name:'no_spjm'},
            {className:'text-center', data:'tgl_spjm', name:'tgl_spjm'},
            {className:'text-center', data:'created_at', name:'created_at'},
            {className:'text-center', data:'user.name', name:'user.name'},
            {className:'text-center', data:'delete', name: 'delete', orderable:false, searchable:false},
        ],
    });
</script>

<script>
    $(document).ready(function(){
        $('#createButton').on('click', function(){
            Swal.fire({
                icon: 'warning',
                title: 'Apakah anda yakin membuat invoice ini?',
                // title: 'Apakah anda yakin membuat invoice ini?',
                showCancelButton: true,
                cancelButtonColor: '#d33',
            }).then((result) => {
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
                        url: '{{ route('invoiceFCL.behandle.formCreate') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
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
                                        window.location.href = '/invoiceFCL/behandle/form-step1/' + response.id;
                                    }, 2000);
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
                }
            })
        })
    })
</script>
@endsection