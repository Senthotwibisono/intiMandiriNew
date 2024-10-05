@extends('partial.main')
@section('custom_styles')

<style>
    .table-responsive td,
    .table-responsive th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

@endsection
@section('content')

<section>
    <div class="card">
        <div class="card-header">
            <header>Generate Report</header>
        </div>
        <form action="{{ route('report.lcl.generateCont')}}" method="get">
            @csrf
            <div class="card-body">
                <div class="row mt-0">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Filter By</label>
                            <select name="filter" style="width: 100%;" class="js-example-basic-single">
                                <option disabled selected>Pilih Satu</option>
                                <option value="Tgl PLP">Tgl PLP</option>
                                <option value="Tgl Gate In">Tgl Gate In</option>
                                <option value="Tgl BC 1.1">Tgl BC 1.1</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="">End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-success" type="submit">Generate</button>
            </div>
        </form>
    </div>
</section>

<section>
    <div class="card">
        <div class="card-body">
            <table class="tabelCustom table-responsive" style="overflow-:auto;">
                <thead>
                    <tr>
                        <th class="text-center">Action</th>
                        <th class="text-center">No Job Order</th>
                        <th class="text-center">Nama Angkut</th>
                        <th class="text-center">No Container</th>
                        <th class="text-center">Size</th>
                        <th class="text-center">ETA</th>
                        <th class="text-center">TPS Asal</th>
                        <th class="text-center">Consolidator</th>
                        <th class="text-center">No PLP</th>
                        <th class="text-center">Tgl PLP</th>
                        <th class="text-center">No BC 1.1</th>
                        <th class="text-center">Tgl BC 1.1</th>
                        <th class="text-center">No POS BC 1.1</th>
                        <th class="text-center">Tgl Masuk</th>
                        <th class="text-center">Jam Masuk</th>
                        <th class="text-center">Nomor Polisi</th>
                        <th class="text-center">Tgl Stripping</th>
                        <th class="text-center">Jam Stripping</th>
                        <th class="text-center">Tgl Keluar</th>
                        <th class="text-center">Jam Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($conts as $cont)
                        <tr class="{{ $cont->status_bc !== 'release' ? 'highlight-yellow' : '' }}">
                            <td>
                                <div class="button-container">
                                    <a href="javascript:void(0)" onclick="openWindow('/lcl/report/contPhoto{{$cont->id}}')" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                </div>
                            </td>
                            <td>{{$cont->job->nojoborder}}</td>
                            <td class="text-center">{{$cont->job->PLP->nm_angkut}}</td>
                            <td class="text-center">{{$cont->nocontainer}}</td>
                            <td class="text-center">{{$cont->size}}</td>
                            <td class="text-center">{{$cont->job->eta}}</td>
                            <td class="text-center">{{$cont->job->PLP->kd_tps_asal}}</td>
                            <td class="text-center">{{$cont->job->PLP->namaconsolidator}}</td>
                            <td class="text-center">{{$cont->job->noplp}}</td>
                            <td class="text-center">{{$cont->job->ttgl_plp}}</td>
                            <td class="text-center">{{$cont->job->PLP->no_bc11}}</td>
                            <td class="text-center">{{$cont->job->PLP->tgl_bc11}}</td>
                            <td class="text-center"> </td>
                            <td class="text-center">{{$cont->tglmasuk ?? 'Belum Masuk'}} </td>
                            <td class="text-center">{{$cont->jammasuk ?? 'Belum Masuk'}} </td>
                            <td class="text-center">{{$cont->nopol ?? 'Belum Masuk'}} </td>
                            <td class="text-center">{{$cont->tglstripping ?? 'Belum Stripping'}}</td>
                            <td class="text-center">{{$cont->jamstripping ?? 'Belum Stripping'}}</td>
                            <td class="text-center">{{$cont->tglbuangmty ?? 'Belum Keluar'}}</td>
                            <td class="text-center">{{$cont->jambuangmty ?? 'Belum Keluar'}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection

@section('custom_js')
<script>
    function openWindow(url) {
        window.open(url, '_blank', 'width=1500,height=1000');
    }
</script>
@endsection
