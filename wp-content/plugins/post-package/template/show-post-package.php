<?php
    if(!is_user_logged_in()){
        wp_safe_redirect(home_url().'/login/');
        exit();
    }
    global $wpdb,$table_name, $user_ID;
    $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    if($wpdb->num_rows > 0){
?>
	<div class="pack_div">
<?php
        foreach ($results as $result) { 
?>           
            <p><label>Package Name:</label> <?php echo $result['name'];?></p>
            <p><label>Total Posts:</label> <?php echo $result['total_posts'];?></p>
            <p><label>Validity Per Post(In days):</label> <?php echo $result['expiry_per_post'];?></p>
            <p><label>Validity of Package(In days):</label> <?php echo $result['expiry_package'];?></p>
            <p><label>Price of Package:</label> $<?php echo $result['package_price'];?></p>
            <?php $action = (mlusa_get_option('paypal_environment') == 'sandbox')?'https://www.sandbox.paypal.com/cgi-bin/webscr':'https://www.paypal.com/cgi-bin/webscr'; ?>
            <form name="posts_packages" id="posts_packages" action="<?php echo $action;?>" method="post">
                <input type="hidden" name="business" value="<?php echo mlusa_get_option('paypal_email_address');?>">
                <!-- Specify a Buy Now button. -->
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="return" value="<?php echo home_url().'/dashboard/';?>">
                <input type="hidden" name="cancel_return" value="<?php echo home_url().'/dashboard/';?>">
                <input type="hidden" name="notify_url" value="<?php echo get_template_directory_uri();?>/paypal/ipn_listner.php">
                <!-- Specify details about the item that buyers will purchase. -->
                <input type="hidden" name="item_name" value="<?php echo $result['name'];?>">
                <input type="hidden" name="amount" value="<?php echo $result['package_price'];?>">
                <input type="hidden" name="currency_code" value="USD">
                <input type="hidden" name="custom" value="<?php echo $user_ID;?>">
                <input type="hidden" name="item_number" value="<?php echo $result['id_post_package'];?>">

                <!-- Prompt buyers to enter their desired quantities. -->
                <input type="hidden" name="undefined_quantity" value="1">

                <!-- Display the payment button. -->
                <input type="submit" name="paypal_payment" value="Pay With PayPal" class="btn_1"/>
            </form>
            <form name="pay_with_braintree" id="pay_with_braintree" action="<?php echo site_url();?>/braintree-payment" method="POST">
                <input type="hidden" name="item_name" value="<?php echo $result['name'];?>">
                <input type="hidden" name="amount" value="<?php echo $result['package_price'];?>">
                <input type="hidden" name="currency_code" value="USD">
                <input type="hidden" name="item_number" value="<?php echo $result['id_post_package'];?>">
                <input type="hidden" name="undefined_quantity" value="1">
                <input type="submit" name="braintree_payment" value="Pay With Braintree" class="btn_1"/>
            </form>
<?php          
        }
    }
?>
</div>