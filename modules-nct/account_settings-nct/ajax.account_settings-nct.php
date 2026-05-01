<?php
$reqAuth = true;
$module = 'account_settings-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.account_settings-nct.php";

$winTitle = 'Account Settings ' . SITE_NM;
$headTitle = 'Account Settings' . SITE_NM;
$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));
$obj = new AccountSettings($module,$_POST);

$response['status'] = '0';
$response['msg'] = "undefined";
//Oops! something went wrong. Please try again later.


if(isset($_POST['oldpwd']) && $_POST['oldpwd'] !=''){
	$oldPass =  isset($_POST['oldpwd'])?$_POST['oldpwd']:'';
	$getExist = $db->select("tbl_users",array("id"),array("password"=>md5($oldPass),"id"=>$_SESSION['sessUserId']))->result();
	
	if(isset($getExist['id']) && $getExist['id'] > 0){
		echo  "true";
	} else {
		echo "false";
	}
	exit;
} else if(isset($_POST['action']) && $_POST['action']=="changepassword"){
	$response = $obj->submit_change_password();
    echo json_encode($response);
    exit;
} 
else if(isset($_POST['action']) && $_POST['action'] == "deactive_account"){
	$user_id =  isset($_POST['user_id'])?$_POST['user_id']:0;
	$response = $obj->processDeactivateAccount($user_id);
    echo json_encode($response);
    exit;
} else if(isset($_POST['action']) && $_POST['action'] == "delete_account"){
	$user_id =  isset($_POST['user_id'])?$_POST['user_id']:0;
	$response = $obj->processDeleteAccount($user_id);
    echo json_encode($response);
    exit;
}

echo json_encode($response);
exit ;
?>