var con_key = '';
var url_hist = '';
var url_job = '';
var jquery_datatable2;
var jquery_datatable3;

$(function() {
	url_hist = $('#tblhist').data('url');
	url_job = $('#tbljob').data('url');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
	let jquery_datatable = $("#tblcont").DataTable({
	        fixedHeader: true,
	        paging: false,
	        ordering: false,
	        info: false,
	    });
//	data = ;
//	console.log(data);
	jquery_datatable2 = $("#tblhist").DataTable({
	        fixedHeader: true,
	        paging: false,
	        ordering: false,
	        info: false,
	        searching:false,
       		ajax:{
        		type: 'POST',
				url: url_hist,
//        		data: {"id":  con_key},
				data: function (d) {
            		d.id = con_key;
		        },
        		dataSrc: "data",
			} ,
			columns: [
	            { data: 'operation_name', name: 'operation_name' },
	            { data: 'ctr_status', name: 'ctr_status' },
	            { data: 'ctr_intern_status', name: 'ctr_intern_status' },
	            { data: 'yard_blok', name: 'yard_block' },
	            { data: 'yard_slot', name: 'yard_slot' },
	            { data: 'yard_row', name: 'yard_row' },
	            { data: 'yard_tier', name: 'yard_tier' },
	            { data: 'oper_name', name: 'oper_name' },
	            { data: 'update_time', name: 'update_time' },
	        ],
//	        order: [[0, 'asc']]
 	    });
	jquery_datatable3 = $("#tbljob").DataTable({
	        fixedHeader: true,
	        paging: false,
	        ordering: false,
	        info: false,
	        searching:false,
       		ajax:{
        		type: 'POST',
				url: url_job,
//        		data: {"id":  con_key},
				data: function (d) {
            		d.id = con_key;
		        },
        		dataSrc: "data",
			} ,
			columns: [
	            { data: 'ctr_status', name: 'ctr_status' },
	            { data: 'ctps_yn', name: 'ctps_yn' },
	            { data: 'commodity_code', name: 'commodity_code' },
	            { data: 'commodity_name', name: 'commodity_name' },
	            { data: 'stack_date', name: 'stack_date' },
	        ],
//	        order: [[0, 'asc']]
	    });
});

jQuery(document).ready(function(){

	jQuery(document).on('click','.cont_no',function(){
    	var current = jQuery(this);
     	var url = current.data('url');
     	var param = current.data('param');
        const urlParams = new URLSearchParams(param);
        const params = Object.fromEntries(urlParams);
//		console.log(url);
//		console.log(params);
       
        cont_disp(url, params); 
        
        con_key = params.id;
//        console.log(con_key);
        jquery_datatable2.ajax.reload();
        jquery_datatable3.ajax.reload();
//        jquery_datatable2.draw();
        return false;
/*
     	$.ajax({
        	url: url,
            dataType:'json',
            /* beforeSend:function() {
                $('.stocks_list').html('Loading...');
            }
        })
        .done(function(data) {
            $('.stocks_list').html('<ul>');
        });
*/
	});

});
/*
$(document).ready(function(){
   $(".cont_no").on('click', function(){
       var urlString = $(this).attr('href').split("?")[1];
       const urlParams = new URLSearchParams(urlString);
       const params = Object.fromEntries(urlParams);
//        console.log('err',params);
       
       cont_disp(params); 
       return false;
   });
});
*/
function cont_disp(url, params){
    $.ajax({
//        url : '{{ route('reports.hist.get_cont') }}',
//        url : 'http://10.7.28.129:8000/reports/hist/get_cont',
		url : url,
        timeout:30000,
        type: "POST",
        data: params,
		dataType:'json',
        success: function(response){
//         	console.log('error:', response.cont_no);
            $('#lv_ves').html(response.ves_id);
			$('#lv_cont').html(response.cont_no);
            $('#lw_cont').html(response.cont_disp);
            $('#lw_cont').show();
        },
        error: function(data) {
            console.log('error:', data);  
        },
    });
}
$(function() {
    $(document).ready(function() {
    	$('#block_no').on('change', function() {
            let block_no = $(this).val();
            let slot_no  = $('#slot_no').val();
            $.ajax({
                type: 'POST',
//                url: '{{ route('yards.rowtier.get_rowtier') }}',
                data: { 
                   	block_no : block_no,
                   	slot_no  : slot_no 
                },
                success: function(response) {
                    $('.page-body').html(response);
                    $('.page-body').show();
                },
                error: function(data) {
                    console.log('error:', data);
                },
            });
        });
	});
});