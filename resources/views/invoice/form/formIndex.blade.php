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

<form action="/invoice/form/submitStep1" method="post" enctype="multipart/form-data">
    @csrf
    <section>
        <div class="card">
            <div class="card-header">
                Invoice Information
            </div>
            <div class="card-body">
                <div class="row mt-0">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Manifest</label>
                            <select name="manifest_id" id="manifest_id" style="width:100%;" class="js-example-basic-single select2 form-select">
                                <option disabled selected value>Pilih Satu !</option>
                                @foreach($manifest as $mans)
                                    <option value="{{$mans->id}}" {{$form->manifest_id == $mans->id ? 'selected' : ''}}>{{$mans->nohbl}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Quantity</label>
                            <input type="number" class="form-control" name="quantity" id="quantity" value="{{$form->manifest->quantity ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Tonase</label>
                            <input type="number" class="form-control" name="weight" id="weight" value="{{$form->manifest->weight ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Volume</label>
                            <input type="number" class="form-control" name="meas" id="meas" value="{{$form->manifest->meas ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="">Volume UP</label>
                            <input type="number" class="form-control" name="cbm" id="cbm" value="{{$form->cbm ?? ''}}" readonly>
                            <input type="hidden" class="form-control" name="id" value="{{$form->id}}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row mt-0">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Customer Name</label>
                            <select name="customer_id" id="customer_id" class="js-example-basic-single select2 form-select" style="width:100%;">
                                <option disabled selected value>Pilih Satu!</option>
                                @foreach($customer as $cus)
                                    <option value="{{$cus->id}}" {{$form->customer_id == $cus->id ? 'selected' : ''}}>{{$cus->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">NPWP</label>
                            <input type="text" class="form-control" id="npwp" value="{{$form->customer->npwp ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Phone</label>
                            <input type="text" class="form-control" id="phone" value="{{$form->customer->phone ?? ''}}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row mt-0">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Forwarding</label>
                            <select name="customer_id" id="customer_id" class="js-example-basic-single select2 form-select" style="width:100%;">
                                <option disabled selected value>Pilih Satu!</option>
                                @foreach($customer as $cus)
                                    <option value="{{$cus->id}}" {{$form->customer_id == $cus->id ? 'selected' : ''}}>{{$cus->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">NPWP</label>
                            <input type="text" class="form-control" id="npwp" value="{{$form->customer->npwp ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Phone</label>
                            <input type="text" class="form-control" id="phone" value="{{$form->customer->phone ?? ''}}" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row mt-0">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Time In</label>
                            <input type="date" name="time_in" id="time_in" class="form-control" value="{{$form->time_in ?? ''}}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Expired</label>
                            <input type="date" name="expired_date" id="" class="form-control" value="{{$form->expired_date ?? ''}}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Master Tarif</label>
                                <select name="tarif_id[]" id="" class="js-example-basic-multiple form-control" style="height: 150%;" multiple="multiple">
                                    @foreach($masterTarif as $tarif)
                                        @if($selectedTarif->contains(function ($selected) use ($tarif) {
                                            return $selected->tarif_id == $tarif->id && $selected->mekanik_y_n != 'Y';
                                        }))
                                            <option value="{{$tarif->id}}" selected>{{$tarif->nama_tarif}}/{{$tarif->kode_tarif}}</option>
                                        @else
                                            <option value="{{$tarif->id}}">{{$tarif->nama_tarif}}/{{$tarif->kode_tarif}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- mekanik -->
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Master Tarif Mekanik</label>
                                <select name="tarifM_id[]" id="" class="js-example-basic-multiple form-control" style="height: 150%;" multiple="multiple">
                                    @foreach($masterTarif as $tarif)
                                        @if($selectedTarif->contains(function ($selected) use ($tarif) {
                                            return $selected->tarif_id == $tarif->id && $selected->mekanik_y_n == 'Y';
                                        }))
                                            <option value="{{$tarif->id}}" selected>{{$tarif->nama_tarif}}/{{$tarif->kode_tarif}}</option>
                                        @else
                                            <option value="{{$tarif->id}}">{{$tarif->nama_tarif}}/{{$tarif->kode_tarif}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
                window.location.href = '/invoice/form/index';
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
                        $('#forwarding').val(data.forwarding);
                        $('#time_in').val(data.tglmasuk);
                        $('#customer_id').val(data.cust).trigger('change');
                    }
                });
            } else {
                // Clear the fields if no manifest is selected
                $('#quantity').val('');
                $('#weight').val('');
                $('#meas').val('');
                $('#cbm').val('');
                $('#time_in').val('');
                $('#customer_id').val('').trigger('change');
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
@endsection