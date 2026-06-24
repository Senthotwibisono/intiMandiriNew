@extends('partial.main')

@section('content')
<h1>Data Container</h1>
<div class="table">
    <table class="tabelCustom table-hover">
        <thead>
            <tr>
                <th>car</th>
                <th>no_cont</th>
                <th>size</th>
                <th>fl_periksa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($containers as $cont)
            <tr>
                <td>{{$cont->car}}</td>
                <td>{{$cont->no_cont}}</td>
                <td>{{$cont->size}}</td>
                <td>{{$cont->fl_periksa}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

Data Manifestes
<div class="table">
    <table class="tabelCustom table-hover">
        <thead>
            <tr>
                <th>car</th>
                <th>jns_kms</th>
                <th>merk_kms</th>
                <th>jml_kms</th>
                <th>fl_periksa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kemasans as $kemasan)
            <tr>
                <td>{{$kemasan->car}}</td>
                <td>{{$kemasan->jns_kms}}</td>
                <td>{{$kemasan->merk_kms}}</td>
                <td>{{$kemasan->jml_kms}}</td>
                <td>{{$kemasan->fl_periksa}}</td>
            @endforeach
        </tbody>
    </table>
</div>

@endsection