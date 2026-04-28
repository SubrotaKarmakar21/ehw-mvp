jQuery(document).ready(function( $ ) {

	/* Data Of upcoming appointment Funcations START ---------------------------------------------------------- */
	var opts = {
		totalPages: $('#my_appointments_total_pages').val(),
		visiblePages: "3",
		onPageClick: function (event, page) {
			var page_val = $("#my_appointments_page_val").val(page);
			getPagingDataOfAppointments(page);
		}
	};

	if (opts['totalPages'] > 1) {
		$('#my_appointments_paging').twbsPagination(opts);
	}

	function getPagingDataOfAppointments(page) {
		var page = $("#my_appointments_page_val").val();
		addOverlay();
		$.ajax({
			type: "POST",
			url: ajaxUrl,
			data: {"action":'get_my_upcoming_appointment',"page":page},
			dataType: "json",
			success: function(data){
				$("#my_appointment_list_container").html(data.content);
				removeOverlay();
			}
		});
		return false;
	}

	$(document).off('click', '#search-keyword-upcoming');
	$(document).on('click', '#search-keyword-upcoming', function() {
		getUpcomingAppointmentList(1);
		if ($('#upcoming-keyword').val() != '') {
			$('.upcoming_clear_seach_container').removeClass('hidden');
		} else{
			$('.upcoming_clear_seach_container').addClass('hidden');
		}
	});

	$('#upcoming-search-date').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true,
		clearBtn: false,
		startDate: '0d'
	}).on('changeDate', function (e) {
		getUpcomingAppointmentList(1);
		$('.upcoming_clear_seach_container').removeClass('hidden');
	});

	$(document).on('click', '#removed_upcoming_search_data', function() {
		$('#upcoming-search-date').val('');
		$('#upcoming-keyword').val('');

		$('.upcoming_clear_seach_container').addClass('hidden');

		getUpcomingAppointmentList(1);
	});

	function getUpcomingAppointmentList(page_no = 1) {
		addOverlay();
		$.ajax({
			type: "POST",
			url: ajaxUrl,
			data: {
				"action" : 'get_my_upcoming_appointment',
				"page" : page_no,
				'keyword' : $('#upcoming-keyword').val(),
				'search_date' : $('#upcoming-search-date').val(),
			},
			dataType: "json",
			success: function(data){
				$("#my_appointment_list_container").html(data.content);
				$('#my_appointments_total_pages').val(data.pagination.total_pages);

				if (typeof data.pagination.total_pages != 'undefined' && data.pagination.total_pages > 1) {
					if ($('#my_appointments_paging').data('twbs-pagination')) {
						$('#my_appointments_paging').twbsPagination('destroy');
					}
					var opts = {
						totalPages: $('#my_appointments_total_pages').val(),
						visiblePages: 3,
						onPageClick: function (event, page) {
							var page_val = $("#my_appointments_page_val").val(page);
							getPagingDataOfAppointments(page);
						}
					};
					$('#my_appointments_paging').twbsPagination(opts);
				} else{
					$('#my_appointments_page_val').val(1);
					if ($('#my_appointments_paging').data('twbs-pagination')) {
						$('#my_appointments_paging').twbsPagination('destroy');
					}
				}

				removeOverlay();
			}
		});
	}
	/* Data Of upcoming appointment Funcations END ---------------------------------------------------------- */

	var opts1 = {
		totalPages: $('#my_past_appointments_total_pages').val(),
		visiblePages: "3",
		onPageClick: function (event, page) {
			var page_val = $("#my_past_appointments_page_val").val(page);
			getPagingDataOfPastAppointments(page);
		}
	};

	if (opts1['totalPages'] > 1) {
		$('#my_past_appointments_paging').twbsPagination(opts1);
	}

	function getPagingDataOfPastAppointments(page) {
		var page = $("#my_past_appointments_page_val").val();
		addOverlay();
		$.ajax({
			type: "POST",
			url: ajaxUrl,
			data: {"action":'get_my_past_appointment',"page":page},
			dataType: "json",
			success: function(data){
				$("#my_past_appointment_list_container").html(data.content);
				removeOverlay();
			}
		});
		return false;
	}

	$(document).off('click', '#search-keyword-past');
	$(document).on('click', '#search-keyword-past', function() {
		getPastAppointmentList(1);


		if ($('#past-keyword').val() != '') {
			$('.past_clear_seach_container').removeClass('hidden');
		} else{
			$('.past_clear_seach_container').addClass('hidden');
		}
	});

	$('#past-search-date').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true,
		clearBtn: false,
		endDate: '0d'
	}).on('changeDate', function (e) {
		getPastAppointmentList(1);
		$('.past_clear_seach_container').removeClass('hidden');
	});

	$(document).on('click', '#removed_past_search_data', function() {
		$('#past-search-date').val('');
		$('#past-keyword').val('');

		$('.past_clear_seach_container').addClass('hidden');

		getPastAppointmentList(1);
	});

	function getPastAppointmentList(page_no = 1) {
		addOverlay();
		$.ajax({
			type: "POST",
			url: ajaxUrl,
			data: {
				"action" : 'get_my_past_appointment',
				"page" : page_no,
				'keyword' : $('#past-keyword').val(),
				'search_date' : $('#past-search-date').val(),
			},
			dataType: "json",
			success: function(data){
				$("#my_past_appointment_list_container").html(data.content);
				$('#my_past_appointments_total_pages').val(data.pagination.total_pages);

				if (typeof data.pagination.total_pages != 'undefined' && data.pagination.total_pages > 1) {
					if ($('#my_past_appointments_paging').data('twbs-pagination')) {
						$('#my_past_appointments_paging').twbsPagination('destroy');
					}
					var opts = {
						totalPages: $('#my_past_appointments_total_pages').val(),
						visiblePages: 3,
						onPageClick: function (event, page) {
							var page_val = $("#my_past_appointments_page_val").val(page);
							getPagingDataOfPastAppointments(page);
						}
					};
					$('#my_past_appointments_paging').twbsPagination(opts);
				} else{
					$('#my_past_appointments_page_val').val(1);
					if ($('#my_past_appointments_paging').data('twbs-pagination')) {
						$('#my_past_appointments_paging').twbsPagination('destroy');
					}
				}

				removeOverlay();
			}
		});
	}

	// View History Button Click
	$(document).off('click', '.view-history-btn');
	$(document).on('click', '.view-history-btn', function() {

    		var appointmentId = $(this).attr('data-id');

    		if(appointmentId && appointmentId > 0){
			window.location.href = SITE_URL + "modules-nct/patient_details-nct/index.php?appointment_id=" + appointmentId;
    		}

	});

});
