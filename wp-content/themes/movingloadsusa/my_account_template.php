<?php
/* Template Name: My account */
$err_msg = '';
$war_msg = '';
$suc_msg = '';
global $wpdb ;$user_id= get_current_user_id();
$current_user=wp_get_current_user();
if(!$user_id){
    wp_safe_redirect(home_url());
    exit();
}
if(isset($_POST['update'])){
    if(isset($_POST['company_name']) && empty($_POST['company_name'])){
        $err_msg .= 'Company name required.<br/>';
    }
    else if(isset($_POST['dot']) && empty($_POST['dot'])){
        $err_msg .= 'DOT# required.<br/>';
    }
    else if(isset($_POST['dot']) && !is_numeric($_POST['dot'])){
        $err_msg .= 'DOT# must be numeric.<br/>';
    }
    else if(isset($_POST['dot']) && !is_dot_exists($_POST['dot'])){
        $err_msg .= 'There is an another record with the same DOT. Please try another.<br/>';
    }
    else if(isset($_POST['dot']) && strlen($_POST['dot'])!= 7){
        $err_msg .= 'Invalid DOT#. It must be 7 digits.';
    }
//     else if(isset($_POST['mc']) && empty($_POST['mc'])){
//        $err_msg .= 'MC# required.<br/>';
//    }
    else if(isset($_POST['mc']) && !empty ($_POST['mc']) && !is_numeric($_POST['mc'])){
        $err_msg .= 'MC# must be numeric.<br/>';
    } 
    else if(isset($_POST['mc']) && !empty ($_POST['mc']) && strlen($_POST['mc'])!= 6){
        $err_msg .= 'Invalid MC#. It must be 6 digits.';
    }
    else if(isset($_POST['company_phone']) && strlen(preg_replace("/[^0-9]/","",$_POST['company_phone'])) < 10 || strlen(preg_replace("/[^0-9]/","",$_POST['company_phone'])) > 11) { 
        $err_msg = "Company phone number should be 10-11 digits.<br/>";
    }
    else if(isset($_POST['company_phone']) && !is_numeric(preg_replace("/[^0-9]/","",$_POST['company_phone']))) { 
        $err_msg = "Company phone number should be numeric.<br/>";
    }
    else if(isset($_POST['address']) && empty($_POST['address'])){
        $err_msg .= 'Address required.<br/>';
    }
    else if(isset($_POST['city']) && empty($_POST['city'])){
        $err_msg .= 'City required.<br/>';
    }
    else if(isset($_POST['state']) && $_POST['state'] == '0'){
        $err_msg .= 'State required.<br/>';
    }
    else if(isset($_POST['zip']) && empty($_POST['zip'])){
        $err_msg .= 'Zip code required.<br/>';
    }
    else if(isset($_POST['first_name']) && empty($_POST['first_name'])){
        $err_msg .= 'Contact first name required.<br/>';
    }
    else if(isset($_POST['last_name']) && empty($_POST['last_name'])){
        $err_msg .= 'Contact last name required.<br/>';
    }
    else if(isset($_POST['phone']) && strlen(preg_replace("/[^0-9]/","",$_POST['phone'])) < 10 || strlen(preg_replace("/[^0-9]/","",$_POST['phone'])) > 11) { 
        $err_msg = "Phone number should be 10-11 digits.<br/>";
    }
    else if(isset($_POST['phone']) && !is_numeric(preg_replace("/[^0-9]/","",$_POST['phone']))) { 
        $err_msg = "Phone number should be numeric.<br/>";
    }
    else if(isset($_POST['user_pass']) && empty($_POST['user_pass'])){
        $err_msg .= 'Please enter a password';
    }
    else if (isset($_POST['user_pass']) && strlen($_POST['user_pass']) < 6) {
        $err_msg = "Password must be at least six characters long.<br/>";    
    }
    else if(isset($_POST['user_pass']) && !isset($_POST['confirm_user_pass']) || $_POST['user_pass'] != $_POST['confirm_user_pass']){
        $err_msg .= 'Password does not match';
    }
    if($err_msg == ''){	
        if(isset($_POST['user_pass'])){
            $userdata = array(
                'ID' => $user_id,
                'user_pass' => esc_sql($_POST['user_pass'])
            );
            wp_update_user( $userdata );
        }
        foreach ($_POST as $key => $post) {
            if(isset($_POST['user_pass']) && $_POST['user_pass'] == $post){
                continue;
            }
            else if($_POST['update'] == $post){
                continue;
            }
            else if($_POST['company_phone'] == $post || $_POST['phone'] == $post){
                update_user_meta($user_id, $key, format_phone(esc_sql($post)));
            }
            else{
                update_user_meta($user_id, $key, $post);
            }
        }
        $suc_msg .= "Successfully updated your account";
    }
}
get_header();			 
?>
<script type='text/javascript'>
$(function(){
     $('#personal_info_edit').click(function(){	 
        $('#personal_info :not(input[name=email]), #personal_info select').prop('disabled', false);  
        $('#sub_btn').prop('disabled', false);  
    });
    $('#credit_card_info_edit').click(function(){	 
        $('#credit_card_info input, #credit_card_info select').prop('disabled', false);  
        $('#sub_btn').prop('disabled', false);  
    });
    $('#account_info_edit').click(function(){	 
        $('#account_info :not(input[name=user_login])').prop('disabled', false);  
        $('#sub_btn').prop('disabled', false);  
    });
});
</script>
     <div class="ctn">
            <!--contain-->
            <div class="contain_main">
            	<div class="ctn_in">
            	<h1>MY ACCOUNT</h1>
                    <div class="registration_main">
                        <form action="" method="POST" name="form4" id="form4">														
                        <div class="registration_main_bg" id="personal_info">
                            <h2>PERSONAL INFO</h2>
                            <span><a href="javascript:void(0);" id="personal_info_edit" title="Edit"><img src="<?php echo get_template_directory_uri();?>/images/edit_icon_2.png" alt=""></a></span>
                            <div class="registration_main_in">
                                <p id="err_msgs" class="<?php if(!empty($err_msg)){echo 'err_msg';}else if(!empty($war_msg)){echo 'war_msg';}else{echo 'suc_msg';}?>">
                                   <?php
                                           if(!empty($err_msg)){echo $err_msg;}
                                           else if(!empty($war_msg)){echo $war_msg;}
                                           else{echo $suc_msg;}
                                   ?>
                                </p>
                                <p><label>Company Name:<strong style="color: red;">*</strong></label><input name="company_name" type="text" class="registration_txtfld" value="<?php echo $current_user->company_name;?>" disabled></p>
                                <p><label>DOT #:<strong style="color: red;">*</strong></label><input name="dot" type="text" maxlength="7" class="registration_txtfld" value="<?php echo $current_user->dot;?>" disabled></p>
                                <p><label>MC #:</label><input name="mc" type="text" maxlength="6" class="registration_txtfld" value="<?php echo $current_user->mc;?>" disabled></p>
                                <p><label>Company Phone:<strong style="color: red;">*</strong></label><input maxlength="15" autocomplete="off" name="company_phone" type="text" class="registration_txtfld" value="<?php echo $current_user->company_phone;?>" disabled autocomplete="off"></p>
                                <p><label>Address:<strong style="color: red;">*</strong></label><input name="address" type="text" class="registration_txtfld" value="<?php echo $current_user->address;?>" disabled></p>
                                <p><label>City:<strong style="color: red;">*</strong></label><input name="city" type="text" class="registration_txtfld" value="<?php echo $current_user->city;?>" disabled></p>
                                <p><label>State:<strong style="color: red;">*</strong></label>
                                    <select name="state" class="registration_selectbox" disabled>
                                        <option value="0">Select State</option>
                                        <?php										
                                            $myrows = $wpdb->get_results( "SELECT * from wp_statemaster" );
                                            foreach($myrows as $state_name){ ?>
                                            <option <?php if(($current_user->state)==$state_name->state_short_name){echo "selected='selected'";}?> value="<?php echo $state_name->state_short_name; ?>"><?php echo $state_name->state_full_name; ?></option>
                                        <?php } ?>	                                          
                                    </select>
                                </p>
                                    <p><label class="zip_width">Zip:<strong style="color: red;">*</strong></label><input name="zip" type="text" class="registration_txtfld2" value="<?php echo $current_user->zip;?>" disabled></p>
                                    <p><label>Contact First Name:<strong style="color: red;">*</strong></label><input name="first_name" type="text" class="registration_txtfld" value="<?php echo $current_user->first_name;?>" disabled></p>
                                    <p><label>Contact Last Name:<strong style="color: red;">*</strong></label><input name="last_name" type="text" class="registration_txtfld" value="<?php echo $current_user->last_name;?>" disabled></p>
                                    <p><label>Phone:<strong style="color: red;">*</strong></label><input maxlength="15" autocomplete="off" name="phone" type="text" class="registration_txtfld" value="<?php echo $current_user->phone;?>" disabled autocomplete="off"></p>
                                    <p><label>Email:<strong style="color: red;">*</strong></label><input name="email" type="email" class="registration_txtfld" value="<?php echo $current_user->user_email; ?>" disabled></p>
                            </div>
                        </div>
                        <div class="registration_main_bg" id="credit_card_info">
                            <h2>CREDIT CARD INFO</h2>
                            <span><a href="javascript:void(0);" id="credit_card_info_edit" title="Edit"><img src="<?php echo get_template_directory_uri();?>/images/edit_icon_2.png" alt=""></a></span>
                            <div class="registration_main_in">
                                <p><label>Credit Card</label>
                                <select name="credit_card" id="credit_card" class="registration_selectbox_3" disabled>
                                    <option value="Mastercard" <?php if($current_user->credit_card == 'Mastercard'){echo 'selected="selected"';}?>>Mastercard</option>
                                    <option value="Visa" <?php if($current_user->credit_card == 'Visa'){echo 'selected="selected"';}?>>Visa</option>
                                </select>
                                </p>
                                <p><label>Card #</label><input name="credit_card_no" id="credit_card_no" type="text" value="<?php echo $current_user->credit_card_no;?>" class="registration_txtfld" disabled></p>
                                <p><label>Exp Date</label>
                                    <select name="expiry_month" id="expiry_month" class="registration_selectbox_4" disabled>
                                        <?php 
                                            $months = array('January','February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
                                        ?>
                                            <option value="0">Select month</option>
                                        <?php foreach($months as $month){?>                                        	
                                            <option value="<?php echo $month;?>" <?php if($current_user->expiry_month == $month){echo 'selected="selected"';}?>><?php echo $month;?></option>
                                        <?php }?>
                                    </select>
                                    <select name="expiry_year" id="expiry_year" class="registration_selectbox_4" disabled>
                                            <option value="0">Select year</option>
                                        <?php for($i=2014;$i<=2050;$i++){?>                                        	
                                            <option value="<?php echo $i;?>" <?php if($current_user->expiry_year == $i){echo 'selected="selected"';}?>><?php echo $i;?></option>
                                        <?php }?>
                                    </select>
                                </p>
                                <p><label>CID:</label><input name="cid" type="text" class="registration_txtfld" value="<?php echo $current_user->cid;?>" disabled></p>
                            </div>
                        </div>
                        
                        <div class="registration_main_bg" id="account_info">
                            <h2>CHANGE PASSWORD</h2>
                            <span><a href="javascript:void(0);" id="account_info_edit" title="Edit"><img src="<?php echo get_template_directory_uri();?>/images/edit_icon_2.png" alt=""></a></span>
                            <div class="registration_main_in">
                                <p><label>New Password</label><input name="user_pass" type="password" class="registration_txtfld" disabled></p>
                                <p><label>Confirm Password</label><input name="confirm_user_pass" type="password" class="registration_txtfld" disabled></p>
                            </div>
                        </div>
                            <div class="registration_main_bg" id="account_info">
                                <?php  
                                $post_count_query=$wpdb->get_results("SELECT  SUM(post_count) as posts_count FROM  wp_user_postaccount WHERE  id_user =$user_id");
                                if( !empty($post_count_query)){
                                    foreach ( $post_count_query as $post_left){
                                     $post_left_count = $post_left->posts_count;                      
                                    }
                                }                               
                                ?>
                                    <h2>ACCOUNT INFO</h2>                          
                                    <div class="registration_main_in">
                                         <?php if(!is_super_admin( $user_id )){ ?> 
                                        <p><label>Account Expiry Date</label><input type="text" value="<?php echo date('m-d-Y',strtotime($current_user->account_expiry));  ?>" disabled class="post_loads_txtfld_01">                                            
                                             <input name="submit_career" type="button" value="Extend Account" class="addbutton" onclick="window.location.href='<?php echo site_url();?>/extend-account'">                                        
                                        </p>                                                                        
                                          
                                         <?php } ?>
                                       
                                        <p><label>Available Posts</label><input type="text" value="<?php echo (isset($post_left_count) && $post_left_count > 0)? $post_left_count:0; ?>" disabled class="post_loads_txtfld_01">                                            
                                            <input name="submit_career" type="button" value="Buy More Posts" class="addbutton" onclick="window.location.href='<?php echo site_url();?>/post-package'">                                        
                                        </p>                                                                        
                                        
                                    </div>
                                </div>
                         
                        <div class="save_btn">
                            <input name="update" type="submit" value="SAVE" class="btn_3" id="sub_btn" disabled>
                        </div>
                    </form>
                </div>
            </div>
        </div>
            <!--cnt_div-end-->
    </div>
<?php get_footer(); ?>