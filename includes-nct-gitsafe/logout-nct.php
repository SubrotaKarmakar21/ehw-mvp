<?php
require_once("../includes/config.php");	
if(isset($_SESSION["sessUserId"]) && $_SESSION["sessUserId"] > 0) {
	unset($_SESSION["adminUserId"]);
	unset($_SESSION["sessUserId"]);
	unset($_SESSION["sesspUserId"]);
	//unset($_SESSION["sessUserType"]);
	//unset($_SESSION["portalType"]);
	if(isset($_SESSION['logout']) && $_SESSION['logout']!="")
	{
	  redirectPage($_SESSION['logout']);		
	}
	session_destroy();
	redirectPage(SITE_URL);
	$_SESSION["msgType"] = array('from'=>'admin','type'=>'suc','var'=>'succLogout');
	
}
redirectPage(SITE_URL);
?>
