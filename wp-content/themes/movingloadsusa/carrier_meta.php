<?php
require $_SERVER['DOCUMENT_ROOT']."/movingloadsusa/wp-load.php";
    global $wpdb, $user_ID;
    if(!$user_ID){
        wp_safe_redirect(site_url().'/login/');
        exit();
    }
    $carrier_phone = get_user_meta($_REQUEST['id_carrier'], 'phone', TRUE);
    $first_name = get_user_meta($_REQUEST['id_carrier'], 'first_name', true);
    $last_name = get_user_meta($_REQUEST['id_carrier'], 'last_name', true);
    echo json_encode(array('carrier_phone' => $carrier_phone, 'carrier_name' => $first_name.' '.$last_name));
?>
