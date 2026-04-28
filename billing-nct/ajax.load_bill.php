<?php

require_once("../../includes-nct/config-nct.php");

global $db, $sessUserId;

$bill_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($bill_id <= 0){
    echo json_encode([]);
    exit;
}

/* Load Bill */

$bill = $db->pdoQuery("
SELECT *
FROM tbl_bills
WHERE id=".$bill_id."
AND clinic_id=".$sessUserId."
")->result();

if(empty($bill)){
    echo json_encode([]);
    exit;
}

/* Load Services */

$items = $db->pdoQuery("
SELECT service_name, price, qty, total
FROM tbl_bill_items
WHERE bill_id=".$bill_id."
")->results();

/* Load Payments */

$payments = $db->pdoQuery("
SELECT id, amount, payment_method, created_at
FROM tbl_bill_payments
WHERE bill_id=".$bill_id."
ORDER BY created_at ASC
")->results();

/* Calculate Paid */

$total_paid = 0;

foreach($payments as $p){
    $total_paid += $p['amount'];
}

$response = [

"patient_name" => $bill['patient_name'],
"patient_phone" => $bill['patient_phone'],
"patient_age" => $bill['patient_age'],
"patient_gender" => $bill['patient_gender'],
"doctor_id" => $bill['doctor_id'],
"bill_date" => $bill['bill_date'],

"items" => $items,

"payments" => $payments,

"subtotal" => $bill['subtotal'],
"discount" => $bill['discount'],
"total" => $bill['total_amount'],

"paid" => $total_paid,
"due" => $bill['total_amount'] - $total_paid

];

echo json_encode($response);
