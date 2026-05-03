<?php

class PatientDetails {

    public function __construct($module){

        foreach ($GLOBALS as $key => $values){
            $this->$key = $values;
        }

        $this->module = $module;
    }

    public function getPageContent(){

        global $sessUserId;

        if(!isset($sessUserId) || $sessUserId <= 0){
            header("Location: ".SITE_URL."login");
            exit;
        }

        global $db;

        $patient_id = 0;

        $appointment = [];

        // CASE 1: from appointment
        if(isset($_GET['appointment_id']) && (int)$_GET['appointment_id'] > 0){

                $appointment_id = (int)$_GET['appointment_id'];

                $appointment = $db->pdoQuery("
                        SELECT a.user_id,
                        CONCAT(a.first_name,' ',a.last_name) AS patient_name,
                        u.gender,
                        u.date_of_birth,
                        u.profile_photo
                        FROM tbl_appointment a
                        LEFT JOIN tbl_users u ON u.id = a.user_id
                        WHERE a.id = ".$appointment_id."
                ")->result();

                if(empty($appointment)){
                        header("Location: ".SITE_URL."dashboard");
                        exit;
                }

                $patient_id = $appointment['user_id'];
        }

        // CASE 2: from patient list
        else if(isset($_GET['patient_id']) && (int)$_GET['patient_id'] > 0){

                $patient_id = (int)$_GET['patient_id'];

                $appointment = $db->pdoQuery("
                        SELECT CONCAT(first_name,' ',last_name) AS patient_name,
                        gender,
                        date_of_birth,
                        profile_photo
                        FROM tbl_users
                        WHERE id = ".$patient_id."
                ")->result();

                if(empty($appointment)){
                        header("Location: ".SITE_URL."my-patients");
                        exit;
                }
        }
        else{
                header("Location: ".SITE_URL."dashboard");
                exit;
        }

        // Get user type
        $user = $db->pdoQuery("
            SELECT user_type
            FROM tbl_users
            WHERE id = ".$this->sessUserId."
        ")->result();

        if(empty($user)){
            header("Location: ".SITE_URL."dashboard");
            exit;
        }

        $isAllowed = false;

        // DIRECT OWNERSHIP CHECK
        $ownershipCheck = $db->pdoQuery("
                SELECT id FROM tbl_users
                WHERE id = ".$patient_id."
                AND parent_id = ".$this->sessUserId."
                AND user_type = 'patient'
                LIMIT 1
        ")->result();

        if(!empty($ownershipCheck)){
                $isAllowed = true;
        }

        // CHECK DOCTOR ACCESS
        if($user['user_type'] == 'doctor'){

                $doctorClinic = $db->pdoQuery("
                        SELECT parent_id
                        FROM tbl_users
                        WHERE id = ".$this->sessUserId."
                ")->result();

                $clinicId = $doctorClinic['parent_id'] ?? 0;

                if($clinicId > 0){

                        // Check via appointment
                        $check = $db->pdoQuery("
                                SELECT a.id
                                FROM tbl_appointment a
                                LEFT JOIN tbl_users d ON d.id = a.doctor_id
                                WHERE a.user_id = ".$patient_id."
                                AND d.parent_id = ".$clinicId."
                                LIMIT 1
                        ")->result();

                        if(!empty($check)){
                                $isAllowed = true;
                        }

                }
        }

        // CHECK CLINIC ACCESS
        else if($user['user_type'] == 'clinic'){

                // Check via appointment
                $check = $db->pdoQuery("
                        SELECT a.id
                        FROM tbl_appointment a
                        LEFT JOIN tbl_users d ON d.id = a.doctor_id
                        WHERE a.user_id = ".$patient_id."
                        AND d.parent_id = ".$this->sessUserId."
                        LIMIT 1
                ")->result();

                if(!empty($check)){
                        $isAllowed = true;
                }

                // Check via bills
                if(!$isAllowed){
                        $billCheck = $db->pdoQuery("
                                SELECT id FROM tbl_bills
                                WHERE patient_id = ".$patient_id."
                                AND clinic_id = ".$this->sessUserId."
                                LIMIT 1
                        ")->result();

                        if(!empty($billCheck)){
                                $isAllowed = true;
                        }
                }
        }

        if(!$isAllowed){
                header("Location: ".SITE_URL."dashboard");
                exit;
        }

        // LOAD TEMPLATE
        $filePath = DIR_TMPL.$this->module."/".$this->module.".tpl.php";

        $tpl = new MainTemplater($filePath);
        $tpl = $tpl->parse();

        $patient_name = $appointment['patient_name'];

        $patient_image = SITE_IMG . "default-user.png";

        if(!empty($appointment['profile_photo'])){
                $patient_image = SITE_UPD_PROFILE_IMAGE . $appointment['profile_photo'];
        }

        $global_patient = $db->pdoQuery("
                SELECT patient_id FROM tbl_users
                WHERE id = ".$patient_id."
        ")->result();

        $patient_summary = "Health summary not available.";
        $prescriptions = [];

        if(!empty($global_patient) && !empty($global_patient['patient_id'])){

                $global_patient_id = $global_patient['patient_id'];
                $prescriptions = $db->pdoQuery("
                        SELECT p.*
                        FROM tbl_prescriptions p
                        INNER JOIN tbl_appointment a ON a.id = p.appointment_id
                        INNER JOIN tbl_users u ON u.id = a.user_id
                        WHERE u.patient_id = '".$global_patient_id."'
                        AND p.status = 'final'
                        ORDER BY p.created_at DESC
                ")->results();
        }

        $structuredData = [];

        foreach($prescriptions as $p){

                $entry = [
                        "date" => $p['created_at'],
                        "diagnosis" => "",
                        "complaints" => [],
                        "vitals" => [],
                        "medications" => [],
                        "investigations" => [],
                        "advice" => "",
                        "diet" => ""
                ];

                // Diagnosis
                if(!empty($p['diagnosis'])){
                        $entry['diagnosis'] = strtolower(trim($p['diagnosis']));
                }

                // Vitals
                if(!empty($p['vitals_json'])){
                        $vitals = json_decode($p['vitals_json'], true);

                        if(is_array($vitals)){
                                foreach($vitals as $k => $v){
                                        $key = strtolower(trim($k));
                                        $entry['vitals'][$key] = $v;
                                }
                        }
                }

                // Complaints
                if(!empty($p['complaints_json'])){
                        $complaints = json_decode($p['complaints_json'], true);

                        if(is_array($complaints)){
                                foreach($complaints as $c){
                                        if(is_string($c)){
                                                $entry['complaints'][] = strtolower(trim($c));
                                        }
                                }
                        }
                }

                // Medications
                if(!empty($p['medications_json'])){
                        $meds = json_decode($p['medications_json'], true);

                        if(is_array($meds)){
                                foreach($meds as $m){

                                        // Handle multiple formats safely
                                        if(is_array($m) && isset($m['name'])){
                                                $entry['medications'][] = strtolower(trim($m['name']));
                                        }
                                        else if(is_string($m)){
                                                $entry['medications'][] = strtolower(trim($m));
                                        }
                                }
                        }
                }

                // Investigations
                if(!empty($p['investigations_json'])){
                        $inv = json_decode($p['investigations_json'], true);

                        if(is_array($inv)){
                                foreach($inv as $i){
                                        if(is_string($i)){
                                                $entry['investigations'][] = strtolower(trim($i));
                                        }
                                }
                        }
                }

                // Advice
                if(!empty($p['general_advice'])){
                        $entry['advice'] = strtolower(trim($p['general_advice']));
                }

                // Diet
                if(!empty($p['diet_plan'])){
                        $entry['diet'] = strtolower(trim($p['diet_plan']));
                }

                $structuredData[] = $entry;
        }

        $insights = [];

        // --- Diagnosis tracking ---
        $diagnosisMap = [];
        $complaintMap = [];
        $vitalMap = [];

        foreach($structuredData as $d){

                // Diagnosis
                if(!empty($d['diagnosis'])){
                        $parts = explode(';', $d['diagnosis']);

                        foreach($parts as $diag){
                                $diag = trim($diag);
                                if($diag != ''){
                                        $diagnosisMap[$diag] = ($diagnosisMap[$diag] ?? 0) + 1;
                                }
                        }
                }

                // Complaints
                foreach($d['complaints'] as $c){
                        $complaintMap[$c] = ($complaintMap[$c] ?? 0) + 1;
                }

                // Vitals
                if(!empty($d['vitals'])){
                        foreach($d['vitals'] as $key => $value){

                                if(!empty($value)){
                                        $vitalMap[$key][] = $value;
                                }
                        }
                }
        }

        // --- Chronic Diagnosis ---
        foreach($diagnosisMap as $diag => $count){
                if($count >= 2){
                        $insights[] = "Repeated diagnosis of ".$diag;
                }
        }

        // --- Frequent Complaints ---
        foreach($complaintMap as $c => $count){
                if($count >= 2){
                        $insights[] = "Recurring complaint: ".$c;
                }
        }

        // --- Vitals Pattern ---
        foreach($vitalMap as $vital => $values){

                if(count($values) >= 2){
                        $insights[] = "Multiple recordings of ".$vital." observed.";
                }
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

        $aiInput = [
                "age" => $patientAge . " " . $patientAgeType,
                "gender" => $appointment['gender'],
                "medical_history" => $structuredData,
                "detected_insights" => $insights
        ];

        $prompt = "
                You are a medical assistant AI.

                Analyze the patient's health data and generate a concise clinical summary.

                Patient Context:
                        - Age: ".$aiInput['age']."
                        - Gender: ".$aiInput['gender']."

                Instructions:
                        - Do NOT mention name, phone, or address
                        - Identify patterns, trends, and risks
                        - Do NOT include any advice
                        - Focus only on clinical observations so that the doctor gets a proper insight of overall health
                        - Use simple but clinical language
                        - Focus on:
                                • recurring diagnoses
                                • symptom patterns
                                • vital trends (BMI, BP, weight, etc.)
                                • possible risk indicators

                Data: ".json_encode($aiInput, JSON_PRETTY_PRINT)."

                Output:
                        Write a short paragraph (6-10 lines) summarizing the patient's health profile.
        ";

        function generateAISummary($prompt){

                $apiKey = OPENAI_API_KEY;

                $data = [
                        "model" => "gpt-4o-mini",
                        "messages" => [
                                ["role" => "system", "content" => "You are a clinical medical assistant."],
                                ["role" => "user", "content" => $prompt]
                        ],
                        "temperature" => 0.3
                ];

                $ch = curl_init("https://api.openai.com/v1/chat/completions");

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);

                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        "Content-Type: application/json",
                        "Authorization: Bearer ".$apiKey
                ]);

                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                $response = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($response, true);

                return $result['choices'][0]['message']['content'] ?? "Health summary not available for this patient.";
        }

        if(!empty($structuredData)){
                $patient_summary = generateAISummary($prompt);
        }

        // APPOINTMENT HISTORY
        $appointment_rows = '';

        if($user['user_type'] == 'doctor'){

                $appointments = $db->pdoQuery("
                        SELECT a.*, CONCAT(a.first_name,' ',a.last_name) AS patient_name
                        FROM tbl_appointment a
                        WHERE a.user_id = ".$patient_id."
                        AND a.doctor_id = ".$this->sessUserId."
                        ORDER BY a.booking_date DESC
                ")->results();

        } else {

                $appointments = $db->pdoQuery("
                        SELECT a.*, CONCAT(a.first_name,' ',a.last_name) AS patient_name,
                        CONCAT(d.first_name,' ',d.last_name) AS doctor_name
                        FROM tbl_appointment a
                        LEFT JOIN tbl_users d ON d.id = a.doctor_id
                        WHERE a.user_id = ".$patient_id."
                        AND d.parent_id = ".$this->sessUserId."
                        ORDER BY a.booking_date DESC
                ")->results();

        }

        foreach($appointments as $a){

        // CHECK NO SHOW
        $hasValidBill = $db->pdoQuery("
                SELECT 1 FROM tbl_bills
                WHERE appointment_id = ".$a['id']."
                AND status = 'active'
                LIMIT 1
        ")->result();

        if(empty($hasValidBill)){
                $status = "<span style='color:red;font-weight:600;'>No Show</span>";
        } else {
                $status = "<span style='color:green;'>Completed</span>";
        }

        // DATE FORMAT
        $date = date('d M Y', strtotime($a['booking_date']));

        // CHECK WHETHER PRESCRIPTIONS EXIST
        $prescription = $db->pdoQuery("
                SELECT id FROM tbl_prescriptions
                WHERE appointment_id = ".$a['id']."
                AND status = 'final'
                LIMIT 1
        ")->result();

        // ACTION BUTTONS
        if(!empty($prescription['id'])){

                $actions = "
                        <a href='".SITE_URL."modules-nct/prescription_view-nct/index.php?prescription_id=".$prescription['id']."'>View</a> |
                        <a href='".SITE_URL."modules-nct/prescription_view-nct/index.php?prescription_id=".$prescription['id']."&action=print'>Download</a>
                ";

        } else {

                $actions = "
                        <a href='javascript:void(0);' class='no-prescription'>View</a> |
                        <a href='javascript:void(0);' class='no-prescription'>Download</a>
                ";

        }

        $appointment_rows .= "
                        <tr>
                                <td>".$date."</td>
                                <td>".$a['patient_name']."</td>
                                ".($user['user_type'] == 'clinic' ? "<td>Dr. ".$a['doctor_name']."</td>" : "")."
                                <td>".$status."</td>
                                <td>".$actions."</td>
                        </tr>
                ";
        }

        if($appointment_rows == ''){
                $colspan = ($user['user_type'] == 'clinic') ? 5 : 4;

                $appointment_rows = "
                        <tr>
                                <td colspan='".$colspan."' style='text-align:center;padding:20px;color:gray;'>
                                        No appointment history found
                                </td>
                        </tr>
                ";
        }

        $appointment_tab_content = '
                <table class="table table-bordered mt-3">
                        <thead>
                                <tr>
                                        <th>Date</th>
                                        <th>Patient</th>
                                        '.($user['user_type'] == 'clinic' ? '<th>Doctor</th>' : '').'
                                        <th>Status</th>
                                        <th>Action</th>
                                </tr>
                        </thead>
                        <tbody>
                                '.$appointment_rows.'
                        </tbody>
                </table>

        ';

        // BILL HISTORY
        $bill_rows = '';
        $total_collection = 0;
        $total_due = 0;
        $total_visits_dates = [];

        $bills = $db->pdoQuery("
                SELECT b.id,
                b.bill_number,
                b.bill_date,
                b.total_amount,
                b.created_date,
                b.status,

                COALESCE(CONCAT(p.first_name,' ',p.last_name), b.patient_name) AS patient_name,

                CASE WHEN d.id IS NOT NULL THEN CONCAT('Dr. ', d.first_name, ' ', d.last_name)
                ELSE ''
                END AS doctor_name,

                IFNULL(SUM(pay.amount),0) AS total_paid,

                MAX(pay.created_at) AS last_payment_time,

                GROUP_CONCAT(CONCAT('₹',pay.amount,' (',pay.payment_method,')') SEPARATOR ',<br>') AS payments,

                (b.total_amount - IFNULL(SUM(pay.amount),0)) AS due_amount

                FROM tbl_bills b

                LEFT JOIN tbl_users p ON p.id = b.patient_id
                LEFT JOIN tbl_users d ON d.id = b.doctor_id
                LEFT JOIN tbl_bill_payments pay ON pay.bill_id = b.id

                WHERE b.status = 'active'

                AND (
                        b.patient_id = ".$patient_id."
                        OR b.appointment_id IN (
                                SELECT id FROM tbl_appointment WHERE user_id = ".$patient_id."
                        )
                )

                AND (
                        b.clinic_id = ".$this->sessUserId."
                        OR b.doctor_id = ".$this->sessUserId."
                )
                GROUP BY b.id

                ORDER BY b.id DESC
        ")->results();

        foreach($bills as $b){

        // TOTAL COLLECTION
        $total_collection += $b['total_paid'];

        // TOTAL DUE
        $total_due += $b['due_amount'];

        // UNIQUE VISITS (by date)
        $total_visits_dates[$b['bill_date']] = true;

        // ACTION BUTTONS
        $actionHtml = "<a href='".SITE_URL."modules-nct/invoice-nct/index.php?id=".$b['id']."'>View</a> | ";

        $canEdit = false;

        $now = time();
        $lastPayment = !empty($b['last_payment_time']) ? strtotime($b['last_payment_time']) : null;

        if($b['due_amount'] > 0){
                $canEdit = true;
        } else {
                if($lastPayment && ($now - $lastPayment <= 7200)){
                        $canEdit = true;
                }
        }

        if($canEdit){
                $actionHtml .= "<a href='".SITE_URL."billing?bill_id=".$b['id']."&payment_mode=1'>Edit</a> | ";
        }

        $actionHtml .= "<a href='".SITE_URL."modules-nct/invoice-nct/index.php?id=".$b['id']."&download=1'>Download</a>";

        $bill_rows .= "
                <tr ".($b['due_amount'] > 0 ? "style='background-color: rgba(255,0,0,0.06);'" : "").">
                        <td>".$b['bill_number']."</td>
                        <td>".$b['bill_date']."</td>
                        <td>".$b['patient_name']."</td>
                        <td>".$b['doctor_name']."</td>
                        <td>".($b['referred_doctor'] ? $b['referred_doctor'] : '')."</td>
                        <td>₹".$b['total_amount']."</td>
                        <td>".($b['payments'] ? $b['payments'] : '')."</td>
                        <td>".$actionHtml."</td>
                </tr>
        ";
        }

        $total_visits = count($total_visits_dates);

        if($bill_rows == ''){
                $bill_rows = "
                        <tr>
                                <td colspan='7' style='text-align:center;padding:20px;color:gray;'>
                                        No bills found for this patient
                                </td>
                        </tr>
                ";
        }

        $bill_tab_content = '
                <div id="bills" class="patient-details-tab-content">

                        <div class="row g-3 mb-3">

                                <div class="col-md-4">
                                        <div class="card text-white bg-dark h-100">
                                                <div class="card-body">
                                                        <h6>Total Visits</h6>
                                                        <h3>'.$total_visits.'</h3>
                                                </div>
                                        </div>
                                </div>

                                <div class="col-md-4">
                                        <div class="card text-white bg-success h-100">
                                                <div class="card-body">
                                                        <h6>Total Collection</h6>
                                                        <h3>₹'.number_format($total_collection,2).'</h3>
                                                </div>
                                        </div>
                                </div>

                                <div class="col-md-4">
                                        <div class="card text-white bg-danger h-100">
                                                <div class="card-body">
                                                        <h6>Total Due</h6>
                                                        <h3>₹'.number_format($total_due,2).'</h3>
                                                </div>
                                        </div>
                                </div>

                        </div>

                        <table class="table table-bordered mt-3">
                                <thead>
                                        <tr>
                                                <th>Bill No.</th>
                                                <th>Date</th>
                                                <th>Patient</th>
                                                <th>Doctor</th>
                                                <th>Referred By</th>
                                                <th>Total</th>
                                                <th>Payments</th>
                                                <th>Action</th>
                                        </tr>
                                </thead>
                                <tbody>
                                        '.$bill_rows.'
                                </tbody>
                        </table>

                </div>
        ';

        if($user['user_type'] == 'clinic'){
                $show_bill_tab = '<span class="patient-details-tab" data-tab="bills">Past Bills</span>';
        } else {
                $show_bill_tab = '';
                $bill_tab_content = '';
        }

        $replace = array(
                "%PATIENT_NAME%"        => $patient_name,
                "%PATIENT_GENDER%"      => ucfirst($appointment['gender']),
                "%PATIENT_AGE%"         => $patientAge,
                "%PATIENT_AGE_TYPE%"    => $patientAgeType,
                "%PATIENT_SUMMARY%"     => $patient_summary,
                "%APPOINTMENT_HISTORY%" => $appointment_tab_content,
                "%SHOW_BILL_TAB%"       => $show_bill_tab,
                "%BILL_TAB_CONTENT%"    => $bill_tab_content
        );

        return str_replace(array_keys($replace), array_values($replace), $tpl);

    }
}
