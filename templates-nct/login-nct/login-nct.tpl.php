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
						<h1>Welcome to {SITE_NM}</h1>
					</div>

					<form id="loginForm" method="post">
						<div class="form-group">
							<input type="text" class="form-control" placeholder="{placeholder_email_address}" value="%email%" name="email_address">
						</div>

						<div class="form-group">
							<div class="password-field show_hide_password-login">
								<input type="password" class="form-control" placeholder="Enter Password" value="%password%" name="password">
								<a href="javascript:void(0);" class="eye show-hide-pass">
									<img src="{SITE_IMG}eye.svg" alt="">
								</a>
							</div>
						</div>

						<div class="form-group">
							<div class="forgot-block">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" name="remember_me" id="remember_me" value="y" %remember_me%>
									<label class="form-check-label" for="remember_me">
										Remember Me
									</label>
								</div>
								<a href="{SITE_FORGOT}"><b>Forgot Password</b></a>
							</div>
						</div>

						<div class="btn-block d-flex justify-content-center">
							<input type="hidden" name="action" value="method">
							<input type="hidden" name="method" value="submitLoginForm">
							<input type="submit" name="submitLoginForm" id="submitLoginForm" class="btn lg-btn w-100" value="Login">
						</div>
					</form>


					<div class="auth-bottom text-center">
						<p>Haven't received activation email yet? <a href="{SITE_REACTIVATE}"><b>Resend</b></a></p>
						<p>Don't have an account? <a href="{SITE_REGISTER}"><b>Register Here</b></a></p>
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
		} else{
			toastr['error']('Something went wrong!');
		}
	}
</script>