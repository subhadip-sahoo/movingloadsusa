<?php
/**
 * Template Name:View close job
 */
global $wpdb;$user_id=get_current_user_id();
if(!$user_id){
    wp_safe_redirect(home_url().'/login/');
    exit();
}

if(!is_activated_post()){
    wp_safe_redirect(site_url().'/post-packages/');
    exit();
}
$err_msg = '';
$suc_msg = '';
$war_msg = '';
$req_job_id=$_REQUEST['job_id'];
$post_type_query=$wpdb->get_row("select post_type from wp_awarded_job where id_job=$req_job_id");
$post_types=$post_type_query->post_type;
if($post_types=='spaces'){
    $current_space_available = (current_space_available($_REQUEST['post_id'], 'spaces'))?current_space_available($_REQUEST['post_id'], 'spaces'):0;
    if(isset($_REQUEST['post_id'])){
        $get_records = $wpdb->get_results("SELECT * FROM wp_spaces WHERE id_space = ".$_REQUEST['post_id'], ARRAY_A);
        if(!empty($get_records)){
            foreach ($get_records as $get_record) {
                 extract($get_record);
            }
        }
    }
}
else if ($post_types=='loads') {
    $current_space_available = (current_space_available($_REQUEST['post_id'], 'loads'))?current_space_available($_REQUEST['post_id'], 'loads'):0;
    if(isset($_REQUEST['post_id'])){
        $get_records = $wpdb->get_results("SELECT * FROM wp_loads WHERE id = ".$_REQUEST['post_id'], ARRAY_A);
        if(!empty($get_records)){
            foreach ($get_records as $get_record) {
                 extract($get_record);
            }
        }
    }
}
if(isset($_REQUEST['job_id'])){
    if(!is_current_user_job($_REQUEST['job_id'])){
        wp_safe_redirect(home_url().'/dashboard/');
        exit();
    }
    $job_id = $_REQUEST['job_id'];
    $get_joblists = $wpdb->get_results("SELECT * FROM wp_awarded_job WHERE id_job = $job_id", ARRAY_A);
    foreach ($get_joblists as $get_joblist) {
        extract($get_joblist);
        $dot = get_user_meta($get_joblist['id_carrier'], 'dot', TRUE);
        $carrier_phone = get_user_meta($get_joblist['id_carrier'], 'phone', TRUE);
        $first_name = get_user_meta($get_joblist['id_carrier'], 'first_name', true);
        $last_name = get_user_meta($get_joblist['id_carrier'], 'last_name', true);
        $carrier_name = $first_name.' '.$last_name;      
    }
}

