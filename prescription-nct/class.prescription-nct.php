<?php

class Prescription {

    public function __construct($module = "", $reqData = array()){
        foreach ($GLOBALS as $key => $values) {
            $this->$key = $values;
        }

        $this->module = $module;
        $this->reqData = $reqData;
    }

    public function getPageContent(){

        global $db, $sessUserId;

	if(empty($sessUserId)){
    		$_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    		header("Location: ".SITE_URL."login");
    		exit;
	}

	$user = $db->select('tbl_users',['user_type'],['id'=>$sessUserId])->result();

	if(empty($user) || $user['user_type'] !== 'doctor'){
    		header("Location: ".SITE_URL."dashboard");
    		exit;
	}

        $appointment_id = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;

        if($appointment_id <= 0){
            return "Invalid Appointment";
        }

        $appointment = $db->pdoQuery("
            SELECT a.*, CONCAT(a.first_name,' ',a.last_name) AS patient_name,
                   u.gender,
                   u.date_of_birth
            FROM tbl_appointment a
            LEFT JOIN tbl_users u ON u.id = a.user_id
            WHERE a.id = ? AND a.doctor_id = ?
        ", [$appointment_id,$sessUserId])->result();

        if(empty($appointment)){
    		header("Location: ".SITE_URL."dashboard");
    		exit;
	}

        // AGE CALCULATION
	$age = '';
	$age_type = 'years';

	if(!empty($appointment['date_of_birth'])){

    		$dob = new DateTime($appointment['date_of_birth']);
    		$now = new DateTime();

    		$dob->setTime(0,0,0);
    		$now->setTime(0,0,0);

    		$diff = $now->diff($dob);

    		if ($diff->y > 0) {
        		$age = $diff->y;
        		$age_type = 'years';
    		} elseif ($diff->m > 0) {
        		$age = $diff->m;
        		$age_type = 'months';
    		} else {
        		$age = $diff->d;
        		$age_type = 'days';
    		}
	}

        $replace = array(
            "%PATIENT_NAME%"   => $appointment['patient_name'],
            "%PATIENT_AGE%"    => $age,
	    "%AGE_TYPE%"       => ucfirst($age_type),
            "%PATIENT_GENDER%" => ucfirst($appointment['gender']),
            "%DATE%"           => date('d M Y')
        );

        $filePath = DIR_TMPL.$this->module."/".$this->module.".tpl.php";

        $tpl = new MainTemplater($filePath);
        $tpl = $tpl->parse();

        return str_replace(array_keys($replace), array_values($replace), $tpl);
    }
}
