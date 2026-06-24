@extends('partial.main')

@section('content')


<div class="page-content">
    <div class="card">
        <div class="card-body d-flex justify-content-center align-items-center">
            <div class="col-6">
                <div class="form-group text-center">
                    <label>Select Time</label>
                    <input type="date" name="date" id="reportTime" class="form-control">
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-6 d-flex justify-content-center align-items-center">
                    <form action="/fcl/report/opname/sppb" method="POST">
                        @csrf
                        <input type="hidden" name="date" class="selectedDate">
                        <button type="submit" class="btn btn-success">
                            Download SPPB
                        </button>
                    </form>
                </div>

                <div class="col-6 d-flex justify-content-center align-items-center">
                    <form action="/fcl/report/opname/spjm" method="POST">
                        @csrf
                        <input type="hidden" name="date" class="selectedDate">
                        <button type="submit" class="btn btn-success">
                            Download Behandle
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            this.querySelector('.selectedDate').value =
                document.getElementById('reportTime').value;
        });
    });
</script>


@endsection