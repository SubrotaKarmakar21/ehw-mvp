<?php

function get_view($tpl_path, $replace = array())
{
    $tpl        = new MainTemplater($tpl_path);
    $parsed_tpl = $tpl->parse();
    if (!empty($replace)) {
        return str_replace(array_keys($replace), array_values($replace), $parsed_tpl);

    } else {
        return $parsed_tpl;
    }
}

function export_to_csv($filename = 'info.csv',$final_result = array()){
    // Set headers to prompt download and specify content type
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Pragma: no-cache');
    header('Expires: 0');

        // Open output stream
    $output = fopen('php://output', 'w');
        // Write data to CSV
    foreach ($final_result as $row) {
        fputcsv($output, $row);
    }
        // Close the output stream
    fclose($output);
    exit;
}

function getMetaTags($metaArray){
    $content = null;
    $content = '<meta name="description" content="' . $metaArray["description"] . '" />
    <meta name="keywords" content="' . $metaArray["keywords"] . '" />
    <meta name="author" content="' . $metaArray["author"] . '" />';

    if (isset($metaArray["nocache"]) && $metaArray["nocache"] == true) {
        $content .= '<meta HTTP-EQUIV="CACHE-CONTROL" content="NO-CACHE" />
        ';
    }

    return sanitize_output($content);
}

function issetor(&$var, $default = false){
    return isset($var) ? $var : $default;
}


/* Send SMTP Mail */
function generateEmailTemplate($type, $arrayCont){
    global $sessUserId, $db, $fb;
    //start:: get sendMailTo user's langId and choose mail template accordingly
    $query = $db->select('tbl_email_templates', array("subject", "templates"), array("constant" => $type))->result();

    $q = $query;

    if(!empty($q))
    {
        $subject = trim(stripslashes($q["subject"]));
        $message = trim(stripslashes($q["templates"]));

        $subject = str_replace("###SITE_NM###", SITE_NM, $subject);
        $message = str_replace("###SITE_LOGO_URL###", SITE_IMG . SITE_LOGO, $message);
        $message = str_replace("###SITE_URL###", SITE_URL, $message);
        $message = str_replace("###SITE_NM###", SITE_NM, $message);
        $message = str_replace("###YEAR###", date('Y'), $message);
        $message = str_replace("###CONTACT_URL###", SITE_CONTACTUS, $message);
    }else{
        $subject = "";
        $message = "";
    }

    $array_keys = (array_keys($arrayCont));

    for ($i = 0; $i < count($array_keys); $i++) {
        $message = str_replace("###" . $array_keys[$i] . "###", "" . $arrayCont[$array_keys[$i]] . "", $message);
        $subject = str_replace("###" . $array_keys[$i] . "###", "" . $arrayCont[$array_keys[$i]] . "", $subject);
    }

    $data['message'] = $message;
    $data['subject'] = $subject;
    return $data;
}

