<?php
$reqAuth = false;
$module  = 'registration-nct';
require_once "../../includes-nct/config-nct.php";
require_once "class.registration-nct.php";

if ($_SERVER["SERVER_NAME"] != 'localhost') {
    require_once DIR_INC."google-api-php-client/vendor/autoload.php";
}


extract($_REQUEST);
$winTitle = $headTitle = 'Sign up' . ' - ' . SITE_NM;

$metaTag = getMetaTags(array(
    "description" => $winTitle,
    "keywords"    => $headTitle,
    "author"      => AUTHOR,
));

$js_array = array(
    SITE_JS . "modules/$module.js?v=".FILE_UPDATED_VERSION,
    SITE_JS . 'intlTelInput-jquery.min.js',
);

if ($sessUserId > 0) {
    $msgType = $_SESSION['msgType'] = disMessage(array(
        "type" => "suc",
        "var"  => You_are_already_logged_in,
    ));
    redirectPage(SITE_URL);
} 

$obj = new Registration($module, 0, $_REQUEST);


if (isset($action) && isset($method) && $action == "method" && method_exists($obj, $method)) {
    $response['status'] = 1;
    $response['msg'] = 'undefined';
    $response['html'] = $obj->{$method}();

    $msgType = $_SESSION["msgType"] = disMessage(array(
        'type' => isset($response['html']['status']) ?  $response['html']['status'] : 'err',
        'var'  => isset($response['html']['message']) ?  $response['html']['message'] : 'error',
    ));

    if (isset($response['html']['status']) && $response['html']['status']) {
        redirectPage(SITE_URL);
    } else{
        redirectPage(SITE_LOGIN);
    }
} else if (isset($_POST["provider"]) && $_POST["provider"]=='google' && $_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);

    $responseArray = array(
        'status'        => 'error',
        'message'       => something_went_wrong,
        'redirect_url'  => SITE_URL
    );

    if ($provider == 'google' && !empty($id_token)) {

        try {
            $client = new Google_Client([
                'client_id' => GOOGLE_CLIENT_ID
            ]);

            $payload = $client->verifyIdToken($id_token);

            if ($payload === false) {
                echo "Invalid token";
                exit;
            }

            if (isset($payload['sub']) && $payload['sub'] != '' && isset($payload['email']) && $payload['email'] != '') {
                $sendArray = array(
                    'identifier' => $payload['sub'],
                    'email'      => $payload['email'],
                    'firstName'  => $payload['given_name'] ?? '',
                    'lastName'   => $payload['family_name'] ?? '',
                    'picture'    => $payload['picture'] ?? '',
                    'loginType'  => 'g',
                    'user_type'     => 'n',
                );

                $responseArray = $obj->socialSignup($sendArray);
            }
        } catch (Exception $e) {
            echo "Google Verify Error: " . $e->getMessage();
            exit;
        }
    }

    echo json_encode($responseArray);
    exit;
} 

$pageContent = $obj->getPageContent();

require_once DIR_TMPL . "parsing-nct.tpl.php";
