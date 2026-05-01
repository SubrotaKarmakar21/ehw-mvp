<?php

session_start();

// Script By Qassim Hassan, wp-time.com

if( isset($_SESSION['user_info']) ){ // check if user is logged in
	header("location: index.php"); // redirect user to index page
	return false;
}

include 'config.php'; // include app info

$_SESSION['login'] = 1;


header("location: https://www.linkedin.com/uas/oauth2/authorization?response_type=code&client_id=$client_id&redirect_uri=$redirect_uri&scope=r_liteprofile%20r_emailaddress&state=CSRF"); // redirect user to oauth page


?>