<?php

$sqlSettings = $db -> select("tbl_site_settings", array(
    "constant",
    "value"
)) -> results();
foreach ($sqlSettings as $conskey => $consval) {
	if($consval['constant'] == 'MAX_FILE_SIZE')
		define($consval["constant"], 1024 * 1024 * (int)$consval["value"]);
	else
    	define($consval["constant"], $consval["value"]);
}



define("SALT_FOR_ENCRYPTION", "NCrypted");

$host = $_SERVER['HTTP_HOST'];
$request_uri = $_SERVER['REQUEST_URI'];
$canonical_url = "http://" . $host . $request_uri;
$_SESSION['DIR_URL'] = DIR_URL;
define('CANONICAL_URL', $canonical_url);

//Nlance App Server Key
defined('SERVER_KEY') or define('SERVER_KEY', 'AIzaSyAuDo32eQ1uvgzE1vixUFdaoUwdlGAGR10');

define('YEAR', date("Y"));

define('MEND_SIGN', '<sup><font color="#FF0000">*</font></sup>');

define("KEY", 'vR4o]M3p`3~^].%L9');

define('AUTHOR', 'Elevate Health World');
define('ADMIN_NM', 'Administrator');
define('REGARDS', SITE_NM);
define('CURRENT_YEAR', date('Y'));

define('LIMIT', 8);
//limit for infinite scroll

define("SITE_INC", SITE_URL . "includes-nct/");
define("SITE_LNG", SITE_INC . "language-nct/");

define("DIR_INC", DIR_URL . "includes-nct/");
define("SITE_MOD", DIR_URL . "modules-nct/");
define("DIR_MOD", DIR_URL . "modules-nct/");

define("SITE_UPD", SITE_URL . "upload-nct/");
define("DIR_UPD", DIR_URL . "upload-nct/");

define('SITE_THEME', SITE_URL . 'themes-nct/');
define("DIR_THEME", DIR_URL . "themes-nct/");
define('SITE_CSS', SITE_THEME . 'css-nct/');
define("SITE_JS", SITE_THEME . "javascript-nct/");
define("SITE_PLUGIN", SITE_JS . "plugins-nct/");
define("DIR_CSS", DIR_THEME . "css-nct/");
define('SITE_IMG', SITE_THEME . 'images-nct/');
define("DIR_IMG", DIR_THEME . "images-nct/");
define("DIR_FONT", DIR_INC . "fonts-nct/");
define("SITE_THUMB", SITE_URL . "thumb/");

/* Start Paypal Settings */
define('PAYPAL_CURRENCY_CODE', 'USD');
define('DEFAULT_CURRENCY_CODE', 'INR');
define('CURRENCY_SYMBOL', '₹');

define('RETURN_URL', SITE_URL . 'payment_successful');
define('CANCEL_RETURN_URL', SITE_URL . 'transaction_cancelled');
define('NOTIFY_URL', SITE_URL . 'notify/');
/* End Paypal Settings */

define('SITE_THEME_FONTS', SITE_URL . 'fonts/');
define('SITE_THEME_IMG', SITE_THEME . 'images-nct/');
define('SITE_THEME_JS', SITE_URL . 'js/');

define('DIR_THEME_IMG', DIR_THEME . 'images-nct/');

define('SITE_LOGO_URL', SITE_IMG.SITE_LOGO);
define("SITE_LOGO_FAVICON", SITE_IMG.SITE_FAVICON);
define("DIR_LOGO_FAVICON", DIR_IMG.SITE_FAVICON);

define("DIR_FUN", DIR_URL . "includes-nct/functions-nct/");
define("DIR_TMPL", DIR_URL . "templates-nct/");
define("DIR_CACHE", DIR_UPD . "cache-nct/");

define('USER_DEFAULT_AVATAR', 'default_profile_pic.png');
define('PRODUCT_DEFAULT_IMAGE', SITE_THEME_IMG . 'product-default-image.jpg');
define("DIR_UPD_CLINIC_BANNER", DIR_UPD . "clinicBanner-nct/");
define("SITE_UPD_HOMEIMG", SITE_URL."upload-nct/slider/");
define("DIR_UPD_HOMEIMG", DIR_URL."upload-nct/slider/");

