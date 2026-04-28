<?php

class Billing {

    public function __construct($module = "", $reqData = array()){
        foreach ($GLOBALS as $key => $values) {
            $this->$key = $values;
        }

        $this->module = $module;
        $this->reqData = $reqData;
    }

    public function getPageContent(){

    	global $db, $sessUserId;

	$bill_id = isset($_GET['bill_id']) ? intval($_GET['bill_id']) : 0;
	$payment_mode = isset($_GET['payment_mode']) ? 1 : 0;
	$appointment_id = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;

    	$filePath = DIR_TMPL.$this->module."/".$this->module.".tpl.php";

    	$clinic_id = $sessUserId;

    	$doctors = $db->pdoQuery("
        	SELECT id, CONCAT(first_name,' ',last_name) AS name, consultation_fees
        	FROM tbl_users
        	WHERE user_type='doctor'
        	AND parent_id=".$clinic_id."
        	ORDER BY first_name ASC
    	")->results();

    	$doctor_options = '';

    	if(!empty($doctors)){
        	foreach($doctors as $doc){
			$doctor_options .= '<option value="'.$doc['id'].'" data-fee="'.$doc['consultation_fees'].'">'.$doc['name'].' — ₹'.$doc['consultation_fees'].'</option>';
        	}
    	}

	$bill = array();

	if($bill_id > 0){

		$bill = $db->pdoQuery("SELECT * FROM tbl_bills WHERE id=".$bill_id." AND clinic_id=".$sessUserId."")->result();

	}

	$appointment = array();

	if($appointment_id > 0){

    		$appointment = $db->pdoQuery("
        		SELECT a.*,
            			CONCAT(a.first_name,' ',a.last_name) AS patient_name,
            			u.phone_no,
            			u.gender,
            			u.date_of_birth,
            			d.consultation_fees
        			FROM tbl_appointment a
        		LEFT JOIN tbl_users u ON u.id = a.user_id
        		LEFT JOIN tbl_users d ON d.id = a.doctor_id
        		WHERE a.id = ".$appointment_id."
    		")->result();

		if(empty($appointment)){
        		header("Location: ".SITE_URL."dashboard");
        		exit;
    		}

	}

	$bill_items = array();

	if($bill_id > 0){

		$bill_items = $db->pdoQuery("SELECT * FROM tbl_bill_items WHERE bill_id=".$bill_id."")->results();

	}

	$patientAge = '';
	$patientAgeType = 'years';

	if(!empty($appointment['date_of_birth'])){

    		$dob = new DateTime($appointment['date_of_birth']);
    		$now = new DateTime();

    		$dob->setTime(0,0,0);
    		$now->setTime(0,0,0);

    		$diff = $now->diff($dob);

    		if ($diff->y > 0) {
        		$patientAge = $diff->y;
        		$patientAgeType = 'years';
    		} elseif ($diff->m > 0) {
        		$patientAge = $diff->m;
        		$patientAgeType = 'months';
    		} else {
        		$patientAge = $diff->d;
        		$patientAgeType = 'days';
    		}
	}

    	$replace = array(
        	"%DOCTOR_OPTIONS%" 		=> $doctor_options,
		"%BILL_ID%" 			=> $bill_id,
		"%PAYMENT_MODE%" 		=> $payment_mode,

		"%PATIENT_NAME%" 		=> !empty($appointment) ? $appointment['patient_name'] : (isset($bill['patient_name']) ? $bill['patient_name'] : ''),
		"%PATIENT_PHONE%" 		=> !empty($appointment) ? $appointment['phone_no'] : (isset($bill['patient_phone']) ? $bill['patient_phone'] : ''),
		"%PATIENT_AGE%" 		=> $patientAge,
		"%PATIENT_AGE_TYPE%" 		=> $patientAgeType,
		"%AGE_TYPE_YEARS%" 		=> ($patientAgeType == 'years') ? 'selected' : '',
		"%AGE_TYPE_MONTHS%" 		=> ($patientAgeType == 'months') ? 'selected' : '',
		"%AGE_TYPE_DAYS%" 		=> ($patientAgeType == 'days') ? 'selected' : '',
		"%PATIENT_GENDER_MALE%" 	=> (!empty($appointment) && $appointment['gender'] == 'male') ? 'selected' : ((isset($bill['patient_gender']) && $bill['patient_gender'] == 'male') ? 'selected' : ''),
		"%PATIENT_GENDER_FEMALE%" 	=> (!empty($appointment) && $appointment['gender'] == 'female') ? 'selected' : ((isset($bill['patient_gender']) && $bill['patient_gender'] == 'female') ? 'selected' : ''),
		"%PATIENT_GENDER_OTHER%" 	=> (!empty($appointment) && $appointment['gender'] == 'other') ? 'selected' : ((isset($bill['patient_gender']) && $bill['patient_gender'] == 'other') ? 'selected' : ''),

		"%BILL_DATE%" 			=> !empty($appointment) ? $appointment['booking_date'] : (isset($bill['bill_date']) ? $bill['bill_date'] : ''),

		"%APPOINTMENT_DOCTOR_ID%" 	=> !empty($appointment) ? $appointment['doctor_id'] : '',
		"%APPOINTMENT_FEES%" 		=> !empty($appointment) ? $appointment['consultation_fees'] : '',
		"%APPOINTMENT_MODE%" 		=> $appointment_id > 0 ? 1 : 0,
		"%APPOINTMENT_ID%" 		=> $appointment_id,
    	);

    	$tpl = new MainTemplater($filePath);
    	$tpl = $tpl->parse();

    	return str_replace(array_keys($replace), array_values($replace), $tpl);

    }
}
?>
