@extends('partial.main')

@section('content')
<section>
    <div class="card">
        <div class="card-body">
            <!-- <div class="row">
                <div class="col-auto ms-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addManual">Add Data</button>
                </div>
            </div> -->
            <br>
            <table class="tabelCustom table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>No Job Order</th>
                        <th>No SPK</th>
                        <th>No Container</th>
                        <th>No MBL</th>
                        <th>ETA</th>
                        <th>Vessel</th>
                        <th>UID</th>
                    </tr>
                    <tbody>
                        @foreach($conts as $cont)
                            <tr>
                                <td>
                                    <a href="/lcl/manifest/detail-{{$cont->id}}" class="btn btn-warning"><i class="fa fa-pen"></i></a>
                                </td>
                                <td>{{$cont->job->nojoborder}}</td>
                                <td>{{$cont->job->nospk}}</td>
                                <td>{{$cont->nocontainer}}</td>
                                <td>{{$cont->job->nombl}}</td>
                                <td>{{$cont->job->eta}}</td>
                                <td>{{$cont->job->Kapal->name ?? ''}}</td>
                                <td>{{$cont->user->name}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </thead>
            </table>
        </div>
    </div>
</section>
@endsection
@section('custom_js')
@endsection