<?php
class ContactUs {
    function __construct($module = "", $id = 0, $reqData = array()) {
        foreach ($GLOBALS as $key => $values) {
            $this -> $key = $values;

        }
        $this -> module = $module;
        $this -> id = $id;
        $this->reqData = $reqData;
        
        //for web service
        $this->dataOnly = (isset($this->reqData['dataOnly']) && $this->reqData['dataOnly']==true)?true:false;
        $this->sessUserId = (isset($reqData['userId']) && $reqData['userId'] >0)?$reqData['userId']:$this -> sessUserId;

        
    }
    public function submitContactForm(){
        //make php validations
        $response = array();
        $response['status'] = false;
        $response['message'] = 'error';
        
        $first_name             = issetor($this->reqData['first_name'],null);
        $last_name              = issetor($this->reqData['last_name'],null);
        $email_address          = issetor($this->reqData['email_address'],null);        
        $phone_no               = issetor($this->reqData['phone_no'],null);
        $message                = issetor($this->reqData['message'],null);
        $phone_country_code     = issetor($this->reqData['phone_country_code'], null);

        if ($this->dataOnly) {
            $user_id = issetor($this->reqData['userId']);
        } else{
            $user_id = isset($_SESSION["sessUserId"]) && (int) $_SESSION["sessUserId"] > 0 ? (int) $_SESSION["sessUserId"] : 0;
        }

        if(trim($first_name) == null || trim($last_name) == null || trim($email_address) == null || trim($message) == null){
            $response['status'] = false;
            $response['message'] = MSG_FILL_VALUE;
            return $response;
        }else{
            //mk entry in contact us table

            $insert_array = array(
                'user_id'               => $user_id,
                'first_name'            => $first_name,
                'last_name'             => $last_name,
                'email'                 => $email_address,
                'phone_no'              => $phone_no,
                'phone_country_code'    => $phone_country_code,
                'iso2_code'             => issetor($this->reqData['phone_iso2_code'],null),
                'message'               => $message,
                'ip_address'            => get_ip_address(),
                'created_date'          => date('Y-m-d H:i:s'),
                'updated_date'          => date('Y-m-d H:i:s')
            );

            $lastInsertId = $this->db->insert('tbl_contact_us',$insert_array)->lastInsertId();    
            
            if($lastInsertId > 0){
                $arrayCont = array(
                    'greetings'         => ADMIN_NM,
                    'username'          => $first_name.' '.$last_name,
                    'email'             => $email_address,
                    'message'           => $message,
                    'contact_number'    => issetor($this->reqData['phone_country_code'],null).' '.$phone_no,
                );    
                $array = generateEmailTemplate('contactUs', $arrayCont);
                sendEmailAddress(ADMIN_EMAIL, $array['subject'], $array['message']);

                $arrayCont1 = array(
                    'greetings'         => $first_name.' '.$last_name,
                    'username'          => $first_name.' '.$last_name,
                    'email'             => $email_address,
                    'message'           => $message,
                    'contact_number'    => issetor($this->reqData['phone_country_code'],null).' '.$phone_no,
                );    
                $array1 = generateEmailTemplate('contactUsUsers', $arrayCont1);
                sendEmailAddress($email_address, $array1['subject'], $array1['message']);

                $response['status'] = true;
                $response['message'] = toastr_contact_us_thank_you;
                return $response;
            }
        }
    }

    public function getPageContent() {

        $usr = $this->db->select("tbl_users", array("*"), array("id" => $this->sessUserId)) -> result();

        $replace = array(
            "%first_name%"              => isset($usr['first_name']) ? $usr['first_name'] : '',
            "%last_name%"               => isset($usr['last_name']) ? $usr['last_name'] : '',
            '%phone_no%'                => isset($usr['phone_no']) ? $usr['phone_no'] : '',
            '%phone_country_code%'      => isset($usr['phone_country_code']) ? $usr['phone_country_code'] : '',
            '%phone_iso2_code%'         => isset($usr['phone_iso2_code']) ? $usr['phone_iso2_code'] : '',
            '%email_address%'           => isset($usr['email']) ? $usr['email'] : '',
        );
        return get_view(DIR_TMPL . $this -> module . "/" . $this -> module . ".tpl.php",$replace);
    }

}
?>
