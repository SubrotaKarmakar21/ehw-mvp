<?php
class Registration {
    function __construct($module = "", $reqData = array()) {
        foreach ($GLOBALS as $key => $values) {
            $this -> $key = $values;

        }
        $this->module = $module;
        $this->reqData = $reqData;
        $this->dataOnly = (isset($this->reqData['dataOnly']) && $this->reqData['dataOnly']==true)?true:false;
    }

    public function getPageContent() {
        $replace = array(
            '%terms_url%'       => $this->get_url('terms'),
            
        );
        return get_view(DIR_TMPL . $this -> module . "/" . $this -> module . ".tpl.php", $replace);
    }

    public function getselectUserType() {
        return get_view(DIR_TMPL . $this -> module . "/select-user-type.tpl.php",array());
    }

    public function get_url($string = 'privacy'){
        $result = $this->db->pdoQuery('SELECT page_slug FROM tbl_content WHERE page_title LIKE ?',array("%$string%"))->result();
        return $result['page_slug'];
    }

    public function submitSignupForm(){

        $response = array();
        $response['status'] = false;
        $response['message'] = something_went_wrong;

        $email                  = isset($_POST['email_address']) ? trim($_POST['email_address']) : null;
        $password               = isset($_POST['password']) ? $_POST['password'] : null;
        $cpass                  = isset($_POST['cpassword']) ? $_POST['cpassword'] : null;
        $first_name             = isset($_POST['first_name']) ? trim($_POST['first_name']) : null;
        $clinic_name            = isset($_POST['clinic_name']) ? trim($_POST['clinic_name']) : null;
        $last_name              = isset($_POST['last_name']) ? trim($_POST['last_name']) : null;
        $user_type              = isset($_POST['user_type']) ? $_POST['user_type'] : '';
        $phone_no               = isset($_POST['phone_no']) ? $_POST['phone_no'] : null;
        
        $phone_country_code     = isset($_POST['phone_country_code']) && $_POST['phone_country_code'] != '' ? $_POST['phone_country_code'] : '+91';
        $phone_iso2_code        = isset($_POST['phone_iso2_code']) && $_POST['phone_iso2_code'] != '' ? trim($_POST['phone_iso2_code']) : 'in';
        $date_of_birth          = isset($_POST['date_of_birth']) && $_POST['date_of_birth'] != '' ? date('Y-m-d',strtotime($_POST['date_of_birth'])) : '';
        $referral_or_community_code = isset($_POST['referral_or_community_code']) ? $_POST['referral_or_community_code'] : '';

        if ($email == null) {
            $response['message']    = err_It_sure_doesnt_seem_like_a_valid_email;
            if (!$this->dataOnly) {
                return $response;
                exit;
            } else {
                echo json_encode($response);
                exit;
            }
        } else if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $response['message']    = err_It_sure_doesnt_seem_like_a_valid_email;
            if (!$this->dataOnly) {
                return $response;
                exit;
            } else {
                echo json_encode($response);
                exit;
            }
        }

        if ($password == null) {
            $response['message']    = err_Please_enter_a_strong_password;
            if (!$this->dataOnly) {
                return $response;
                exit;
            } else {
                echo json_encode($response);
                exit;
            }
        } else if ($password != $cpass) {
            $response['message']    = err_Password_and_confirm_password_must_be_same;
            if (!$this->dataOnly) {
                return $response;
                exit;
            } else {
                echo json_encode($response);
                exit;
            }
        }

        if ($user_type == '') {
            $response['message']    = err_select_user_type;
            if (!$this->dataOnly) {
                return $response;
                exit;
            } else {
                echo json_encode($response);
                exit;
            }
        }

        if ($this->dataOnly || $user_type == 'doctor') {
            if ($first_name == '') {
                $response['message']    = 'Please enter first name.';
                if (!$this->dataOnly) {
                    return $response;
                    exit;
                } else {
                    echo json_encode($response);
                    exit;
                }
            }
            if ($last_name == '') {
                $response['message']    = 'Please enter last name.';
                if (!$this->dataOnly) {
                    return $response;
                    exit;
                } else {
                    echo json_encode($response);
                    exit;
                }
            }
        }

        if ($user_type == 'clinic') {
            if ($clinic_name == '') {
                $response['message']    = 'Please enter clinic name.';
                if (!$this->dataOnly) {
                    return $response;
                    exit;
                } else {
                    echo json_encode($response);
                    exit;
                }
            }
        }

        if ($this->dataOnly) {
            if ($date_of_birth == '') {
                $response['message']    = 'Please select date of birth.';
                echo json_encode($response);
                exit;
            }
            
        }

        $isExist = $this->db->select('tbl_users', array('id'), array('email' => $email), ' LIMIT 1');

