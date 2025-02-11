@extends('partial.main')
@section('custom_styles')
<style>
    .select2-hidden {
    display: none !important;
    visibility: hidden;
}

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
@endsection

@section('content')

<body>
    <form action="/invoiceFCL/form/extend/postStep2" method="post">
        @csrf
        <div class="card">
            <div id="printableArea">
                <div class="card-header">
                    <div class="divider divider-left">
                        <div class="divider-text">
                            No Job Order : {{$singleCont->cont->job->nojoborder}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="text-center">
                            <h4><strong>Nota dan Perhitungan Pelayanan Jasa : </strong>Perpanjangan Penumpukan</h4> 
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
                            <div class="row">
                                <div class="col-3">
                                   <strong> Old Invcoie</strong>
                                </div>
                                <div class="col-1">
                                    :
                                </div>
                                <div class="col-8">
                                    {{$form->oldInvoice->invoice_no ?? '-'}}
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
                                   <strong> Nomor BL</strong>
                                </div>
                                <div class="col-1">
                                    :
                                </div>
                                <div class="col-8">
                                    {{$form->nobl}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">
                                   <strong> Tanggal BL AWB</strong>
                                </div>
                                <div class="col-1">
                                    :
                                </div>
                                <div class="col-8">
                                    {{$form->tgl_bl_awb}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">
                                   <strong> Perpanjangan Dari</strong>
                                </div>
                                <div class="col-1">
                                    :
                                </div>
                                <div class="col-8">
                                    {{$form->eta}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">
                                   <strong> Sampai Dengan</strong>
                                </div>
                                <div class="col-1">
                                    :
                                </div>
                                <div class="col-8">
                                    {{$form->etd ?? '-'}}
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
                <hr>
                
        
                <!-- Tarif WMS -->
                <div class="card-body">
                    <h5>Tarif WMS Inti Mandiri</h5>
                    @php
                        $totalWMS = 0; // Initialize the grand total
                    @endphp
                    @foreach($size as $sz)
                        <div class="row">
                            <div class="divider divider-left">
                                <div class="divider-text">
                                    Tarif Container Size : {{$sz}}
                                </div>
                            </div>
                            @foreach($type as $tp)
                                <div class="divider">
                                    <div class="divider-text">
                                        {{$tp}}
                                    </div>
                                </div>
                                @php
                                    $tarif = $tarifWMS->where('size', $sz)->where('type', $tp)->first();
                                    $jumlahCont = $containerInvoice->where('size', $sz)->where('ctr_type', $tp)->count();
                                @endphp
                                <div class="table text-center">
                                    <table class="table-hover table-stripped table-bordered mx-auto">
                                        <thead>
                                            <tr>
                                                <th>Kegiatan</th>
                                                <th>Tarif Dasar</th>
                                                <th>Jumlah Container</th>
                                                <th>Jumlah Hari</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Penumpukkan</td>
                                                <td>{{number_format($tarif->tarif_dasar_massa,0)}} * {{number_format($tarif->massa)}}%</td>
                                                <td>{{$jumlahCont}}</td>
                                                <td>{{$jumlahHariWMS}}</td>
                                                @php
                                                    $totalPenumpukan = (($tarif->tarif_dasar_massa * $tarif->massa)/100)*$jumlahCont*$jumlahHariWMS;
                                                    $totalWMS += $totalPenumpukan;
                                                @endphp
                                                <td>{{number_format($totalPenumpukan, 0)}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    <div class="row text-white p-3">
                        <div class="col-6 text-center">
                            <h4 class="lead ">Total Harga WMS</h4>
                        </div>
                        <div class="col-6" style="text-align:center;">
                            @php
                                $totalWMSAdmin = $totalWMS;
                            @endphp
                            <h4 class="lead ">{{ number_format($totalWMSAdmin, 0) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row text-white p-3">
                        <div class="col-6">
                            @php
                                $total = $totalWMS;
                                $ppn = ($total * 11)/100;
                                $grandTotal = $total + $ppn;
                            @endphp
                            <h4 class="lead ">Total</h4>
                            <h4 class="lead ">PPN 11%</h4>
                            <!-- <h4 class="lead ">PPN (Amount)</h4> -->
                            <h4 class="lead ">Grand Total</h4>
                        </div>
                        <div class="col-6" style="text-align:right;">
                            <h4 class="lead "><span>{{ number_format($total, '0', ',', '.') ?? ''}}</span></h4>
                            <h4 class="lead "><span>{{ number_format($ppn, '0', ',', '.') ?? ''}}</span></h4>
                            <h4 class="lead "><span><strong>{{ number_format($grandTotal, '0', ',', '.') ?? ''}}</strong></span></h4>
                            <input type="hidden" name="form_id" value="{{$form->id}}">
                            <input type="hidden" name="job_id" value="{{$singleCont->cont->job->id}}">
                            <input type="hidden" name="total_wms" value="{{$totalWMS}}">
                            <input type="hidden" name="total" value="{{$total}}">
                            <input type="hidden" name="admin" value="0">
                            <input type="hidden" name="grand_total" value="{{$grandTotal}}">
                            <input type="hidden" name="ppn" value="{{$ppn}}">
                            <input type="hidden" name="job_id" value="{{$singleCont->cont->job->id}}">
                            <input type="hidden" name="massaWMS" value="{{$jumlahHariWMS}}">
                        </div>
                        <hr>
                    </div>
                    <hr>
                </div>
            </div>

            <div class="button-contianer">
                <div class="col-auto">
                    <button type="submit" class="btn btn-success" id="submitButton">Submit</button>
                    <a href="/invoiceFCL/form/createEdit/Step1/{{$form->id}}" class="btn btn-warning">Back</a>
                    <button type="button" class="btn btn-danger cancelButton" id="cancelButton">Cancel</button>
                    <button type="button" onclick="printSection()">Print</button>
                </div>
            </div>
        </div>
    </form>
</body>

@endsection

@section('custom_js')
<script>
    function printSection() {
    window.print();
}
</script>
<script>
    $(document).on('click', '.cancelButton', function(){
        var formId = {{$form->id}};
        console.log('Form Id = ' + formId);

        Swal.fire({
            title: 'Are you sure?',
            text: "Form Invoice akan terhapus jika anda melakukan cancel",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: '/invoiceFCL/form/cancelForm/' + formId, // Adjust route
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}' // Include CSRF token for security
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Cancelled!',
                            text: 'Form Invoice telah dihapus.',
                            icon: 'success'
                        }).then(() => {
                            location.href = '/invoiceFCL/form/index';
                        });
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    })
</script>

@endsection