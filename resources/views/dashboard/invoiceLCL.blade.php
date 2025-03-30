@extends('partial.main')

@section('custom_styles')

<style>
    #myDonutChart {
        max-width: 300px;
        max-height: 300px;
    }
</style>

@endsection

@section('content')
<section>
    <div class="row mt-0 d-flex align-items-stretch">
        <div class="col-sm-4">
            <div class="card h-100 justify-content-center align-items-center mt-0">
                <div class="card-header text-center">
                    <p><strong>Inti Mandiri || Depo Information System</strong></p>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('logo/IntiMandiri.png') }}" style="width: 80%;" alt="Logo">
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card h-100 justify-content-center align-items-center mt-0">
                <div class="card-header text-center">
                    <h1><strong>Selamat Datang, <b>{{ \Auth::user()->name}}</b></strong></h1>
                    <p id="real-time-clock">{{ \Carbon\Carbon::now() }}</p>
                </div>
                
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card h-100 justify-content-center align-items-center mt-0">
                <div class="card-header text-center">
                    <p><strong>Jumlah Sampai Saat Ini</strong></p>
                </div>
                <div class="card-body">
                    <canvas id="myDonutChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>
<br>
<section>
@endsection
@section('custom_js')
<script>
    function updateClock() {
        const now = new Date();
        const formattedTime = now.toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('real-time-clock').textContent = formattedTime;
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>

<script>
    var ctx = document.getElementById('myDonutChart').getContext('2d');

    var donutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Lunas', 'Belum Bayar', 'Cancel'],
            datasets: [{
                label: 'Kapasitas',
                data: [{{ $lunas ?? 0 }}, {{ $piutang ?? 0 }}, {{ $cancel ?? 0 }}], // Menghindari error jika variabel kosong
                backgroundColor: [
                    'rgb(6, 224, 224)',   // Warna 'Lunas'
                    'rgb(255, 255, 99)',  // Warna 'Belum Bayar'
                    'rgb(255, 98, 98)'    // Warna 'Cancel'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            let value = tooltipItem.raw.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                            return tooltipItem.label + ': ' + value;
                        }
                    }
                }
            }
        }
    });
</script>

@endsection