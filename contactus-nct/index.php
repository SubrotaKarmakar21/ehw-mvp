<?php
$reqAuth = false;
$module = 'contactus-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.contactus-nct.php";

$winTitle = $headTitle = 'Contact Us - ' . SITE_NM;

$metaTag = getMetaTags(array(
    "description" => $winTitle,
    "keywords"    => $headTitle,
    "author"      => AUTHOR,
));


$js_array = array(
    SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION,
    SITE_JS . 'intlTelInput-jquery.min.js',
);

$obj = new ContactUs($module, 0, $_REQUEST);
extract($_REQUEST);
if (isset($action) && isset($method) && $action == "method" && method_exists($obj, $method)) {
    $response['status'] = 1;
    $response['msg'] = 'undefined';
    $response['html'] = $obj->{$method}();

    $msgType = $_SESSION["msgType"] = disMessage(array(
        'type' => isset($response['html']['status']) ?  $response['html']['status'] : 'err',
        'var'  => isset($response['html']['message']) ?  $response['html']['message'] : 'error',
    ));
    redirectPage(SITE_CONTACTUS);
}
$pageContent = $obj -> getPageContent();

require_once DIR_TMPL . "parsing-nct.tpl.php";
?>
