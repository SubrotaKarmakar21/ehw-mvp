<?php
/*
error_reporting(E_ALL);
ini_set('display_errors',1);
*/

class AddAppointment {
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

    public function submitAppointmentForm(){
        //make php validations
        $response = array();
        $response['status'] = false;
        $response['message'] = something_went_wrong;
        $response['booking_id'] = 0;

        $booking_for            = isset($_REQUEST['booking_for']) && $_REQUEST['booking_for'] != '' ? trim($_REQUEST['booking_for']) : 'someone_else';
        $case_type              = isset($this->reqData['case_type']) ? $this->reqData['case_type'] : '';
        $booking_date           = isset($this->reqData['booking_date']) && $this->reqData['booking_date'] != '' ? date('Y-m-d',strtotime($this->reqData['booking_date'])) : '';
        $slot_id                = isset($this->reqData['slot_id']) ? $this->reqData['slot_id'] : 0;
        $remarks                = isset($this->reqData['remarks']) ? $this->reqData['remarks'] : '';
        $referral_or_community_code  = isset($this->reqData['referral_or_community_code']) ? $this->reqData['referral_or_community_code'] : '';

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

        $doctor_id = 0;

	if (isset($_POST['doctor_id']) && $_POST['doctor_id'] > 0) {
    		$doctor_id = (int)$_POST['doctor_id'];
	} elseif (isset($this->reqData['doctor_id']) && $this->reqData['doctor_id'] > 0) {
    		$doctor_id = (int)$this->reqData['doctor_id'];
	}

        if (!$this->dataOnly) {
            if ($this->sessUserType == 'doctor') {
                $doctor_id              = $this->sessUserId;
            }
        }
	if ($doctor_id <= 0){
		die("Invalid doctor selection.");
	}

        $slotInfo = $this->db->pdoQuery("SELECT from_time,to_time FROM tbl_users_time_slot WHERE user_id = ".(int) $doctor_id." AND is_available = 'y' AND id = ".(int) $slot_id ." ")->result();
        $from_time      = isset($slotInfo['from_time']) ? $slotInfo['from_time'] : '';
        $to_time        = isset($slotInfo['to_time']) ? $slotInfo['to_time'] : '';

        $booking_id = $user_id = 0;
        $step = isset($_REQUEST['step']) ? $_REQUEST['step'] : 0;

        if ($this->dataOnly) {
            $user_id = $_REQUEST['userId'];
            if ($step == 1) {
                $userType = getTableValue('tbl_users','user_type',array('id' => $user_id));
                $condition = ($booking_for != '' && $case_type != '' && $doctor_id > 0 && $booking_date != '' && $slot_id > 0 && $from_time != '' && $to_time != '' && $user_id > 0);
            } else{
                $booking_id = $_REQUEST['booking_id'];
                $condition = $booking_id > 0 && $first_name != '' && $last_name != '' && $gender != '' && $phone_no != '' && $address != '' && $date_of_birth != '' && $user_id > 0;
            }
        } else{
            $condition = ($case_type != '' && $doctor_id > 0 && $booking_date != '' && $slot_id > 0 && $from_time != '' && $to_time != '' && $first_name != '' && $last_name != '' && $phone_no > 0 && $gender != '' && $date_of_birth != '' && $address != '') || ($doctor_id > 0 && $this->sessUserType == 'clinic');
        }

        if ($condition) {

            if ($step == 1) {
                if ($booking_date < date('Y-m-d')) {
                    $response['status']     = false;
                    $response['message']    = 'Please select valid booking date.';
                    return $response;
                }
            }

            $booking_day = date('l', strtotime($booking_date));

            if ($this->dataOnly) {
                if ($referral_or_community_code != '') {
                    $this->db->update('tbl_users', array('referral_or_community_code' => $referral_or_community_code),array('id' => $user_id));
                }
            }

            if (!$this->dataOnly || ($step == 2)) {

                // Ensure patient exists
		$parent_id = getTableValue('tbl_users','parent_id',array('id' => $doctor_id));

		if ($parent_id <= 0) {
    			$parent_id = $doctor_id;
		}

		if (!$this->dataOnly) {

    			$user_id = 0;
			$patient_id = '';

			function normalizeName($first, $last){
    				$full = strtolower(trim($first . ' ' . $last));
    				$full = preg_replace('/[^a-z]/', ' ', $full);
    				$parts = array_filter(explode(' ', $full));
    				sort($parts);
    				return implode(' ', $parts);
			}

			$inputName = normalizeName($first_name, $last_name);

			// STEP 1: CHECK SAME CLINIC
			$clinic_id = getTableValue('tbl_users','parent_id',['id'=>$doctor_id]);
			if($clinic_id <= 0){
    				$clinic_id = $doctor_id;
			}

			$existingClinicPatients = $this->db->pdoQuery("
    				SELECT id, first_name, last_name, gender, patient_id
    				FROM tbl_users
    				WHERE user_type = 'patient'
    				AND parent_id = '".$clinic_id."'
    				AND phone_no = '".$phone_no."'
			")->results();

			foreach($existingClinicPatients as $p){

    				$dbName = normalizeName($p['first_name'], $p['last_name']);

    				if($dbName === $inputName && $p['gender'] == $gender){
        				$user_id = $p['id'];
       					$patient_id = $p['patient_id'];
        				break;
    				}
			}

			/* IF NOT FOUND → INSERT */
			if ($user_id == 0) {

    				$parent_id = getTableValue('tbl_users','parent_id',array('id' => $doctor_id));
    				if ($parent_id <= 0) {
        				$parent_id = $doctor_id;
    				}

				$existingGlobalPatient = null;

				$inputName = normalizeName($first_name, $last_name);

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

        			$insertPatient = array(
            				'user_type' => 'patient',
            				'email' => '',
            				'password' => md5(generatePassword()),
            				'parent_id' => (!empty($parent_id) && $parent_id > 0) ? (int)$parent_id : 0,
            				'first_name' => $first_name,
            				'last_name' => $last_name,
            				'phone_no' => $phone_no,
            				'phone_country_code' => isset($this->reqData['phone_country_code']) ? $this->reqData['phone_country_code'] : '',
            				'iso2_code' => isset($this->reqData['phone_iso2_code']) ? $this->reqData['phone_iso2_code'] : '',
            				'address' => isset($this->reqData['address']) ? $this->reqData['address'] : '',
            				'latitude' => isset($this->reqData['latitude']) ? $this->reqData['latitude'] : '',
            				'longitude' => isset($this->reqData['longitude']) ? $this->reqData['longitude'] : '',
            				'city_name' => isset($this->reqData['city_name']) ? $this->reqData['city_name'] : '',
            				'state_name' => isset($this->reqData['state_name']) ? $this->reqData['state_name'] : '',
            				'country_name' => isset($this->reqData['country_name']) ? $this->reqData['country_name'] : '',
            				'zip_code' => isset($this->reqData['zip_code']) ? $this->reqData['zip_code'] : '',
            				'gender' => $gender,
            				'date_of_birth' => $date_of_birth,
            				'created_date' => date('Y-m-d H:i:s'),
            				'updated_date' => date('Y-m-d H:i:s'),
            				'ip_address' => get_ip_address(),
            				'referral_or_community_code' => $referral_or_community_code,
        			);

        			$user_id = $this->db->insert('tbl_users', $insertPatient)->getLastInsertId();

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
    					['id' => $user_id, 'user_type' => 'patient']
				);
    			}
		}

		$bookingArray = array(
                    'user_id' => $user_id,
                    'doctor_id' => $doctor_id,
                    'booking_for' => $booking_for,
                    'case_type' => $case_type,
                    'booking_date' => $booking_date,
                    'booking_day' => $booking_day,
                    'from_time' => $from_time,
                    'to_time' => $to_time,
                    'remarks' => $remarks,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'phone_no' => $phone_no,
                    'phone_country_code' => isset($this->reqData['phone_country_code']) ? $this->reqData['phone_country_code'] : '',
                    'iso2_code' => isset($this->reqData['phone_iso2_code']) ? $this->reqData['phone_iso2_code'] : '',
                    'address' => isset($this->reqData['address']) ? $this->reqData['address'] : '',
                    'latitude' => isset($this->reqData['latitude']) ? $this->reqData['latitude'] : '',
                    'longitude' => isset($this->reqData['longitude']) ? $this->reqData['longitude'] : '',
                    'city_name' => isset($this->reqData['city_name']) ? $this->reqData['city_name'] : '',
                    'state_name' => isset($this->reqData['state_name']) ? $this->reqData['state_name'] : '',
                    'country_name' => isset($this->reqData['country_name']) ? $this->reqData['country_name'] : '',
                    'zip_code' => isset($this->reqData['zip_code']) ? $this->reqData['zip_code'] : '',
                    'gender' => $gender,
                    'date_of_birth' => $date_of_birth,
                    'referral_or_community_code' => $referral_or_community_code,
                    'created_date' => date('Y-m-d H:i:s'),
                    'updated_date' => date('Y-m-d H:i:s'),
                    'ip_address' => get_ip_address(),
                );

                $booking_id = $this->db->insert('tbl_appointment', $bookingArray)->getLastInsertId();

		/* WhatsApp Appointment Confirmation */

		$clinic_id = getTableValue('tbl_users','parent_id',array('id'=>$doctor_id));

		/* Determine clinic context */

		if ($this->sessUserType == 'clinic') {

    			$clinic_id = $this->sessUserId;

		} elseif ($this->sessUserType == 'doctor') {

    			$parent = getTableValue('tbl_users','parent_id',array('id'=>$this->sessUserId));

    			if($parent > 0){
        			$clinic_id = $parent;   // doctor belongs to clinic
    			} else{
        			$clinic_id = $this->sessUserId;   // independent doctor acts as clinic
    			}

		} else {

    			$clinic_id = 0;

		}

		/* Fetch clinic details */

		$clinic_name = getTableValue('tbl_users','clinic_name',array('id'=>$clinic_id));
		$doctor_first = getTableValue('tbl_users','first_name',array('id'=>$doctor_id));
		$doctor_last  = getTableValue('tbl_users','last_name',array('id'=>$doctor_id));
		$doctor_name  = $doctor_first.' '.$doctor_last;

		$patient_name = $first_name.' '.$last_name;

		$appointment_date = date('d M Y', strtotime($booking_date));
		$appointment_time = date('h:i A', strtotime($from_time));

		$message =
			"Hello ".$patient_name."!%0A%0A".
			"Your appointment has been booked successfully.%0A%0A".
			"Clinic: ".$clinic_name."%0A".
			"Doctor: ".$doctor_name."%0A".
			"Date: ".$appointment_date."%0A".
			"Time: ".$appointment_time."%0A%0A".
			"Regards%0A".$clinic_name;

		$whatsapp_url = "https://wa.me/91".$phone_no."?text=".$message;

	    	$response['whatsapp_link'] = $whatsapp_url;
            $response['status'] = true;
            $response['message'] = 'Appointment booked successfully.';
            $response['booking_id'] = $booking_id;
	   }
        } else {

            $response['status'] = false;
            $response['message'] = MSG_FILL_VALUE;
            return $response;

        }

        return $response;
    }

    public function getPageContent() {

        $select_doctor_option_when_clinic = '';
        if ($this->sessUserType == 'clinic') {
            $select_doctor_option_when_clinic = '<div class="form-group">
            <label for="doctor_id" class="form-label">'.MEND_SIGN.'Doctor: &nbsp;</label>
            <select class="form-control" name="doctor_id" id="doctor_id">
            <option value="">Select Doctor</option>
            '.get_doctors_by_clinic($this->sessUserId).'
            </select>
            </div>';
        }

        $replace = array(
            '%select_doctor_option_when_clinic%'        => $select_doctor_option_when_clinic,
        );
        return get_view(DIR_TMPL . $this -> module . "/" . $this -> module . ".tpl.php",$replace);
    }

}

?>
