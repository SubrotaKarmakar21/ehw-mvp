<?php
class EditProfile {
    function __construct($module = "", $reqData = array()) {
        foreach ($GLOBALS as $key => $values) {
            $this -> $key = $values;

        }
        $this -> module = $module;
        $this->reqData = $reqData;
        //for web service
        $this->dataOnly = (isset($this->reqData['dataOnly']) && $this->reqData['dataOnly']==true)?true:false;
        $this->sessUserId = (isset($reqData['userId']) && $reqData['userId'] >0)?$reqData['userId']:$this -> sessUserId;
    }

    public function submitEditProfileForm(){
        //make php validations
        $response = array();
        $response['status'] = false;
        $response['message'] = 'error';

        if ($this->sessUserId <= 0) {
            $response['status'] = false;
            $response['message'] = something_went_wrong;
            return $response;
        }
        $clinic_name            = isset($this->reqData['clinic_name']) ? $this->reqData['clinic_name'] : '';
        $first_name             = isset($this->reqData['first_name']) ? $this->reqData['first_name'] : '';
        $last_name              = isset($this->reqData['last_name']) ? $this->reqData['last_name'] : '';
	$gstin 			= isset($this->reqData['gstin']) ? strtoupper(trim($this->reqData['gstin'])) : '';
        $phone_no               = isset($this->reqData['phone_no']) ? $this->reqData['phone_no'] : '';
	$whatsapp_number 	= isset($this->reqData['whatsapp_number']) ? $this->reqData['whatsapp_number'] : '';
        $address                = isset($this->reqData['address']) ? $this->reqData['address'] : '';
        $type_of_doctor_id      = isset($this->reqData['type_of_doctor_id']) ? $this->reqData['type_of_doctor_id'] : array();
        $specialties_id                 = isset($this->reqData['specialties_id']) ? $this->reqData['specialties_id'] : array();
        $associated_to_existing_clinic  = isset($this->reqData['associated_to_existing_clinic']) ? $this->reqData['associated_to_existing_clinic'] : 'n';
        /*for app*/
        $gender                 = isset($this->reqData['gender']) && $this->reqData['gender'] != '' ? trim($this->reqData['gender']) : 'n';
        $date_of_birth          = isset($this->reqData['date_of_birth']) && $this->reqData['date_of_birth'] != '' ? date('Y-m-d',strtotime($this->reqData['date_of_birth'])) : '';
        $updateArray = array(
            'first_name'                        => $first_name,
            'last_name'                         => $last_name,
	    'gstin' 				=> isset($this->reqData['gstin']) ? $this->reqData['gstin'] : '',
	    'doctor_description'		=> isset($this->reqData['doctor_description']) ? $this->reqData['doctor_description'] : '',
	    'clinic_description' 		=> isset($this->reqData['clinic_description']) ? trim($this->reqData['clinic_description']) : '',
            'phone_no'                          => $phone_no,
	    'whatsapp_number'			=> $whatsapp_number,
            'phone_country_code'                => isset($this->reqData['phone_country_code']) ? $this->reqData['phone_country_code'] : '',
            'iso2_code'                         => isset($this->reqData['phone_iso2_code']) ? strtolower($this->reqData['phone_iso2_code']) : '',
            'address'                           => isset($this->reqData['address']) ? $this->reqData['address'] : '',
            'latitude'                          => isset($this->reqData['latitude']) ? $this->reqData['latitude'] : '',
            'longitude'                         => isset($this->reqData['longitude']) ? $this->reqData['longitude'] : '',
            'city_name'                         => isset($this->reqData['city_name']) ? $this->reqData['city_name'] : '',
            'state_name'                        => isset($this->reqData['state_name']) ? $this->reqData['state_name'] : '',
            'country_name'                      => isset($this->reqData['country_name']) ? $this->reqData['country_name'] : '',
            'zip_code'                          => isset($this->reqData['zip_code']) ? $this->reqData['zip_code'] : '',
            'gender'                            => $gender,
        );
        if ($date_of_birth != '') {
            $updateArray['date_of_birth'] = $date_of_birth;
        }

	/* ========================= */
	/* PRESCRIPTION HEADER UPLOAD */
	/* ========================= */

	// DEBUG
	if(isset($_FILES['clinic_banner'])){

    		file_put_contents('/tmp/banner_upload_debug.txt', print_r($_FILES['clinic_banner'], true));

	}

	if(isset($_FILES['avatar_file']['name']) && $_FILES['avatar_file']['name'] != ''){

    		$banner_name = uploadImage($_FILES['clinic_banner'], "clinic_banner", DIR_UPD_CLINIC_BANNER);

    		if($banner_name){
        		$updateArray['clinic_banner'] = $banner_name;
    		}

	}

	if(!empty($gstin)){

    		if(strlen($gstin) != 15){
        		$response['status'] = false;
        		$response['message'] = 'GSTIN must be 15 characters.';
        		return $response;
    		}

    		if(!preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{1}Z[A-Z0-9]{1}$/', $gstin)){
        		$response['status'] = false;
        		$response['message'] = 'Invalid GSTIN format.';
        		return $response;
    		}

	}

        $parent_id = (int) getTableValue('tbl_users','parent_id',array('id' => $this->sessUserId));

        if ($parent_id <= 0) {
            $updateArray['associated_to_existing_clinic'] = $associated_to_existing_clinic;
            if ($associated_to_existing_clinic == 'y') {
                $updateArray['parent_id'] = isset($this->reqData['clinic_id']) ? (int) $this->reqData['clinic_id'] : 0;
            } else{
                $updateArray['parent_id'] = 0;
            }
        }

        if (!$this->dataOnly) {
            /*validation for web*/
            if ($this->sessUserType == 'doctor') {
                $practicing_since   = isset($this->reqData['practicing_since']) && $this->reqData['practicing_since'] != '' ? $this->reqData['practicing_since'] : '';
                $consultation_fees  = isset($this->reqData['consultation_fees']) && $this->reqData['consultation_fees'] != '' ? $this->reqData['consultation_fees'] : '';

                if (!empty($practicing_since)) {
                    list($month, $year) = explode('-', $practicing_since);
                    $db_date = sprintf("%04d-%02d-01", $year, $month);
                } else {
                    $db_date = NULL;
                }

                if ($consultation_fees == '') {
                    $response['status'] = false;
                    $response['message'] = 'Please enter consultation fees.';
                    return $response;
                }

                if ($db_date == '' || $db_date == NULL) {
                    $response['status'] = false;
                    $response['message'] = 'Please enter practicing since.';
                    return $response;
                }

                if (trim($first_name) == null) {
                    $response['status'] = false;
                    $response['message'] = 'Please enter first name.';
                    return $response;
                }

                if (trim($last_name) == null) {
                    $response['status'] = false;
                    $response['message'] = 'Please enter last name.';
                    return $response;
                }

                $updateArray['practicing_since'] = $db_date;
                $updateArray['last_name'] = $last_name;
                $updateArray['first_name'] = $first_name;
                $updateArray['consultation_fees'] = isset($this->reqData['consultation_fees']) ? $this->reqData['consultation_fees'] : '';
		$updateArray['doctor_description'] = isset($this->reqData['doctor_description']) ? $this->reqData['doctor_description'] : '';
            } else if ($this->sessUserType == 'clinic') {
                if (trim($clinic_name) == null) {
                    $response['status'] = false;
                    $response['message'] = 'Please enter clinic name.';
                    return $response;
                }

                $updateArray['clinic_name'] = $clinic_name;
            }

            if (count($specialties_id) <= 0) {
                $response['status'] = false;
                $response['message'] = 'Please select specialties.';
                return $response;
            }

            if (count($type_of_doctor_id) <= 0) {
                $response['status'] = false;
                $response['message'] = 'Please select type of doctors.';
                return $response;
            }
        }

        /*validation for app patient */
        if ($this->dataOnly) {
            if (trim($first_name) == null) {
                $response['status'] = false;
                $response['message'] = 'Please enter first name.';
                return $response;
            }

            if (trim($last_name) == null) {
                $response['status'] = false;
                $response['message'] = 'Please enter last name.';
                return $response;
            }
        }

        if(trim($phone_no) == null || trim($address) == null){
            $response['status'] = false;
            $response['message'] = MSG_FILL_VALUE;
            return $response;
        } else{
            //mk entry in contact us table

            $updateArray['updated_date'] = date('Y-m-d H:i:s');

            $this->db->update('tbl_users',$updateArray,array('id' => $this->sessUserId));

            if (!$this->dataOnly) {
                /*Insert/update & delete Type Of DOctors ============================================== */
                $existing_type_of_doctor_ids = $this->db->pdoQuery("SELECT type_of_doctor_id
                    FROM tbl_users_doctor_type
                    WHERE user_id = ".$this->sessUserId." ")->results();

                $existing_type_of_doctor_ids = array_map(function($row) {
                    return $row['type_of_doctor_id'];
                }, $existing_type_of_doctor_ids);
                $type_of_doctor_to_delete = array_diff($existing_type_of_doctor_ids, $type_of_doctor_id);

                if (count($type_of_doctor_to_delete) > 0) {
                    foreach ($type_of_doctor_to_delete as $key => $value) {
                        $this->db->delete('tbl_users_doctor_type',array('user_id' => $this->sessUserId,'type_of_doctor_id' => $value));
                    }
                }

                if (count($type_of_doctor_id) > 0) {
                    foreach ($type_of_doctor_id as $key => $value) {
                        $is_exist = (int) getTableValue('tbl_users_doctor_type','id',array('user_id' => $this->sessUserId,'type_of_doctor_id' => $value));

                        $subCatIns = array(
                            'created_date'              => date('Y-m-d H:i:s'),
                        );

                        if ($is_exist <= 0) {
                            $subCatIns['user_id']               = $this->sessUserId;
                            $subCatIns['type_of_doctor_id']     = $value;

                            $this->db->insert('tbl_users_doctor_type',$subCatIns);
                        }
                    }
                }
                /*Insert/update & delete Type Of DOctors ============================================== */

                /*Insert/update & delete specialties ============================================== */
                $existing_specialties_ids = $this->db->pdoQuery("SELECT specialties_id
                    FROM tbl_users_specialties
                    WHERE user_id = ".$this->sessUserId." ")->results();

                $existing_specialties_ids = array_map(function($row) {
                    return $row['specialties_id'];
                }, $existing_specialties_ids);
                $specialties_id_to_delete = array_diff($existing_specialties_ids, $specialties_id);


                if (count($specialties_id_to_delete) > 0) {
                    foreach ($specialties_id_to_delete as $key => $value) {
                        $this->db->delete('tbl_users_specialties',array('user_id' => $this->sessUserId,'specialties_id' => $value));
                    }
                }

                if (count($specialties_id) > 0) {
                    foreach ($specialties_id as $key => $value) {
                        $is_exist = (int) getTableValue('tbl_users_specialties','id',array('user_id' => $this->sessUserId,'specialties_id' => $value));

                        $subCatIns = array(
                            'created_date'              => date('Y-m-d H:i:s'),
                        );

                        if ($is_exist <= 0) {
                            $subCatIns['user_id']               = $this->sessUserId;
                            $subCatIns['specialties_id']    = $value;

                            $this->db->insert('tbl_users_specialties',$subCatIns);
                        }
                    }
                }
                /*Insert/update & delete specialties ============================================== */

                $seasonal_price_action          = isset($this->reqData['seasonal_price_action']) ? $this->reqData['seasonal_price_action'] : [];
                $seasonal_price_action_temp     = isset($this->reqData['seasonal_price_action_temp']) ? $this->reqData['seasonal_price_action_temp'] : [];
                $available                      = isset($this->reqData['available']) ? $this->reqData['available'] : [];

                if (count($seasonal_price_action) > 0) {
                    foreach ($seasonal_price_action as $key => $value) {
                        $arr = explode('_', $value);
                        $new_day = isset($arr[0]) ? $arr[0] : '';
                        $new_index = isset($arr[1]) ? $arr[1] : '';

                        $slotData = [];
                        $dbId = isset($this->reqData['db_value_id_'.$new_day.'_to_'.$new_index]) ? $this->reqData['db_value_id_'.$new_day.'_to_'.$new_index] : 0;

                        if ($dbId > 0) {
                            if (isset($available[$new_day][0]) && $available[$new_day][0] == 'n') {
                                $this->db->pdoQuery("DELETE FROM tbl_users_time_slot WHERE user_id = ".(int) $this->sessUserId." AND day = '".$new_day."' ");
                            } else {
                                if (isset($this->reqData[$new_day.'_from_'.$new_index]) && $this->reqData[$new_day.'_from_'.$new_index] != '' && $this->reqData[$new_day.'_from_'.$new_index] != '') {
                                    $slotData['from_time'] =   $this->reqData[$new_day.'_from_'.$new_index];
                                }
                                if (isset($this->reqData[$new_day.'_to_'.$new_index]) && $this->reqData[$new_day.'_to_'.$new_index] != '' && $this->reqData[$new_day.'_to_'.$new_index] != '') {
                                    $slotData['to_time'] =   $this->reqData[$new_day.'_to_'.$new_index];
                                }

                                $slotData['is_available']   = 'y';
                                $slotData['day']            = $new_day;
                                $slotData['user_id']        = $this->sessUserId;
                                $slotData['updated_date']   = date('Y-m-d H:i:s');

                                $newId = $this->db->update('tbl_users_time_slot', $slotData,array('id' => $dbId));
                            }
                        }
                    }
                }

                if (count($seasonal_price_action_temp) > 0) {
                    foreach ($seasonal_price_action_temp as $key => $value) {
                        $arr = explode('_', $value);
                        $new_day = isset($arr[0]) ? $arr[0] : '';
                        $new_index = isset($arr[1]) ? $arr[1] : '';

                        $slotData = [];

                        if (isset($available[$new_day][0]) && $available[$new_day][0] == 'n') {
                            $this->db->pdoQuery("DELETE FROM tbl_users_time_slot WHERE user_id = ".(int) $this->sessUserId." AND day = '".$new_day."' ");
                        } else {
                            if (isset($this->reqData[$new_day.'_from_'.$new_index]) && $this->reqData[$new_day.'_from_'.$new_index] != '' && $this->reqData[$new_day.'_from_'.$new_index] != '') {
                                $slotData['from_time'] =   $this->reqData[$new_day.'_from_'.$new_index];
                            }
                            if (isset($this->reqData[$new_day.'_to_'.$new_index]) && $this->reqData[$new_day.'_to_'.$new_index] != '' && $this->reqData[$new_day.'_to_'.$new_index] != '') {
                                $slotData['to_time'] =   $this->reqData[$new_day.'_to_'.$new_index];
                            }

                            $slotData['is_available']   = 'y';
                            $slotData['day']            = $new_day;
                            $slotData['user_id']        = $this->sessUserId;
                            $slotData['updated_date']   = date('Y-m-d H:i:s');
                            $slotData['created_date']   = date('Y-m-d H:i:s');
                            $newId = $this->db->insert('tbl_users_time_slot', $slotData)->getLastInsertId();
                        }
                    }
                }
            }

            $response['status'] = true;
            $response['message'] = 'Profile details successfully updated.';
            return $response;
        }
    }

    public function getPageContent() {
        $usr = $this->db->select("tbl_users", array("*"), array("id" => $this->sessUserId)) -> result();
	$clinic_banner_preview = '';

	if(!empty($usr['clinic_banner'])){
    		$banner_url = SITE_URL."upload-nct/clinicBanner-nct/".$usr['clinic_banner'];

    		$clinic_banner_preview = '<img src="'.$banner_url.'" style="width:100%;max-width:900px;height:200px;object-fit:contain;background:#fff;border:1px solid #ddd;border-radius:6px;padding:5px;">';
	}

        $user_type = isset($usr['user_type']) ? $usr['user_type'] : '';
        $parent_id = isset($usr['parent_id']) ? $usr['parent_id'] : 0;

        $other_profile_field_info = '';
        if ($user_type == 'doctor') {
            $main_content = new MainTemplater(DIR_TMPL.$this->module."/doctors_profile_form-nct.tpl.php");
            $main_content1 = $main_content->parse();

            $practicing_since_display = '';
            if (isset($usr['practicing_since']) && !empty($usr['practicing_since'])) {
                $practicing_since_display = date("m-Y", strtotime($usr['practicing_since']));
            }

            $associated_to_existing_clinic = isset($usr['associated_to_existing_clinic']) ? $usr['associated_to_existing_clinic'] : 'n';
            $associated_clinic_id = isset($usr['associated_clinic_id']) ? $usr['associated_clinic_id'] : 0;

     $clinic_banner_preview = '';

     if(!empty($usr['clinic_banner'])){

    	$banner_url = SITE_URL."upload-nct/clinicBanner-nct/".$usr['clinic_banner'];

    	$clinic_banner_preview = '<img src="'.$banner_url.'" style="width:100%;max-width:900px;height:200px;object-fit:contain;background:#fff;border:1px solid #ddd;border-radius:6px;padding:5px;">';
     }

     $replace_new = array(
		'%clinic_banner_preview%'			=> $clinic_banner_preview,
		'%GSTIN%' 					=> isset($usr['gstin']) ? $usr['gstin'] : '',
                '%consultation_fees%'                           => isset($usr['consultation_fees']) ? $usr['consultation_fees'] : '',
		'%doctor_description%'				=> isset($usr['doctor_description']) ? $usr['doctor_description'] : '',
                '%practicing_since%'                            => $practicing_since_display,
                '%get_clinic_list%'                             => $this->get_clinics_list_to_associate($associated_clinic_id),

                '%associated_to_existing_clinic_y%'             => $associated_to_existing_clinic == 'y' ? 'checked' : '',
                '%associated_to_existing_clinic_n%'             => $associated_to_existing_clinic == 'n' ? 'checked' : '',
                '%hide_if_associated_to_existing_clinic_n%'     => $associated_to_existing_clinic == 'y' ? '' : 'hidden',
                '%associated_with_field_container%'             => $parent_id <= 0 ? '' : 'hidden',

            );
            $other_profile_field_info = str_replace(array_keys($replace_new),array_values($replace_new),$main_content1);
        }

        $type_of_doctors = $this->db->pdoQuery("
            SELECT cap.type_of_doctor_id
            FROM tbl_users_doctor_type AS cap
            WHERE cap.user_id = ".(int) $usr['id']." ORDER BY cap.id ASC ")->results();
        $type_of_doctor_ids = array_map(function($row) {
            return $row['type_of_doctor_id'];
        }, $type_of_doctors);

        $specialties = $this->db->pdoQuery("
            SELECT cap.specialties_id
            FROM tbl_users_specialties AS cap
            WHERE cap.user_id = ".(int) $usr['id']." ORDER BY cap.id ASC ")->results();
        $specialties_ids = array_map(function($row) {
            return $row['specialties_id'];
        }, $specialties);

        /*get slot info to 7 days*/
        $main_content1 = new MainTemplater(DIR_TMPL . $this->module . "/days_input_from_db-nct.tpl.php");
        $main_content1 = $main_content1->parse();

        $days_slot_html = '';
        if ($this->sessUserType == 'doctor') {
            $days_array = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');
            if (count($days_array)) {
                foreach ($days_array as $key => $val) {
                    $days_slot_html.= $this->create_slot_html('n',$val);
                }
            }
        }

        $is_image = get_image_url($usr['profile_photo'],"profile_photo",'th2_','n');

        $show_when_image_uploaded = 'hidden';
        if ($is_image != '') {
            $show_when_image_uploaded = 'remove-profile-image';
        }

	$clinic_banner_preview = '';

	if(!empty($usr['clinic_banner'])){

    		$banner = SITE_URL.'upload-nct/clinicBanner-nct/'.$usr['clinic_banner'];

    		$clinic_banner_preview = '
    		<div style="margin-top:10px;">
        		<img src="'.$banner.'" style="width:100%;max-width:900px;height:200px;object-fit:contain;background:#fff;border:1px solid #ddd;border-radius:6px;padding:5px;">
    		</div>';
	}


        $replace = array(
            '%old_profile_photo%'           => isset($usr['profile_photo']) ? $usr['profile_photo'] : '',
            "%uploaded_image%"              => get_image_url($usr['profile_photo'],"profile_photo",'th2_'),
	    '%clinic_banner_preview%' 	    => $clinic_banner_preview,
            "%first_name%"                  => isset($usr['first_name']) ? $usr['first_name'] : '',
            "%last_name%"                   => isset($usr['last_name']) ? $usr['last_name'] : '',
	    '%GSTIN%' 			    => isset($usr['gstin']) ? $usr['gstin'] : '',
            '%phone_no%'                    => isset($usr['phone_no']) ? $usr['phone_no'] : '',
	    "%whatsapp_number%" 	    => isset($usr['whatsapp_number']) ? $usr['whatsapp_number'] : '',
            '%phone_country_code%'          => isset($usr['phone_country_code']) ? $usr['phone_country_code'] : '',
            '%phone_iso2_code%'             => isset($usr['phone_iso2_code']) ? $usr['phone_iso2_code'] : '',
	    '%clinic_description%' 	    => isset($usr['clinic_description']) ? $usr['clinic_description'] : '',

            '%address%'                     => isset($usr['address']) ? $usr['address'] : '',
            '%latitude%'                    => isset($usr['latitude']) ? $usr['latitude'] : '',
            '%longitude%'                   => isset($usr['longitude']) ? $usr['longitude'] : '',
            '%city_name%'                   => isset($usr['city_name']) ? $usr['city_name'] : '',
            '%state_name%'                  => isset($usr['state_name']) ? $usr['state_name'] : '',
            '%country_name%'                => isset($usr['country_name']) ? $usr['country_name'] : '',
            '%zip_code%'                    => isset($usr['zip_code']) ? $usr['zip_code'] : '',
            '%get_type_of_doctors_list%'    => get_type_of_doctors_list($type_of_doctor_ids),
            '%get_specialties_list%'        => get_specialties_list($specialties_ids),
            '%other_profile_field_info%'    => $other_profile_field_info,
            '%days_slot_html%'              => $days_slot_html,
            '%show_when_image_uploaded%'    => $show_when_image_uploaded,
            '%clinic_name%'                 => isset($usr['clinic_name']) ? $usr['clinic_name'] : '',
            '%user_name_container%'         => $user_type == 'doctor' ? '' : 'hidden',
            '%clinic_name_container%'       => $user_type == 'clinic' ? '' : 'hidden',
        );
        return get_view(DIR_TMPL . $this -> module . "/" . $this -> module . ".tpl.php",$replace);
    }

    public function get_clinics_list_to_associate($selected_id = 0,$platform = 'web'){

        $html = '';
        $app_array = [];

        $userDetails = $this->db->pdoQuery("
            SELECT 
            c.*
            FROM tbl_users AS c
            WHERE c.user_type = 'clinic' AND c.clinic_name != '' AND c.status = 'a' AND c.email_verified = 'y' AND c.is_deactivated = 'n'
            GROUP BY c.id
            ")->results();

        if (count($userDetails) > 0) {

            if ($platform == 'web') {
                $main_content = new MainTemplater(DIR_TMPL . "select_option-nct.tpl.php");
                $main_content1 = $main_content->parse();
            }

            foreach ($userDetails as $key => $value) {

                $image = get_image_url($value['profile_photo'],"profile_photo",'th2_');
                if ($platform == 'app') {
                    $app_array[] = array(
                        'id'                    => (string) $value['id'],
                        'title'                 => $value['clinic_name'],
                        'image'                 => $image,
                        'is_selected'           => $selected_id == $value['id'] ? 'y' : 'n',
                    );
                } else{
                    $replace = array(
                        '%VALUE%'                   => $value['id'],
                        '%DISPLAY_VALUE%'           => $value['clinic_name'],
                        '%SELECTED%'                => $selected_id == $value['id'] ? 'selected' : '',
                    );
                    $html .= str_replace(array_keys($replace),array_values($replace),$main_content1);
                }
            }
        }

        if ($platform == 'app') {
            return $app_array;
        } else{
            return $html;
        }
    }

    public function create_slot_html($is_from_ajax = 'n',$day = ''){

        $main_content1 = new MainTemplater(DIR_TMPL . $this->module . "/days_input_from_db-nct.tpl.php");
        $main_content1 = $main_content1->parse();

        /* get next index */

        $days_slot_html = '';
        if ($is_from_ajax == 'y') {
            $index = isset($_POST['index']) && $_POST['index'] != '' ? $_POST['index'] : 1;

            /* default empty slot (no DB value yet) */

            $add_remove_icon = '<div class="col-md-2 time-fields '.$day.'">
            <button type="button" class="btn sm-btn-remove w-100 remove-slot" data-type="'.$day.'">
            <i class="fa-solid fa-minus"></i> Remove
            </button>
            </div>';

            $fields_replace = array(
                "%day_title%"                       => '',
                '%day%'                             => $day,
                '%index%'                           => $index,
                '%id%'                              => 0,
                '%day_to_time%'                     => '',
                '%day_from_time%'                   => '',
                '%hide_if_unavailable%'             => '',
                '%required%'                        => 'required',
                '%label_title%'                     => '',
                '%check_box_html%'                  => '&nbsp;',
                '%hide_if_not_first%'               => 'hidden',
                '%add_remove_icon%'                 => $add_remove_icon,
                '%temp%'                            => '_temp',
            );

            $days_slot_html = str_replace(array_keys($fields_replace), array_values($fields_replace), $main_content1);
        } else{
            $query = "SELECT t.*
            FROM tbl_users_time_slot AS t
            WHERE t.user_id = ".(int)$this->sessUserId." AND lower(day) = '".$day."'
            ORDER BY t.id ASC";
            $qrySel = $this->db->pdoQuery($query)->results();

            $days_slot_html.= '<div class="k-slot-container">';
            if (count($qrySel) > 0) {
                $i = 1;

                foreach ($qrySel as $key => $value) {
                    $hide_if_unavailable = $value['is_available'] == 'n' ? 'hidden' : '';

                    $check_box_html = $add_remove_icon = '';
                    if ($key == 0) {
                        $check_box_html = '<div class="form-check">
                        <input type="checkbox" name="available['.$day.'][]" class="dayofweek day-toggle form-check-input" data-day="'.$day.'" value="n" '.($value['is_available'] == 'n' ? 'checked="checked"' : '').' id="available['.$day.'][]" />

                        <label class="form-check-label" for="available['.$day.'][]">Mark As Unavailable</label>
                        </div>';

                        $add_remove_icon = '<div class="col-2  time-fields '.$day.' '.$hide_if_unavailable.' ">
                        <button type="button" class="btn sm-btn w-100 add-slot" data-type="'.$day.'">
                        <i class="fa-solid fa-plus"></i> Add
                        </button>
                        </div>';
                    } else{
                        $add_remove_icon = '<div class="col-md-2  time-fields '.$day.' '.$hide_if_unavailable.' ">
                        <button type="button" class="btn sm-btn-remove w-100 remove-slot" data-id="'.(int) $value['id'].'" data-type="'.$day.'">
                        <i class="fa-solid fa-minus"></i> Remove
                        </button>
                        </div>';
                    }

                    $fields_replace1 = array(
                        "%day_title%"               => ucfirst($day),
                        '%day%'                     => $day,
                        '%index%'                   => $i,
                        '%id%'                      => $value['id'],
                        '%day_to_time%'             => $value['to_time'],
                        '%day_from_time%'           => $value['from_time'],
                        '%hide_if_unavailable%'     => $hide_if_unavailable,
                        '%required%'                => $value['is_available'] == 'n' ? '' : 'required',
                        '%label_title%'             => $key == 0 ? MEND_SIGN.ucfirst($day).':' : '',
                        '%check_box_html%'          => $check_box_html,
                        '%add_remove_icon%'         => $add_remove_icon,
                        '%hide_if_not_first%'       => $key == 0 ? '' : 'hidden',
                        '%temp%'                    => '',
                    );

                    $days_slot_html.= str_replace(array_keys($fields_replace1), array_values($fields_replace1), $main_content1);
                    $i++;
                }
                
            } else{
                $i = 1;

                $check_box_html = '<div class="form-check">
                <input type="checkbox" name="available['.$day.'][]" class="dayofweek day-toggle form-check-input" data-day="'.$day.'" value="n" id="available['.$day.'][]" checked />

                <label class="form-check-label" for="available['.$day.'][]">Mark As Unavailable</label>
                </div>';

                $add_remove_icon = '<div class="col-2 time-fields hidden '.$day.' ">
                <button type="button" class="btn sm-btn w-100 add-slot" data-type="'.$day.'">
                <i class="fa-solid fa-plus"></i> Add
                </button>
                </div>';

                $fields_replace1 = array(
                    "%day_title%"                       => ucfirst($day),
                    '%day%'                             => $day,
                    '%index%'                           => $i,
                    '%id%'                              => 0,
                    '%day_to_time%'                     => '',
                    '%day_from_time%'                   => '',
                    '%hide_if_unavailable%'             => 'hidden',
                    '%required%'                        => '',
                    '%label_title%'                     => MEND_SIGN.ucfirst($day),
                    '%check_box_html%'                  => $check_box_html,
                    '%hide_if_not_first%'               => '',
                    '%add_remove_icon%'                 => $add_remove_icon,
                    '%temp%'                            => '_temp',
                );

                $days_slot_html.= str_replace(array_keys($fields_replace1), array_values($fields_replace1), $main_content1);
                $i++;
            }
            $days_slot_html.= '</div>';

        }

        if ($is_from_ajax == 'y') {
            $response['html'] = $days_slot_html;
            $response['index'] = $index;

            return $response;
        } else{
            return $days_slot_html;
        }
    }

}

?>
