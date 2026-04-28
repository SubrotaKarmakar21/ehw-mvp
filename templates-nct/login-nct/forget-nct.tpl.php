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
						<h1>Forgot Password</h1>
					</div>

					<form id="forgetForm" method="post">
						<div class="form-group">
							<input type="text" class="form-control" placeholder="{placeholder_email_address}" name="email" id="email">
						</div>

						<div class="btn-block d-flex justify-content-center">
							<button type="submit" name="submitForgetForm" id="submitForgetForm" class="btn lg-btn w-100">Submit</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		 <div class="auth-bg"></div>
	</section>
</main>