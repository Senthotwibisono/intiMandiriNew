@extends('partial.main')

@section('content')
    <body>
        <div class="card">
            <div class="card-header">
                <div class="button-cotnainer">
                    <button type="button" class="btn btn-primary createForm"><i class="fa fa-plus"></i></button>
                </div>
            </div>
        </div>
    </body>
@endsection

@section('custom_js')

<script>
    $(document).on('click', '.createForm', function(){
        Swal.fire({
                title: 'Are you sure?',
                text: "Apakah anda yakin ingin membuat invoice?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    window.location.href = '/invoiceFCL/form/createIndex/Step1';
                }
            });
    })
</script>

@endsection