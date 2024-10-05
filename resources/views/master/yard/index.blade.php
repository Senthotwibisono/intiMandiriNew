@extends('partial.main')

@section('custom_styles')
<style>
    .grid-container {
        display: grid;
        grid-template-columns: repeat(4, 250px); /* 5 kolom dengan ukuran 100px */
        gap: 0px; /* Jarak antar kotak */
        scale: 1;
    }
    .grid-item {
        width: 250px;
        height: 150px;
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
    color: #000; /* Warna teks */
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
        gap: 0px; /* Jarak antar kotak */
        scale: 1;
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
        background-color: red;
        color: white;
    }

    .bg-green {
        background-color: green;
        color: white;
    }

    .bg-yellow {
        background-color: yellow;
        /* color: white; */
    }

    .bg-light-gray {
        background-color: #f2f2f2; /* Light gray */
    }
</style>
@endsection

@section('content')
<section>
    <div id="zoom-container" class="d-flex justify-content-center align-items-center mt-0" style="height: 80vh;">
        <div class="grid-container" style="border: 1px solid #ccc;"> 
            @foreach($yard as $item)
                @php
                    $bgColorClass = '';
                    if($item->yard_block != null || $item->yard_block != '') {
                        $bgColorClass = 'bg-yellow';
                    }

                @endphp
                <button type="button" class="btn btn-outline-success grid-item formEdit {{ $bgColorClass }}" data-id="{{$item->id}}">
                    <span class="yard-block-text">{{ $item->yard_block }}</span>
                    @if($item->yard_block != null)
                        @php
                            $gridTemplateColumns = 'repeat(' . $item->max_slot . ', 1fr)';

                            // Calculate item size
                            $itemWidth = 250 / $item->max_slot;
                            $itemHeight = 150 / $item->max_row; // Assume 4 tiers as in your original design
                        @endphp
                        <div class="detil-grid-container" style="grid-template-columns: {{ $gridTemplateColumns }};">
                            @for ($i = 1; $i <= $item->max_slot * $item->max_row; $i++) {{-- Adjust for total grid items --}}
                                <div class="detil-grid-item" data-id="{{ $i }}" style="width: {{ $itemWidth }}px; height: {{ $itemHeight }}px;"></div>
                            @endfor
                        </div>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
    <form id="selection-form" action=" " method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="selected_grids" id="selected-grids">
    </form>
</section>

<div class="modal fade" id="editCust" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Yard Detail</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <i data-feather="x"></i></button>
            </div>
            <form action="{{ route('master.yard.update')}}" method="POST" id="updateForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Yard Block</label>
                        <input type="text" class="form-control" name="yard_block" id="yard_block_edit">
                        <input type="hidden" class="form-control" name="id" id="id_edit">
                    </div>
                    <div class="form-group">
                        <label for="">Max Slot</label>
                        <input type="text" class="form-control" name="max_slot" id="max_slot_edit">
                    </div>
                    <div class="form-group">
                        <label for="">Max Row</label>
                        <input type="text" class="form-control" name="max_row" id="max_row_edit">
                    </div>
                    <div class="form-group">
                        <label for="">Max Tier</label>
                        <input type="text" class="form-control" name="max_tier" id="max_tier_edit">
                    </div>
                    <br>
                    <div class="button-container">
                        <button class="btn btn-danger resetButton">Reset This Yard</button>
                        <a href="" class="btn btn-info detilYard" id="detilYardLink">View Detil</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal"> <i class="bx bx-x d-block d-sm-none"></i> <span class="d-none d-sm-block">Close</span> </button>
                    <button type="button" id="updateButton" class="btn btn-primary ml-1" data-bs-dismiss="modal"> <i class="bx bx-check d-block d-sm-none"></i> <span class="d-none d-sm-block">Submit</span> </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('custom_js')
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
    document.addEventListener('DOMContentLoaded', function () {
        // Attach event listener to the update button
        document.getElementById('updateButton').addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to update this record?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form programmatically if confirmed
                    document.getElementById('updateForm').submit();
                }
            });
        });
    });
</script>

<script>
   
    $(document).ready(function() {});

    $(document).on('click', '.resetButton', function(e) {
        e.preventDefault();
        var data = {
            'id': $('#id_edit').val(),
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        Swal.fire({
            title: 'Are you Sure?',
            text: "Yard will destroy",
            icon: 'warning',
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Confirm',
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {

                $.ajax({
                    type: 'POST',
                    url: '/master/yard-reset',
                    data: data,
                    cache: false,
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                            if (response.success) {
                              Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                              })
                              .then(() => {
            // Memuat ulang halaman setelah berhasil menyimpan data
            window.location.reload();
        }).then(() => {
            // Buka modal "success" setelah halaman dimuat ulang
            
        });
                            } else {
                              Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                              });
                            }

                    },
                    error: function(response) {
                        var errors = response.responseJSON.errors;
                        if (errors) {
                            var errorMessage = '';
                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: errorMessage,
                            });
                        } else {
                            console.log('error:', response);
                        }
                    },
                });

            } else if (result.isDenied) {
                Swal.fire('Changes are not saved', '', 'info')
            }


        })

    });
</script>
@endsection