<?php
require_once("../../includes-nct/config-nct.php");

header('Content-Type: application/json');

if(empty($sessUserId)){
    echo json_encode([]);
    exit;
}

/* SEARCH TESTS */
if(isset($_POST['action']) && $_POST['action'] == "searchTests"){

    $keyword = trim($_POST['keyword']);

    // get clinic id
    $user = $db->pdoQuery("
        SELECT id, parent_id, user_type
        FROM tbl_users
        WHERE id=".$sessUserId."
    ")->result();

    $clinic_id = ($user['user_type'] == 'clinic') ? $user['id'] : $user['parent_id'];

    $tests = $db->pdoQuery("
        SELECT id, service_name
        FROM tbl_clinic_services
        WHERE clinic_id=".$clinic_id."
        AND status='a'
        AND service_name LIKE '%".$keyword."%'
        ORDER BY service_name ASC
        LIMIT 10
    ")->results();

    echo json_encode($tests);
    exit;
}

/* SAVE PRESCRIPTION DRAFT */
if(isset($_POST['action']) && $_POST['action'] == "savePrescriptionDraft"){

    global $db, $sessUserId;

    $appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;

    if($appointment_id <= 0){
        echo json_encode(["status"=>"error","message"=>"Invalid appointment"]);
        exit;
    }

    /* PREPARE DATA (JSON) */
    $vitals = json_encode([
        "height" => $_POST['height'] ?? "",
        "weight" => $_POST['weight'] ?? "",
        "bmi"    => $_POST['bmi'] ?? "",
        "bp"     => $_POST['bp'] ?? "",
        "pulse"  => $_POST['pulse'] ?? "",
        "rr"     => $_POST['rr'] ?? "",
        "spo2"   => $_POST['spo2'] ?? ""
    ]);

    $complaints     = isset($_POST['complaints']) ? json_encode($_POST['complaints']) : "[]";
    $diagnosis      = $_POST['diagnosis'] ?? "";
    $medications    = isset($_POST['medications']) ? json_encode($_POST['medications']) : "[]";
    $investigations = isset($_POST['investigations']) ? json_encode($_POST['investigations']) : "[]";

    $followup = json_encode([
        "date"  => $_POST['followup_date'] ?? "",
        "notes" => $_POST['followup_notes'] ?? ""
    ]);

    /* INSERT INTO TABLE */
    $db->pdoQuery("
        INSERT INTO tbl_prescriptions
        (
            appointment_id,
            doctor_id,
            vitals_json,
            complaints_json,
            diagnosis,
            medications_json,
            investigations_json,
            followup_json,
            status,
            created_at
        )
        VALUES
        (
            ".$appointment_id.",
            ".$sessUserId.",
            '".$vitals."',
            '".$complaints."',
            '".$diagnosis."',
            '".$medications."',
            '".$investigations."',
            '".$followup."',
            'draft',
            NOW()
        )
    ");

    $prescription_id = $db->lastInsertId();

    echo json_encode([
        "status" => "success",
        "prescription_id" => $prescription_id
    ]);
    exit;
}

echo json_encode([]);
