<?php
$reqAuth = false;
$module = 'registration-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.registration-nct.php";

extract($_REQUEST);

$winTitle =$headTitle= 'Sign up - ' . SITE_NM;
$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));

$obj = new Registration($module, 0, issetor($token));

if (isset($_REQUEST['submitUserTypeFrm'])) {
    $user_type = isset($_REQUEST['user_type']) ? $_REQUEST['user_type'] : 'n';

    if ($user_type != null) {
        $db->update('tbl_users', array('user_type' => $user_type), array('id' => $sessUserId));
        $_SESSION['user_type'] = $user_type;
        redirectPage(SITE_DASHBOARD);
    }
}

$pageContent = $obj -> getselectUserType();

require_once DIR_TMPL . "parsing-nct.tpl.php";
?>