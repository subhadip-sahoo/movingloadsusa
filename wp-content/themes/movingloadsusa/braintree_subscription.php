<?php
session_start();
/* Template Name: Braintree Subscription */
require_once trailingslashit(get_template_directory()) . '/Braintree/lib/Braintree.php';
require_once trailingslashit(get_template_directory()) . '/Braintree/Braintree_account.php';
global $wpdb;
$display_msg = '';
$err_msg = '';

if(isset($_POST['pay_with_braintree'])){
    $user_ID = $_POST['user_ID'];
    $credit_card_no = esc_sql($_POST['credit_card_no']);
    $exp_month = esc_sql($_POST['exp_month']);
    $exp_year = esc_sql($_POST['exp_year']);
    $cvv_no= esc_sql($_POST['cvv_no']);
    if(empty($user_ID)){
        wp_safe_redirect(site_url().'/login');
        exit();
    }
    else if(empty($_POST['price'])){
        wp_safe_redirect(site_url().'/post-packages/');
        exit();
    }
    else if(empty($_POST['plan_id'])){
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
        $userdata = get_userdata($user_ID);
        $user_email = $userdata->user_email;
        $user_fname = $userdata->first_name;
        $user_lname = $userdata->last_name;
        $user_company = $userdata->company_name;
        $user_phone = $userdata->phone;
        $result = Braintree_Customer::create(array(
            'firstName' => $user_fname,
            'lastName' => $user_lname,
            'company' => $user_company,
            'email' => $user_email,
            'phone' => $user_phone
        ));
        if($result->success){
            $customer_id = $result->customer->id;
            $result = Braintree_CreditCard::create(array(
                'customerId' => $customer_id,
                'number' => $credit_card_no,
                'expirationMonth' => $exp_month,
                'expirationYear' => $exp_year,
                'cardholderName' => $user_fname.' '.$user_lname
            ));
            if($result->success){
                $token = $result->creditCard->token;
                $result = Braintree_Subscription::create(array(
                    'paymentMethodToken' => $token,
                    'planId' => $_POST['plan_id'],
                    'price' => $_POST['price'],
                    'trialPeriod' => false,
                    'options' => array('startImmediately' => true)
                ));
                 if($result->success){
                    switch($result->subscription->status){
                        case 'Active':
                            if(get_user_meta($user_ID, 'account_expiry', TRUE) < date('Y-m-d H:i:s')){
                                update_user_meta($user_ID, 'account_expiry', date('Y-m-d H:i:s', strtotime('+30 day')));
                            }
                            else if(get_user_meta($user_ID, 'account_expiry', TRUE) >= date('Y-m-d H:i:s')){
                                $previous_expiry_date = get_user_meta($user_ID, 'account_expiry', TRUE);
                                update_user_meta($user_ID, 'account_expiry', date('Y-m-d H:i:s', strtotime($previous_expiry_date.'+30 day')));
                            }
                            $from = get_option('admin_email');
                            $from_name = "Moving Loads USA";
                            $headers = "From: movinngloadsusa <$from>\r\n";
                            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                            $subject = "You have successfully subscribed.";
                            $msg = "Your subcription details are as follows<br/>";
                            $msg .= "Transaction ID: ".$result->subscription->transactions[0]->id."<br/>Transaction amount: ".$result->subscription->transactions[0]->amount." ".$result->subscription->transactions[0]->currencyIsoCode."<br/>";
                            $msg .= "Payment Date: ".$result->subscription->transactions[0]->createdAt->format('Y-m-d H:i:s')."<br/>";
                            $msg .= "Subscription ID: ".$result->subscription->id."<br/>";
                            $msg .= "Please take care of Transaction ID and Subscription ID for future use.<br/>";
                            $msg .= "Subscription ID will be required at the time cancelation of subscription.<br/>";
                            $msg .= "Best regards<br/>movinngloadsusa admin";
                            wp_mail( $user_email, $subject, $msg, $headers );
                            $display_msg .= "<p>You have successfully subscribed.</p>";
                            $display_msg .= "<p>Transaction ID: ".$result->subscription->transactions[0]->id."<p>";
                            $display_msg .= "<p>Transaction amount: ".$result->subscription->transactions[0]->amount." ".$result->subscription->transactions[0]->currencyIsoCode."<p>";
                            $display_msg .= "<p>Payment Date: ".$result->subscription->transactions[0]->createdAt->format('Y-m-d H:i:s')."<p>";
                            $display_msg .= "<p>Subscription ID: ".$result->subscription->id."<p>";
                            $display_msg .= "<p>Please note down Transaction ID and Subscription ID for future use.<p>";
                            $display_msg .= "<p>Subscription ID will be required at the time cancelation of subscription.<p>";
                            $display_msg .= "<p><a href='".site_url()."/my-account/'>Click here</a> to visit your account.<p>";
                            break;
                        case 'Pending':
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
                     if($result->subscription->transactions[0]){
                        switch($result->subscription->transactions[0]->status){
                            case 'processor_declined':
                                //******  A mail has been thrown after executing this code ************** //
                                $from = get_option('admin_email');
                                $from_name = "Moving Loads USA";
                                $headers = "From: movinngloadsusa <$from>\r\n";
                                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                                $subject = "Your transaction is declined by processor.";
                                $msg = "Your payment has not been successfully completed because processor declines the transaction.<br/>";
                                $msg .= "We are very sorry for the inconvenience caused.<br/>";
                                $msg .= "The status what we have got from the server is stated below<br/>";
                                $msg .= $result->subscription->transactions[0]->processorResponseCode." : ".$result->subscription->transactions[0]->processorResponseText;
                                $msg .= "We are very sorry for the inconvenience caused.<br/>";
                                $msg .= "Please try again later. Thank you.<br/>";
                                $msg .= "Best regards<br/>movinngloadsusa admin";
                                wp_mail( $user_email, $subject, $msg, $headers );
                                $display_msg .= "<p>Your payment has not been successfully completed because processor declines the transaction.</p>";
                                $display_msg .= "<p>We are very sorry for the inconvenience caused.<p>";
                                $display_msg .= "<p>Status code:".$result->subscription->transactions[0]->processorResponseCode."</p>";
                                $display_msg .= "<p>Status Message:".$result->subscription->transactions[0]->processorResponseText."</p>";
                                $display_msg .= "<p><a href='".site_url()."/my-account/'>Click here</a> to visit your account.<p>";
                                break;
                            case 'gateway_rejected':
                                //******  A mail has been thrown after executing this code ************** //
                                $from = get_option('admin_email');
                                $from_name = "Moving Loads USA";
                                $headers = "From: movinngloadsusa <$from>\r\n";
                                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                                $subject = "Your transaction is rejected by the gateway.";
                                $msg = "Your payment has not been successfully completed because transaction is rejected by the gateway.<br/>";
                                $msg .= "We are very sorry for the inconvenience caused.<br/>";
                                $msg .= "The status what we have got from the server is stated below<br/>";
                                $msg .= $result->subscription->transactions[0]->gatewayRejectionReason;
                                $msg .= "We are very sorry for the inconvenience caused.<br/>";
                                $msg .= "Please try again later. Thank you.<br/>";
                                $msg .= "Best regards<br/>movinngloadsusa admin";
                                wp_mail( $user_email, $subject, $msg, $headers );
                                $display_msg .= "<p>Your payment has not been successfully completed because transaction is rejected by the gateway.</p>";
                                $display_msg .= "<p>We are very sorry for the inconvenience caused.<p>";
                                $display_msg .= "<p>Status Message:".$result->subscription->transactions[0]->gatewayRejectionReason."</p>";
                                $display_msg .= "<p><a href='".site_url()."/my-account/'>Click here</a> to visit your account.<p>";
                                break;
                            default:
                                break;
                        }
                    }
                    else{
                        $display_msg .= "<p>Validation errors occured</p>";
                        $display_msg .= "<p>$result->message<p>";
                        $display_msg .= "<p><a href='".site_url()."/my-account/'>Click here</a> to visit your account.<p>";
                    }
                    $display_msg .= "<p>Validation errors occured</p>";
                    $display_msg .= "<p>$result->message<p>";
                    $display_msg .= "<p><a href='".site_url()."/my-account/'>Click here</a> to visit your account.<p>";
                    $_SESSION['status'] = $display_msg;
                    unset($_POST);
                    wp_safe_redirect(site_url().'/payment-result');
                    exit();
                }
            }
            else{
                $display_msg .= "<p>Validation errors occured</p>";
                $display_msg .= "<p>$result->message<p>";
                $display_msg .= "<p><a href='".site_url()."/my-account/'>Click here</a> to visit your account.<p>";
                $_SESSION['status'] = $display_msg;
                unset($_POST);
                wp_safe_redirect(site_url().'/payment-result');
                exit();
            }
        }
        else{
            $display_msg .= "<p>Validation errors occured</p>";
            $display_msg .= "<p>$result->message<p>";
            $display_msg .= "<p><a href='".site_url()."/my-account/'>Click here</a> to visit your account.<p>";
            $_SESSION['status'] = $display_msg;
            unset($_POST);
            wp_safe_redirect(site_url().'/payment-result');
            exit();
        }
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
                                <input type="text" placeholder="MM" maxlength="2" autocomplete="off" name="exp_month" class="post_loads_txtfld_002"/> / 
                                <input type="text" placeholder="YYYY" maxlength="4" autocomplete="off" name="exp_year" class="post_loads_txtfld_002"/>
                            </p>
                            <p>
                                <label>CVV:</label>
                                <input name="cvv_no" type="text" autocomplete="off" class="registration_txtfld" value="">
                            </p>
                        </div>
                    </div>
                    <div class="save_btn">
                        <input type="hidden" name="price" value="<?php echo $_POST['amount'];?>">
                        <input type="hidden" name="currency_code" value="USD">
                        <input type="hidden" name="plan_id" value="<?php echo $_POST['plan_id'];?>">
                        <input type="hidden" name="plan_name" value="<?php echo $_POST['plan_name'];?>">
                        <input type="hidden" name="user_ID" value="<?php echo $_POST['custom'];?>">
                        <input name="pay_with_braintree" type="submit" value="Pay Now" class="btn_3" id="sub_btn" >
                    </div>
            </form>
        </div>
    </div>
</div>
<!--cnt_div-end-->
</div>
<?php get_footer(); ?>