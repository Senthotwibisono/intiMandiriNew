@extends('partial.main')
@section('custom_styles')

@endsection
@section('content')

<section>
    <div class="card">
    <div class="card-body">
            <div style="overflow-x:auto;">
                <table class="tabelCustom">
                    <thead>
                        <tr>
                            <th class="text-center">Action</th>
                            <th class="text-center">No HBL</th>
                            <th class="text-center">Tgl HBL</th>
                            <th class="text-center">No Tally</th>
                            <th class="text-center">Barcode</th>
                            <th class="text-center">Shipper</th>
                            <th class="text-center">Customer</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Packing</th>
                            <th class="text-center">Kode Kemas</th>
                            <th class="text-center">Desc</th>
                            <th class="text-center">Weight</th>
                            <th class="text-center">Meas</th>
                            <th class="text-center">Tgl Mulai Stripping</th>
                            <th class="text-center">Tgl Selesai Stripping</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($manifest as $mans)
                            <tr>
                                <td>
                                    <div class="button-container">
                                        <a href="/lcl/realisasi/racking/detail-{{$mans->id}}" class="btn btn-warning editButton"><i class="fa fa-pencil"></i></a>
                                    </div>
                                </td>
                                <td>{{$mans->nohbl}}</td>
                                <td>{{$mans->tgl_hbl}}</td>
                                <td>{{$mans->notally}}</td>
                                <td>{{$mans->barcode}}</td>
                                <td>{{$mans->shipperM->name ?? ''}}</td>
                                <td>{{$mans->customer->name ?? ''}}</td>
                                <td>{{$mans->quantity}}</td>
                                <td>{{$mans->packing->name ?? ''}}</td>
                                <td>{{$mans->packing->code ?? ''}}</td>
                                <td>
                                    <textarea class="form-control" cols="3" readonly>{{$mans->descofgoods}}</textarea>
                                </td>
                                <td>{{$mans->weight}}</td>
                                <td>{{$mans->meas}}</td>
                                <td>{{$mans->startstripping}}</td>
                                <td>{{$mans->endstripping}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@endsection
@section('custom_js')

@endsection