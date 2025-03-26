@extends('partial.main')
<style>
    @media print {
    /* Sembunyikan seluruh halaman selain yang ingin dicetak */
    body * {
        visibility: hidden;
    }

    #printableArea, #printableArea * {
        visibility: visible;
    }

    #printableArea {
        position: flex;
    }
}
</style>
@section('custom_styles')

@endsection

@section('content')

<section>
    <form action="{{route('invoiceFCL.behandel.createInvoice')}}" method="post" id="formUpdate">
        @csrf
        <div class="page-content" id="printableArea">
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <div class="card-header">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    No Job Order : {{$singleCont->cont->job->nojoborder}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-center">
                                    <h4><strong>Nota dan Perhitungan Pelayanan Jasa : </strong>Behandle</h4> 
                                </div>
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-3">
                                           <strong> Perusahaan</strong>
                                        </div>
                                        <div class="col-1">
                                            :
                                        </div>
                                        <div class="col-8">
                                            {{$form->cust->name ?? '-'}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                           <strong> Alamat</strong>
                                        </div>
                                        <div class="col-1">
                                            :
                                        </div>
                                        <div class="col-8">
                                            {{$form->cust->alamat ?? '-'}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                           <strong> Kapal</strong>
                                        </div>
                                        <div class="col-1">
                                            :
                                        </div>
                                        <div class="col-8">
                                            {{$singleCont->cont->job->Kapal->name ?? '-'}} / {{$singleCont->cont->job->voy ?? '-'}}
                                        </div>
                                        <input type="hidden" value="{{$singleCont->cont->job->Kapal->name ?? '-'}} / {{$singleCont->cont->job->voy ?? '-'}}" name="kapal_voy">
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                           <strong> Ukuran Container</strong>
                                        </div>
                                        <div class="col-1">
                                            :
                                        </div>
                                        <div class="col-8">
                                            {{$jenisContainer}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                           <strong> Tipe Container</strong>
                                        </div>
                                        <div class="col-1">
                                            :
                                        </div>
                                        <div class="col-8">
                                            {{$typeContainer}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-3">
                                           <strong> Nomor Invoice</strong>
                                        </div>
                                        <div class="col-1">
                                            :
                                        </div>
                                        <div class="col-8">
                                            XXXXXXXXXX
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                           <strong> Nomor SPJM</strong>
                                        </div>
                                        <div class="col-1">
                                            :
                                        </div>
                                        <div class="col-8">
                                            {{$form->no_spjm}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                           <strong> Tanggal SPJM</strong>
                                        </div>
                                        <div class="col-1">
                                            :
                                        </div>
                                        <div class="col-8">
                                            {{$form->tgl_spjm}}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                           <strong> No. Container</strong>
                                        </div>
                                        <div class="col-1">
                                            :
                                        </div>
                                        <div class="col-8">
                                            {{$nocontainer ?? '-'}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class="card-body">
                            @php
                                $total = 0;
                            @endphp
                            @foreach($size as $sz)
                            <div class="row">
                                <div class="divider divider-left">
                                    <div class="divider-text">
                                        Tarif untuk container ukuran : {{$sz}}
                                    </div>
                                </div>
                                @foreach($type as $tp)
                                    @php
                                        $jumlahCont = $containers->where('size', $sz)->where('ctr_type', $tp)->count();
                                        $tarif = $tarifs->where('size', $sz)->where('type', $tp)->first();
                                        $bhd = $tarif->behandle * $jumlahCont;
    
                                        $total += $bhd;
                                    @endphp
                                    @if($jumlahCont != null)
                                        <div class="text-left">
                                            <span>Type: <b>{{$tp}}</b></span>
                                        </div>
                                        <div class="table text-center">
                                            <table class="table-hover table-stripped table-bordered mx-auto">
                                                <thead>
                                                    <tr>
                                                        <th>Ukuran</th>
                                                        <th>Type</th>
                                                        <th>Tarif</th>
                                                        <th>Jumlah</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{$sz}}</td>
                                                        <td>{{$tp}}</td>
                                                        <td>{{$tarif->behandle ?? 0}}</td>
                                                        <td>{{$jumlahCont}}</td>
                                                        <td>{{$bhd}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        <div class="card-footer">
                            <div class="row  p-3">
                                <div class="col-6">
                                    @php
                                        $admin = $tarifs->whereNotNull('admin_behandle')->first();
                                        $adminAmount =  $admin->admin_behandle;
                                        $totalAmount = $total + $adminAmount;
                                        $ppn = ($totalAmount * 11)/100;
                                        $grandTotal = $totalAmount + $ppn;
                                    @endphp
                                    <h4 class="lead ">Admin</h4>
                                    <h4 class="lead ">Total</h4>
                                    <h4 class="lead ">PPN 11%</h4>
                                    <h4 class="lead ">Grand Total</h4>
                                </div>
                                <div class="col-6" style="text-align:right;">
                                    <h4 class="lead "><span>{{ number_format($adminAmount, '0', ',', '.') ?? ''}}</span></h4>
                                    <h4 class="lead "><span>{{ number_format($totalAmount, '0', ',', '.') ?? ''}}</span></h4>
                                    <h4 class="lead "><span>{{ number_format($ppn, '0', ',', '.') ?? ''}}</span></h4>
                                    <h4 class="lead "><span><strong>{{ number_format($grandTotal, '0', ',', '.') ?? ''}}</strong></span></h4>
                                    <input type="hidden" name="form_id" value="{{$form->id}}">
                                    <input type="hidden" name="admin" value="{{$adminAmount}}">
                                    <input type="hidden" name="total" value="{{$totalAmount}}">
                                    <input type="hidden" name="ppn" value="{{$ppn}}">
                                    <input type="hidden" name="grandTotal" value="{{$grandTotal}}">
                                </div>
                                <hr>
                                <div class="button-container">
                                    <button type="button" class="btn btn-success" id="submitButton">Submit</button>
                                    <a href="{{ route('invoiceFCL.behandle.step1', $form->id) }}" class="btn btn-warning">Back</a>
                                    <button type="button" class="btn btn-danger" id="cancelButton">Cancel</button> 
                                    <button type="button" onclick="printSection()">Print</button>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

@endsection

@section('custom_js')
<script>
    function printSection() {
    window.print();
}
</script>
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
                    document.getElementById('formUpdate').submit();
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function(){
        $('#cancelButton').on('click', function(){
            var id = {{$form->id}};
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

@endsection