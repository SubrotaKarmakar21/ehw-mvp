<div class="patient-details-wrapper">

    <div class="patient-details-header">
        <h1>Patient Details</h1>
    </div>

    <div class="patient-details-dashboard">

        <!-- LEFT CARD -->
        <div class="patient-details-left">
            <div class="card-box patient-details-card">

                <div class="patient-details-info-row">
                    <img src="%PATIENT_IMAGE%" class="patient-details-avatar">

                    <div class="patient-details-info">
                        <h4>%PATIENT_NAME%</h4>
                        <p><strong>Age:</strong> %PATIENT_AGE% %PATIENT_AGE_TYPE%</p>
                        <p><strong>Gender:</strong> %PATIENT_GENDER%</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- RIGHT CARD -->
        <div class="patient-details-right">
            <div class="card-box patient-details-summary-card">

                <h4 class="patient-details-summary-title">Patient Health Summary</h4>

                <div class="patient-details-summary-content">
                    %PATIENT_SUMMARY%
                </div>

            </div>
        </div>

    </div>

    <div class="patient-details-tabs-wrapper">

    	<div class="patient-details-tabs-header">
        	<span class="patient-details-tab active" data-tab="appointments">
            		Appointment History
        	</span>

        	%SHOW_BILL_TAB%
    	</div>

    	<div class="patient-details-tabs-content">

        	<div id="appointments" class="patient-details-tab-content active">
            		%APPOINTMENT_HISTORY%
        	</div>

        	%BILL_TAB_CONTENT%

    	</div>

   </div>

</div>

<!-- PRESCRIPTION NOT FOUND ALERT -->
<div id="prescriptionToastAlert" class="ehw-prescription-toast">
    No prescription found
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){

    const tabs = document.querySelectorAll('.patient-details-tab');

    tabs.forEach(tab => {
        tab.addEventListener('click', function(){

            // remove active from all tabs
            document.querySelectorAll('.patient-details-tab')
                .forEach(t => t.classList.remove('active'));

            // hide all contents
            document.querySelectorAll('.patient-details-tab-content')
                .forEach(c => c.classList.remove('active'));

            // activate clicked tab
            this.classList.add('active');

            const target = this.getAttribute('data-tab');
            const targetEl = document.getElementById(target);

            if(targetEl){
                targetEl.classList.add('active');
            }

        });
    });

});
</script>

<script>
document.addEventListener("DOMContentLoaded", function(){

    const toast = document.getElementById('prescriptionToastAlert');

    document.querySelectorAll('.no-prescription').forEach(el => {
        el.addEventListener('click', function(){

            // show
            toast.classList.add('show');

            // auto hide after 3 sec
            setTimeout(() => {
                toast.classList.remove('show');
            }, 2500);

        });
    });

});
</script>
