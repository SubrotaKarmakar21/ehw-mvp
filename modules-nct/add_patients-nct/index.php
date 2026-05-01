<?php
$reqAuth = true;
$module = 'add_patients-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.add_patients-nct.php";


$id = isset($_REQUEST['id']) && $_REQUEST['id'] != '' ? decryptIt($_REQUEST['id']) : 0;

if ($id > 0) {
    $winTitle = $headTitle = 'Edit Patient - ' . SITE_NM;    
} else{
    $winTitle = $headTitle = 'Add Patient - ' . SITE_NM;
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
);

$js_array = array(
    SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION,
    SITE_JS . 'intlTelInput-jquery.min.js',
    SITE_JS . "cropper.min.js",
    SITE_PLUGIN . "select2/select2.min.js",
    SITE_JS . "jquery.timepicker.min.js",
    SITE_ADM_PLUGIN. "bootstrap-datepicker/js/bootstrap-datepicker.js",
);

$obj = new AddPatients($module, 0, $_REQUEST);
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
        redirectPage(SITE_MY_PATIENTS);
    } else{
        redirectPage(SITE_EDIT_PATIENTS.encryptIt($id));
    }
    
}
$pageContent = $obj -> getPageContent();

require_once DIR_TMPL . "parsing-nct.tpl.php";
?>
