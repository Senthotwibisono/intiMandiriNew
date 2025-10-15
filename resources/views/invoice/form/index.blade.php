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
            <div class="table">
                <table class="table-hover table-striped" id="tableForm">
                    <thead>
                        <tr>
                            <th>No HBL</th>
                            <th>Tgl. HBL</th>
                            <th>Quantity</th>
                            <th>Customer</th>
                            <th>Kasir</th>
                            <th>Created At</th>
                            <th>Action</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                   
                </table>
            </div>
        </div>
    </div>
</section>

@endsection

@section('custom_js')
<script>
    $(document).ready(function() {
        $('#tableForm').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollY: '50vh',
            ajax: '{{route('form.data')}}',
            order: [[5, 'desc']],
            columns: [
                {data: 'manifest.nohbl', name: 'manifest.nohbl', className: 'text-center'},
                {data: 'manifest.tgl_hbl', name: 'manifest.tgl_hbl', className: 'text-center'},
                {data: 'manifest.quantity', name: 'manifest.quantity', className: 'text-center'},
                {data: 'customer.name', name: 'customer.name', className: 'text-center'},
                {data: 'user.name', name: 'user.name', className: 'text-center'},
                {data: 'created_at', name: 'created_at', className: 'text-center'},
                {data: 'action', name: 'action', className: 'text-center'},
                {data: 'deleteInvoice', name: 'deleteInvoice', className: 'text-center'},
            ]
        })
    });
</script>
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

<script>
$(document).ready(function() {
    // Gunakan event delegation
    $(document).on('click', '.deleteInvoice', function() {
        var formId = $(this).data('id');
        console.log(formId);

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan bisa mengembalikan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/invoice/form/delete-' + formId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire(
                            'Dihapus!',
                            'Data berhasil dihapus.',
                            'success'
                        ).then(() => {
                            // reload DataTable agar data terupdate
                            $('#tableForm').DataTable().ajax.reload();
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat menghapus data.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endsection