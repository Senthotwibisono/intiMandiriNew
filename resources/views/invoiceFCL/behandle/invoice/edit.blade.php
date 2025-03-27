@extends('partial.main')
@section('custom_styles')
<style>
    .spinner-text {
        animation: blinkSpinner 1.5s infinite;
    }

    @keyframes blinkSpinner {
        0% { opacity: 0.2; }
        50% { opacity: 1; }
        100% { opacity: 0.2; }
    }
</style>
<style>
    .highlight-blue {
        background-color: lightblue !important;;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        @if($header->flag_hidden == 'Y')
        <h4 class="text-danger spinner-text">Invoice Ini Telah di Sembunyikan Pada {{$header->hidden_at ?? '-'}}, oleh {{$header->userHidden->name ?? '-'}}</h4>
        @endif
    </div>
    <div class="card-body">
        <form action="/invoiceFCL/invoice/updateInvoice" method="post" id="submitForm">
            @csrf
            <div class="divider divider-center">
                <div class="divider-text">
                    General Information
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="">Invoice No</label>
                        <input type="text" class="form-control" value="{{$header->invoice_no ?? '-'}}" readonly>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="">Proforma No</label>
                        <input type="text" class="form-control" value="{{$header->proforma_no ?? '-'}}" readonly>
                    </div>
                </div>
                <!-- Cust -->
                 <div class="divider divider-left">
                    <div class="divider-text">
                        Customer Information
                    </div>
                 </div>
                 <div class="col-3">
                    <div class="form-group">
                        <label for="">Customer Name</label>
                        <input type="text" class="form-control" value="{{$header->customer_name ?? '-'}}" name="customer_name">
                    </div>
                 </div>
                 <div class="col-3">
                    <div class="form-group">
                        <label for="">Customer NPWP</label>
                        <input type="text" class="form-control" value="{{$header->customer_npwp ?? '-'}}" name="customer_npwp">
                    </div>
                 </div>
                 <div class="col-3">
                    <div class="form-group">
                        <label for="">Customer Fax</label>
                        <input type="text" class="form-control" value="{{$header->customer_fax ?? '-'}}" name="customer_fax">
                    </div>
                 </div>
                 <div class="col-3">
                    <div class="form-group">
                        <label for="">Customer Alamat</label>
                        <textarea class="form-control" id="" name="customer_alamat">{{$header->customer_alamat ?? '-'}}</textarea>
                    </div>
                 </div>
                 <div class="divider divider-left">
                    <div class="divider-text">
                        Cash Information
                    </div>
                 </div>
                 <div class="col-4">
                    <div class="form-group">
                        <label for="">Total</label>
                        <input type="number" class="form-control" step="0.01" value="{{ number_format($header->total, 2, '.', '') }}" name="total">
                    </div>
                 </div>
                 <div class="col-4">
                    <div class="form-group">
                        <label for="">Admin</label>
                        <input type="number" class="form-control" step="0.01" value="{{ number_format($header->admin, 0, '.', '') }}" name="admin">
                    </div>
                 </div>
                 <div class="col-4">
                    <div class="form-group">
                        <label for="">PPN</label>
                        <input type="number" class="form-control" step="0.01" value="{{ number_format($header->ppn, 0, '.', '') }}" name="ppn">
                    </div>
                 </div>
                 <div class="col-4">
                    <div class="form-group">
                        <label for="">Grand Total</label>
                        <input type="number" class="form-control" step="0.01" value="{{ number_format($header->grand_total, 0, '.', '') }}" name="grand_total">
                    </div>
                 </div>
                 <div class="divider divider-left">
                    <div class="divider-text">
                        Edit Able
                    </div>
                 </div>
                 <div class="col-6">
                    <div class="form-group">
                        <label for="">Create At</label>
                        <input type="datetime-local" class="form-control" value="{{$header->order_at}}" name="order_at">
                        <input type="hidden" name="id" value="{{$header->id}}">
                    </div>
                 </div>
                 <div class="col-6">
                    <div class="form-group">
                        <label for="">Lunas At</label>
                        <input type="datetime-local" class="form-control" value="{{$header->lunas_at}}" name="lunas_at">
                    </div>
                 </div>
            </div>
        </form>
    </div>
    <div class="card-footer">
        <div class="button-container">
            <div class="col-auto">
                <a href="/invoiceFCL/behandle/invoice-index" class="btn btn-warning">Back</a>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="button" id="submitButton">Submit</button>
            </div>
            <!-- Hidden -->
            @if($header->flag_hidden == 'N')
                <div class="col-auto">
                    <button class="btn btn-danger" type="button" data-id="{{$header->id}}" id="hiddenInvoice">Make This Invoice Hidden</button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('custom_js')

<script>
    document.addEventListener('DOMContentLoaded', function(){
        document.getElementById('hiddenInvoice').addEventListener('click', function(){
            hidden(this);
        })
    })

    async function hidden(element) {
        var id = element.getAttribute('data-id');
        console.log('id : ' + id);
        const confirm = await Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'This invoice number will be change',
            showCancelButton: true,
        });
        // console.log(confirm);
        if (confirm.isConfirmed) {
            const { value: password } = await Swal.fire({
              title: "Enter your password",
              input: "password",
              inputLabel: "Password",
              inputPlaceholder: "Enter your password",
              inputAttributes: {
                maxlength: "10",
                autocapitalize: "off",
                autocorrect: "off"
              }
            });
            if (password) {
              if (password == '4646') {
                    Swal.fire({
                        title: 'Bentaran ya bang ini di proses dulu',
                        allowOutsideClick: false,
                        didOpen : async () => {
                            swal.showLoading();
                            try {
                                let response = await fetch('{{route('invoiceFCL.behandle.hiddenInvoice')}}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        id: id,
                                    })
                                });
                                console.log(response);
                                let result = await response.json();
                                console.log(result);
                                if (response.status == 200) {
                                    if (result.success == true) {     
                                        swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil, horeee',
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Aduh error bang rob, ',
                                            text: 'ini error nya bang: ' + result.message,
                                        });
                                    }
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Aduh error bang rob, ',
                                        text: 'ini error nya bang: ' + response.status + ' ' + response.statusText,
                                    });
                                }
                            } catch (error) {
                                console.log(error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Aduh error bang rob, ',
                                });
                            }
                        }
                    }) 
              }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Passwordnya salah bang rob',
                })
              }
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Password wajib di isi bang robbb',
                });
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oke, tidak jadi ya...',
            });
        }
    }
</script>

@endsection