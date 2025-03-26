@extends('partial.main')

@section('custom_styles')

@endsection

@section('content')

<section>
    <div class="page-content">
        <form action="{{route('invoiceFCL.behandle.postStep1')}}" method="post" id="formPost">
            @csrf
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">No SPJM</label>
                                    <input type="text" class="form-control" id="no_spjm" name="no_spjm" value="{{$form->no_spjm}}">
                                    <input type="hidden" class="form-control" id="id" name="id" value="{{$form->id}}">
                                </div>
                            </div>
    
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="">Tgl SPJM</label>
                                    <div class="input-group mb-3">
                                        <input type="date" class="form-control" id="tgl_spjm" name="tgl_spjm" value="{{$form->tgl_spjm}}">
                                        <button type="button" class="btn btn-primary" id="searchSPJM"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">List Container</label>
                            <select name="container_id[]" id="container_id" class="js-example-basic-multiple select2 form-control" multiple> 
                                @foreach($selectedContainers as $cont)
                                    <option value="{{$cont->id}}" selected>{{$cont->nocontainer}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Customer</label>
                            <select name="customer_id" id="customer_id" class="js-example-basic-multiple select2 form-control">
                                <option disabled selected value>Pilih Satu!</option>
                                @foreach($customers as $customer)
                                    <option value="{{$customer->id}}" {{$form->customer_id == $customer->id ? 'selected' : ''}}>{{$customer->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="button-container">
                            <div class="col-auto">
                                <button type="button" class="btn btn-success" id="submitButton">Submit</button>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-danger" id="cancelButton">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection

@section('custom_js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('submitButton').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Pastikan Data yang Anda Masukkan sudah Benar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    document.getElementById('formPost').submit();
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function(){
        $('#cancelButton').on('click', function(){
            var id = $('#id').val();
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
                                        window.location.href = '{{route('invoiceFCL.behandle.formIndex')}}';
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
    $('#searchSPJM').on('click', function(){
        var no_spjm = $('#no_spjm').val();
        var tgl_spjm = $('#tgl_spjm').val();
        console.log('No & Tgl SPJM : ' + no_spjm + ', ' + tgl_spjm);
        Swal.showLoading();

        $.ajax({
            url: '{{ route('invoiceFCL.behandle.getContainer') }}',
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
                no_spjm : no_spjm,
                tgl_spjm : tgl_spjm,
            },
            cache: false,
            dataType: 'json',
            success: function (response) {
                console.log(response);
                if (response.success) {
                    swal.close();
                    let selectContainer = $('#container_id');
                    selectContainer.empty();
    
                    let selectedValues = []; // Array untuk menyimpan semua ID
    
                    // Tambahkan data ke dalam Select2 dan kumpulkan semua ID
                    $.each(response.containers, function(index, item) {
                        let option = new Option(item.nocontainer, item.id, false, false);
                        selectContainer.append(option);
                        selectedValues.push(item.id); // Tambahkan ID ke array
                    });
    
                    // Pilih semua item setelah data ditambahkan
                    if (selectedValues.length > 0) {
                        selectContainer.val(selectedValues).trigger('change');
                    }
    
                    // Refresh Select2 agar data baru muncul
                    selectContainer.trigger('change');
                    $('#customer_id').val(response.customer_id).trigger('change');
                } else {
                    Swal.fire('Error', response.message, 'error')
                    .then(() => {
                        location.reload();
                    });
                }
            },
            error: function(response) {
                swal.fire({
                    icon: 'error',
                    text: 'Something Wrong: ' + response.responseJSON?.message,
                    title: 'Error',
                });
            }
        })
    })
</script>

@endsection