get_header(); 
?>
            <div class="ctn">
                    <!--contain-->
                    <div class="contain_main">
                        <div class="ctn_in">
                            <h1><?php echo strtoupper(get_the_title()); ?></h1>
                                <div class="registration_main">
                                    <div class="open_post_bg">                                                                                                                                                        
                                            <div class="open_post_bg_in">
                                            <?php     
                                             if($post_types=='spaces'){   ?>
                                                    <h2>SPACE DETAILS</h2>
                                                          <div class="open_post_ul_div">
                                                            <ul>
                                                                <li>
                                                                    <div class="open_post_li_width">From Date:</div>
                                                                    <div class="open_post_li_width2"><?php if(isset($o_date)){ echo date('m-d-Y',strtotime($o_date));}?></div>
                                                                </li>
                                                                <li>
                                                                    <div class="open_post_li_width">To Date:</div>
                                                                    <div class="open_post_li_width2"><?php if(isset($d_date)){ echo date('m-d-Y',strtotime($d_date));}?></div>
                                                                </li>
                                                                <li>
                                                                    <div class="open_post_li_width">Space Available:</div>
                                                                    <div class="open_post_li_width2"><?php echo $space_available; ?> c.f</div>
                                                                </li>

                                                                <li>
                                                                    <div class="open_post_li_width">Current Space Available:</div>
                                                                    <div class="open_post_li_width2"><?php echo $current_space_available; ?> c.f</div>
                                                                </li>

                                                                <li>
                                                                    <div class="open_post_li_width">Trailer Size:</div>
                                                                    <div class="open_post_li_width2"><?php echo $trailer_size; ?> c.f</div>
                                                                </li>
                                                                <li>
                                                                    <div class="open_post_li_width">Origin State:</div>
                                                                    <div class="open_post_li_width2"><?php if(isset($o_state)){ echo $o_state;}?></div>
                                                                </li>
                                                                <li>
                                                                    <div class="open_post_li_width">Destination State:</div>
                                                                    <div class="open_post_li_width2"><?php if(isset($d_state)){ echo $d_state;}?></div>
                                                                </li>
                                                                <li>
                                                                    <div class="open_post_li_width">Comments:</div>
                                                                    <div class="open_post_li_width2"><?php echo $comments; ?></div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                            <?php } else if ($post_types=='loads') { ?>
                                                   <h2 class="loads_cls">ORIGIN STATE</h2>
                                                <div class="open_post_ul_div">
                                                    <ul>
                                                        <li>
                                                            <div class="open_post_li_width">Zip Code:</div>
                                                            <div class="open_post_li_width2"><?php if(isset($o_zip)){ echo $o_zip;}?></div>
                                                        </li>
                                                        <li>
                                                            <div class="open_post_li_width">City:</div>
                                                            <div class="open_post_li_width2"><?php if(isset($o_city)){ echo $o_city;}?></div>
                                                        </li>
                                                        <li>
                                                            <div class="open_post_li_width">State:</div>
                                                            <div class="open_post_li_width2"><?php if(isset($o_state)){ echo $o_state;}?></div>
                                                        </li>
                                                        <li>
                                                            <div class="open_post_li_width">Loading From:</div>
                                                            <div class="open_post_li_width2"><?php echo $o_loading; ?></div>
                                                        </li>
                                                        <li>
                                                            <div class="open_post_li_width">Date Load Available for Pickup:</div>
                                                            <div class="open_post_li_width2"><?php if(isset($o_date)){ echo date('m-d-Y',strtotime($o_date));}?></div>
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
                                                    <h2 class="loads_cls">DESTINATION STATE</h2>
                                                    <ul>
                                                        <li>
                                                            <div class="open_post_li_width">Dest Zip Code:</div>
                                                            <div class="open_post_li_width2"><?php if(isset($d_zip)){ echo $d_zip;}?></div>
                                                        </li>
                                                        <li>
                                                            <div class="open_post_li_width">Dest City:</div>
                                                            <div class="open_post_li_width2"><?php if(isset($d_city)){ echo $d_city;}?></div>
                                                        </li>
                                                        <li>
                                                            <div class="open_post_li_width">State:</div>
                                                            <div class="open_post_li_width2"><?php if(isset($d_state)){ echo $d_state;}?></div>
                                                        </li>
                                                        <li>
                                                            <div class="open_post_li_width">Delivery To:</div>
                                                            <div class="open_post_li_width2"><?php echo $d_delivary; ?></div>
                                                        </li>
                                                        <li>
                                                            <div class="open_post_li_width">Date Load Available for Delivery:</div>
                                                            <div class="open_post_li_width2"><?php if(isset($d_date)){ echo date('m-d-Y',strtotime($d_date));}?></div>
                                                        </li>
                                                        <li>
                                                            <div class="open_post_li_width">Comments:</div>
                                                            <div class="open_post_li_width2"><?php echo $d_comments; ?></div>
                                                        </li>
                                                    </ul>
                                                </div>
                                        <?php } ?> 
                                        <h2>JOB DETAILS:</h2>
                                         <div class="open_post_ul_div">
                                                <ul>
                                                    <li>
                                                        <div class="open_post_li_width">DOT #:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($dot)){echo $dot;}?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Carrier/MOVING:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($carrier_name)){echo $carrier_name;}?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Carrier Phone #:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($carrier_phone)){echo $carrier_phone;}?></div>
                                                    </li>
                                                    
                                                    <li>
                                                        <div class="open_post_li_width">Driver/Dispatcher Name:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($dispatcher_name)){echo $dispatcher_name;}?></div>
                                                    </li>
                                                        
                                                    <li>
                                                        <div class="open_post_li_width">Driver/Disp Phone #:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($dispatcher_phone)){echo $dispatcher_phone;}?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Cust Job #:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($cus_job_id)){echo $cus_job_id;}?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Cust Name:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($cus_name)){echo $cus_name;}?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Cust Phone #:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($cus_phone)){echo $cus_phone;}?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Job Loading Date:</div>
                                                        <div class="open_post_li_width2"><?php if(($job_loading_date)){echo date('m-d-Y',  strtotime($job_loading_date));}?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">CFT Loaded:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($cft_loaded)){echo $cft_loaded;}?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Job Pickup Date:</div>
                                                        <div class="open_post_li_width2"><?php if(($job_pickup_date)){echo date('m-d-Y',strtotime($job_pickup_date));}?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width"># Blankets:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($blankets)){echo $blankets;}?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">CFT P/U:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($cft_pu)){echo $cft_pu;}?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">$ per CFT:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($cost_per_cft)){echo $cost_per_cft;}?></div>
                                                    </li>
                                                     <li>
                                                        <div class="open_post_li_width">Client Balance Due:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($client_balance_due)){echo $client_balance_due;}?></div>
                                                    </li>                                                     
                                                     <li>
                                                        <div class="open_post_li_width">Client Balance Due:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($client_balance_due)){echo $client_balance_due;}?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Balance to Company:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($balance_to_company)){echo $balance_to_company;}?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Balance to Carrier:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($balance_to_carrier)){echo $balance_to_carrier;}?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Comments:</div>
                                                        <div class="open_post_li_width2"><?php if(isset($job_comments)){echo $job_comments;}?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Job Delivered:</div>
                                                        <div class="open_post_li_width2"><?php if($job_delivered == '0'){echo "No"; }else if($job_delivered == '1'){ echo "Yes"; }?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Balance Paid:</div>
                                                        <div class="open_post_li_width2"><?php if($balance_paid == '0'){echo "No"; }else if($balance_paid == '1'){ echo "Yes"; }?></div>
                                                    </li>
                                                    <li>
                                                        <div class="open_post_li_width">Close Job:</div>
                                                        <div class="open_post_li_width2"><?php if($close_job == '0'){echo "No"; }else if($close_job == '1'){ echo "Yes"; }?></div>
                                                    </li>
                                                </ul>
                                            </div>                                                                      
                                    </div>                             
                             </div>
                        </div>
                    </div>
                </div>
    <!--cnt_div-end-->
</div>

<?php
get_footer();
?>