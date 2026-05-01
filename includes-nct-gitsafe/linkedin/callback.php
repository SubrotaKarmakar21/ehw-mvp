<?php

include 'Qassim_HTTP.php'; // include Qassim_HTTP() function
include 'config.php'; // include app data
include 'product_signup_curl.php';

if ($_GET['error'] == 'access_denied') {
    $_SESSION["msgType"] = 'toastr["error"]("Authentification failed. The user has canceled the authentication or the provider refused the connection.");';
    ?>
    <script type="text/javascript">window.close();</script>
    <?php
}

$code = $_GET['code'];
$method_ = 1; // method = 1, because we want POST method

//$url_ = "https://www.linkedin.com/uas/oauth2/accessToken";
$url_="https://www.linkedin.com/oauth/v2/accessToken";
$header_ = array("Content-Type: application/x-www-form-urlencoded");

$data_ = http_build_query(array(
    "client_id" => $client_id,
    "client_secret" => $client_secret,
    "redirect_uri" => $redirect_uri,
    "grant_type" => "authorization_code",
    "code" => $code
        ));

$json_ = 1; // json = 1, because we want JSON response

$get_access_token = Qassim_HTTP($method_, $url_, $header_, $data_, $json_);

$access_token = $get_access_token['access_token']; // user access token


/* Get User Info */

$method = 0; // method = 0, because we want GET method

$url = 'https://api.linkedin.com/v2/me?projection=(id,firstName,lastName,profilePicture(displayImage~:playableStreams))';

$header = array("Authorization: Bearer $access_token");

$data = 0; // data = 0, because we do not have data

$json = 1; // json = 1, because we want JSON response

$user_basic_info = Qassim_HTTP($method, $url, $header, $data, $json);



$url2 = 'https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))';

$header = array("Authorization: Bearer $access_token");

$data = 0; // data = 0, because we do not have data

$json = 1; // json = 1, because we want JSON response

$email_info = Qassim_HTTP($method, $url2, $header, $data, $json);
$user_info = array_merge($user_basic_info,$email_info);


//echo "<pre>";print_r($user_info);die;

