<?php
$reqAuth = false;
$module = 'home-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.home-nct.php";

$winTitle = 'Welcome to ' . SITE_NM;
$headTitle = 'Home' . SITE_NM;
$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));
$obj = new Home();

$response['status'] = '0';
$response['msg'] = "undefined";
//Oops! something went wrong. Please try again later.


if (isset($_POST['action']) && $_POST['action'] == 'subscribe') {

	extract($_POST);
	$email = isset($email) ? $email : '';
	$date = date('Y-m-d H:i:s');

	$ip = get_ip_address();

	if ($email != '') {

		if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			
			$is_exist = getTableValue('tbl_subscribers', 'id',array('email'=>$email));
			if($is_exist){
				$response['status'] = 0;
				$response['msg'] = Seems_like_you_have_already_subscribed_to.' '.SITE_NM;
				echo json_encode($response);
				exit ;
			}
			//mailchimp code 30-12-2020
			$list_id = MAILCHIMP_LIST_ID;
			$api_key = MAILCHIMP_API_KEY;
			$data_center = substr($api_key,strpos($api_key,'-')+1);
			$url = 'https://'. $data_center .'.api.mailchimp.com/3.0/lists/'. $list_id .'/members';
			$json = json_encode([
			    'email_address' => $email,
			    'status'        => 'subscribed', //pass 'subscribed' or 'pending'
			]);
			try {
			    $ch = curl_init($url);
			    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $api_key);
			    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			    curl_setopt($ch, CURLOPT_POST, 1);
			    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			    $result = curl_exec($ch);
			    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			    curl_close($ch);
			 //   echo $status_code;exit;
			    if ($status_code == 200) {
			    	$insertarray = array(
        				"email" => $email,
        				"status" => 'a',
        				"ipAddress" => $ip,
        				"subscribed_on" => $date
        			);
        			$insert_id = $db -> insert('tbl_subscribers', $insertarray) -> getLastInsertId();
        			$response['status'] = 1;
        			$response['msg'] = Thank_you_for_subscribing_with.' ' . SITE_NM;
			    }
			   	else if ($status_code == 400) {
			        $response['status'] = 0;
        			$response['msg'] = Seems_like_you_have_already_subscribed_to.' ' . SITE_NM;
			    }
			    else {
			    	$response['status'] = 0;
        			$response['msg'] = something_went_wrong.' ' . SITE_NM;
			    }
			} catch(Exception $e) {
			    $response['status'] = 0;
    			$response['msg'] = something_went_wrong.' ' . SITE_NM;
			}
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