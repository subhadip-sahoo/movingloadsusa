<?php
session_start();
/* Template Name: Braintree Payment */
require_once trailingslashit(get_template_directory()) . '/Braintree/lib/Braintree.php';
require_once trailingslashit(get_template_directory()) . '/Braintree/Braintree_account.php';
global $wpdb, $user_ID;
$display_msg = '';
$err_msg = '';
if(!$user_ID){
    wp_safe_redirect(site_url().'/login');
    exit();
}
if(isset($_POST['pay_with_braintree'])){
    $userdata = get_userdata($user_ID);
    $user_fname = $userdata->first_name;
    $user_lname = $userdata->last_name;
    $credit_card_no = esc_sql($_POST['credit_card_no']);
    $exp_month = esc_sql($_POST['exp_month']);
    $exp_year = esc_sql($_POST['exp_year']);
    $cvv_no= esc_sql($_POST['cvv_no']);
    if(empty($_POST['price'])){
        wp_safe_redirect(site_url().'/post-packages/');
        exit();
    }
    else if(empty($_POST['package_id'])){
        wp_safe_redirect(site_url().'/post-packages/');
        exit();
    }
    else if(empty($credit_card_no)){
        $err_msg .= 'Card number should not be empty.<br/>';
    }
    else if(empty($exp_month)){
        $err_msg .= 'Expiry month should not be empty.<br/>';
    }
    else if(empty($exp_year)){
        $err_msg .= 'Expiry year should not be empty.<br/>';
    }
    else if(empty($cvv_no)){
        $err_msg .= 'CVV number should not be empty.<br/>';
    }
    if($err_msg == ''){
        $result = Braintree_Transaction::sale(array(
                                                    'amount' => $_POST['price'],
                                                    'orderId' => $_POST['package_id'],
                                                    'creditCard' => array(
                                                            'number' => $credit_card_no,
                                                            'cvv' => $cvv_no,
                                                            'expirationMonth' => $exp_month,
                                                            'expirationYear' => $exp_year,
                                                            'cardholderName' => $user_fname.' '.$user_lname
                                                        ),
                                                    'options' => array(
                                                            'submitForSettlement' => true
                                                        )
                                                    )
                                                );
        if($result->success){
            switch($result->transaction->status){
                case 'authorized':
                    $get_packages = $wpdb->get_results("SELECT * FROM wp_post_package_master WHERE id_post_package = ".$result->transaction->orderId, ARRAY_A);
                    $data = array();
                    if($wpdb->num_rows == 1){
                        foreach ($get_packages as $get_package) {
                            $post_count = $_POST['quantity'] * $get_package['total_posts'];
                            $expiry_date = date('Y-m-d H:i:s', strtotime('+'.$get_package['expiry_package'].'day'));
                            $data = array(
                                'id_user' => $user_ID,
                                'id_package' => $result->transaction->orderId,
                                'quantity' => $_POST['quantity'],
                                'post_count' => $post_count,
                                'expiry_date' => $expiry_date
                            );
                        }
                        $wpdb->insert('wp_user_postaccount', $data);
                    }
                    $user_results = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = $user_ID");
                    if($wpdb->num_rows == 1){
                        foreach ($user_results as $user_result) {
                            $user_email = $user_result->user_email;
                        }
                    }
                    //******  A mail has been thrown after executing this code ************** //
                    $from = get_option('admin_email');
                    $from_name = "Moving Loads USA";
                    $headers = "From: movinngloadsusa <$from>\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $subject = "Your payment has been successful completed.";
                    $msg = "Your payment details are as follows<br/>";
                    $msg .= "Package Name: ".$_POST['package_name']."<br/>";
                    $msg .= "Quantity: ".$_POST['quantity']."<br/>";
                    $msg .= "Transaction ID: ".$result->transaction->id."<br/>Transaction amount: ".$result->transaction->amount." ".$result->transaction->currencyIsoCode."<br/>";
                    $msg .= "Payment Date: ".$result->transaction->createdAt->format('Y-m-d H:i:s')."<br/>";
                    $msg .= "This package will expire on ".date(DISPLAY_FORMAT_DATETIME_SHORT, strtotime($expiry_date))."<br/>";
                    $msg .= "Please take care of Transaction ID for any future issue/enquiry.<br/>";
                    $msg .= "Best regards<br/>movinngloadsusa admin";
                    wp_mail( $user_email, $subject, $msg, $headers );
                    foreach ($user_results as $user_result) {
                        $display_name = $user_result->display_name;
                        $ID = $user_result->ID;	
                    }
                    $user_details = array(
                        'userid' => $ID,
                        'name' => $display_name,
                        'status' => 'Completed',
                        'date' => $result->transaction->createdAt->format('Y-m-d H:i:s'),
                        'amount' => $result->transaction->amount,
                        'item' => $_POST['package_name'],
                        'transaction_id' => $result->transaction->id,
                    );
                    $wpdb->insert('wp_user_details', $user_details);
                    $display_msg .= "<p>Your payment has been successful completed.</p>";
                    $display_msg .= "<p><label>Package Name: </label>".$_POST['package_name']."<p>";
                    $display_msg .= "<p><label>Transaction ID: </label>".$result->transaction->id."<p>";
                    $display_msg .= "<p><label>Transaction amount:</label> ".$result->transaction->amount." ".$result->transaction->currencyIsoCode."<p>";
                    $display_msg .= "<p><label>Payment Date:</label> ".$result->transaction->createdAt->format('Y-m-d H:i:s')."<p>";
                    $display_msg .= "<p>Please note down the Transaction ID for future use.<p>";
                    $display_msg .= "<p>A mail has been forwarded to you. Please check your inbox.<p>";
                    $display_msg .= "<p><a href='".site_url()."/my-account/'>Click here</a> to visit your account.<p>";
                    break;
                case 'submitted_for_settlement':
                    $get_packages = $wpdb->get_results("SELECT * FROM wp_post_package_master WHERE id_post_package = ".$result->transaction->orderId, ARRAY_A);
                    $data = array();
                    if($wpdb->num_rows == 1){
                        foreach ($get_packages as $get_package) {
                            $post_count = $_POST['quantity'] * $get_package['total_posts'];
                            $expiry_date = date('Y-m-d H:i:s', strtotime('+'.$get_package['expiry_package'].'day'));
                            $data = array(
                                'id_user' => $user_ID,
                                'id_package' => $result->transaction->orderId,
                                'quantity' => $_POST['quantity'],
                                'post_count' => $post_count,
                                'expiry_date' => $expiry_date
                            );
                        }
                        $wpdb->insert('wp_user_postaccount', $data);
                    }
                    $user_results = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = $user_ID");
                    if($wpdb->num_rows == 1){
                        foreach ($user_results as $user_result) {
                            $user_email = $user_result->user_email;
                        }
                    }
                    //******  A mail has been thrown after executing this code ************** //
                    $from = get_option('admin_email');
                    $from_name = "Moving Loads USA";
                    $headers = "From: movinngloadsusa <$from>\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    $subject = "Your payment has been successful completed.";
                    $msg = "Your payment details are as follows<br/>";
                    $msg .= "Package Name: ".$_POST['package_name']."<br/>";
                    $msg .= "Quantity: ".$_POST['quantity']."<br/>";
                    $msg .= "Transaction ID: ".$result->transaction->id."<br/>Transaction amount: ".$result->transaction->amount." ".$result->transaction->currencyIsoCode."<br/>";
                    $msg .= "Payment Date: ".$result->transaction->createdAt->format('Y-m-d H:i:s')."<br/>";
                    $msg .= "This package will expire on ".date(DISPLAY_FORMAT_DATETIME_SHORT, strtotime($expiry_date))."<br/>";
                    $msg .= "Please take care of Transaction ID for any future issue/enquiry.<br/>";
                    $msg .= "Best regards<br/>movinngloadsusa admin";
                    wp_mail( $user_email, $subject, $msg, $headers );
                    foreach ($user_results as $user_result) {
                        $display_name = $user_result->display_name;
                        $ID = $user_result->ID;	
                    }
                    $user_details = array(
                        'userid' => $ID,
                        'name' => $display_name,
                        'status' => 'Completed',
                        'date' => $result->transaction->createdAt->format('Y-m-d H:i:s'),
                        'amount' => $result->transaction->amount,
                        'item' => $_POST['package_name'],
                        'transaction_id' => $result->transaction->id,
                    );
                    $wpdb->insert('wp_user_details', $user_details);
                    $display_msg .= "<p>Your payment has been successful completed.</p>";
                    $display_msg .= "<p><label>Package Name:</label> ".$_POST['package_name']."<p>";
                    $display_msg .= "<p><label>Transaction ID:</label> ".$result->transaction->id."<p>";
                    $display_msg .= "<p><label>Transaction amount:</label> ".$result->transaction->amount." ".$result->transaction->currencyIsoCode."<p>";
                    $display_msg .= "<p><label>Payment Date:</label> ".$result->transaction->createdAt->format('Y-m-d H:i:s')."<p>";
                    $display_msg .= "<p>Please note down the Transaction ID for future use.<p>";
                    $display_msg .= "<p>A mail has been forwarded to you. Please check your inbox.<p>";
                    $display_msg .= "<p><a href='".site_url()."/my-account/'>Click here</a> to visit your account.<p>";
                    break;
                default:
                    break;
            }
            $_SESSION['status'] = $display_msg;
            unset($_POST);
            wp_safe_redirect(site_url().'/payment-result');
            exit();
        }
        else if(!$result->success){
            if($result->transaction){
                switch($result->transaction->status){
                    case 'processor_declined':
                        $user_results = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = $user_ID");
                        if($wpdb->num_rows == 1){
                            foreach ($user_results as $user_result) {
                                $user_email = $user_result->user_email;
                            }
                        }
                        //******  A mail has been thrown after executing this code ************** //
                        $from = get_option('admin_email');
                        $from_name = "Moving Loads USA";
                        $headers = "From: movinngloadsusa <$from>\r\n";
                        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                        $subject = "Your transaction is declined by processor.";
                        $msg = "Your payment has not been successfully completed because processor declines the transaction.<br/>";
                        $msg .= "We are very sorry for the inconvenience caused.<br/>";
                        $msg .= "The status what we have got from the server is stated below<br/>";
                        $msg .= $result->transaction->processorResponseCode." : ".$result->transaction->processorResponseText;
                        $msg .= "We are very sorry for the inconvenience caused.<br/>";
                        $msg .= "Please try again later. Thank you.<br/>";
                        $msg .= "Best regards<br/>movinngloadsusa admin";
                        wp_mail( $user_email, $subject, $msg, $headers );
                        $display_msg .= "<p>Your payment has not been successfully completed because processor declines the transaction.</p>";
                        $display_msg .= "<p>We are very sorry for the inconvenience caused.<p>";
                        $display_msg .= "<p>Status code:".$result->transaction->processorResponseCode."</p>";
                        $display_msg .= "<p>Status Message:".$result->transaction->processorResponseText."</p>";
                        $display_msg .= "<p><a href='".site_url()."/my-account/'>Click here</a> to visit your account.<p>";
                        break;
                    case 'gateway_rejected':
                        $user_results = $wpdb->get_results("SELECT * FROM wp_users WHERE ID = $user_ID");
                        if($wpdb->num_rows == 1){
                            foreach ($user_results as $user_result) {
                                $user_email = $user_result->user_email;
                            }
                        }
                        //******  A mail has been thrown after executing this code ************** //
                        $from = get_option('admin_email');
                        $from_name = "Moving Loads USA";
                        $headers = "From: movinngloadsusa <$from>\r\n";
                        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                        $subject = "Your transaction is rejected by the gateway.";
                        $msg = "Your payment has not been successfully completed because transaction is rejected by the gateway.<br/>";
                        $msg .= "We are very sorry for the inconvenience caused.<br/>";
                        $msg .= "The status what we have got from the server is stated below<br/>";
                        $msg .= $result->transaction->gatewayRejectionReason;
                        $msg .= "We are very sorry for the inconvenience caused.<br/>";
                        $msg .= "Please try again later. Thank you.<br/>";
                        $msg .= "Best regards<br/>movinngloadsusa admin";
                        wp_mail( $user_email, $subject, $msg, $headers );
                        $display_msg .= "<p>Your payment has not been successfully completed because transaction is rejected by the gateway.</p>";
                        $display_msg .= "<p>We are very sorry for the inconvenience caused.<p>";
                        $display_msg .= "<p>Status Message:".$result->transaction->gatewayRejectionReason."</p>";
                        $display_msg .= "<p><a href='".site_url()."/my-account/'>Click here</a> to visit your account.<p>";
                        break;
                    default:
                        break;
                }
            }
            else{
                $display_msg .= "<p>Validation errors occured</p>";
                foreach ($result->errors->deepAll() as $error) {
                    $display_msg .= "<p>" . $error->message . "</p>";
                }
                $display_msg .= "<p><a href='".site_url()."/my-account/'>Click here</a> to visit your account.<p>";
            }
        }
        $_SESSION['status'] = $display_msg;
        unset($_POST);
        wp_safe_redirect(site_url().'/payment-result');
        exit();
    }
}
get_header();
?>
<div class="ctn">
    <!--contain-->
    <div class="contain_main">
        <div class="ctn_in">
            <h1>PAY WITH BRAINTREE</h1>
            <div class="registration_main">
                <form action="" method="POST" name="braintree" id="braintree">														
                    <div class="registration_main_bg" id="credit_card_info">
                        <h2>CREDIT CARD INFO</h2>
                        <div class="registration_main_in">
                            <p id="err_msgs" class="<?php if(!empty($err_msg)){echo 'err_msg';}else if(!empty($war_msg)){echo 'war_msg';}else{echo 'suc_msg';}?>">
                                <?php
                                        if(!empty($err_msg)){echo $err_msg;}
                                        else if(!empty($war_msg)){echo $war_msg;}
                                        else{echo $suc_msg;}
                                ?>
                            </p>
                            <p>
                                <label>Card Number</label>
                                <input name="credit_card_no" id="credit_card_no" type="text" autocomplete="off" value="" class="registration_txtfld" ></p>
                            <p>
                                <label>Expiry Date</label>
                                <input type="text" placeholder="MM" maxlength="2" autocomplete="off" name="exp_month" class="post_loads_txtfld_002 dt_dv"/> / 
                                <input type="text" placeholder="YYYY" maxlength="4" autocomplete="off" name="exp_year" class="post_loads_txtfld_002 dt_dv"/>
                            </p>
                            <p>
                                <label>CVV:</label>
                                <input name="cvv_no" type="text" autocomplete="off" class="registration_txtfld" value="">
                            </p>
                        </div>
                    </div>
                    <div class="save_btn">
                        <input type="hidden" name="package_name" value="<?php echo $_POST['item_name'];?>">
                        <input type="hidden" name="price" value="<?php echo $_POST['amount'];?>">
                        <input type="hidden" name="currency_code" value="USD">
                        <input type="hidden" name="package_id" value="<?php echo $_POST['item_number'];?>">
                        <input type="hidden" name="quantity" value="<?php echo $_POST['undefined_quantity'];?>">
                        <input name="pay_with_braintree" type="submit" value="Pay Now" class="btn_3" id="sub_btn" >
                    </div>
            </form>
        </div>
    </div>
</div>
<!--cnt_div-end-->
</div>
<?php get_footer(); ?>