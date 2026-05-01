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
						<h1>Reset Password</h1>
					</div>
					<form id="password_reset_form" name="password_reset_form" method="post">
						<div class="form-group">
							<label for="new_password" class="is-label-txt">{MEND_SIGN}Password</label>
							<input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter Password" />
						</div>
						<div class="form-group">
							<label for="confirm_new_password" class="is-label-txt">Confirm Password</label>
							<input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" placeholder="Enter Confirm Password" />
							<input type="hidden" name="token" id="token" value="%TOKEN%" />
						</div>
						<div class="form-group text-center cf">
							<button type="submit" name="reset_password" id="reset_password" class="light-orange-btn lg-btn">Reset Password</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		 <div class="auth-bg"></div>
	</section>
</main>