function sendEmailAddress($to, $subject, $message,$attachment = array()) {
    if($_SERVER["SERVER_NAME"] == 'localhost') {
        return true;
    }
    require_once("class.phpmailer.php");
    require_once("class.smtp.php");
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPDebug = 2;
    $mail->SMTPAuth = true;
    //mail via gmail
    $mail->SMTPSecure = 'tls';
    $mail->Host = SMTP_HOST;
    $mail->Port = SMTP_PORT;
    $mail->IsHTML(true);
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    //$mail->SetFrom(SMTP_USERNAME);
    $mail->SetFrom(FROM_EMAIL, FROM_NM);
    $mail->AddReplyTo(FROM_EMAIL, FROM_NM);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AddAddress($to);
    if (!empty($attachment)) {
        $mail->AddAttachment($attachment['path_to_file'] . $attachment['name_of_file'], $name = $attachment['name_of_file'], $encoding = 'base64', $type = 'application/pdf');
    }
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $result = true;
    if (!$mail->Send()) {
        //echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
    return $result;
}

function convertDate($date, $time = false, $what = 'default'){
    if ($what == 'wherecond') {
        return date('Y-m-d', strtotime($date));
    } else if ($what == 'display') {
        return date('M d, Y h:i A', strtotime($date));
    } else if ($what == 'onlyDate') {
        return date('M d, Y', strtotime($date));
    } else if ($what == 'gmail') {
        return date('D, M d, Y - h:i A', strtotime($date));
        //Tue, Jul 16, 2013 at 12:14 PM
    } else if ($what == 'ridewrap_admin_view') {
        return date('Y-m-d H:i:s', strtotime($date));
    } else if ($what == 'ridewrap_admin_grid') {
        return date('Y-m-d H:i:s', strtotime($date));
    } else if ($what == 'elevate_health') {
        // Format the date with the custom ordinal suffix
        $formattedDate = date('jS M, Y l', strtotime($date));
        return $formattedDate;
    } else if ($what == 'default') {
        if (trim($date) != '' && $date != '0000-00-00' && $date != '1970-01-01') {
            if (!$time) {
                $retDt = date('d-m-Y', strtotime($date));
                return $retDt == '01-01-1970' ? '' : $retDt;
            } else {
                '1970-01-01 01:00:00';
                '01-01-1970 01:00 AM';
                $retDt = date('d-m-Y h:i A', strtotime($date));
                return $retDt == '01-01-1970 01:00 AM' ? '' : $retDt;
            }
        } else {
            return '';
        }

    } else if ($what == 'db') {
        if (trim($date) != '' && $date != '0000-00-00' && $date != '1970-01-01') {
            if (!$time) {
                $retDt = date('Y-m-d', strtotime($date));
                return $retDt == '1970-01-01' ? '' : $retDt;
            } else {
                $retDt = date('Y-m-d H:i:s', strtotime($date));
                return $retDt == '1970-01-01 01:00:00' ? '' : $retDt;
            }
        } else {
            return '';
        }

    } else if ($what == 'withSuffix') {
        $timestamp = strtotime($date);
        $day = date('j', $timestamp);
        $suffix = 'th';

        if (!in_array($day % 100, [11, 12, 13])) {
            switch ($day % 10) {
                case 1: $suffix = 'st'; break;
                case 2: $suffix = 'nd'; break;
                case 3: $suffix = 'rd'; break;
            }
        }

        return date('M ', $timestamp) . $day . $suffix . ', ' . date('Y', $timestamp);

    } 
}


function makeConstantFile($default_lang = 0, $insertId = 0) {

    global $db, $adminUserId;

    if ($default_lang > 0 && $insertId > 0) {

        $qrysel1 = $db->select("tbl_language", "*", array("default_lan" => "y"), "", "", 0)->results();

        foreach ($qrysel1 as $fetchSel) {

            $fp = fopen(DIR_INC . "language-nct/" . $insertId . ".php", "wb");

            $content = '';

            $qsel1 = $db->select("tbl_constant", "*", array("languageId" => $fetchSel['id']))->results();

            $content .= '<?php ';

            foreach ($qsel1 as $fetchSel1) {

                $content .= ' define("' . $fetchSel1['constantName'] . '","' . $fetchSel1['constantValue'] . '"); ';

            }

            $content .= ' ?>';

            fwrite($fp, $content);

            fclose($fp);

        }

    } else {
        $files = glob(DIR_INC . 'language-nct/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $qrysel1 = $db->select("tbl_language", "*", array("status" => "a"), "", "", 0)->results();

        foreach ($qrysel1 as $fetchSel) {
            $fp = fopen(DIR_INC . "language-nct/" . $fetchSel['id'] . ".php", "wb");

            $content = '';

            $qsel1 = $db->select("tbl_constant", "*", array("languageId" => $fetchSel['id']))->results();
            $content .= '<?php ';
            foreach ($qsel1 as $fetchSel1) {
                $content .= ' define("' . $fetchSel1['constantName'] . '","' . $fetchSel1['constantValue'] . '"); ';
            }
            $content .= ' ?>';
            fwrite($fp, $content);
            fclose($fp);



            /*for javascript*/

            $js_filePath = DIR_INC . "language-nct/" . $fetchSel['id'] . ".js";
            if (is_file($js_filePath)) {
                unlink($js_filePath);
            }

            $js_fp      = fopen($js_filePath, (file_exists($js_filePath)) ? 'a' : 'w');
            $js_content = '';

            $js_content .= 'var lang = { ';
            foreach ($qsel1 as $fetchSel1) {
                $js_content .= $fetchSel1['constantName'] . ' : "' . trim(preg_replace('/\s+/', ' ', $fetchSel1['constantValue'])) . '", ';
            }
            $js_content .= ' };';
            fwrite($js_fp, $js_content);
            fclose($js_fp);

        }

    }

}

function undefinedUserConstant($constantNm = null, $templateFile = null, $sendMail = true){
    if($sendMail){
        //sendEmailAddress('ashish.joshi@ncrypted.com', 'Error in ' . SITE_URL, $constantNm . ' inside '.$templateFile . ' is not defined.');    
    }else{        
        $content = "\n\n---------------------------------------------------" . date("Y-m-d H:i:s")."---------------------------------------------------";
        $content .= "\n\nSubject: Error in ". SITE_URL;
        $content .= "\n\n".$constantNm . " inside ".$templateFile . " is not defined.";
        $h = fopen(DIR_INC."development_notes.txt", "a");
        $r = fwrite($h, $content);
        fclose($h);
    }
    return $constantNm;   
}

//get the full path of all files in the directory and all subdirectories of a directory. 
function find_all_files($dir) { 
    if(!is_dir($dir)){
        return array();
    }

    $root = scandir($dir); 
    foreach($root as $value) 
    { 
        if($value === '.' || $value === '..') {continue;} 
        if(is_file("$dir/$value")) {$result[]="$dir/$value";continue;} 
        foreach(find_all_files("$dir/$value") as $value) 
        { 
            $result[]=$value; 
        } 
    } 
    return $result; 
} 
/*
start:: make entry of url and params in call table
 */
function url_origin($s, $use_forwarded_host = false){
    $ssl      = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on');
    $sp       = strtolower($s['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
    $port     = $s['SERVER_PORT'];
    $port     = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
    $host     = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
    $host     = isset($host) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

function full_url($s, $use_forwarded_host = false){
    return url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
}

/*
end:: make entry of url and params in call table
 */

function msgExit($msg = 'undefined', $bool = false){
    echo json_encode(array(
        'status' => $bool,
        'msg'    => $msg,
    ));
    exit;
}

function fatal_handler($sendMail = true){
    $errfile = "unknown file";
    $errstr  = "shutdown";
    $errno   = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if ($error !== null) {
        $errno   = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr  = $error["message"];

        if ($errno != 32 && $errno != 2) {
            if($sendMail){
                //sendEmailAddress('ashish.joshi@ncrypted.com', 'Error in ' . SITE_URL, format_error($errno, $errstr, $errfile, $errline));    
            }else{
                $content = "\n\n---------------------------------------------------" . date("Y-m-d H:i:s")."---------------------------------------------------";
                $content .= "\n\nSubject: Error in ". SITE_URL;
                $content .= "\n\n".format_error($errno, $errstr, $errfile, $errline);
                $h = fopen(DIR_INC."development_notes.txt", "a");
                $r = fwrite($h, $content);
                fclose($h);
            }
            
        }
    }
}

function pri($data, $exit=true) {
    print '<pre>'; print_r($data); print '</pre>';
    (($exit)?exit():'');
}

function format_error($errno, $errstr, $errfile, $errline){
    $trace = print_r(debug_backtrace(true), true);

    $content = "<table><thead bgcolor='#c8c8c8'><th>Item</th><th>Description</th></thead><tbody>";
    $content .= "<tr valign='top'><td><b>Error</b></td><td><pre>$errstr</pre></td></tr>";
    $content .= "<tr valign='top'><td><b>Errno</b></td><td><pre>$errno</pre></td></tr>";
    $content .= "<tr valign='top'><td><b>File</b></td><td>$errfile</td></tr>";
    $content .= "<tr valign='top'><td><b>Line</b></td><td>$errline</td></tr>";
    $content .= "<tr valign='top'><td><b>Trace</b></td><td><pre>$trace</pre></td></tr>";
    $content .= '</tbody></table>';

    return $content;
}

function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle){
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function regenerateSession($reload = false){
    // This token is used by forms to prproject cross site forgery attempts
    if (!isset($_SESSION['nonce']) || $reload) {
        $_SESSION['nonce'] = md5(microtime(true));
    }

    if (!isset($_SESSION['IPaddress']) || $reload) {
        $_SESSION['IPaddress'] = $_SERVER['REMOTE_ADDR'];
    }

    if (!isset($_SESSION['userAgent']) || $reload) {
        $_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
    }

    //$_SESSION['user_id'] = $this->user->getId();

    // Set current session to expire in 1 minute
    $_SESSION['OBSOLETE'] = true;
    $_SESSION['EXPIRES']  = time() + 60;

    // Create new session without destroying the old one
    session_regenerate_id(false);

    // Grab current session ID and close both sessions to allow other scripts to use them
    $newSession = session_id();
    session_write_close();

    // Set session ID to the new one, and start it back up again
    session_id($newSession);
    session_start();

    // Don't want this one to expire
    unset($_SESSION['OBSOLETE']);
    unset($_SESSION['EXPIRES']);
}

function setFormToken(){
    $_SESSION['form_token'] = md5(time());
    //dump($_SESSION['form_token']);
    return $_SESSION['form_token'];
}

function checkFormToken($token = null){
    return true;
    /////
    if (isset($_SERVER["HTTP_ORIGIN"])) {

        if (strpos(SITE_URL, $_SERVER["HTTP_ORIGIN"]) !== 0) {
            exit("CSRF protection in POST request: detected invalid Origin header: " . $_SERVER["HTTP_ORIGIN"]);
        }
    }
    /////

    $sessToken = isset($_SESSION['form_token']) ? $_SESSION['form_token'] : null;
    unset($_SESSION['form_token']);
    //sesstion should not be valid after 5 minutes
    $duration = 5 * 60;
    //dump(array($token,$sessToken));
    if ($sessToken && $token == $sessToken) {
        $time = ($duration - (time() - $sessToken));
        return ($time <= 0) ? true : false;
    } else {
        return false;
    }
}

function pushToAndroid($token, $message){
    $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';

    $fields = array(
        'registration_ids' => $token,
        'notification'     => array(
            'title' => $message['title'],
            'body'  => $message['body'],
            'sound' => 'default',
        ),
        'data'             => array(
            'pkg' => 'xyz',
        ),
    );

    $headers = array(
        'Authorization:key=' . SERVER_KEY,
        'Content-Type:application/json',
    );
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);

    curl_close($ch);

    return $result;
}

function replace_null_with_empty_string($array){
    if (!is_array($array)) {
        return "";
    }
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = replace_null_with_empty_string($value);
        } else {
            if (is_null($value)) {
                $array[$key] = "";
            }

        }
    }
    return $array;
}

/*
 * function for checking whether user has enabled certain type of notification or not
 * returns boolean(true|false)
 */
function check_noti_enable($userId, $typeId){
    global $db;
    if ($userId > 0 && $typeId > 0) {
        $check = getTableValue('tbl_notification_settings', 'id', array(
            'userId' => $userId,
            'typeId' => $typeId,
        ));
    }
    return ($check > 0) ? false : true;
}

//for infinite scrolling
function getPagerData($numHits, $limit, $page){
    $numHits  = (int) $numHits;
    $limit    = max((int) $limit, 1);
    $page     = (int) $page;
    $numPages = ceil($numHits / $limit);

    $page = max($page, 1);
    $page = min($page, $numPages);

    $offset = ($page - 1) * $limit;

    $ret = new stdClass;

    $ret->offset   = $offset;
    $ret->limit    = $limit;
    $ret->numPages = $numPages;
    $ret->page     = $page;

    return $ret;
}

function renderStarRating($rating, $maxRating = 5, $html5 = false){
    if ($html5) {
        $fullStar  = "&#9733;";
        $halfStar  = $fullStar;
        $emptyStar = "&#9734;";
    } else {
        $fullStar  = "<i class = 'fa fa-star'></i>";
        $halfStar  = "<i class = 'fa fa-star-half-full'></i>";
        $emptyStar = "<i class = 'fa fa-star-o'></i>";
    }

    $rating = $rating <= $maxRating ? $rating : $maxRating;

    $fullStarCount  = (int) $rating;
    $halfStarCount  = ceil($rating) - $fullStarCount;
    $emptyStarCount = $maxRating - $fullStarCount - $halfStarCount;

    $html = str_repeat($fullStar, $fullStarCount);
    $html .= str_repeat($halfStar, $halfStarCount);
    $html .= str_repeat($emptyStar, $emptyStarCount);

    return $html;
}

function getUserData($userId = null){
    global $db;
    $userId = ($userId != null) ? $userId : (isset($_SESSION["userId"]) ? $_SESSION["userId"] : null);
    if ($userId != null) {
        $result = $db->select('tbl_users', '*', array('id' => $userId))->result();
        return $result;
    } else {
        return null;
    }
}

function makeSlug($string, $table, $field, $whereCol, $extra = 'url', $id = null){
    global $fb;
    $slug = trim($string); // trim the string

    if ($extra == 'url') {
        $slug = preg_replace('/[^a-zA-Z0-9 -]/', '', $slug); // only take alphanumerical characters, but keep the spaces and dashes too...
        $slug = str_replace(' ', '-', $slug); // replace spaces by dashes
    } elseif ($extra == 'name') {
        $slug = preg_replace('/[^a-zA-Z0-9]/', '', $slug); // only take alphanumerical characters, but keep the spaces and dashes too...
    }
    $slug = strtolower($slug);
    //$fb->trace('trace');
    if ($id != null && $field != null) {
        //in case edit
        //exist except given id
        $does_exist = getTableValue($table, $field, array("$whereCol" => $slug, "AND $field <>" => $id));

        if (isset($does_exist) && $does_exist != "") {
            return $slug . generateRandString(4);
        } else {
            return $slug;
        }
    } else {
        $does_exist = getTableValue($table, $field, array("$whereCol" => $slug));

        if (isset($does_exist) && $does_exist != "") {
            return $slug . generateRandString(4);
        } else {
            return $slug;
        }
    }
}

function String_crop($string = null, $noChar = 0){

    $CapsString = ucwords(strtolower($string));
    $StringLen  = strlen($CapsString);

    if ($noChar == 0) {$noChar = strlen($CapsString);}

    if ($StringLen > $noChar) {
        return substr($CapsString, 0, $noChar) . '..';
    } else {
        return substr($CapsString, 0, $noChar);
    }
}

/**
 * Dump helper. Functions to dump variables to the screen, in a nicley formatted manner.
 */
if (!function_exists('dump')) {
    function dump($var, $label = 'Dump', $echo = true)
    {
        // Store dump in variable
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        // Add formatting
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre style="background: #FFFEEF; color: #000; border: 1px dotted #000; padding: 10px; margin: 10px 0; text-align: left;">' . $label . ' => ' . $output . '</pre>';
        // Output
        if ($echo == true) {
            echo $output;
        } else {
            return $output;
        }
    }

}
/**
 * Dump helper.
 * Functions to dump variables to the screen, in a nicley formatted manner.
 */
if (!function_exists('dump_exit')) {
    function dump_exit($var, $label = 'Dump', $echo = true)
    {
        dump($var, $label, $echo);
        exit;
    }
}

function getTotalRows($tableName, $condition = '', $countField = '*'){

    global $db;
    //$db->select($tableName,$countField,$condition);

    $qSel = "SELECT * from " . $tableName . " WHERE " . $condition;

    $qrysel0   = $db->pdoQuery($qSel);
    $totlaRows = $qrysel0->affectedRows();
    return $totlaRows;
}

/*
 * returns parsed html
 * @author Ashish Joshi
 */


function get_link($page, $content = ''){
    global $db, $sessuser_type;

    switch ($page) {
        case 'home':{
            $url = SITE_URL . $content;
            break;
        }
        case 'wallet':{
            $url = SITE_URL . 'wallet/' . $content;
            break;
        }
        case 'paypal_notify':{
            $url = SITE_URL . 'payment/notify/' . $content;
            break;
        }
        case 'paypal_failed':{
            $url = SITE_URL . 'payment/failed/' . $content;
            break;
        }
        case 'paypal_thankyou':{
            $url = SITE_URL . 'payment/thankyou/' . $content;
            break;
        }
        default:{
            $url = SITE_URL;
            break;
        }
    }
    return $url;
}

function getAdminUnreadNotificationsCount(){
    global $db, $adminUserId;

    $get_notifications_count = $db->pdoQuery("SELECT COUNT(*) as notifications_count FROM tbl_admin_notifications WHERE admin_id = " . $adminUserId . " AND is_notified = 'n' AND is_read = 'n'")->result();
    return $get_notifications_count['notifications_count'];
}

/* Functions for getting time diffrance */
function get_time_difference($start, $end){
    $uts['start'] = strtotime($start);
    $uts['end']   = strtotime($end);
    if ($uts['start'] !== -1 && $uts['end'] !== -1) {
        if ($uts['end'] >= $uts['start']) {
            $diff = $uts['end'] - $uts['start'];
            if ($days = intval((floor($diff / 86400)))) {
                $diff = $diff % 86400;
            }

            if ($hours = intval((floor($diff / 3600)))) {
                $diff = $diff % 3600;
            }

            if ($minutes = intval((floor($diff / 60)))) {
                $diff = $diff % 60;
            }

            $diff = intval($diff);
            return (array(
                'days'    => $days,
                'hours'   => $hours,
                'minutes' => $minutes,
                'seconds' => $diff,
            ));
        } else {
            trigger_error("Ending date/time is earlier than the start date/time", E_USER_WARNING);
        }
    } else {
        trigger_error("Invalid date/time data detected", E_USER_WARNING);
    }
    return (false);
}

function getAllNotificationsAdmin(){
    global $db, $adminUserId;

    $query = "SELECT *
    FROM tbl_admin_notifications
    WHERE admin_id = " . filtering($adminUserId, 'input', 'int') . "
    ORDER BY id DESC LIMIT 0, 5";

    $get_notifications = $db->pdoQuery($query)->results();

    if ($get_notifications) {

        $notification        = new MainTemplater(DIR_ADMIN_TMPL . "/notification-li-nct.tpl.php");
        $notification_parsed = $notification->parse();

        $field = array(
            '%NOTIFICATION%',
            '%NOTIFICATION_URL%',
            '%NOTIFICATION_TITLE%',
            '%NOTIFICATION_DATE%',
            '%TIME_AGO%',
        );
        $counter      = 0;
        $final_result = null;
        foreach ($get_notifications as $notification) {

            $notification_date = date("d M, Y", strtotime($notification['date_added']));
            $response          = get_time_difference($notification['date_added'], date("Y-m-d H:i:s"));

            if ($response['days']) {
                $time_ago = $response['days'] . " Days ago";
            } else if ($response['hours']) {
                $time_ago = $response['hours'] . " Hours ago";
            } else if ($response['minutes']) {
                $time_ago = $response['minutes'] . " Mins ago";
            } else if ($response['seconds']) {
                $time_ago = $response['seconds'] . " Secs ago";
            }

            $type = $notification['type'];
            //type: new_user, dispute, new_project, contact_us

            switch ($type) {
                case 'new_user':{
                    $user_details       = $db->select("tbl_users", "*", array("id" => $notification['entity_id']))->result();
                    $notification_text  = "New user with user name " . $user_details['userName'] . " has been registered.";
                    $notification_url   = SITE_ADM_MOD . "users-nct";
                    $notification_title = "New user registered.";
                    break;
                }

            }

            $field_replace = array(
                filtering($notification_text),
                filtering($notification_url),
                filtering($notification_title),
                $notification_date,
                $time_ago,
            );

            $final_result .= str_replace($field, $field_replace, $notification_parsed);
            $db->update("tbl_admin_notifications", array("is_notified" => 'y'), array("id" => $notification['id']));
            $counter++;
        }
    } else {
        $final_result = '<li id="no_notification"> <p>No new notification.</p> </li>';
    }

    return $final_result;
}

function uploadFile($file, $tbl, $col, $id_col, $id = 0, $dir_path="", $site_path=""){
    global $db;
    $result = null;
    if (!$file['name']) {
        return false;
    }

    if (!empty($tbl) && !empty($col) && !empty($id_col) && !empty($id)) {
        $result = getTableValue($tbl, $col, array($id_col => $id)); //get old image name
    }
    $file_title  = $file['name'];
    $folder      = $dir_path;
    $path_folder = $site_path;
    $file_name   = strtolower(pathinfo($file['name'], PATHINFO_FILENAME));
    $ext         = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $uniqer      = md5(uniqid(rand(), 1));
    $file_name   = $uniqer . '.' . $ext;
    if ($folder && !is_dir($folder)) {
        mkdir($folder, 0777);
    }
    $uploadfile = $folder . $file_name;

    copy($file['tmp_name'], $uploadfile);
    if (!empty($result)) {
        // remove old image after new image is uploaded successfully
        $filepath = $folder;
        if (file_exists($filepath . $result)) {
            unlink($filepath . $result);
        }
    }
    return array("filepath" => $path_folder, "file_name" => $file_name, 'actual_file_name' => $file['name']);
}

function filtering($value = '', $type = 'output', $valType = 'string', $funcArray = ''){
    global $abuse_array, $abuse_array_value;

    if ($valType != 'int' && $type == 'output') {
        if(!empty($abuse_array))
        {
            $value = str_ireplace($abuse_array, $abuse_array_value, $value);    
        }
    }

    if ($type == 'input' && $valType == 'string') {
        $value = str_replace('<', '< ', $value);
    }

    $content = $filterValues = '';
    if ($valType == 'int') {
        $filterValues = (isset($value) ? (int) strip_tags(trim($value)) : 0);
    }

    if ($valType == 'float') {
        $filterValues = (isset($value) ? (float) strip_tags(trim($value)) : 0);
    } else if ($valType == 'string') {
        $filterValues = (isset($value) ? (string) strip_tags(trim($value)) : null);
    } else if ($valType == 'text') {
        $filterValues = (isset($value) ? (string) trim($value) : null);
    } else {
        $filterValues = (isset($value) ? trim($value) : null);
    }

    if ($type == 'input') {
        //$content = mysql_real_escape_string($filterValues);
        //$content = $filterValues;
        //$value = str_replace('<', '< ', $filterValues);
        $content = addslashes($filterValues);
    } else if ($type == 'output') {
        if ($valType == 'string') {
            $filterValues = html_entity_decode($filterValues);
        }

        $value   = str_replace(array('\r', '\n', ''), array('', '', ''), $filterValues);
        $content = stripslashes($value);
    } else {
        $content = $filterValues;
    }

    if ($funcArray != '') {
        $funcArray = explode(',', $funcArray);
        foreach ($funcArray as $functions) {
            if ($functions != '' && $functions != ' ') {
                if (function_exists($functions)) {
                    $content = $functions($content);
                }
            }
        }
    }

    return $content;
}
/////////////////////////////////////////////////////////////

function redirectPage($url){
    header('Location:' . $url);
    exit;
}

function redirectErrorPage($error){
    echo $error;
    //redirectPage(SITE_URL.'modules/error?u='.base64_encode($error));
}

/* Santitize Output */

function sanitize_output($buffer){

    $search  = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s');
    $replace = array('>', '<', '\\1', '');
    $buffer  = ($buffer != "") ? preg_replace($search, $replace, $buffer) : "";
    return $buffer;
}

/* Use to remove whitespacs,Spaces and make string to lower case. Add '-' where Space. */

function Slug($string){
    $slug        = strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
    $slug_exists = slug_exist($slug);

    if ($slug_exists) {
        $i        = 1;
        $baseSlug = $slug;
        while (slug_exist($slug)) {
            $slug = $baseSlug . "-" . $i++;
        }
    }

    return $slug;
}

function slug_exist($slug){
    global $db;
    $sql          = "SELECT page_slug FROM tbl_content WHERE page_slug = '" . $slug . "' ";
    $content_page = $db->pdoQuery($sql)->result();

    if ($content_page) {
        return true;
    }
}

/* Comment Remaining */

function requiredLoginId(){
    global $sessuser_type, $sesspUserId, $memberId;
    if (isset($sessuser_type) && $sessuser_type == 's') {
        return $sesspUserId;
    } else {
        return $memberId;
    }

}

function closetags($html){
    #put all opened tags into an array
    preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);

    $openedtags = $result[1]; #put all closed tags into an array
    preg_match_all('#</([a-z]+)>#iU', $html, $result);
    $closedtags = $result[1];
    $len_opened = count($openedtags);
    # all tags are closed
    if (count($closedtags) == $len_opened) {
        return $html;
    }
    $openedtags = array_reverse($openedtags);
    # close tags
    for ($i = 0; $i < $len_opened; $i++) {

        if (!in_array($openedtags[$i], $closedtags)) {

            $html .= '</' . $openedtags[$i] . '>';
        } else {

            unset($closedtags[array_search($openedtags[$i], $closedtags)]);
        }
    }
    return $html;
}

/* Get IP Address of current system. */
function get_ip_address(){
    foreach (array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
    ) as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }
}
/* Get Domain name from url */

