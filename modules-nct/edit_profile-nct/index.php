<?php
$reqAuth = true;
$module = 'edit_profile-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.edit_profile-nct.php";

$winTitle = $headTitle = 'Edit Profile - ' . SITE_NM;

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

$obj = new EditProfile($module, $_REQUEST);
extract($_REQUEST);
if (isset($action) && isset($method) && $action == "method" && method_exists($obj, $method)) {
    $response['status'] = 1;
    $response['msg'] = 'undefined';
    $response['html'] = $obj->{$method}();

    $msgType = $_SESSION["msgType"] = disMessage(array(
        'type' => isset($response['html']['status']) ?  $response['html']['status'] : 'err',
        'var'  => isset($response['html']['message']) ?  $response['html']['message'] : 'error',
    ));
    redirectPage(SITE_PROFILE);
}
$pageContent = $obj -> getPageContent();

require_once DIR_TMPL . "parsing-nct.tpl.php";
?>
