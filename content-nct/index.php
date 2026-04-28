<?php

$reqAuth = false;
$module = 'content-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.content-nct.php";

$slug = isset($_GET['pageSlug']) ? $_GET['pageSlug'] : '';

$objContent = new Content($slug,$module);

$getStaticPageContent = $objContent->getStaticPageContent($slug);

if($slug == '' || $getStaticPageContent['id']==0){
	$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'invalidLink'));
	redirectPage(SITE_URL);
}

$url = SITE_CMS.'/'.$slug;

$winTitle = $getStaticPageContent['page_name'].' - '.SITE_NM;
$headTitle = $getStaticPageContent['meta_keyword'];

$metaTag = getMetaTags(array(
    "description" => $getStaticPageContent['meta_desc'],
    "keywords"    => $headTitle,
    "author"      => AUTHOR,
));

$pageContent = $objContent->getPageContent();

require_once DIR_TMPL . "parsing-nct.tpl.php";
?>
