<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title><?= $title ?> | Icon Sarana</title>

<link rel="stylesheet" href="{{asset('dist/assets/css/main/app.css')}}">
<link rel="shortcut icon" href="{{asset('logo/icon.png')}}" type="image/x-icon">
<link rel="shortcut icon" href="{{asset('logo/icon.png')}}" type="image/png">

<link rel="stylesheet" href="{{asset('dist/assets/css/shared/iconly.css')}}">
<link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
<link rel="stylesheet" href="{{asset('dist/assets/css/pages/datatables.css')}}">
<link rel="stylesheet" href="{{asset('dist/assets/extensions/simple-datatables/style.css')}}">
<link rel="stylesheet" href="{{asset('dist/assets/css/pages/simple-datatables.css')}}">
<link rel="stylesheet" href="{{asset('dist/assets/extensions/sweetalert2/sweetalert2.min.css')}}">
<link rel="stylesheet" href="{{ asset('select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('query-ui/jquery-ui.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('query-ui/jquery-ui.min.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<!-- Select 2 js -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- flatpickr js  -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Dropify css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" integrity="sha512-EZSUkJWTjzDlspOoPSpUFR0o0Xy7jdzW//6qhUkoZ9c4StFkVsp9fbbd0O06p9ELS3H486m4wmrCELjza4JEog==" crossorigin="anonymous" />

<!-- daterange picker -->
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />


<style>
  .logoicon {
    transform: scale(3);
  }

  .round-image-3 {
    width: 40px;
    /* Sesuaikan dengan lebar yang diinginkan */
    height: 40px;
    /* Sesuaikan dengan tinggi yang diinginkan */
    border-radius: 50%;
    overflow: hidden;
  }
</style>
@if(View::hasSection('custom_styles'))
@yield('custom_styles')
@endif