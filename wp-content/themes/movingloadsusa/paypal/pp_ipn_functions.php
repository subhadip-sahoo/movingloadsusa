<?php
function func_subscr_signup($arr_ipn){
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = '".$arr_ipn['custom']."'");
    if($wpdb->num_rows == 1){
        foreach ($results as $result) {
            $user_email = $result->user_email;
        }
    }
    //******  A mail has been thrown after executing this code ************** //
    $from = get_option('admin_email');
    $from_name = "Moving Loads USA";
    $headers = "From: movinngloadsusa <$from>\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $subject = "Your subscription has been successful.";
    $msg = "Thank you for subscription.<br/>Your subscription details are as follows<br/>";
    $msg .= "PayPal Email Address: ".$arr_ipn['payer_email']."<br/>Subscription ID: ".$arr_ipn['subscr_id']."<br/>";
    $msg .= "Subscription Date: ".$arr_ipn['subscr_date']."<br/>";
    $msg .= "You will receive another email regarding payment status shortly.<br/>";
    $msg .= "Best regards<br/>movinngloadsusa admin";
    wp_mail( $user_email, $subject, $msg, $headers );
}

function func_subscr_payment($arr_ipn){
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = '".$arr_ipn['custom']."'");
    if($wpdb->num_rows == 1){
        foreach ($results as $result) {
            $user_email = $result->user_email;
        }
    }
    if(get_user_meta($arr_ipn['custom'], 'account_expiry', TRUE) < date('Y-m-d H:i:s')){
        update_user_meta($arr_ipn['custom'], 'account_expiry', date('Y-m-d H:i:s', strtotime('+30 day')));
    }
    else if(get_user_meta($arr_ipn['custom'], 'account_expiry', TRUE) >= date('Y-m-d H:i:s')){
        $previous_expiry_date = get_user_meta($arr_ipn['custom'], 'account_expiry', TRUE);
        update_user_meta($arr_ipn['custom'], 'account_expiry', date('Y-m-d H:i:s', strtotime($previous_expiry_date.'+30 day')));
    }
    $from = get_option('admin_email');
    $from_name = "Moving Loads USA";
    $headers = "From: movinngloadsusa <$from>\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $subject = "Your payment has been successful completed.";
    $msg = "Your payment details are as follows<br/>";
    $msg .= "Transaction ID: ".$arr_ipn['txn_id']."<br/>Transaction amount: ".$arr_ipn['mc_gross']." ".$arr_ipn['mc_currency']."<br/>";
    $msg .= "Payment Date: ".$arr_ipn['payment_date']."<br/>Subscription ID: ".$arr_ipn['subscr_id']."<br/>";
    $msg .= "PayPal Email Address: ".$arr_ipn['payer_email']."<br/>";
    $msg .= "Best regards<br/>movinngloadsusa admin";
    wp_mail( $user_email, $subject, $msg, $headers );	
}

function func_subscr_cancel($arr_ipn){
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = '".$arr_ipn['custom']."'");
    if($wpdb->num_rows == 1){
        foreach ($results as $result) {
            $user_email = $result->user_email;
        }
    }
    $from = get_option('admin_email');
    $from_name = "Moving Loads USA";
    $headers = "From: movinngloadsusa <$from>\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $subject = "Your have canceled your subscription.";
    $msg = "Your subscription details are as follows<br/>";
    $msg .= "PayPal Email Address: ".$arr_ipn['payer_email']."<br/>Subscription ID: ".$arr_ipn['subscr_id']."<br/>";
    $msg .= "Subscription Cancellation Date: ".$arr_ipn['subscr_date']."<br/>";
    $msg .= "You will receive another email regarding payment status shortly.<br/>";
    $msg .= "Best regards<br/>movinngloadsusa admin";
    wp_mail( $user_email, $subject, $msg, $headers );
	
	
}
function func_subscr_failed($arr_ipn){
    global $wpdb;

}
function func_subscr_modify($arr_ipn){
    global $wpdb;

}
function func_subscr_eot($arr_ipn){
    global $wpdb;

}
function func_web_accept($arr_ipn){
    global $wpdb, $table_name;
    $table_name = $wpdb->prefix . 'post_package_master';
    $get_packages = $wpdb->get_results("SELECT * FROM $table_name WHERE id_post_package = ".$arr_ipn['item_number'], ARRAY_A);
    $data = array();
    if($wpdb->num_rows == 1){
        foreach ($get_packages as $get_package) {
            $post_count = $arr_ipn['quantity'] * $get_package['total_posts'];
            $expiry_date = date('Y-m-d H:i:s', strtotime('+'.$get_package['expiry_package'].'day'));
            $data = array(
                'id_user' => $arr_ipn['custom'],
                'id_package' => $arr_ipn['item_number'],
                'quantity' => $arr_ipn['quantity'],
                'post_count' => $post_count,
                'expiry_date' => $expiry_date
            );
        }
        $wpdb->insert('wp_user_postaccount', $data);
    }
    $results = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = '".$arr_ipn['custom']."'");
    if($wpdb->num_rows == 1){
        foreach ($results as $result) {
            $user_email = $result->user_email;
        }
    }
    //******  A mail has been thrown after executing this code ************** //
    $from = get_option('admin_email');
    $from_name = "Moving Loads USA";
    $headers = "From: movinngloadsusa <$from>\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $subject = "Your payment has been successful completed.";
    $msg = "Your payment details are as follows<br/>";
    $msg .= "Package Name: ".$arr_ipn['item_name']."<br/>";
    $msg .= "Quantity: ".$arr_ipn['quantity']."<br/>";
    $msg .= "Transaction ID: ".$arr_ipn['txn_id']."<br/>Transaction amount: ".$arr_ipn['mc_gross']." ".$arr_ipn['mc_currency']."<br/>";
    $msg .= "Payment Date: ".$arr_ipn['payment_date']."<br/>";
    $msg .= "PayPal Email Address: ".$arr_ipn['payer_email']."<br/>";
    $msg .= "This package will expire on ".date(DISPLAY_FORMAT_DATETIME_SHORT, strtotime($expiry_date))."<br/>";
    $msg .= "Best regards<br/>movinngloadsusa admin";
    wp_mail( $user_email, $subject, $msg, $headers );
	
	foreach ($results as $result) {
            $display_name = $result->display_name;
			$ID = $result->ID;	
		}
			$user_details = array(
                'userid' => $ID,
                'name' => $display_name,
                'status' => $arr_ipn['payment_status'],
                'date' => $arr_ipn['payment_date'],
                'amount' => $arr_ipn['mc_gross'],
				'item' => $arr_ipn['item_name'],
				'transaction_id' => $arr_ipn['txn_id'],
				
				
            );
			$wpdb->insert('wp_user_details', $user_details);
}
?>