<?php

/* show errors only during development */
/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);*/

require_once("../../includes-nct/config-nct.php");

if(empty($sessUserId)){
    echo json_encode(["debug"=>"sessUserId empty"]);
    exit;
}

$db->setErrorLog(false);

while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');

/* ============================= */
/* PATIENT SEARCH                */
/* ============================= */

if(isset($_POST['action']) && $_POST['action']=="searchPatient"){

    global $db;

    $keyword = trim($_POST['keyword']);

    /* determine clinic id (works for clinic login and doctor login) */

    $user = $db->pdoQuery("
    	SELECT id, parent_id, user_type
    	FROM tbl_users
    	WHERE id=".$sessUserId."
    ")->result();

    $clinic_id = ($user['user_type'] == 'clinic') ? $user['id'] : $user['parent_id'];

    $patients = $db->pdoQuery("
	SELECT
    		u.id,
    		CONCAT(u.first_name,' ',u.last_name) AS name,
    		u.phone_no,
    		u.gender,
    		u.date_of_birth,
    		(
        		SELECT MIN(a.booking_date)
        		FROM tbl_appointment a
        		WHERE a.user_id = u.id
        		AND a.booking_date >= CURDATE()
    		) AS booking_date
	FROM tbl_users u
	WHERE u.user_type='patient'
	AND u.parent_id=".$clinic_id."
	AND (
    		u.first_name LIKE '%".$keyword."%'
    		OR u.last_name LIKE '%".$keyword."%'
    		OR u.phone_no LIKE '%".$keyword."%'
	)
	ORDER BY
	CASE WHEN booking_date IS NULL THEN 1 ELSE 0 END,
	booking_date ASC
	LIMIT 10
	")->results();

    echo json_encode($patients);
    exit;

    /* discard debug text */
    ob_end_clean();

    header('Content-Type: application/json');
    echo json_encode($patients);
    exit;
}

/* ============================= */
/* GET PATIENT APPOINTMENT       */
/* ============================= */

if(isset($_POST['action']) && $_POST['action']=="getPatientAppointment"){

    $patient_id = (int)$_POST['patient_id'];

    $appointment = $db->pdoQuery("
        SELECT a.doctor_id, a.booking_date, d.consultation_fees
        FROM tbl_appointment a
        LEFT JOIN tbl_users d ON d.id = a.doctor_id
        WHERE a.user_id = ".$patient_id."
        ORDER BY a.booking_date ASC
        LIMIT 1
    ")->result();

    echo json_encode($appointment);
    exit;

}

/* ============================= */
/* SERVICE SEARCH                */
/* ============================= */

if(isset($_POST['action']) && $_POST['action'] =="searchService"){

    global $db, $sessUserId;

    $keyword = trim($_POST['keyword']);
    $clinic_id = $sessUserId;

    $services = $db->pdoQuery("
        SELECT id, service_name, price
        FROM tbl_clinic_services
        WHERE clinic_id=".$clinic_id."
        AND status='a'
        AND service_name LIKE '%".$keyword."%'
        ORDER BY service_name ASC
        LIMIT 10
    ")->results();

    echo json_encode($services);
    exit;
}

/* ============================= */
/* ADD PAYMENT TO EXISTING BILL  */
/* ============================= */

if(isset($_POST['action']) && $_POST['action']=="addPayment"){

    global $db, $sessUserId;

    $bill_id = isset($_POST['bill_id']) ? intval($_POST['bill_id']) : 0;
    $amount  = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $method  = isset($_POST['method']) ? trim($_POST['method']) : "Cash";

    if($bill_id <= 0 || $amount <= 0){
        echo json_encode(["status"=>"error","message"=>"Invalid payment data"]);
        exit;
    }

    $bill = $db->pdoQuery("
        SELECT id
        FROM tbl_bills
        WHERE id=".$bill_id."
        AND clinic_id=".$sessUserId."
    ")->result();

    if(empty($bill)){
        echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
        exit;
    }

    /* INSERT PAYMENT */

    $db->pdoQuery("
        INSERT INTO tbl_bill_payments
        (bill_id, amount, payment_method, created_at)
        VALUES
        (".$bill_id.", ".$amount.", '".$method."', NOW())
    ");

    $payment_id = $db->lastInsertId();

    /* ============================= */
    /* RECALCULATE BILL TOTALS       */
    /* ============================= */

    $totals = $db->pdoQuery("
    	SELECT
        	b.total_amount,
        	IFNULL(SUM(p.amount),0) AS paid_amount
    	FROM tbl_bills b
    	LEFT JOIN tbl_bill_payments p ON p.bill_id = b.id
    	WHERE b.id=".$bill_id."
    	GROUP BY b.id
	")->result();

    $paid = isset($totals['paid_amount']) ? (float)$totals['paid_amount'] : 0;
    $total = isset($totals['total_amount']) ? (float)$totals['total_amount'] : 0;
    $due = $total - $paid;

    echo json_encode([
    	"status" 	=> "success",
	"payment_id"	=> $payment_id,
    	"total"  	=> $total,
    	"paid"   	=> $paid,
    	"due"    	=> $due
    ]);

    exit;
}

/* ============================= */
/* DELETE PAYMENT                */
/* ============================= */

if(isset($_POST['action']) && $_POST['action']=="deletePayment"){

	global $db, $sessUserId;

    	$payment_id = isset($_POST['payment_id']) ? (int)$_POST['payment_id'] : 0;

	if($payment_id <= 0){
        	echo json_encode(["status"=>"error","message"=>"Invalid payment ID"]);
        	exit;
    	}

    	$payment = $db->pdoQuery("
        	SELECT p.id
        	FROM tbl_bill_payments p
        	INNER JOIN tbl_bills b ON b.id = p.bill_id
        	WHERE p.id=".$payment_id."
        	AND b.clinic_id=".$sessUserId."
    	")->result();

    	if(empty($payment)){
        	echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
        	exit;
    	}

    	/* DELETE PAYMENT */

    	$db->pdoQuery("
        	DELETE FROM tbl_bill_payments
        	WHERE id=".$payment_id."
    	");

    	echo json_encode(["status"=>"success"]);
    	exit;
}

/* ============================= */
/* GENERATE BILL                 */
/* ============================= */

if($_POST['action']=="generateBill"){

    /* REQUIRED FIELD VALIDATION */

    if(
    	empty($_POST['patient_name']) ||
    	empty($_POST['patient_phone']) ||
    	empty($_POST['patient_age']) ||
    	empty($_POST['patient_gender'])
    ){
    	echo json_encode([
        	"status" => "error",
        	"message" => "Patient name, phone, age and gender are required."
    	]);
    	exit;
    }

    global $db, $sessUserId;

    $clinic_id = (int)$sessUserId;
    $appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
    $patient_id = (int)$_POST['patient_id'];
    $patient_name   = !empty($_POST['patient_name']) ? trim($_POST['patient_name']) : NULL;
    $patient_age    = !empty($_POST['patient_age']) ? (int)$_POST['patient_age'] : NULL;
    $age_type = isset($_POST['age_type']) ? $_POST['age_type'] : 'years';
    $today = new DateTime();

    if($patient_age){

    	if($age_type == "days"){
        	$today->modify("-".$patient_age." days");
    	}
    	elseif($age_type == "months"){
        	$today->modify("-".$patient_age." months");
    	}
    	else{ // years
        	$today->modify("-".$patient_age." years");
    	}

    	$date_of_birth = $today->format('Y-m-d');

    }else{
    	$date_of_birth = NULL;
    }
    $patient_gender = !empty($_POST['patient_gender']) ? $_POST['patient_gender'] : NULL;
    $patient_phone  = !empty($_POST['patient_phone']) ? $_POST['patient_phone'] : NULL;

    $doctor_id = !empty($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : NULL;

    $referred_doctor = !empty($_POST['referred_doctor']) ? trim($_POST['referred_doctor']) : NULL;

    $bill_date = !empty($_POST['bill_date']) ? date("Y-m-d", strtotime($_POST['bill_date'])) : date("Y-m-d");

    $services = isset($_POST['services']) ? $_POST['services'] : [];

    $payments = isset($_POST['payments']) ? $_POST['payments'] : [];
    $subtotal = 0;

    foreach($services as $s){
    	$subtotal += ((float)$s['price'] * (int)$s['qty']);
    }

    /* DISCOUNT */

    $discount_input = isset($_POST['discount']) ? trim($_POST['discount']) : '';
    $discount = 0;

    if($discount_input){

    	if(strpos($discount_input,'%') !== false){

	        $percent = floatval(str_replace('%','',$discount_input));
        	$discount = $subtotal * $percent / 100;

    	}else{

        	$discount = floatval($discount_input);

    	}
    }

    $total_amount = $subtotal - $discount;

    if($total_amount < 0){
        $total_amount = 0;
    }

    /* ============================= */
    /* GENERATE BILL NUMBER          */
    /* ============================= */

    $db->pdoQuery("
	UPDATE tbl_users
	SET bill_serial = bill_serial + 1
	WHERE id=".$clinic_id);

	$clinicInfo = $db->pdoQuery("
		SELECT bill_serial, partner_id
		FROM tbl_users
		WHERE id=".$clinic_id)->result();

	$serial = $clinicInfo['bill_serial'];
	$partner_id = $clinicInfo['partner_id'];

	$bill_number = $partner_id."-".$serial;

    	$db->pdoQuery("INSERT INTO tbl_bills (bill_number, clinic_id, appointment_id, patient_id, patient_name, patient_age, patient_gender, patient_phone, doctor_id, referred_doctor, bill_date, patient_dob, subtotal, discount, total_amount)
	VALUES (
		'".$bill_number."',
    		".$clinic_id.",
		".($appointment_id ? $appointment_id : "NULL").",
    		".($patient_id ? $patient_id : "NULL").",
    		".($patient_name ? "'".$patient_name."'" : "NULL").",
    		".($patient_age ? $patient_age : "NULL").",
    		".($patient_gender ? "'".$patient_gender."'" : "NULL").",
    		".($patient_phone ? "'".$patient_phone."'" : "NULL").",
    		".($doctor_id ? $doctor_id : "NULL").",
    		".($referred_doctor ? "'".$referred_doctor."'" : "NULL").",
    		'".$bill_date."',
		".($date_of_birth ? "'".$date_of_birth."'" : "NULL").",
    		".$subtotal.",
    		".$discount.",
    		".$total_amount."
	)");

    $bill_id = $db->LastInsertId();

    if(!empty($payments)){

    	foreach($payments as $p){

        	$amount = floatval($p['amount']);
        	$method = trim($p['method']);

        	if($amount <= 0) continue;

        	$db->pdoQuery("
            		INSERT INTO tbl_bill_payments (bill_id, amount, payment_method, created_at)
            		VALUES (".$bill_id.", ".$amount.", '".$method."', NOW())
        	");

    	}

    }

    foreach($services as $s){

        $db->pdoQuery("INSERT INTO tbl_bill_items (bill_id,service_name,price,qty,total)
		VALUES (
			".$bill_id.",
			'".$s['service_name']."',
			".$s['price'].",
			".$s['qty'].",
			".($s['price']*$s['qty'])."
		)");

    }

    $response = [
    	"status"=>"success",
    	"bill_id"=>$bill_id
    ];

    if (ob_get_length()){
	ob_clean();
    }

    echo json_encode($response);
    exit;
}

/* ============================= */
/* FALLBACK RESPONSE             */
/* ============================= */

echo json_encode([]);
exit;
