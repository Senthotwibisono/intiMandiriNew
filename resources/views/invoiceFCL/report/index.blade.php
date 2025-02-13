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

<div class="card">
    <form action="/invoiceFCL/invoice/report/excel" method="get" id="generateForm">
        <div class="card-body">
            <div class="row mt-0">
                <div class="col-4">
                    <div class="form-group">
                        <label for="">Filter By</label>
                        <select name="filter[]" id="" style="width: 100%;" class="js-example-basic-multiple select2 form-select" multiple>
                            <option value="Y">Lunas</option>
                            <option value="N">Belum Lunas</option>
                            <option value="C">Cancel</option>
                        </select>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="">Type</label>
                        <select name="type[]" id="" style="width: 100%;" class="js-example-basic-multiple select2 form-select" multiple>
                            <option value="STANDART">STANDART</option>
                            <option value="EXTEND">EXTEND</option>
                            <option value="BCF">BCF</option>
                            <option value="TPP">TPP</option>
                        </select>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="">Generate By</label>
                        <select name="tanggal" id="" style="width: 100%;" class="js-example-basic-single select2 form-select">
                            <option disabled selected value>Pilih Satu</option>
                            <option value="created_at">Tanggal Buat Invoice</option>
                            <option value="lunas_at">Tanggal Bayar Invoice</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">Start Date</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="">End Date</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="button-container">
                <button type="submit" class="btn btn-success">Generate</button>
                <button type="button" class="btn btn-danger" onclick="generatePDF()">Generate PDF</button>
            </div>
        </div>
    </form>
</div>

@endsection

@section('custom_js')
<script>
    function generatePDF() {
        // Ambil nilai dari form untuk membangun URL
        const form = document.getElementById('generateForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();

        // Buka view di tab baru tanpa submit form
        window.open('/invoiceFCL/invoice/report/pdf?' + params, '_blank');
    }
</script>
@endsection