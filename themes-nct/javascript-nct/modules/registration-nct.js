jQuery(document).ready(function( $ ) {

    initIntlPhoneInput();

    toggleUserTypeFields();
    $('input[name="user_type"]').on('change', function () {
        toggleUserTypeFields();
    });
    
    $("#signUp").validate({
        ignore: [],
        rules: {
            user_type: {required : true},
            first_name: {
                required: function () {
                    if ($('input[name="user_type"]:checked').val() == 'doctor') {
                        console.log('fname_required',1);
                        return true;
                    } else{
                        return false;
                    }
                },
                minlength: 3,
                fname_lname_validation:{
                    required: function () {
                        if ($('input[name="user_type"]:checked').val() == 'doctor') {
                            return true;
                        } else{
                            return false;
                        }
                    }
                }
            },
            last_name: {
                required: function () {
                    if ($('input[name="user_type"]:checked').val() == 'doctor') {
                        console.log('lname_required',1);
                        return true;
                    } else{
                        return false;
                    }
                },
                minlength: 3,
                fname_lname_validation: {
                    required: function () {
                        if ($('input[name="user_type"]:checked').val() == 'doctor') {
                            return true;
                        } else{
                            return false;
                        }
                    }
                }
            },
            clinic_name: {
                required: function () {
                    if ($('input[name="user_type"]:checked').val() == 'clinic') {
                        return true;
                    } else{
                        return false;
                    }
                },
                minlength: 3,
                maxlength: 50,
                clinic_name_validation: {
                    required: function () {
                        if ($('input[name="user_type"]:checked').val() == 'doctor') {
                            return true;
                        } else{
                            return false;
                        }
                    }
                }
            },
            phone_no: {
                required: true,
                number: true,
                minlength:10,
                maxlength:10
            },
            email_address: {
                required: true,
                email: true,
                remote: {
                    url: ajaxUrl,
                    data: {
                        action: 'chk_email'
                    },
                    async: false,
                    type: "POST"
                },
            },
            cpassword: {
                required: true,
                minlength: 6,
                maxlength: 25,
            },
            password: {
                required: true,
                equalTo: '#cpassword',
                minlength: 6,
                maxlength: 25,
            },
            hiddenRecaptcha: {
                required: function () {
                    if (grecaptcha.getResponse() == '') {
                        return true;
                    } else {
                        return false;
                    }
                }
            },
            txtTerms: {
                required: true
            },
            referral_or_community_code: {
                maxlength: 10
            },
            
        },
        messages: {
            user_type: {
                required: "Please select user type."
            },
            first_name: {
                required: "Please enter first name.",
            },
            last_name: {
                required: "Please enter last name.",
            },
            clinic_name: {
                required: "Please enter clinic name.",
                fname_lname_required : "Please use alphabets, numbers. The first name must contain at least one alphabet letter.",
            },
            phone_no : {
                required: "Please enter phone number.",
            },
            email_address: {
                required: lang.error_enter_email_address,
                remote:lang.error_email_address_already_exist,
                email: lang.error_valid_email
            },
            cpassword: {
                required: "Please enter confirm password.",
            },
            password: {
                required: lang.error_enter_password,
                equalTo: lang.error_password_cofirm_password_not_match
            },
            hiddenRecaptcha:{
                required: lang.error_prove_you_are_not_robot
            },
            txtTerms: {
                required: "Please agree with terms and conditions"
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

    $(document).on("submit",'#submitSignup',function(){
        if ($('#signUp').valid()) {
            $('#signUp').submit();
        } else{
            return false;
        }
    });
});

function toggleUserTypeFields() {
    var userType = $('input[name="user_type"]:checked').val();
    userType = typeof userType != 'undefined' ? userType : '';

    if (userType === 'doctor') {
        $('.doctor_info_container').removeClass('hidden');
        $('.clinic_name_container').addClass('hidden');

        $('#clinic_name').val('');
    } else if (userType === 'clinic') {
        $('.doctor_info_container').addClass('hidden');
        $('.clinic_name_container').removeClass('hidden');

        $('#first_name, #last_name').val('');
    } else {
        $('.doctor_info_container').addClass('hidden');
        $('.clinic_name_container').addClass('hidden');

        $('#first_name, #last_name, #clinic_name').val('');
    }
}