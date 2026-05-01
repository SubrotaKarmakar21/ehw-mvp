jQuery(document).ready(function( $ ) {

    initIntlPhoneInput();
    initGoogleLocation();

    if (sessUserType == 'clinic' && $('#doctor_id').val() <= 0) {
        $('.booking_date_container').addClass('hidden');
    } else{
        $('.booking_date_container').removeClass('hidden');
    }

    $(document).on("change",'#doctor_id',function(){

        if ($(this).val() > 0) {
            $('.booking_date_container').removeClass('hidden');
        } else{
            $('.booking_date_container').addClass('hidden');
        }
    });

    $('#date_of_birth').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        clearBtn: false,
        orientation: "bottom auto",
        endDate: '0d'
    });

    initBookingDatePicker();

    $.validator.addMethod('pagenm',function (value, element) {
        return /^[a-zA-Z][a-zA-Z]*$/.test(value);
    },'Page name is not valid. Only alphanumeric and -,_,+ are allowed');

    $('#add_appointment_form').validate({
        ignore: [],
        rules: {
            first_name: {
                required: true,
                fname_lname_validation : true,
                minlength: 1,
                maxlength: 20
            },
            last_name:{
                fname_lname_validation : true,
                minlength: 1,
                maxlength: 20
            },
            address:{
                required: true
            },
            phone_no: {
                required:true,
                number: true,
                minlength: 10,
                maxlength: 10
            },
            gender: {
                required: true
            },
            age: {
    		required: true,
    		number: true,
    		min: 0,
    		max: 120
	    },
            case_type: {
                required: true
            },
            booking_date: {
                required: true
            },
            slot_id:{
                required: true,
            },
            doctor_id:{
                required: true,
            }
        },
        messages: {
            first_name: {
                required: "Please enter first name."
            },
            phone_no:{
                required: "Please enter phone number"
            },
            address:{
                required: "Please enter address."
            },
            gender: {
                required: "Please select gender."
            },
            date_of_birth: {
                required: "Please select date of birth."
            },
            case_type: {
                required: "Please select case type."
            },
            booking_date: {
                required: "Please select booking date."
            },
            slot_id:{
                required: "Please select slot."
            },
            doctor_id:{
                required: "Please select doctor."
            }
        },

        errorPlacement: function (error, element) {
            let container = element.data('error-container');
            if (container) {
                $(container).html(error);
            } else {
                error.insertAfter(element);
            }
        },

        submitHandler: function (form) {

    		var formData = $(form).serialize();

    		$.ajax({
        		url: ajaxURL,
        		type: "POST",
        		data: formData,
        		dataType: "json",
        		success: function(response){

            		if(response.whatsapp_link){
                		window.open(response.whatsapp_link,'_blank');
            		}

            		window.location.href = "/dashboard";
        	}
    	});

    	return false;
}

    });

    /*$(document).on("submit",'#btnAddPatient',function(){
        if ($('#add_appointment_form').valid()) {
            $('#add_appointment_form').submit();
        } else{
            return false;
        }
    });*/

});

function initBookingDatePicker() {
    var $date = $('#booking_date');

    if (!$date.length) return;


    // destroy if already initialized
    if ($date.data('datepicker')) {
        $date.datepicker('destroy');
    }

    $date.datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        clearBtn: false,
        orientation: "bottom auto",
        startDate: '+0d'
    })
    .on('changeDate', function (e) {

        var selectedDate = e.format('yyyy-mm-dd');
        var doctor_id = $('#doctor_id').val();

        if (doctor_id <= 0 && sessUserType == 'clinic') {
            toastr['error']('Please select doctor for booking slot.');
            return true;
        }

        $.ajax({
            url: ajaxURL,
            type: 'POST',
            data: {
                action: 'get_time_slot',
                booking_date: selectedDate,
                doctor_id: sessUserType == 'doctor' ? sessUserId : doctor_id,
            },
            dataType: 'json',
            beforeSend: function () {
                addOverlay();
            },
            success: function (res) {

                if (res.html != '') {
                    $('#slots_container').html(res.html);

                    $('.d-block').removeClass('hidden');
                } else{
                    toastr['error']('Slot not available.');
                    $('.d-block').addClass('hidden');
                    $('#slots_container').html('');
                }
                removeOverlay();
            },
            error: function () {
                removeOverlay();
            }

        });
    });
}
