<?php
$reqAuth = false;
$module = 'add_patients-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.add_patients-nct.php";

$winTitle = 'Add Patient ' . SITE_NM;
$headTitle = 'Add Patient' . SITE_NM;
$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));
$obj = new AddPatients();

$response['status'] = '0';
$response['msg'] = "undefined";
//Oops! something went wrong. Please try again later.

if (isset($_POST['action']) && $_POST['action'] == 'get_time_slot') {
	
	$doctor_clinic_id = isset($_POST['doctor_clinic_id']) ? $_POST['doctor_clinic_id'] : 0;
	$booking_date = isset($_POST['booking_date']) ? $_POST['booking_date'] : '';


	$responseArray = get_time_slots('web',$doctor_clinic_id,$booking_date);

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

	echo json_encode($response);
	exit;

}

echo json_encode($response);
exit ;
?>