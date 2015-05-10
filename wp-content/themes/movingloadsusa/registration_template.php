<?php
session_start();
/* Template Name: Registration */
require_once(ABSPATH . WPINC . '/user.php');
global $wpdb, $user_ID;
if($user_ID){
    wp_safe_redirect(home_url().'/dashboard/');
    exit();
}
$err_msg = '';
$suc_msg = '';
$war_msg = '';
if (!$user_ID) {
    if(isset($_POST['submit'])){     
        $email = esc_sql($_POST['email']);
        $password = esc_sql($_POST['password']);		
        $company_name = esc_sql($_POST['company_name']);
        $dot = esc_sql($_POST['dot']);
        $dot_exists = FALSE;
        $wpdb->get_results("SELECT * FROM `wp_usermeta` WHERE `meta_key` = 'dot' AND `meta_value` = '$dot'");
        if($wpdb->num_rows > 0){
            $dot_exists = TRUE;
        }
        $mc = esc_sql($_POST['mc']);
        $company_phone = preg_replace("/[^0-9]/","",esc_sql($_POST['company_phone']));
        $address = esc_sql($_POST['address']);
        $city = esc_sql($_POST['city']);
        $state = esc_sql($_POST['state']);
        $zip = esc_sql($_POST['zip']);
        $first_name = esc_sql($_POST['first_name']);
        $username = $first_name.time();       
        $last_name = esc_sql($_POST['last_name']);
        $phone = preg_replace("/[^0-9]/","",esc_sql($_POST['phone']));
        $con_pass = esc_sql($_POST['con_password']);	
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
//        else if(empty($mc)) { 
//            $err_msg = "Please enter mc.<br/>";
//        }
        else if(!empty ($mc) && !is_numeric($mc)) { 
            $err_msg = "MC# must be numeric.<br/>";
        }
        else if(!empty ($mc) && strlen($mc)!=6) { 
            $err_msg = "MC# number must be 6 digit.<br/>";
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
        else if (empty($password)) {
             $err_msg = "Password should not be blank.<br/>";    
        }
        else if (strlen($password) < 6) {
             $err_msg = "Password must be at least six characters long.<br/>";    
        }
        else if ($password!=$con_pass) {
             $err_msg = "Password does not match.<br/>";    
        }
        else {
            if( $_SESSION['security_code'] == $_POST['security_code'] && !empty($_SESSION['security_code'] ) ){
                $userinfo = array('user_login'=>$username,'user_pass'=>$password,'user_email'=>$email);
                $status = wp_insert_user($userinfo);
                if ( is_wp_error($status) ) {
                    $war_msg .= 'Email Address already exists. Please try another one.';
                }
                if($war_msg == ''){
                    $userdata = array(
                        'ID' => $status,
                        'user_pass' => $password,
                        'display_name' => $first_name
                    );
                    $new_user_id = wp_update_user( $userdata );
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
                    $subject = "Thank you for registration";
                    $msg = "Thank you for registration.<br/>Your login details<br/>Username: $email<br/>Password: Your choosen password.<br/>";
                    if(isset($set_key) && $set_key == 1){
                        $msg .= "<a href='".site_url()."/verify-email/?verify_id=$new_user_id&activation_key=$act_key'>Click here for verify your email</a><br/><br/>";
                    }else{
                        $msg .= "<a href='".site_url()."/verify-email/?verify_id=$new_user_id'>Click here for verify your email</a><br/><br/>";
                    }
                    $msg .= "Best regards<br/>movinngloadsusa admin";
                    wp_mail( $email, $subject, $msg, $headers );
                    foreach ($_POST as $key => $post) {
                        if($_POST['email'] == $post || $_POST['username'] == $post || $_POST['password'] == $post || $_POST['submit'] == $post || $_REQUEST['tc'] == $post){
                            continue;
                        }
                        else if($_POST['company_phone'] == $post || $_POST['phone'] == $post){
                            update_user_meta($new_user_id, $key, format_phone(preg_replace("/[^0-9]/","",esc_sql($post))));
                        }
                        else{
                            update_user_meta($new_user_id, $key, esc_sql($post));
                        }
                    }
                    $suc_msg .= 'Registration is successfully completed. Please check your mail.';
                    $username = '';
                    $email = '';
                    $password = '';		
                    $company_name= '';
                    $dot='';
                    $mc='';
                    $company_phone='';
                    $address='';
                    $city='';
                    $state='';
                    $zip='';
                    $first_name='';
                    $last_name='';
                    $phone='';
                    $hear_about_us = '';
                }
        }else{
            $err_msg = "Invalid secuirity code.<br/>";
        }
      }
      
    }
    
}
get_header();
?>
    <div class="ctn">
        <!--contain-->
        <div class="contain_main">
            <div class="ctn_in">
            <h1>REGISTRATION INFO</h1>
                <div class="registration_main">
                    <div class="registration_main_bg">
                        <h2>TRY US FREE FOR 30 DAYS!</h2>
                        <div class="registration_main_in">
                            <p id="err_msgs" class="<?php if(!empty($err_msg)){echo 'err_msg';}else if(!empty($war_msg)){echo 'war_msg';}else{echo 'suc_msg';}?>">
                               <?php
                                    if(!empty($err_msg)){echo $err_msg;}
                                    else if(!empty($war_msg)){echo $war_msg;}
                                    else{echo $suc_msg;}
                               ?>
                            </p>
                            <form action="<?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" method="POST" name="form4" id="form4">
                                <p><label>Company Name:<strong style="color: red;">*</strong></label><input name="company_name" type="text" class="registration_txtfld" value="<?php if(isset($company_name)){ echo $company_name;}?>"></p>
                                <p><label>DOT #:<strong style="color: red;">*</strong></label><input name="dot" type="text" maxlength="7" class="registration_txtfld" value="<?php if(isset($dot)){ echo $dot;}?>"></p>
                                <p><label>MC #:</label><input name="mc" type="text" class="registration_txtfld" maxlength="6" value="<?php if(isset($mc)){ echo $mc;}?>" ></p>
                                <p><label>Company Phone:<strong style="color: red;">*</strong></label><input maxlength="15" autocomplete="off" name="company_phone" id="company_phone" type="text" class="registration_txtfld" value="<?php if(isset($company_phone)){ echo format_phone($company_phone);}?>"></p>
                                <p><label>Address:<strong style="color: red;">*</strong></label><input name="address" type="text" class="registration_txtfld" value="<?php if(isset($address)){ echo $address;}?>"></p>
                                <p><label>City:<strong style="color: red;">*</strong></label><input name="city" type="text" class="registration_txtfld" value="<?php if(isset($city)){ echo $city;}?>"></p>
                                <p><label>State:<strong style="color: red;">*</strong></label>									
                                    <select name="state" class="registration_selectbox"> 
                                        <?php
                                            $myrows = $wpdb->get_results( "SELECT * from wp_statemaster" );
                                        ?>
                                        <option value="0" <?php echo (isset($state) && $state == 0)?'selected="selected"':'';?>>Select a state</option>
                                        <?php
                                            foreach($myrows as $state_name){?>
                                            <option value="<?php echo $state_name->state_short_name; ?>" <?php echo (isset($state) && $state == $state_name->state_short_name)?'selected="selected"':'';?>><?php echo $state_name->state_full_name; ?></option>
                                        <?php } ?>	
                                    </select>	
                                </p>
                                <p><label class="zip_width">Zip:<strong style="color: red;">*</strong></label><input name="zip" type="text" class="registration_txtfld2" value="<?php if(isset($zip)){ echo $zip;}?>"></p>
                                <p><label>Contact First Name:<strong style="color: red;">*</strong></label><input name="first_name" type="text" class="registration_txtfld" value="<?php if(isset($first_name)){ echo $first_name;}?>"></p>
                                <p><label>Contact Last Name:<strong style="color: red;">*</strong></label><input name="last_name" type="text" class="registration_txtfld" value="<?php if(isset($last_name)){ echo $last_name;}?>"></p>
                                <p><label>Phone:<strong style="color: red;">*</strong></label><input maxlength="15" autocomplete="off" id="phone" name="phone"  type="text" class="registration_txtfld" value="<?php if(isset($phone)){ echo format_phone($phone);}?>"></p>
                                <p><label>Email:<strong style="color: red;">*</strong></label><input name="email" type="email" class="registration_txtfld" value="<?php if(isset($email)){ echo $email;}?>"></p>
                                <div class="user_div">PASSWORD</div>
                                <p><label>Password:<strong style="color: red;">*</strong></label><input name="password" type="password" class="registration_txtfld" ></p>
                                <p><label>Confirm Password:<strong style="color: red;">*</strong></label><input name="con_password" type="password" class="registration_txtfld"></p>
                                  <div class="terms_conditions_div">
                                    <p>
                                        <label></label>
                                        <img src="<?php echo get_template_directory_uri();?>/captcha/CaptchaSecurityImages.php?width=100&height=40&characters=5" />
                                    </p>
                                    <p>
                                        <label></label>
                                        <input id="security_code" placeholder="Enter the code above" name="security_code" type="text" class="registration_txtfld" value=""/>
                                    </p>
                                </div>
                                <div class="terms_conditions_div">
                                    <p>How did You Hear About Us?</p>
                                    <p>
                                        <select name="hear_about_us" class="registration_selectbox2">
                                            <option value="Google" <?php echo (isset($hear_about_us) && $hear_about_us == 'Google')?'selected="selected"':'';?>>Google</option>
                                            <option value="Online - Other" <?php echo (isset($hear_about_us) && $hear_about_us == 'Online - Other')?'selected="selected"':'';?>>Online - Other</option>
                                            <option value="Newspaper" <?php echo (isset($hear_about_us) && $hear_about_us == 'Newspaper')?'selected="selected"':'';?>>Newspaper</option>
                                            <option value="Magazine" <?php echo (isset($hear_about_us) && $hear_about_us == 'Magazine')?'selected="selected"':'';?>>Magazine</option>
                                            <option value="Radio" <?php echo (isset($hear_about_us) && $hear_about_us == 'Radio')?'selected="selected"':'';?>>Radio</option>
                                            <option value="TV" <?php echo (isset($hear_about_us) && $hear_about_us == 'TV')?'selected="selected"':'';?>>TV</option>
                                            <option value="Word Of Mouth" <?php echo (isset($hear_about_us) && $hear_about_us == 'Word Of Mouth')?'selected="selected"':'';?>>Word Of Mouth</option>
                                            <option value="Drove By" <?php echo (isset($hear_about_us) && $hear_about_us == 'Drove By')?'selected="selected"':'';?>>Drove By</option>
                                            <option value="Was Previously Here at an Event" <?php echo (isset($hear_about_us) && $hear_about_us == 'Was Previously Here at an Event')?'selected="selected"':'';?>>Was Previously Here at an Event</option>
                                            <option value="Other" <?php echo (isset($hear_about_us) && $hear_about_us == 'Other')?'selected="selected"':'';?>>Other</option>
                                        </select>
                                    </p>
                                    <p><span>Terms & Conditions:</span></p>
                                    <p><input name="tc" type="checkbox" value="on" class="check_1" required><label>I have read and agree to the terms and conditions above</label></p>
                                </div>
                                <p class="btn_reg_main">
                                    <input name="clear" type="reset" value="Clear" class="regis_btn">
                                    <input name="submit" type="submit" value="Submit" class="regis_btn">
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