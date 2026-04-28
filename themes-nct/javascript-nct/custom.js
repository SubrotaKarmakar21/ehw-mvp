/* START :: functions */

$.validator.addMethod("noSpace", function(value, element) { 
    return value.indexOf(" ") < 0 && value != ""; 
}, lang.NO_SPACE_ERROR);

$.validator.addMethod('userNameValid',function (value, element) { 
    return /^[a-zA-Z][a-zA-Z\ ]*$/.test(value); 
},'Page name is not valid. Only alphabets and -,_ are allowed');

$.validator.addMethod("alphanumericnew", function(value, element) {
    return /^[a-zA-Z][a-zA-Z]*$/.test(value);
}, "Letters, numbers, and underscores only please");

$.validator.addMethod("validemail", function(value, element) {
    return this.optional(element) || /^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/i.test(value); 
}, lang.MSG_ENTER_VALID_EMAIL_ADDRESS);

$.validator.addMethod('pageTitle', function (value, element) {
    return /^[a-zA-Z0-9][a-zA-Z0-9\-\ \_]*$/.test(value);
}, 'Value is not valid. Only alphanumeric and -,_ * space are allowed');

$.validator.addMethod('organizationValidate', function (value, element) {
    return /^[a-zA-Z0-9][a-zA-Z0-9\-\_\ \ \']*$/.test(value);
}, 'Value is not valid. Only alphanumeric and -,_ & space are allowed');

$.validator.addMethod('addressValidate', function (value, element) {
    return /^[a-zA-Z0-9][a-zA-Z0-9\-\.\,\_\#\ \']*$/.test(value);
}, 'Value is not valid. Only alphanumeric and -,_ & space are allowed');

$.validator.addMethod('postalCodeValidate', function (value, element) {
    return /^[0-9][0-9]*$/.test(value);
}, 'Value is not valid. Only numeric are allowed');

function initGoogleLocation() {
    // Get all elements with the class name 'location'
    var inputFields = document.getElementsByClassName('google_location');

    for (var i = 0; i < inputFields.length; i++) {
        (function(inputField) {
            // Set options for the Google Maps Autocomplete
            var options = {
                //componentRestrictions: { country: "US" },
                strictBounds: false,
            };

            var autocomplete = new google.maps.places.Autocomplete(inputField, options);

            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var place = autocomplete.getPlace();

                $('#latitude').val(place.geometry.location.lat());
                $('#longitude').val(place.geometry.location.lng());

                let address1 = "";
                let postcode = "";

                for (const component of place.address_components) {
                    const componentType = component.types[0];

                    switch (componentType) {
                    case "street_number":
                        address1 = `${component.long_name} ${address1}`;
                        break;
                    case "route":
                        address1 += component.short_name;
                        break;
                    case "postal_code":
                        postcode = `${component.long_name}${postcode}`;
                        break;
                    case "postal_code_suffix":
                        postcode = `${postcode}-${component.long_name}`;
                        break;
                    case "locality":
                        $("#city_name").val(component.long_name);
                        break;
                    case "administrative_area_level_1":
                        $("#state_name").val(component.long_name);
                        break;
                    case "country":
                        $("#country_name").val(component.long_name);
                        break;
                    }
                }

                $("#zip_code").val(postcode);
            });
        })(inputFields[i]);
    }
}

function initIntlPhoneInput() {
    var input = $("#country_number");
    var iti = input.intlTelInput({
        initialCountry: $('#phone_iso2_code').val() || phone_iso2_code,
        separateDialCode: true,
        allowDropdown: false /*true when need all flags*/
    });

    // var country_code = iti.intlTelInput("getSelectedCountryData").dialCode;
    // input.on("countrychange", function() {
    //     var tmp = $('#country_number').parent().find(".iti__selected-flag").attr("title");
    //     var tmp2 = tmp.split(" ");
    //     var count = tmp2.length;
    //     var tmp2 = tmp.split(" ");
    //     var count = tmp2.length;
    //     var contactCode = (tmp2[count - 1] != '' ? tmp2[count - 1] : phone_contact_code);
    //     var iso2_code = $('.iti__active').attr('data-country-code');
    //     $("#phone_country_code").val(contactCode);
    //     $("#phone_iso2_code").val(iso2_code);
    // });

    var initData = iti.intlTelInput("getSelectedCountryData");
    if ($('#phone_country_code').val() == '') {
        $('#phone_country_code').val("+" + initData.dialCode);
    }
    if ($('#phone_iso2_code').val() == '') {
        $('#phone_iso2_code').val(initData.iso2);
    }
}

function submitFormHandler(url, form_id, loading_text, suc_callback, error_callback, before_callback) {

    try {
        removeOverlay();
        $.ajax({
            url : url,
            beforeSend : function() {
                $("form#" + form_id).find('[type=submit]').prop('disabled', true);
                if ( typeof (eval(before_callback)) === 'function') {
                    window[(before_callback)]();
                }
            },
            method : 'post',
            dataType : 'json',
            data : jQuery("form#" + form_id).serialize(),
            success : function(data) {
                if (data.status) {
                    if ( typeof (eval(suc_callback)) === 'function') {
                        if ( typeof window[(suc_callback)] === 'function') {
                            window[(suc_callback)](data);
                        } else {
                            suc_callback(data);
                        }
                    }
                    if (data.msg != "undefined") {
                        toastr['success'](data.msg);
                    }
                    
                    //$("form#" + form_id).trigger("reset");

                } else {
                    if ( typeof (eval(error_callback)) === 'function') {
                        if ( typeof window[(error_callback)] === 'function') {
                            window[(error_callback)](data);
                        } else {
                            error_callback(data);
                        }
                    }
                    if (data.msg != "undefined") {
                        toastr['error'](data.msg);
                    }

                }
                $("form#" + form_id).find('[name=token]').val(data.newToken);
                removeOverlay();
            },
            complete : function(e) {
                $("form#" + form_id).find('[type=submit]').prop('disabled', false);
                removeOverlay();
            }
        });

    } catch(e) {

        console.log("Error in redirection - " + e);
    }
}

