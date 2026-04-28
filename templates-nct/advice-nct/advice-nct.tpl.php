<div class="container-fluid">

    <h4 class="mb-4">General Advice</h4>

    <div class="mb-3">
        <textarea id="advice_box" class="form-control" rows="6">%GENERAL_ADVICE%</textarea>
    </div>

    <h4 class="mb-3 mt-4">Diet Plan</h4>

    <div class="mb-3">
        <textarea id="diet_box" class="form-control" rows="6">%DIET_PLAN%</textarea>
    </div>

    <h4 class="mt-4">DOs & DON'Ts</h4>

    <div class="table-responsive">
    	<table class="table table-bordered">
       		<thead>
           		<tr>
               			<th>DOs</th>
                		<th>DON'Ts</th>
            		</tr>
        	</thead>
        	<tbody>
            		%DOS_DONTS_TABLE%
        	</tbody>
    	</table>
    </div>

    <div class="mt-4">

    	<div class="form-check mb-3">
        	<input class="form-check-input" type="checkbox" id="doctor_confirmation">
        	<label class="form-check-label" for="doctor_confirmation">
            		I confirm that I have reviewed this prescription and the above advice, and it reflects my professional judgment.
        	</label>
    	</div>

    </div>

    <h4 class="mt-4">Doctor Signature</h4>

    <div style="border:1px solid #ccc; border-radius:8px; padding:10px;">

    	<canvas id="signaturePad" width="500" height="150" style="border:1px solid #ddd;"></canvas>

    	<div class="mt-2">
        	<button type="button" id="clearSignature" class="btn btn-outline-secondary btn-sm">Clear</button>
    	</div>

    </div>

    <button id="saveAdviceBtn" class="btn btn-ehw-green">Save & Generate Prescription</button>

</div>

<script>
var SITE_URL = "<?php echo SITE_URL; ?>";
var prescription_id = "%PRESCRIPTION_ID%";
</script>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{SITE_JS}modules/advice-nct.js"></script>
