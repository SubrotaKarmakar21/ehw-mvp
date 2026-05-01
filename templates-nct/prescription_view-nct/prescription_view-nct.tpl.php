<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap" rel="stylesheet">

<div class="container prescription-container">

    <div id="prescription-print-area" class="prescription-page" style="position:relative;">

        <div class="prescription-content">

            <!-- HEADER -->
            <div class="prescription-header">

                <!-- LEFT: DOCTOR BLOCK -->
                <div class="doctor-header-block">

    			<div class="doctor-name">
        			%DOCTOR_NAME%
    			</div>

    			<div class="doctor-meta">
        			%DOCTOR_CATEGORY%
    			</div>

    			<div class="doctor-desc">
        			%DOCTOR_DESCRIPTION%
    			</div>

		</div>

		<!-- RIGHT: CLINIC BLOCK -->
		<div class="clinic-header-block">

    			<img src="%CLINIC_BANNER%" class="clinic-banner-right">

			<div class="clinic-info-right">
				%CLINIC_INFO%
			</div>

            	</div>

	    </div>

            <hr class="invoice-divider">

	    <!-- PATIENT INFO ROW -->
	    <div class="patient-info-row">

    		<div class="patient-left">
        		<span><b>Patient:</b> %PATIENT_NAME%</span>
        		<span><b>Age:</b> %PATIENT_AGE%</span>
        		<span><b>Gender:</b> %PATIENT_GENDER%</span>
    		</div>

    		<div class="patient-right">
        		<span><b>Date:</b> %PRESCRIPTION_DATE%</span>
    		</div>

	    </div>

	    <!-- VITALS -->
	    <div class="vitals-section">
    		%VITALS%
	    </div>

	    <hr class="invoice-divider">

	    <!-- PRESCRIPTION CORE -->
	    %COMPLAINTS%

	    %DIAGNOSIS%

	    %MEDICATIONS%

	    %INVESTIGATIONS%

	    %FOLLOWUP%
        </div>

        <hr class="invoice-divider">

	<!-- ADVICE SECTION -->
	%GENERAL_ADVICE%

	%DIET_PLAN%

	%DOS_DONTS%

	<!-- DOCTOR SIGNATURE -->
	%DOCTOR_SIGNATURE%
    </div>

</div>

%PRINT_BUTTON_HTML%
