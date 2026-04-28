jQuery(document).ready(function( $ ) {

    $(document).off('change', '.day-toggle');
    $(document).off('click', '.add-slot');
    $(document).off('click', '.remove-slot');
    $(document).off('click', '#btn_crop_submit');

    $(document).on('click', '.remove-profile-image', function (e) {
        var id = $(this).attr('data-id');
        if (id <= 0) {
            $('.upd_prop_img_list').attr('src',default_profile_image);
            $('.remove-profile-image').addClass('hidden');
            $('#new_upd_img_normal').val('');
        } else{
            $.confirm({
                title: '',
                content: "Are you sure you want to remove image.",
                confirmButton: "Yes",
                cancelButton: "No",
                confirmButtonClass: 'btn-primary',
                cancelButtonClass: 'btn-danger',
                confirm: function(){
                    addOverlay();
                    $.ajax({
                        url: ajaxUrl,
                        data:{
                            action: 'remove_profile_picture',
                            id: id,
                        },
                        type: "POST",
                        dataType: "json",
                        success: function (response) {
                            $('.upd_prop_img_list').attr('src',response.profile_img);

                            $('.remove-profile-image').addClass('hidden');
                            removeOverlay();
                        }
                    });
                },
                cancel: function(){}

            });
        }

    });

    initIntlPhoneInput();
    initGoogleLocation();

    $(document).on('change', '.day-toggle', function () {
        var day = $(this).data('day');
        var row = $('.' + day + '-slot');

        if ($(this).is(':checked')) {
            row.find('.'+day).addClass('hidden');
            row.find('input[type="text"]').val('');

            row.find('input[type="text"]').removeClass('required');

            $("input[name^='oh["+day+"][from_time]']").each(function () {
                if ($(this).data('rules-added')) {
                    $(this).rules('remove', 'required');
                    $(this).removeData('rules-added');
                    $(this).val('');
                }
            });

            $("input[name^='oh["+day+"][to_time]']").each(function () {
                if ($(this).data('rules-added')) {
                    $(this).rules('remove', 'required');
                    $(this).removeData('rules-added');
                    $(this).val('');
                }
            });
        } else {
            row.find('.'+day).removeClass('hidden');

            row.find('input[type="text"]').addClass('required');

            setTimeout(function () {
                $("input[name^='oh["+day+"][from_time]']").each(function () {
                    if (!$(this).data('rules-added')) {
                        $(this).rules('add', {
                            required: true
                        });
                        $(this).data('rules-added', true);
                    }
                });
            },500);
        }
    });

    $('#practicing_since').datepicker({
        format: "mm-yyyy",
        startView: "months",
        minViewMode: "months",
        autoclose: true,
        clearBtn: false,
        orientation: "bottom auto",
        endDate: new Date()
    });


    $.validator.addMethod('pagenm',function (value, element) {
        return /^[a-zA-Z][a-zA-Z]*$/.test(value);
    },'Page name is not valid. Only alphanumeric and -,_,+ are allowed');

    $("#editProfileForm").validate({
        ignore: [],
        rules: {
            first_name: {
                required: true,
                fname_lname_validation : true,
                minlength: 1,
                maxlength: 20
            },
            phone_no: {
                required:true,
                number: true,
                minlength:10,
                maxlength:10
            },
            last_name:{
                fname_lname_validation : true,
                minlength: 1,
                maxlength: 20
            },
            gender: {
                required: true
            },
            email_address: {
                required: true,
                email: true,
                remote: {
                    url: siteUrl+'ajax-registration-nct/',
                    data: {
                        action: 'chk_email'
                    },
                    async: false,
                    type: "POST"
                },
            },
            address:{
                required: true
            },
            'type_of_doctor_id[]':{
                required: true
            },
            'specialties_id[]':{
                required: true
            },
	    practicing_since:{
	    },
            consultation_fees:{
                required: true
            }
        },
        messages: {
            first_name: {
                required: "Please enter first name.",
            },
            phone_no:{
                required: "Please enter phone number"
            },
            gender: {
                required: "Please select gender."
            },
            email_address: {
                required: lang.error_enter_email_address,
                remote:lang.error_email_address_already_exist,
                email: lang.error_valid_email
            },
            address:{
                required: "Please enter address."
            },
            'type_of_doctor_id[]':{
                required: "Please select type of professional.",
            },
            'specialties_id[]':{
                required: "Please select specialties.",
            },
            practicing_since:{
            },
            consultation_fees:{
                required: "Please select consultation fees."
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

    $(document).on("click",'#btmEditProfile',function(){
        if ($('#editProfileForm').valid()) {
            $('#editProfileForm').submit();
        } else{
            return false;
        }
    });

    $(document).on('click', '.add-slot', function () {
        var day = $(this).data('type');
        var index = $('.' + day + '-slot').length + 1;

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action : 'add_slot',
                day: day,
                index: index,
            },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                $('.' + day + '-slot:last').after(response.html);

            // re-init timepicker for new inputs
                initFromTimepicker(day + '_from_' + response.index);
                initToTimepicker(day + '_to_' + response.index);


                setTimeout(function () {
                    $("input[name^='oh["+day+"][from_time]']").each(function () {
                        if (!$(this).data('rules-added')) {
                            $(this).rules('add', {
                                required: true
                            });
                            $(this).data('rules-added', true);
                        }
                    });
                },500);

            }
        });
    });

    $(document).on('click', '.remove-slot', function () {
        var day = $(this).attr('data-type');
        var id = $(this).attr('data-id');

        if (typeof id != 'undefined' && id > 0) {
            $.confirm({
                title: '',
                content: "Are you sure you want to remove this slot.",
                confirmButton: "Yes",
                cancelButton: "No",
                confirmButtonClass: 'btn-primary',
                cancelButtonClass: 'btn-danger',
                confirm: function(){
                    addOverlay();
                    $.ajax({
                        url: ajaxUrl,
                        data:{
                            action: 'remove_slot',
                            id: id,
                        },
                        type: "POST",
                        dataType: "json",
                        success: function (response) {

                            if (response.status) {
                                toastr['success'](response.message);
                                $('.slot_container_'+id).remove();
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
            $(this).closest('.'+day+'-slot').remove();
        }

    });

    // $('.from-time').each(function () {
    //         // get id attribute
    //     var inputId = $(this).attr('id');

    //     if (inputId != '') {
    //         initFromTimepicker(inputId);
    //     }
    // });

    // $('.to-time').each(function () {
    //         // get id attribute
    //     var inputId = $(this).attr('id');

    //     if (inputId != '') {
    //         initToTimepicker(inputId);
    //     }
    // });

    // function initFromTimepicker(inputId) {
    //     $('#' + inputId).timepicker({
    //         interval: 60,
    //         step: 60,
    //         dynamic: false,
    //         dropdown: true,
    //         scrollbar: true,
    //         template: 'modal',
    //         change: function (time) {

    //             var currentId = $(this).attr('id');

    //             let toId = currentId.replace("_from_", "_to_");

    //             if ($('#' + toId).length) {
    //                 $('#' + toId).timepicker('destroy').val('');

    //                 $('#' + toId).timepicker({
    //                     interval: 60,
    //                     minTime: $(this).val(),
    //                     startTime: $(this).val(),
    //                     dynamic: false,
    //                     dropdown: true,
    //                     scrollbar: true,
    //                 });
    //             } else {
    //                 console.warn("Target to-time not found for:", currentId);
    //             }
    //         }
    //     });
    // }

    // function initToTimepicker(inputId ) {
    //     $('#'+inputId).timepicker({
    //         interval: 60,
    //         dynamic: false,
    //         minTime: getCurrentTime(new Date($("#default_date").val())),
    //         dropdown: true,
    //         scrollbar: true,
    //         template: 'modal'
    //     });
    // }

    function addOneHour(timeStr) {
        let d = new Date("1970-01-01 " + timeStr);
        d.setMinutes(d.getMinutes() + 30);

        return d.toLocaleString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    $('.from-time').each(function () {
        let inputId = $(this).attr('id');
        if (inputId) initFromTimepicker(inputId);
    });

    $('.to-time').each(function () {
        let inputId = $(this).attr('id');
        if (inputId) initToTimepicker(inputId);
    });

    /* ---------------- FROM TIMEPICKER ---------------- */

    function timeToMinutes(timeStr) {
        let [time, mer] = timeStr.split(' ');
        let [h, m] = time.split(':').map(Number);

        if (mer === 'PM' && h !== 12) h += 12;
        if (mer === 'AM' && h === 12) h = 0;

        return h * 60 + m;
    }

    function validateSlot(currentFromId) {

        let currentToId = currentFromId.replace('_from_', '_to_');

        let fromVal = $('#' + currentFromId).val();
        let toVal   = $('#' + currentToId).val();

        if (!fromVal || !toVal) return true;

        let fMin = timeToMinutes(fromVal);
        let tMin = timeToMinutes(toVal);

        if (fMin === tMin) {
            alert('Start time and end time cannot be same');
            $('#' + currentToId).val('');
            return false;
        }

        if (tMin < fMin) {
            alert('End time must be greater than start time');
            $('#' + currentToId).val('');
            return false;
        }

        let isValid = true;

        let commonWord = currentFromId.split('_from_')[0];

        $('.'+commonWord+'-slot').each(function() {
            let otherFrom = $(this).find('.from-time').val();

            let otherTo   = $(this).find('.to-time').val();

            let otherFromId = $(this).find('.from-time').attr('id');
            if (!otherFrom || !otherTo) return;
            if (otherFromId === currentFromId) return;

            let ofMin = timeToMinutes(otherFrom);
            let otMin = timeToMinutes(otherTo);

            if (fMin < otMin && tMin > ofMin) {
                isValid = false;
                return false;
            }
        });

        if (!isValid) {
            alert('This time slot overlaps with an existing slot');
            $('#' + currentToId).val('');
            return false;
        }

        return true;
    }

    function initFromTimepicker(inputId) {

        $('#' + inputId).timepicker({
            interval: 30,
            step: 30,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            template: 'modal',

            change: function () {
                let fromId = $(this).attr('id');
                let toId = fromId.replace('_from_', '_to_');

                let fromTime = $(this).val();
                if (!fromTime) return;

                let minToTime = addHalfHour(fromTime);

                $('#' + toId).timepicker('destroy').val('');

                $('#' + toId).timepicker({
                    interval: 30,
                    minTime: minToTime,
                    startTime: minToTime,
                    dynamic: false,
                    dropdown: true,
                    scrollbar: true,
                    change: function () {
                        validateSlot(fromId);
                    }
                });
            }
        });
    }

    function initToTimepicker(inputId) {

        $('#' + inputId).timepicker({
            interval: 30,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            template: 'modal',
            minTime: getCurrentTime(new Date($("#default_date").val())),
            change: function () {
                let toId = $(this).attr('id');
                let fromId = toId.replace('_to_', '_from_');

                validateSlot(fromId);
            }
        });
    }

    function getCurrentTime(date) {
        var hours = date.getHours(),
        minutes = date.getMinutes(),
        ampm = hours >= 12 ? 'pm' : 'am';

        hours = hours % 12;
        hours = hours ? hours : 12;
        minutes = minutes < 10 ? '0'+minutes : minutes;

        return hours + ':' + minutes + ' ' + ampm;
    }
    /*Add more with time slot*/

    var $image = $('#srcPhotoProfile');
    $('#profile_photo').on('change', function() {
        var validExtensions = ['jpg', 'png', 'jpeg', 'webp'];
        var fileName = this.files[0].name;
        var fileSize = this.files[0].size/(1024*1024);
        var fileNameExt = fileName.split('.').pop().toLowerCase();

        if ($.inArray(fileNameExt, validExtensions) === -1) {
            toastr.error('Please select only png, jpg, jpeg, webp file.');
            $('#profile_photo').val('');
        } else if(fileSize > parseFloat(10)){
            toastr["error"]('Max filesize allowed 10 MB.');
            $('#profile_photo').val('');
        } else {

            var reader = new FileReader();
            reader.onload = function(e) {

                $('#srcPhotoProfile').attr('src', e.target.result);
                $('#btn_crop_submit').prop('disabled', true);
                $('#close_crop_modal').prop('disabled', true);
                $('#crop_profile_cover_modal').modal({
                    backdrop: 'static'
                });
                $('#crop_profile_cover_modal').modal('show');
                $('#which_types').val('profile_photo_from_admin');

                $('#crop_profile_cover_modal').on('shown.bs.modal', function() {
                    $image.cropper({
                        aspectRatio: 100 / 100,
                        viewMode: 1,
                        movable: true,
                        cropBoxMovable: true,
                        cropBoxResizable: false,
                        zoomable: true,
                        dragCrop: false,
                        scalable: true,
                        rotatable: true,
                        autoCropArea: 1,
                        minCropBoxWidth: 100,
                        minCropBoxHeight: 100,
                        crop: function(e) {
                            var json = ['{"x":' + Math.round(e.x), '"y":' + Math.round(e.y), '"height":' + Math.round(e.height), '"width":' + Math.round(e.width), '"scaleX":' + Math.round(e.scaleX), '"scaleY":' + Math.round(e.scaleY), '"rotate":' + e.rotate + '}'].join();
                            $('.avatar-data').val(json);
                        }
                    });
                    var cropper = $image.data('cropper');
                    $('#btn_crop_submit').prop('disabled', false);
                    $('#close_crop_modal').prop('disabled', false);
                });

            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    $(document).on('click', '#btn_crop_submit', function(e) {
        e.preventDefault();

        $('#btn_crop_submit').prop('disabled', true);
        $('#close_crop_modal').prop('disabled', true);

        var formnew = $('#avtar_form')[0];
        var formData = new FormData(formnew);

        formData.append('id', btoa(sessUserId));
        formData.append('avatar_file', $('#profile_photo')[0].files[0]);

        $.ajax({
            url: siteUrl+"crop",
            type: "post",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            enctype: 'multipart/form-data',
            mimeType: 'multipart/form-data',
            cache: false,
            beforeSend: function() {
                addOverlay();
            },
            success: function(res) {
                if(res.status){

                    $('#profile_photo').val('');
                    $('.upd_prop_img_list').attr('src',res.file_path);
                    $('.remove-profile-image').removeClass('hidden');
                    $('#new_upd_img_normal').val(res.image_name);

                    $('#btn_crop_submit').prop('disabled', true);
                    $('#close_crop_modal').prop('disabled', true);
                    $('#crop_profile_cover_modal').modal('hide');
                    $image.cropper('destroy');

                    // toastr['success']('Profile Image successfully updated.');
                } else {
                    toastr['error'](res.message);
                }
                removeOverlay();
            },
            error: function() {
                // toastr['error'](res.message);
                removeOverlay();
            }
        });
    });

    $(document).on('click', '[data-method="rotate"]', function(e) {
        e.preventDefault();
        $image.data('cropper').rotate($(this).data('option'));
    });
    $(document).on('click', '[data-method="zoom"]', function(e) {
        e.preventDefault();
        $image.data('cropper').zoom($(this).data('option'));
    });
    $(document).on('click', '[data-method="scaleX"]', function(e) {
        e.preventDefault();
        $image.data('cropper').scaleX($(this).data('option'));
        if($(this).data('option') == "-1"){
            $(this).data("option","1");
        } else {
            $(this).data("option","-1");
        }
    });
    $(document).on('click', '[data-method="scaleY"]', function(e) {
        e.preventDefault();
        $image.data('cropper').scaleY($(this).data('option'));
        if($(this).data('option') == "-1"){
            $(this).data("option","1");
        } else {
            $(this).data("option","-1");
        }
    });
    $(document).on('click', '[data-method="reset"]', function(e) {
        e.preventDefault();
        $('[data-method="scaleX"]').data("option","-1");
        $('[data-method="scaleY"]').data("option","-1");
        $image.data('cropper').reset();
    });
    $(document).on('click', '#close_crop_modal', function(e) {
        e.preventDefault();
        $image.cropper('destroy');
        $('#crop_profile_cover_modal').modal('hide');
        $('#profile_photo').val('');
    });

    $("#type_of_doctor_id").select2({
        placeholder: "Select Type of Doctors",
        allowClear: true,
        minimumResultsForSearch: 0
    });

    $("#specialties_id").select2({
        placeholder: "Select Specialties",
        allowClear: true,
        minimumResultsForSearch: 0
    });

    

    

});
