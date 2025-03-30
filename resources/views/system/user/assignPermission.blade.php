@extends('partial.main')
@section('custom_styles')

@endsection
@section('content')

<div class="card">
    <div class="card-body">
        <form action="/user/permission-post/{{$users->id}}" method="post">
            @csrf
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select_all"></th>
                        <th>Permission Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permission as $perm)
                    <tr>
                        <td>
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="permission-checkbox" {{ $users->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                        </td>
                        <td>{{ $perm->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
    </div>
</div>

@endsection
@section('custom_js')
<script>
    document.getElementById('select_all').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.permission-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
@endsection