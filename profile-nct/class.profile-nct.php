<?php
class Profile
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
        $this->sessUserId = (isset($reqData['userId']) && $reqData['userId'] > 0) ? $reqData['userId']:$this->sessUserId;
        //dump_exit($this->userData);
        //TODO:: set height width of featured projects
    }

    public function getPageContent(){

        $usr = $this->db->select("tbl_users", array("*"), array("id" => $this->sessUserId)) -> result();

        $id             = isset($usr['id']) ? $usr['id'] : 0;
        $user_type      = isset($usr['user_type']) ? $usr['user_type'] : '';
        $first_name     = isset($usr['first_name']) ? $usr['first_name'] : '';
        $last_name      = isset($usr['last_name']) ? $usr['last_name'] : '';
        $clinic_name    = isset($usr['clinic_name']) ? $usr['clinic_name'] : '';
	$partner_id = isset($usr['partner_id']) ? $usr['partner_id'] : '';
        $parent_id      = isset($usr['parent_id']) ? $usr['parent_id'] : 0;

        if ($user_type == 'clinic') {
            $user_name = $clinic_name;
        } else{
            $user_name = $first_name.' '.$last_name;
        }

        $type_of_doctors = $this->db->pdoQuery("
            SELECT ud.type_of_doctor_id,d.name
            FROM tbl_users_doctor_type AS ud
            LEFT JOIN tbl_type_of_doctors AS d ON d.id = ud.type_of_doctor_id
            WHERE ud.user_id = ".(int) $id."
            ORDER BY ud.id ASC ")->results();
        $type_of_doctors_str = '';
        if (count($type_of_doctors) > 0) {
            foreach ($type_of_doctors as $key => $value) {
                $type_of_doctors_str.= '<li class="tag">'.$value['name'].'</li>';
            }
        }

        $specialties = $this->db->pdoQuery("
            SELECT ud.specialties_id,d.name
            FROM tbl_users_specialties AS ud
            LEFT JOIN tbl_specialties AS d ON d.id = ud.specialties_id
            WHERE ud.user_id = ".$id."
            ORDER BY ud.id ASC ")->results();

        $specialties_str = '';
        if (count($specialties) > 0) {
            foreach ($specialties as $key => $value) {
                $specialties_str.= '<li class="tag">'.$value['name'].'</li>';
            }
        }

        $consultation_fees  = isset($usr['consultation_fees']) ? $usr['consultation_fees'] : '';
        $practicing_since   = isset($usr['practicing_since']) && $usr['practicing_since'] != '' ? date("m-Y", strtotime($usr['practicing_since'])) : '';

        $practicing_since_html = $associated_clinic_html = $consultation_fees_html = $time_slot_html = '';
        if ($user_type == 'doctor') {
            if ($consultation_fees != '') {
                $consultation_fees_html = '<li class="profile-cell">
                <div class="row profile-info-block">
                <div class="col-lg-4">
                <h4>Consultation Fees:</h4>
                </div>
                <div class="col-lg-8">
                ₹ '.$consultation_fees.'
                </div>
                </div>
                </li>';
            }

            $associated_to_existing_clinic = isset($usr['associated_to_existing_clinic']) ? $usr['associated_to_existing_clinic'] : 'n';
            if ($associated_to_existing_clinic == 'y') {
                $cname = getTableValue('tbl_users','clinic_name',array('id' => $parent_id));
                $associated_clinic_html = '<li class="profile-cell">
                <div class="row profile-info-block">
                <div class="col-lg-4">
                <h4>Associated Clinic Name:</h4>
                </div>
                <div class="col-lg-8">
                '.$cname.'
                </div>
                </div>
                </li>';
            }

            if ($practicing_since != '') {
                $practicing_since_html = '<li class="profile-cell">
                <div class="row profile-info-block">
                <div class="col-lg-4">
                <h4>Practicing since:</h4>
                </div>
                <div class="col-lg-8">
                '.$practicing_since.'
                </div>
                </div>
                </li>';
            }

            $time_slot_html = get_time_slot_table($id);
        }

	$banner_html = '';

	if(!empty($usr['clinic_banner'])){
    		$banner_html = '<div class="profile-banner mb-3">
        				<img src="'.SITE_URL.'upload-nct/clinicBanner-nct/'.$usr['clinic_banner'].'"
        				style="width:100%; height:220px; object-fit:cover; border-radius:12px;">
    				</div>';
	}

        $replace = array(
            "%uploaded_image%"              => get_image_url($usr['profile_photo'],"profile_photo",'th2_'),
            "%clinic_banner_html%" 	    => $banner_html,
	    '%partner_id%'		    => $partner_id,
	    '%user_name%'                   => $user_name,
            "%first_name%"                  => isset($usr['first_name']) ? $usr['first_name'] : '',
            "%last_name%"                   => isset($usr['last_name']) ? $usr['last_name'] : '',
            '%phone_no%'                    => isset($usr['phone_no']) ? $usr['phone_no'] : 'NA',
            '%phone_country_code%'          => isset($usr['phone_country_code']) ? $usr['phone_country_code'] : '',
            '%phone_iso2_code%'             => isset($usr['phone_iso2_code']) ? $usr['phone_iso2_code'] : '',
	    '%clinic_description%' 	    => isset($usr['clinic_description']) ? $usr['clinic_description'] : '',
            '%address%'                     => isset($usr['address']) ? $usr['address'] : '',
	    '%GSTIN%' 			    => !empty($usr['gstin']) ? $usr['gstin'] : 'Not Available',
            '%latitude%'                    => isset($usr['latitude']) ? $usr['latitude'] : '',
            '%longitude%'                   => isset($usr['longitude']) ? $usr['longitude'] : '',
            '%city_name%'                   => isset($usr['city_name']) ? $usr['city_name'] : '',
            '%state_name%'                  => isset($usr['state_name']) ? $usr['state_name'] : '',
            '%country_name%'                => isset($usr['country_name']) ? $usr['country_name'] : '',
            '%zip_code%'                    => isset($usr['zip_code']) ? $usr['zip_code'] : '',
            '%type_of_doctors_str%'         => $type_of_doctors_str,
            '%specialties_str%'             => $specialties_str,
            '%consultation_fees_html%'      => $consultation_fees_html,
	    '%doctor_description%'	    => isset($usr['doctor_description']) ? $usr['doctor_description'] : '',
            '%practicing_since_html%'       => $practicing_since_html,
            '%time_slot_html%'              => $time_slot_html,
            '%associated_clinic_html%'      => $associated_clinic_html,
            '%hide_when_clinic%'            => $user_type == 'clinic' ? 'hidden' : '',
        );

        return get_view(DIR_TMPL . $this->module . "/" . $this->module . ".tpl.php", $replace);
    }

}
