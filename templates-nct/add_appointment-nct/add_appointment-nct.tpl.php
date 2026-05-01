<link href="{SITE_CSS}intlTelInput.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.41&libraries=places&language=en&key={GOOGLE_MAPS_API_KEY}"></script>

<main class="flex-shrink-0 inner-main">

    <section class="profile-page gray-bg">
        <div class="container">
            <div class="common-white-box profile-box">
                <h1 class="form-title">Add an Appointment</h1>
                <form name="add_appointment_form" id="add_appointment_form" method="POST">

                    <div class="form-group doctor_info_container" style="position:relative;">
    			<label class="form-label">{MEND_SIGN}First Name</label>

    			<input type="text"name="first_name" id="patient_search" class="form-control" placeholder="Search Patient">

    			<input type="hidden" id="patient_id">

    			<div id="patient_results"
         			class="list-group"
         			style="position:absolute; top:100%; left:0; width:100%; z-index:9999;">
    			</div>
		    </div>

                    <div class="form-group doctor_info_container ">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter Last Name">
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlInput1" class="form-label">{MEND_SIGN}Gender</label>
                        <div class="radio-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="gender_male" value="male" data-error-container="#error_gender">
                                <label class="form-check-label" for="gender_male">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="gender_female" value="female" data-error-container="#error_gender">
                                <label class="form-check-label" for="gender_female">Female</label>
                            </div>
			    <div class="form-check form-check-inline">
    				<input class="form-check-input" type="radio" name="gender" id="gender_other" value="other" data-error-container="#error_gender">
    				<label class="form-check-label" for="gender_other">Other</label>
			    </div>
                        </div>
                        <div id="error_gender"></div>
                    </div>

                    <div class="form-group">
    			<label class="form-label">{MEND_SIGN}Age</label>

    			<div style="display:flex; gap:10px;">
        			<input type="number" class="form-control" name="age" id="age" placeholder="Enter Age" min="0">

        			<select name="age_type" id="age_type" class="form-control" style="max-width:120px;">
            				<option value="years">Years</option>
            				<option value="months">Months</option>
            				<option value="days">Days</option>
        			</select>
    			</div>
		</div>

                    <div class="form-group">
                        <label for="country_number" class="form-label">{MEND_SIGN}{label_phone_number}</label>
                        <div class="country-field">

                            <input type="text" name="phone_no" id="country_number" placeholder="{placholder_phone_number}" class="form-control" value="">
                            <input type="hidden" name="phone_country_code" id="phone_country_code" value="+91">
                            <input type="hidden" name="phone_iso2_code" id="phone_iso2_code" value="in">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">{MEND_SIGN}Address: &nbsp;</label>
                        <input type="text" class="form-control logintextbox-bg required google_location" name="address" id="address" value="" placeholder="Enter Address" />

                        <input type="hidden" name="latitude" id="latitude" value="">
                        <input type="hidden" name="longitude" id="longitude" value="">
                        <input type="hidden" name="city_name" id="city_name" value="">
                        <input type="hidden" name="state_name" id="state_name" value="">
                        <input type="hidden" name="country_name" id="country_name" value="">
                        <input type="hidden" name="zip_code" id="zip_code" value="">
                    </div>

                    <div class="form-group">
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

                    %select_doctor_option_when_clinic%

                    <div class="form-group booking_date_container">
                        <label class="form-label">{MEND_SIGN}Select Date</label>
                        <input type="text" class="form-control" name="booking_date" id="booking_date" placeholder="Select Booking Date" value="" readonly />
                    </div>

                    <div class="d-block form-group hidden">
                        <div class="select-time-slot">
                        <div class="row row-cols-1 row-cols-md-2 g-2" id="slots_container">

                        </div>
                        </div>
                        <div id="error_slot_id"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Remarks</label>
                        <textarea rows="3" class="form-control" name="remarks" id="remarks" placeholder="Enter remarks"></textarea>
                    </div>

                    <div class="form-group text-center cf" mb-0>
                        <input type="hidden" name="action" value="method">
                        <input type="hidden" name="method" value="submitAppointmentForm">
                        <button type="submit" class="btn lg-btn" name="btnAddPatient" id="btnAddPatient">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

