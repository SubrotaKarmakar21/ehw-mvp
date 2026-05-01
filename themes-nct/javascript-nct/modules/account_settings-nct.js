jQuery(document).ready(function( $ ) {
	$('#frm_changepassword').validate({
		rules: {
			oldPassword: {
				required: true,
				minlength: 6,
                maxlength: 25,
				remote: {
					url: ajaxUrl,
					type: 'POST',
					data: {
						oldpwd: function() {
							return $("#oldPassword").val();
						}
					}
				}
			},
			newPassword: {
				required: true,
				minlength: 6,
                maxlength: 25,
			},
			confirmNewPassword: {
				required: true,
				minlength: 6,
                maxlength: 25,
				equalTo: "#newPassword"
			}
		},
		messages: {
			oldPassword: {
				required: lang.error_enter_password,
				remote: lang.MSG_INCORRECT_PASS
			},
			newPassword: {
				required: lang.toastr_please_enter_new_password,
			},
			confirmNewPassword: {
				required: lang.error_enter_password_confirmation,
				equalTo: lang.error_new_and_confirm_password_are_not_same
			}
		}
	});

    //button click form submit
	$(document).on("click", "#changePasswordBtn", function() {

		if ($("#frm_changepassword").valid()) {
			var formData = $("#frm_changepassword").serialize();
			$.ajax({

				url: ajaxUrl,
				type: "POST",
				data: formData,
				dataType: "json",
				async: false,
				beforeSend: function() {
					addOverlay();
					$("#changePasswordBtn").val("Changing....");
					$("#changePasswordBtn").attr('disabled', 'disabled');
				},
				success: function(data) {
					if (data.type == "success") {
						toastr[data.type](data.message);
					} else {
						toastr[data.type](data.message);
					}
					$("#changePasswordBtn").val("Change Password");
					$("#changePasswordBtn").removeAttr('disabled');
					$("#frm_changepassword")[0].reset();
					removeOverlay();
				}
			});
		}

	});

	$(document).on("click", "#deactive_account", function () {
        $.confirm({
            title: '',
            content: lang.ALERT_ARE_YOU_SURE_TO_DEACTIVE_THIS_ACCOUNT,
            confirmButton: "Yes",
            cancelButton: "No",
            confirmButtonClass: 'btn-primary',
            cancelButtonClass: 'btn-danger',
            confirm: function(){
                $.ajax({
                    type: "post",
                    data: {
                        action: 'deactive_account',
                        user_id: sessUserId,
                    },
                    url: ajaxUrl,
                    dataType: 'json',
                    beforeSend: function() {
                        addOverlay();
                    },
                    complete: function() {
                        removeOverlay();
                    },
                    success: function(data) {
                        if (data.status) {
                            // toastr['success'](data.message);
                            setTimeout(function(){
                                window.location = siteUrl;
                            },500);
                        } else {
                            toastr['error'](data.message);
                        }
                    }
                });
            },
            cancel: function(){}
        });
    });

    $(document).on("click", "#delete_account", function () {
        $.confirm({
            title: '',
            content: lang.ALERT_ARE_YOU_SURE_TO_REMOVE_THIS_ACCOUNT,
            confirmButton: "Yes",
            cancelButton: "No",
            confirmButtonClass: 'btn-primary',
            cancelButtonClass: 'btn-danger',
            confirm: function(){
                addOverlay();
                $.ajax({
                    type: "post",
                    data: {
                        action: 'delete_account',
                        user_id: sessUserId,
                    },
                    url: ajaxUrl,
                    dataType: 'json',
                    beforeSend: function() {
                        addOverlay();
                    },
                    complete: function() {
                        removeOverlay();
                    },
                    success: function(data) {
                        if (data.status) {
                            // toastr['success'](data.message);
                            setTimeout(function(){
                                window.location = SITE_URL;
                            },500);
                        } else {
                            toastr['error'](data.message);
                        }

                        removeOverlay();
                    }
                });
            },
            cancel: function(){}
        });
    });
});