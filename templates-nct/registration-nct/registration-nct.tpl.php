<script src='https://www.google.com/recaptcha/api.js'></script>
<link href="{SITE_CSS}intlTelInput.css" rel="stylesheet" type="text/css"/>
<script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
<script type="text/javascript" src="https://apis.google.com/js/api.js"></script>
<script src="https://accounts.google.com/gsi/client" async defer></script>

<main class="flex-shrink-0 auth-main">
	<section class="auth-section">
		<div class="container">
			<div class="auth-box">
				<div class="fom-sm">
					<div class="auth-top">
						<figure>
							<a href="{SITE_URL}">
								<img src="{SITE_LOGO_URL}" alt="{SITE_NM}">
							</a>
						</figure>
						<h1>Signup</h1>
					</div>
					<form id="signUp" method="post">

						<div class="form-group">
							<label for="exampleFormControlInput1" class="form-label">{MEND_SIGN}User Type</label>
							<div class="radio-group">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="user_type"
									id="user_type_doctor" value="doctor" data-error-container="#error_user_type" checked>
									<label class="form-check-label" for="user_type_doctor">Doctor</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="user_type"
									id="user_type_clinic" value="clinic" data-error-container="#error_user_type">
									<label class="form-check-label" for="user_type_clinic">Clinic</label>
								</div>
							</div>
							<div id="error_user_type"></div>
						</div>

						<div class="form-group doctor_info_container ">
							<label for="first_name" class="is-label-txt">{MEND_SIGN}First Name</label>
							<input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter First Name">
						</div>

						<div class="form-group doctor_info_container ">
							<label for="last_name" class="is-label-txt">{MEND_SIGN}Last Name</label>
							<input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter Last Name">
						</div>

						<div class="form-group clinic_name_container hidden">
							<label for="clinic_name" class="is-label-txt">{MEND_SIGN}Clinic Name</label>
							<input type="text" name="clinic_name" id="clinic_name" class="form-control" placeholder="Enter First Name">
						</div>

						<div class="form-group">
							<label for="email_address" class="is-label-txt">{MEND_SIGN}{label_email_address}</label>
							<input type="text" name="email_address" id="email_address" class="form-control" placeholder="{placeholder_email_address}">
						</div>

						<div class="form-group">
							<label for="cpassword" class="is-label-txt">{MEND_SIGN}Password</label>
							<input type="password" name="cpassword" id="cpassword" class="form-control" placeholder="Enter Password">
						</div>
						<div class="form-group">
							<label for="password" class="is-label-txt">Confirm Password</label>
							<input type="password" name="password" id="password" class="form-control" placeholder="Enter Confirm Password">
						</div>

						<div class="form-group">
							<label for="country_number" class="is-label-txt">{MEND_SIGN}{label_phone_number}</label>
							<div class="country-field">
								<input type="text" name="phone_no" id="country_number" placeholder="{placholder_phone_number}" class="form-control logintextbox-bg" value="">
								<input type="hidden" name="phone_country_code" id="phone_country_code" value="">
								<input type="hidden" name="phone_iso2_code" id="phone_iso2_code" value="">
							</div>
						</div>

						<div class="form-group">
							<label for="referral_or_community_code" class="is-label-txt">Referral Code/Community Code</label>
							<input type="text" name="referral_or_community_code" id="referral_or_community_code" class="form-control" placeholder="Enter Referral Code/Community Code">
						</div>

						<div class="form-group">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" name="txtTerms" id="txtTerms_t" value="y" data-error-container="#error_txtTerms">
								<label class="form-check-label" for="txtTerms_t">
									I accept <a target="_blank" href="{SITE_CMS}%terms_url%">Terms of Service	</a>
								</label>
							</div>
							<div id="error_txtTerms"></div>

						</div>

						<div class="form-group">
							<div class="form-group captcha-img">
								<input type="hidden" class="hiddenRecaptcha " name="hiddenRecaptcha" id="hiddenRecaptcha">
								<div class="g-recaptcha" data-sitekey="{GOOGLE_RECAPTCHA_SITE_KEY}"></div>
								<label for="hiddenRecaptcha" generated="true" class="error" style="display:none"></label>			
							</div>
						</div>

						<div class="form-group">
							<input type="hidden" name="action" value="method">
							<input type="hidden" name="method" value="submitSignupForm">
							<button type="submit" class="btn lg-btn w-100" id="submitSignup" name="submitSignup">Create Account</button>
						</div>
					</form>

					<div class="auth-bottom text-center">
						<p>Already have an account? <a href="{SITE_LOGIN}"><b>Signin</b></a></p>

						<div class="or-line">
							<span>Or</span>
						</div>

						<div id="g_id_onload"  data-client_id="{GOOGLE_CLIENT_ID}" data-context="signup" data-ux_mode="popup" data-callback="googleLoginEndpoint" data-itp_support="true">
						</div>

						<div class="g_id_signin google-btn google-center-btn" data-type="standard" data-shape="pill" data-theme="outline" data-text="signup_with" data-size="large">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="auth-bg"></div>
	</section>
</main>


<script type="text/javascript">
	function init() {
		gapi.load('auth2', function () {
        /* Ready. Make a call to gapi.auth2.init or some other API */
		});
	}

	function googleLoginEndpoint(googleUser) {
    // get user information from Google
		var id_token =  googleUser.credential;
		if(id_token !== ""){

			$.ajax({
				type: "POST",
				url: siteUrl + "signin/google",
				dataType: "json",
				data: {
					"first_name": '',
					"last_name": '',
					"email": '',
					"profile_picture": '',
					"provider": "google",
					"provider_id": '',
					"id_token": id_token
				},
				dataType: 'json',
				beforeSend: function () {
					addOverlay();
				}
			}).done(function (data) {
				if (data) {
					if (data.status == 'success') {
						toastr[data.status](data.message);
						setTimeout(function () {
							window.location.href = data.redirect_url
						}
						, 2000);
					} else {
						toastr[data.status](data.message);
						setTimeout(function () {
							window.location.href = data.redirect_url
						}
						, 2000);
					}
				} else {
					toastr['error']('Something went wrong!');
				}
				removeOverlay();
			}).fail(function (jqXHR, textStatus, errorThrown) {
				toastr['error']('Something went wrong!');
				removeOverlay();
			});
		}else{
			toastr['error']('Something went wrong!');
		}
	}
</script>