function GetDomainName($url){
    $now1   = ereg_replace('www\.', '', $url);
    $now2   = ereg_replace('\.com', '', $now1);
    $domain = parse_url($now2);
    if (!empty($domain["host"])) {
        return $domain["host"];
    } else {
        return $domain["path"];
    }
}

/* Generate Random String as type alpha,nume,alphanumeric,hexidec */

function genrateRandom($length = 8, $seeds = 'alphanum'){
    // Possible seeds
    $seedings['alpha']    = 'abcdefghijklmnopqrstuvwqyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $seedings['numeric']  = '0123456789';
    $seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $seedings['hexidec']  = '0123456789abcdef';
    // Choose seed
    if (isset($seedings[$seeds])) {
        $seeds = $seedings[$seeds];
    }
    // Seed generator
    list($usec, $sec) = explode(' ', microtime());
    $seed             = (float) $sec + ((float) $usec * 100000);
    mt_srand($seed);
    // Generate
    $str         = '';
    $seeds_count = strlen($seeds);
    for ($i = 0; $length > $i; $i++) {
        $str .= $seeds[mt_rand(0, $seeds_count - 1)];
    }
    return $str;
}
/* Generate Random String */

function generateRandString($totalString = 10, $type = 'alphanum'){
    if ($type == 'alphanum') {
        $alphanum = "AaBbC0cDdEe1FfGgH2hIiJj3KkLlM4mNnOo5PpQqR6rSsTt7UuVvW8wXxYy9Zz";
    } else if ($type == 'num') {
        $alphanum = "098765432101234567890098765432101234567890098765432101234567890";
    }

    return substr(str_shuffle($alphanum), 0, $totalString);
}

/* Sub admin Check Permission */

function checkPermission($user_type, $pagenm, $permission){
    if ($user_type == 'a') {
        $flag      = 0;
        $sadm_page = array('subadmin');
        if (in_array($pagenm, $sadm_page)) {
            $flag = 1;
        } else {
            $getval = getValFromTbl('id', 'adminrole', 'id IN (' . $permission . ') AND pagenm="' . $pagenm . '"');
            if ($getval == 0) {
                $flag = 1;
            }

        }
        if ($flag == 1) {

            $_SESSION['notice'] = NOTPER;
            redirectPage(SITE_URL . get_language_url() . 'admin/dashboard');
            exit;
        }
    }
}

/* Load Css Set directory and give filenname as array */

function load_css($filename = array()){
    $returnStyle = '';
    $filePath    = array();
    if (!empty($filename)) {
        if (domain_details('dir') == 'admin-nct') {
            foreach ($filename as $k => $v) {
                if (is_array($v)) {
                    if (isset($v[1]) && $v[1] != "") {
                        $filePath[] = $v[1] . $v[0];
                    } else {
                        $filePath[] = SITE_ADM_CSS . $v[0];
                    }
                } else {
                    $filePath[] = SITE_ADM_CSS . $v;
                }
            }
        } else {
            foreach ($filename as $k => $v) {
                if (is_array($v)) {
                    if (isset($v[1]) && $v[1] != "") {
                        $filePath[] = $v[1] . $v[0];
                    } else {
                        $filePath[] = SITE_CSS . $v[0];
                    }
                } else {
                    $filePath[] = SITE_CSS . $v;
                }
            }
        }
    }
    foreach ($filePath as $style) {
        $returnStyle .= '<link rel="stylesheet" type="text/css" href="' . $style . '">';
    }
    return $returnStyle;
}

/* Load JS Set directory and give filenname as array */

function load_js($filename = array()){
    $returnStyle = '';
    $filePath    = array();
    if (!empty($filename)) {
        if (domain_details('dir') == 'admin-nct') {
            foreach ($filename as $k => $v) {
                if (is_array($v)) {
                    if (isset($v[1]) && $v[1] != "") {
                        $filePath[] = $v[1] . $v[0];
                    } else {
                        $filePath[] = SITE_ADM_JS . $v[0];
                    }
                } else {
                    $filePath[] = SITE_ADM_JS . $v;
                }
            }
        } else {
            foreach ($filename as $k => $v) {
                if (is_array($v)) {
                    if (isset($v[1]) && $v[1] != "") {
                        $filePath[] = $v[1] . $v[0];
                    } else {
                        $filePath[] = SITE_JS . $v[0];
                    }
                } else {
                    $filePath[] = SITE_JS . $v;
                }
            }
        }
    }
    foreach ($filePath as $scripts) {
        $returnStyle .= '<script type="text/javascript" src="' . $scripts . '"></script>';
    }
    return $returnStyle;
}

/* Diplay message function */

