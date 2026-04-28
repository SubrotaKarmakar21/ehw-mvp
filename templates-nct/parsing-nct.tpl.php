<?php

$main->set("module", $module);
require_once(DIR_THEME.'theme.template.php');

/* Loading template files */

/* for head  start*/
$search = array('%METATAG%','%TITLE%');
$replace = array($metaTag,$winTitle);
$head_content=str_replace($search,$replace,$head->parse());
/* for head  end*/

/* Outputting the data to the end user */
$search = array(
	'%LANGCODE%',
	'%LANGCLASS%',
	'%HEAD%',
	'%SITE_HEADER%',
	'%BODY%',
	'%FOOTER%',
	'%MESSAGE_TYPE%'
);

$header_content = '';
if ($module != 'login-nct' && $module != 'registration-nct' && $module != 'reset-password-nct') {
	$header_content = $objHome->getHeaderContent($module);
}

$replace = array(
	'en',
	'en',
	$head_content,
	$header_content,
	$pageContent,
	$objHome->getFooterContent($module),
	$msgType
);

$page_content = str_replace($search,$replace,$page->parse());

$page_content = '<div class="dashboard-content-wrapper">' .$page_content .'</div>';
echo $page_content;
exit;
