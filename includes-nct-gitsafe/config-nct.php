<?php
/*
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
*/

ob_start();
session_name('NCT');
session_start();
set_time_limit(0);
if(!session_id()){
    session_set_cookie_params(3600);
}
date_default_timezone_set('Asia/Kolkata');


global $db, $helper, $fields, $module, $adminUserId, $sessUserId, $objHome, $main_temp, $breadcrumb, $Permission, $memberId;
global $head, $header, $left, $right, $footer, $content, $title, $resend_email_verification_popup;
global $css_array, $js_array, $js_variables;
global $dataOnly;

$include_sharing_js = false;

$header_panel = true;
$footer_panel = true;
$styles       = array();
$scripts      = array();

$reqAuth 	 = isset($reqAuth) ? $reqAuth : false;
$adminUserId 	 = (isset($_SESSION["adminUserId"]) && $_SESSION["adminUserId"] > 0 ? (int) $_SESSION["adminUserId"] : 0);

$sessUserId      = (isset($_SESSION["sessUserId"]) && (int) $_SESSION["sessUserId"] > 0 ? (int) $_SESSION["sessUserId"] : 0);
$sessFirstName   = (isset($_SESSION["first_name"]) && $_SESSION["first_name"] != '' ? $_SESSION["first_name"] : null);
$sessLastName    = (isset($_SESSION["last_name"]) && $_SESSION["last_name"] != '' ? $_SESSION["last_name"] : null);
$sessUserType    = (isset($_SESSION["user_type"]) && $_SESSION["user_type"] != '' ? $_SESSION["user_type"] : null);

$toastr_message = isset($_SESSION["toastr_message"]) ? $_SESSION["toastr_message"] : null;
unset($_SESSION['toastr_message']);

require_once 'database-nct.php';
define ('ALLOW_ALL_EXTERNAL_SITES', TRUE);
require_once 'functions-nct/class.pdohelper.php';
// require_once 'functions-nct/class.pdowrapper.php';
require_once 'functions-nct/class.pdowrapper-child.php';
require_once 'mime_type_lib.php';
$dbConfig = array(
    "host"     => DB_HOST,
    "dbname"   => DB_NAME,
    "username" => DB_USER,
    "password" => DB_PASS,
);
$db     = new PdoWrapper($dbConfig);
$helper = new PDOHelper();

define("WHATSAPP_TOKEN","YOUR_WHATSAPP_TOKEN");
define("WHATSAPP_PHONE_ID","YOUR_WHATSAPP_PHONE_ID");

define('GOOGLE_RECAPTCHA_SITE_KEY','YOUR_GOOGLE_RECAPTCHA SITE_KEY');
define('GOOGLE_RECAPTCHA_SECRET_KEY','YOUR_GOOGLE_RECAPTCHA_SECRET_KEY');

define('OPENAI_API_KEY','YOUR_OPEN_AI_KEY');

/*if (ENVIRONMENT == 'p') {
    $db->setErrorLog(false);
} else {
    $db->setErrorLog(true);
}*/

require_once 'constant-nct.php';
require_once 'functions-nct/functions-nct.php';
require_once DIR_FUN . 'validation.class.php';

require_once DIR_INC . 'FirePHPCore/FirePHP.class.php';
global $fb;
$fb = FirePHP::getInstance(true);

curPageURL();
curPageName();
checkIfIsActive();
Authentication($reqAuth, true);

if($sessUserId > 0 && (strpos($_SERVER['REQUEST_URI'], "reactivate-user-account") === false) && (strpos($_SERVER['REQUEST_URI'], "logout") === false) && (strpos($_SERVER['REQUEST_URI'], "content") === false) && (strpos($_SERVER['REQUEST_URI'], "contactus") === false) && domain_details('dir') != 'admin-nct'){
    $is_deactivated = getTableValue('tbl_users','is_deactivated',array('id'=>$sessUserId));
    if(isset($is_deactivated) && $is_deactivated=='y'){
        $msgType = $_SESSION["msgType"] = disMessage(array('type' => 'err', 'var' => "Please reactivate your account to continue! "));
        redirectPage(SITE_REACTIVE_ACCOUNT);
    }
}

require "class.main_template-nct.php";

$main    = new MainTemplater();
$msgType = isset($_SESSION["msgType"]) ? $_SESSION["msgType"] : null;
unset($_SESSION['msgType']);

if (domain_details('dir') == 'admin-nct') {
    $left_panel = true;
    require_once DIR_ADM_INC . 'functions-nct/admin-function-nct.php';
    require_once DIR_ADM_MOD . 'home-nct/class.home-nct.php';
    $objHome = new Home($module, 0);

} else {

    require_once DIR_INC . 'language-nct/1.php';
    $_SESSION["lId"] = 1;

    require_once DIR_MOD . 'home-nct/class.home-nct.php';
    //require_once DIR_INC . "paypal-nct/paypal_class.php";
    $objHome = new Home("home-nct");
}

$objPost = new stdClass();

$description = SITE_NM;
$keywords    = "";
