<link href="{SITE_CSS}intlTelInput.css" rel="stylesheet" type="text/css"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.41&libraries=places&language=en&key={GOOGLE_MAPS_API_KEY}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<main class="flex-shrink-0 inner-main">

    <section class="profile-page gray-bg">
        <div class="container">
            <div class="common-white-box profile-box">
                <div class="form-group">
                    <div class="upload-block">
                        <div class="profile-photo">
                            <img src="%uploaded_image%" alt="%first_name%" class="profile-img upd_prop_img_list">
                            <div class="user-edit">
                                <div class="edit-btns">
                                    <input type="hidden" name="old_profile_photo" id="old_profile_photo" value="%old_profile_photo%">
                                    <label class="upload-btn">
                                        <input type="file" name="profile_photo" id="profile_photo" class="k-pointer-only"> <i class="fa-solid fa-pen"></i>
                                    </label>

                                    <a href="javascript:void(0);" class="delete-btn %show_when_image_uploaded%">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="editProfileForm" name="editProfileForm" method="POST" enctype="multipart/form-data">
		    <div class="form-group">

			<label class="form-label"><b>Invoice Header Banner</b></label>

			<p style="color:#777;font-size:13px;">
				Upload a wide banner containing your clinic logo and clinic name.
				Avoid photos or signboard images.
			</p>

			<div style="border:1px dashed #ccc;padding:15px;border-radius:6px;background:#fafafa;">

    				<input type="file" name="clinic_banner" id="clinic_banner" class="form-control">
				<input type="hidden" name="which_types" value="clinic_banner">
			</div>

			<p style="margin-top:8px;font-size:12px;color:#888;">
				Recommended size: <b>1200 × 200 px</b>
			</p>

			<div class="banner-preview">
				%clinic_banner_preview%
			</div>

		    </div>
                    <div class="form-group %user_name_container%">
                        <label for="first_name" class="form-label">{MEND_SIGN}First Name: &nbsp;</label>
                        <input type="text" class="form-control logintextbox-bg" name="first_name" id="first_name" value="%first_name%" />
                    </div>

                    <div class="form-group %user_name_container%">
                        <label for="last_name" class="form-label">Last Name: &nbsp;</label>
                        <input type="text" class="form-control logintextbox-bg" name="last_name" id="last_name" value="%last_name%" />
                    </div>

                    <div class="form-group %clinic_name_container%">
                        <label for="clinic_name" class="form-label">{MEND_SIGN}Clinic Name: &nbsp;</label>
                        <input type="text" class="form-control logintextbox-bg" name="clinic_name" id="clinic_name" value="%clinic_name%" />
                    </div>

		    <div class="form-group %clinic_name_container%">
    			<label for="gstin" class="form-label">GSTIN: &nbsp;</label>
    			<input type="text" class="form-control logintextbox-bg" name="gstin" id="gstin" value="%GSTIN%" placeholder="Enter GSTIN (optional)" maxlength="15" style="text-transform:uppercase;"/>
		    </div>

                    <div class="form-group">
                        <label for="country_number"  class="form-label">{MEND_SIGN}{label_phone_number}</label>
                        <div class="country-field">

                            <input type="text" name="phone_no" id="country_number" placeholder="{placholder_phone_number}" class="form-control" value="%phone_no%">
                            <input type="hidden" name="phone_country_code" id="phone_country_code" value="%phone_country_code%">
                            <input type="hidden" name="phone_iso2_code" id="phone_iso2_code" value="%phone_iso2_code%">
                        </div>
                    </div>

		    <div class="form-group">
    			<label for="whatsapp_number" class="form-label">WhatsApp Number</label>

    			<div class="country-field">

        			<input type="text" name="whatsapp_number" id="whatsapp_number" placeholder="Enter WhatsApp number" class="form-control" value="%whatsapp_number%">

        			<input type="hidden" name="whatsapp_country_code" id="whatsapp_country_code">
        			<input type="hidden" name="whatsapp_iso2_code" id="whatsapp_iso2_code">

    			</div>

    			<button type="button" id="verify_whatsapp_btn" class="btn btn-success" style="margin-top:8px;">
        			Verify
    			</button>

			<div id="whatsapp_otp_box" style="display:none;margin-top:10px;">

				<input type="text" id="whatsapp_otp" class="form-control" placeholder="Enter 6 digit OTP" maxlength="6" style="width:200px;display:inline-block;">

				<button type="button" id="verify_otp_btn" class="btn btn-success">

					Submit

				</button>

			</div>

		    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">{MEND_SIGN}Address: &nbsp;</label>
                        <input type="text" class="form-control logintextbox-bg required google_location" name="address" id="address" value="%address%" />

                        <input type="hidden" name="latitude" id="latitude" value="%latitude%">
                        <input type="hidden" name="longitude" id="longitude" value="%longitude%">
                        <input type="hidden" name="city_name" id="city_name" value="%city_name%">
                        <input type="hidden" name="state_name" id="state_name" value="%state_name%">
                        <input type="hidden" name="country_name" id="country_name" value="%country_name%">
                        <input type="hidden" name="zip_code" id="zip_code" value="%zip_code%">
                    </div>

		    <div class="form-group">
    			<label for="clinic_description" class="form-label">
        			Clinic Description:
    			</label>
    			<textarea class="form-control logintextbox-bg" name="clinic_description" id="clinic_description" rows="4" placeholder="Write a short description about your clinic">%clinic_description%</textarea>
		    </div>

                    <div class="form-group">
                        <label for="type_of_doctor_id" class="form-label">{MEND_SIGN}Types of doctors: &nbsp;</label>
                        <select class="form-control multy-select logintextbox-bg required" name="type_of_doctor_id[]" id="type_of_doctor_id" title="Select Type of Doctors" multiple>
                            %get_type_of_doctors_list%
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="specialties_id" class="form-label">{MEND_SIGN}Specialties: &nbsp;</label>
                        <select class="form-control logintextbox-bg required multy-select" name="specialties_id[]" id="specialties_id" title="Select Specialties" multiple>
                            %get_specialties_list%
                        </select>
                    </div>

                    %other_profile_field_info%

                    <div class="hours-card %user_name_container%">
                        <div class="form-group">
                            <label class="form-label">Available Days and Time Slots</label>
                            %days_slot_html%
                        </div>
                    </div>

                    <div class="form-group text-center cf">
                        <input type="hidden" name="action" value="method">
                        <input type="hidden" name="method" value="submitEditProfileForm">
                        <button type="button" class="light-orange-btn lg-btn" name="btmEditProfile" id="btmEditProfile">Save Changes</button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</section>

