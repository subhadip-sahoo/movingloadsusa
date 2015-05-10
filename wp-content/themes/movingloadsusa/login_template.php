<?php
/* Template Name: Login */
global $wpdb,$user_id;
if($user_ID){
    wp_safe_redirect(home_url());
    exit();
}
$err_msg = '';
$war_msg = '';
if(isset($_POST['login'])){
    $user_login = esc_sql($_POST['user_login']);
    $user_pass = esc_sql($_POST['user_pass']);
    if(isset($_POST['rememberme']) && $_POST['rememberme'] == 'on'){
        $remember = TRUE;
    }
    else{
        $remember = FALSE;
    }
    if(empty($user_login)) { 
        $err_msg .= "User name should not be empty.<br/>";
    } 
    else if(empty( $user_pass)) { 
        $err_msg .= "Please enter a password.<br/>";
    }
    else {
        $creds = array();
        $creds['user_login'] =  $user_login;
        $creds['user_password'] =  $user_pass;
        $creds['remember'] =  $remember;
        $user = wp_signon( $creds, true);
        if ( is_wp_error($user) ) {
            if(isset($user->errors['incorrect_password'])){
                $war_msg .= "Wrong Username or Password";
            }
            else if(isset($user->errors['account_expired'])){
                //$war_msg .= $user->errors['account_expired'][0];
                $user_login = $user->errors['user_login'][0];
                $user_email = $user->errors['user_email'][0];
                $user_activation_key = $user->errors['user_activation_key'][0];
                if($user_activation_key == ''){
                    header("location:".site_url()."/account-expired/?action=subscription&user_login=$user_login&user_email=$user_email");
                    exit();
                }else{
                    header("location:".site_url()."/account-expired/?action=subscription&user_login=$user_login&user_email=$user_email&key=$user_activation_key");
                    exit();
                }
            }
            else if(isset($user->errors['verification_failed'])){
                $war_msg .= $user->errors['verification_failed'][0];
            }
			else{
                $war_msg .= "Wrong Username or Password.";
            }
        }
        else {
            wp_safe_redirect(home_url().'/dashboard');
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
            	<h1>LOGIN</h1>
                    <div class="registration_main">
                        <div class="registration_main_bg">
                            <h2>EMAIL / PASSWORD</h2>
                            <div class="registration_main_in">
                                <p id="err_msgs" class="<?php if(!empty($err_msg)){echo 'err_msg';}else if(!empty($war_msg)){echo 'war_msg';}else{echo 'suc_msg';}?>">
                                    <?php
                                        if(!empty($err_msg)){echo $err_msg;}
                                        else if(!empty($war_msg)){echo $war_msg;}
                                        else{echo $suc_msg;}
                                    ?>
                                </p>
                            	<form action="" method="POST">
                                    <p><label>Email</label><input name="user_login" type="text" class="registration_txtfld"></p>
                                    <p><label>Password</label><input name="user_pass" type="password" class="registration_txtfld"></p>
                                    
                                    <p class="btn_login_main">
                                    	<input name="login" type="submit" value="Login" class="regis_btn">
                                        <span><input name="rememberme" type="checkbox" value="on" class="check_2"> Remember me</span>
                                        <div class="btn_login_main_for login_page_reponsive_cls"><a href="<?php echo home_url()?>/registration">Register</a> | <a href="<?php echo home_url()?>/forgot-password">Forgot Password</a></div>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--cnt_div-end-->
            </div>
        </div>
    </div>
<?php get_footer(); ?>