/* Start ADMIN SIDE */
define("SITE_ADMIN_URL", SITE_URL . "admin-nct/");
define("SITE_ADM_CSS", ADMIN_URL . "themes-nct/css-nct/");
define("SITE_ADM_IMG", ADMIN_URL . "themes-nct/images-nct/");
define("SITE_ADM_INC", ADMIN_URL . "includes-nct/");
define("SITE_ADM_MOD", ADMIN_URL . "modules-nct/");
define("SITE_ADM_JS", ADMIN_URL . "includes-nct/javascript-nct/");
define("SITE_ADM_UPD", ADMIN_URL . "upload-nct/");
define("SITE_JAVASCRIPT", SITE_URL . "includes-nct/javascript-nct/");
define("SITE_ADM_PLUGIN", ADMIN_URL . "includes-nct/plugins-nct/");
define("SITE_ADM_JAVA", SITE_ADMIN_URL . "includes-nct/javascript-nct/");

define("DIR_ADMIN_URL", DIR_URL . "admin-nct/");
define("DIR_ADMIN_THEME", DIR_ADMIN_URL . "themes-nct/");
define("DIR_ADMIN_TMPL", DIR_ADMIN_URL . "templates-nct/");
define("DIR_ADM_INC", DIR_ADMIN_URL . "includes-nct/");
define("DIR_ADM_MOD", DIR_ADMIN_URL . "modules-nct/");
define("DIR_ADM_PLUGIN", DIR_ADM_INC . "plugins-nct/");
/* End ADMIN SIDE */

define("NMRF", '<div class="no-results">No more results found.</div>');
define("LOADER", '<img alt="Loading.." src=" ' . SITE_THEME_IMG . 'ajax-loader-transparent.gif" class="lazy-loader" />');


define('FILE_UPDATED_VERSION', time());
define("SITE_ADMIN_THEME", SITE_ADMIN_URL."themes-nct/");

define('CROPER_IMAGE_VALIDATE_EXT', array('jpg', 'png', 'jpeg','JPEG','JPG','PNG','webp') );
define('CROPER_IMAGE_VALIDATE_MSG', 'Please select only png, jpg, jpeg, webp file.' );

//For temp files
define("DIR_UPD_TEMP_FILES", DIR_UPD . "temp_files/");
define("SITE_UPD_TEMP_FILES", SITE_UPD . "temp_files/");

// define('SITE_UPD_BANNER', SITE_UPD . 'home-content-nct/');
// define("DIR_UPD_BANNER", DIR_UPD . "home-content-nct/");

// define('SITE_UPD_HOW_IT_WORKS', SITE_UPD . 'how-it-works-nct/');
// define("DIR_UPD_HOW_IT_WORKS", DIR_UPD . "how-it-works-nct/");

define('SITE_UPD_PROFILE_IMAGE', SITE_UPD . 'profilePhoto-nct/');
define("DIR_UPD_PROFILE_IMAGE", DIR_UPD . "profilePhoto-nct/");

define("DIR_UPD_TYPE_OF_DOCTORS", DIR_UPD . "type_of_doctors-nct/");
define("SITE_UPD_TYPE_OF_DOCTORS", SITE_UPD . "type_of_doctors-nct/");

define("phone_iso2_code",'in');
define("phone_contact_code",'+91');

define("MYSQL_DISTANCE_CONSTANT",'6371'); // for miles 3959
define("PRO_DELIVERY_RADIUOS", '20');

/*redirect url slugs*/
define('SITE_CONTACTUS',SITE_URL.'contactus');
define('SITE_CMS',SITE_URL.'content/');
define('SITE_LOGIN',SITE_URL.'login');
define('SITE_REGISTER',SITE_URL.'signup');
define('SITE_FORGOT',SITE_URL.'forgot-password');
define('SITE_REACTIVATE',SITE_URL.'reactivate');
define('SITE_PROFILE',SITE_URL.'profile');

define('SITE_EDIT_PROFILE',SITE_URL.'edit-profile');
define('SITE_LOGOUT',SITE_URL.'logout');
define('SITE_ADD_PATIENTS',SITE_URL.'add-patient');
define('SITE_EDIT_PATIENTS',SITE_URL.'edit-patient/');
define('SITE_BILLING', SITE_URL.'billing');
define('SITE_ADD_APPOINTMENT',SITE_URL.'add-appointment');
define('SITE_ADD_DOCTORS',SITE_URL.'add-doctor');
define('SITE_EDIT_DOCTORS',SITE_URL.'edit-doctor/');
define('SITE_MY_DOCTORS',SITE_URL.'my-doctors');
define('SITE_MY_SERVICES', SITE_URL.'my-services');
define('SITE_MY_PATIENTS',SITE_URL.'my-patients');

define('SITE_DASHBOARD',SITE_URL.'dashboard');
define('SITE_USERTYPE',SITE_URL.'select-user-type');
define('SITE_ACCOUNT_SETTINGS',SITE_URL.'account-settings');
define('SITE_REACTIVE_ACCOUNT',SITE_URL.'reactivate-user-account');

/*redirect url slugs*/

?>
