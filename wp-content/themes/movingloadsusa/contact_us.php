<?php
/* Template Name:Contact Us */
$err_msg = '';
$suc_msg = '';
$war_msg = '';
if(isset($_POST['contact'])){
    if(empty($_POST['cus_name'])){
        $err_msg .= 'Name should not be blanked.';
    }
    else if(empty($_POST['cus_email'])){
        $err_msg .= 'Email should not be blanked.';
    }
    else if(filter_var($_POST['cus_email'], FILTER_VALIDATE_EMAIL) == FALSE){
        $err_msg .= 'Enter a valid email.';
    }
    else if(empty($_POST['cus_phone'])){
        $err_msg .= 'Phone no should not be blanked.';
    }
    else if(strlen(preg_replace("/[^0-9]/","",esc_sql($_POST['cus_phone']))) < 10 || strlen(preg_replace("/[^0-9]/","",esc_sql($_POST['cus_phone']))) > 11){
        $err_msg .= 'Phone number should be 10-11 digits';
    }
    else if(empty($_POST['cus_message'])){
        $err_msg .= 'Message should not be blanked.';
    }
    if(empty($err_msg)){
        $to = get_option('admin_email');
        $from = get_option('admin_email');
        $from_name = "Moving Loads USA";
        $headers = "From: $from_name <$from>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $subject = "Conatct details";
        $message = "Conatct details are as follows<br/>";
        $message .= "Name: ".$_POST['cus_name']."<br/>";
        $message .= "Email: ".$_POST['cus_email']."<br/>";
        $message .= "Phone no: ".$_POST['cus_phone']."<br/>";
        $message .= "Message: ".$_POST['cus_message']."<br/>";
        if(wp_mail( $to, $subject, $message, $headers )){
            $suc_msg .= 'Thank you for contact us. We will respond to you shortly.';
        }else{
            $err_msg .= 'There is an error occured. Please try again later!';
        }
    }
}
get_header();	
?>
        <div class="ctn">
            <!--contain-->
            <div class="contain_main">
            	<div class="ctn_in">
            	<h1>CONTACT US</h1>
                    <div class="registration_main">
                        <div class="registration_main_bg">
                            <h2>CONTACT FORM</h2>
                            <div class="registration_main_in">
                                <p id="err_msgs" class="<?php if(!empty($err_msg)){echo 'err_msg';}else if(!empty($war_msg)){echo 'war_msg';}else{echo 'suc_msg';}?>">
                                   <?php
                                           if(!empty($err_msg)){echo $err_msg;}
                                           else if(!empty($war_msg)){echo $war_msg;}
                                           else{echo $suc_msg;}
                                   ?>
                                </p>
                            	<form action="" method="POST">
                                    <p><label>Name<strong style="color: red;">*</strong></label><input name="cus_name" value="<?php if(isset($_POST['cus_name'])){echo $_POST['cus_name'];}?>" type="text" class="registration_txtfld"></p>
                                    <p><label>Email<strong style="color: red;">*</strong></label><input name="cus_email" value="<?php if(isset($_POST['cus_email'])){echo $_POST['cus_email'];}?>" type="email" class="registration_txtfld"></p>
                                    <p><label>Phone No<strong style="color: red;">*</strong></label><input maxlength="15" value="<?php if(isset($_POST['cus_phone'])){echo format_phone($_POST['cus_phone']);}?>" autocomplete="off" name="cus_phone" type="text" class="registration_txtfld"></p>
                                    <p><label>Message<strong style="color: red;">*</strong></label><textarea name="cus_message" class="post_loads_txtarea2_contact"><?php if(isset($_POST['cus_message'])){echo $_POST['cus_message'];}?></textarea></p>
                                    
                                    <p class="btn_login_main">
                                    	<input name="contact" type="submit" value="Submit" class="regis_btn">
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