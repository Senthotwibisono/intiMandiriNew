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
    <form action="/invoice/reportGenerateExcel" method="get" id="generateForm">
    <div class="card-body">
        <div class="row mt-0">
            <div class="form-group">
                <label for="">Filter By</label>
                <select name="filter" id="" style="width: 100%;" class="js-example-basic-single select2 form-select">
                    <option disabled selected value>Pilih Satu</option>
                    <option value="L">Lunas</option>
                    <option value="N">Belum Lunas</option>
                </select>
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
        // Change the form action to point to the PDF generation endpoint
        const form = document.getElementById('generateForm');
        form.action = '/invoice/reportGeneratePdf';
        form.submit();
    }
</script>
@endsection