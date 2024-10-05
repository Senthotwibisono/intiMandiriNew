@extends('partial.main')
@section('custom_styles')

@endsection

@section('content')

<section>
    <div class="card">
        <div class="card-header">
            <a href="javascript:void(0);" class="btn btn-success" id="createForm"><i class="fa fa-plus"></i></a>
        </div>
        <div class="card-body">
            <table class="tabelCustom">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No HBL</th>
                        <th>Tgl. HBL</th>
                        <th>Quantity</th>
                        <th>Customer</th>
                        <th>Kasir</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($forms as $form)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$form->manifest->nohbl ?? ''}}</td>
                            <td>{{$form->manifest->tgl_hbl ?? ''}}</td>
                            <td>{{$form->manifest->quantity ?? ''}}</td>
                            <td>{{$form->customer->name ?? ''}}</td>
                            <td>{{$form->user->name ?? ''}}</td>
                            <td>{{$form->created_at}}</td>
                            <td>
                                <a href="/invoice/form/formStep1/{{$form->id}}" class="btn btn-warning"><i class="fa fa-pencil"></i></a>
                            </td>
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
    document.getElementById('createForm').addEventListener('click', function() {
        fetch('/invoice/form/create', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({})
        }).then(response => response.json())
        .then(data => {
            if (data.id) {
                // Redirect to invoice step1 with the form ID
                window.location.href = `/invoice/form/formStep1/${data.id}`;
            }
        });
    });
</script>
@endsection