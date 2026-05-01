<?php
$reqAuth = false;
$module  = 'login-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.login-nct.php";

extract($_REQUEST);
$winTitle = $headTitle = 'Login ' . SITE_NM;

$metaTag = getMetaTags(array(
    "description" => $winTitle,
    "keywords"    => $headTitle,
    "author"      => AUTHOR,
));

$js_array = array(SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION);

if ($sessUserId > 0) {
    redirectPage(SITE_URL);
} 

$obj = new Login($module, 0, $_REQUEST);


if (isset($action) && isset($method) && $action == "method" && method_exists($obj, $method)) {
    $response['status'] = 1;
    $response['msg'] = 'undefined';
    $response['html'] = $obj->{$method}();

    $msgType = $_SESSION["msgType"] = disMessage(array(
        'type' => isset($response['html']['status']) ?  $response['html']['status'] : 'err',
        'var'  => isset($response['html']['message']) ?  $response['html']['message'] : 'error',
    ));

    if (isset($response['html']['status']) && $response['html']['status']) {
        redirectPage(SITE_DASHBOARD);
    } else{
        redirectPage(SITE_LOGIN);
    }
    
}


$pageContent = $obj->getPageContent();

require_once DIR_TMPL . "parsing-nct.tpl.php";
