<?php
$reqAuth = false;
$module = 'my_patients-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.my_patients-nct.php";

$winTitle = 'My Patients ' . SITE_NM;
$headTitle = 'My Patients' . SITE_NM;
$metaTag = getMetaTags(array("description" => $winTitle,"keywords" => $headTitle,"author" => AUTHOR));

$obj = new MyPatients($module,$_REQUEST);

$response['status'] = '0';
$response['msg'] = "undefined";
//Oops! something went wrong. Please try again later.


if(isset($_POST['action']) && $_POST['action'] == 'get_my_items'){
    $page = isset($_REQUEST['page']) && (int)$_REQUEST['page'] > 0 ? (int)$_REQUEST['page'] : 1;

    $clinic_id = isset($_REQUEST['clinic_id']) ? (int) $_REQUEST['clinic_id'] : 0;

    $id = (int) $sessUserId;

    $content = $obj->getItemsList($id,$page,true);
    echo json_encode($content);
    exit;
} else if(isset($_POST['action']) && $_POST['action'] == 'get_patient_details'){
    $id = isset($_REQUEST['id']) && $_REQUEST['id'] != '' ? (int) $_REQUEST['id'] : 0;

    $content['html'] = $obj->get_user_info($id);
    echo json_encode($content);
    exit;
} else if (isset($_POST['action']) && $_POST['action'] == 'delete_patient') {
    $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
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
    $db->pdoQuery("DELETE FROM tbl_users WHERE id = ".$id." AND parent_id = ".$sessUserId."");

    $response['status'] = true;
    $response['message'] = 'Record successfully deleted';
    echo json_encode($response);
    exit;

}

echo json_encode($response);
exit ;
?>