function submitValueHandler(url, data_string, loading_text, suc_callback, error_callback, async, before_callback) {

    if ( typeof (async) === "undefined") {
        async = true;
    }
    try {

        if ( typeof (req) != 'undefined') {
            req_val.abort();
        }
        addOverlay();
        req_val = $.ajax({

            url : url,
            beforeSend : function() {
                if ( typeof (eval(before_callback)) === 'function') {
                    window[(before_callback)]();
                }
            },
            method : 'post',
            dataType : 'json',
            data : data_string,
            async : async,
            success : function(data) {

                if (data.status) {
                    if ( typeof (eval(suc_callback)) === 'function') {
                        if ( typeof window[(suc_callback)] === 'function') {
                            window[(suc_callback)](data);
                        } else {
                            suc_callback(data);
                        }
                    }
                    if (data.msg != "undefined") {
                        toastr['success'](data.msg);
                    }

                } else {
                    if ( typeof (eval(error_callback)) === 'function') {
                        if ( typeof window[(error_callback)] === 'function') {
                            window[(error_callback)](data);
                        } else {
                            error_callback(data);
                        }
                    }
                    if (data.msg != "undefined") {
                        toastr['error'](data.msg);
                    }

                }
                removeOverlay();
                //$("form#" + form_id).find('[name=token]').val(data.newToken);

            }
        });
        return req_val;
    } catch(e) {
        console.log("Error in redirection - " + e);
    }
}

function submitFormHandlerWithUpload(url, form_id, loading_text, suc_callback, error_callback) {
    var formElement = document.getElementById(form_id);
    var formObj = jQuery("#" + form_id);
    var formURL = formObj.attr("action");

    var formData = new FormData(formElement);
    addOverlay();
    jQuery.ajax({
        url : url,
        type : 'post',
        dataType : 'json',
        data : formData,
        processData : false, // tell jQuery not to process the data
        contentType : false, // tell jQuery not to set contentType
        enctype : 'multipart/form-data',
        mimeType : 'multipart/form-data',
        cache : false,
        beforeSend : function() {
            $("form#" + form_id).find('[type=submit]').prop('disabled', true);
        },
        success : function(data, textStatus, jqXHR) {
            if (data.status) {
                if ( typeof (eval(suc_callback)) === 'function') {
                    if ( typeof window[(suc_callback)] === 'function') {
                        window[(suc_callback)](data);
                    } else {
                        suc_callback(data);
                    }
                }
                if (data.msg != "undefined") {
                    toastr['success'](data.msg);
                }

            } else {
                if ( typeof (eval(error_callback)) === 'function') {
                    if ( typeof window[(error_callback)] === 'function') {
                        window[(error_callback)](data);
                    } else {
                        error_callback(data);
                    }
                }
                if (data.msg != "undefined") {
                    toastr['error'](data.msg);
                }

            }
            $("form#" + form_id).find('[name=token]').val(data.newToken);
            removeOverlay();
        },
        complete : function(e) {
            $("form#" + form_id).find('[type=submit]').prop('disabled', false);
            removeOverlay();
        },
        error : function(jqXHR, textStatus, errorThrown) {
            removeOverlay();
        }
    });
    return false;

}
/* END :: functions */

/* START :: javascripts */
var isMobile = false;
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
    isMobile = true; 
}

/*
 * variable 'taCheckTypes' used for validating user description or message 
 * in post/edit/repost project as well in messages.
 * if required, can be declared or override locally in 'validateDescription' rule
 */
var taCheckTypes = {
    at : {
        regEx : /@|\[at\]|\(at\)|\{at\}|\-at\-|\+at\+|\[dot\]|\(dot\)|\{dot\}|\-dot\-|\+dot\+/,
        message : lang.illegal_use_of_communication_email+" "+siteNm
    },
    connect : {
        regEx : lang.illegal_use_of_communication_email_regex,
        message : lang.illegal_use_of_communication_email+" "+siteNm
    },
    payment : {
        regEx : lang.illegal_use_of_communication_payment_regex,
        message : lang.illegal_use_of_communication_payment
    },
    customOffer : {
        regEx : lang.illegal_use_of_communication_offer_regex,
        message : lang.illegal_use_of_communication_payment
    }
};


$(document).ready(function() {    

    /* ---------------------------------------------------------- */
    /*  WOW SMOOTH ANIMATIN
    /* ----------------------------------------------------------- */

    wow = new WOW({
        animateClass : 'animated',
        offset : 100
    });
    wow.init();
    
});

$(document).on('changed.bs.select', '[data-ele="userLanguage"]', function(e) {
    submitValueHandler(siteUrl, "action=method&method=updateUserLang&userLanguage="+$(this).val(), "please wait..",function(){
        window.location.reload();
    });    
});


$(document).keyup(function(e) {
    if (e.keyCode == 27) {
        $('.modal').modal('hide');
    }
});
