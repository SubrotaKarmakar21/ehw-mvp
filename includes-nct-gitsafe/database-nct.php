<?php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

if($protocol == 'https://'){
    define('ENABLE_SSL',true);
}else{
    define('ENABLE_SSL',false);
}

if($_SERVER["SERVER_NAME"] == 'localhost') {
    define("ENVIRONMENT", "d");//d- development, p- production
    define("DB_HOST", "YOUR_DB_HOST");
    define("DB_USER", "YOUR_DB_USER");
    define("DB_PASS", "YOUR_DB_PASSWORD");
    define("DB_NAME", "YOUR_DB_NAME");
    define("PROJECT_DIRECTORY_NAME", "");
    define('SITE_URL',$protocol.$_SERVER["SERVER_NAME"].'/elevate_health/');
    define('ADMIN_URL', SITE_URL . 'admin-nct/');
    define('DIR_URL', $_SERVER["DOCUMENT_ROOT"] . '/elevate_health/');
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
}
else {
    define("ENVIRONMENT", "d");
    define("DB_HOST", "localhost");
    define("DB_USER", "YOUR_DB_USER");
    define("DB_PASS", "YOUR_DB_PASSWORD");
    define("DB_NAME", "YOUR_DB_NAME");
    define("PROJECT_DIRECTORY_NAME", "");
    define('SITE_URL', $protocol . $_SERVER["SERVER_NAME"] . '/');
    define('ADMIN_URL', SITE_URL . 'admin-nct/');
    define('DIR_URL', $_SERVER["DOCUMENT_ROOT"] . '/');
    // error_reporting(0);
    error_reporting(E_ALL || E_STRICT);
}

?>
