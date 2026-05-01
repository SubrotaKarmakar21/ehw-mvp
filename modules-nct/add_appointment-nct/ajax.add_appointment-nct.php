<?php
$reqAuth = false;
$module = 'add_appointment-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.add_appointment-nct.php";

$winTitle = 'Add an Appointment ' . SITE_NM;
$headTitle = 'Add an Appointment' . SITE_NM;
$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));
$obj = new AddAppointment('',0,$_POST);

$response['status'] = '0';
$response['msg'] = "undefined";
//Oops! something went wrong. Please try again later.

/* ===============================
   PATIENT SEARCH
================================= */

if(isset($_POST['action']) && $_POST['action']=="searchPatient"){

        $keyword = trim($_POST['keyword']);

        $clinic_id = $_SESSION['sessUserId'];

	$patients = $db->pdoQuery("
        	SELECT id, first_name, last_name, phone_no, gender, date_of_birth, address
        	FROM tbl_users
        	WHERE user_type='patient'
        	AND parent_id = ?
        	AND (
            		first_name LIKE ?
            		OR last_name LIKE ?
            		OR phone_no LIKE ?
        	)
        	ORDER BY first_name ASC
       		LIMIT 10
	", [
	$clinic_id,
        "%".$keyword."%",
        "%".$keyword."%",
        "%".$keyword."%"
])->results();

        echo json_encode($patients);
        exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'get_time_slot') {

	$doctor_id 			= isset($_POST['doctor_id']) ? $_POST['doctor_id'] : 0;
	$booking_date 		= isset($_POST['booking_date']) ? $_POST['booking_date'] : '';

	$responseArray = get_time_slots('web',$doctor_id,$booking_date);

	$is_available = isset($responseArray[0]['is_available']) ? $responseArray[0]['is_available'] : 'n';

	$html = '';
	if ($is_available == 'y') {
		$slot = isset($responseArray[0]['slot']) ? $responseArray[0]['slot'] : [];

		if (count($slot) > 0) {
			foreach ($slot as $key => $value) {
				$replace = array(
					'%time%'			=> $value['time'],
					'%id%'				=> $value['id'],
					'%checked%'			=> '',
				);
				$html.=get_view(DIR_TMPL . $module . "/time_slot-nct.tpl.php",$replace);
			}
		}
	}

	$response['html']       = $html;

	if(isset($_POST['action']) && $_POST['action']=="method"){

        	$method = $_POST['method'];

        	if(method_exists($obj,$method)){

                	$response = $obj->$method();

                	echo json_encode($response);
                	exit;
        	}
	}

	echo json_encode($response);
	exit;
}

/* ===== APPOINTMENT SUBMIT ===== */

if(isset($_POST['action']) && $_POST['action']=="method"){

    	$method = $_POST['method'];

    	if(method_exists($obj,$method)){

        	$response = $obj->$method();

        	echo json_encode($response);
        	exit;
    	}
}

echo json_encode($response);
exit ;
?>
