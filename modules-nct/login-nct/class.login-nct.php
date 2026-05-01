<?php
class Login {
    function __construct($module = "", $id = 0, $reqData = array()) {
        foreach ($GLOBALS as $key => $values) {
            $this -> $key = $values;

        }
        $this -> module = $module;
        $this -> id = $id;
        $this->reqData = $reqData;

        // for web service
        $this->dataOnly   = (isset($this->reqData['dataOnly']) && $this->reqData['dataOnly'] == true) ? true : false;
        $this->sessUserId = (isset($reqData['userId']) && $reqData['userId'] >0)?$reqData['userId']:$this -> sessUserId;

    }

    public function getPageContent() {

        $pureSiteNm = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', SITE_NM)));
        // pri($_COOKIE);
        $replace = array(
            '%email%' => (isset($_COOKIE["email"]) && $_COOKIE["email"] != '') ? $_COOKIE["email"] : NULL,
            '%password%' => (isset($_COOKIE["password"]) && $_COOKIE["password"] != '') ? $_COOKIE["password"] : NULL,
            '%remember_me%' => (isset($_COOKIE["rememberme"]) && $_COOKIE["rememberme"] == 'y') ? 'checked="checked"' : NULL,
        );
        return get_view(DIR_TMPL . $this -> module . "/" . $this -> module . ".tpl.php", $replace);
    }

    public function getForgetPage() {
        return get_view(DIR_TMPL . $this -> module . "/forget-nct.tpl.php",array(
            '%tokenValue%' => setFormToken()));
    }

    public function getReactivatePage() {
        return get_view(DIR_TMPL . "login-nct/reactivate-nct.tpl.php",array(
            '%tokenValue%' => setFormToken()));
    }

    public function submitForgotPassword(){
        extract($_POST);

        $response = array();
        $response['status'] = false;
        $response['message'] = 'Somethinh Went Wrong!';
        if (isset($email) && $email != NULL) {
            $selQuery     = $this->db->pdoQuery('SELECT id,first_name,last_name,email,status,email_verified FROM tbl_users WHERE email = ? AND user_type IN ("doctor","clinic") ', array(
                $email,
            ));

            if ($selQuery -> affectedRows() >= 1) {
                $result = $selQuery -> result();
                if ($result != false) {
                    extract($result);

                    if ($email_verified == "n") {
                        $response['message'] = MSG_PLS_ACTIVATE_ACC;
                        return $response;
                    } else if ($status == "d") {
                        $response['message'] = ERROR_ACCOUNT_DEACTIVATED_CONTACT_ADMIN;
                        return $response;
                    } else {
                        $new_pass = genrateRandom();                    

                        $password_reset_key = md5($email . time());
                        
                        $this->db->update("tbl_users", array(
                            "password_reset_key"    => $password_reset_key,
                            "prk_generated_on"      => date("Y-m-d H:i:s")
                        ), array("id" => $result['id']));

                        $password_reset_link = SITE_URL . 'resetpassword/' . $password_reset_key;
                        $arrayCont = array();
                        $arrayCont['greetings'] = $result['first_name'].' '.$result['last_name'];
                        $arrayCont['password_reset_link'] = "<a href=" . $password_reset_link . " title='Reset Password'>Reset Password</a><br>";

                        $array = generateEmailTemplate('forgot_password', $arrayCont);

                        sendEmailAddress($email, $array['subject'], $array['message']);

                        $response['status'] = true;
                        $response['message'] = We_have_sent_you_password_Please_check_your_registered_email;
                        return $response;
                    }
                }
                else {
                    $response['message'] = MSG_INVALID_USER;
                    return $response;
                }
            }
            else {
                $response['message'] = MSG_INVALID_USER;
                return $response;
            }
        }
        else {
            $response['message'] = MSG_INVALID_USER;
            return $response;
        }
    }

