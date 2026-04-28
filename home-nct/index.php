<?php
$reqAuth = false;
$module = 'home-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.home-nct.php";

extract($_REQUEST);

$user_action = isset($_GET['user_action'])?$_GET['user_action']:'';
if ($user_action == 'reactivate_user_account') {
    $winTitle = 'Reactivate Account - '.SITE_NM;
    $headTitle = 'Reactivate Account';
} else{
    $winTitle = Welcome_to.' - ' . SITE_NM;
    $headTitle = 'Welcome';
}

$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));

$js_array = array(SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION);

$obj = new Home($module, $_REQUEST);
extract($_REQUEST);

if ($sessUserId <= 0 && $user_action == 'reactivate_user_account') {
    redirectPage(SITE_URL);
}

if (isset($_POST['submitAccountReactivation'])) {
    if($sessUserId>0){
        $objHome->submitUserReactivation($sessUserId);
    } else{
        $msgType = $_SESSION['msgType'] = disMessage(array('type' => 'err', 'var' => WENT_WRONG));
        redirectPage(SITE_REACTIVE_ACCOUNT);
        exit;
    }
} else if (isset($action) && isset($method) && $action == "method" && method_exists($obj, $method)) {
    $response['status'] = 1;
    $response['msg'] = 'undefined';
    $response['html'] = $obj -> {$method}();
    echo json_encode($response);
    exit ;
}


if($user_action == 'reactivate_user_account' && $sessUserId > 0){
    $isAccountDeactivate = getTableValue('tbl_users','is_deactivated',array('id' => $sessUserId));

    if ($isAccountDeactivate == 'n') {
        redirectPage(SITE_DASHBOARD);
    }
    $pageContent = $obj->getUserAccountReactivationPageContent();
} else{
    $pageContent = $obj -> getPageContent();
}




require_once DIR_TMPL . "parsing-nct.tpl.php";
?>