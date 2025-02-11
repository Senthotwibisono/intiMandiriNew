@extends('partial.main')
@section('custom_styles')
<style>
    .select2-container--bootstrap-5 .select2-selection {
    border: 1px solid rgb(0, 0, 0) !important; /* Border berwarna biru */
    border-radius: 5px; /* Agar sudutnya sedikit melengkung */
    padding: 6px; /* Tambahkan padding agar terlihat lebih rapi */
}

.select2-container--bootstrap-5 .select2-selection:focus {
    border-color: #0056b3 !important; /* Border berubah saat fokus */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Efek shadow saat fokus */
}
</style>
@endsection
@section('content')
<body>
    <div class="card">
        <div class="card-body">
            <form action="/invoiceFCL/form/extend/step1Post" method="post" id="tarifTPSForm">
                @csrf
                <div class="row">
                    <div class="divider divider-center">
                        <div class="divider-text">
                            Container Information
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">Pilih Nomor Invoice Terdahulu</label>
                            <select name="inv_id" id="inv_id" class="form-select select2" placeholder="Masukan beberapa Karakter Terlebih Dahulu">
                                <option disabled selected value>Pilih Satu</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">No BL Awb</label>
                            <input type="text" name="nobl" id="nobl" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">Container Available</label>
                            <select name="container_id[]" id="container_available" class="select2 js-example-basic-multiple form-select" style="width:100%;" readonly multiple> 

                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="divider divider-center">
                        <div class="divider-text">
                            Customer & Tanggal Keluar
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="">Customer</label>
                            <select name="cust_id" id="cust_id" class="js-example-basic-single select2 form-control">
                                <option disabled selected value>Pilih Satu</option>
                                    @foreach($customers as $cust)
                                        <option value="{{$cust->id}}">{{$cust->name}} -- {{$cust->npwp ?? '-'}}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">Expired Awal</label>
                            <input type="date" class="form-control" name="eta" id="eta">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">Rencana Keluar</label>
                            <input type="date" class="form-control" name="etd">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <div class="container-button">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                <button type="button" id="updateTarifWMS" class="btn btn-primary ml-1"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
            </div>
        </div>
    </div>
</body>
@endsection

@section('custom_js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('updateTarifWMS').addEventListener('click', function (e) {
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
                    document.getElementById('tarifTPSForm').submit();
                }
            });
        });
    });
</script>

<script>
    $(document).on('change', '#inv_id', function(){
        let id = $(this).val();
        Swal.showLoading();
        $.ajax({
            type: 'GET',
            data: 
            {
                _token: '{{ csrf_token() }}',
                id:id
             },
            url: '/invoiceFCL/form/extend/getBLData',
            cache: false,
            dataType: 'json',
            success: function(response){
                swal.close();
                console.log(response);

                if (response.success) {
                    
                    $('#nobl').val(response.data);
                    $('#eta').val(response.dataHeader.etd);
                    $('#cust_id').val(response.customer ? response.customer.id : '').trigger('change');
    
                    let selectContainer = $('#container_available');
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
                } else {
                    Swal.fire('Error', response.message, 'error')
                    .then(() => {
                        location.reload();
                    });

                }
            },
            error: function(response){
                swal.fire({
                    icon: 'error',
                    text: 'Something Wrong: ' + response.responseJSON?.message,
                    title: 'Error',
                });
            }
        })

    })
</script>
<script>
    $(document).ready(function() {
        $('#inv_id').select2({
            theme: "bootstrap-5", // Jika pakai Bootstrap 5
            placeholder: "Pilih NO Inoice",
            allowClear: true,
            ajax: {
                url: '/invoiceFCL/form/extend/getBLAWB', // Route yang menangani permintaan data
                dataType: 'json',
                delay: 250, // Menunda permintaan untuk menghindari terlalu banyak request
                data: function(params) {
                    return {
                        search: params.term, // Mengambil keyword pencarian
                        page: params.page || 1 // Paginasi
                    };
                },
                processResults: function(response) {
                    return {
                        results: $.map(response.data, function(item) {
                            return {
                                id: item.id,
                                text: item.invoice_no // Sesuaikan dengan kolom yang ingin ditampilkan
                            };
                        }),
                        pagination: {
                            more: response.more
                        }
                    };
                },
                cache: true
            }
        });
    });
</script>
@endsection