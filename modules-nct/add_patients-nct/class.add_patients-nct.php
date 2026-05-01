<?php
class AddPatients {
    function __construct($module = "", $id = 0, $reqData = array()) {
        foreach ($GLOBALS as $key => $values) {
            $this -> $key = $values;

        }
        $this -> module = $module;
        $this -> id = $id;
        $this->reqData = $reqData;

        $this->id = isset($this->reqData['id']) && $this->reqData['id'] != '' ? decryptIt($this->reqData['id']) : 0;

        //for web service
        $this->dataOnly = (isset($this->reqData['dataOnly']) && $this->reqData['dataOnly']==true)?true:false;
        $this->sessUserId = (isset($reqData['userId']) && $reqData['userId'] >0)?$reqData['userId']:$this -> sessUserId;
    }

    public function submitAddPatientForm(){
        $response = array();
        $response['status'] = false;
        $response['message'] = 'error';

        $id                     = isset($this->reqData['id']) ? $this->reqData['id'] : 0;
	$doctor_clinic_id 	= $this->sessUserId;
        $first_name             = isset($this->reqData['first_name']) ? $this->reqData['first_name'] : '';
        $last_name              = isset($this->reqData['last_name']) ? $this->reqData['last_name'] : '';
        $phone_no               = isset($this->reqData['phone_no']) ? $this->reqData['phone_no'] : '';
        $gender                 = isset($this->reqData['gender']) ? $this->reqData['gender'] : '';
	$age 			= isset($this->reqData['age']) ? (int)$this->reqData['age'] : 0;
	$age_type 		= isset($this->reqData['age_type']) ? $this->reqData['age_type'] : 'years';

	$date_of_birth = '';

	if ($age > 0) {

    		if ($age_type == 'years') {
        		$date_of_birth = date('Y-m-d', strtotime("-$age years"));
    		}
    		elseif ($age_type == 'months') {
        		$date_of_birth = date('Y-m-d', strtotime("-$age months"));
    		}
    		elseif ($age_type == 'days') {
        		$date_of_birth = date('Y-m-d', strtotime("-$age days"));
    		}
	}

        $address                = isset($this->reqData['address']) ? $this->reqData['address'] : '';
        // $case_type              = isset($this->reqData['case_type']) ? $this->reqData['case_type'] : '';
        // $booking_date          = isset($this->reqData['booking_date']) && $this->reqData['booking_date'] != '' ? date('Y-m-d',strtotime($this->reqData['booking_date'])) : '';
        // $remarks                = isset($this->reqData['remarks']) ? $this->reqData['remarks'] : '';
        // $slot_id                = isset($this->reqData['slot_id']) ? $this->reqData['slot_id'] : 0;
        $response = array();
        $response['status'] = false;
        $response['message'] = something_went_wrong;
        $response['booking_id'] = 0;

        // $slotInfo = $this->db->pdoQuery("SELECT from_time,to_time FROM tbl_users_time_slot WHERE user_id = ".(int) $doctor_clinic_id." AND is_available = 'y' AND id = ".(int) $slot_id ." ")->result();
        // $from_time      = isset($slotInfo['from_time']) ? $slotInfo['from_time'] : '';
        // $to_time        = isset($slotInfo['to_time']) ? $slotInfo['to_time'] : '';

	function normalizeName($first, $last){
    		$full = strtolower(trim($first . ' ' . $last));
    		$full = preg_replace('/[^a-z]/', ' ', $full);
    		$parts = array_filter(explode(' ', $full));
    		sort($parts);
    		return implode(' ', $parts);
	}

        if ($first_name != '' && $doctor_clinic_id > 0 && $last_name != '' && $phone_no > 0 && $gender != '' && $date_of_birth != '' && $address != '') {

	    $inputName = normalizeName($first_name, $last_name);

	    $existingClinicPatients = $this->db->pdoQuery("
    		SELECT id, first_name, last_name, gender, patient_id
    		FROM tbl_users
    		WHERE user_type = 'patient'
    		AND parent_id = '".$doctor_clinic_id."'
    		AND phone_no = '".$phone_no."'
	    ")->results();

	    $existingClinicMatch = false;

	    foreach($existingClinicPatients as $p){

    		$dbName = normalizeName($p['first_name'], $p['last_name']);

    		if($dbName === $inputName && $p['gender'] == $gender){
        		$existingClinicMatch = true;
        		break;
    		}
	    }

            if ($id > 0 || !$existingClinicMatch) {
                $insertPatient = array(
		    'first_name'		=> $first_name,
                    'last_name'                 => $last_name,
                    'phone_no'			=> $phone_no,
		    'address'                   => isset($this->reqData['address']) ? $this->reqData['address'] : '',
                    'latitude'                  => isset($this->reqData['latitude']) ? $this->reqData['latitude'] : '',
                    'longitude'                 => isset($this->reqData['longitude']) ? $this->reqData['longitude'] : '',
                    'city_name'                 => isset($this->reqData['city_name']) ? $this->reqData['city_name'] : '',
                    'state_name'                => isset($this->reqData['state_name']) ? $this->reqData['state_name'] : '',
                    'country_name'              => isset($this->reqData['country_name']) ? $this->reqData['country_name'] : '',
                    'zip_code'                  => isset($this->reqData['zip_code']) ? $this->reqData['zip_code'] : '',
                    'gender'                    => $gender,
                    'date_of_birth'             => $date_of_birth,
                    "updated_date"              => date('Y-m-d H:i:s'),
                    'ip_address'                => get_ip_address(),
                );

                if ($id <= 0) {
                    $insertPatient['first_name']            = $first_name;
                    $insertPatient['phone_no']              = $phone_no;
                    $insertPatient['phone_country_code']    = isset($this->reqData['phone_country_code']) ? $this->reqData['phone_country_code'] : '';
                    $insertPatient['iso2_code']             = isset($this->reqData['phone_iso2_code']) ? $this->reqData['phone_iso2_code'] : '';
                }

                if ($id > 0) {
		    $existingPatient = $this->db->select('tbl_users',['id'],['id' => $id,'parent_id' => $this->sessUserId,'user_type' => 'patient'])->result();

    		    if(empty($existingPatient)){
        		echo json_encode(['status' => false,'message' => 'Unauthorized access']);
        		exit;
    		    }
                    $user_id = $this->db->update('tbl_users', $insertPatient,array('id' => $id, 'parent_id' => $this->sessUserId));
                } else{
                    $insertPatient['user_type']             = 'patient';
                    $insertPatient['password']              = md5(generatePassword());
                    $insertPatient['parent_id']             = $doctor_clinic_id;
                    $insertPatient['created_date']          = date('Y-m-d H:i:s');
		    $insertPatient['user_type']             = 'patient';

                    $user_id = $this->db->insert('tbl_users', $insertPatient)->getLastInsertId();

		    /* GLOBAL PATIENT MATCH */

		    $existingGlobalPatient = null;

		    $existingPatients = $this->db->pdoQuery("
    			SELECT patient_id, first_name, last_name, gender
    			FROM tbl_users
    			WHERE user_type = 'patient'
    			AND phone_no = '".$phone_no."'
		    ")->results();

		    foreach($existingPatients as $ep){

    			$dbName = normalizeName($ep['first_name'], $ep['last_name']);

    			if($dbName === $inputName && $ep['gender'] == $gender){
        			$existingGlobalPatient = $ep;
        			break;
    			}
		    }

    		    /* Generate Patient ID */

		    if(!empty($existingGlobalPatient) && !empty($existingGlobalPatient['patient_id'])){

    			// SAME PATIENT → reuse patient_id
    			$patient_id = $existingGlobalPatient['patient_id'];

		    } else {

    			// NEW PATIENT → generate new patient_id
    			$year = date('y');
    			$serial = str_pad($user_id, 6, '0', STR_PAD_LEFT);
    			$patient_id = $year . '-' . $serial;
		    }

		    $this->db->update(
    			'tbl_users',
    			['patient_id' => $patient_id],
    			['id' => $user_id]
		    );

		    /* WhatsApp Greeting */

		    $clinic_name = getTableValue('tbl_users','clinic_name',array('id'=>$doctor_clinic_id));

		    $patient_name = $first_name.' '.$last_name;

		    $message = "Hello ".$patient_name."!%0A%0A".
           	    "Welcome to ".$clinic_name.".%0A%0A".
           	    "Thank you for choosing us for your healthcare.%0A%0A".
           	    "Regards%0A".$clinic_name;

		    $response['whatsapp_link'] = "https://wa.me/91".$phone_no."?text=".$message;
                }

                $response['status']     = true;
                $response['message']    = 'Patient details successfully added.';
                $response['user_id'] = $user_id;
            } else{
                $response['status']     = false;
                $response['message']    = 'User already added.';
            }
        } else{
            $response['status'] = false;
            $response['message'] = MSG_FILL_VALUE;
            return $response;
        }

        echo json_encode($response);
	exit;
    }

    public function getPageContent() {

        $usr = $this->db->select("tbl_users", array("*"), array("id" => $this->id, "parent_id" => $this->sessUserId))->result();

	if($this->id > 0 && empty($usr)){
    		header("Location: ".SITE_URL."my-patients");
    		exit;
	}

        $user_id        = isset($usr['id']) ? $usr['id'] : 0;
        $user_type      = isset($usr['user_type']) ? $usr['user_type'] : '';
        $gender         = isset($usr['gender']) ? $usr['gender'] : '';
	$age = '';
	$age_type = 'years';

	if(isset($usr['date_of_birth']) && $usr['date_of_birth'] != ''){

    		$dob = new DateTime($usr['date_of_birth']);
    		$now = new DateTime();
    		$diff = $now->diff($dob);

    		if($diff->y > 0){
        		$age = $diff->y;
        		$age_type = 'years';
    		}
    		elseif($diff->m > 0){
        		$age = $diff->m;
        		$age_type = 'months';
    		}
    		else{
        		$age = $diff->d;
        		$age_type = 'days';
    		}
	}

        $replace = array(
            '%id%'                          => $this->id,
            '%doctor_clinic_id%'            => $this->sessUserId,
            '%disabled%'                    => $this->id > 0 ? 'disabled' : '',
	    '%readonly%' 		    => $this->id > 0 ? 'readonly' : '',
	    '%name_readonly%' 		    => '',
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
	    '%age%' 			    => $age,
	    '%age_type_years%'  	    => $age_type == 'years' ? 'selected' : '',
	    '%age_type_months%' 	    => $age_type == 'months' ? 'selected' : '',
	    '%age_type_days%'   	    => $age_type == 'days' ? 'selected' : '',
            '%button_text%'                 => $this->id <= 0 ? 'Add' : 'Edit',
        );

        return get_view(DIR_TMPL . $this -> module . "/" . $this -> module . ".tpl.php",$replace);
    }
}

?>
