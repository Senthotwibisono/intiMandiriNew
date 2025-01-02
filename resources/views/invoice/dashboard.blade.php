@extends('partial.main')

@section('custom_styles')
<meta http-equiv="refresh" content="300">
<style>
    .logoiconDashboard {
        transform: scale(0.4);
    }
    .card-header {
        background-color: #f2f2f2;
        padding: 10px;
    }
    .card-body {
        padding: 20px;
    }
    .card-body img {
        max-width: 100%;
        height: auto;
    }
    .text-center p {
        margin: 0;
    }
    .container-recap,
    .manifest-recap {
        font-size: 24px;
        margin-top: 10px;
    }
    #lottie-animation {
        width: 100%;
        height: 100%;
    }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(22, 90px);; /* 4 kolom dengan ukuran 180px */
        scale: 0.75;
    }
    .grid-item {
        width: 90px;
        height: 45px;
        background-color: #f2f2f2;
        border: 1px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .yard-block-text {
        position: absolute;
        z-index: 2;
        color: #000;
        font-weight: bold;
        font-size: 1.5em;
    }
    .selected {
        background-color: #add8e6 !important;
        color: white;
    }

    .detil-grid-container {
        display: grid;
        grid-template-columns: repeat(22, 90px); /* 5 kolom dengan ukuran 100px */
        gap: 0px; /* Jarak antar kotak */
        scale: 0.75;
    }
    .detil-grid-item {
        width: 90px;
        height: 45px;
        background-color: #f2f2f2;
        border: 1px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .card {
        max-width: 100%;
        overflow-x: auto;
    }

    .bg-white {
        background-color: white;
    }

    .bg-red {
        background-color: red !important;
        color: white;
    }

    .bg-green {
        background-color: green !important;
        color: white;
    }

    .bg-yellow {
        background-color: yellow !important;
    }

    .bg-light-gray {
        background-color: #f2f2f2;
    }

    .grid-manifest {
        display: grid;
        grid-template-columns: repeat(22, 90px); /* 5 kolom dengan ukuran 100px */
        gap: 0px; /* Jarak antar kotak */
        scale: 0.75;
    }
    .grid-item-manifest {
        width: 90px;
        height: 45px;
        background-color: #f2f2f2;
        border: 1px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .grid-item-detail {
        position: flex;
        top: 100%;
        left: 0;
        background: #f8f9fa;
        border: 1px solid #ddd;
        padding: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 100;
        display: none;
        width: 10px;
    }

    .grid-item-manifest:hover .grid-item-detail {
        display: block;
    }

    #myDonutChart {
        max-width: 300px;
        max-height: 300px;
    }

    .card-body {
        justify-content: center;
        align-items: center;
    }
    .kotak {
        height: 150px; /* Mengurangi tinggi kotak menjadi 5% dari tinggi viewport */
        width: 150px; /* Mengurangi tinggi kotak menjadi 5% dari tinggi viewport */
        line-height: 10vh; /* Menyesuaikan line-height agar sama dengan tinggi kotak */
        font-size: 12px; 
        background-color: #fff;
        text-align: center;
        border: 1px solid #000000;
        flex: 1;
        margin: 0px;
        border-radius: 0px;
    }

    .rowSide {
        position: flex;
        display: flex; /* Mengaktifkan Flexbox */
        flex-wrap: nowrap; /* Item tetap dalam satu baris (tidak turun ke baris baru) */
        gap: 2px; /* Jarak antar item */
        width: 150px;
    }
    
    .item {
        position: flex;
        padding: 50px;
        background-color: white; /* Warna latar belakang (opsional) */
        border: 1px solid #ddd; /* Border (opsional) */
        border-radius: 2px; /* Sudut membulat (opsional) */
    }

    .item.filled {
        background-color: red;
        color: #fff;
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
                    <p><strong>Daily Recap</strong></p>
                </div>
                <div class="card-body">
                    <p>{{$now}}</p>
                    <div class="table">
                        <table class="table-responsive table-stripped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Tonase</th>
                                    <th>Volume</th>
                                    <th>Masuk</th>
                                    <th>Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>Container</th>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>{{$masukCont}}</td>
                                    <td>{{$keluarCont}}</td>
                                </tr>
                                <tr>
                                    <th>Manifest</th>
                                    <td>{{$tonase}}</td>
                                    <td>{{$volume}}</td>
                                    <td>{{$masukManifest}}</td>
                                    <td>{{$keluarManifest}}</td>
                                </tr>
                            </tbody>
                        </table>
                        <a href="/lcl/report/daily"><p>See more...</p></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card h-100 justify-content-center align-items-center mt-0">
                <div class="card-header text-center">
                    <p><strong>Kapasitas Gudang</strong></p>
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
    
</section>
@endsection

@section('custom_js')
<script src="{{ asset('lottifiles/lokal.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        lottie.loadAnimation({
            container: document.getElementById('lottie-animation'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '/lottifiles/pack.json' // Update the path according to your Lottie JSON file
        });
    });
</script>
<script>
   $(document).on('click', '.formEdit', function() {
    let id = $(this).data('id');
    $.ajax({
      type: 'GET',
      url: '/master/yard-detail-' + id,
      cache: false,
      data: {
        id: id
      },
      dataType: 'json',

      success: function(response) {

        console.log(response);
        $('#editCust').modal('show');
        $("#editCust #yard_block_edit").val(response.data.yard_block);
        $("#editCust #id_edit").val(response.data.id);
        $("#editCust #max_slot_edit").val(response.data.max_slot);
        $("#editCust #max_row_edit").val(response.data.max_row);
        $("#editCust #max_tier_edit").val(response.data.max_tier);
        $("#detilYardLink").attr('href', response.route);
      },
      error: function(data) {
        console.log('error:', data)
      }
    });
  });
</script>

<script>
    var ctx = document.getElementById('myDonutChart').getContext('2d');
    var donutChart = new Chart(ctx, {
        type: 'doughnut', // Tipe chart
        data: {
            labels: ['Terisi', 'Tidak Terisi'], // Label chart
            datasets: [{
                label: 'Kapasitas',
                data: [{{ $persentaseTerisi }}, {{ $persentaseTidakTerisi }}], // Data persentase
                backgroundColor: [
                    'rgba(75, 192, 192, 1)', // Warna untuk 'Terisi'
                    'rgba(211, 211, 211, 1)'  // Warna untuk 'Tidak Terisi' (abu-abu muda)
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
                            return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                        }
                    }
                },
                datalabels: {
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 20
                    },
                    formatter: function(value, context) {
                        if (context.dataIndex === 0) {
                            return context.chart.data.datasets[0].data[0] + '%';
                        } else {
                            return null;
                        }
                    },
                    anchor: 'center',
                    align: 'center'
                }
            }
        }
    });
</script>



@endsection
