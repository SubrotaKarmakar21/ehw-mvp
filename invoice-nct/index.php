<?php

require_once("../../includes-nct/config-nct.php");

$module = "invoice-nct";

$bill_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

require_once("class.invoice-nct.php");

$obj = new Invoice($module,$bill_id);

if(isset($_GET['download'])){
    $obj->downloadPDF();
    exit;
}

$winTitle = $headTitle = "Invoice - ".SITE_NM;

$pageContent = $obj->getPageContent();

require_once(DIR_TMPL."parsing-nct.tpl.php");
