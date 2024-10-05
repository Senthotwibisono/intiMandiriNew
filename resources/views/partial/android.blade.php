<!DOCTYPE html>
<html lang="en">

<head>
    @include('partial.head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @media (max-width: 480px) {

            .button {
                font-size: 1rem !important;
            }

            .btn-smaller {
                padding: 0.15rem 0.15rem !important;
            }

            s .card-header,
            .card-body {
                padding: 0.2rem !important;
                font-size: 10px !important;
            }

            /* Reduce modal size */
            .modal.fade.text-left .modal-dialog {
                max-width: 100% !important;
                font-size: 10px !important;
            }

            /* If you want to further adjust modal content size, you can do so by targeting modal content elements here. */
            .modal.fade.text-left .modal-body {
                max-width: 100% !important;
                font-size: 12px !important;
                /* Your styles for modal content here */
            }

            .modal-content {
                max-width: 100% !important;
                font-size: 10px !important;
                /* Your styles for modal content here */
            }

            #container-list {
                /* Add your custom styles here to make it smaller */
                max-width: 100% !important;
                /* Adjust the width to your desired size */
                margin: 0 auto !important;
                /* Center the div horizontally */
                padding: 5px !important;
                /* Add padding for spacing inside the div */
            }
        }
    </style>
</head>

<body class="theme-dark" style="overflow-y: auto;">
    <div class="container-fluid">
        <button onclick="goBack()" style="background: none; border: none;">
            <i class="bi bi-arrow-left" style="font-size: 50px; color: lightskyblue;"></i>
        </button>
    </div>
    <div class="page-heading">
        @yield('content')
    </div>
    <footer>
        @include('partial.footer')
    </footer>
    <!-- Essential scripts only -->
    <script src="{{asset('dist/assets/js/bootstrap.js')}}"></script>
    <script src="{{asset('dist/assets/js/app.js')}}"></script>
    <script src="{{asset('fontawesome/js/all.js')}}"></script>
    <script src="{{asset('fontawesome/js/all.min.js')}}"></script>
    <script src="{{asset('dist/assets/extensions/simple-datatables/umd/simple-datatables.js')}}"></script>
    <script src="{{asset('dist/assets/js/pages/simple-datatables.js')}}"></script>
    <script src="{{asset('dist/assets/extensions/choices.js/public/assets/scripts/choices.js')}}"></script>
    <script src="{{asset('dist/assets/js/pages/form-element-select.js')}}"></script>
    <script src="{{asset('dist/assets/extensions/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{asset('dist/assets/js/pages/sweetalert2.js')}}"></script>
    <script src="{{ asset('vendor/components/jquery/jquery.min.js') }}"></script>
    @yield('custom_js')
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
     @if (\Session::has('success'))
  <script type="text/javascript">
    // Add CSRF token to the headers
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    var successMessage = "{!! \Session::get('success') !!}";

    if (successMessage) {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: successMessage,
      }).then(function() {
        // Make an AJAX request to unset session variable
        $.ajax({
          url: "{{ route('unset-session', ['key' => 'success']) }}",
          type: 'POST',
          success: function(response) {
            console.log('Success session unset');
            // {{logger('Success session unset')}} -> call func logger in helper
          },
          error: function(error) {
            console.log('Error unsetting session', error);
          }
        });
      });
    }
  </script>
  @endif
</body>

</html>