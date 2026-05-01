<main class="flex-shrink-0 inner-main">
    <section class="inner-pages gray-bg">
        <div class="container">
	    %clinic_banner_html%
            <div class="common-white-box profile-box position-relative">
                <a href="{SITE_EDIT_PROFILE}" class="edit-profile-btn"><i class="fa-solid fa-pen"></i></a>
                <ul class="profile-row">
                    <li class="profile-cell">
                        <div class="profile-user-info">
                            <span class="profile-pic">
                                <img src="%uploaded_image%" alt="">
                            </span>
                            <div class="user-info">
                                <h3>%user_name%</h3>
                                <p class="user-icon-block">
                                    <i class="fa-solid fa-phone"></i> %phone_country_code% %phone_no%
                                </p>
                            </div>
			    <div class="profile-partner-id">
    				Partner ID: <strong>%partner_id%</strong>
			    </div>
                        </div>
                    </li>
                    <li class="profile-cell">
                        <div class="row profile-info-block">
                            <div class="col-lg-12">
                                <h4 class="mb-1">Address:</h4>
                            </div>
                            <div class="col-lg-12">
                                %address%
                            </div>
                        </div>
                    </li>
		    <li class="profile-cell">
    			<div class="row profile-info-block">
        			<div class="col-lg-4">
            				<h4>GSTIN:</h4>
        			</div>
        			<div class="col-lg-8">
            				%GSTIN%
        			</div>
    			</div>
		    </li>
		    <li class="profile-cell">
    			<div class="row profile-info-block">
        			<div class="col-lg-4">
            				<h4>Clinic Description:</h4>
        			</div>
        			<div class="col-lg-8">
            				%clinic_description%
        			</div>
    			</div>
		    </li>
                    <li class="profile-cell">
                        <div class="row profile-info-block">
                            <div class="col-lg-4">
                                <h4>Type of Professional:</h4>
                            </div>
                            <div class="col-lg-8">

                                <ul class="tags">
                                    %type_of_doctors_str%
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="profile-cell">
                        <div class="row profile-info-block">
                            <div class="col-lg-4">
                                <h4>Specialties:</h4>
                            </div>
                            <div class="col-lg-8">
                                <ul class="tags">
                                    %specialties_str%
                                </ul>
                            </div>
                        </div>
                    </li>

                    %consultation_fees_html%

		    <li class="profile-cell %hide_when_clinic%">
    			<div class="row profile-info-block">
        			<div class="col-lg-4">
            				<h4>Doctor Description:</h4>
        			</div>
        			<div class="col-lg-8">
            				%doctor_description%
        			</div>
    			</div>
		    </li>
                    %practicing_since_html%

                    %associated_clinic_html%

                    <li class="profile-cell %hide_when_clinic%">
                        <div class="row profile-info-block">
                            <div class="col-lg-12">
                                <h4 class="mb-1">Available Days and Time Slots</h4>
                            </div>
                            <div class="col-lg-12">
                                <div class="slot-view">
                                    %time_slot_html%
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</main>
