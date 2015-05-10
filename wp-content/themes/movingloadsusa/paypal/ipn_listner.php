<?php
require_once $_SERVER['DOCUMENT_ROOT']."/wp-blog-header.php";
require_once trailingslashit(get_template_directory()) . '/paypal/pp_ipn_functions.php';
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
    $keyval = explode ('=', $keyval);
    if (count($keyval) == 2)
        $myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
	$get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
	if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
		$value = urlencode(stripslashes($value));
	} else {
		$value = urlencode($value);
	}
	$req .= "&$key=$value";
}
$paypal_url = (mlusa_get_option('paypal_environment') == 'sandbox')?'https://www.sandbox.paypal.com/cgi-bin/webscr':'https://www.paypal.com/cgi-bin/webscr';
$ch = curl_init($paypal_url);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

if (!($res = curl_exec($ch))){
    curl_close($ch);
    exit();
}
curl_close($ch);
if (strcmp ($res, "VERIFIED") == 0) {
    global $wpdb;
    $data = array();
    foreach ($_POST as $key => $value) {
        $data = array(
            'id_user' => $_POST['custom'],
            'ipn_key' => $key,
            'ipn_value' => $value
        );
        $wpdb->insert('wp_pp_ipn_details', $data);
    }
    switch ($_POST['txn_type']){
        case "subscr_signup":
            func_subscr_signup($_POST);
            break;
        case "subscr_payment":
            func_subscr_payment($_POST);
            break;
        case "subscr_cancel":
            func_subscr_cancel($_POST);
            break;
        case "subscr_failed":
            func_subscr_failed($_POST);
            break;
        case "subscr_modify":
            func_subscr_modify($_POST);
            break;
        case "subscr_eot":
            func_subscr_eot($_POST);
            break;
        case "web_accept":
            func_web_accept($_POST);
            break;
        default :
            break;
    } 
} 
else if (strcmp ($res, "INVALID") == 0) {
	
}
?>