if (!empty($user_info)) {
    $res_array =$data= array();
    $status = "invalid";
    $statusCode = 0;
    $gid = $user_info['id'];
    $data['LinkedInIdentifier'] = $user_info['id'];
    $displayName=$user_info['firstName'];
    $data['fname'] = $user_info['firstName']['localized']['en_US'];
    $data['lname'] = $user_info['lastName']['localized']['en_US'];
    $data['email'] = $user_info['elements'][0]['handle~']['emailAddress'];
    $user_name =  $data['fname'].' '.$data['lname'];
    $profilePhotoUrl = $user_info['profilePicture']['displayImage~']['elements'][3]['identifiers'][0]['identifier'];
    if($data['LinkedInIdentifier'] != '' && $data['fname'] != '' && $data['lname'] != ''){
        if($sessUserId > 0){
            $data_n = $db -> select('tbl_users', '*', array('userId' => $sessUserId));
	        $affrows = $data_n -> affectedRows();

        	if ($affrows > 0) {
        		$res = $data_n -> result();
        		if($data['email'] != $res['email']){
        			$status = "err";
        			$message = Please_verify_with_the_same_email;
        			$pageRedirect = SITE_URL."dashboard";
                    $_SESSION["msgType"] = disMessage(array('type' => $status, 'var' => $message));
                    redirectPage($pageRedirect);
        
        		}
        
        		if ($res['isActive'] == 'y') {
        			$updArr = array(
        				'lastLoginTime' => date("Y-m-d h:i:s"),
        				'ipAddress' => get_ip_address(),
        				'linkedin_verify' =>1
        			);
        			$data_p = $db -> update('tbl_users', $updArr, array('userId' => $sessUserId));
        			$status = "suc";
        			$message = Success;
        			$pageRedirect = SITE_URL."dashboard";
                    //$_SESSION["msgType"] = disMessage(array('type' => $status, 'var' => $message));
                    redirectPage($pageRedirect);
        
        		}
        	}
        }else{
            
                $img = $profilePhotoUrl.'&sz=650';
                $profileImagePath = $th1;
                $upload_dir = DIR_UPD . 'profile/';
                $image=''; 
                if($profilePhotoUrl != ''){
                    $exvalue = exif_imagetype($profilePhotoUrl);
                    $image = rand().time();
                    if((int)$exvalue == 1){
                        $image .= $ext ='.GIF';
                    }
                    else if((int)$exvalue == 2){
                        $image .= $ext ='.JPG';
                    }
                    else if((int)$exvalue == 3){
                        $image .=$ext = '.PNG';
                    }
                    else{
                        $image .= $ext ='.JPG';
                    }

                }


                file_put_contents(DIR_UPD . 'profile/' . $image, file_get_contents($profilePhotoUrl));
                
            
            
            
            
            
            $firstName  = $data['fname'];
            $lastName   = $data['lname'];
            $userName   = makeSlug($firstName . $lastName, 'tbl_users', 'userId', 'userName', 'name');
            $userName   = filter_var($userName, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
            $email      = (isset($data['email']) ? $data['email'] : '');
            $identifier = $data['LinkedInIdentifier'];
            $password   = generatePassword();
            $data       = $db->select('tbl_users', '*', array('email' => $email));
            $affrows    = $data->affectedRows();
            if ($affrows > 0) {
                $res = $data->result();
                if ($res['isActive'] == 'y' && $res['status'] == 'a') {
                    $_SESSION['userId']      = $res['userId'];
                    $_SESSION['userName']    = $res['userName'];
                    $_SESSION['profileLink'] = $userName;
                    $_SESSION['userType']    = $res['userType'];
                    $_SESSION['firstName']   = ucfirst(strtolower($res['firstName']));
                    $_SESSION['lId']         = $res['langId'];
                    //update social verification accordingly
                    $db->update('tbl_users', array('linkedin_verify' => 1), array('userId' => $res['userId']));
        
                    $pageRedirect = ($res['userType'] == 't') ? SITE_USERTYPE : SITE_DASHBOARD;
        
                    $message      = Welcome_back . " " . $_SESSION['userName'];
                    $status = "suc";
        			$_SESSION["msgType"] = disMessage(array('type' => $status, 'var' => $message));
                    redirectPage($pageRedirect);
        
                    
        
                } else {
                    $response['status']   = 0;
                    $response['msg']      = Your_account_is_no_longer_active;
                    $response['redirect'] = SITE_URL;
                    $_SESSION["msgType"]  = disMessage(array(
                        'type' => 'err',
                        'var'  => $response['msg'],
                    ));
                    $pageRedirect = SITE_URL;
        
                    $message      = Your_account_is_no_longer_active;
                    $status = "err";
        			$_SESSION["msgType"] = disMessage(array('type' => $status, 'var' => $message));
                    redirectPage($pageRedirect);
        
                }
        
            } else {
                if ($email == null || $firstName == null) {
                    $_SESSION["msgType"] = disMessage(array(
                        'type' => 'err',
                        'var'  => Something_went_wrong,
                    ));
                    redirectPage(SITE_URL . 'sign-up/');
                }
                $pass   = generatePassword();
                $insArr = array(
                    'firstName'       => $firstName,
                    'lastName'        => $lastName,
                    'userName'        => $userName,
                    'profilePhoto'    => $image,
                    'email'           => $email,
                    'isActive'        => 'y',
                    'status'          => 'a',
                    'loginType'       => 'l',
                    'activationCode'  => md5(time()),
                    'createdDate'     => date("Y-m-d h:i:s"),
                    'lastLoginTime'   => date("Y-m-d h:i:s"),
                    'profileLink'     => $userName,
                    'password'        => md5($pass),
                    'ipAddress'       => get_ip_address(),
                    'facebook_verify' =>  0,
                    'google_verify'   =>  0,
                    'linkedin_verify' =>  1,
                    'langId'          => $_SESSION['lId'],
                );
                $data = $db->insert('tbl_users', $insArr)->getLastInsertId();
                 //mailchimp code for register 2-1-2020
                 addemailtomailchimp($email);
                if ($data > 0) {
                    
                    /*nct potal newslatter signup 29-5-18*/
						 	if($lastInsertId>0){
								$arr['name']    = $firstName." ". $lastName;
								$arr['email']   =  $email;
								$arr['product'] = SITE_NM;
								$arr['url']     = SITE_URL;
								$arr['ip']      = get_ip_address();;
				            	signupNewsletter($arr);
				            }
				        	/*nct portal newslatter signup end*/
                    
                    $email_address = $email;
        
                    $to        = $email;
                    $arrayCont = array(
                        'greetings' => $firstName,
                        'USERNAME'  => $userName,
                        'PASSWORD'  => $pass,
                    );
                    $_SESSION['sendMailTo'] = issetor($data, 0);
        
                    $array = generateEmailTemplate('social_signup', $arrayCont);
                    sendEmailAddress($email_address, $array['subject'], $array['message']);
        
                    $qrySelNL                        = $db->select("tbl_newsletters", "*", array("id" => 1))->result();
                    $arrayCont                       = array();
                    $arrayCont['subject']            = $qrySelNL['newsletter_subject'];
                    $arrayCont['newsletter_content'] = $qrySelNL['newsletter_content'];
                    $arrayCont['greetings']          = $firstName;
                    $array                           = generateEmailTemplate('newsletter', $arrayCont);
                    sendEmailAddress($email_address, $arrayCont['subject'], $array['message']);
        
                    $_SESSION['userId']      = $data;
                    $_SESSION['userName']    = $userName;
                    $_SESSION['profileLink'] = $userName;
                    $_SESSION['firstName']   = ucfirst(strtolower($firstName));
        
                    $response['redirect'] = SITE_USERTYPE;
                    $response['msg']      = Welcome_to . " " . SITE_NM . " " . ucfirst(strtolower($userName));
        
                    $_SESSION["msgType"] = disMessage(array(
                        'type' => 'suc',
                        'var'  => $response['msg'],
                    ));
                    $pageRedirect = SITE_USERTYPE;
        
                    redirectPage($response['redirect']);
        
        
                } else {
                    $status = "err";
                    $message = Something_went_wrong;
                    $pageRedirect = SITE_URL;
                    $_SESSION["msgType"] = disMessage(array('type' => $status, 'var' => $message));
                    redirectPage($pageRedirect);
                }
            }
            
            
        }
    }else{
        $status = "err";
        $message = Something_went_wrong;
        $pageRedirect = SITE_URL;
        $_SESSION["msgType"] = disMessage(array('type' => $status, 'var' => $message));
        redirectPage($pageRedirect);
        
    }

}



?>