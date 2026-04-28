<?php
class Home
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

    public function getUserAccountReactivationPageContent() {
        $final_result = get_view(DIR_TMPL . $this->module . "/user_account_reactivation-nct.tpl.php");
        return $final_result;
    }

    public function submitUserReactivation($user_id = 0){
        $respArr = array(
            'status'    =>  false,
            'message'   =>  something_went_wrong,
            'data'      =>  []
        );

        $getUserData = $this->db->select('tbl_users',array('first_name','last_name','user_type','clinic_name','email','is_deactivated'),array('id' => $user_id))->result();

        if(isset($getUserData)){
            if(isset($getUserData['is_deactivated']) && $getUserData['is_deactivated'] == 'y'){
                $timestamp = time();

                $acti_key = base64_encode(time());

                $email = $getUserData['email'];
                $this->db->update("tbl_users",array("activation_code"=>$acti_key),array('email'=>$email));

                $to = $email;

                $greetings = $getUserData['user_type'] == 'clinit' ? $getUserData['clinic_name'] : $getUserData['first_name'].' '.$getUserData['last_name'];
                $arrayCont = array(
                    'greetings'         => $greetings,
                    'link'              => SITE_REACTIVE_ACCOUNT.'/'.base64_encode($email).'/reactivation_key/'.$acti_key,
                );
                $array = generateEmailTemplate('reactivate_account_by_user', $arrayCont);
                sendEmailAddress($to, $array['subject'], $array['message']);

                $respArr['status']      =  true;
                $respArr['message']     =  toastr_email_sent_to_reactivate_account;
            } else if(isset($getUserData['is_deactivated']) && $getUserData['is_deactivated']=='n'){
                $respArr['status']      =  false;
                $respArr['message']     =  You_have_already_activated_your_account;
            }
        }

        if ($this->dataOnly) {
            return $respArr;
        } else{
            $msgType = $_SESSION['msgType'] = disMessage(array('type' => ($respArr['status']?'suc':'err'), 'var' => $respArr['message']));
            redirectPage(SITE_REACTIVE_ACCOUNT);
        }
    }

    public function getHeaderContent($module = 'home-nct'){
        $header_section = null;
        if (isset($this->sessUserId) && $this->sessUserId > 0) {
            $generate_bill_button = $add_doctor_button = $my_doctors_menu = $my_services_menu = $my_patients_menu = $billing_menu = '';

            $userInfo = $this->db->select("tbl_users", array("id",'first_name','last_name','clinic_name','profile_photo','user_type'), array("id" => $this->sessUserId)) -> result();

            $user_type          = isset($userInfo['user_type']) ? $userInfo['user_type'] : '';
            $last_name          = isset($userInfo['last_name']) ? $userInfo['last_name'] : '';
            $first_name         = isset($userInfo['first_name']) ? $userInfo['first_name'] : '';
            $clinic_name          = isset($userInfo['clinic_name']) ? $userInfo['clinic_name'] : '';
            $profile_photo      = isset($userInfo['profile_photo']) ? $userInfo['profile_photo'] : '';
            $profile_photo_path = get_image_url($profile_photo,"profile_photo",'th1_');

            if ($user_type == 'clinic') {
                $alt_name = $clinic_name;
            } else{
                $alt_name = $first_name.' '.$last_name;
            }

	    if ($user_type == 'clinic') {
    		$generate_bill_button = '<li class="nav-item me-auto">
        		<a href="'.SITE_BILLING.'" class="header-btn nav-link btn">
            			Generate Bill
        		</a>
    		</li>';
	    }

            if ($user_type == 'clinic') {
                $add_doctor_button = '<li class="nav-item">
                <a href="'.SITE_ADD_DOCTORS.'" class="header-btn nav-link btn">
                Add Doctor
                </a>
                </li>';

                $my_doctors_menu = '<li><a class="dropdown-item" href="'.SITE_MY_DOCTORS.' ">My Doctors</a></li>';
            }

	    if ($user_type == 'clinic') {
    		$my_services_menu = '<li><a class="dropdown-item" href="'.SITE_URL.'my-services">My Services</a></li>';
	    }

	    if ($user_type == 'clinic') {
    		$billing_menu = '<li><a class="dropdown-item" href="'.SITE_URL.'modules-nct/manage_bills-nct/">Billings</a></li>';
	    }

            if ($user_type == 'clinic' || $user_type == 'doctor') {
                $my_patients_menu = '<li><a class="dropdown-item" href="'.SITE_MY_PATIENTS.' ">My Patients</a></li>';
            }


            $replace = array(
                '%profile_photo%'                   => $profile_photo_path,
                '%alt_name%'                        => $alt_name,
		'%generate_bill_button%'	    => $generate_bill_button,
                '%add_doctor_button%'               => $add_doctor_button,
                '%my_doctors_menu%'                 => $my_doctors_menu,
		'%my_services_menu%'		    => $my_services_menu,
		'%billing_menu%'		    => $billing_menu,
                '%my_patients_menu%'                => $my_patients_menu,
            );
            $header_section = get_view(DIR_TMPL . $this->module . "/after-login-header-section.tpl.php", $replace);
        } else {
            $header_section = get_view(DIR_TMPL . $this->module . "/before-login-header-section.tpl.php", array());
        }


        $replace = array(
            '%URL_REDIRECT_TO%'        => isset($this->sessUserId) && $this->sessUserId > 0 ? SITE_DASHBOARD : SITE_URL,
            '%header_section%'          => $header_section,
        );

       return get_view(DIR_TMPL . "header-nct.tpl.php", $replace);

    }

    public function getFooterContent($module = 'home-nct'){

        $pages     = $this->db->select("tbl_content", array('*'), array("is_active" => 'y'))->results();
        $menu_item = null;
        foreach ($pages as $page) {
            if(trim($page['page_slug']) != null && trim($page['page_title']) != null){
                if ($this->dataOnly) {
                    $app_array[] = array(
                        'id'                    => (string) $page['id'],
                        'title'                 => $page['page_title'],
                        'link_type'             => $page['link_type'],
                        'page_url'              => $page['url'],
                        'page_desc'             => strip_tags($page['page_desc']),
                    );
                } else{
                    $menu_item .= "<li><a href=" . SITE_URL . 'content/' . $page['page_slug'] . ">" . $page['page_title'] . "</a></li>";
                }
            }
        }

        if ($this->dataOnly) {
            return $app_array;
        } else{
            $menu_item .= '<li><a href="' . SITE_CONTACTUS . '">' . Contact_us . '</a></li>';

            $att_add="";

            if($module != 'home-nct'){
                $att_add = 'rel="nofollow"';
            } 
        }
        

        return get_view(DIR_TMPL . "footer-nct.tpl.php", array(
            '%MENU_ITEMS%'              => $menu_item,
            '%ATT_ADD%'                 => $att_add,
            '%YEAR%'                    => date('Y'),
        ));
    }

    public function get_type_of_doctors($selected_id_array = []){

        $html = '';

        $res = $this->db->pdoQuery("SELECT * FROM tbl_type_of_doctors WHERE is_active = 'y' ")->results();

        if (isset($res) && count($res) > 0) {

            // $main_content = new MainTemplater(DIR_TMPL . "select_option-nct.tpl.php");
            // $main_content1 = $main_content->parse();

            foreach ($res as $key => $value) {

                $image = get_image_url($value['logo'],"type_of_doctors",'th2_');
                if ($this->dataOnly) {
                    $app_array[] = array(
                        'id'                    => (string) $value['id'],
                        'title'                 => $value['name'],
                        'image'                 => $image,
                    );
                } else{
                    $replace = array(
                        '%VALUE%'                   => $value['id'],
                        '%DISPLAY_VALUE%'           => $value['name'],
                        '%SELECTED%'                => $selected,
                    );
                    $html .= str_replace(array_keys($replace),array_values($replace),$main_content1);
                }


            }
        }

        if ($this->dataOnly) {
            return $app_array;
        } else{
            return $html;
        }
    }

    public function get_specialties($selected_id_array = []){

        $html = '';

        $res = $this->db->pdoQuery("SELECT * FROM tbl_specialties WHERE is_active = 'y' ")->results();

        if (isset($res) && count($res) > 0) {

            // $main_content = new MainTemplater(DIR_TMPL . "select_option-nct.tpl.php");
            // $main_content1 = $main_content->parse();

            foreach ($res as $key => $value) {

                if ($this->dataOnly) {
                    $app_array[] = array(
                        'id'                    => (string) $value['id'],
                        'title'                 => $value['name'],
                    );
                } else{
                    $replace = array(
                        '%VALUE%'                   => $value['id'],
                        '%DISPLAY_VALUE%'           => $value['name'],
                        '%SELECTED%'                => $selected,
                    );
                    $html .= str_replace(array_keys($replace),array_values($replace),$main_content1);
                }


            }
        }

        if ($this->dataOnly) {
            return $app_array;
        } else{
            return $html;
        }
    }

    public function getPageContent(){

        $replace = array(
        );

        return get_view(DIR_TMPL . $this->module . "/" . $this->module . ".tpl.php", $replace);
    }

}