        if ($isExist->affectedRows() > 0) {
            $response['message']    = error_email_address_already_exist;
            if (!$this->dataOnly) {
                return $response;
                exit;
            } else {
                echo json_encode($response);
                exit;
            }
        } else {
            $insertarray = array(
                "email"                         => $email,
                'user_type'                     => $user_type,
                "password"                      => md5($password),
                "first_name"                    => $first_name,
                'last_name'                     => $last_name,
                'phone_no'                      => $phone_no,
                'phone_country_code'            => $phone_country_code,
                'iso2_code'                     => $phone_iso2_code,
                'referral_or_community_code'    => $referral_or_community_code,
                "email_verified"                => 'n',
                'status'                        => 'd',
                "ip_address"                    => get_ip_address(),
                "created_date"                  => date('Y-m-d H:i:s'),
                "updated_date"                  => date('Y-m-d H:i:s'),
            );

            if ($user_type == 'clinic') {
                $insertarray['clinic_name'] = $clinic_name;
            }

            // for App
            if ($date_of_birth != '') {
                $insertarray['date_of_birth'] = $date_of_birth;
            }

            $activation_code = $insertarray['activation_code'] = md5(time());
            $activationLink = SITE_URL . 'active-account/' . $activation_code;

            $insert_id = $this->db->insert('tbl_users', $insertarray)->getLastInsertId();

            if ($insert_id > 0) {
                if ($user_type == 'doctor') {
                    insert_default_time_slot($insert_id);
                }
                

                $to        = $email;
                $arrayCont = array(
                    'greetings'      => $user_type == 'clinic' ? $clinic_name : $first_name.' '.$last_name,
                    'activationLink' => $activationLink,
                );
                $array = generateEmailTemplate('user_register', $arrayCont);

                sendEmailAddress($to, $array['subject'], $array['message']);

                $response['status'] = true;
                $response['message']    = 'Thank you for registering on'.' '.SITE_NM.'. '.'Please verify your account by clicking on the link shared over email.';
                $response['user_id'] = $insert_id;

            } else{
                $response['status'] = false;
                $response['message']    = something_went_wrong;
            }
            
            if (!$this->dataOnly) {
                return $response;
                exit;
            } else {
                echo json_encode($response);
                exit;
            }

        }

    }

    public function socialSignup($respArray){
        $responseArray = array(
            'status'            =>  'error',
            'message'           =>  something_went_wrong,
            'redirect_url'      =>  SITE_URL,
            'data'              =>  []
        );


        $identifier         = $respArray['identifier'];
        $email              = $respArray['email'];
        $firstName          = filtering($respArray['firstName'],'input');
        $lastName           = filtering($respArray['lastName'],'input');
        $img                = $respArray['picture'];
        $loginType          = $respArray['loginType'];

        $sql = "SELECT u.*  
        FROM tbl_users u 
        WHERE u.email = '" . $email . "' ";

        $get_user_details = $this->db->pdoQuery($sql)->result();
        
        if (!empty($get_user_details)) {
            if ('d' == $get_user_details['status']) {
                $responseArray['message'] = ERROR_ACCOUNT_DEACTIVATED_CONTACT_ADMIN;
                $responseArray['redirect_url'] = SITE_URL;
                return $responseArray;
            } else if ('y' == $get_user_details['is_deleted']) {
                $responseArray['message'] = MSG_DELETD_ADMIN;
                $responseArray['redirect_url'] = SITE_URL;
                return $responseArray;
            } else {
                $user_id = filtering($get_user_details['id'], 'output', 'int');
                $first_name = filtering($get_user_details['first_name']);
                $last_name = filtering($get_user_details['last_name']);
                $user_type = filtering($get_user_details['user_type']);

                if(!$this->dataOnly){
                    $_SESSION['sessUserId'] = $user_id;
                    $_SESSION['first_name'] = $first_name;
                    $_SESSION['last_name'] = $last_name;
                    $_SESSION['user_type'] = $user_type;
                }

                if($img != ''){
                    $imgNm = md5(uniqid(rand(), 1));
                    $image= $imgNm.'.JPG';
                    $content = file_get_contents($img);

                    $dir_upd = DIR_UPD_PROFILE_IMAGE;
                    if(!file_exists($dir_upd)){
                        mkdir($dir_upd,0777);
                    } 

                    $fp = fopen(DIR_UPD_PROFILE_IMAGE.$image, "w");
                    fwrite($fp, $content);
                    fclose($fp);
                    $image1 =  resizeImageSocial($dir_upd.$image, $dir_upd . 'th1_' . $image,100,100);
                    $image2 =  resizeImageSocial($dir_upd.$image, $dir_upd . 'th2_' . $image,50,50);


                    $old_image = isset($get_user_details['profile_photo']) && $get_user_details['profile_photo'] != '' ? $get_user_details['profile_photo'] : '';
                    if ($old_image != '' && file_exists(DIR_UPD_PROFILE_IMAGE.$old_image)) {

                        if (file_exists(DIR_UPD_PROFILE_IMAGE.$old_image)) {
                            unlink(DIR_UPD_PROFILE_IMAGE.$old_image);
                        }
                        if (file_exists(DIR_UPD_PROFILE_IMAGE.'th1_'.$old_image)) {
                            unlink(DIR_UPD_PROFILE_IMAGE.'th1_'.$old_image);
                        }
                        if (file_exists(DIR_UPD_PROFILE_IMAGE.'th2_'.$old_image)) {
                            unlink(DIR_UPD_PROFILE_IMAGE.'th2_'.$old_image);
                        }
                    }

                    $affected_rows = $this->db->update("tbl_users", array("profile_photo" => $image, "updated_date" => date("Y-m-d H:i:s")), array("id" => $user_id))->affectedRows();
                }

                $responseArray['status']            = 'success';
                $responseArray['message']           = LOGIN_SUCCESSFUL;
                $responseArray['redirect_url'] = $user_type == 'n' ? SITE_USERTYPE : SITE_DASHBOARD;

                if ($this->dataOnly) {
                    $responseArray['data']          = getLoginResponseForAPP($user_id);
                }
                return $responseArray;
            }
        } else {
            $user_type = isset($respArray['user_type']) && $respArray['user_type'] != '' ? $respArray['user_type'] : 'n';

            $user_details_array = array();
            $user_details_array['first_name'] = $firstName;
            $user_details_array['last_name'] = $lastName;
            $user_details_array['email'] = $email;
            $user_details_array['identifier'] = $identifier;
            $user_details_array['user_type'] = $user_type;

            $password = generatePassword();
            $user_details_array['password'] = md5($password);
            $user_details_array['status'] = 'a';
            $user_details_array['email_verified'] = 'y';
            $user_details_array['login_type'] = $loginType;
            $user_details_array['created_date'] = date("Y-m-d H:i:s");
            $user_details_array['created_date'] = date("Y-m-d H:i:s");
            $user_details_array['ip_address'] = get_ip_address();

            $user_id = $this->db->insert("tbl_users", $user_details_array)->getLastInsertId();

            if ($user_id > 0) {
                if($img != ''){
                    $imgNm = md5(uniqid(rand(), 1));
                    $image= $imgNm.'.JPG';
                    $content = file_get_contents($img);

                    $dir_upd = DIR_UPD_PROFILE_IMAGE;
                    if(!file_exists($dir_upd)){
                        mkdir($dir_upd,0777);
                    } 

                    $fp = fopen(DIR_UPD_PROFILE_IMAGE.$image, "w");
                    fwrite($fp, $content);
                    fclose($fp);
                    $image1 =  resizeImageSocial($dir_upd.$image, $dir_upd . 'th1_' . $image,100,100);
                    $image2 =  resizeImageSocial($dir_upd.$image, $dir_upd . 'th2_' . $image,200,200);
                    $image3 =  resizeImageSocial($dir_upd.$image, $dir_upd . 'th3_' . $image,335,335);

                    $affected_rows = $this->db->update("tbl_users", array("profile_photo" => $image, "updated_date" => date("Y-m-d H:i:s")), array("id" => $user_id))->affectedRows();
                }
                
                if ($user_type == 'doctor') {
                    insert_default_time_slot($user_id);
                }

                $arrayCont = array(
                    'greetings'         => $firstName.' '.$lastName,
                    'email_address'     => $email,
                    'password'          => $password,
                );
                $array = generateEmailTemplate('social_signup', $arrayCont);
                sendEmailAddress($email, $array['subject'], $array['message']);

                if(!$this->dataOnly){
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['first_name'] = $firstName;
                    $_SESSION['last_name'] = $lastName;
                    $_SESSION['user_type'] = $user_type;
                }

                $responseArray['status']    =   'success';
                $responseArray['message']   =   "Registered successfully.";
                $responseArray['redirect_url'] = $user_type == 'n' ? SITE_USERTYPE : SITE_DASHBOARD;

                if ($this->dataOnly) {
                    $responseArray['data']          = getLoginResponseForAPP($user_id);
                }
                return $responseArray;
            } else {
                $responseArray['message'] = "There seems to be some issue while updating your data to our database. Please contact site Admin.";
                $responseArray['redirect_url'] = SITE_URL;
                return $responseArray;
            }
        }
    }
}

?>