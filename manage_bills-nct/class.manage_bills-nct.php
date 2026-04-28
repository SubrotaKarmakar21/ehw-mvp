<?php

class ManageBills {

    public function __construct($module){

        foreach ($GLOBALS as $key => $values){
            $this->$key = $values;
        }

        $this->module = $module;
    }


    public function getPageContent(){

	/*echo "<pre>";
	print_r($_GET);
	echo "</pre>";
	exit;*/

	if(isset($_POST['action']) && $_POST['action'] == 'delete_bill'){$this->deleteBill();}

	global $sessUserId;

	if(!isset($sessUserId) || $sessUserId <= 0){
    		header("Location: ".SITE_URL."login");
    		exit;
	}

        global $db;

	$search = isset($_GET['search']) ? trim($_GET['search']) : '';
	$date   = isset($_GET['date']) ? trim($_GET['date']) : '';
	$viewDue = isset($_GET['view_due']) ? 1 : 0;
	$viewTrash = isset($_GET['view_trash']) ? 1 : 0;

	/* ============================= */
	/* BILLING SUMMARY CALCULATIONS */
	/* ============================= */

	/* TOTAL COLLECTED */

	$totalCollected = $db->pdoQuery("
    		SELECT IFNULL(SUM(p.amount),0) AS total
    		FROM tbl_bill_payments p
    		INNER JOIN tbl_bills b ON b.id = p.bill_id
    		WHERE b.clinic_id = ".$this->sessUserId."
		AND b.status = 'active'
    		AND p.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
	")->result();

	$totalCollectedAmount = $totalCollected['total'];


	/* TODAY COLLECTION */

	$today = date("Y-m-d");

	$todayCollection = $db->pdoQuery("SELECT IFNULL(SUM(p.amount),0) AS total
    		FROM tbl_bill_payments p
    		INNER JOIN tbl_bills b ON b.id = p.bill_id
    		WHERE b.clinic_id = ".$this->sessUserId."
		AND b.status = 'active'
    		AND DATE(p.created_at) = '".$today."'
	")->result();

	$todayCollectionAmount = $todayCollection['total'];


	/* TOTAL DUE */

	$totalDue = $db->pdoQuery("
    		SELECT IFNULL(SUM(b.total_amount - IFNULL(pay.total_paid,0)),0) AS total_due
    		FROM tbl_bills b

    		LEFT JOIN (
        		SELECT bill_id, SUM(amount) AS total_paid
        		FROM tbl_bill_payments
        		GROUP BY bill_id
    		) pay ON pay.bill_id = b.id

    		WHERE b.clinic_id = ".$this->sessUserId."
		AND b.status = 'active'
	")->result();

	$totalDueAmount = $totalDue['total_due'];

	$where = "WHERE b.clinic_id=".(int)$this->sessUserId;

	if($search != ''){

    		$search = addslashes($search);

    		$where .= " AND (
        		b.bill_number LIKE '%$search%' OR
        		b.patient_name LIKE '%$search%' OR
        		CONCAT(p.first_name,' ',p.last_name) LIKE '%$search%' OR
        		CONCAT(d.first_name,' ',d.last_name) LIKE '%$search%' OR
        		b.referred_doctor LIKE '%$search%'
    		)";

	}

	if($date != ''){

    		$where .= " AND DATE(b.bill_date) = '$date'";

	}

	/* PAGINATION */

	$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

		if($page < 1){
    			$page = 1;
		}

	$limit = 50;
	$offset = ($page - 1) * $limit;

	/* TOTAL BILLS */

	if($viewTrash){

    		$totalBills = $db->pdoQuery("
        		SELECT COUNT(*) AS total
        		FROM tbl_bills
        		WHERE clinic_id=".$this->sessUserId."
        		AND status='cancelled'
    		")->result();

	}
	else if($viewDue){

    		$totalBills = $db->pdoQuery("
        		SELECT COUNT(*) AS total FROM (
            			SELECT b.id, b.total_amount
            			FROM tbl_bills b
            			LEFT JOIN tbl_bill_payments pay ON pay.bill_id = b.id
            			WHERE b.clinic_id=".$this->sessUserId."
            			GROUP BY b.id
            			HAVING (b.total_amount - IFNULL(SUM(pay.amount),0)) > 0
        		) AS temp
    		")->result();

	}
	else{

    		$totalBills = $db->pdoQuery("
        		SELECT COUNT(*) AS total
        		FROM tbl_bills b
        		LEFT JOIN tbl_users p ON p.id = b.patient_id
        		LEFT JOIN tbl_users d ON d.id = b.doctor_id
        		".$where."
    		")->result();

	}

	$totalPages = ceil($totalBills['total'] / $limit);

	if($viewTrash){

    		$bills = $db->pdoQuery("
        		SELECT
            			b.id,
            			b.bill_number,
            			b.bill_date,
            			b.cancel_reason,
            			b.cancelled_at,
            			b.status,
            			COALESCE(CONCAT(p.first_name,' ',p.last_name), b.patient_name) AS patient_name
        		FROM tbl_bills b
        		LEFT JOIN tbl_users p ON p.id = b.patient_id
        		WHERE b.clinic_id=".$this->sessUserId."
        		AND b.status = 'cancelled'
        		ORDER BY b.id DESC
			LIMIT $limit OFFSET $offset
    		")->results();
	}
	else if($viewDue){

		$bills = $db->pdoQuery("

			SELECT
			b.id,
			b.bill_number,
			b.bill_date,
			b.total_amount,
			b.created_date,
			b.status,

			COALESCE(CONCAT(p.first_name,' ',p.last_name), b.patient_name) AS patient_name,

			IFNULL(SUM(pay.amount),0) AS total_paid,

			(b.total_amount - IFNULL(SUM(pay.amount),0)) AS due_amount

			FROM tbl_bills b

			LEFT JOIN tbl_users p ON p.id = b.patient_id

			LEFT JOIN tbl_bill_payments pay ON pay.bill_id = b.id

			WHERE b.clinic_id=".$this->sessUserId."
			GROUP BY b.id
			HAVING due_amount > 0
			ORDER BY b.id DESC
			LIMIT $limit OFFSET $offset
		")->results();

	}
	else{
        	$bills = $db->pdoQuery("
                	SELECT
                        	b.id,
                        	b.bill_number,
                        	b.bill_date,
                        	b.total_amount,
                        	b.referred_doctor,
				b.created_date,
				b.status,

                        	COALESCE(CONCAT(p.first_name,' ',p.last_name), b.patient_name) AS patient_name,
				CASE WHEN d.id IS NOT NULL THEN CONCAT('Dr. ', d.first_name, ' ', d.last_name) ELSE '' END AS doctor_name,
                        	IFNULL(SUM(pay.amount),0) AS total_paid,
				MAX(pay.created_at) AS last_payment_time,
				GROUP_CONCAT(CONCAT('₹',pay.amount,' (',pay.payment_method,')') SEPARATOR ',<br>') AS payments,
                        	(b.total_amount - IFNULL(SUM(pay.amount),0)) AS due_amount

                	FROM tbl_bills b

                	LEFT JOIN tbl_users p ON p.id = b.patient_id
                	LEFT JOIN tbl_users d ON d.id = b.doctor_id
                	LEFT JOIN tbl_bill_payments pay ON pay.bill_id = b.id

                	".$where."

                	GROUP BY b.id

                	ORDER BY b.id DESC
                	LIMIT $limit OFFSET $offset
        	")->results();
	}

        $rows = '';


	foreach($bills as $b){

    		if($b['status'] == 'cancelled'){

        		$actionHtml = "<span style='color:red;font-weight:bold;'>BILL CANCELLED</span>";

    		}else{

        		$actionHtml = "<a href='".SITE_URL."modules-nct/invoice-nct/index.php?id=".$b['id']."'>View</a> | ";

			$canEdit = false;

			$now = time();
			$created = strtotime($b['created_date']);
			$lastPayment = !empty($b['last_payment_time']) ? strtotime($b['last_payment_time']) : null;

			/* RULE 1: IF DUE EXISTS → ALWAYS EDITABLE */
			if($b['due_amount'] > 0){
    				$canEdit = true;
			}

			/* RULE 2: IF FULLY PAID → 2 HOURS FROM LAST PAYMENT */
			else{
    				if($lastPayment && ($now - $lastPayment <= 7200)){
        				$canEdit = true;
    				}
			}

			/* SHOW EDIT BUTTON */
			if($canEdit){
    				$actionHtml .= "<a href='".SITE_URL."billing?bill_id=".$b['id']."&payment_mode=1'>Edit</a> | ";
			}

        		if($b['status'] == 'active' && (time() - strtotime($b['created_date']) <= 7200)){
            			$actionHtml .= "<a href='javascript:void(0);' class='delete-bill' data-id='".$b['id']."'>Delete</a> | ";
        		}

        		$actionHtml .= "<a href='".SITE_URL."modules-nct/invoice-nct/index.php?id=".$b['id']."&download=1'>Download</a>";
    		}

		if($viewTrash){

    			$rows .= "
        			<tr>
            				<td>".$b['bill_number']."</td>
            				<td>".$b['bill_date']."</td>
            				<td>".$b['patient_name']."</td>
            				<td style='color:red;'>".$b['cancel_reason']."</td>
            				<td>
                				<a href='".SITE_URL."modules-nct/invoice-nct/index.php?id=".$b['id']."'>View</a>
            				</td>
        			</tr>
    				";

    			continue;
		}

		else if($viewDue){

			$rows .= "

				<tr>

					<td>".$b['bill_number']."</td>

					<td>".$b['bill_date']."</td>

					<td>".$b['patient_name']."</td>

					<td>₹".$b['total_amount']."</td>

					<td>₹".$b['total_paid']."</td>

					<td style='color:red;font-weight:bold;'>₹".$b['due_amount']."</td>

					<td>".$actionHtml."</td>
				</tr>

			";

		}else{

			$rows .= "
            			<tr ".($b['due_amount'] > 0 ? "style='background-color: rgba(255,0,0,0.06);'" : "").">
                			<td>".$b['bill_number']."</td>

                			<td>".$b['bill_date']."</td>

                			<td>".$b['patient_name']."</td>

					<td>".$b['doctor_name']."</td>

					<td>".$b['referred_doctor']."</td>

                			<td>₹".$b['total_amount']."</td>

					<td>".($b['payments'] ? $b['payments'] : ' ')."</td>

                			<td>".$actionHtml."</td>

            			</tr>
			";
        	}
	}

	/* SHOW MESSAGE IF NO DUES */

	if($viewDue && $rows == ''){

    		$rows = "
        		<tr>
            			<td colspan='7' style='text-align:center;padding:30px;font-weight:bold;color:green;'>
                			There are no pending dues 🎉
            			</td>
        		</tr>
    		";

	}

	if($viewTrash && $rows == ''){

    		$rows = "
        		<tr>
            		<td colspan='5' style='text-align:center;padding:30px;color:gray;'>
               			No cancelled bills found
            		</td>
        	</tr>
    		";
	}

	$pagination = '<div style="margin-top:20px;text-align:center;">';
	$queryParams = $_GET;

	/* PREVIOUS BUTTON */
	if($page > 1){
    		$queryParams['page'] = $page - 1;
    		$prevUrl = '?' . http_build_query($queryParams);

    		$pagination .= '<a href="'.$prevUrl.'" style="margin:5px;">« Prev</a>';
	}

	/* PAGE NUMBERS */
	for($i=1;$i<=$totalPages;$i++){

    		$queryParams['page'] = $i;
    		$url = '?' . http_build_query($queryParams);

    		if($i == $page){
        		$pagination .= "<strong style='margin:5px;'>$i</strong>";
    		}else{
        		$pagination .= "<a href='$url' style='margin:5px;'>$i</a>";
    		}
	}

	/* NEXT BUTTON */
	if($page < $totalPages){
    		$queryParams['page'] = $page + 1;
    		$nextUrl = '?' . http_build_query($queryParams);

    		$pagination .= '<a href="'.$nextUrl.'" style="margin:5px;">Next »</a>';
	}

	$pagination .= '</div>';

        $filePath = DIR_TMPL.$this->module."/".$this->module.".tpl.php";

	if($viewTrash){

    		$tableHeader = "
        		<th>Bill No.</th>
			<th>Date</th>
        		<th>Patient</th>
        		<th>Cancel Reason</th>
        		<th>Action</th>
    		";

	}

	else if($viewDue){

		$tableHeader = "

			<th>Bill No.</th>
			<th>Date</th>
			<th>Patient</th>
			<th>Total Amount</th>
			<th>Total Paid</th>
			<th>Due</th>
			<th>Action</th>

		";

	}else{

		$tableHeader = "

			<th>Bill No.</th>
			<th>Date</th>
			<th>Patient</th>
			<th>Doctor</th>
			<th>Referred By</th>
			<th>Total</th>
			<th>Payments</th>
			<th>Action</th>

		";

	}

        $tpl = new MainTemplater($filePath);
        $tpl = $tpl->parse();

	$tpl = str_replace("%ROWS%", $rows, $tpl);
	$tpl = str_replace("%SEARCH%", $search, $tpl);
	$tpl = str_replace("%DATE%", $date, $tpl);
	$tpl = str_replace("%PAGINATION%", $pagination, $tpl);
	$tpl = str_replace("%TOTAL_COLLECTED%", number_format($totalCollectedAmount,2), $tpl);
	$tpl = str_replace("%TODAY_COLLECTION%", number_format($todayCollectionAmount,2), $tpl);
	$tpl = str_replace("%TOTAL_DUE%", number_format($totalDueAmount,2), $tpl);
	$tpl = str_replace("%TABLE_HEADER%", $tableHeader, $tpl);

	return $tpl;
    }

	/* SEARCH BILL HISTORY */

	public function ajaxSearchBills(){

    		global $db, $sessUserId;

    		$search = isset($_GET['search']) ? trim($_GET['search']) : '';
    		$date   = isset($_GET['date']) ? trim($_GET['date']) : '';

    		$where = "WHERE b.clinic_id=".(int)$sessUserId;

    		if($search != ''){

        		$search = addslashes($search);

        		$where .= " AND (
            			b.bill_number LIKE '%$search%' OR
            			b.patient_name LIKE '%$search%' OR
            			CONCAT(p.first_name,' ',p.last_name) LIKE '%$search%' OR
            			CONCAT(d.first_name,' ',d.last_name) LIKE '%$search%' OR
            			b.referred_doctor LIKE '%$search%'
        		)";
    		}

    		if($date != ''){
        		$where .= " AND DATE(b.bill_date)='$date'";
    		}

    		$bills = $db->pdoQuery("
			SELECT
    				b.id,
    				b.bill_number,
    				b.bill_date,
    				b.total_amount,
    				b.referred_doctor,
				b.created_date,
				b.status,

    			COALESCE(CONCAT(p.first_name,' ',p.last_name), b.patient_name) AS patient_name,
			CASE WHEN d.id IS NOT NULL THEN CONCAT('Dr. ', d.first_name, ' ', d.last_name) ELSE '' END AS doctor_name,
    			IFNULL(SUM(pay.amount),0) AS total_paid,
			MAX(pay.created_at) AS last_payment_time,
			GROUP_CONCAT(CONCAT('₹',pay.amount,' (',pay.payment_method,')') SEPARATOR ',<br>') AS payments,
    			(b.total_amount - IFNULL(SUM(pay.amount),0)) AS due_amount

			FROM tbl_bills b

			LEFT JOIN tbl_users p ON p.id=b.patient_id
			LEFT JOIN tbl_users d ON d.id=b.doctor_id
			LEFT JOIN tbl_bill_payments pay ON pay.bill_id=b.id

			$where

			GROUP BY b.id

			ORDER BY b.id DESC
			LIMIT 50
		")->results();

    		$rows='';

    		foreach($bills as $b){

    			if($b['status'] == 'cancelled'){

        			$actionHtml = "<span style='color:red;font-weight:bold;'>BILL CANCELLED</span>";

    			}else{

        			$actionHtml = "<a href='".SITE_URL."modules-nct/invoice-nct/index.php?id=".$b['id']."'>View</a> | ";

				$canEdit = false;

				$now = time();
				$created = strtotime($b['created_date']);
				$lastPayment = !empty($b['last_payment_time']) ? strtotime($b['last_payment_time']) : null;

				/* RULE 1: IF DUE EXISTS → ALWAYS EDITABLE */
				if($b['due_amount'] > 0){
    					$canEdit = true;
				}

				/* RULE 2: IF FULLY PAID → 2 HOURS FROM LAST PAYMENT */
				else{
    					if($lastPayment && ($now - $lastPayment <= 7200)){
        					$canEdit = true;
    					}
				}

				/* SHOW EDIT BUTTON */
				if($canEdit){
   					$actionHtml .= "<a href='".SITE_URL."billing?bill_id=".$b['id']."&payment_mode=1'>Edit</a> | ";
				}

        			if($b['status'] == 'active' && (time() - strtotime($b['created_date']) <= 7200)){
            				$actionHtml .= "<a href='javascript:void(0);' class='delete-bill' data-id='".$b['id']."'>Delete</a> | ";
        			}

        			$actionHtml .= "<a href='".SITE_URL."modules-nct/invoice-nct/index.php?id=".$b['id']."&download=1'>Download</a>";
    			}

        		$rows .= "
				<tr ".($b['due_amount'] > 0 ? "style='background-color: rgba(255,0,0,0.06);'" : "").">
            				<td>".$b['bill_number']."</td>
            				<td>".$b['bill_date']."</td>
            				<td>".$b['patient_name']."</td>
            				<td>".$b['doctor_name']."</td>
            				<td>".$b['referred_doctor']."</td>
            				<td>₹".$b['total_amount']."</td>
					<td>".($b['payments'] ? $b['payments'] : ' ')."</td>
            				<td>".$actionHtml."</td>
        			</tr>";
    		}

    		echo $rows;
    		exit;
	}

	/* DELETE BILL */

	public function deleteBill(){

    		global $db, $sessUserId;

    		$bill_id = isset($_POST['bill_id']) ? (int)$_POST['bill_id'] : 0;

    		if($bill_id <= 0){
        		echo json_encode(['status'=>false,'message'=>'Invalid Bill']);
        		exit;
    		}

   		// Verify bill belongs to this clinic
    		$bill = $db->pdoQuery("
        		SELECT id, created_date, status
        		FROM tbl_bills
        		WHERE id = ? AND clinic_id = ?
    			", [$bill_id, $sessUserId])->result();

    		if(!$bill){
        		echo json_encode(['status'=>false,'message'=>'Unauthorized']);
        		exit;
    		}

    		// Check already cancelled
    		if($bill['status'] == 'cancelled'){
        		echo json_encode(['status'=>false,'message'=>'Already cancelled']);
        		exit;
    		}

    		// Check 2 hour rule
    		if(time() - strtotime($bill['created_date']) > 7200){
        		echo json_encode(['status'=>false,'message'=>'Delete time expired']);
        		exit;
    		}

    		// UPDATE status (NO DELETE)
		$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

		$db->update('tbl_bills',['status' => 'cancelled','cancel_reason' => $reason,'cancelled_at' => date('Y-m-d H:i:s'),'cancelled_by' => $sessUserId],['id'=>$bill_id]);
    		echo json_encode(['status'=>true,'message'=>'Bill cancelled']);
    		exit;
	}
}
