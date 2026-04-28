<!--profile_image_cropper content-->
<div class="modal fade" id="avatar-modal" tabindex="-1" role="dialog" aria-labelledby="avatar-modal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<form class="avatar-form" id="avatar-form" action="{SITE_CROP}" enctype="multipart/form-data" method="post">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">{Change_Profile_Picture}</h3>					
				</div>
				<div class="modal-body">
					<div class="avatar-body">
						<!-- Upload image and data -->
						<div class="avatar-upload">
							<input type="hidden" class="avatar-src" name="avatar_src" id="avatar_src" />
							<input type="hidden" class="avatar-data" name="avatar_data" id="avatar_data" />
						</div>
						<!-- Crop and preview -->
						<div class="row">
							<div class="col-md-12 text-center">
								<div class="avatar-wrapper"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button id="btnCrop" name="btnCrop" type="button" class="btn btn_blue_new">
						{Crop}
					</button>
					<button type="button" class="btn btn-link" data-dismiss="modal">
						{Close}
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!--profile_image_cropper content-->
