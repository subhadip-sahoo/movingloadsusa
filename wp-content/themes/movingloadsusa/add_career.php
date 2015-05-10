<?php
session_start();
require $_SERVER['DOCUMENT_ROOT']."/movingloadsusa/wp-blog-header.php";
require_once(ABSPATH . WPINC . '/user.php');
global $wpdb, $user_ID;
if(!$user_ID){
    wp_safe_redirect(home_url().'/login/');
    exit();
}
$err_msg = '';
$suc_msg = '';
$war_msg = '';
$email = esc_sql($_REQUEST['email']);		
$company_name = esc_sql($_REQUEST['company_name']);
$dot = esc_sql($_REQUEST['dot']);
$dot_exists = FALSE;
$wpdb->get_results("SELECT * FROM `wp_usermeta` WHERE `meta_key` = 'dot' AND `meta_value` = '$dot'");
if($wpdb->num_rows > 0){
    $dot_exists = TRUE;
}
$mc = esc_sql($_REQUEST['mc']);
$company_phone = preg_replace("/[^0-9]/","",esc_sql($_REQUEST['company_phone']));
$address = esc_sql($_REQUEST['address']);
$city = esc_sql($_REQUEST['city']);
$state = esc_sql($_REQUEST['state']);
$zip = esc_sql($_REQUEST['zip']);
$first_name = esc_sql($_REQUEST['first_name']);
$username = $first_name.time();       
$last_name = esc_sql($_REQUEST['last_name']);
$phone = preg_replace("/[^0-9]/","",esc_sql($_REQUEST['phone']));
$password = wp_generate_password( 10, false);
$user_type = $_REQUEST['user_status'];
if(empty($company_name)) { 
    $err_msg = "Please enter a company name.<br/>";
}
else if(empty($dot)) { 
    $err_msg = "Please enter dot.<br/>";
}
else if(!empty($dot) && $dot_exists == TRUE) { 
    $err_msg = "There is an another record with the same DOT. Please try another.<br/>";
}
else if(!is_numeric($dot)) { 
    $err_msg = "DOT must be numeric.<br/>";
}
else if(strlen($dot)!=7) { 
    $err_msg = "Dot number must be 7 digit.<br/>";
}
//else if(empty($mc)) { 
//    $err_msg = "Please enter mc.<br/>";
//}
else if(!empty($mc) && !is_numeric($mc)) { 
    $err_msg = "MC must be numeric.<br/>";
}
else if(!empty($mc) && strlen($mc)!=6) { 
    $err_msg = "MC number must be 6 digit.<br/>";
}
else if(empty($company_phone)) { 
    $err_msg = "Please enter a company phone number.<br/>";
}
else if(strlen($company_phone) < 10 || strlen($company_phone) > 11) { 
    $err_msg = "Company phone number should be 10-11 digits.<br/>";
}
else if(!is_numeric($company_phone)) { 
    $err_msg = "Company phone number should be numeric.<br/>";
}
else if(empty($address)) { 
    $err_msg = "Please enter Address.<br/>";
}
else if(empty($city)) { 
    $err_msg = "Please enter City name.<br/>";
}
else if(empty($state)) { 
    $err_msg = "Please select State.<br/>";
}
else if(empty($zip)) { 
    $err_msg = "Please enter zip code.<br/>";
}
else if(empty($first_name)) { 
    $err_msg = "Please enter your first name.<br/>";
}
else if(empty($last_name)) { 
    $err_msg = "Please enter your last name.<br/>";
}
else if(empty($phone)) { 
    $err_msg = "Please enter your phone number.<br/>";
}
else if(strlen($phone) < 10 || strlen($phone) > 11) { 
    $err_msg = "Phone number should be 10-11 digits.<br/>";
}
else if(!is_numeric($phone)) { 
    $err_msg = "Phone number should be numeric.<br/>";
}
else if(empty($email)) { 
    $err_msg = "Please enter an email address.<br/>";
}
else if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $email)) { 
    $err_msg = "Please enter a valid email.<br/>";
}
else if(!isset($_REQUEST['tc']) && $_REQUEST['tc'] == 'on') { 
    $err_msg = "Please check Terms & Conditions.<br/>";
}
else {
    if( $_SESSION['security_code'] == $_REQUEST['security_code'] && !empty($_SESSION['security_code'] ) ){
        $userinfo = array(
            'user_login' => $username,
            'user_pass' => $password,
            'user_email' => $email
        );
        $status = wp_insert_user($userinfo);
        if ( is_wp_error($status) ) {
            $war_msg .= 'Email Address already exists. Please try another one.';
        }
        if($war_msg == ''){
            $userdata = array(
                'ID' => $status,
                'user_pass'=>$password,
                'display_name' => $first_name
            );
            $new_user_id = wp_update_user( $userdata );
            $wpdb->update('wp_users', array('user_status' => $user_type), array('ID' => $new_user_id));
            $keys = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE ID = $new_user_id", ARRAY_A);
            if($wpdb->num_rows == 1){
                foreach ($keys as $key) {
                    if(!empty($key['user_activation_key'])){
                        $act_key = $key['user_activation_key'];
                        $set_key = 1;
                    }else{
                        $set_key = 0;
                    }
                }
            }
            $from = get_option('admin_email');
            $from_name = "Moving Loads USA";
            $headers = "From: movinngloadsusa <$from>\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $subject = "You have been awarded a job";
            $msg = "Dear $first_name, <br/>You have been awarded a job. You have to login into your account to view your job.<br/>";
            $msg .= "Your login credentials<br/>Username: $email<br/>Password: $password<br/>";
            $msg .= "Please change your password after login (Recommended).<br/>";
            $msg .= "Before that you have to verify your email by clicking the below link.<br/>";
            if(isset($set_key) && $set_key == 1){
                $msg .= "<a href='".site_url()."/verify-email/?verify_id=$new_user_id&activation_key=$act_key'>Click here for verify your email</a><br/><br/>";
            }else{
                $msg .= "<a href='".site_url()."/verify-email/?verify_id=$new_user_id'>Click here for verify your email</a><br/><br/>";
            }
            $msg .= "You will receive another email regarding your job details.";
            $msg .= "Best regards<br/>Moving Loads USA Admin";
            wp_mail( $email, $subject, $msg, $headers );
            foreach ($_REQUEST as $key => $post) {
                if($_REQUEST['email'] == $post || $_REQUEST['submit_career'] == $post || $_REQUEST['tc'] == $post || $_REQUEST['user_status'] == $post){
                    continue;
                }
                else if($_POST['company_phone'] == $post || $_POST['phone'] == $post){
                    update_user_meta($new_user_id, $key, format_phone(preg_replace("/[^0-9]/","",esc_sql($post))));
                }
                else{
                    update_user_meta($new_user_id, $key, esc_sql($post));
                }
            }
            $suc_msg .= 'You have successfully added this carrier.';
        }
    }else{
        $err_msg = "Invalid secuirity code.<br/>";
    }
}
if(!empty($err_msg)){
    $class = 'err_msg';
    echo json_encode(array('class' => $class, 'message' => $err_msg));
}
else if(!empty($war_msg)){
    $class = 'war_msg';
    echo json_encode(array('class' => $class, 'message' => $war_msg));
}
else{
    $class = 'suc_msg';
    $carrier_phone = get_user_meta($new_user_id, 'phone', TRUE);
    $first_name = get_user_meta($new_user_id, 'first_name', true);
    $last_name = get_user_meta($new_user_id, 'last_name', true);
    echo json_encode(array('class' => $class, 'message' => $suc_msg, 'display_name' => $first_name, 'dot' => $dot, 'carrier_phone' => $phone, 'carrier_name' => $first_name.' '.$last_name, 'id_carrier' => $new_user_id));
}
?>