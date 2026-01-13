@extends('partial.main')

@section('content')

<section>
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <div class="container-button">
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" onClick="openModal()"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table">
                    <table class="table-hover table-stripped" id="tableDriver">
                        <thead class="text-center">
                            <tr>
                                <th>Dirver Name</th>
                                <th>Dirver Code</th>
                                <th>Dirver Phone</th>
                                <th>No KTP</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="addManual" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Add Data Driver</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <div class="modal-body">
                <div class="from-group">
                    <div class="col-12">
                        <label for="">Name</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>
                    <div class="col-12">
                        <label for="">Code</label>
                        <input type="text" name="code" id="code" class="form-control">
                    </div>
                    <div class="col-12">
                        <label for="">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control">
                        <input type="hidden" name="id" id="id" class="form-control">
                    </div>
                    <div class="col-12">
                        <label for="">No KTP</label>
                        <input type="text" name="no_ktp" id="no_ktp" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                <button type="button" class="btn btn-primary ml-1" data-bs-dismiss="modal" onClick="postDriver()"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('custom_js')

<script>
    $(document).ready(function(){
        let excel = {
                        extend: 'excelHtml5',
                        autoFilter: true,
                        sheetName: 'Exported data',
                        className: 'btn btn-outline-success',
                    };
        let pdf = {
                    extend: 'pdfHtml5',
                    text: 'Ekspor PDF',
                    className: 'btn btn-outline-danger',
                    orientation: 'landscape', // Mode lanskap untuk tampilan lebih luas
                    pageSize: 'A1', // Pilihan ukuran kertas (bisa A3, A4, A5, dll.)
                    download: 'open', // Membuka file langsung tanpa mendownload
                    exportOptions: {
                        columns: function (idx, data, node) {
                            return true; // Semua kolom akan diekspor, termasuk yang tersembunyi
                        }
                    },
                    customize: function (doc) {
                        doc.defaultStyle.fontSize = 8; // Mengatur ukuran font agar semua data muat
                        doc.styles.tableHeader.fontSize = 8; // Mengatur ukuran header tabel
                        doc.styles.title.fontSize = 12; // Ukuran font judul
                        doc.pageMargins = [2, 2, 2, 2]; // Mengatur margin halaman
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split(''); 
                    }
                };
        $('#tableDriver').dataTable({
            processing: true,
            serverSide: true,
            dom: 'lBfrtip', // Pastikan ada 'B' untuk menampilkan tombol
            buttons: [
                'copy', 'csv', excel , pdf, 'print'
            ],
            ajax: '{{route('master.driver.data')}}',
            columns:[
                {name:'name', data:'name', className:'text-center'},
                {name:'code', data:'code', className:'text-center'},
                {name:'phone', data:'phone', className:'text-center'},
                {name:'no_ktp', data:'no_ktp', className:'text-center'},
                {name:'edit', data:'edit', className:'text-center'},
                {name:'delete', data:'delete', className:'text-center'},
            ],
        });
    })
</script>

<script>
    async function openModal() {
        showLoading();
        $('#addManual').modal('show').on('shown.bs.modal', function () {
            hideLoading();
        });
    }
    async function getDataDriver(event) {
        showLoading();
        const baseUrl = "{{ route('master.driver.getData', ['id' => ':id']) }}";
        const id = event.getAttribute('data-id');
        const url = baseUrl.replace(':id', id);

        const response = await fetch(url);
        hideLoading();
        // console.log(response);
        if (response.ok) {
            const hasil = await response.json();
            if (hasil.success) {
                $('#addManual').modal('show');
                $('#addManual #name').val(hasil.data.name);
                $('#addManual #code').val(hasil.data.code);
                $('#addManual #phone').val(hasil.data.phone);
                $('#addManual #no_ktp').val(hasil.data.no_ktp);
                $('#addManual #id').val(hasil.data.id);
            } else {
                await errorHasil(hasil);
            }
        } else {
            await errorResponse(response);
        }
    }

    async function postDriver() {
        Swal.fire({
            icon: 'warning',
            title: 'Are You Sure',
            text: 'Data akan terupdate jika ada sudah yakin',
            showCancelButton: true,
        }).then( async (result) =>  {
            if (result.isConfirmed) {
                showLoading();
                const name = document.getElementById('name').value;
                const code = document.getElementById('code').value;
                const phone = document.getElementById('phone').value;
                const id = document.getElementById('id').value;
                const data = {
                    name:name,
                    code:code,
                    phone:phone,
                    no_ktp : document.getElementById('no_ktp').value,
                    id:id,
                };

                const url = "{{route('master.driver.post')}}";
                let response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        data: data,
                    })
                });
                hideLoading();
                console.log(response);
                if (response.ok) {
                    const hasil = await response.json();
                    if (hasil.success) {
                        await successHasil(hasil);
                    } else {
                        await errorHasil(hasil);
                    }
                } else {
                    await errorResponse(response);
                }
            } else {
                return;
            }
        })
    }

    async function deleteDriver(event) {
        Swal.fire({
            icon: 'warning',
            title: 'Apakah anda yakin?',
            text: 'Data akan terhapus jika anda sudah yakin',
            showCancelButton: true,
        }).then(async(result) => {
            if (result.isConfirmed) {
                showLoading();
                const id = event.getAttribute('data-id');
                // console.log(id);
                const url = "{{route('master.driver.delete')}}";
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        id: id,
                    })
                });
                hideLoading();
                if (response.ok) {
                    const hasil = await response.json();
                    if (hasil.success) {
                        await successHasil(hasil);
                    } else {
                        await errorHasil(hasil);
                    }
                } else {
                    await errorResponse(response);
                }
            } else {    
                return;
            }
        })
    }
</script>

@endsection