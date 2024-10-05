@extends('partial.main')
@section('content')

<div class="row">
    <div class="col-sm-6">
        <section class="d-flex justify-content-center align-items-center">
            <div class="card">
                <div class="card-header text-center">
                    <h4>LCL</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="" id="lottie-animation" style="width: 500px; height:300px;"></div>
                            <br>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6 section-title mt-0">
                                   
                                    <a class="btn btn-danger" href="/bc/lcl/delivery/behandle">
                                        Behandle Remaining <span class="badge bg-transparent">{{$behandle}}</span>
                                    </a>
                                </div>
                                <div class="col-sm-6 section-title mt-0">
                                    <a class="btn btn-danger" href="/bc/lcl/delivery/gateOut">
                                      Gate Out Remaining <span class="badge bg-transparent">{{$GateOut}}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 text-center">
                            <div class="" id="lottie-2" style="height: 300px;"></div>
                            <br>
                            <hr>
                            <div class="row text-center">
                                <div class="col-sm-12 section-title mt-0">
                                    <a class="btn btn-danger" href="/bc/lcl/realisasi/buangMT">
                                        Waiting Release <span class="badge bg-transparent">{{$contRemaining}}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-sm-6">
       <section class="d-flex justify-content-center align-items-center">
            <div class="card">
                <div class="card-header  text-center">
                    <h4>FCL</h4>
                </div>
                <div class="card-body  text-center">
                    <div class="" id="lottie-3" style="width: 500px; height:360px;"></div>
                    <hr>
                    <div class="col-sm-12">
                        <buttn class="btn btn-outline-primary" disabled>Still in Development</buttn>
                    </div>
                </div>
            </div>
       </section>
    </div>
</div>

@endsection

@section('custom_js')
<script src="{{asset('lottifiles/lokal.min.js')}}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var animation = lottie.loadAnimation({
      container: document.getElementById('lottie-animation'),
      renderer: 'svg',
      loop: true,
      autoplay: true,
      path: '/lottifiles/pack.json' // Ubah path sesuai dengan jalur file Lottie JSON Anda
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var animation = lottie.loadAnimation({
      container: document.getElementById('lottie-2'),
      renderer: 'svg',
      loop: true,
      autoplay: true,
      path: '/lottifiles/97854-imprint-genius-hero.json' // Ubah path sesuai dengan jalur file Lottie JSON Anda
    });
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var animation = lottie.loadAnimation({
      container: document.getElementById('lottie-3'),
      renderer: 'svg',
      loop: true,
      autoplay: true,
      path: '/lottifiles/develop.json' // Ubah path sesuai dengan jalur file Lottie JSON Anda
    });
  });
</script>
@endsection