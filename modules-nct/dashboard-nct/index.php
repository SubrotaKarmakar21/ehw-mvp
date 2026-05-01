<?php
$reqAuth = true;
$module = 'dashboard-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.dashboard-nct.php";

extract($_REQUEST);
$winTitle = $headTitle = 'Dashboard - ' . SITE_NM;

$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));

$css_array = array(
    SITE_ADM_PLUGIN. "bootstrap-datepicker/css/datepicker.css",
);

$js_array = array(
    SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION,
    SITE_JS."paging/jquery.twbsPagination.js",
    SITE_ADM_PLUGIN."bootstrap-datepicker/js/bootstrap-datepicker.js",
);

$obj = new Dashboard($module, $_REQUEST);
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