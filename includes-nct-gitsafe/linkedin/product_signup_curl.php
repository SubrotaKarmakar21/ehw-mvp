<?php
	function cryptoJsAesEncrypt($passphrase, $value){
	    $salt = openssl_random_pseudo_bytes(8);
	    $salted = '';
	    $dx = '';
	    while (strlen($salted) < 48) {
	        $dx = md5($dx.$passphrase.$salt, true);
	        $salted .= $dx;
	    }
	    $key = substr($salted, 0, 32);
	    $iv  = substr($salted, 32,16);
	    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
	    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
	    return json_encode($data);
	}

	function get_web_page($curl_data )
	{

		$url = "https://portal.ncrypted.com/dynamic_rfp_api/product_signup"; 
		//$url = "https://nctportal.ncryptedprojects.com/dynamic_rfp_api/product_signup"; 
	    $options = array( 
	        CURLOPT_RETURNTRANSFER => true,         // return web page 
	        CURLOPT_HEADER         => false,        // don't return headers 
	        CURLOPT_FOLLOWLOCATION => true,         // follow redirects 
	        CURLOPT_ENCODING       => "",           // handle all encodings 
	        CURLOPT_USERAGENT      => "spider",     // who am i 
	        CURLOPT_AUTOREFERER    => true,         // set referer on redirect 
	        CURLOPT_CONNECTTIMEOUT => 120,          // timeout on connect 
	        CURLOPT_TIMEOUT        => 120,          // timeout on response 
	        CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects 
	        CURLOPT_POST           => 1,            // i am sending post data 
	        CURLOPT_POSTFIELDS     => $curl_data,    // this are my post vars 
	        CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl 
	        CURLOPT_SSL_VERIFYPEER => false,        // 
	        CURLOPT_VERBOSE        => 1                // 
	    ); 

	    $ch      = curl_init($url); 
	    curl_setopt_array($ch,$options); 
	    $content = curl_exec($ch); 
	    $err     = curl_errno($ch); 
	    $errmsg  = curl_error($ch) ; 
	    $header  = curl_getinfo($ch); 
	    curl_close($ch); 
	  	$header['content'] = $content; 
	    return $header; 
	} 

	function signupNewsletter($curl_data=array())
	{
		# code...
/*		$curl_data['name'] = $value['name'];
		$curl_data['email'] = $value['email']
		$curl_data['product'] = 'airbnb';
		$curl_data['url'] = 'http://www.airnbnb.com/';
		$curl_data['ip'] = '221.120.227.235';*/
		if(!empty($curl_data)){
			$encrypted = cryptoJsAesEncrypt("123",$curl_data);
			$full_data_array['full_data'] = $encrypted;
			$response = get_web_page($full_data_array);
		}
 

	}
	//print_r($response);

	/*
	$arr['name'] = 'yash';
	$arr['email'] = 'yash.pandya@ncrypted.com';
	$arr['product'] = 'Bistro';
	$arr['url'] = 'www.google.com';
	$arr['ip'] = '192.168.100.128';
	signupNewsletter($arr);
	*/
?>
