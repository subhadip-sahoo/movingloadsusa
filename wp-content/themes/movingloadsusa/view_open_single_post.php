<?php
/* Template Name: View Open Single Post */
global $wpdb;$user_id=get_current_user_id();
if(!$user_id){
    wp_safe_redirect(home_url().'/login/');
    exit();
}
$post_id = $_REQUEST['id'];
$post_type = $_REQUEST['pt'];
$where = ($post_type == 'spaces')?'id_space':'id';
$get_details  = $wpdb->get_results("SELECT * FROM wp_$post_type WHERE $where = $post_id", ARRAY_A);
if(!empty($get_details)){
    foreach ($get_details as $get_detail) {
        extract($get_detail);
    }
}
$user_data = get_userdata($userid);
get_header();
?>

<div class="ctn">
    <!--contain-->
        <div class="contain_main">
            <div class="ctn_in">
            	<h1>VIEW OPEN SINGLE POSTS</h1>
                    <div class="registration_main">
                        <div class="open_post_bg">
                            <div class="open_post_bg_in"> 
                            <?php
                                $expected_url = '';
                                $display_edit = FALSE;
                                if($userid == $user_id){
                                    $display_edit = TRUE;
                                    if($post_type == 'loads'){
                                        $expected_url .= site_url()."/post-loads/?post_id=$post_id";
                                    }
                                    else{
                                       $expected_url .= site_url()."/post-space/?post_id=$post_id"; 
                                    }
                                }
                            ?>
                            <h2 <?php echo($post_type == 'loads')?'class="loads_cls"':'class="spaces_cls"'; ?>><?php echo ucwords($post_type);?>
                            <?php
                                if($display_edit == TRUE){
                            ?>
                                <span>
                                    <a href="<?php echo $expected_url;?>" title="Edit">Edit </a>
                                    <img src="<?php echo get_template_directory_uri();?>/images/edit_icon_1.png" alt="">
                                </span>
                                <?php } ?>
                            </h2>
                            <?php if($post_type == 'loads'){ ?>
                            <div class="open_post_ul_div">
                                <ul>
                                    <li>
                                        <div class="open_post_li_width">Zip Code:</div>
                                        <div class="open_post_li_width2"><?php echo $o_zip; ?></div>
                                    </li>
                                    <li>
                                        <div class="open_post_li_width">City:</div>
                                        <div class="open_post_li_width2"><?php echo $o_city; ?></div>
                                    </li>
                                    <li>
                                        <div class="open_post_li_width">State:</div>
                                        <div class="open_post_li_width2"><?php echo $o_state; ?></div>
                                    </li>
                                    <li>
                                        <div class="open_post_li_width">Loading From:</div>
                                        <div class="open_post_li_width2"><?php echo $o_loading; ?></div>
                                    </li>
                                    <li>
                                        <div class="open_post_li_width">Date Load Available for Pickup:</div>
                                        <div class="open_post_li_width2"><?php echo date('m-d-Y',strtotime($o_date)); ?></div>
                                    </li>
                                    <li>
                                        <div class="open_post_li_width">Stairs:</div>
                                        <div class="open_post_li_width2"><?php echo $o_stairs; ?></div>
                                    </li>
                                    <li>
                                        <div class="open_post_li_width">53' Trailer Available:</div>
                                        <div class="open_post_li_width2"><?php echo $o_traileravlble; ?></div>
                                    </li>
                                    <li>
                                        <div class="open_post_li_width">Long Haul:</div>
                                        <div class="open_post_li_width2"><?php echo $o_haul; ?></div>
                                    </li>
                                    <li>
                                        <div class="open_post_li_width">Load Size:</div>
                                        <div class="open_post_li_width2"><?php echo $o_loadsize; ?> c.f. </div>
                                    </li>
                                </ul>
                                <div class="open_post_ul_bottom"></div>
                                    <ul>
                                        <li>
                                            <div class="open_post_li_width">Dest Zip Code:</div>
                                            <div class="open_post_li_width2"><?php echo $d_zip; ?></div>
                                        </li>
                                        <li>
                                            <div class="open_post_li_width">Dest City:</div>
                                            <div class="open_post_li_width2"><?php echo $d_city; ?></div>
                                        </li>
                                        <li>
                                            <div class="open_post_li_width">State:</div>
                                            <div class="open_post_li_width2"><?php echo $d_state; ?></div>
                                        </li>
                                        <li>
                                            <div class="open_post_li_width">Delivery To:</div>
                                            <div class="open_post_li_width2"><?php echo $d_delivary; ?></div>
                                        </li>
                                        <li>
                                            <div class="open_post_li_width">Date Load Available for Delivery:</div>
                                            <div class="open_post_li_width2"><?php echo date('m-d-Y',strtotime($d_date)); ?></div>
                                        </li>
                                        <li>
                                            <div class="open_post_li_width">Comments:</div>
                                            <div class="open_post_li_width2"><?php echo $d_comments; ?></div>
                                        </li>
                                    </ul>			
                                    <div class="open_post_ul_bottom"></div>
                                    <ul>   
                                        <li>    
                                            <div class="open_post_li_width">Company Name:</div>  
                                            <div class="open_post_li_width2">	
                                                <a name="add_carrier" id="add_carrier" type="button" value="" class="addbutton topopup" style="background:none;border:none;text-decoration:underline;"><?php echo $user_data->company_name;?></a>
                                            </div>  
                                        </li>	
                                        <li>  
                                            <div class="open_post_li_width">Company Phone:</div>   
                                            <div class="open_post_li_width2"><?php echo $user_data->company_phone;?></div>      
                                        </li>
                                    </ul>
                                    <div class="open_post_ul_bottom">
                                        <a href="<?php echo site_url();?>/add-job/?post_id=<?php echo $id;?>">
                                            <input name="award_job" type="button"  class="btn_4" value="Award Loads">
                                        </a>
                                    </div>
                                </div>
                            <?php } if($post_type == 'spaces'){ ?>
                                <div class="open_post_ul_div">
                                    <ul>
                                        <li>
                                            <div class="open_post_li_width">From Date:</div>
                                            <div class="open_post_li_width2"><?php echo date('m-d-Y',strtotime($o_date)); ?></div>
                                        </li>
                                        <li>
                                            <div class="open_post_li_width">To Date:</div>
                                            <div class="open_post_li_width2"><?php echo date('m-d-Y',strtotime($d_date)); ?></div>
                                        </li>
                                        <li>
                                            <div class="open_post_li_width">Space Available:</div>
                                            <div class="open_post_li_width2"><?php echo $space_available; ?></div>
                                        </li>
                                        <li>
                                            <div class="open_post_li_width">Trailer Size:</div>
                                            <div class="open_post_li_width2"><?php echo $trailer_size; ?></div>
                                        </li>
                                        <li>
                                            <div class="open_post_li_width">Origin State:</div>
                                            <div class="open_post_li_width2"><?php echo $o_state; ?></div>
                                        </li>
                                        <li>
                                            <div class="open_post_li_width">Destination State:</div>
                                            <div class="open_post_li_width2"><?php echo $d_state; ?></div>
                                        </li>
                                        <li>
                                            <div class="open_post_li_width">Comments:</div>
                                            <div class="open_post_li_width2"><?php echo $comments; ?></div>
                                        </li>
                                    </ul>
                                    <div class="open_post_ul_bottom">
                                        <a href="<?php echo site_url();?>/add-job-space/?post_id=<?php echo $id_space;?>">
                                            <input name="award_job" type="button"  class="btn_4" value="Award Space" />
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                                <!-- POPUP UP -->
                                <div id="backgroundPopup"></div>
                                <div id="toPopup" class="add-pup">
                                    <div class="close"></div>
                                    <span class="ecs_tooltip">Press Esc to close</span>
                                    <div class="open_post_bg_in ovr">
                                        <h2>Company Details</h2> 
                                        <div id="popup_content"> <!--your content start-->
                                            <p id="add_career_result"></p>
                                            <div class="post_loads_div cmpny pup33">
                                                <p><label><b>Company Name:</b> </label><?php echo $user_data->company_name; ?></p><br />
                                                <p><label><b>Company Phone:</b> </label><?php echo $user_data->company_phone; ?></p><br />
                                                <p><label><b>Address:</b> </label><?php echo $user_data->address; ?></p><br />
                                                <p><label><b>City:</b> </label><?php echo $user_data->city; ?></p><br />
                                                <p><label><b>Zip:</b> </label><?php echo $user_data->zip; ?></p><br />
                                                <p><label><b>Conatct Name:</b> </label><?php echo $user_data->first_name.' '.$user_data->last_name; ?></p><br />
                                                <p><label><b>Phone no:</b> </label><?php echo $user_data->phone; ?></p><br />
                                                <p><label><b>Email:</b> </label><?php echo $user_data->user_email; ?></p>
                                            </div>
                                        </div> <!--your content end-->
                                    </div>
                                </div>
                                <!-- END POPUP UP -->
                            </div>
                        </div>                                            
                    </div>
                </div>
            </div>
            <!--cnt_div-end-->
    </div>
<?php get_footer(); ?>