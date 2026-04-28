<link href="{SITE_CSS}intlTelInput.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.41&libraries=places&language=en&key={GOOGLE_MAPS_API_KEY}"></script>

<main class="flex-shrink-0 inner-main">

    <section class="profile-page gray-bg">
        <div class="container">
            <div class="common-white-box profile-box">
                <form name="add_patient_form" id="add_patient_form" method="POST">

                    <input type="hidden" name="doctor_clinic_id" id="doctor_clinic_id" value="%doctor_clinic_id%">
                    <div class="form-group doctor_info_container ">
                        <label for="first_name" class="form-label">{MEND_SIGN}First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter First Name" value="%first_name%">
                    </div>

                    <div class="form-group doctor_info_container ">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter Last Name" value="%last_name%">
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
    			<label class="form-label">{MEND_SIGN}Age</label>

    			<div style="display:flex; gap:10px;">

        			<input type="number" class="form-control" name="age" id="age" placeholder="Enter Age" min="0" style="flex:1;" value="%age%">

        			<select name="age_type" id="age_type" class="form-control" style="width:120px;">
            				<option value="years" %age_type_years%>Years</option>
            				<option value="months" %age_type_months%>Months</option>
            				<option value="days" %age_type_days%>Days</option>
       	 			</select>

    			</div>
		    </div>

                    <div class="form-group">
                        <label for="country_number" class="form-label">{MEND_SIGN}{label_phone_number}</label>
                        <div class="country-field">

			    <input type="text" name="phone_no" id="country_number" placeholder="{placholder_phone_number}" class="form-control" value="%phone_no%" %readonly%>
                            <input type="hidden" name="phone_country_code" id="phone_country_code" value="+91">
                            <input type="hidden" name="phone_iso2_code" id="phone_iso2_code" value="in">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">{MEND_SIGN}Address: &nbsp;</label>
                        <input type="text" class="form-control logintextbox-bg required google_location" name="address" id="address" placeholder="Enter Address" value="%address%" />

                        <input type="hidden" name="latitude" id="latitude" value="%latitude%">
                        <input type="hidden" name="longitude" id="longitude" value="%longitude%">
                        <input type="hidden" name="city_name" id="city_name" value="%city_name%">
                        <input type="hidden" name="state_name" id="state_name" value="%state_name%">
                        <input type="hidden" name="country_name" id="country_name" value="%country_name%">
                        <input type="hidden" name="zip_code" id="zip_code" value="%zip_code%">
                    </div>

                    <!-- <div class="form-group">
                        <label for="exampleFormControlInput1" class="form-label">{MEND_SIGN}Case Type</label>
                        <div class="radio-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="case_type" id="case_type_new" value="new" data-error-container="#error_case_type">
                                <label class="form-check-label" for="case_type_new">New Case</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="case_type" id="case_type_follow_up" value="follow_up" data-error-container="#error_case_type">
                                <label class="form-check-label" for="case_type_follow_up">Follow up</label>
                            </div>
                        </div>
                        <div id="error_case_type"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">{MEND_SIGN}Select Date</label>
                        <input type="text" class="form-control" name="booking_date" id="booking_date" placeholder="Select Booking Date" value="" readonly />
                    </div>

                    <div class="d-block form-group hidden">
                        <div class="select-time-slot">
                        <div class="row row-cols-1 row-cols-md-2 g-2" id="slots_container">

                        </div>
                        </div>
                        <div id="error_slot_id"></div>
                    </div> -->

                    <!-- <div class="form-group">
                        <label class="form-label">Remarks</label>
                        <textarea rows="3" class="form-control" name="remarks" id="remarks" placeholder="Enter remarks"></textarea>
                    </div> -->

                    <div class="form-group text-center cf" mb-0>
                        <input type="hidden" name="id" id="id" value="%id%">
                        <input type="hidden" name="action" value="method">
                        <input type="hidden" name="method" value="submitAddPatientForm">
                        <button type="submit" class="btn lg-btn" name="btnAddPatient" id="btnAddPatient">%button_text%</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

</main>

<script>

document.addEventListener("DOMContentLoaded", function(){

    var form = document.getElementById("add_patient_form");

    form.addEventListener("submit", function(e){

        e.preventDefault();

        var formData = new FormData(form);

        fetch(window.location.href,{
            method:"POST",
            body:formData
        })
        .then(res => res.json())
        .then(response => {

            if(response.status){

    		if(response.whatsapp_link){
        		window.open(response.whatsapp_link,"_blank");
    		}

    		window.location.href="/my-patients";

	    }else{

    		alert(response.message);

	    }

        });

    });

});

</script>
