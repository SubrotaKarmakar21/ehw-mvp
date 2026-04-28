<?php
$reqAuth = false;
$module = 'my_services-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.my_services-nct.php";

/* AJAX LIVE SEARCH */
if(isset($_GET['ajaxSearch'])){
    $obj = new MyServices($module, $_REQUEST);
    $obj->ajaxSearchServices();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $obj = new MyServices($module, $_REQUEST);

    // Update service
    if (isset($_POST['update_service']) && isset($_POST['edit_id'])) {

        $obj->updateService($_POST['edit_id']);

    } else {
        // Add new service
        $obj->addService();
    }

    header("Location: " . SITE_URL . "my-services");
    exit;
}

extract($_REQUEST);
$winTitle = $headTitle = 'My Services - ' . SITE_NM;

$metaTag = getMetaTags(array(
    "description" => $winTitle,
    "keywords" => $headTitle,
    "author" => AUTHOR
));

if ($sessUserId <= 0) {
    $msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
    redirectPage(SITE_URL);
}

// Handle ADD page request
if (isset($_GET['add'])) {

    $obj = new MyServices($module, $_REQUEST);

    $pageContent = $obj->renderForm();

    require_once DIR_TMPL . "parsing-nct.tpl.php";
    exit;
}

// Handle RESTORE
if (isset($_GET['restore']) && is_numeric($_GET['restore'])) {

    $obj = new MyServices($module, $_REQUEST);
    $obj->restoreService($_GET['restore']);

    header("Location: " . SITE_URL . "my-services?trash=1");
    exit;
}

// Handle DELETE
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {

    $obj = new MyServices($module, $_REQUEST);
    $obj->deleteService($_GET['delete']);

    header("Location: " . SITE_URL . "my-services");
    exit;
}

// Handle EDIT page request
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {

    $obj = new MyServices($module, $_REQUEST);
    $pageContent = $obj->getEditForm($_GET['edit']);

    require_once DIR_TMPL . "parsing-nct.tpl.php";
    exit;
}

$css_array = array(
    SITE_PLUGIN. "jquery-confirm/jquery-confirm.min.css",
);

$js_array = array(
    SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION,
    SITE_PLUGIN. "jquery-confirm/jquery-confirm.min.js",
);

$obj = new MyServices($module, $_REQUEST);

$pageContent = $obj->getPageContent();

require_once DIR_TMPL . "parsing-nct.tpl.php";
?>
