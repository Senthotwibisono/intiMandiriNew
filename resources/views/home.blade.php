@extends('partial.main')

@section('custom_styles')
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
        grid-template-columns: repeat(4, 120px); /* 4 kolom dengan ukuran 180px */
        gap: 0px; /* Jarak antar kotak */
    }
    .grid-item {
        width: 120px;
        height: 120px;
        background-color: #f2f2f2;
        border: 1px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        position: relative;
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
        grid-template-columns: repeat(4);
        gap: 0px;
    }
    .detil-grid-item {
        width: 4px;
        height: 4px;
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
        grid-template-columns: repeat(22, 50px);
        gap: 0px;
        align-items: center;
    }
    .grid-item-manifest {
        width: 50px;
        height: 20px;
        background-color: #f2f2f2;
        border: 1px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    #myDonutChart {
        max-width: 300px;
        max-height: 300px;
    }

    .card-body {
        justify-content: center;
        align-items: center;
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
                        <a href=""><p>See more...</p></a>
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
    <div class="row mt-0">
        <!-- <div class="col-4">
            <div class="card text-center">
                <div class="card-header">
                    <h4>Yard Condition</h4>
                </div>
                <div class="card-body">
                    <div class="grid-container">
                        @foreach($yard as $item)
                            @php
                                $bgColorClass = $item->yard_block ? 'bg-yellow' : '';
                            @endphp
                            <div class="card grid-item formEdit {{ $bgColorClass }}" data-id="{{$item->id}}">
                                @if($item->yard_block)
                                    <span class="yard-block-text">
                                        {{ $item->yard_block }}
                                        <br>
                                        Terisi: {{ $item->percentage_filled }}%
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div> -->
        <div class="col-sm-12">
            <div class="card justify-content-center align-items-center mt-0">
                <div class="card-header">
                    <h4>Rack Condition</h4>
                </div>
                <div class="card-body grid-manifest justify-content-center align-items-center mt-0">
                    @foreach($gudang as $item)
                        @php
                            $bgColorClass = match($item->use_for) {
                                'M' => 'bg-white',
                                'D' => 'bg-red',
                                'B' => 'bg-green',
                                'L' => 'bg-yellow',
                                default => ''
                            };
                        @endphp
                        <div class="grid-item-manifest {{ $bgColorClass }}" onclick="toggleSelection(this)">
                            @if($item->jumlah_barang >= 1 )
                                {{$item->jumlah_barang}}
                            @else
                                {{$item->name ?? ''}}
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="editCust" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Yard Detail</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Yard Block</label>
                    <input readonly type="text" class="form-control" name="yard_block" id="yard_block_edit">
                    <input readonly type="hidden" class="form-control" name="id" id="id_edit">
                </div>
                <div class="form-group">
                    <label for="">Max Slot</label>
                    <input readonly type="text" class="form-control" name="max_slot" id="max_slot_edit">
                </div>
                <div class="form-group">
                    <label for="">Max Row</label>
                    <input readonly type="text" class="form-control" name="max_row" id="max_row_edit">
                </div>
                <div class="form-group">
                    <label for="">Max Tier</label>
                    <input readonly type="text" class="form-control" name="max_tier" id="max_tier_edit">
                </div>
                <br>
                <div class="button-container">
                    <a href="" class="btn btn-info detilYard" id="detilYardLink">View Detil</a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
            </div>
        </div>
    </div>
</div>
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
