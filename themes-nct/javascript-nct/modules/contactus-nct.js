jQuery(document).ready(function( $ ) {

   initIntlPhoneInput();

    $.validator.addMethod('pagenm',function (value, element) { 
        return /^[a-zA-Z][a-zA-Z]*$/.test(value); 
    },'Page name is not valid. Only alphanumeric and -,_,+ are allowed');

    $("#contactForm").validate({
        ignore: [],
        rules: {
            first_name: { 
                required: true, 
                minlength: 3,
                fname_lname_required: true
            },
            last_name: { 
                required: true, 
                minlength: 3,
                fname_lname_required: true
            },
            email_address: {
                required: true,
                email: true,
            },
            phone_no: {
                required: true,
                number: true,
                minlength:10,
                maxlength:10
            },
            message:{
                required: true,
            },
        },
        messages: {
            first_name: {
                required: "Please enter first name.",
            },
            last_name: {
                required: "Please enter last name.",
            },
            email_address: {
                required: lang.error_enter_email_address,
                email: lang.error_valid_email_address
            },
            phone_no : {
                required: lang.error_enter_phone_number,
            },
            message: {
                required: "Please enter message.",
            },
        },
        errorPlacement: function (error, element) {
            if (element.attr("data-error-container")) {
                error.appendTo(element.attr("data-error-container"));
            } else {
                error.insertAfter(element);
            }
        }
    });

    $(document).on("submit",'#btmContactUs',function(){
        if ($('#contactForm').valid()) {
            $('#contactForm').submit();
        } else{
            return false;
        }
    });
});