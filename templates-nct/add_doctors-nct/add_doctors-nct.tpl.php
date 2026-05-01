<link href="{SITE_CSS}intlTelInput.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.41&libraries=places&language=en&key={GOOGLE_MAPS_API_KEY}"></script>

<main class="flex-shrink-0 inner-main">
    <section class="profile-page gray-bg">
        <div class="container">
            <div class="common-white-box profile-box">
                <form id="editProfileForm" name="editProfileForm" method="POST">
                    <div class="form-group">
                        <div class="upload-block">
                            <div class="profile-photo">
                                <img src="%uploaded_image%" alt="%first_name%" class="profile-img upd_prop_img_list">
                                <div class="user-edit">
                                    <div class="edit-btns">
                                        <label class="upload-btn">
                                            <input type="file" name="profile_photo" id="profile_photo" class="k-pointer-only"> <i class="fa-solid fa-pen"></i>
                                        </label>
                                        <input type="hidden" name="new_upd_img_normal[]" id="new_upd_img_normal" value=""/>

                                        <a href="javascript:void(0);" class="delete-btn remove-profile-image %show_when_image_uploaded%" data-id="%id%">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="first_name" class="form-label">{MEND_SIGN}First Name: &nbsp;</label>
                        <input type="text" class="form-control logintextbox-bg required" name="first_name" id="first_name" value="%first_name%" placeholder="Enter First Name" value="%first_name%" />
                    </div>

                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name: &nbsp;</label>
                        <input type="text" class="form-control logintextbox-bg required" name="last_name" id="last_name" value="%last_name%" placeholder="Enter Last Name" />
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlInput1" class="form-label">{MEND_SIGN}Gender</label>
                        <div class="radio-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="gender_male" value="male" data-error-container="#error_gender" %gender_male%>
                                <label class="form-check-label" for="gender_male">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="gender_female" value="female" data-error-container="#error_gender" %gender_female%>
                                <label class="form-check-label" for="gender_female">Female</label>
                            </div>
			    <div class="form-check form-check-inline">
    				<input class="form-check-input" type="radio" name="gender" id="gender_other" value="other" data-error-container="#error_gender" %gender_other%>
    				<label class="form-check-label" for="gender_other">Other</label>
			    </div>
                        </div>
                        <div id="error_gender"></div>
                    </div>

                    <div class="form-group">
                        <label for="email_address" class="is-label-txt">{MEND_SIGN}{label_email_address}</label>
                        <input type="text" name="email_address" id="email_address" class="form-control" placeholder="{placeholder_email_address}" value="%email%" %disabled%>
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
                        <label for="address" class="form-label">{MEND_SIGN}Address: &nbsp;</label>
                        <input type="text" class="form-control logintextbox-bg required google_location" name="address" id="address" value="%address%" placeholder="Enter Address" />

                        <input type="hidden" name="latitude" id="latitude" value="%latitude%">
                        <input type="hidden" name="longitude" id="longitude" value="%longitude%">
                        <input type="hidden" name="city_name" id="city_name" value="%city_name%">
                        <input type="hidden" name="state_name" id="state_name" value="%state_name%">
                        <input type="hidden" name="country_name" id="country_name" value="%country_name%">
                        <input type="hidden" name="zip_code" id="zip_code" value="%zip_code%">
                    </div>

                    <div class="form-group">
                        <label for="type_of_doctor_id" class="form-label">{MEND_SIGN}Type of Professional: &nbsp;</label>
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

                    <div class="form-group">
                        <label for="practicing_since" class="form-label">Practicing since: &nbsp;</label>
                        <input type="text" class="form-control logintextbox-bg" name="practicing_since" id="practicing_since" value="%practicing_since%" readonly placeholder="mm-yyyy" />
                    </div>

                    <div class="form-group">
                        <label for="consultation_fees" class="form-label">{MEND_SIGN}Consultation Fees: &nbsp;</label>

                        <div class="input-group">

                            <span class="input-group-text" id="basic-addon1">₹</span>

                            <input type="text" name="consultation_fees" id="consultation_fees" placeholder="Enter Consultation Fees" data-error-container="#error_consultation_fees" class="form-control" value="%consultation_fees%" min="1">
                        </div>
                        <div id="error_consultation_fees"></div>

                    </div>

		    <div class="form-group">
  			<label>Doctor Description / Experience:</label>
  			<textarea name="doctor_description" class="form-control" rows="5" placeholder="Write about experience, qualifications...">%doctor_description%</textarea>
		    </div>

                    <div class="hours-card">
                        <div class="form-group">
                            <label class="form-label">Available Days and Time Slots</label>
                            %days_slot_html%
                        </div>
                    </div>

                    <div class="form-group text-center cf">
                        <input type="hidden" name="id" id="id" value="%id%">
                        <input type="hidden" name="action" value="method">
                        <input type="hidden" name="method" value="addEditDoctorsFrom">
                        <button type="button" class="light-orange-btn lg-btn" name="btmEditProfile" id="btmEditProfile">Add Doctor</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
