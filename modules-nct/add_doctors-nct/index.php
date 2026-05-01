<?php
$reqAuth = true;
$module = 'add_doctors-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.add_doctors-nct.php";


$id = isset($_REQUEST['id']) && $_REQUEST['id'] != '' ? decryptIt($_REQUEST['id']) : 0;

if ($id > 0) {
    $winTitle = $headTitle = 'Edit Doctor - ' . SITE_NM;    
} else{
    $winTitle = $headTitle = 'Add Doctor - ' . SITE_NM;
}

if ($sessUserType != 'clinic') {
    $msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
    redirectPage(SITE_DASHBOARD);
}


$metaTag = getMetaTags(array(
    "description" => $winTitle,
    "keywords"    => $headTitle,
    "author"      => AUTHOR,
));

$css_array = array(
    SITE_CSS . "cropper.min.css",
    SITE_PLUGIN . "select2/select2_metro.css",
    SITE_CSS . "jquery.timepicker.min.css",
    SITE_ADM_PLUGIN. "bootstrap-datepicker/css/datepicker.css",
    SITE_PLUGIN. "jquery-confirm/jquery-confirm.min.css",
);

$js_array = array(
    SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION,
    SITE_JS . 'intlTelInput-jquery.min.js',
    SITE_JS . "cropper.min.js",
    SITE_PLUGIN . "select2/select2.min.js",
    SITE_JS . "jquery.timepicker.min.js",
    SITE_ADM_PLUGIN. "bootstrap-datepicker/js/bootstrap-datepicker.js",
    SITE_PLUGIN. "jquery-confirm/jquery-confirm.min.js",
);

$obj = new AddDoctor($module, $_REQUEST);
extract($_REQUEST);
if (isset($action) && isset($method) && $action == "method" && method_exists($obj, $method)) {
    $response['status'] = 1;
    $response['msg'] = 'undefined';
    $response['html'] = $obj->{$method}();

    $status = isset($response['html']['status']) ?  $response['html']['status'] : 'err';
    $msgType = $_SESSION["msgType"] = disMessage(array(
        'type' => $status,
        'var'  => isset($response['html']['message']) ?  $response['html']['message'] : 'error',
    ));

    if ($status == 'suc') {
        redirectPage(SITE_MY_DOCTORS);
    } else{
        redirectPage(SITE_ADD_DOCTORS);
    }
    
    
}
$pageContent = $obj -> getPageContent();

require_once DIR_TMPL . "parsing-nct.tpl.php";
?>