function disMessage($msgArray, $script = true){

    $message = '';
    $content = '';
    $type           = isset($msgArray["type"]) ? $msgArray["type"] : null;
    $var            = isset($msgArray["var"]) ? $msgArray["var"] : NULL;

    if (!is_null($var)) {
        switch ($var) {
            case 'loginRequired' : {
                $message = "Please login to continue";
                break;
            }
            case 'invaildUsers' : {
                $message = "Invalid username/email or password";
                break;
            }
            case 'NRF' : {
                $message = "No result found";
                break;
            }
            case 'alreadytaken': {
                $message = "Username or Email is already taken";
                break;
            }
            case 'fillAllvalues' : {
                $message = "Please fill in all the attributes to complete form";
                break;
            }
            case 'InvalidEmail' : {
                $message = "Please enter proper email address or multiple email with colon separator";
                break;
            }
            case 'EnterEmail' : {
                $message = "Enter email address";
                break;
            }
            case 'succActivateAccount' : {
                $message = "You have successfully verified your email. Please wait for administrator approval";
                break;
            }
            case 'inactivatedUser' : {
                $message = "You haven't verified your email yet, please check your inbox";
                break;
            }
            case 'unapprovedUser' : {
                $message = "Your account has not been approved, please contact support for more details";
                break;
            }
            case 'succChangePass' : {
                $message = "You have successfully changed your password. Please login again to continue";
                break;
            }
            case 'succLogin' : {
                $message = "You have successfully login to your account";
                break;
            }
            case 'incorectActivate' : {
                $message = "Incorrect account, Problem to activate your account";
                break;
            }

                ## global admin
            case 'userExist' : {
                $message = "Username already exists";
                break;
            }
            case 'emailExist' : {
                $message = "Email address already exists";
                break;
            }
            case 'sucNewslater' : {
                $message = "Your have successfully subscribed to our newsletter";
                break;
            }
            case 'sucNewslater2' : {
                $message = "Your have successfully activated your subscription";
                break;
            }
            case 'userNameExist' : {
                $message = "Username already exists";
                break;
            }
            case 'succLogout' : {
                $message = "You have sucessfully logged out from the site";
                break;
            }
            case 'succregwithoutact' : {
                $message = "You have successfully registered, please check your email";
                break;
            }

            case 'recAdded' : {
                $message = "Record has been added successfully";
                break;
            }
            case 'recEdited' : {
                $message = "Record has been edited successfully";
                break;
            }
            case 'recActivated' : {
                $message = "Record has been activated successfully";
                break;
            }
            case 'recDeActivated' : {
                $message = "Record has been inactivated successfully";
                break;
            }
            case 'recDeleted' : {
                $message = "Record has been deleted successfully";
                break;
            }
            case 'recExist' : {
                $message = "Record already exist. Please check carefully.";
                break;
            }

            case 'wrongPass' : {
                $message = "You have entered an incorrect password";
                break;
            }
            case 'passNotmatch' : {
                $message = "New password entry and password confirmation doesn't match";
                break;
            }
            case 'NoPermission' : {
                $message = "You don't have permission to access this module";
                break;
            }
            case 'delUser':{
                $message = MSG_DELETD_ADMIN;
                break;
            }
            case 'recImported' : {
                $message = "Record has been imported successfully";
                break;
            }
            case 'succForgotPass' : {
                $message = "Your password reminder requested has been accepted, please check your email";
                break;
            }
            case 'invalidCaptcha' : {
                $message = "Please enter valid captcha";
                break;
            }
            case 'BlockedUser' : {
                $message = "Your account has been blocked. Please contact support";
                break;
            }
            case 'RemainEmailVerify' : {
                $message = "Your email verification is not complete. You can login after completing this process";
                break;
            }
            case 'wrongemail' : {
                $message = "You have entered the wrong email address";
                break;
            }
            case 'incorectReset' : {
                $message = "Incorrect account, Problem to reset your account";
                break;
            }
            default : {
                $message = $var;
                break;
            }
        }
    }


    $type1 = ($type == 'suc' ? 'success' : ($type == 'inf' ? 'info' : ($type == 'war' ? 'warning' : 'error')));
    if ($script) {
        $content = '<script type="text/javascript"> toastr["' . $type1 . '"]("' . $message . '");</script>';
    } else {
        $content = 'toastr["' . $type1 . '"]("' . $message . '");';
    }

    return $content;
}

/* Check Authentication */

