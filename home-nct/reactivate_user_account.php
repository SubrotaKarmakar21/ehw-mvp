<?php


$reqAuth = false;
require_once("../../includes-nct/config-nct.php");
require_once("class.home-nct.php");
$module = 'home-nct';

if (isset($_REQUEST["email_address"]) && !empty($_REQUEST["email_address"]) && isset($_REQUEST["reactivation_key"]) && !empty($_REQUEST["reactivation_key"])) {

    $email_address          = base64_decode($_REQUEST["email_address"]);
    $activation_code       = $_REQUEST["reactivation_key"];


    $email_valid = $db->select("tbl_users", array('id','is_deactivated','user_type'), array(
        "email" => filtering($email_address, 'input'),
        "activation_code" => filtering($activation_code, 'input')))->result();

    $user_type = isset($email_valid['user_type']) ? $email_valid['user_type'] : '';

    if (!empty($email_valid)) {
        if ('n' == $email_valid['is_deactivated']) {
            $_SESSION['msgType'] = disMessage(array('type' => 'err', 'var' => You_have_already_activated_your_account));
        } else {
            $db->update("tbl_users", array(
                "is_deactivated"     => 'n',
                'activation_code'    => ''
            ), array(
                "id" => $email_valid['id']
            ));

            if($user_type == 'patient'){
                $db->update("tbl_appointment",array('is_active'=>'y'),array("user_id"=>$email_valid['id']));
            } 

            $_SESSION['msgType'] = disMessage(array('type' => 'suc', 'var' => 'Account reactivated successfully.'));
        }
    } else {
        $_SESSION['msgType'] = disMessage(array('type' => 'err', 'var' => "This link is not active anymore. Please use the new link shared with you."));
    }

    if($sessUserId > 0){
        redirectPage(SITE_DASHBOARD);
    } else{
        redirectPage(SITE_LOGIN);
    }
} else {
    $_SESSION['msgType'] = disMessage(array('type' => 'err', 'var' => something_went_wrong));
    redirectPage(SITE_LOGIN);
}
