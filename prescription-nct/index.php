<?php
$reqAuth = true;
$module = 'prescription-nct';

require_once "../../includes-nct/config-nct.php";
require_once "class.prescription-nct.php";

extract($_REQUEST);

$winTitle = $headTitle = 'Write Prescription - ' . SITE_NM;

$metaTag = getMetaTags(array(
    "description" => $winTitle,
    "keywords" => $headTitle,
    "author" => AUTHOR
));

$css_array = array(
    // Add custom CSS later if needed
);

$js_array = array(
    SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION,
);

$obj = new Prescription($module, $_REQUEST);

if (isset($action) && isset($method) && $action == "method" && method_exists($obj, $method)) {
    $response['status'] = 1;
    $response['msg'] = 'undefined';
    $response['html'] = $obj->{$method}();
    echo json_encode($response);
    exit;
}

$pageContent = $obj->getPageContent();

/* THIS IS THE MOST IMPORTANT LINE */
require_once DIR_TMPL . "parsing-nct.tpl.php";
