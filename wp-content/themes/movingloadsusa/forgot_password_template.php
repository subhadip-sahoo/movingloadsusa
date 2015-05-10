<?php
/**
Template Name:Forgot Password
 */
 ?>
			<?php
			$err_msg='';
			$war_msg='';
			$suc_msg='';
			global $wpdb ,$user_id;
			if($user_ID){
				wp_safe_redirect(home_url());
			}

								
							if(isset($_POST['get_new_password']))
							{								   
									$user_logs= esc_sql($_POST['user_log']);
									if(empty($user_logs)) { 
										$err_msg .="User name or Email should not be empty.<br/>";										
									}
									else
									{
											$user_log = $wpdb->escape(trim($_POST['user_log']));					
												
												if ( strpos($user_log, '@') ) {
													$user_data = get_user_by_email($user_log);
													if(empty($user_data) || $user_data->caps[administrator] == 1) { 
														$err_msg .='Invalid Email Address';														
													}
												}
												else {
													$user_data = get_userdatabylogin($user_log);
													if(empty($user_data) || $user_data->caps[administrator] == 1) { 
														$err_msg .='Invalid Username';														
													}
												}									
											
											
												if($user_data!=""){													
													$user_login = $user_data->user_login;
													$user_email = $user_data->user_email;													
													 $user = get_userdatabylogin($user_login);
														if($user){
														  $id_user= $user->ID;
														}
															$user_info = get_userdata( $id_user);
															$generate=$user_info->user_activation_key;
															$from = get_option('admin_email');
															$headers = "From: MovingLoadsUSA <$from>\r\n";
															$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
															$subject="Resetting Password Request";
															$message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n"."<br/>";
															$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n"."<br/>";
															$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n"."<br/>";
															$home_url= home_url();
															$url=$home_url."/reset-password?exsiting_id=$id_user&activation_number=$generate";
															$message .=__('To reset your password, visit the following address:') . "\r\n\r\n";
															$message .= "<a href='$url'>Click here to reset password</a>\r\n";
														
														if ( $message && !wp_mail($user_email, $subject, $message,$headers) ) 
														{
															$war_msg .="Email failed to send for some unknown reason";
															
														}
														else
														{
															$suc_msg .="Please check your email";
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
            	<h1>Forgot Password</h1>
					<div class="registration_main">
                        <div class="registration_main_bg">
						<h2>USERNAME OR EMAIL</h2>									
                                <div class="registration_main_in">
									<p id="err_msgs" class="<?php if(!empty($err_msg)){echo 'err_msg';}else if(!empty($war_msg)){echo 'war_msg';}else{echo 'suc_msg';}?>">
									   <?php
										   if(!empty($err_msg)){echo $err_msg;}
										   else if(!empty($war_msg)){echo $war_msg;}
										   else{echo $suc_msg;}
									   ?>
									</p>
                            	<form action="" method="POST">                                	
                                    <p><label>Your Email</label><input name="user_log" type="text" class="registration_txtfld"></p>                                          
                                                                      
                                    <p class="btn_login_main">
                                    	<input name="get_new_password" type="submit" value="Get New Password" class="regis_btn">
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
	