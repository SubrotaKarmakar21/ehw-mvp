<?php

require_once("../../includes-nct/config-nct.php");

$module = "billing-nct";

$reqData = $_REQUEST;
require_once("class.billing-nct.php");

$obj = new Billing($module,0,$reqData);

$winTitle = $headTitle = 'Generate Bill - '.SITE_NM;

$pageContent = $obj->getPageContent();

require_once(DIR_TMPL."parsing-nct.tpl.php");
?>
