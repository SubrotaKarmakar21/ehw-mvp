<footer class="">
	<div class="container">
		<div class="row">
			<div class="col-md-6">
				<p class="copyright">©%YEAR% {SITE_NM}. All rights reserved</p>
			</div>
			<div class="col-md-6">
				<ul class="footer-nav">
					%MENU_ITEMS%
				</ul>
			</div>
		</div>
	</div>
</footer>

<div class="modal fade gen-modal-bx" id="crop_profile_cover_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Crop Image</h4>
			</div>
			<form class="avatar-form" action="" enctype="multipart/form-data" method="post" name="avtar_form" id="avtar_form">
				<div class="modal-body">
					<input type="hidden" class="avatar-src" name="avatar_src">
					<input type="hidden" class="avatar-data" name="avatar_data" value="">
					<input type="hidden" name="which_types" id="which_types" value="banner_image">
					<div class="img-container">
						<img id="srcPhotoProfile" alt="Picture" style="max-width: 500px;">
					</div>
					<div class="modal-footer form-group">
						<div class="col-md-6 text-left">
							<div class="btn-group">
								<button type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Zoom In">
									<span data-animation="false" class="docs-tooltip" data-toggle="tooltip" title="Zoom In">
										<span class="fa fa-search-plus"></span>
									</span>
								</button>
								<button type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Zoom Out">
									<span data-animation="false" class="docs-tooltip" data-toggle="tooltip" title="Zoom Out">
										<span class="fa fa-search-minus"></span>
									</span>
								</button>
							</div>
							<div class="btn-group">
								<button type="button" class="btn btn-primary" data-method="rotate" data-option="-90" title="Rotate Left">
									<span data-animation="false" class="docs-tooltip" data-toggle="tooltip" title="Rotate Left">
										<span class="fa fa-undo"></span>
									</span>
								</button>
								<button type="button" class="btn btn-primary" data-method="rotate" data-option="90" title="Rotate Right">
									<span data-animation="false" class="docs-tooltip" data-toggle="tooltip" title="Rotate Right">
										<span class="fa fa-repeat"></span>
									</span>
								</button>
							</div>
						</div>
						<div class="form-actions fluid col-md-6 text-right">
							<div class="row ">
								<div class="col-lg-12">
									<div class="text-center">
										<button type="submit" name="btn_crop_submit" id="btn_crop_submit" class="btn btn-primary btn-lg" >Submit</button>
										<button type="button" name="close_crop_modal" id="close_crop_modal" class="btn btn-secondary btn-lg" >Cancel</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	var siteNm = '{SITE_NM}',
	sessUserId = '<?php echo isset($_SESSION['sessUserId']) ? $_SESSION['sessUserId'] : "0"; ?>',
	sessUserType = '<?php echo isset($_SESSION['user_type']) ? $_SESSION['user_type'] : ""; ?>',
	siteUrl = '{SITE_URL}',
	ajaxUrl = '<?php echo SITE_URL."ajax-".$this->module."/"; ?>',
	default_profile_image ='<?php echo SITE_IMG . USER_DEFAULT_AVATAR;?>',
	sitePlugin = '{SITE_PLUGIN}',          
	reCaptchaSiteKey = '{GOOGLE_RECAPTCHA_SITE_KEY}',
	CURRENCY_SYMBOL = '{CURRENCY_SYMBOL}',
	DEFAULT_CURRENCY_CODE = '{DEFAULT_CURRENCY_CODE}',
	phone_iso2_code = '{phone_iso2_code}',
	phone_contact_code = '{phone_contact_code}',
	google_client_id = '{GOOGLE_CLIENT_ID}',
	/*used for infinite scrolling*/ 
	pageIndex = 1, 
	 /*used for infinite scrolling*/ 
	hasmoredata = true;
</script>



<script src="{SITE_JS}jquery.min.js"></script>
<script src="{SITE_JS}popper.min.js"></script>
<script src="{SITE_JS}jquery.validate.min.js"></script>
<script src="{SITE_JS}nct-bootstrap.min.js"></script>
<script src="{SITE_JS}bootstrap-select.min.js"></script>

<div class="loader-widget" style="display: none;">
	<figure>
		<img src="<?php echo SITE_IMG;?>ajax-loader-transparent.gif">
	</figure>
</div>
<script>
	function addOverlay() {$(".loader-widget").show();}
	function removeOverlay() {
		setTimeout(function() { $(".loader-widget").fadeOut(); }, 1000);
	}
</script>
<script src="<?php echo SITE_LNG.$_SESSION["lId"]; ?>.js"></script>

<!-- <script src="{SITE_PLUGIN}jQuery-Form-Validator-master/form-validator/jquery.form-validator.min.js"></script> -->

<script src="{SITE_JS}custom.js"></script>
<script src="{SITE_JS}wow.min.js"></script>

<script type="text/javascript" src="{SITE_JS}general_validation.js"></script>

<?php
global $css_array,$js_array,$js_variables;
if (!empty($css_array))
{
	foreach ($css_array as $k=>$v)
	{
		echo '<link href="' . $v . '" rel="stylesheet" type="text/css"/>';
	}
}

if($js_variables!=NULL){
	echo '<script type="text/javascript">'.$js_variables.'</script>';
}

if (!empty($js_array))
{
	foreach ($js_array as $k=>$v)
	{
		echo '<script src="' . $v . '" type="text/javascript"></script>';
	}
}
?>
<script type="text/javascript"> 
	$(document).ready(function(){

		
	});
</script>