<script>

document.addEventListener("DOMContentLoaded",function(){

    const searchInput = document.getElementById("patient_search");

    searchInput.addEventListener("keyup",function(){

        let keyword = this.value;

        if(keyword.length < 2){
            document.getElementById("patient_results").innerHTML="";
	    lockPatientFields(false);
            return;
        }

        fetch("modules-nct/add_appointment-nct/ajax.add_appointment-nct.php",{
            method:"POST",
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:"action=searchPatient&keyword="+keyword
        })
        .then(res=>res.json())
        .then(data=>{

            let html="";

            data.forEach(p=>{

                html += `
                <a href="#" class="list-group-item patient-item"
                    data-id="${p.id}"
                    data-first="${p.first_name}"
                    data-last="${p.last_name}"
                    data-phone="${p.phone_no}"
                    data-gender="${p.gender}"
                    data-dob="${p.date_of_birth}"
                    data-address="${p.address}">

                    ${p.first_name} ${p.last_name} (${p.phone_no})
                </a>`;
            });

            document.getElementById("patient_results").innerHTML = html;

        });

    });

});
</script>

<script>
document.addEventListener("click",function(e){

    if(e.target.classList.contains("patient-item")){

        e.preventDefault();

        let p = e.target.dataset;

        document.getElementById("patient_search").value = p.first;
        document.getElementById("last_name").value = p.last;
        document.getElementById("country_number").value = p.phone;
        document.getElementById("address").value = p.address;

        if(p.gender === "male"){
    		document.getElementById("gender_male").checked = true;
	}else if(p.gender === "female"){
    		document.getElementById("gender_female").checked = true;
	}else if(p.gender === "other"){
    		document.getElementById("gender_other").checked = true;
	}

        if(p.dob){

    		let dob = new Date(p.dob);
    		let today = new Date();

    		let diffDays = Math.floor((today - dob) / (1000 * 60 * 60 * 24));

    		if(diffDays < 30){
        		document.getElementById("age").value = diffDays;
        		document.getElementById("age_type").value = "days";
    		}
    		else if(diffDays < 365){
        		let months = Math.floor(diffDays / 30);
        		document.getElementById("age").value = months;
        		document.getElementById("age_type").value = "months";
    		}
    		else{
        		let years = Math.floor(diffDays / 365);
        		document.getElementById("age").value = years;
        		document.getElementById("age_type").value = "years";
    		}
	}

        document.getElementById("patient_results").innerHTML="";
	lockPatientFields(true);
    }

});
</script>

<script>
function lockPatientFields(lock = true){

    const fields = ['patient_search','last_name','country_number','address','age'];

    fields.forEach(id => {
        let el = document.getElementById(id);
        if(el){
            el.readOnly = lock;
        }
    });

    // Lock gender radios
    ['gender_male','gender_female','gender_other'].forEach(id => {
    	let el = document.getElementById(id);
    		if(el){
        		if(lock){
            			el.style.pointerEvents = 'none';
        		}else{
            			el.style.pointerEvents = 'auto';
        	}
    	}
    });

    // Lock age type drop down
    let ageType = document.getElementById('age_type');
    if(ageType){
    	if(lock){
       		ageType.style.pointerEvents = 'none';
       		ageType.style.backgroundColor = '#e9ecef';
    	}else{
       		ageType.style.pointerEvents = 'auto';
       		ageType.style.backgroundColor = '';
    }
}
}
</script>

<script>
var ajaxURL = "/modules-nct/add_appointment-nct/ajax.add_appointment-nct.php";
</script>

</main>