function Authentication($reqAuth = false, $redirect = true, $alloweduser_type = 'a'){
    $todays_date = date("Y-m-d");
    global $adminUserId, $sessUserId, $db;

    $whichSide = domain_details('dir');
    if ($reqAuth == true) {
        if ($whichSide == 'admin-nct') {

            if ($adminUserId == 0) {
                $_SESSION["toastr_message"] = disMessage(array('type' => 'err', 'var' => 'Please login to continue.'));
                $_SESSION['req_uri_adm']    = $_SERVER['REQUEST_URI'];

                if ($redirect) {
                    redirectPage(SITE_ADMIN_URL);
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {

            if ($sessUserId <= 0) {

                $_SESSION["toastr_message"] = disMessage(array('type' => 'err', 'var' => 'Please login to continue.'));
                $_SESSION['req_uri']        = $_SERVER['REQUEST_URI'];
                $_SESSION["userId"]         = 0;
                $_SESSION["first_name"]      = null;
                $_SESSION["last_name"]       = null;
                $_SESSION["user_type"]       = null;
                $_SESSION["userName"]       = null;

                if ($redirect) {
                    $msgType = $_SESSION["msgType"] = disMessage(array(
                        'type' => 'err',
                        'var'  => 'Please login to continue.',
                    ));
                    redirectPage(SITE_URL.'login/');
                } else {
                    return false;
                }
            }else{      
                return true;
            }
        }
    }
}

function getTableValue($table, $field, $wherecon = array()){
    global $db;
    $qrySel   = $db->select($table, array($field), $wherecon);
    $qrysel1  = $qrySel->result();
    $totalRow = $qrySel->affectedRows();
    $fetchRes = $qrysel1;

    if ($totalRow > 0) {
        return $fetchRes[$field];
    } else {
        return "";
    }
}

function getExt($file){
    $path_parts = pathinfo($file);
    $ext        = $path_parts['extension'];
    return $ext;
}

function GenerateThumbnail($varPhoto, $uploadDir, $tmp_name, $th_arr = array(), $file_nm = '', $addExt = true, $crop_coords = array()) {
    $ext = '.' . strtoupper(getExt($varPhoto));
    $tot_th = count($th_arr);

    if (($ext == ".JPG" || $ext == ".GIF" || $ext == ".PNG" || $ext == ".BMP" || $ext == ".JPEG" || $ext == ".ICO" || $ext == ".WEBP" || $ext == ".SVG")) {
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777);
        }

        if ($file_nm == ''){
            $imagename = rand() . time();
        } else{
            $imagename = $file_nm;
        }

        if ($addExt || $file_nm == ''){
            $imagename = $imagename . $ext;
        }

        $pathToImages = $uploadDir . $imagename;  
        $Photo_Source = copy($tmp_name, $pathToImages);

        if ($Photo_Source) {
            for ($i = 0; $i < $tot_th; $i++) {
                resizeImage($uploadDir . $imagename, $uploadDir . 'th' . ($i + 1) . '_' . $imagename, $th_arr[$i]['width'], $th_arr[$i]['height'], false, $crop_coords);
            }

            return $imagename;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function resizeImage($filename, $newfilename = "", $max_width='', $max_height = '', $withSampling = true, $crop_coords = array()) {
    if ($newfilename == "") {
        $newfilename = $filename;
    }

    // error_reporting(E_ALL);

    $fileExtension = strtolower(getExt($filename));
        // echo $fileExtension;exit;
    if ($fileExtension == "jpeg") {
        $img = imagecreatefromjpeg($filename);
    } else if ($fileExtension == "png") {
        $img = imagecreatefrompng($filename);
    } else if ($fileExtension == "gif") {
        $img = imagecreatefromgif($filename);
    } else if ($fileExtension == "webp") {
        $img = imagecreatefromwebp($filename);
    } else if ($fileExtension == "svg") {
        $imagick = new Imagick();
        $imagick->setBackgroundColor(new ImagickPixel('transparent'));
        $imagick->readImage($filename);
        // Convert SVG to PNG
        $imagick->setImageFormat("png");

        // Create a temporary image file
        $tempPng = tempnam(sys_get_temp_dir(), 'svg_to_png') . ".png";
        $imagick->writeImage($tempPng);

        // Load the PNG as a GD image
        $img = imagecreatefrompng($tempPng);

        // Delete the temporary file
        unlink($tempPng);

        $imagick->clear();
        $imagick->destroy();
    } else if ($fileExtension == "ico" || $fileExtension == "jpg") {
        $img = imagecreate($max_width, $max_height);
    } else {
        $img = imagecreatefromjpeg($filename);
    }



    $width = imageSX($img);
    $height = imageSY($img);

        // Build the thumbnail
    $target_width = $max_width;
    $target_height = $max_height;
    $target_ratio = $target_width / $target_height;
    $img_ratio = $width / $height;

    if (empty($crop_coords)) {

        if ($target_ratio > $img_ratio) {
            $new_height = $target_height;
            $new_width = $img_ratio * $target_height;
        } else {
            $new_height = $target_width / $img_ratio;
            $new_width = $target_width;
        }

        if ($new_height > $target_height) {
            $new_height = $target_height;
        }
        if ($new_width > $target_width) {
            $new_height = $target_width;
        }


        $new_img = imagecreatetruecolor($target_width, $target_height);
        imagealphablending($new_img, false);
        imagesavealpha($new_img,true);
        $transparent = imagecolorallocatealpha($new_img, 0, 0, 0, 127);
        imagefilledrectangle($new_img, 0, 0, $target_width, $target_height, $transparent);
        imagecopyresampled($new_img, $img, 0, 0, 0, 0, $target_width, $target_height, $width, $height);
    } else {
        $new_img = imagecreatetruecolor($target_width, $target_height);
        imagealphablending($new_img, false);
        imagesavealpha($new_img,true);
        $transparent = imagecolorallocatealpha($new_img, 255, 255, 255, 127);
        imagefilledrectangle($new_img, 0, 0, $target_width, $target_height, $transparent);
        imagecopyresampled($new_img, $img, 0, 0, 0, 0, $target_width, $target_height, $width, $height);
    }

    if ($fileExtension == "jpg" || $fileExtension == "jpeg") {
        $createImageSave = imagejpeg($new_img, $newfilename, 90);
    } else if ($fileExtension == 'png') {
        $createImageSave = imagepng($new_img, $newfilename, 9);
    } else if ($fileExtension == "gif") {
        $createImageSave = imagegif($new_img, $newfilename);
    } else if ($fileExtension == "webp") {
        $createImageSave = imagewebp($new_img, $newfilename, 90);
    } else if ($fileExtension == "ico") {
        $createImageSave = imagecreate($max_width,$max_height);
    } else {
        $createImageSave = imagejpeg($new_img, $newfilename, 90);
    }

    return true;
}

function resizeImageSocial($filename='',$newfilename='',$max_width='',$max_height='',$withSampling=''){
    if($newfilename=="")
        $newfilename=$filename;
    $image_info = getimagesize($filename);
    $image_ext = $image_info[2];
    list($width, $height) = getimagesize($filename);

    $newwidth =round($max_width);
    $newheight =round($max_height);

    if(  $image_ext == IMAGETYPE_JPEG ){
        $source = imagecreatefromjpeg($filename);
    } elseif(  $image_ext == IMAGETYPE_GIF ) {
        $source = imagecreatefromgif($filename);
    } elseif(  $image_ext  == IMAGETYPE_PNG ) {
        $source = imagecreatefrompng($filename);
    }
    $thumb = imagecreatetruecolor($newwidth, $newheight);
    if($withSampling){
        imagecopyresampled($thumb,  $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    }
    else{
        imagecopyresized($thumb,   $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    }
    $thumb = $thumb;
    if(  $image_ext  == IMAGETYPE_JPEG )
        return imagejpeg($thumb,$newfilename,75);
    if(  $image_ext  == IMAGETYPE_GIF )
        return imagegif($thumb,$newfilename);
    elseif(  $image_ext  == IMAGETYPE_PNG )
        return imagepng($thumb,$newfilename,9);
}

function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
    list($imagewidth, $imageheight, $imageType) = getimagesize($image);
    $imageType                                  = image_type_to_mime_type($imageType);

    $newImageWidth  = ceil($width * $scale);
    $newImageHeight = ceil($height * $scale);
    $newImage       = imagecreatetruecolor($newImageWidth, $newImageHeight);
    switch ($imageType) {
        case "image/gif":
        $source = imagecreatefromgif($image);
        break;
        case "image/pjpeg":
        case "image/jpeg":
        case "image/jpg":
        $source = imagecreatefromjpeg($image);
        break;
        case "image/png":
        case "image/x-png":
        $source = imagecreatefrompng($image);
        break;
    }
    imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
    switch ($imageType) {
        case "image/gif":
        imagegif($newImage, $thumb_image_name);
        break;
        case "image/pjpeg":
        case "image/jpeg":
        case "image/jpg":
        imagejpeg($newImage, $thumb_image_name, 100);
        break;
        case "image/png":
        case "image/x-png":
        imagepng($newImage, $thumb_image_name);
        break;
    }
    chmod($thumb_image_name, 0777);
    return $thumb_image_name;
}


/*Admin Functions*/


function curPageURL(){
    $pageURL = 'http';

    if (isset($_SERVER["HTTPS"])) {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }

    define('CURRENT_PAGE_URL', $pageURL);
}

function curPageName(){
    $pageName = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
    define('CURRENT_PAGE_NAME', $pageName);
}

function checkIfIsActive(){
    global $db;

    $userId = isset($_SESSION['userId']) && $_SESSION['userId'] > 0 ? $_SESSION['userId'] : '';

    if (isset($userId) && '' != $userId) {
        $user_details = $db->select("tbl_users", "*", array(
            "id" => $_SESSION['userId'],
        ))->result();
        if ($user_details) {
            if ('n' == $user_details['is_active']) {
                unset($_SESSION['userId']);
                unset($_SESSION['first_name']);
                unset($_SESSION['last_name']);

                $_SESSION['toastr_message'] = disMessage(array('type' => 'err', 'var' => "You have not verified the email address that is registered with us. Please try logging in again after verifying your email address."));
                redirectPage(SITE_URL);
                return false;
            } else if ('d' == $user_details['status']) {
                unset($_SESSION['userId']);
                unset($_SESSION['first_name']);
                unset($_SESSION['last_name']);

                $_SESSION['toastr_message'] = disMessage(array('type' => 'err', 'var' => "Your account has been deactivated by Admin. Please contact Site Admin to re-activate your account."));
                redirectPage(SITE_URL);
                return false;
            } else {
                return true;
            }
        } else {
            unset($_SESSION['userId']);
            unset($_SESSION['first_name']);
            unset($_SESSION['last_name']);

            $_SESSION['toastr_message'] = disMessage(array('type' => 'err', 'var' => "There seems to be an issue. Please try logging in again."));
            redirectPage(SITE_URL);
            return false;
        }
    } else {
        return true;
    }
}

/* get domain details, pass module, dir, file or file-module whichever required. */

function domain_details($returnWhat){
    $arrScriptName = explode('/', $_SERVER['SCRIPT_NAME']); 
    foreach ($arrScriptName as $singleSciptName) {

        if ($singleSciptName == "admin-nct") {
            return $singleSciptName;
            break;
        }
    }
}

/*new structure html function*/
function html($fileName, $flg = false){
    if (file_exists($fileName)) {
        if ($flg) {
            echo (new MainTemplater($fileName))->parse();
        } else {
            return (new MainTemplater($fileName))->parse();
        }
    } else {
        dump($fileName, "File Not Found");
    }
}
function html_r($fileName, $find = "", $replace = "", $flg = false){
    if (file_exists($fileName)) {
        if ($flg) {
            echo replace($find, $replace, (new MainTemplater($fileName))->parse(), false);
        } else {
            return replace($find, $replace, (new MainTemplater($fileName))->parse(), false);
        }
    } else {
        dump($fileName, "File Not Found");
    }
}
function html_t($fileName){
    if (file_exists($fileName)) {
        return new MainTemplater($fileName);
    } else {
        dump($fileName, "File Not Found");
    }
}
function replace($search, $replace, $html, $flg = true){
    if ($flg) {
        echo str_replace($search, $replace, $html);
    } else {
        return str_replace($search, $replace, $html);
    }
}

// Generates a strong password of N length containing at least one lower case letter,
// one uppercase letter, one digit, and one special character. The remaining characters
// in the password are chosen at random from those four sets.
//
// The available characters in each set are user friendly - there are no ambiguous
// characters such as i, l, 1, o, 0, etc. This, coupled with the $add_dashes option,
// makes it much easier for users to manually type or speak their passwords.
//
// Note: the $add_dashes option will increase the length of the password by
// floor(sqrt(N)) characters.
function generatePassword($length = 8, $add_dashes = false, $available_sets = 'luds'){
    $sets = array();
    if (strpos($available_sets, 'l') !== false) {
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
    }

    if (strpos($available_sets, 'u') !== false) {
        $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    }

    if (strpos($available_sets, 'd') !== false) {
        $sets[] = '23456789';
    }

    if (strpos($available_sets, 's') !== false) {
        $sets[] = '!@#$%&*?';
    }

    $all      = '';
    $password = '';
    foreach ($sets as $set) {
        $password .= $set[array_rand(str_split($set))];
        $all .= $set;
    }
    $all = str_split($all);
    for ($i = 0; $i < $length - count($sets); $i++) {
        $password .= $all[array_rand($all)];
    }

    $password = str_shuffle($password);
    if (!$add_dashes) {
        return $password;
    }

    $dash_len = floor(sqrt($length));
    $dash_str = '';
    while (strlen($password) > $dash_len) {
        $dash_str .= substr($password, 0, $dash_len) . '-';
        $password = substr($password, $dash_len);
    }
    $dash_str .= $password;
    return $dash_str;
}

function closePopup(){
    $content = '<script type="text/javascript">window.close();</script>';
    return $content;
}

function humanTiming($time){

    $time = time() - $time; // to get the time since that moment

    $tokens = array(
        31536000 => 'year',
        2592000  => 'month',
        604800   => 'week',
        86400    => 'day',
        3600     => 'hour',
        60       => 'minute',
        1        => 'second',
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) {
            continue;
        }

        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
    }
}

function getTime($date){
    $time = humanTiming(strtotime($date));
    if ($time == "") {
        $time = "Just Now";
    } else {
        $time .= " ago";
    }

    return $time;
}

function encryptIt($string)
{
    $secret = 'nct_security';
    $key = substr(hash('sha256', $secret, true), 0, 16);

    $encrypted = openssl_encrypt(
        $string,
        'aes-128-ecb',
        $key,
        OPENSSL_RAW_DATA
    );

    return bin2hex($encrypted);
}

function decryptIt($string)
{
    $secret = 'nct_security';
    $key = substr(hash('sha256', $secret, true), 0, 16);

    $data = hex2bin($string);

    $decrypted = openssl_decrypt(
        $data,
        'aes-128-ecb',
        $key,
        OPENSSL_RAW_DATA
    );

    return $decrypted;
}

/*Fun. added by krishna Marakana*/

function insert_default_time_slot($user_id = 0){
    global $db;

    $user_type = getTableValue('tbl_users','user_type',array('id' => $user_id));

    if ($user_type == 'doctor') {
        $days_array = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

        if (count($days_array) > 0) {
            foreach ($days_array as $key => $value) {

                if ($value == 'sunday') {
                    $insert_array = array(
                        'user_id'               => $user_id,
                        'day'                   => $value,
                        'is_available'          => 'n',
                        'created_date'          => date("Y-m-d H:i:s"),
                        'updated_date'          => date("Y-m-d H:i:s"),
                    );
                    $db->insert('tbl_users_time_slot',$insert_array);
                } else{

                    $insert_array = array(
                        'user_id'               => $user_id,
                        'day'                   => $value,
                        'is_available'          => 'y',
                        'created_date'          => date("Y-m-d H:i:s"),
                        'updated_date'          => date("Y-m-d H:i:s"),
                    );

                    $insert_array['from_time'] = '10:00 AM';
                    $insert_array['to_time'] = '01:00 PM';
                    $db->insert('tbl_users_time_slot',$insert_array);


                    $insert_array['from_time'] = '02:00 PM';
                    $insert_array['to_time'] = '06:00 PM';
                    $db->insert('tbl_users_time_slot',$insert_array);
                }
                
            }
        }
    }

    
}

function get_image_url($imageName, $type = 'banner_image', $thumbSize = "th1_",$is_need_deafult_image = 'y') {

    $image_url = '';
    if ($imageName != '' || $imageName != null) {
        if ($type == 'profile_photo') {
            if (file_exists(DIR_UPD_PROFILE_IMAGE . $thumbSize . $imageName)) {
                $image_url = SITE_UPD_PROFILE_IMAGE . $thumbSize . $imageName;
            } else if (file_exists(DIR_UPD_PROFILE_IMAGE. $imageName)) {
                $image_url = SITE_UPD_PROFILE_IMAGE .$imageName;
            } else {
                if ($is_need_deafult_image == 'y') {
                    $image_url = SITE_IMG . USER_DEFAULT_AVATAR;
                }
            }
        } else if ($type == 'type_of_doctors') {
            if (file_exists(DIR_UPD_TYPE_OF_DOCTORS . $thumbSize . $imageName)) {
                $image_url = SITE_UPD_TYPE_OF_DOCTORS . $thumbSize . $imageName;
            } else if (file_exists(DIR_UPD_TYPE_OF_DOCTORS. $imageName)) {
                $image_url = SITE_UPD_TYPE_OF_DOCTORS .$imageName;
            } else {
                if ($is_need_deafult_image == 'y') {
                    $image_url = SITE_IMG . USER_DEFAULT_AVATAR;
                }
            }
        } else{
            if ($is_need_deafult_image == 'y') {
                $image_url = SITE_IMG . "no_img.jpg";
            }
        }
    } else {
        if ($is_need_deafult_image == 'y') {
            $image_url = SITE_IMG . USER_DEFAULT_AVATAR;
        }
    }
    return $image_url;
}

function get_type_of_doctors_list($selected_id_array = []){
    global $db;

    $html = '';

    $res = $db->pdoQuery("SELECT * FROM tbl_type_of_doctors WHERE is_active = 'y' ")->results();

    if (isset($res) && count($res) > 0) {

        $main_content = new MainTemplater(DIR_TMPL . "select_option-nct.tpl.php");
        $main_content1 = $main_content->parse();

        foreach ($res as $key => $value) {

            $selected = '';
            if (in_array($value['id'],$selected_id_array)) {
                $selected = "selected='selected'";
            }

            $replace = array(
                '%VALUE%'                   => $value['id'],
                '%DISPLAY_VALUE%'           => $value['name'],
                '%SELECTED%'                => $selected,
            );
            $html .= str_replace(array_keys($replace),array_values($replace),$main_content1);
        }
    }

    return $html;
}

function get_specialties_list($selected_id_array = []){
    global $db;

    $html = '';

    $res = $db->pdoQuery("SELECT * FROM tbl_specialties WHERE is_active = 'y' ")->results();

    if (isset($res) && count($res) > 0) {

        $main_content = new MainTemplater(DIR_TMPL . "select_option-nct.tpl.php");
        $main_content1 = $main_content->parse();

        foreach ($res as $key => $value) {

            $selected = '';
            if (in_array($value['id'],$selected_id_array)) {
                $selected = "selected='selected'";
            }

            $replace = array(
                '%VALUE%'                   => $value['id'],
                '%DISPLAY_VALUE%'           => $value['name'],
                '%SELECTED%'                => $selected,
            );
            $html .= str_replace(array_keys($replace),array_values($replace),$main_content1);
        }
    }

    return $html;
}

function myTruncate($string, $limit, $break = " ", $pad = "...", $onlyText = true) {
    $string = ($onlyText == true) ? str_replace('&nbsp;', ' ', strip_tags($string)) : $string;
    // return with no change if string is shorter than $limit
    if (strlen($string) <= $limit) {
        return $string;
    }

    // is $break present between $limit and the end of the string?
    if (false !== ($breakpoint = strpos($string, $break, $limit))) {
        if ($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
        }
    }

    return $string;
}

function get_case_type($case_type){
    global $db, $sessuser_type;

    switch ($case_type) {
        case 'new':{
            $case_type_text = 'New';
            break;
        }
        case 'follow_up':{
            $case_type_text = 'Follow Up';
            break;
        }
        default:{
            $case_type_text = 'New';
            break;
        }
    }
    return $case_type_text;
}

function get_gender($gender){
    global $db, $sessuser_type;

    switch ($gender) {
        case 'male':{
            $case_type_text = 'Male';
            break;
        }
        case 'female':{
            $case_type_text = 'Female';
            break;
        }
        default:{
            $case_type_text = '';
            break;
        }
    }
    return $case_type_text;
}

function get_user_type($user_type){
    global $db, $sessuser_type;

    switch ($user_type) {
        case 'patient':{
            $case_type_text = 'Patient';
            break;
        }
        case 'doctor':{
            $case_type_text = 'Doctor';
            break;
        }
        case 'clinic':{
            $case_type_text = 'Clinic';
            break;
        }
        default:{
            $case_type_text = '';
            break;
        }
    }
    return $case_type_text;
}

function registerDevice($deviceId, $deviceType, $deviceToken = '', $userId = null){
    $device = getDevice($deviceId,false,$userId);

    if ($device !== false) {
        $updateDeviceArray  = array(
            'device_type'   => $deviceType,
            'device_token'  => $deviceToken,
            'updated_at'    => date('Y-m-d H:i:s')
        );
        if ($userId) {
            $updateDeviceArray['user_id'] = (int)$userId;
        }
        return updateDevice($updateDeviceArray, $device['id']);
    } else {
        $addDeviceArray     = array(
            'device_id'     => $deviceId,
            'device_type'   => $deviceType,
            'device_token'  => $deviceToken,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        );
        if ($userId) {
            $addDeviceArray['user_id'] = (int)$userId;
        }
        return addDevice($addDeviceArray);
    }
}

function addDevice($data){
    global $db;
    $stmnt  =  $db->insert('tbl_logged_devices', $data);
    return $stmnt->getLastInsertId();
}

function getDevice($deviceId, $includeDeleted = false,$user_id = 0){
    global $db;
    $where      = ['device_id' => $deviceId,'user_id' => $user_id];
    $extraWhere = '';

    if ($includeDeleted === false) {
        $extraWhere = ' AND deleted_at IS NULL';
    }

    $stmnt  = $db->select('tbl_logged_devices', '*', $where, $extraWhere);
    return $stmnt->result();
}

function updateDevice($data, $deviceId){
    global $db;
    $stmnt = $db->update('tbl_logged_devices', $data, ['id' => $deviceId]);
    return (bool)$stmnt->affectedRows();
}

function deleteDevice($deviceId='',$user_id = 0){
    global $db;

    $stmnt = $db->delete('tbl_logged_devices', ['device_id' => $deviceId,'user_id' => $user_id]);
    return (bool)$stmnt->affectedRows();
}

function ws_response_data($response = array()){

    $status                     = isset($response['status']) ? $response['status'] : false;
    $message                    = isset($response['message']) ? $response['message'] : MSG_SMTHG_WRNG;
    $data                       = isset($response['data']) && count($response['data']) > 0 ? $response['data'] : array();
    $statusCode                 = isset($response['statusCode']) ? $response['statusCode'] : 400;
    $action                     = isset($response['action']) ? trim($response['action']) : '';

    if ($statusCode == 402) {
        $statusCode = $statusCode;
    } else if (count($data) > 0 || $status == true) {
        $statusCode = 200;
    } 


    if ($status == false) {
        if ($action == 'search_data' || $action == 'get_booked_appointment_list' || $action == 'get_time_slots_info') {
            $data = array();
        } else{
            $data = (object) array();
        }
    }

    // $is_free_plan = isset($response['is_free_plan']) ? $response['is_free_plan'] : 'n';

    // if ($status) {
    //     if ($action == 'createToken' && $is_free_plan == 'y') {
    //         $data = (object) array();
    //     }
    // }

    $resData = array();
    $resData['status']          = $status;
    $resData['message']         = $message;
    $resData['data']            = $data;

    if (isset($response['pagination'])) {
        $pagination     = isset($response['pagination']) ? $response['pagination'] : (object) array();

        if (is_array($pagination) && count($pagination) > 0) {
            $pagination['current_page']     = isset($pagination['current_page']) ? (int) $pagination['current_page'] : 0;
            $pagination['total_pages']      = isset($pagination['total_pages']) ? (int) $pagination['total_pages'] : 0;
            $pagination['total']            = isset($pagination['total']) ? (int) $pagination['total'] : 0;
        }


        $resData['pagination']     = $pagination;
    }

    $resData['statusCode']      = $statusCode;

    return $resData;
}

function get_user_profile_url($id = 0,$usertype = ''){ 
    global $db;
    return SITE_URL.'profile/'.base64_encode($id);
}

function getLoginResponseForAPP($user_id = 0){
    global $db;

    $userDetails = $db->pdoQuery("SELECT * FROM tbl_users WHERE id = ".(int) $user_id." ")->result();

    $profile_photo      = isset($userDetails['profile_photo']) ? $userDetails['profile_photo'] : '';
    $user_type          = isset($userDetails['user_type']) ? $userDetails['user_type'] : '';
    $is_deactivated     = isset($userDetails['is_deactivated']) ? $userDetails['is_deactivated'] : 'n';
    $gender             = isset($userDetails['gender']) && $userDetails['gender'] != 'n' ? $userDetails['gender'] : '';

    $replace = array(
        'user_id'                           => (string) $user_id,
        'profile_img'                       => get_image_url($profile_photo,"profile_photo",'th2_'),
        'profile_url'                       => get_user_profile_url($user_id),
        'user_type'                         => $user_type,
        'is_deactivated'                      => $is_deactivated,
        'first_name'                        => isset($userDetails['first_name']) ? $userDetails['first_name'] : '',
        'last_name'                         => isset($userDetails['last_name']) ? $userDetails['last_name'] : '',
        'email'                             => isset($userDetails['email']) ? $userDetails['email'] : '',
        'phone_no'                          => isset($userDetails['phone_no']) ? $userDetails['phone_no'] : '',
        'phone_country_code'                => isset($userDetails['phone_country_code']) ? $userDetails['phone_country_code'] : '',
        'date_of_birth'                     => isset($userDetails['date_of_birth']) ? $userDetails['date_of_birth'] : '',
        'created_date'                      => isset($userDetails['created_date']) && $userDetails['created_date'] != '' ? $userDetails['created_date'] : '',

        'clinic_name'                       => isset($userDetails['clinic_name']) && $userDetails['clinic_name'] != '' ? $userDetails['clinic_name'] : '',
        'gender'                            => $gender,
        'address'                           => isset($userDetails['address']) ? $userDetails['address'] : '',
        'latitude'                          => isset($userDetails['latitude']) ? $userDetails['latitude'] : '',
        'longitude'                         => isset($userDetails['longitude']) ? $userDetails['longitude'] : '',
        'emergency_ambulance_contact_no'    => emergency_ambulance_contact_no,
        'referral_or_community_code'        => isset($userDetails['referral_or_community_code']) ? $userDetails['referral_or_community_code'] : '',
    );
    return $replace;
}

function getProfileData($user_id = 0,$platform = 'web'){
    global $db;

    $userDetails = $db->pdoQuery("SELECT * FROM tbl_users WHERE id = ".(int) $user_id." ")->result();

    $profile_photo      = isset($userDetails['profile_photo']) ? $userDetails['profile_photo'] : '';
    $user_type          = isset($userDetails['user_type']) ? $userDetails['user_type'] : '';
    $is_deactivated       = isset($userDetails['is_deactivated']) ? $userDetails['is_deactivated'] : 'n';

    $replace = array(
        'user_id'                   => (string) $user_id,
        'profile_img'               => get_image_url($profile_photo,"profile_photo",'th2_'),
        'profile_url'               => get_user_profile_url($user_id),
        'user_type'                 => $user_type,
        'is_deactivated'              => $is_deactivated,
        'first_name'                => isset($userDetails['first_name']) ? $userDetails['first_name'] : '',
        'last_name'                 => isset($userDetails['last_name']) ? $userDetails['last_name'] : '',
        'email'                     => isset($userDetails['email']) ? $userDetails['email'] : '',
        'phone_no'                  => isset($userDetails['phone_no']) ? $userDetails['phone_no'] : '',
        'phone_country_code'        => isset($userDetails['phone_country_code']) ? $userDetails['phone_country_code'] : '',
        'date_of_birth'             => isset($userDetails['date_of_birth']) ? $userDetails['date_of_birth'] : '',
        'address'                   => isset($userDetails['address']) ? $userDetails['address'] : '',
        'latitude'                  => isset($userDetails['latitude']) ? $userDetails['latitude'] : '',
        'longitude'                 => isset($userDetails['longitude']) ? $userDetails['longitude'] : '',
        'city_name'                 => isset($userDetails['city_name']) ? $userDetails['city_name'] : '',
        'state_name'                => isset($userDetails['state_name']) ? $userDetails['state_name'] : '',
        'country_name'              => isset($userDetails['country_name']) ? $userDetails['country_name'] : '',
        'zip_code'                  => isset($userDetails['zip_code']) ? $userDetails['zip_code'] : '',
        'gender'                    => isset($userDetails['gender']) ? $userDetails['gender'] : '',
        'created_date'              => isset($userDetails['created_date']) && $userDetails['created_date'] != '' ? $userDetails['created_date'] : '',
        'clinic_name'               => isset($userDetails['clinic_name']) && $userDetails['clinic_name'] != '' ? $userDetails['clinic_name'] : '',
        'practicing_since'          => isset($userDetails['practicing_since']) ? $userDetails['practicing_since'] : '',
        'consultation_fees'         => isset($userDetails['consultation_fees']) ? $userDetails['consultation_fees'] : '',
        'type_of_doctors'           => $user_type != 'patient' ? get_type_of_doctorss_string($user_id) : '',
        'specialties'               => $user_type != 'patient' ? get_specialties_string($user_id) : '',
        'available_doctors'         => $user_type == 'clinic' ? getAvailableDoctorsInClinic($user_id) : '',
        'slot_info'                 => $user_type == 'doctor' ? get_time_slots($platform,$user_id) : [],
    );
    return $replace;
}

function getAvailableDoctorsInClinic($clinic_id = 0){
    global $db;

    $array = [];

    $userDetails = $db->pdoQuery("SELECT * FROM tbl_users WHERE parent_id = ".(int) $clinic_id." AND status = 'a' AND email_verified = 'y' AND is_deactivated = 'n' ")->results();

    if (count($userDetails) > 0) {
        foreach ($userDetails as $key => $value) {
            $profile_photo      = isset($value['profile_photo']) ? $value['profile_photo'] : '';
            $user_type          = isset($value['user_type']) ? $value['user_type'] : '';
            $is_deactivated       = isset($value['is_deactivated']) ? $value['is_deactivated'] : 'n';

            $array[] = array(
                'parent_id'                 => (string) $clinic_id,
                'user_id'                   => (string) $value['id'],
                'profile_img'               => get_image_url($profile_photo,"profile_photo",'th2_'),
                'profile_url'               => get_user_profile_url($clinic_id),
                'first_name'                => isset($value['first_name']) ? $value['first_name'] : '',
                'last_name'                 => isset($value['last_name']) ? $value['last_name'] : '',
                'address'                   => isset($value['address']) ? $value['address'] : '',
                'practicing_since'          => isset($value['practicing_since']) ? $value['practicing_since'] : '',
                'consultation_fees'         => isset($value['consultation_fees']) ? $value['consultation_fees'] : '',
                'type_of_doctors'           => get_type_of_doctorss_string($value['id']),
                'specialties'               => get_specialties_string($value['id']),
            );
        }
    }
    
    return $array;
}

function get_specialties_string($user_id = ''){
    global $db;

    $specialties = $db->pdoQuery("
        SELECT ud.specialties_id,d.name
        FROM tbl_users_specialties AS ud
        LEFT JOIN tbl_specialties AS d ON d.id = ud.specialties_id
        WHERE ud.user_id = ".$user_id." ORDER BY ud.id ASC ")->results();

    $specialties_str = '';
    if (count($specialties) > 0) {
        foreach ($specialties as $key => $value) {
            $specialties_str.= ' '.$value['name'].',';
        }
        $specialties_str = substr(trim($specialties_str), 0, -1);
    }

    return $specialties_str;
}

function get_type_of_doctorss_string($user_id = ''){
    global $db;

    $type_of_doctors = $db->pdoQuery("
        SELECT ud.type_of_doctor_id,d.name
        FROM tbl_users_doctor_type AS ud
        LEFT JOIN tbl_type_of_doctors AS d ON d.id = ud.type_of_doctor_id
        WHERE ud.user_id = ".$user_id." ORDER BY ud.id ASC ")->results();

    $type_of_doctors_str = '';
    if (count($type_of_doctors) > 0) {
        foreach ($type_of_doctors as $key => $value) {
            $type_of_doctors_str.= ' '.$value['name'].',';
        }
        $type_of_doctors_str = substr(trim($type_of_doctors_str), 0, -1);
    }

    return $type_of_doctors_str;
}

function get_clinics_list($selected_id = 0,$platform = 'web'){
    global $db;
    $html = '';
    $app_array = [];

    $userDetails = $db->pdoQuery("
        SELECT 
        c.*,
        COUNT(d.id) AS total_doctors
        FROM tbl_users AS c
        INNER JOIN tbl_users AS d ON d.parent_id = c.id 
        WHERE c.user_type = 'clinic' AND c.clinic_name != '' AND c.status = 'a' AND c.email_verified = 'y' AND c.is_deactivated = 'n' AND d.status = 'a' AND d.is_deactivated = 'n' AND d.email_verified = 'y' AND d.user_type = 'doctor'
        GROUP BY c.id
        HAVING total_doctors > 0
        ")->results();

    if (count($userDetails) > 0) {

        if ($platform == 'web') {
            $main_content = new MainTemplater(DIR_TMPL . "select_option-nct.tpl.php");
            $main_content1 = $main_content->parse();
        }

        foreach ($userDetails as $key => $value) {

            $image = get_image_url($value['profile_photo'],"profile_photo",'th2_');
            if ($platform == 'app') {
                $app_array[] = array(
                    'id'                    => (string) $value['id'],
                    'title'                 => $value['clinic_name'],
                    'image'                 => $image,
                    'is_selected'           => $selected_id == $value['id'] ? 'y' : 'n',
                );
            } else{
                $replace = array(
                    '%VALUE%'                   => $value['id'],
                    '%DISPLAY_VALUE%'           => $value['clinic_name'],
                    '%SELECTED%'                => $selected_id == $value['id'] ? 'selected' : '',
                );
                $html .= str_replace(array_keys($replace),array_values($replace),$main_content1);
            }
        }
    }

    if ($platform == 'app') {
        return $app_array;
    } else{
        return $html;
    }
}

function get_doctors_by_clinic($clinic_id = 0,$selected_id = 0,$platform = 'web'){
    global $db;
    $html = '';
    $app_array = [];

    $userDetails = $db->pdoQuery("SELECT *,CONCAT(first_name,' ',last_name) AS user_name FROM tbl_users WHERE user_type = 'doctor' AND parent_id = ".(int) $clinic_id." AND first_name != '' AND status = 'a' AND email_verified = 'y' AND is_deactivated = 'n' ")->results();

    if (count($userDetails) > 0) {

        if ($platform == 'web') {
            $main_content = new MainTemplater(DIR_TMPL . "select_option-nct.tpl.php");
            $main_content1 = $main_content->parse();
        }

        foreach ($userDetails as $key => $value) {

            $image = get_image_url($value['profile_photo'],"profile_photo",'th2_');
            if ($platform == 'app') {
                $app_array[] = array(
                    'id'                    => (string) $value['id'],
                    'title'                 => 'Dr. '.$value['user_name'],
                    'image'                 => $image,
                    'is_selected'           => $selected_id == $value['id'] ? 'y' : 'n',
                );
            } else{
                $replace = array(
                    '%VALUE%'                   => $value['id'],
                    '%DISPLAY_VALUE%'           => 'Dr. '.$value['user_name'],
                    '%SELECTED%'                => $selected_id == $value['id'] ? 'selected' : '',
                );
                $html .= str_replace(array_keys($replace),array_values($replace),$main_content1);
            }
        }
    }

    if ($platform == 'app') {
        return $app_array;
    } else{
        return $html;
    }
}

function generateSlots($from, $to, $interval = 60) {
    $slots = [];

    $start = strtotime($from);
    $end   = strtotime($to);

    while ($start + ($interval * 60) <= $end) {
        $slots[] = date('h:i A', $start) . ' to ' .
        date('h:i A', $start + ($interval * 60));
        $start += ($interval * 60);
    }

    return $slots;
}

function get_time_slots($platform = 'web',$user_id = 0,$booking_date = ''){
    global $db;
    $html = '';
    $app_array = [];

    if (!empty($booking_date)) {
        // single date mode
        $date = new DateTime($booking_date);

        $days[] = [
            'date_label' => $date->format('M d, l'),
            'day'        => strtolower($date->format('l')),
            'date'       => $date->format('Y-m-d')
        ];
    } else {
        /* Step 1: next 7 days */
        $today = new DateTime();

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date = clone $today;
            $date->modify("+$i day");

            $days[] = [
                'date_label' => $date->format('M d, l'),
                'day'        => strtolower($date->format('l')),
                'date'       => $date->format('Y-m-d')
            ];
        }
    }

    $availability = [];
    /* Step 2: fetch availability */

    $today = date('Y-m-d');
    $currentTime = date('H:i:s');

    if (count($days) > 0) {
        foreach ($days as $key => $value) {

            $loop_date = $value['date'];

            $timeCondition = "";
            if ($loop_date == $today) {
                // current date → past slots remove
                $timeCondition = " AND STR_TO_DATE(to_time, '%h:%i %p') > CURTIME() ";
            }

            $slotInfo = $db->pdoQuery("
                SELECT id, day, from_time, to_time, is_available
                FROM tbl_users_time_slot
                WHERE user_id = ".(int)$user_id."
                AND is_available = 'y'
                AND LOWER(day) = '".strtolower($value['day'])."'
                $timeCondition
                ")->results();

            // $slotInfo = $db->pdoQuery("SELECT id,day,from_time,to_time,is_available FROM tbl_users_time_slot WHERE user_id = ".(int) $user_id." AND is_available = 'y' AND LOWER(day) = '".strtolower($value['day'])."' ")->results();

            $slot = [];
            if (!empty($slotInfo)) {
                foreach ($slotInfo as $r) {
                    $slot[] = [
                        'id'   => $r['id'],
                        'time' => $r['from_time'].' - '.$r['to_time']
                    ];
                }
            }

            if (count($slot) > 0) {
                $availability[] = array(
                    'date'              => $value['date_label'],
                    'format_date'       => $value['date'],
                    'is_available'      => count($slotInfo) <= 0 ? 'n' : 'y',
                    'slot'              => $slot,
                );
            }
        }
    }
    return $availability;
}


function get_near_by_doctors_and_clinics($user_type = '',$platform = 'web',$check_for_today = 'n'){
    global $db;

    $latitude           = isset($_REQUEST['latitude']) ? $_REQUEST['latitude'] : '';
    $longitude          = isset($_REQUEST['longitude']) ? $_REQUEST['longitude'] : '';
    $limit              = isset($_REQUEST['per_page_limit']) && $_REQUEST['per_page_limit'] > 0 ? $_REQUEST['per_page_limit'] : LIMIT;
    $currentPage        = isset($_REQUEST['page_no']) && $_REQUEST['page_no'] > 0 ? $_REQUEST['page_no'] : 1;

    $offset = ($currentPage - 1 ) * $limit;

    $nearByDoctorsClinics = [];
    if ($latitude != '' && $longitude != '' && $user_type != '') {

        $where = '';
        if ($user_type == 'doctor') {
            // $where = " AND parent_id <= 0 AND (u.parent_id = 0 OR u.parent_id IS NULL) ";
        }
        
        if ($check_for_today == 'y' && $user_type == 'doctor') {
            $query = "
            SELECT 
            DISTINCT u.*, ROUND( ST_Distance_Sphere( POINT(u.longitude, u.latitude), POINT($longitude, $latitude) ) / 1000, 2 ) AS distance_km 
            FROM tbl_users AS u 
            INNER JOIN tbl_users_time_slot AS ts ON ts.user_id = u.id
            WHERE ts.is_available = 'y' AND ts.day = LOWER(DAYNAME(CURDATE())) AND STR_TO_DATE(ts.to_time, '%h:%i %p') >= CURTIME() AND u.status = 'a' AND u.email_verified = 'y' AND u.is_deactivated = 'n' AND u.user_type = '".$user_type."' ".$where."  AND u.latitude IS NOT NULL AND u.longitude IS NOT NULL HAVING distance_km <= ".PRO_DELIVERY_RADIUOS." ORDER BY distance_km ASC";
        } else{
            $query = "
            SELECT u.*,ROUND(
                ST_Distance_Sphere(
                    POINT(u.longitude, u.latitude),
                    POINT($longitude, $latitude)
                    ) / 1000,
                2
                ) AS distance_km
            FROM tbl_users as u 
            WHERE status = 'a' AND email_verified = 'y' AND is_deactivated = 'n' AND user_type = '".$user_type."' ".$where." GROUP BY u.id HAVING distance_km <= ".PRO_DELIVERY_RADIUOS." ORDER BY distance_km ASC ";
        }

        $limit_query = " LIMIT " . $limit . " OFFSET " . $offset;
        $totalRows = $db->pdoQuery($query)->affectedRows();
        $getShowableResults = $db->pdoQuery($query . $limit_query)->results();

        if (count($getShowableResults) > 0) {
            $showableRows = count($getShowableResults);

            foreach ($getShowableResults as $key => $value) {
                $profile_photo  = isset($value['profile_photo']) ? $value['profile_photo'] : '';
                $user_type      = isset($value['user_type']) ? $value['user_type'] : '';

                $nearByDoctorsClinics[] = array(
                    'id'                        => $value['id'],
                    'profile_img'               => get_image_url($profile_photo,"profile_photo",'th2_'),
                    'profile_url'               => get_user_profile_url($value['id']),
                    'user_type'                 => $user_type,
                    'is_deactivated'              => $value['is_deactivated'],
                    'first_name'                => isset($value['first_name']) ? $value['first_name'] : '',
                    'last_name'                 => isset($value['last_name']) ? $value['last_name'] : '',
                    'email'                     => isset($value['email']) ? $value['email'] : '',
                    'phone_no'                  => isset($value['phone_no']) ? $value['phone_no'] : '',
                    'phone_country_code'        => isset($value['phone_country_code']) ? $value['phone_country_code'] : '',
                    'date_of_birth'             => isset($value['date_of_birth']) ? $value['date_of_birth'] : '',
                    'address'                   => isset($value['address']) ? $value['address'] : '',
                    'latitude'                  => isset($value['latitude']) ? $value['latitude'] : '',
                    'longitude'                 => isset($value['longitude']) ? $value['longitude'] : '',
                    'city_name'                 => isset($value['city_name']) ? $value['city_name'] : '',
                    'state_name'                => isset($value['state_name']) ? $value['state_name'] : '',
                    'country_name'              => isset($value['country_name']) ? $value['country_name'] : '',
                    'zip_code'                  => isset($value['zip_code']) ? $value['zip_code'] : '',
                    'gender'                    => isset($value['gender']) ? $value['gender'] : '',
                    'created_date'              => isset($value['created_date']) && $value['created_date'] != '' ? $value['created_date'] : '',
                    'clinic_name'               => isset($value['clinic_name']) && $value['clinic_name'] != '' ? $value['clinic_name'] : '',
                    'practicing_since'          => isset($value['practicing_since']) ? $value['practicing_since'] : '',
                    'consultation_fees'         => isset($value['consultation_fees']) ? $value['consultation_fees'] : '',
                    'type_of_doctors'           => $user_type != 'patient' ? get_type_of_doctorss_string($value['id']) : '',
                    'specialties'               => $user_type != 'patient' ? get_specialties_string($value['id']) : '',
                    'available_doctors'         => $user_type == 'clinic' ? getAvailableDoctorsInClinic($value['id']) : '',
                    'slot_info'                 => $user_type == 'doctor' ? get_time_slots($platform,$value['id']) : [],
                );
            }
        }

        $page_data = getPagerData($totalRows, $limit,$currentPage);
        $pagination = array('current_page'=>$currentPage,'total_pages'=>$page_data->numPages,'total'=>$totalRows);
    }

    $response = array(
        'data'          => count($nearByDoctorsClinics) > 0 ? $nearByDoctorsClinics : [],
        'pagination'    => isset($pagination) ? $pagination : [],
    );


    return $response;
}


function deleteAccount($user_id = 0,$dataOnly = false){
    global $db;

    $user_type = getTableValue('tbl_users','user_type',array('id' => $user_id));

    if($user_type == 'patient'){
        $db->delete("tbl_appointment",array("user_id"=>$user_id));
    } else if($user_type == 'clinic'){

        $db->delete("tbl_users_time_slot",array("user_id"=>$user_id));
        $db->delete("tbl_users_specialties",array("user_id"=>$user_id));
        $db->delete("tbl_users_doctor_type",array("user_id"=>$user_id));
    } else if($user_type == 'doctor'){

        $db->delete("tbl_users_time_slot",array("user_id"=>$user_id));
        $db->delete("tbl_users_specialties",array("user_id"=>$user_id));
        $db->delete("tbl_users_doctor_type",array("user_id"=>$user_id));
    }

    $db->delete("tbl_email_notifications",array("user_id"=>$user_id));

    if ($dataOnly) {
        // $pushDataArray = array(
        //     'senderId'                  => '0',
        //     'receiverId'                => (string) $user_id,
        //     'redirectId'                => '0',
        //     'subject'                   => 'delete_user_by_admin',
        //     'notification'              => 'You account has been deleted by admin',
        //     'siteName'                  => SITE_NM
        // );
        // push_notifications($pushDataArray);
    }

    $db->delete("tbl_users",array('id' => $user_id));
}


function get_time_slot_table($user_id = 0){
    global $db;

    $time_slot_html = '';
    $days_array = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');
    if (count($days_array) > 0) {
        $time_slot_html.="<table class='table'>";

        foreach ($days_array as $key => $val) {
            $query = "SELECT t.*
            FROM tbl_users_time_slot AS t
            WHERE t.user_id = ".(int)$user_id." AND lower(day) = '".$val."'
            ORDER BY t.id ASC";
            $qrySel = $db->pdoQuery($query)->results();


            if (count($qrySel) > 0) {
                $slot_details = '';
                foreach ($qrySel as $k => $row) {
                    if ($row['is_available'] == 'y') {
                        $slot_details.= '<span class="tag-time">'.$row['from_time'].' to '.$row['to_time'].'</span>';
                    }
                }

                if ($slot_details != '') {
                    $time_slot_html.="<tr>
                    <th class='px-0 slot-th'>".ucfirst($val)."</th>
                    <td class='pe-0'><div class='tags-time'>".$slot_details."</div></td>
                    </tr>";
                } else{
                    $time_slot_html.="<tr>
                    <th class='px-0 slot-th'>".ucfirst($val)."</th>
                    <td class='pe-0'><span class='unb-label'>Unavailable</span></td>
                    </tr>";
                }
            } else{
                $time_slot_html.="<tr>
                <th class='px-0 slot-th'>".ucfirst($val)."</th>
                <td class='pe-0'><span class='unb-label'>Unavailable</span></td>
                </tr>";
            }

        }
        $time_slot_html.="</table>";
    }

    return $time_slot_html;
}

/*Fun. added by krishna Marakana*/


if (!function_exists('mime_content_type')) {
    function mime_content_type($filename){
        $idx           = explode('.', $filename);
        $count_explode = count($idx);
        $idx           = strtolower($idx[$count_explode - 1]);

        $mimet = array(
            'ai'      => 'application/postscript',
            'aif'     => 'audio/x-aiff',
            'aifc'    => 'audio/x-aiff',
            'aiff'    => 'audio/x-aiff',
            'asc'     => 'text/plain',
            'atom'    => 'application/atom+xml',
            'avi'     => 'video/x-msvideo',
            'bcpio'   => 'application/x-bcpio',
            'bmp'     => 'image/bmp',
            'cdf'     => 'application/x-netcdf',
            'cgm'     => 'image/cgm',
            'cpio'    => 'application/x-cpio',
            'cpt'     => 'application/mac-compactpro',
            'crl'     => 'application/x-pkcs7-crl',
            'crt'     => 'application/x-x509-ca-cert',
            'csh'     => 'application/x-csh',
            'css'     => 'text/css',
            'dcr'     => 'application/x-director',
            'dir'     => 'application/x-director',
            'djv'     => 'image/vnd.djvu',
            'djvu'    => 'image/vnd.djvu',
            'doc'     => 'application/msword',
            'dtd'     => 'application/xml-dtd',
            'dvi'     => 'application/x-dvi',
            'dxr'     => 'application/x-director',
            'eps'     => 'application/postscript',
            'etx'     => 'text/x-setext',
            'ez'      => 'application/andrew-inset',
            'gif'     => 'image/gif',
            'gram'    => 'application/srgs',
            'grxml'   => 'application/srgs+xml',
            'gtar'    => 'application/x-gtar',
            'hdf'     => 'application/x-hdf',
            'hqx'     => 'application/mac-binhex40',
            'html'    => 'text/html',
            'html'    => 'text/html',
            'ice'     => 'x-conference/x-cooltalk',
            'ico'     => 'image/x-icon',
            'ics'     => 'text/calendar',
            'ief'     => 'image/ief',
            'ifb'     => 'text/calendar',
            'iges'    => 'model/iges',
            'igs'     => 'model/iges',
            'jpe'     => 'image/jpeg',
            'jpeg'    => 'image/jpeg',
            'jpg'     => 'image/jpeg',
            'js'      => 'application/x-javascript',
            'kar'     => 'audio/midi',
            'latex'   => 'application/x-latex',
            'm3u'     => 'audio/x-mpegurl',
            'man'     => 'application/x-troff-man',
            'mathml'  => 'application/mathml+xml',
            'me'      => 'application/x-troff-me',
            'mesh'    => 'model/mesh',
            'mid'     => 'audio/midi',
            'midi'    => 'audio/midi',
            'mif'     => 'application/vnd.mif',
            'mov'     => 'video/quicktime',
            'movie'   => 'video/x-sgi-movie',
            'mp2'     => 'audio/mpeg',
            'mp3'     => 'audio/mpeg',
            'mpe'     => 'video/mpeg',
            'mpeg'    => 'video/mpeg',
            'mpg'     => 'video/mpeg',
            'mpga'    => 'audio/mpeg',
            'ms'      => 'application/x-troff-ms',
            'msh'     => 'model/mesh',
            'mxu m4u' => 'video/vnd.mpegurl',
            'nc'      => 'application/x-netcdf',
            'oda'     => 'application/oda',
            'ogg'     => 'application/ogg',
            'pbm'     => 'image/x-portable-bitmap',
            'pdb'     => 'chemical/x-pdb',
            'pdf'     => 'application/pdf',
            'pgm'     => 'image/x-portable-graymap',
            'pgn'     => 'application/x-chess-pgn',
            'php'     => 'application/x-httpd-php',
            'php4'    => 'application/x-httpd-php',
            'php3'    => 'application/x-httpd-php',
            'phtml'   => 'application/x-httpd-php',
            'phps'    => 'application/x-httpd-php-source',
            'png'     => 'image/png',
            'pnm'     => 'image/x-portable-anymap',
            'ppm'     => 'image/x-portable-pixmap',
            'ppt'     => 'application/vnd.ms-powerpoint',
            'ps'      => 'application/postscript',
            'qt'      => 'video/quicktime',
            'ra'      => 'audio/x-pn-realaudio',
            'ram'     => 'audio/x-pn-realaudio',
            'ras'     => 'image/x-cmu-raster',
            'rdf'     => 'application/rdf+xml',
            'rgb'     => 'image/x-rgb',
            'rm'      => 'application/vnd.rn-realmedia',
            'roff'    => 'application/x-troff',
            'rtf'     => 'text/rtf',
            'rtx'     => 'text/richtext',
            'sgm'     => 'text/sgml',
            'sgml'    => 'text/sgml',
            'sh'      => 'application/x-sh',
            'shar'    => 'application/x-shar',
            'shtml'   => 'text/html',
            'silo'    => 'model/mesh',
            'sit'     => 'application/x-stuffit',
            'skd'     => 'application/x-koan',
            'skm'     => 'application/x-koan',
            'skp'     => 'application/x-koan',
            'skt'     => 'application/x-koan',
            'smi'     => 'application/smil',
            'smil'    => 'application/smil',
            'snd'     => 'audio/basic',
            'spl'     => 'application/x-futuresplash',
            'src'     => 'application/x-wais-source',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc'  => 'application/x-sv4crc',
            'svg'     => 'image/svg+xml',
            'swf'     => 'application/x-shockwave-flash',
            't'       => 'application/x-troff',
            'tar'     => 'application/x-tar',
            'tcl'     => 'application/x-tcl',
            'tex'     => 'application/x-tex',
            'texi'    => 'application/x-texinfo',
            'texinfo' => 'application/x-texinfo',
            'tgz'     => 'application/x-tar',
            'tif'     => 'image/tiff',
            'tiff'    => 'image/tiff',
            'tr'      => 'application/x-troff',
            'tsv'     => 'text/tab-separated-values',
            'txt'     => 'text/plain',
            'ustar'   => 'application/x-ustar',
            'vcd'     => 'application/x-cdlink',
            'vrml'    => 'model/vrml',
            'vxml'    => 'application/voicexml+xml',
            'wav'     => 'audio/x-wav',
            'wbmp'    => 'image/vnd.wap.wbmp',
            'wbxml'   => 'application/vnd.wap.wbxml',
            'wml'     => 'text/vnd.wap.wml',
            'wmlc'    => 'application/vnd.wap.wmlc',
            'wmlc'    => 'application/vnd.wap.wmlc',
            'wmls'    => 'text/vnd.wap.wmlscript',
            'wmlsc'   => 'application/vnd.wap.wmlscriptc',
            'wmlsc'   => 'application/vnd.wap.wmlscriptc',
            'wrl'     => 'model/vrml',
            'xbm'     => 'image/x-xbitmap',
            'xht'     => 'application/xhtml+xml',
            'xhtml'   => 'application/xhtml+xml',
            'xls'     => 'application/vnd.ms-excel',
            'xml xsl' => 'application/xml',
            'xpm'     => 'image/x-xpixmap',
            'xslt'    => 'application/xslt+xml',
            'xul'     => 'application/vnd.mozilla.xul+xml',
            'xwd'     => 'image/x-xwindowdump',
            'xyz'     => 'chemical/x-xyz',
            'zip'     => 'application/zip',
        );

if (isset($mimet[$idx])) {
    return $mimet[$idx];
} else {
    return 'application/octet-stream';
}
}
}
