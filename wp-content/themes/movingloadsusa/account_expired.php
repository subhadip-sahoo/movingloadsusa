<?php
/* Template Name: Account Expired */
global $wpdb;
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'subscription'){
    $results = $wpdb->get_results("SELECT * FROM wp_users WHERE user_login = '".$_REQUEST['user_login']."' AND user_email = '".$_REQUEST['user_email']."'");
    if($wpdb->num_rows == 1) {
        foreach ($results as $result) {
            $user_id = $result->ID;
            $user_activation_key = $result->user_activation_key;
        }
    }
    else{
        wp_safe_redirect(home_url().'/login/');
        exit();
    }
    if(isset($_REQUEST['key'])){
        if($_REQUEST['key'] != $user_activation_key){
            wp_safe_redirect(home_url().'/login/');
            exit();
        }
    }   
}
else{
    wp_safe_redirect(home_url().'/login/');
    exit();
}
get_header();?>
	 <div class="contain_main">
            <div class="ctn_in">
                <h1><?php the_title();?></h1>
                <div class="clear"></div>				
                <p>Your account has been expired. Please subscribe through PayPal.</p>
                <p>Be a member.</p>
                <?php $action = (mlusa_get_option('paypal_environment') == 'sandbox')?'https://www.sandbox.paypal.com/cgi-bin/webscr':'https://www.paypal.com/cgi-bin/webscr'; ?>
                <form action="<?php echo $action;?>" method="post">
                    <input type="hidden" name="business" value="<?php echo mlusa_get_option('paypal_email_address');?>">
                    <input type="hidden" name="cmd" value="_xclick-subscriptions">
                    <input type="hidden" name="item_name" value="Membership">
                    <input type="hidden" name="return" value="<?php echo home_url().'/login/';?>">
                    <input type="hidden" name="cancel_return" value="<?php echo home_url().'/account-expired/';?>">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="notify_url" value="<?php echo get_template_directory_uri();?>/paypal/ipn_listner.php">
                    <input type="hidden" name="a3" value="<?php echo mlusa_get_option('paypal_subscription_amount');?>">
                    <input type="hidden" name="p3" value="<?php echo mlusa_get_option('paypal_subscription_period_no');?>">
                    <input type="hidden" name="t3" value="<?php echo mlusa_get_option('paypal_subscription_period_days');?>">
                    <input type="hidden" name="src" value="1">
                    <input type="hidden" name="custom" value="<?php echo $user_id;?>">
                    <input type="submit" name="paypal_payment" value="Subscribe With PayPal" class="btn_1"/>
                </form>
                <form name="pay_with_braintree" id="pay_with_braintree" action="<?php echo site_url();?>/braintree-subscription" method="POST">
                    <input type="hidden" name="plan_id" value="<?php echo mlusa_get_option('braintree_plan_ID');?>">
                    <input type="hidden" name="plan_name" value="<?php echo mlusa_get_option('braintree_plan_name');?>">
                    <input type="hidden" name="amount" value="<?php echo mlusa_get_option('paypal_subscription_amount');?>">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="custom" value="<?php echo $user_id;?>">
                    <input type="submit" name="braintree_payment" value="Subscribe With Braintree" class="btn_1"/>
                </form>
            </div>
	</div>
<?php get_footer(); ?>