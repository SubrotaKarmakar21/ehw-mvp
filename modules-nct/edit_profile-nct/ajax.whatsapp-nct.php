<?php

require_once("../../includes-nct/config-nct.php");

$action = isset($_POST['action']) ? $_POST['action'] : '';

if($action == "send_otp"){

    $number = $_POST['whatsapp_number'];

    if(strlen($number) != 10){
        echo "Invalid number";
        exit;
    }

    $otp = rand(100000,999999);

    $user_id = $_SESSION['sessUserId'];

    $db->insert("tbl_whatsapp_otp",[
        "user_id"=>$user_id,
        "whatsapp_number"=>$number,
        "otp"=>$otp,
        "status"=>"pending",
        "created_at"=>date("Y-m-d H:i:s")
    ]);

    /* SEND OTP VIA WHATSAPP API */

    $message = "Your Elevate Health World verification code is ".$otp;

    sendWhatsappMessage("91".$number,$otp);

    echo "OTP_SENT";

    exit;
}

function sendWhatsappMessage($number,$otp){

    $token = WHATSAPP_TOKEN;
    $phone_number_id = WHATSAPP_PHONE_ID;

    $url = "https://graph.facebook.com/v22.0/".$phone_number_id."/messages";

    $data = [
        "messaging_product"=>"whatsapp",
        "to"=>$number,
        "type"=>"template",
        "template"=>[
            "name"=>"ehw_login_otp",
            "language"=>[
                "code"=>"en"
            ],
            "components"=>[
    		[
        		"type"=>"body",
        		"parameters"=>[
            			[
                			"type"=>"text",
                			"text"=>$otp
            			]
        	]
    	],
    [
        "type"=>"button",
        "sub_type"=>"url",
        "index"=>"0",
        "parameters"=>[
            [
                "type"=>"text",
                "text"=>$otp
            ]
        ]
    ]
]
        ]
    ];

    $ch = curl_init();

    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($data));

    curl_setopt($ch,CURLOPT_HTTPHEADER,[
        "Content-Type: application/json",
        "Authorization: Bearer ".$token
    ]);

    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

    $response = curl_exec($ch);

    curl_close($ch);

    file_put_contents(
        "/var/www/html/whatsapp/send_log.txt",
        date("Y-m-d H:i:s")."\n".$response."\n\n",
        FILE_APPEND
    );

    return $response;
}

if($action == "verify_otp"){

	$otp = $_POST['otp'];

	$user_id = $_SESSION['sessUserId'];

	$row = $db->pdoQuery("

		SELECT * FROM tbl_whatsapp_otp
		WHERE user_id = ?
		AND otp = ?
		AND status='pending'
		ORDER BY id DESC
		LIMIT 1

	",[$user_id,$otp])->result();

	if($row){

		$db->update("tbl_users",["whatsapp_verified"=>"y"],["id"=>$user_id]);

		$db->update("tbl_whatsapp_otp",["status"=>"verified"],["id"=>$row['id']]);

		echo "VERIFIED";

	}else{

		echo "INVALID";

	}

	exit;

}
