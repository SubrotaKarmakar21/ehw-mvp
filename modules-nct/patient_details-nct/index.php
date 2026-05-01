<?php

require_once("../../includes-nct/config-nct.php");

$module = "patient_details-nct";

$reqData = $_REQUEST;

require_once("class.patient_details-nct.php");

$obj = new PatientDetails($module,0,$reqData);

$winTitle = $headTitle = 'Patient Details - '.SITE_NM;

$pageContent = $obj->getPageContent();

require_once(DIR_TMPL."parsing-nct.tpl.php");

?>
