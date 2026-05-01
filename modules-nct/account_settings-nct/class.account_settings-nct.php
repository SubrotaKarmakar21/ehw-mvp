<?php
class AccountSettings
{
    public function __construct($module = "", $reqData = array())
    {
        foreach ($GLOBALS as $key => $values) {
            $this->$key = $values;
        }
        $this->module   = $module;
        $this->reqData  = $reqData;
        $this->userData = getUserData();

        $this->dataOnly = (isset($this->reqData['dataOnly']) && $this->reqData['dataOnly']==true)?true:false;
        $this->sessUserId = (isset($reqData['userId']) && $reqData['userId'] >0)?$reqData['userId']:$this -> sessUserId;
        //dump_exit($this->userData);
        //TODO:: set height width of featured projects
    }

    public function getPageContent(){

        $replace = array(

        );

        return get_view(DIR_TMPL . $this->module . "/" . $this->module . ".tpl.php", $replace);
    }

    public function submit_change_password(){
        extract($_POST);

        $oldPassword = isset($this->reqData['oldPassword']) ? $this->reqData['oldPassword'] : '';
        $newPassword = isset($this->reqData['newPassword']) ? $this->reqData['newPassword'] : '';
        $confirmNewPassword = isset($this->reqData['confirmNewPassword']) ? $this->reqData['confirmNewPassword'] : '';

        if($oldPassword != '' && $newPassword!='' && $confirmNewPassword !=''){
            $old_db_pass = getTableValue('tbl_users','password',array("id"=>$this->sessUserId));

            if ($old_db_pass != md5($oldPassword)) {
                $dataArray['type'] = "error";
                $dataArray['message'] = toastr_old_password_invalid;
                return $dataArray;
            }
            if($oldPassword != $newPassword){
                if ($newPassword == $confirmNewPassword) {
                    $this->db->update("tbl_users",array("password"=>md5($newPassword)),array("id"=>$this->sessUserId));
                    $dataArray['type'] = "success";
                    $dataArray['message'] = toastr_password_successfully_updated;
                } else{
                    $dataArray['type'] = "error";
                    $dataArray['message'] = error_new_and_confirm_password_are_not_same;
                    return $dataArray;
                }
            } else{
                $dataArray['type'] = "error";
                $dataArray['message'] = error_new_and_old_password_are_same;               
            }   
        } else{
            $dataArray['type'] = "error";
            $dataArray['message'] = MSG_FILL_VALUE;
        }

        return $dataArray;
    }

    public function processDeactivateAccount($user_id = 0) {

        $response = array();
        $response['status'] = false;

        if($user_id > 0){
            $get_user_details = $this->db->select('tbl_users',array('id','is_deactivated','user_type'),array('id'=>$user_id))->result(); 

            if ($get_user_details) {
                if($get_user_details['is_deactivated'] == 'y'){
                    $msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Your account has already been deactivated.'));

                    $response['message'] = toastr_account_already_deactivated;
                } else{
                    $this->db->update("tbl_users",array("is_deactivated" => 'y'),array('id' => $user_id));
                    
                    if($get_user_details['user_type'] == 'patient'){
                        $this->db->update("tbl_appointment",array('is_active'=>'d'),array("user_id"=>$user_id));
                    } else if($get_user_details['user_type'] == 'doctor'){

                    } else if($get_user_details['user_type'] == 'clinic'){

                    }

                    $response['status'] = true;
                    $response['message'] = toastr_your_account_has_been_deactivated;
                }

                if (!$this->dataOnly) {
                    unset($_SESSION["sessUserId"]);
                    unset($_SESSION["first_name"]);
                    unset($_SESSION["last_name"]);
                    unset($_SESSION["user_type"]);
                }
                return $response;
            } else {
                $response['message'] = something_went_wrong;
                return $response;
            }
        } else {
            $response['message'] = something_went_wrong;
            return $response;
        }
    }

    public function processDeleteAccount($user_id = 0) {

        $response = array();
        $response['status'] = false;

        if($user_id > 0){
            $get_user_details = $this->db->select('tbl_users',array('id','user_type'),array('id'=>$user_id))->result(); 
            if ($get_user_details) {

                deleteAccount($user_id,$this->dataOnly);
                
                $msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=> 'delUser'));

                $response['status'] = true;
                $response['message'] = MSG_DELETD_ADMIN;

                if (!$this->dataOnly) {
                    unset($_SESSION["sessUserId"]);
                    unset($_SESSION["first_name"]);
                    unset($_SESSION["last_name"]);
                    unset($_SESSION["user_type"]);
                }
                return $response;
            } else {
                $response['message'] = MSG_USER_NOTFOUND;
                return $response;
            }
        } else {
            $response['message'] = MSG_SOMETHING_WENT_WRONG;
            return $response;
        }
    }
}
