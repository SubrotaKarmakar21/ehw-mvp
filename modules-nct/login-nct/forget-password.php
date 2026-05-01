<?php
$reqAuth = false;
$module = 'login-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.login-nct.php";

extract($_REQUEST);

$winTitle = 'Forgot password ' . SITE_NM;

$headTitle = 'Forgot password ' . SITE_NM;
$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));

$js_array = array(SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION);


$obj = new Login($module, 0, issetor($token));

if ($sessUserId > 0) {
	$msgType = $_SESSION['msgType'] = disMessage(array(
		"type" => "suc",
		"var" => You_are_already_logged_in
	));
	redirectPage(SITE_URL);
}
else if (isset($_POST['submitForgetForm'])) {
	extract($_POST);

	$response = $obj->submitForgotPassword();
	if (isset($response['status']) && $response['status']) {
		$msgType = $_SESSION['msgType'] = disMessage(array('type' => 'suc', 'var' => $response['message']));
		redirectPage(SITE_LOGIN);
	} else{
		$msgType = $_SESSION['msgType'] = disMessage(array('type' => 'err', 'var' => $response['message']));
		redirectPage(SITE_FORGOT);
	}
}


$pageContent = $obj -> getForgetPage();

require_once DIR_TMPL . "parsing-nct.tpl.php";
?>