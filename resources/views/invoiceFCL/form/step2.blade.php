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
    <form action="{{ route('invoice.lcl.postStep2')}}" method="post">
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
                            <h4><strong>Nota dan Perhitungan Pelayanan Jasa : </strong>Penumpukan dan Pergerakan Ekstra</h4> 
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
                                   <strong> ETA</strong>
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
                                   <strong> Tgl Masuk TPS</strong>
                                </div>
                                <div class="col-1">
                                    :
                                </div>
                                <div class="col-8">
                                    {{$tglMasukView ?? '-'}}
                                </div>
                                <input type="hidden" name="tglmasuk" value="{{$singleCont->cont->tglmasuk ?? '-'}}">
                                <input type="hidden" name="kd_tps_asal" value="{{$form->LokasiSandar->kd_tps_asal ?? '-'}}">
                            </div>
                            <div class="row">
                                <div class="col-3">
                                   <strong> Rencana Keluar</strong>
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
                <div class="card-body">
                    <h5>Tarif TPS {{$form->LokasiSandar->kd_tps_asal ?? '-'}}</h5>
                    @php
                        $totalTPS = 0; // Initialize the grand total
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
                                    $hargaTPS = $tarifTPS->where('size', $sz)->where('type', $tp)->first();
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
                                                <td>Penumpukkan Massa 1</td>
                                                <td>0</td>
                                                <td>{{$jumlahCont}}</td>
                                                <td>{{$form->eta}}</td>
                                                <td>0</td>
                                            </tr>
                                            @foreach($tglMasuk as $masuk)
                                                @php
                                                    $jumlahContMassa = $containerInvoice->where('tglmasuk', $masuk)->count();
                                                    $eta = \Carbon\Carbon::parse($form->eta);
                                                    $masuk = \Carbon\Carbon::parse($masuk);
    
                                                    $jumlahHari = $eta->diffInDays($masuk);
    
                                                    // Tentukan massa2TPS dan massa3TPS
                                                    if ($jumlahHari > 1) {
                                                        $massa2TPS = 1;
                                                        $massa3TPS = $jumlahHari - 1;
                                                    } else {
                                                        $massa2TPS = 0;
                                                        $massa3TPS = 0;
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>Penumpukkan Massa 2 (Masuk pd {{$masuk->format('Y-m-d')}})</td>
                                                    <td>{{number_format($hargaTPS->tarif_dasar_massa,2)}} * {{number_format($hargaTPS->massa2)}}%</td>
                                                    <td>{{$jumlahContMassa}}</td>
                                                    <td>{{$massa2TPS}}</td>
                                                    @php
                                                        $totalPenumpukanMassa2 = (($hargaTPS->tarif_dasar_massa * $hargaTPS->massa2)/100)*$jumlahContMassa*$massa2TPS;
                                                        $totalTPS += $totalPenumpukanMassa2;
                                                    @endphp
                                                    <td>{{number_format($totalPenumpukanMassa2, 2)}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Penumpukkan Massa 3 (Masuk pd {{$masuk->format('Y-m-d')}})</td>
                                                    <td>{{number_format($hargaTPS->tarif_dasar_massa,2)}} * {{number_format($hargaTPS->massa3)}}%</td>
                                                    <td>{{$jumlahContMassa}}</td>
                                                    <td>{{$massa3TPS}}</td>
                                                    @php
                                                        $totalPenumpukanMassa3 = (($hargaTPS->tarif_dasar_massa * $hargaTPS->massa3)/100)*$jumlahContMassa*$massa3TPS;
                                                        $totalTPS += $totalPenumpukanMassa3;
                                                    @endphp
                                                    <td>{{number_format($totalPenumpukanMassa3, 2)}}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td>Lift On</td>
                                                <td>{{number_format($hargaTPS->lift_on,2)}}</td>
                                                <td>{{$jumlahCont}}</td>
                                                <td>0</td>
                                                @php
                                                    $totalLiftOnTPS = $hargaTPS->lift_on * $jumlahCont;
                                                    $totalTPS += $totalLiftOnTPS;
                                                @endphp
                                                <td>{{number_format($totalLiftOnTPS, 2)}}</td>
                                            </tr>
                                            <tr>
                                                <td>Gate Pass</td>
                                                <td>{{number_format($hargaTPS->gate_pass,2)}}</td>
                                                <td>{{$jumlahCont}}</td>
                                                <td>0</td>
                                                @php
                                                    $totalGatePassTPS = $hargaTPS->gate_pass * $jumlahCont;
                                                    $totalTPS += $totalGatePassTPS;
                                                @endphp
                                                <td>{{number_format($totalGatePassTPS, 2)}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    <div class="row text-white p-3">
                        <div class="col-6 text-center">
                            <h4 class="lead ">Admin TPS</h4>
                            <h4 class="lead ">Total Harga TPS</h4>
                        </div>
                        <div class="col-6" style="text-align:center;">
                            @php
                                $adminTPS = $tarifTPS->first()->pluck('admin')->first();
                                $totalTPSAdmin = $adminTPS + $totalTPS;
                            @endphp
                            <h4 class="lead ">{{ number_format($adminTPS, 2) }}</h4>
                            <h4 class="lead ">{{ number_format($totalTPSAdmin, 2) }}</h4>
                        </div>
                    </div>
                </div>
        
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
                                        @foreach($tglMasuk as $masuk)
                                        @php
                                        $jumlahContMassaWMS = $containerInvoice->where('tglmasuk', $masuk)->count();
    
                                        $masuk = \Carbon\Carbon::parse($masuk);
                                        $keluar = \Carbon\Carbon::parse($form->etd);
    
                                        $jumlahHariWMSNew = $masuk->diffInDays($keluar) + 1;
    
                                        @endphp
                                        <tbody>
                                            <tr>
                                                <td>Penumpukkan (Masuk pd {{$masuk->format('Y-m-d')}})</td>
                                                <td>{{number_format($tarif->tarif_dasar_massa,2)}} * {{number_format($tarif->massa)}}%</td>
                                                <td>{{$jumlahContMassaWMS}}</td>
                                                <td>{{$jumlahHariWMSNew}}</td>
                                                @php
                                                    $totalPenumpukan = (($tarif->tarif_dasar_massa * $tarif->massa)/100)*$jumlahContMassaWMS*$jumlahHariWMSNew;
                                                    $totalWMS += $totalPenumpukan;
                                                @endphp
                                                <td>{{number_format($totalPenumpukan, 2)}}</td>
                                            </tr>
                                        </tbody>
                                        @endforeach
                                        <tbody>
                                            <tr>
                                                <td>Paket PLP</td>
                                                <td>{{ number_format($tarif->paket_plp, 2) }}</td>
                                                <td>{{ $jumlahCont }}</td>
                                                <td>0</td>
                                                @php
                                                    $total = $tarif->paket_plp * $jumlahCont;
                                                    $totalWMS += $total;
                                                @endphp
                                                <td>{{ number_format($total, 2) }}</td>
                                            </tr>
                                        </tbody>
                                        <tbody>
                                            <tr>
                                                <td>Lift On</td>
                                                <td>{{number_format($tarif->lift_on,2)}}</td>
                                                <td>{{$jumlahCont}}</td>
                                                <td>0</td>
                                                @php
                                                    $totalLiftOn = $tarif->lift_on * $jumlahCont;
                                                    $totalWMS += $totalLiftOn;
                                                @endphp
                                                <td>{{number_format($totalLiftOn, 2)}}</td>
                                            </tr>
                                        </tbody>
                                        <tbody>
                                            <tr>
                                                <td>Lift Off</td>
                                                <td>{{number_format($tarif->lift_off,2)}}</td>
                                                <td>{{$jumlahCont}}</td>
                                                <td>0</td>
                                                @php
                                                    $totalLiftOff = $tarif->lift_off * $jumlahCont;
                                                    $totalWMS += $totalLiftOff;
                                                @endphp
                                                <td>{{number_format($totalLiftOff, 2)}}</td>
                                            </tr>
                                        </tbody>
                                        <tbody>
                                            <tr>
                                                <td>Surcharge</td>
                                                <td>{{number_format($tarif->surcharge,2)}}% dari (PLP, Penumpukan, Lift ON/OFF)</td>
                                                <td>{{$jumlahCont}}</td>
                                                <td>0</td>
                                                @php
                                                    $totalSurcharge = (($total + $totalPenumpukan + $totalLiftOn + $totalLiftOff)*$tarif->surcharge)/100;
                                                    $totalWMS += $totalSurcharge;
                                                @endphp
                                                <td>{{number_format($totalSurcharge, 2)}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    <div class="row text-white p-3">
                        <div class="col-6 text-center">
                            <h4 class="lead ">Admin WMS</h4>
                            <h4 class="lead ">Total Harga WMS</h4>
                        </div>
                        <div class="col-6" style="text-align:center;">
                            @php
                                $adminWMS = $tarifWMS->pluck('admin')->first();
                                $totalWMSAdmin = $totalWMS + $adminWMS
                            @endphp
                            <h4 class="lead ">{{ number_format($adminWMS, 2) }}</h4>
                            <h4 class="lead ">{{ number_format($totalWMSAdmin, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row text-white p-3">
                        <div class="col-6">
                            @php
                                $totalAdmin = $adminTPS + $adminWMS;
                                $total = $totalWMS + $totalTPS + $totalAdmin;
                                $ppn = ($total * 11)/100;
                                $grandTotal = $total + $ppn;
                            @endphp
                            <h4 class="lead ">Total</h4>
                            <h4 class="lead ">PPN 11%</h4>
                            <!-- <h4 class="lead ">PPN (Amount)</h4> -->
                            <h4 class="lead ">Grand Total</h4>
                        </div>
                        <div class="col-6" style="text-align:right;">
                            <h4 class="lead "><span>{{ number_format($total, '2', ',', '.') ?? ''}}</span></h4>
                            <h4 class="lead "><span>{{ number_format($ppn, '2', ',', '.') ?? ''}}</span></h4>
                            <h4 class="lead "><span><strong>{{ number_format($grandTotal, '2', ',', '.') ?? ''}}</strong></span></h4>
                            <input type="hidden" name="form_id" value="{{$form->id}}">
                            <input type="hidden" name="job_id" value="{{$singleCont->cont->job->id}}">
                            <input type="hidden" name="total_tps" value="{{$totalTPS}}">
                            <input type="hidden" name="total_wms" value="{{$totalWMS}}">
                            <input type="hidden" name="total" value="{{$total}}">
                            <input type="hidden" name="admin" value="{{$totalAdmin}}">
                            <input type="hidden" name="grand_total" value="{{$grandTotal}}">
                            <input type="hidden" name="ppn" value="{{$ppn}}">
                            <input type="hidden" name="job_id" value="{{$singleCont->cont->job->id}}">
    
                            <input type="hidden" name="massa2TPS" value="{{$massa2}}">
                            <input type="hidden" name="massa3TPS" value="{{$massa3}}">
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