    public function submitLoginForm(){

        $response['status'] = false;
        $response['message']    = something_went_wrong;

        $email              = isset($_POST['email_address']) ? trim($_POST['email_address']) : '';
        $password           = isset($_POST['password']) ? trim($_POST['password']) : '';
        $remember_me        = isset($_POST['remember_me']) ? $_POST['remember_me'] : '';

        if (isset($email) && isset($password)) {

            $entered_pass = $password;
            $selQuery = $this->db->pdoQuery(
		"SELECT * FROM tbl_users
		WHERE email = '".$email."'
		AND password = '".md5($password)."'
		AND user_type IN ('doctor','clinic')"
	    );

	    $countUsr = ($selQuery) ? $selQuery->affectedRows() : 0;
	    $result   = ($selQuery) ? $selQuery->result() : false;

            if ($countUsr >= 1) {

                if ($result != false) {
                    extract($result);
                    if (isset($remember_me) && $remember_me == 'y') {
                        setcookie('email', $email, time() + 3600 * 24 * 30, '/', '', isset($_SERVER["HTTPS"]), true);
                        setcookie('password', $entered_pass, time() + 3600 * 24 * 30, '/', '', isset($_SERVER["HTTPS"]), true);
                        setcookie('rememberme', 'y', time() + 3600 * 24 * 30, '/', '', isset($_SERVER["HTTPS"]), true);
                    } else {
                        setcookie('email', '', time() - 3600, '/', '', isset($_SERVER["HTTPS"]), true);
                        setcookie('password', '', time() - 3600, '/', '', isset($_SERVER["HTTPS"]), true);
                        setcookie('rememberme', '', time() - 3600, '/', '', isset($_SERVER["HTTPS"]), true);
                    }
                    if ($email_verified == "n") {
                        $response['message']    = MSG_PLS_ACTIVATE_ACC;
                        if (!$this->dataOnly) {
                            return $response;
                            exit;
                        } else {
                            echo json_encode($response);
                            exit;
                        }
                    } else if ($status == "d") {
                        $response['message']    = ERROR_ACCOUNT_DEACTIVATED_CONTACT_ADMIN;
                        if (!$this->dataOnly) {

                            return $response;
                            exit;
                        } else {
                            echo json_encode($response);
                            exit;
                        }
                    } else {
                        regenerateSession();
                        $_SESSION["sessUserId"]             = $id;
                        $_SESSION["first_name"]     = ucfirst(strtolower($first_name));
                        $_SESSION["last_name"]      = ucfirst(strtolower($last_name));
                        $_SESSION['user_type']      = $user_type;

                        if (isset($_SESSION['req_uri']) && $_SESSION['req_uri'] != null) {
                            $path = $_SESSION['req_uri'];
                            unset($_SESSION['req_uri']);
                            redirectPage($path);
                        }

                        $response['status'] = true;
                        $response['message']    = LOGIN_SUCCESSFUL;
                        if (!$this->dataOnly) {
                            return $response;
                            exit;
                        } else {
                            echo json_encode($response);
                            exit;
                        }
                    }
                } else {
                    $response['message']    = MSG_INVALID_USER;
                    if (!$this->dataOnly) {
                        return $response;
                        exit;
                    } else {
                        echo json_encode($response);
                        exit;
                    }
                }
            } else {
                $response['message']    = MSG_INVALID_USER;
                if (!$this->dataOnly) {
                    return $response;
                    exit;
                } else {
                    echo json_encode($response);
                    exit;
                }
            }
        } else {
            $response['message']    = toastr_fill_all_required_details_before_proceed;
            if (!$this->dataOnly) {
                return $response;
                exit;
            } else {
                echo json_encode($response);
                exit;
            }
        }

    }

    public function resendActivationLink(){

        extract($_POST);

        $response = array();
        $response['status'] = false;
        $response['message'] = something_went_wrong;
        if (isset($email) && $email != NULL) {
            $selQuery     = $this->db->pdoQuery('SELECT id,first_name,last_name,email,status,email_verified FROM tbl_users WHERE email = ? AND user_type IN ("doctor","clinic") ', array(
                $email,
            ));

            if ($selQuery -> affectedRows() >= 1) {
                $result = $selQuery -> result();
                if ($result != false) {
                    extract($result);

                    if ($status == "d" && $email_verified == 'y') {
                        $response['message'] = ERROR_ACCOUNT_DEACTIVATED_CONTACT_ADMIN;
                        return $response;
                    } else if ($email_verified == 'n') {
                        $activation_code = $updatearray['activation_code'] = md5(time());
                        $activationLink = SITE_URL . 'active-account/' . $activation_code;

                        $this->db->update('tbl_users',$updatearray, array('id'=>$result['id']));

                        $to        = $email;
                        $arrayCont = array(
                            'greetings'      => $first_name.' '.$last_name,
                            'activationLink' => $activationLink,
                        );

                        $array = generateEmailTemplate('resend_activation_link', $arrayCont);
                        sendEmailAddress($to, $array['subject'], $array['message']);

                        $response['status'] = true;
                        $response['message'] = We_have_sent_new_activation_link_to_your_email_address_please_check_your_inbox;
                        return $response;
                    } else{
                        $response['message'] = You_have_already_activated_your_account;
                        return $response;
                    }
                }
                else {
                    $response['message'] = MSG_INVALID_USER;
                    return $response;
                }
            } else {
                $response['message'] = MSG_INVALID_USER;
                return $response;
            }
        } else {
            $response['message'] = MSG_INVALID_USER;
            return $response;
        }
    }

}
?>
