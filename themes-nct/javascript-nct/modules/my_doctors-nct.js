jQuery(document).ready(function( $ ) {

	/* Data Of upcoming appointment Funcations START ---------------------------------------------------------- */
	initializePagination($('#my_item_total_pages').val(),$('#my_item_page_val').val(),'y');

	/* Data Of upcoming appointment Funcations END ---------------------------------------------------------- */

	click_events();
});

function initializePagination(totalPages, currentPage,is_first_load = 'n') {
	if ($('#my_paging').data("twbs-pagination")) {
		$('#my_paging').twbsPagination('destroy');
	}

	currentPage = parseInt(currentPage) || 1;
	totalPages = parseInt(totalPages) || 0;

	if (totalPages > 1) {
		$('#my_item_paging').twbsPagination({
			startPage: currentPage,
			totalPages: totalPages,
			visiblePages: 10,
			first: '<<',
			prev: '<',
			next: '>',
			last: '>>',
			initiateStartPageClick: false,
			onPageClick: function (event, page) {
                // avoid unnecessary reload if same page
				if (parseInt($('#my_item_page_val').val()) === page) return;

				$('#my_item_page_val').val(page);
				getPagingDataOfMyItems(page);
			}
		});
	} else {
		$('#my_item_paging').empty();
	}
}

function getPagingDataOfMyItems(page) {
	addOverlay();
	$.ajax({
		type: "POST",
		url: ajaxUrl,
		data: {"action":'get_my_items',"page":page,"clinic_id" : clinic_id },
		dataType: "json",
		success: function(data){
			$("#my_items_list_container").html(data.content);

			if (typeof data.pagination.total_pages != 'undefined') {
				initializePagination(data.pagination.total_pages,page);
			}

			click_events();
			removeOverlay();
		}
	}); 
	return false;
}

function click_events() {
	
	$(document).off('click', '.open_view_info_modal');
	$(document).on('click', '.open_view_info_modal', function() {
		var id = $(this).attr('data-id');

		addOverlay();
		$.ajax({
			type: "POST",
			url: ajaxUrl,
			data: {"action":'get_doctor_details',"id" : id },
			dataType: "json",
			success: function(data){
				$("#user_info_container").html(data.html);
				$("#view_info_modal").modal('show');
				removeOverlay();
			}
		}); 
	});

	$(document).off('click', '.delete-doctor');
	$(document).on('click', '.delete-doctor', function (e) {
		var id = $(this).attr('data-id');
		if (id != '') {
			$.confirm({
				title: '',
				content: "Are you sure you want to delete this record.",
				confirmButton: "Yes",
				cancelButton: "No",
				confirmButtonClass: 'btn-primary',
				cancelButtonClass: 'btn-danger',
				confirm: function(){
					addOverlay();
					$.ajax({
						url: ajaxUrl,
						data:{
							action: 'delete_doctor',
							id: id,
						},
						type: "POST",
						dataType: "json",
						success: function (response) {
							getPagingDataOfMyItems(1);

							if (response.status) {
								toastr['success'](response.message);
							} else{
								toastr['error'](response.message);
							}
							removeOverlay();
						}
					});
				},
				cancel: function(){}

			});
		} else{
			toastr['error']('something went wrong!');
		}

	});

}
