<?php

$reqAuth = false;

require_once("../../includes-nct/config-nct.php");
require_once("class.reset-password-nct.php");

$module = 'reset-password-nct';


if (!isset($_GET["code"])) {
    redirectPage(SITE_URL);
}

$js_array = array(SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION);


$winTitle = 'Reset password'.' - ' . SITE_NM;
$headTitle = 'Reset password'.'' . SITE_NM;
$metaTag = getMetaTags(array("description" => $winTitle, "keywords" => $headTitle, "author" => AUTHOR));

$metaTag = getMetaTags(array(
    "description" => $winTitle,
    "keywords"    => $headTitle,
    "author"      => AUTHOR,
));

$activationToken = isset($_GET["code"]) ? (filtering($_GET["code"], 'input')) : "";

$header_panel = true;
$left_panel = false;
$footer_panel = true;

$table = 'tbl_users';

$styles = array();
$scripts = array();

$objLogin = new ResetPassword();

if (isset($_POST['reset_password']) && $_SERVER['REQUEST_METHOD'] == "POST") {
    $response = $objLogin->sublitResetPassword();

    if (isset($response['status']) && $response['status']) {
        $msgType = $_SESSION['msgType'] = disMessage(array('type' => 'suc', 'var' => $response['message']));        
    } else{
        $msgType = $_SESSION['msgType'] = disMessage(array('type' => 'err', 'var' => $response['message']));
    }
    redirectPage(SITE_URL);

} else if (isset($_GET['code'])) {

    $check_if_code_is_valid = $db->select("tbl_users", "*", array("password_reset_key" => $activationToken))->result();
    if ($check_if_code_is_valid) {
        $prk_generated_on = strtotime($check_if_code_is_valid['prk_generated_on']);
        $expiry_time = $prk_generated_on + 3600;
        if (time() > $expiry_time) {
            $_SESSION['msgType'] = disMessage(array('type' => 'err', 'var' => "Reset Password link expired."));
            redirectPage(SITE_URL);
        }
    } else {
        $_SESSION['msgType'] = disMessage(array('type' => 'err', 'var' => "Invalid Link."));
        redirectPage(SITE_URL);
    }
}



$pageContent = $objLogin->getPageContent($activationToken);

require_once(DIR_TMPL . "parsing-nct.tpl.php");
