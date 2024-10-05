@extends('partial.main')
@section('custom_styles')
<style>
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, 180px); /* Adjust the number of columns dynamically */
        gap: 0px; /* No gap between boxes */
        scale: 1;
    }
    .grid-item {
        width: 180px;
        height: 125px;
        background-color: #f2f2f2;
        border: 1px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .bg-yellow {
        background-color: yellow;
        /* color: white; */
    }
</style>
@endsection
@section('content')

<div class="container py-2">
    <div class="row mt-0">
        <div class="form-group">
            <label for="">Yard Slot</label>
            <input type="hidden" id="yard_id" value="{{$yard->id}}">
            <select name="slot" id="slot" style="width:100%;" class="js-example-basic-single form-select select2">
                <option disabeled selected>Pilih Satu!!</option>
                @foreach($slots as $slot)
                <option value="{{$slot}}">{{$slot}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<section>
    <div class="card align-items-center mt-0" style="overflow-y: auto; overflow-x: auto;">
        <div id="grid-container" class="grid-container">
           
        </div>
    </div>
</section>
@endsection
@section('custom_js')
<script>
$(document).ready(function() {
    $('#slot').on('change', function() {
        let selectedSlot = $(this).val();
        let yardId = $('#yard_id').val(); // Get the yard_id

        // Clear previous grid items
        $('#grid-container').empty();

        if (selectedSlot && yardId) {
            $.ajax({
                url: '/master/yard-rowTierView', // Update with your route
                type: 'GET',
                data: { slot: selectedSlot, yard_id: yardId },
                success: function(response) {
                    // Find the maximum tier and row values
                    let maxTier = Math.max(...response.map(item => item.tier));
                    let maxRow = Math.max(...response.map(item => item.row));

                    // Dynamically set the grid container's dimensions
                    let gridItemWidth = 180; // Width of a single grid item
                    let gridItemHeight = 125; // Height of a single grid item
                    let containerWidth = maxRow * gridItemWidth; // Total width of the grid container
                    let containerHeight = maxTier * gridItemHeight; // Total height of the grid container

                    $('#grid-container').css({
                        'grid-template-columns': `repeat(${maxRow}, ${gridItemWidth}px)`,
                        'width': containerWidth + 'px',
                        'height': containerHeight + 'px'
                    });

                    // Generate grid items in the desired order
                    for (let row = 1; row <= maxRow; row++) {
                        let newRow = $('<div class="grid-row"></div>'); // Create a new row

                        for (let tier = maxTier; tier >= 1; tier--) {
                            // Find the corresponding row-tier item
                            let rowTier = response.find(item => item.row === row && item.tier === tier);
                            if (rowTier) {
                                let containerInfo = rowTier.cont ? `Container: ${rowTier.cont.nocontainer}` : '';
                                let gridItemClass = rowTier.cont ? 'bg-yellow' : ''; // Apply the yellow background if cont_id exists
                                let gridItem = `<div class="grid-item text-center ${gridItemClass}">${containerInfo ? containerInfo : `Row: ${row}, Tier: ${tier}`}</div>`;
                                newRow.append(gridItem); // Append grid item to the new row
                            } else {
                                let gridItem = `<div class="grid-item">Row: ${row}, Tier: ${tier}</div>`;
                                newRow.append(gridItem); // Append grid item to the new row
                            }
                        }

                        $('#grid-container').append(newRow); // Append the new row to the grid container
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    });
});
</script>
@endsection