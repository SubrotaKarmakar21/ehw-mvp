<?php
require_once("../../includes-nct/config-nct.php");

header('Content-Type: application/json');

global $db, $sessUserId;

if(isset($_POST['action']) && $_POST['action'] == 'saveAdvice'){

    $prescription_id = (int)$_POST['prescription_id'];
    $general_advice = trim($_POST['general_advice']);
    $diet_plan = trim($_POST['diet_plan']);
    $signature = $_POST['signature'];

    // VALIDATION
    if($prescription_id <= 0){
        echo json_encode(["status"=>"error","message"=>"Invalid prescription"]);
        exit;
    }

    if(empty($general_advice) || empty($diet_plan)){
        echo json_encode(["status"=>"error","message"=>"Complete all fields"]);
        exit;
    }

    if(empty($signature)){
        echo json_encode(["status"=>"error","message"=>"Signature required"]);
        exit;
    }

    // SAVE + VERIFY
    $db->update("tbl_prescriptions", [
        "general_advice" => $general_advice,
        "diet_plan" => $diet_plan,
        "doctor_signature" => $signature,
        "status" => "final",
        "is_verified" => 1,
        "verified_at" => date("Y-m-d H:i:s"),
        "verified_by" => $sessUserId
    ], [
        "id" => $prescription_id
    ]);

    echo json_encode(["status"=>"success"]);
    exit;
}

// fallback
echo json_encode(["status"=>"error","message"=>"Invalid request"]);
exit;
