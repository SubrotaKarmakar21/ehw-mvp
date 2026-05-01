<?php
$reqAuth = false;
$module = 'contactus-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.contactus-nct.php";

$winTitle = 'Welcome to ' . SITE_NM;
$headTitle = 'contactus' . SITE_NM;
$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));
$obj = new ContactUs();

$response['status'] = '0';
$response['msg'] = "undefined";
//Oops! something went wrong. Please try again later.


echo json_encode($response);
exit ;
?>