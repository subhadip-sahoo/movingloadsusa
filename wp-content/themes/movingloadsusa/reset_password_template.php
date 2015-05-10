<?php
/**
Template Name:Reset Password
 */
?>
<?php
global $wpdb ;
			$err_msg = '';
			$war_msg='';
			$suc_msg='';		
			$number=$_GET['activation_number'];
			if($number=='' || is_user_logged_in())
			{
				wp_safe_redirect(home_url());
			}

			if(isset($_POST['reset_password']))
			{
								
							$new_pass= esc_sql($_POST['new_pass']);
							$con_pass= esc_sql($_POST['con_pass']);
						
							if(empty($new_pass)) { 
								$err_msg .="Please Enter  New Password.<br/>";
						
							}
							
						
							else if(empty($con_pass )) { 
								$err_msg .= "Please Enter Confirm Password.<br/>";
						
							}
							else if($new_pass!=$con_pass)
							{
								$war_msg .= "does not match";

							}
							$us_id=esc_sql($_POST['us_id']);
								$numberss=esc_sql($_POST['numbers']);
								//echo $numberss;
									//echo $us_id;
									$user_info = get_userdata( $us_id);
									 $generate=$user_info->user_activation_key;	
								
																
							if($new_pass!='' && $con_pass!='' &&  $new_pass==$con_pass && $numberss==$generate)
							{
							$us_id=esc_sql($_POST['us_id']);
									
									$user_info = get_userdata( $us_id);
									 $generate=$user_info->user_activation_key;		
								
								$update=wp_update_user( array ( 'ID' => $us_id, 'user_pass' =>$new_pass) ) ;
								if($update)
								{
									
									wp_safe_redirect(home_url().'/login');
									exit();
									
								}
								
							}
							else
								{
									$war_msg .="Please Try again later";
								}	
				
			}
			
?>
<?php get_header(); ?>
			  <div class="ctn">
            <!--contain-->
            <div class="contain_main">
            	<div class="ctn_in">
            	<h1>Reset Password</h1>
					<div class="registration_main">
                        <div class="registration_main_bg">
						<h2>ENTER YOUR NEW PASSWORD BELOW</h2>			
                                <div class="registration_main_in">
								<p id="err_msgs" class="<?php if(!empty($err_msg)){echo 'err_msg';}else if(!empty($war_msg)){echo 'war_msg';}else{echo 'suc_msg';}?>">
									   <?php
										   if(!empty($err_msg)){echo $err_msg;}
										   else if(!empty($war_msg)){echo $war_msg;}
										   else{echo $suc_msg;}
									   ?>
									</p>
                            	<form action="" method="POST">                                	
                                    <p><label> New Password</label><input name="new_pass" type="password" class="registration_txtfld"></p>
                                    <p><label>Confirm New Password</label><input name="con_pass" type="password" class="registration_txtfld"></p>                                    
                                                                        
                                    <p class="btn_reg_main">
                                        <input type="hidden" value="<?php $exsiting_user_id=$_GET['exsiting_id']; echo $exsiting_user_id;?>" name="us_id">
                                        <input type="hidden" value="<?php $numbers=$_GET['activation_number']; echo $numbers;?>" name="numbers">
                                        <input name="reset_password" type="submit" value="Reset Password" class="regis_btn">
                                    </p>
									
                                </form>
										<?php 
										if(!empty($err_msg)){$color = 'red';}
										else if(!empty($war_msg)){$color = 'orange';}
										else{$color = 'green';} 
									?>
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