jQuery(document).ready(function( $ ) {

    initIntlPhoneInput();
    initGoogleLocation();

    $('#date_of_birth').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        clearBtn: false,
        orientation: "bottom auto",
        endDate: '0d'
    });

    // initBookingDatePicker();

    $.validator.addMethod('pagenm',function (value, element) {
        return /^[a-zA-Z][a-zA-Z]*$/.test(value);
    },'Page name is not valid. Only alphanumeric and -,_,+ are allowed');

    $('#add_patient_form').validate({
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
                minlength:10,
                maxlength:10
            },
            gender: {
                required: true
            },
            date_of_birth: {
                required: true
            },
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
            addPatientSubmit();
        }
    });

    $(document).on("submit",'#btnAddPatient',function(){
        if ($('#add_patient_form').valid()) {
            $('#add_patient_form').submit();
        } else{
            return false;
        }
    });

});