<script>
var USER_ID = "%user_id%";
var SITE_URL = "{SITE_URL}";
</script>

<script>
$(document).on('change','#clinic_banner',function(){

    var file = this.files[0];

    if(!file) return;

    // STEP 1: show instant preview
    var reader = new FileReader();

    reader.onload = function(e){

        var preview = '<img src="'+e.target.result+'" style="width:100%;max-width:900px;height:200px;object-fit:contain;background:#fff;border:1px solid #ddd;border-radius:6px;padding:5px;">';

        $('.banner-preview').html(preview);

    };

    reader.readAsDataURL(file);


    // STEP 2: upload file to server
    var form_data = new FormData();
    form_data.append('avatar_file', file);
    form_data.append('which_types', 'clinic_banner');
    form_data.append('id', btoa(USER_ID));

    $.ajax({
        url: SITE_URL + "includes-nct/crop-nct.php",
        type: "POST",
        data: form_data,
        contentType: false,
        processData: false,
        success: function(response){
            console.log(response);
        }
    });

});
</script>

<script>

$(document).ready(function(){

    var whatsappInput = document.querySelector("#whatsapp_number");

    var itiWhatsapp = window.intlTelInput(whatsappInput, {
        initialCountry: "in",
        separateDialCode: true,
        preferredCountries: ["in","us","gb"],
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    });

    whatsappInput.addEventListener("countrychange", function(){

        var countryData = itiWhatsapp.getSelectedCountryData();

        $("#whatsapp_country_code").val(countryData.dialCode);
        $("#whatsapp_iso2_code").val(countryData.iso2);

    });

    // allow digits only
    $('#whatsapp_number').on('input', function(){
        this.value = this.value.replace(/[^0-9]/g,'');
    });

    // max 10 digits
    $('#whatsapp_number').attr('maxlength','10');

});

</script>

<script>
$(document).ready(function(){

    $('#verify_whatsapp_btn').click(function(){

        var number = $('#whatsapp_number').val();

        if(number.length != 10){
            alert("Please enter valid WhatsApp number");
            return;
        }

        $.ajax({

            url: SITE_URL + "modules-nct/edit_profile-nct/ajax.whatsapp-nct.php",
            type:"POST",

            data:{
                action:"send_otp",
                whatsapp_number:number
            },

            success:function(res){

                console.log(res);

		res = res.trim();
                if(res === "OTP_SENT"){
                    $('#whatsapp_otp_box').show();
                }else{
                    alert(res);
                }

            }

        });

    });

});
</script>

<script>
$('#verify_otp_btn').click(function(){

	var otp = $('#whatsapp_otp').val();

	$.ajax({

		url: SITE_URL+"modules-nct/edit_profile-nct/ajax.whatsapp-nct.php",

		type:"POST",

		data:{
			action:"verify_otp",
			otp:otp
		},

		success:function(res){

			if(res=="VERIFIED"){

				alert("WhatsApp connected successfully");

				location.reload();

			}else{

				alert("Invalid OTP");

			}

		}

	});

});
</script>

<script>
$('#gstin').on('input', function(){

    let value = $(this).val().toUpperCase();

    // Allow only A-Z and 0-9
    value = value.replace(/[^A-Z0-9]/g, '');

    // Limit to 15 characters
    value = value.substring(0, 15);

    $(this).val(value);

});
</script>

<script>
$(document).on('submit', '#editProfileForm', function(e){

    let gstin = $('#gstin').val().trim();

    if(gstin !== ''){

        gstin = gstin.toUpperCase();
        $('#gstin').val(gstin);

        let gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{1}Z[A-Z0-9]{1}$/;

        if(!gstRegex.test(gstin)){
            alert("Invalid GSTIN format");

            e.preventDefault();
            return false;
        }
    }

});
</script>
</main>
