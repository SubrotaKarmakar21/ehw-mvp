<div class="form-group">
	<label for="practicing_since" class="form-label">{MEND_SIGN}Practicing since: &nbsp;</label>
	<input type="text" class="form-control logintextbox-bg required" name="practicing_since" id="practicing_since" value="%practicing_since%" readonly placeholder="mm-yyyy" />
</div>

<div class="form-group">
	<label for="consultation_fees" class="form-label">{MEND_SIGN}Consultation Fees: &nbsp;</label>
	<div class="input-group">
		<span class="input-group-text" id="basic-addon1">₹</span>
		<input type="text" name="consultation_fees" id="consultation_fees" placeholder="Enter Consultation Fees" data-error-container="#error_consultation_fees" class="form-control" value="%consultation_fees%" min="1">
	</div>
	<div id="error_consultation_fees"></div>

	<div class="form-group">
  		<label for="doctor_description" class="form-label">
    			Doctor Description / Experience:
  		</label>

  		<textarea name="doctor_description"
            		  id="doctor_description"
            		  class="form-control"
            		  rows="5"
            		  placeholder="Write about your experience, qualifications, specialization...">
    			%doctor_description%
  		</textarea>
	</div>
</div>



<div class="form-group %associated_with_field_container%">
	<label for="exampleFormControlInput1" class="form-label">{MEND_SIGN}Are you associated with any existing clinic?</label>
	<div class="radio-group">
		<div class="form-check form-check-inline">
			<input class="form-check-input" type="radio" name="associated_to_existing_clinic" id="associated_to_existing_clinic_y" value="y" data-error-container="#error_associated_to_existing_clinic" %associated_to_existing_clinic_y%>
			<label class="form-check-label" for="associated_to_existing_clinic_y">Yes</label>
		</div>
		<div class="form-check form-check-inline">
			<input class="form-check-input" type="radio" name="associated_to_existing_clinic" id="associated_to_existing_clinic_n" value="n" data-error-container="#error_associated_to_existing_clinic" %associated_to_existing_clinic_n%>
			<label class="form-check-label" for="associated_to_existing_clinic_n">No</label>
		</div>
	</div>
	<div id="error_associated_to_existing_clinic"></div>
</div>

<div class="form-group associated_clinic_container %associated_with_field_container% %hide_if_associated_to_existing_clinic_n%">
	<label for="clinic_id" class="form-label">{MEND_SIGN}Clinic: &nbsp;</label>
	<select class="form-control logintextbox-bg required multy-select" name="clinic_id" id="clinic_id" title="Select Clinic">
		<option>Select Clinic</option>
		%get_clinic_list%
	</select>
</div>
