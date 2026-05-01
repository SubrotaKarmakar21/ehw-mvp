<?php

require_once("../../includes-nct/config-nct.php");

$module = "advice-nct";

$reqData = $_REQUEST;

require_once("class.advice-nct.php");

$obj = new Advice($module, $reqData);

$winTitle = $headTitle = 'Advice - '.SITE_NM;

$pageContent = $obj->getPageContent();

require_once(DIR_TMPL."parsing-nct.tpl.php");
?>
