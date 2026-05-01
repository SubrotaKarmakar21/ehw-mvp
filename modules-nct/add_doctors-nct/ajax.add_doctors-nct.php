<?php
$reqAuth = false;
$module = 'add_doctors-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.add_doctors-nct.php";

$winTitle = 'Add Doctor ' . SITE_NM;
$headTitle = 'Add Doctor' . SITE_NM;
$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));
$obj = new AddDoctor($module);

$response['status'] = '0';
$response['msg'] = "undefined";
//Oops! something went wrong. Please try again later.


if (isset($_POST['action']) && $_POST['action'] == 'remove_profile_picture') {
	$id = isset($_POST['id']) ? $_POST['id'] : 0;


	$old_image = getTableValue('tbl_users','profile_photo',array('id' => $id));


	if ($old_image != '') {
		$upload_dir = DIR_UPD_PROFILE_IMAGE;
		if (file_exists($upload_dir.$old_image)) {
			unlink($upload_dir.$old_image);
		}

		if (file_exists($upload_dir.'th1_'.$old_image)) {
			unlink($upload_dir.'th1_'.$old_image);
		}

		if (file_exists($upload_dir.'th2_'.$old_image)) {
			unlink($upload_dir.'th2_'.$old_image);
		}
	}

	
	$db->update('tbl_users',array('profile_photo' => ''),array('id' => $id));

	$profile_img = get_image_url('',"profile_photo",'th2_');
	
	$response['profile_img'] = $profile_img;

	echo json_encode($response);
	exit;

} else if (isset($_POST['action']) && $_POST['action'] == 'add_slot') {

	$day 		= isset($_POST['day']) ? $_POST['day'] : '';
	$index 		= isset($_POST['index']) && $_POST['index'] != '' ? $_POST['index'] : '';

	$response = $obj->create_slot_html('y',$day);
	
	echo json_encode($response);
	exit ;

} else if (isset($_POST['action']) && $_POST['action'] == 'remove_slot') {

	$response['status'] = false;
	$response['message'] = something_went_wrong;

	$id 		= isset($_POST['id']) ? $_POST['id'] : 0;
	if ($id > 0) {
		$db->delete('tbl_users_time_slot',array('id' => $id));

		$response['status'] = true;
		$response['message'] = 'slot successfully removed.';
	}
	
	echo json_encode($response);
	exit;

}

echo json_encode($response);
exit ;
?>