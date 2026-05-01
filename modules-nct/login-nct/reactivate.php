<?php
$reqAuth = false;
$module  = 'login-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.login-nct.php";

extract($_REQUEST);

$winTitle = $headTitle = Reactivate_your_account .' - ' . SITE_NM;

$metaTag = getMetaTags(array(
    "description" => $winTitle,
    "keywords"    => $headTitle,
    "author"      => AUTHOR,
));

$js_array = array(SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION);
$obj = new Login($module, 0, issetor($token));


if ($sessUserId > 0) {
    $msgType = $_SESSION['msgType'] = disMessage(array(
        "type" => "suc",
        "var"  => You_are_already_logged_in,
    ));
    redirectPage(SITE_URL);
} else if (isset($_POST['submitReactivateForm'])) {
    extract($_POST);

    $response = $obj->resendActivationLink();
    if (isset($response['status']) && $response['status']) {
        $msgType = $_SESSION['msgType'] = disMessage(array('type' => 'suc', 'var' => $response['message']));
        redirectPage(SITE_URL);
    } else{
        $msgType = $_SESSION['msgType'] = disMessage(array('type' => 'err', 'var' => $response['message']));
        redirectPage(SITE_REACTIVATE);
    }
}


$pageContent = $obj->getReactivatePage();

require_once DIR_TMPL . "parsing-nct.tpl.php";
