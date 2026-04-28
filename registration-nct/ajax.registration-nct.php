<?php
$reqAuth = false;
$module  = 'registration-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.registration-nct.php";

$winTitle = $headTitle =  'Signup - ' . SITE_NM;

$metaTag   = getMetaTags(array(
    "description" => $winTitle,
    "keywords"    => $headTitle,
    "author"      => AUTHOR,
));
$obj = new Registration($module, 0, issetor($token));

$response['status'] = '0';
$response['msg']    = "undefined";


if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'chk_email' && isset($_REQUEST['email_address']) && $_REQUEST['email_address'] != '') {
    $email_address = filtering($_REQUEST['email_address']);
    $email_valid = $db->select("tbl_users", "*", array("email" => $email_address))->result();
    if ($email_valid) {
        echo 'false';
        exit;
    } else {
        echo 'true';
        exit;
    }
} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'activation') {
    extract($_REQUEST);
    $activation_code = isset($activationcode) ? $activationcode : null;

    if ($activation_code != null) {
        $selUser = $db->pdoQuery('SELECT id,status,email_verified FROM tbl_users WHERE activation_code = ? LIMIT 1', array($activation_code));

        if ($selUser->affectedRows() > 0) {
            $fetchUser = $selUser->result();
            
            if ($fetchUser['email_verified'] == 'y' && $fetchUser['status'] == 'd') {
                $_SESSION["msgType"] = disMessage(array(
                    'type' => 'err',
                    'var'  => ERROR_ACCOUNT_DEACTIVATED_CONTACT_ADMIN,
                ));
                redirectPage(SITE_REGISTER);
            } else if ($fetchUser['email_verified'] == 'y' && $fetchUser['status'] == 'a') {
                $_SESSION["msgType"] = disMessage(array(
                    'type' => 'err',
                    'var'  => You_have_already_activated_your_account,
                ));
                redirectPage(SITE_REGISTER);
            } else {

                $id = $fetchUser['id'];

                $db->update('tbl_users', array(
                    'email_verified'     => 'y',
                    'status'                => 'a',
                ), array("id" => $id));
                $_SESSION["msgType"] = disMessage(array(
                    'type' => 'suc',
                    'var'  => email_Verification_completed,
                ));
                redirectPage(SITE_LOGIN);
            }
        } else {
            $_SESSION["msgType"] = disMessage(array(
                'type' => 'err',
                'var'  => Verification_failed,
            ));
            redirectPage(SITE_URL);

        }
    }
    $_SESSION["msgType"] = disMessage(array(
        'type' => 'err',
        'var'  => something_went_wrong,
    ));
    redirectPage(SITE_URL);
} 

echo json_encode($response);
exit;
