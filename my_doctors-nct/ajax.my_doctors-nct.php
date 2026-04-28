<?php
$reqAuth = false;
$module = 'my_doctors-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.my_doctors-nct.php";

$winTitle = 'My Doctors ' . SITE_NM;
$headTitle = 'My Doctors' . SITE_NM;
$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));
$obj = new MyDoctors($module,$_REQUEST);

$response['status'] = '0';
$response['msg'] = "undefined";
//Oops! something went wrong. Please try again later.


if(isset($_POST['action']) && $_POST['action'] == 'get_my_items'){
    $page = isset($_REQUEST['page'])?$_REQUEST['page']:0;
    $clinic_id = isset($_REQUEST['clinic_id']) && $_REQUEST['clinic_id'] != '' ? decryptIt($_REQUEST['clinic_id']) : 0;
    $id = $clinic_id > 0 ? $clinic_id : $sessUserId;

    $page = isset($page)?(int)$page:0;

    $content = $obj->getItemsList($id,$page,true);
    echo json_encode($content);
    exit;
} else if(isset($_POST['action']) && $_POST['action'] == 'get_doctor_details'){

    $id = isset($_REQUEST['id']) && $_REQUEST['id'] != '' 
        ? decryptIt($_REQUEST['id']) 
        : 0;

    $content = array();
    $content['html'] = $obj->get_user_info((int)$id);

    echo json_encode($content);
    exit;

} else if (isset($_POST['action']) && $_POST['action'] == 'delete_doctor') {
    $id = isset($_REQUEST['id']) && $_REQUEST['id'] != '' ? decryptIt($_REQUEST['id']) : 0;
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

    $db->delete('tbl_users_specialties',array('user_id' => $id));
    $db->delete('tbl_users_doctor_type',array('user_id' => $id));
    $db->delete('tbl_users_time_slot',array('user_id' => $id));
    $db->delete('tbl_users',array('id' => $id));
    
    $response['status'] = true;
    $response['message'] = 'Record successfully deleted';
    echo json_encode($response);
    exit;

}

echo json_encode($response);
exit ;
?>
