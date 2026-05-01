<?php

require_once("../../includes-nct/config-nct.php");

global $db, $sessUserId;

$bill_id = isset($_POST['bill_id']) ? (int)$_POST['bill_id'] : 0;
$reason  = isset($_POST['reason']) ? trim($_POST['reason']) : '';

if($bill_id <= 0){
    echo json_encode(['status'=>false,'message'=>'Invalid Bill']);
    exit;
}

// Verify ownership
$bill = $db->pdoQuery("
    SELECT id, created_date, status
    FROM tbl_bills
    WHERE id=? AND clinic_id=?
", [$bill_id, $sessUserId])->result();

if(!$bill){
    echo json_encode(['status'=>false,'message'=>'Unauthorized']);
    exit;
}

// Already cancelled
if($bill['status'] == 'cancelled'){
    echo json_encode(['status'=>false,'message'=>'Already cancelled']);
    exit;
}

// 2 hour rule
if(time() - strtotime($bill['created_date']) > 7200){
    echo json_encode(['status'=>false,'message'=>'Delete time expired']);
    exit;
}

// UPDATE
$db->pdoQuery("
    UPDATE tbl_bills
    SET status='cancelled',
        cancel_reason=?,
        cancelled_at=NOW(),
        cancelled_by=?
    WHERE id=?
", [$reason, $sessUserId, $bill_id]);

echo json_encode(['status'=>true,'message'=>'Bill cancelled successfully']);
exit;
