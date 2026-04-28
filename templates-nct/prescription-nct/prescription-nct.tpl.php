<div class="container-fluid">

    <h2 class="mb-4">Write Prescription</h2>

    <div class="row">

        <div class="col-md-4">
            <label>Patient Name</label>
            <input type="text" class="form-control" value="%PATIENT_NAME%" readonly>
        </div>

	<div class="col-md-3">
    		<label>Age</label>
    		<div class="d-flex gap-2">
        		<input type="text" class="form-control" value="%PATIENT_AGE%" readonly>
        		<input type="text" class="form-control" value="%AGE_TYPE%" readonly style="max-width:120px;">
    		</div>
	</div>

        <div class="col-md-2">
            <label>Gender</label>
            <input type="text" class="form-control" value="%PATIENT_GENDER%" readonly>
        </div>

        <div class="col-md-2">
            <label>Date</label>
            <input type="text" class="form-control" value="%DATE%" readonly>
        </div>

	<hr class="my-4">

	<h5 class="mb-3">Vitals</h5>

	<div class="row">

    		<div class="col-md-2">
        		<label>Height (cm)</label>
        		<input type="number" id="height" class="form-control" placeholder="e.g. 170">
    		</div>

    		<div class="col-md-2">
        		<label>Weight (kg)</label>
        		<input type="number" id="weight" class="form-control" placeholder="e.g. 65">
    		</div>

    		<div class="col-md-2">
        		<label>BMI</label>
        		<input type="text" id="bmi" class="form-control" readonly placeholder="Auto">
    		</div>

    		<div class="col-md-2">
        		<label>Blood Pressure (mmHg)</label>
        		<input type="text" id="bp" class="form-control" placeholder="120/80">
    		</div>

    		<div class="col-md-2">
        		<label>Pulse (bpm)</label>
        		<input type="number" id="pulse" class="form-control" placeholder="72">
    		</div>

    		<div class="col-md-2">
        		<label>Respiratory Rate (breathes/min)</label>
        		<input type="number" id="rr" class="form-control" placeholder="16">
    		</div>

    		<div class="col-md-2 mt-3">
        		<label>O₂ Saturation (%)</label>
        		<input type="number" id="spo2" class="form-control" placeholder="98">
    		</div>

	</div>

	<hr class="my-4">

	<h5 class="mb-3">Complaints</h5>

	<div class="table-responsive">
    		<table class="table table-bordered" id="complaints-table">
        		<thead>
            			<tr class="complaint-row">
                			<th style="width:90%;">Complaint</th>
                			<th style="width:10%;">Action</th>
            			</tr>
        		</thead>
        		<tbody id="complaints-body">

            			<tr>
                			<td>
                    				<input type="text" name="complaints[]" class="form-control" placeholder="e.g. Fever, headache...">
                			</td>
                			<td class="text-center">
                    				<button type="button" class="btn btn-outline-danger remove-complaint">×</button>
                			</td>
            			</tr>

        		</tbody>
    		</table>
	</div>

	<button type="button" class="btn btn-outline-secondary mt-2" id="add-complaint">+ Add Complaint</button>

	<hr class="my-4">

	<h5 class="mb-3">Diagnosis</h5>

	<div class="row">

    		<div class="col-md-6">
        		<input type="text" id="diagnosis" class="form-control" name="diagnosis" placeholder="Enter diagnosis (optional)">
    		</div>

	</div>

	<hr class="my-4">

	<h5 class="mb-3">Medications</h5>

	<div class="table-responsive">
    		<table class="table table-bordered" id="medicine-table">
        		<thead>
            			<tr>
                			<th style="width:5%;">#</th>
                			<th style="width:20%;">Medicine</th>
                			<th>M</th>
                			<th>A</th>
                			<th>E</th>
                			<th>N</th>
                			<th style="width:10%;">Duration</th>
                			<th style="width:10%;">Type</th>
                			<th style="width:15%;">Advice</th>
                			<th style="width:15%;">Remarks</th>
                			<th>Action</th>
            			</tr>
        		</thead>

        		<tbody id="medicine-body">

            			<tr>

                			<td class="serial">1</td>

                			<td>
                    				<input type="text" class="form-control medicine-name" placeholder="Medicine name">
                			</td>

                			<td><input type="text" class="form-control dose" placeholder="1"></td>
                			<td><input type="text" class="form-control dose" placeholder="0"></td>
                			<td><input type="text" class="form-control dose" placeholder="1"></td>
                			<td><input type="text" class="form-control dose" placeholder="0"></td>

                			<td>
                    				<input type="number" class="form-control duration" placeholder="5">
                			</td>

                			<td>
                    				<select class="form-control duration-type">
                        				<option value="days">Days</option>
                        				<option value="weeks">Weeks</option>
                        				<option value="months">Months</option>
                    				</select>
                			</td>

                			<td>
                    				<input type="text" class="form-control advise" placeholder="After food">
                			</td>

                			<td>
                    				<input type="text" class="form-control remarks" placeholder="Optional">
                			</td>

                			<td class="text-center">
                    				<button type="button" class="btn btn-outline-danger remove-medicine">×</button>
                			</td>

            			</tr>

        		</tbody>
    		</table>
	</div>

	<button type="button" class="btn btn-outline-secondary mt-2" id="add-medicine">+ Add Medicine</button>

	<hr class="my-4">

	<h4>Investigations</h4>

		<div id="investigation_container">

    			<div class="row mb-2 investigation-row">

        			<div class="col-md-10 position-relative">
            				<input type="text" class="form-control investigation-input" placeholder="Search or type test name">
        			</div>

        			<div class="col-md-2">
            				<button type="button" class="btn btn-outline-danger remove-investigation w-100">Remove</button>
        			</div>

    			</div>

		</div>

	<button type="button" class="btn btn-outline-secondary mt-2" id="addInvestigationBtn">+ Add Test</button>

	<hr class="my-4">

	<h4>Follow-Up (Optional)</h4>

	<div class="row">

    		<div class="col-md-4">
        		<label>Next Visit Date</label>
        		<input type="date" id="followup_date" class="form-control">
    		</div>

    		<div class="col-md-8">
        		<label>Notes</label>
        		<input type="text" id="followup_notes" class="form-control" placeholder="e.g. Show reports / After 5 days">
    		</div>

	</div>

	<div class="text-end mt-4">
    		<button id="nextBtn" class="btn btn-ehw-green">Next →</button>
	</div>

</div>

<script>
var SITE_URL = "<?php echo SITE_URL; ?>";
</script>
