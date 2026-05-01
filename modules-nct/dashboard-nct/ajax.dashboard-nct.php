<?php
$reqAuth = true;
$module = 'dashboard-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.dashboard-nct.php";

$winTitle = 'Dashboard ' . SITE_NM;
$headTitle = 'Dashboard' . SITE_NM;
$metaTag = getMetaTags(array(
	"description" => $winTitle,
	"keywords" => $headTitle,
	"author" => AUTHOR
));
$obj = new Dashboard($module,$_REQUEST);

$response['status'] = '0';
$response['msg'] = "undefined";
//Oops! something went wrong. Please try again later.

if(isset($_POST['action']) && $_POST['action'] == 'get_my_upcoming_appointment'){
    $page = isset($_REQUEST['page'])?$_REQUEST['page']:0;

    $page = isset($page)?(int)$page:0;

    $content = $obj->getMyAppointmentList('upcoming',$sessUserId,$page,true);
    echo json_encode($content);
    exit;
} else if(isset($_POST['action']) && $_POST['action'] == 'get_my_past_appointment'){
    $page = isset($_REQUEST['page'])?$_REQUEST['page']:0;

    $page = isset($page)?(int)$page:0;

    $content = $obj->getMyAppointmentList('past',$sessUserId,$page,true);
    echo json_encode($content);
    exit;
} else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'export_excel') {

    $data_type = isset($_REQUEST['data_type']) ? $_REQUEST['data_type'] : '';

    if ($data_type == 'past') {
        $where = " AND a.booking_date < '".date("Y-m-d")."' ";
    } else{
        $where = " AND a.booking_date >= '".date("Y-m-d")."' ";
    }

    $qryRes = $db->pdoQuery("
        SELECT  a.*,CONCAT(a.first_name,' ',a.last_name) AS user_name,c.parent_id,CONCAT(c.first_name,' ',c.last_name) AS doctor_name
        FROM tbl_appointment as a 
        LEFT JOIN tbl_users AS c ON c.id = a.doctor_id
        WHERE (c.parent_id = ".(int) $sessUserId." OR a.doctor_id = ".(int) $sessUserId.") ".$where." AND a.is_active = 'y' AND a.first_name != ''
        ORDER BY a.booking_date DESC ")->results();
    
    $usertype_array = array(
        "Patient Name",
        "Gender",
        "Case Type",
        "Age",
        "Booked Date",
        "Booked Slot",
        "Address",
        "Remarks",
        "Added On",
    );

    if(count($qryRes) > 0){
        foreach($qryRes as $fetchRes){
            $usertype_array = array_merge($usertype_array);

            $age = '';
            if (!empty($fetchRes['date_of_birth']) && $fetchRes['date_of_birth'] != '0000-00-00') {
                $dob = new DateTime($fetchRes['date_of_birth']);
                $today = new DateTime();

                if ($dob <= $today) {
                    $age = $today->diff($dob)->y;
                } else {
                    $age = 0;
                }
            }

            $constant_array = array(
                filtering($fetchRes['user_name']),
                get_gender($fetchRes['gender']),
                get_case_type($fetchRes['case_type']),
                $age,
                $fetchRes['booking_date'],
                $fetchRes['from_time'].' to '. $fetchRes['to_time'],
                $fetchRes['address'],
                $fetchRes['remarks'],
                convertDate($fetchRes['created_date'],false,'ridewrap_admin_grid'),
            );

            $final_constant_array[] = $constant_array;
        }

        $final_result = array($usertype_array);
        foreach($final_constant_array as $k=>$v){
            $final_result = array_merge($final_result,array($v));
        }

        if ($data_type == 'past') {
            $filename = 'Past_Appointments_'.date('d-m-Y').'.csv';
        } else{
            $filename = 'Upcoming_Appointments_'.date('d-m-Y').'.csv';
        }

        export_to_csv($filename,$final_result);
        
        exit;
    }else{
        $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NRF'));
        echo '<script type="text/javascript">
        window.location.href="'.SITE_DASHBOARD.'";
        </script>';
    }
}


echo json_encode($response);
exit ;
?>