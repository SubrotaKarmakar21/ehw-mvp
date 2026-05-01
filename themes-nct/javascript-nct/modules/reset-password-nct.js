jQuery(document).ready(function( $ ) {

    
    $("#password_reset_form").validate({
        rules: {            
            new_password: {
                required: true,
                minlength: 6,
                maxlength: 25,
            },
            confirm_new_password: {
                required: true,
                equalTo: "#new_password"
            }
        },
        messages: {
            new_password: {
                required: lang.error_enter_password,
            },
            confirm_new_password: {
                required: lang.error_enter_password_confirmation,
                equalTo: lang.error_password_cofirm_password_not_match
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("data-error-container")) {
                error.appendTo(element.attr("data-error-container"));
            } else {
                error.insertAfter(element);
            }
        }
    });

    $(document).on("submit",'#reset_password',function(){
        if ($('#password_reset_form').valid()) {
            $('#password_reset_form').submit();
        } else{
            return false;
        }
    });

});