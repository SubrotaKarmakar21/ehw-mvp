<?php
$module = 'logout-nct';
require_once "../../includes-nct/config-nct.php";

unset($_SESSION["sessUserId"]);
unset($_SESSION["first_name"]);
unset($_SESSION["last_name"]);
unset($_SESSION["user_type"]);

redirectPage(SITE_URL);
?>
