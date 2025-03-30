@extends('partial.main')
@section('custom_styles')

@endsection
@section('content')

<section>
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <h4>List Permissons</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <form action="{{route('system.permission.post')}}" method="post">
                        @csrf
                        <div class="col-auto">
                            <div class="form-group">
                                <label for="">Name</label>
                                <input type="text" name="name" class="form-control">
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-group">
                                <label for="">Guard Name</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="guard_name" value="web" readonly>
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="table">
                    <table class="tabelCustom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Guard</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissionList as  $perm)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$perm->name}}</td>
                                    <td>{{$perm->guard_name}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@section('custom_js')

@endsection