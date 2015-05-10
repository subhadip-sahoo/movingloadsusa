<?php
/* Template Name: Verify Email */
global $wpdb, $user_ID;
    if($user_ID){
        wp_safe_redirect(site_url());
        exit();
    }
    if(!isset($_REQUEST['verify_id'])){
        wp_redirect(site_url());
        exit();
    }
    if(get_user_meta($_REQUEST['verify_id'], 'account_status', true) == 1){
        wp_redirect(site_url());
        exit();
    }
    if(isset($_REQUEST['activation_key'])){
        $keys = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE ID = ".$_REQUEST['verify_id'], ARRAY_A);
        $trial_period = mlusa_get_option('trail_period');
        if($wpdb->num_rows == 1){
            foreach ($keys as $key) {
                if($key['user_activation_key'] == $_REQUEST['activation_key']){
                    if(get_user_meta($_REQUEST['verify_id'], 'account_status', true) == 0){
                        update_user_meta($_REQUEST['verify_id'], 'account_status', 1);
                        add_user_meta($_REQUEST['verify_id'], 'account_expiry', date('Y-m-d H:i:s', strtotime("+$trial_period day")));
                    }else{
                        wp_redirect(site_url());
                        exit();
                    }
                }else{
                    wp_redirect(site_url());
                    exit();
                }
            }
        }else{
            wp_redirect(site_url());
            exit();
        }
    }
    else{
        if(get_user_meta($_REQUEST['verify_id'], 'account_status', true) == 0){
            update_user_meta($_REQUEST['verify_id'], 'account_status', 1);
        }else{
            wp_redirect(site_url());
            exit();
        }
    }
get_header(); ?>
	 <div class="contain_main">
            	<div class="ctn_in">
                    <h1>Email Verification</h1>
                    <div class="clear"></div>				
                    <p>Your Email has been verified successfully. Please click <a href="<?php echo site_url();?>/login/">here</a> to login.</p>
		</div>
	</div>
<?php get_footer(); ?>