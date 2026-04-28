<?php
/*
//DEBUG
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
*/

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

class Invoice {

    public function __construct($module,$bill_id){

        foreach ($GLOBALS as $key => $values) {
            $this->$key = $values;
        }

        $this->module = $module;
        $this->bill_id = $bill_id;
    }

    public function getPageContent($template = "invoice-nct.tpl.php"){

        global $db;

        $bill = $db->pdoQuery("
        SELECT
                b.*,
                p.first_name AS patient_first,
                p.last_name AS patient_last,
                p.phone_no,
                COALESCE(p.gender, b.patient_gender) AS patient_gender,
		COALESCE(p.date_of_birth, b.patient_dob) AS date_of_birth,
                d.first_name AS doctor_first,
                d.last_name AS doctor_last
        FROM tbl_bills b
        LEFT JOIN tbl_users p ON p.id=b.patient_id
        LEFT JOIN tbl_users d ON d.id = b.doctor_id
        WHERE b.id=".$this->bill_id."
	AND b.clinic_id=".$this->sessUserId."
        ")->result();

	if(empty($bill)){
    		header("Location: ".SITE_URL."modules-nct/manage_bills-nct/");
    		exit;
	}

	/* GET CLINIC INFO */

	$clinic = $db->pdoQuery("
    		SELECT address,phone_no,clinic_banner,gstin
    		FROM tbl_users
    		WHERE id=".$this->sessUserId
	)->result();

	/* AGE CALCULATION */

	$age = '';

	if(!empty($bill['date_of_birth']) && $bill['date_of_birth'] != '0000-00-00'){

    		$today = new DateTime();
    		$dob = new DateTime($bill['date_of_birth']);
    		$diff = $today->diff($dob);

    		if($diff->y > 0){
        		$age = $diff->y . "Y";
    		}
    		elseif($diff->m > 0){
        		$age = $diff->m . "M";
    		}
   		else{
        		$age = $diff->d . "D";
    		}

	}else{

    		// fallback when DOB is NOT available
    		$age = $bill['patient_age'] ? $bill['patient_age'] . "Y" : '';
	}

        $items = $db->pdoQuery("
            SELECT *
            FROM tbl_bill_items
            WHERE bill_id=".$this->bill_id
        )->results();

	/* GET PAYMENTS */

	$payments = $db->pdoQuery("
    		SELECT amount,payment_method,created_at
    		FROM tbl_bill_payments
    		WHERE bill_id=".$this->bill_id."
	")->results();

	$paymentRows = '';
	$totalPaid = 0;

	foreach($payments as $p){

    		$paymentRows .= "
			<tr>
				<td style='border:1px solid #000;padding:6px;'>".date('d M Y',strtotime($p['created_at']))."</td>
				<td style='border:1px solid #000;padding:6px;'>".$p['payment_method']."</td>
				<td style='border:1px solid #000;padding:6px;text-align:right;'>₹".number_format($p['amount'],2)."</td>
			</tr>";

    		$totalPaid += $p['amount'];
	}

	$dueAmount = $bill['total_amount'] - $totalPaid;

	if($dueAmount < 0){
    		$dueAmount = 0;
	}

        $rows = '';

        foreach($items as $i){

            $rows .= "
            <tr>
                <td>".$i['service_name']."</td>
                <td>".$i['price']."</td>
                <td>".$i['qty']."</td>
                <td>".$i['total']."</td>
            </tr>
            ";
        }

        $filePath = DIR_TMPL.$this->module."/".$template;

	$discountPercent = '';

	if($bill['subtotal'] > 0 && $bill['discount'] > 0){
    		$percent = ($bill['discount'] / $bill['subtotal']) * 100;
    		$discountPercent = " (".round($percent,2)."%)";
	}

        $replace = array(

		"%BILL_NUMBER%" => $bill['bill_number'],
		"%BILL_ID%" => $this->bill_id,
		"%GSTIN_LINE%" => !empty($clinic['gstin']) ? '<div class="gstin-line"><b>GSTIN:</b> '.$clinic['gstin'].'</div>' : '',
		"%BILL_STATUS%" => $bill['status'],
		"%PATIENT%" => !empty($bill['patient_first']) ? $bill['patient_first']." ".$bill['patient_last'] : $bill['patient_name'],
		"%PHONE%" => !empty($bill['phone_no']) ? $bill['phone_no'] : $bill['patient_phone'],
    		"%AGE%" => $age,
		"%GENDER%" => ucfirst($bill['patient_gender']),
		"%DOCTOR%" => !empty($bill['doctor_first']) ? "Dr. ".$bill['doctor_first']." ".$bill['doctor_last'] : "",
    		"%REFERRED%" => $bill['referred_doctor'],
    		"%DATE%" => $bill['bill_date'],
		"%SUBTOTAL%" => number_format($bill['subtotal'],2),
		"%DISCOUNT%" => number_format($bill['discount'],2).$discountPercent,
    		"%TOTAL%" => number_format($bill['total_amount'],2),
		"%PAYMENT_ROWS%" => $paymentRows,
		"%PAID%" => number_format($totalPaid,2),
		"%DUE%" => number_format($dueAmount,2),
    		"%ROWS%" => $rows,

		"%PRINT_BUTTON_HTML%" => ($bill['status'] != 'cancelled') ? '<div style="margin-left: 700px; margin-top:20px; margin-bottom:15px" class="print-button">
            			<a href="index.php?id='.$this->bill_id.'&download=1" class="btn btn-ehw-green print-button">
                			Print Invoice
            			</a>
       			</div>'
    		: '',

		"%CANCELLED_WATERMARK%" => ($bill['status'] == 'cancelled') ? '<div class="cancelled-watermark">CANCELLED</div>' : '',

		/* CLINIC HEADER */
		"%CLINIC_ADDRESS%" => $clinic['address'],
		"%CLINIC_PHONE%" => $clinic['phone_no'],
		"%CLINIC_BANNER%" => !empty($clinic['clinic_banner']) ? SITE_URL."upload-nct/clinicBanner-nct/".$clinic['clinic_banner'] : ""
	);

        $tpl = new MainTemplater($filePath);
        $tpl = $tpl->parse();

        return str_replace(array_keys($replace), array_values($replace), $tpl);

    }

    public function downloadPDF(){

    	// get the exact same HTML used in webpage
    	$html = $this->getPageContent("invoice-pdf.tpl.php");

    	// remove print button from PDF
	$html = preg_replace('/<a[^>]*download=1[^>]*>.*?<\/a>/s','',$html);

    	$mpdf = new \Mpdf\Mpdf([
        	'tempDir' => $_SERVER['DOCUMENT_ROOT'].'/tmp',
        	'margin_top' => 20,
        	'margin_bottom' => 25
    	]);

    	$mpdf->SetHTMLFooter('
		<div style="border-top:1px solid #ddd; margin-bottom:5px; color:#93d694;"></div>

		<table width="100%">
		<tr>
			<td style="font-size:12px; color:#10443e;">Powered by Elevate Health World</td>
			<td style="text-align:right;font-size:12px; color:#10443e;">www.elevatehealthworld.com</td>
		</tr>
		</table>
	');

	$stylesheet = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/themes-nct/css-nct/style-nct.css');

	global $db;

	$bill = $db->pdoQuery("SELECT b.bill_number, p.first_name, p.last_name, b.patient_name
    		FROM tbl_bills b
    		LEFT JOIN tbl_users p ON p.id = b.patient_id
    		WHERE b.id=".$this->bill_id
	)->result();

	$mpdf->WriteHTML($stylesheet,1);
	$mpdf->WriteHTML($html,2);
	$patientName = !empty($bill['patient_first']) ? $bill['patient_first']." ".$bill['patient_last'] : $bill['patient_name'];
	$patientName = preg_replace('/[^A-Za-z0-9 ]/', '', $patientName);

	$filename = "Invoice_".$patientName."_".$bill['bill_number'].".pdf";

	$mpdf->Output($filename,"I");
    }

}
