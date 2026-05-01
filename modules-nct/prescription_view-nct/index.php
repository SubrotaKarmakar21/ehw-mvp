<?php

require_once("../../includes-nct/config-nct.php");

$module = "prescription_view-nct";

$reqData = $_REQUEST;

require_once("class.prescription_view-nct.php");

$obj = new PrescriptionView($module, $reqData);

$winTitle = $headTitle = 'Prescription Preview - '.SITE_NM;

$pageContent = $obj->getPageContent();

require_once(DIR_TMPL."parsing-nct.tpl.php");
?>
