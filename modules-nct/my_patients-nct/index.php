<?php
$reqAuth = false;
$module = 'my_patients-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.my_patients-nct.php";

if(isset($_GET['ajaxSearch'])){
    $obj = new MyPatients($module, $_REQUEST);
    echo $obj->getItemsList($obj->clinic_id,1,false)['content'];
    exit;
}

extract($_REQUEST);
$winTitle = $headTitle = 'My Patients - ' . SITE_NM;

$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));

$clinic_id = isset($_REQUEST['clinic_id']) && $_REQUEST['clinic_id'] != '' ? decryptIt($_REQUEST['clinic_id']) : 0;

if ($sessUserId <= 0) {
    $adminUserId = isset($_SESSION['adminUserId']) ? $_SESSION['adminUserId'] : 0;
    if ($adminUserId <= 0 || $clinic_id <= 0) {
        $msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
        redirectPage(SITE_URL);
    }
} else{

    if ($clinic_id > 0) {
        redirectPage(SITE_MY_PATIENTS);
    }

    // if (($clinic_id > 0 && $clinic_id != $sessUserId) && ($sessUserType == 'clinic' || $sessUserType == 'doctor')) {
    //     $msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
    //     redirectPage(SITE_DASHBOARD);
    // }
}

$css_array = array(
    SITE_ADM_PLUGIN. "bootstrap-datepicker/css/datepicker.css",
    SITE_PLUGIN. "jquery-confirm/jquery-confirm.min.css",
);

$js_array = array(
    SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION,
    SITE_JS."paging/jquery.twbsPagination.js",
    SITE_ADM_PLUGIN."bootstrap-datepicker/js/bootstrap-datepicker.js",
    SITE_PLUGIN. "jquery-confirm/jquery-confirm.min.js",
);

$obj = new MyPatients($module, $_REQUEST);
extract($_REQUEST);
if (isset($action) && isset($method) && $action == "method" && method_exists($obj, $method)) {
    $response['status'] = 1;
    $response['msg'] = 'undefined';
    $response['html'] = $obj -> {$method}();
    echo json_encode($response);
    exit ;
}

$pageContent = $obj -> getPageContent();

require_once DIR_TMPL . "parsing-nct.tpl.php";
?>
