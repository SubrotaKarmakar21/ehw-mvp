<?php
class AddDoctor {
    function __construct($module = "", $reqData = array()) {
        foreach ($GLOBALS as $key => $values) {
            $this -> $key = $values;

        }
        $this -> module = $module;

        $this->reqData = $reqData;
        $this->id = isset($this->reqData['id']) && $this->reqData['id'] != '' ? decryptIt($this->reqData['id']) : 0;

        //for web service
        $this->dataOnly = (isset($this->reqData['dataOnly']) && $this->reqData['dataOnly']==true)?true:false;
        $this->sessUserId = (isset($reqData['userId']) && $reqData['userId'] >0)?$reqData['userId']:$this -> sessUserId;
    }

    public function addEditDoctorsFrom(){
        //make php validations
        $response = array();
        $response['status'] = false;
        $response['message'] = 'error';

        $id                     = isset($this->reqData['id']) ? $this->reqData['id'] : 0;
        $email                  = isset($this->reqData['email_address']) ? $this->reqData['email_address'] : '';
        $first_name             = isset($this->reqData['first_name']) ? $this->reqData['first_name'] : '';
        $last_name              = isset($this->reqData['last_name']) ? $this->reqData['last_name'] : '';
        $phone_no               = isset($this->reqData['phone_no']) ? $this->reqData['phone_no'] : '';
        $gender                 = isset($this->reqData['gender']) ? $this->reqData['gender'] : '';
        $address                = isset($this->reqData['address']) ? $this->reqData['address'] : '';
        $type_of_doctor_id      = isset($this->reqData['type_of_doctor_id']) ? $this->reqData['type_of_doctor_id'] : array();
        $specialties_id         = isset($this->reqData['specialties_id']) ? $this->reqData['specialties_id'] : array();
	$practicing_since 	= trim($this->reqData['practicing_since'] ?? '');

	if($practicing_since == ''){
    		$db_date = NULL;
	}else{
    		list($month,$year) = explode('-', $practicing_since);
    		$db_date = sprintf("%04d-%02d-01",$year,$month);
	}

        $consultation_fees      = isset($this->reqData['consultation_fees']) && $this->reqData['consultation_fees'] != '' ? $this->reqData['consultation_fees'] : '';
	$doctor_description = trim($this->reqData['doctor_description'] ?? '');

        $updateArray = array(
            'first_name'                => $first_name,
            'last_name'                 => $last_name,
            'phone_no'                  => $phone_no,
            'phone_country_code'        => isset($this->reqData['phone_country_code']) ? $this->reqData['phone_country_code'] : '',
	    'iso2_code' 		=> isset($this->reqData['phone_iso2_code']) ? $this->reqData['phone_iso2_code'] : '',
            'address'                   => isset($this->reqData['address']) ? $this->reqData['address'] : '',
            'latitude'                  => isset($this->reqData['latitude']) ? $this->reqData['latitude'] : '',
            'longitude'                 => isset($this->reqData['longitude']) ? $this->reqData['longitude'] : '',
            'city_name'                 => isset($this->reqData['city_name']) ? $this->reqData['city_name'] : '',
            'state_name'                => isset($this->reqData['state_name']) ? $this->reqData['state_name'] : '',
            'country_name'              => isset($this->reqData['country_name']) ? $this->reqData['country_name'] : '',
            'zip_code'                  => isset($this->reqData['zip_code']) ? $this->reqData['zip_code'] : '',
            'practicing_since'          => ($db_date ? $db_date : NULL),
            'consultation_fees'         => $consultation_fees,
	    'doctor_description'	=> $doctor_description,
            'gender'                    => $gender,
        );

        if(trim($first_name) == null || trim($last_name) == null || trim($phone_no) == null || trim($address) == null || count($type_of_doctor_id) <= 0 || count($specialties_id) <= 0 || (trim($email) == null && $id <= 0)){
            $response['status'] = false;
            $response['message'] = MSG_FILL_VALUE;
            return $response;
        } else{
            //mk entry in contact us table

            $updateArray['updated_date'] = date('Y-m-d H:i:s');
            $updateArray['ip_address'] = get_ip_address();

            if ($id > 0) {
		if (!isset($_POST['new_upd_img_normal']) || count($_POST['new_upd_img_normal']) == 0) {
        		$existing_photo = getTableValue('tbl_users','profile_photo',array('id'=>$id));
        		$updateArray['profile_photo'] = $existing_photo;
    		}
		if(empty($updateArray['practicing_since'])){
        		unset($updateArray['practicing_since']);
    		}
                $this->db->update('tbl_users',$updateArray,array('id' => $id, 'parent_id' => $this->sessUserId));
                $user_id = $id;
            } else{
                $updateArray['email'] = $email;
                $updateArray['created_date'] = date('Y-m-d H:i:s');
                $updateArray['parent_id'] = $this->sessUserId;
		$updateArray['associated_to_existing_clinic'] = 'y';
                $updateArray['user_type'] = 'doctor';
                $password = generatePassword();
                $updateArray['password'] = md5($password);
                $updateArray['status'] = 'a';
                $updateArray['email_verified'] = 'y';
		// $updateArray['patient_uid'] = 'DOC'.time().rand(100,999);

		// Fetch logged-in clinic details
		$clinicDetails = $this->db->select('tbl_users', '*', array('id' => $this->sessUserId))->result();

		// Assign clinic name to doctor
		$updateArray['clinic_name'] = isset($clinicDetails['clinic_name']) ? $clinicDetails['clinic_name'] : '';
		$updateArray['profile_photo'] = 'default-doctor.png';

		if(empty($updateArray['practicing_since'])){
    			unset($updateArray['practicing_since']);
		}
		/*echo "<pre>";
		print_r($updateArray);
		echo "COUNT: ".count($updateArray);
		exit;*/

                $user_id = $this->db->insert('tbl_users',$updateArray)->getLastInsertId();

                $arrayCont = array(
                    'greetings'         => $first_name.' '.$last_name,
                    'email_address'     => $email,
                    'password'          => $password,
                );
                $array = generateEmailTemplate('user_register_notify_password', $arrayCont);

                sendEmailAddress($email, $array['subject'], $array['message']);
            }

            $upload_dir = DIR_UPD_PROFILE_IMAGE;
            if(!file_exists($upload_dir)){
                mkdir($upload_dir,0777);
            }

	    if (!empty($_POST['new_upd_img_normal'][0])) {
                $old_image = getTableValue('tbl_users','profile_photo',array('id' => $user_id));
                if ($old_image != '') {
                    if (file_exists($upload_dir.$old_image)) {
                        unlink($upload_dir.$old_image);
                    }

                    if (file_exists($upload_dir.'th1_'.$old_image)) {
                        unlink($upload_dir.'th1_'.$old_image);
                    }

                    if (file_exists($upload_dir.'th2_'.$old_image)) {
                        unlink($upload_dir.'th2_'.$old_image);
                    }
                }

                foreach ($_POST['new_upd_img_normal'] as $key => $value) {
                    if (file_exists(DIR_UPD_TEMP_FILES.$value)) {
                        copy(DIR_UPD_TEMP_FILES.$value, $upload_dir.$value);
                        unlink(DIR_UPD_TEMP_FILES.$value);
                    }

                    if (file_exists(DIR_UPD_TEMP_FILES.'th1_'.$value)) {
                        copy(DIR_UPD_TEMP_FILES.'th1_'.$value, $upload_dir.'th1_'.$value);
                        unlink(DIR_UPD_TEMP_FILES.'th1_'.$value);
                    }

                    if (file_exists(DIR_UPD_TEMP_FILES.'th2_'.$value)) {
                        copy(DIR_UPD_TEMP_FILES.'th2_'.$value, $upload_dir.'th2_'.$value);
                        unlink(DIR_UPD_TEMP_FILES.'th2_'.$value);
                    }
                    $this->db->update('tbl_users',array('profile_photo' => $value),array("id"=>$user_id));
                }
            }

            /*Insert/update & delete Type Of DOctors ============================================== */
            $existing_type_of_doctor_ids = $this->db->pdoQuery("SELECT type_of_doctor_id 
                FROM tbl_users_doctor_type
                WHERE user_id = ".$user_id." ")->results();

            $existing_type_of_doctor_ids = array_map(function($row) {
                return $row['type_of_doctor_id'];
            }, $existing_type_of_doctor_ids);
            $type_of_doctor_to_delete = array_diff($existing_type_of_doctor_ids, $type_of_doctor_id);

            if (count($type_of_doctor_to_delete) > 0) {
                foreach ($type_of_doctor_to_delete as $key => $value) {
                    $this->db->delete('tbl_users_doctor_type',array('user_id' => $user_id,'type_of_doctor_id' => $value));
                }
            }

            if (count($type_of_doctor_id) > 0) {
                foreach ($type_of_doctor_id as $key => $value) {
                    $is_exist = (int) getTableValue('tbl_users_doctor_type','id',array('user_id' => $user_id,'type_of_doctor_id' => $value));

                    $subCatIns = array(
                        'created_date'              => date('Y-m-d H:i:s'),
                    );

                    if ($is_exist <= 0) {
                        $subCatIns['user_id']               = $user_id;
                        $subCatIns['type_of_doctor_id']     = $value;

                        $this->db->insert('tbl_users_doctor_type',$subCatIns);
                    }
                }
            }
            /*Insert/update & delete Type Of DOctors ============================================== */

            /*Insert/update & delete specialties ============================================== */
            $existing_specialties_ids = $this->db->pdoQuery("SELECT specialties_id
                FROM tbl_users_specialties
                WHERE user_id = ".$user_id." ")->results();

            $existing_specialties_ids = array_map(function($row) {
                return $row['specialties_id'];
            }, $existing_specialties_ids);
            $specialties_id_to_delete = array_diff($existing_specialties_ids, $specialties_id);


            if (count($specialties_id_to_delete) > 0) {
                foreach ($specialties_id_to_delete as $key => $value) {
                    $this->db->delete('tbl_users_specialties',array('user_id' => $user_id,'specialties_id' => $value));
                }
            }

            if (count($specialties_id) > 0) {
                foreach ($specialties_id as $key => $value) {
                    $is_exist = (int) getTableValue('tbl_users_specialties','id',array('user_id' => $user_id,'specialties_id' => $value));

                    $subCatIns = array(
                        'created_date'              => date('Y-m-d H:i:s'),
                    );

                    if ($is_exist <= 0) {
                        $subCatIns['user_id']               = $user_id;
                        $subCatIns['specialties_id']    = $value;

                        $this->db->insert('tbl_users_specialties',$subCatIns);
                    }
                }
            }
            /*Insert/update & delete specialties ============================================== */

            $seasonal_price_action          = isset($this->reqData['seasonal_price_action']) ? $this->reqData['seasonal_price_action'] : [];
            $seasonal_price_action_temp     = isset($this->reqData['seasonal_price_action_temp']) ? $this->reqData['seasonal_price_action_temp'] : [];
            $db_value_id                    = isset($this->reqData['db_value_id']) ? $this->reqData['db_value_id'] : [];
            $available                      = isset($this->reqData['available']) ? $this->reqData['available'] : [];

            if (count($seasonal_price_action) > 0) {
                foreach ($seasonal_price_action as $key => $value) {
                    $arr = explode('_', $value);
                    $new_day = isset($arr[0]) ? $arr[0] : '';
                    $new_index = isset($arr[1]) ? $arr[1] : '';

                    $slotData = [];
                    $dbId = isset($db_value_id[$new_day][$key]) ? $db_value_id[$new_day][$key] : 0;

                // echo $dbId.'__'.$new_day.'__'.$new_index;exit;
                // if value set che means is_active -> n che

                    if (isset($available[$new_day][0]) && $available[$new_day][0] == 'n') {
                    // insert with is_available -> n
                        $this->db->pdoQuery("DELETE FROM tbl_users_time_slot WHERE user_id = ".(int) $user_id." AND day = '".$new_day."' ");
                    } else {
                        if (isset($this->reqData[$new_day.'_from_'.$new_index]) && $this->reqData[$new_day.'_from_'.$new_index] != '' && $this->reqData[$new_day.'_from_'.$new_index] != '') {
                            $slotData['from_time'] =   $this->reqData[$new_day.'_from_'.$new_index];
                        }
                        if (isset($this->reqData[$new_day.'_to_'.$new_index]) && $this->reqData[$new_day.'_to_'.$new_index] != '' && $this->reqData[$new_day.'_to_'.$new_index] != '') {
                            $slotData['to_time'] =   $this->reqData[$new_day.'_to_'.$new_index];
                        }

                        $slotData['is_available']   = 'y';
                        $slotData['day']            = $new_day;
                        $slotData['user_id']        = $user_id;
                        $slotData['updated_date']   = date('Y-m-d H:i:s');

                        $newId = $this->db->update('tbl_users_time_slot', $slotData,array('id' => $dbId));
                    }
                }
            }

            if (count($seasonal_price_action_temp) > 0) {
                // echo "123";exit;
                foreach ($seasonal_price_action_temp as $key => $value) {
                    $arr = explode('_', $value);
                    $new_day = isset($arr[0]) ? $arr[0] : '';
                    $new_index = isset($arr[1]) ? $arr[1] : '';

                    $slotData = [];

                    if (isset($available[$new_day][0]) && $available[$new_day][0] == 'n') {
                    // insert with is_available -> n
                        $this->db->pdoQuery("DELETE FROM tbl_users_time_slot WHERE user_id = ".(int) $user_id." AND day = '".$new_day."' ");
                    } else {
                        if (isset($this->reqData[$new_day.'_from_'.$new_index]) && $this->reqData[$new_day.'_from_'.$new_index] != '' && $this->reqData[$new_day.'_from_'.$new_index] != '') {
                            $slotData['from_time'] =   $this->reqData[$new_day.'_from_'.$new_index];
                        }
                        if (isset($this->reqData[$new_day.'_to_'.$new_index]) && $this->reqData[$new_day.'_to_'.$new_index] != '' && $this->reqData[$new_day.'_to_'.$new_index] != '') {
                            $slotData['to_time'] =   $this->reqData[$new_day.'_to_'.$new_index];
                        }

                        $slotData['is_available']   = 'y';
                        $slotData['day']            = $new_day;
                        $slotData['user_id']        = $user_id;
                        $slotData['updated_date']   = date('Y-m-d H:i:s');
                        $slotData['created_date']   = date('Y-m-d H:i:s');
                        $newId = $this->db->insert('tbl_users_time_slot', $slotData)->getLastInsertId();
                    }
                }
            }

	    // AUTO CREATE CONSULTATION SERVICE
	    $clinic_id = getTableValue('tbl_users','parent_id',['id'=>$user_id]);
	    $doctor = $this->db->select('tbl_users',
            ['first_name','last_name','consultation_fees'],
            ['id'=>$user_id]
            )->result();

	    $doctor_name = trim($doctor['first_name'].' '.$doctor['last_name']);
	    $doctor_name = preg_replace('/^Dr\.?\s*/i','',$doctor_name);
            $doctor_name = 'Dr. '.$doctor_name;
            $consultation_fee = $doctor['consultation_fees'] ?? 0;

	    // Use INSERT ... ON DUPLICATE KEY UPDATE
	    $this->db->pdoQuery("
    		INSERT INTO tbl_clinic_services
    		(clinic_id, doctor_id, category_id, service_name, price, status, created_date, updated_date)
    		VALUES
    		(?,?,?,?,?,'a',NOW(),NOW())
    		ON DUPLICATE KEY UPDATE
    		service_name = VALUES(service_name),
    		price = VALUES(price),
    		updated_date = NOW()
	    ",[
    	    	$clinic_id,
            	$user_id,
    	    	1,
    	   	'Consultation - '.$doctor_name, $consultation_fee
	    ]);

            $response['status'] = true;
            $response['message'] = "Doctor's details successfully updated.";
            return $response;
        }
    }

    public function getPageContent() {

        $usr = $this->db->select("tbl_users", array("*"), array("id" => $this->id, "parent_id" => $this->sessUserId))->result();

	if($this->id > 0 && empty($usr)){
    		header("Location: ".SITE_URL."my-doctors");
    		exit;
	}

        $user_id = isset($usr['id']) ? $usr['id'] : 0;
        $user_type = isset($usr['user_type']) ? $usr['user_type'] : '';

        $practicing_since_display = '';
        $type_of_doctor_ids = $specialties_ids = [];
        if ($user_id > 0) {
            if (isset($usr['practicing_since']) && !empty($usr['practicing_since'])) {
                $practicing_since_display = date("m-Y", strtotime($usr['practicing_since']));
            }
            $type_of_doctors = $this->db->pdoQuery("
                SELECT cap.type_of_doctor_id
                FROM tbl_users_doctor_type AS cap
                WHERE cap.user_id = ".(int) $user_id." ORDER BY cap.id ASC ")->results();
            $type_of_doctor_ids = array_map(function($row) {
                return $row['type_of_doctor_id'];
            }, $type_of_doctors);

            $specialties = $this->db->pdoQuery("
                SELECT cap.specialties_id
                FROM tbl_users_specialties AS cap
                WHERE cap.user_id = ".(int) $user_id." ORDER BY cap.id ASC ")->results();
            $specialties_ids = array_map(function($row) {
                return $row['specialties_id'];
            }, $specialties);
        }

        /*get slot info to 7 days*/
        $main_content1 = new MainTemplater(DIR_TMPL . $this->module . "/days_input_from_db-nct.tpl.php");
        $main_content1 = $main_content1->parse();

        $days_slot_html = '';
        $days_array = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');
        if (count($days_array)) {
            foreach ($days_array as $key => $val) {
                $days_slot_html.= $this->create_slot_html('n',$val,$user_id);
            }
        }

        $profile_photo = isset($usr['profile_photo']) ? $usr['profile_photo'] : '';
        $is_image = get_image_url($profile_photo,"profile_photo",'th2_','n');

        $show_when_image_uploaded = 'hidden';
        if ($is_image != '') {
            $show_when_image_uploaded = 'remove-profile-image';
        }

        $gender = isset($usr['gender']) ? $usr['gender'] : '';

	/*echo "<pre>";
	print_r($usr['doctor_description']);
	exit;*/

        $replace = array(
            '%id%'                          => $this->id,
            '%disabled%'                    => $this->id > 0 ? 'disabled' : '',
            '%old_profile_photo%'           => isset($usr['profile_photo']) ? $usr['profile_photo'] : '',
            "%uploaded_image%"              => get_image_url($profile_photo,"profile_photo",'th2_'),
            "%first_name%"                  => isset($usr['first_name']) ? $usr['first_name'] : '',
            "%last_name%"                   => isset($usr['last_name']) ? $usr['last_name'] : '',
            '%phone_no%'                    => isset($usr['phone_no']) ? $usr['phone_no'] : '',
            '%email%'                       => isset($usr['email']) ? $usr['email'] : '',
            '%gender_male%'                 => $gender == 'male' ? 'checked' : '',
            '%gender_female%'               => $gender == 'female' ? 'checked' : '',
	    '%gender_other%'  		    => $gender == 'other' ? 'checked' : '',
            '%phone_country_code%'          => isset($usr['phone_country_code']) ? $usr['phone_country_code'] : '',
            '%phone_iso2_code%'             => isset($usr['phone_iso2_code']) ? $usr['phone_iso2_code'] : '',
            '%address%'                     => isset($usr['address']) ? $usr['address'] : '',
            '%latitude%'                    => isset($usr['latitude']) ? $usr['latitude'] : '',
            '%longitude%'                   => isset($usr['longitude']) ? $usr['longitude'] : '',
            '%city_name%'                   => isset($usr['city_name']) ? $usr['city_name'] : '',
            '%state_name%'                  => isset($usr['state_name']) ? $usr['state_name'] : '',
            '%country_name%'                => isset($usr['country_name']) ? $usr['country_name'] : '',
            '%zip_code%'                    => isset($usr['zip_code']) ? $usr['zip_code'] : '',
            '%get_type_of_doctors_list%'    => get_type_of_doctors_list($type_of_doctor_ids),
            '%get_specialties_list%'        => get_specialties_list($specialties_ids),
            '%consultation_fees%'           => isset($usr['consultation_fees']) ? $usr['consultation_fees'] : '',
	    "%doctor_description%" 	    => isset($usr['doctor_description']) ? $usr['doctor_description'] : '',
            '%practicing_since%'            => $practicing_since_display,
            '%days_slot_html%'              => $days_slot_html,
            '%show_when_image_uploaded%'    => $show_when_image_uploaded,
        );
        return get_view(DIR_TMPL . $this -> module . "/" . $this -> module . ".tpl.php",$replace);
    }

    public function create_slot_html($is_from_ajax = 'n',$day = '',$user_id = 0){

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
            WHERE t.user_id = ".(int)$user_id." AND lower(day) = '".$day."'
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
