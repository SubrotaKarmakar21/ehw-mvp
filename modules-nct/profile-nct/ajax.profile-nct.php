<?php
$reqAuth = true;
$module = 'profile-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.profile-nct.php";

$winTitle = 'Profile ' . SITE_NM;
$headTitle = 'Profile' . SITE_NM;
$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));
$obj = new Profile();

$response['status'] = '0';
$response['msg'] = "undefined";
//Oops! something went wrong. Please try again later.




echo json_encode($response);
exit ;
?>