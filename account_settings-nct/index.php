<?php
$reqAuth = true;
$module = 'account_settings-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.account_settings-nct.php";

extract($_REQUEST);
$winTitle = $headTitle = 'Account Settings - ' . SITE_NM;

$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));

$css_array = array(
    SITE_PLUGIN. "jquery-confirm/jquery-confirm.min.css",
);

$js_array = array(
    SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION,
    SITE_PLUGIN. "jquery-confirm/jquery-confirm.min.js",
);

$obj = new AccountSettings($module, $_REQUEST);
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