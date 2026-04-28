jQuery(document).ready(function( $ ) {

    $(".show-hide-pass").on('click', function(event) {
        event.preventDefault();
        if ($('.show_hide_password-login input').attr("type") == "text") {
            $('.show_hide_password-login input').attr('type', 'password');
            $('.show_hide_password-login i').addClass("fa-eye");
            $('.show_hide_password-login i').removeClass("icon-open-eye");
        } else if ($('.show_hide_password-login input').attr("type") == "password") {
            $('.show_hide_password-login input').attr('type', 'text');
            $('.show_hide_password-login i').removeClass("fa-eye");
            $('.show_hide_password-login i').addClass("icon-open-eye");
        }
    });

    $("#loginForm").validate({
        ignore: [],
        rules: {
            email_address: {
                required: true,
                email: true,
            },
            password: {
                required: true,
                minlength: 6,
                maxlength: 25,
                noSpace:true
            },
        },
        messages: {
            email_address: {
                required: lang.error_enter_email_address,
                email: lang.error_valid_email_address
            },
            password: {
                required: lang.error_enter_password,
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

    $(document).on("submit",'#submitLoginForm',function(){
        if ($('#loginForm').valid()) {
            $('#loginForm').submit();
        } else{
            return false;
        }
    });


    $("#forgetForm").validate({
        ignore: [],
        rules: {
            email: {
                required: true,
                email: true,
            },
        },
        messages: {
            email: {
                required: lang.error_enter_email_address,
                email: lang.error_valid_email_address
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
    $(document).on("submit",'#submitForgetForm',function(){
        if ($('#forgetForm').valid()) {
            $('#forgetForm').submit();
        } else{
            return false;
        }
    });

    $("#reactivateForm").validate({
        ignore: [],
        rules: {
            email: {
                required: true,
                email: true,
            },
        },
        messages: {
            email: {
                required: lang.error_enter_email_address,
                email: lang.error_valid_email_address
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

    $(document).on("submit",'#submitReactivateForm',function(){
        if ($('#reactivateForm').valid()) {
            $('#reactivateForm').submit();
        } else{
            return false;
        }
    });

    

});