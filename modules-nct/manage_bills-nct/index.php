<?php

require_once("../../includes-nct/config-nct.php");

$module = "manage_bills-nct";

require_once("class.manage_bills-nct.php");

$obj = new ManageBills($module);

/* AJAX SEARCH */
if(isset($_GET['ajaxSearch'])){
    $obj->ajaxSearchBills();
    exit;
}

$winTitle = $headTitle = "Billing History - ".SITE_NM;

$pageContent = $obj->getPageContent();

require_once(DIR_TMPL."parsing-nct.tpl.php");
