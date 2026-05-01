<?php
$reqAuth = false;
$module = 'login-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.login-nct.php";

$obj = new Home();

$response['status'] = '0';
$response['msg'] = "undefined";
//Oops! something went wrong. Please try again later.
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'socialLogin') {
    extract($_REQUEST);
  
    $email = isset($email) ? $email : '';
    $date = date('Y-m-d H:i:s');

    $ip = get_ip_address();

    if ($email != '') {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            
            $is_exist = getTableValue('tbl_subscribers', 'id',array('email'=>$email));
            if($is_exist){
                $response['status'] = 0;
                $response['msg'] = Seems_like_you_have_already_subscribed_to." ".SITE_NM;
                echo json_encode($response);
                exit ;
            }
            $insertarray = array(
                "email" => $email,
                "status" => 'a',
                "ipAddress" => $ip,
                "subscribed_on" => $date
            );
            $insert_id = $db -> insert('tbl_subscribers', $insertarray) -> getLastInsertId();
            $response['status'] = 1;
            $response['msg'] = Thank_you_for_subscribing_with." " . SITE_NM;
        }
        else {
            $response['status'] = 0;
            $response['msg'] = It_sure_doesnt_seem_like_an_email_to_me_You_can_check_that_email_again;
        }

    }
    else {
        $response['status'] = 0;
        $response['msg'] = Seems_like_you_forgot_to_enter_email;

    }

}

echo json_encode($response);
exit ;
?>