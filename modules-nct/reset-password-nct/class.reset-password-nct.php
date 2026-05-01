<?php

class ResetPassword {

    function __construct() {
        foreach ($GLOBALS as $key => $values) {
            $this->$key = $values;
        }
    }

    public function getPageContent($activationToken) {
        $final_result = NULL;

        $replace = array('%TOKEN%'       => $activationToken);

        return get_view(DIR_TMPL . $this->module . "/" . $this->module . ".tpl.php", $replace);
    }

    public function sublitResetPassword(){

        $response = array();
        $response['status'] = false;
        $response['message'] = something_went_wrong;

        $token          = filtering($_POST['token'], 'input');
        $password       = filtering($_POST['new_password'], 'input');
        $cnpassword     = filtering($_POST['confirm_new_password'], 'input');

        if ($token != '' && $password != '' && ($password == $cnpassword)) {
            $get_user_details = $this->db->select('tbl_users', array('id'), array('password_reset_key' => $token))->result();
            $user_id = $get_user_details['id'];
            if ($user_id > 0) {
                $this->db->update('tbl_users', array("password" => md5($password), 'password_reset_key' => ''), array("id" => $user_id));

                $response['status'] = true;
                $response['message'] = toastr_password_successfully_updated;
                return $response;
            } else {
                $response['message'] = something_went_wrong;
                return $response;
            }
        } else {
            $response['message'] = 'Please enter valid details!';
            return $response;
        }

    }

}

?>