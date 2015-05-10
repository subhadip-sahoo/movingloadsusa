<?php
session_start();
/* Template Name: Add job */
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
$current_space_available = (current_space_available($_REQUEST['post_id'], 'loads'))?current_space_available($_REQUEST['post_id'], 'loads'):0;
if(isset($_REQUEST['post_id'])){
    $get_records = $wpdb->get_results("SELECT * FROM wp_loads WHERE id = ".$_REQUEST['post_id'], ARRAY_A);
    if(!empty($get_records)){
        foreach ($get_records as $get_record) {
             extract($get_record);
        }
    }
}
if(isset($_REQUEST['post_id']) && !isset($_REQUEST['job_id'])){
    $get_cust_ID = $wpdb->get_results("SELECT IFNULL(id_job,0)+1 AS cus_job_id FROM wp_awarded_job WHERE id_job = (SELECT MAX(id_job) FROM wp_awarded_job)");
    foreach ($get_cust_ID as $cust_ID) {
        $cus_job_id = $cust_ID->cus_job_id;
    }
    $cus_job_id = 'JOB-LOADS-'.str_pad($cus_job_id, 4, "1000", STR_PAD_LEFT);
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
    $max_cft_loaded = $current_space_available + $cft_loaded;
}
if(isset($_POST['submit'])){
    if(isset($_POST['carrier_name'])){
        $carrier_name = esc_sql($_POST['carrier_name']);
    }
    $id_carrier = $_POST['id_carrier'];
    $id_post = $_POST['id_post'];
    $dispatcher_name = esc_sql($_POST['dispatcher_name']);
    $cus_name = esc_sql($_POST['cus_name']);
    $cus_job_id = esc_sql($_POST['cus_job_id']);
    $job_loading_date = str_replace('-', '/', esc_sql($_POST['job_loading_date']));
    $cft_loaded = esc_sql($_POST['cft_loaded']);
    $balance_to_carrier = esc_sql($_POST['balance_to_carrier']);
    $carrier_phone = esc_sql($_POST['carrier_phone']); // need to add user meta
    $dispatcher_phone = preg_replace("/[^0-9]/","",esc_sql($_POST['dispatcher_phone']));
    $dot = esc_sql($_POST['dot']); // need to add user meta
    $cus_phone = preg_replace("/[^0-9]/","",esc_sql($_POST['cus_phone']));
    $job_pickup_date = str_replace('-', '/', esc_sql($_POST['job_pickup_date']));
    $blankets = esc_sql($_POST['blankets']);
    $cft_pu = esc_sql($_POST['cft_pu']);
    $cost_per_cft = esc_sql($_POST['cost_per_cft']);
    $client_balance_due = esc_sql($_POST['client_balance_due']);
    $balance_to_company = esc_sql($_POST['balance_to_company']);
    $job_comments = esc_sql($_POST['job_comments']);
    $job_delivered= (isset($_POST['job_delivered']))?esc_sql($_POST['job_delivered']):0;
    $balance_paid = (isset($_POST['balance_paid']))?esc_sql($_POST['balance_paid']):0;
    $close_job = (isset($_POST['close_job']))?esc_sql($_POST['close_job']):0;
    
    if(empty($dot)) { 
        $err_msg = "Please enter dot number.<br/>";
    }
    else if(strlen($dot)!=7) { 
            $err_msg = "Dot number must be 7 digit.<br/>";
    }
    else if(empty($id_carrier) || !is_numeric($id_carrier)){
        $err_msg = 'Please select a carrier. If not present in the list then manually add it.';
    }
    else if(!empty($_POST['dispatcher_phone'])&& strlen($dispatcher_phone) < 10 || strlen($dispatcher_phone) > 11) { 
        $err_msg = "Dispatcher phone number should be 10-11 digits.<br/>";
    }
    else if(!empty($_POST['cus_phone']) && strlen($cus_phone) < 10 || strlen($cus_phone) > 11) { 
        $err_msg = "Customer phone number should be 10-11 digits.<br/>";
    }
//    else if(empty($dispatcher_name)){
//        $err_msg = 'Please enter driver/dispatcher name.';
//    }
//    else if(empty($dispatcher_phone)){
//        $err_msg = 'Please enter dispatcher phone.';
//    }
//    else if(empty($cus_job_id)){
//        $err_msg = 'Please enter customer job ID.';
//    }
//    else if(empty($cus_name)){
//        $err_msg = 'Please enter customer name.';
//    }
//    else if(empty($cus_phone)){
//        $err_msg = 'Please enter customer phone no.';
//    }
    else if(empty($job_loading_date)){
        $err_msg = 'Please enter job loading date.';
    }
    else if(empty($cft_loaded)){
        $err_msg = 'Please enter cft loaded.';
    } 
    else if(!isset($_REQUEST['job_id']) && $current_space_available < $cft_loaded){
        $err_msg = "CFT loaded can not be greater than current load size. You can add maximum $current_space_available c.f";
    }
    else if(isset($_REQUEST['job_id']) && $cft_loaded > $max_cft_loaded){
        $err_msg = "CFT loaded can not be greater than current load size. You can add maximum $current_space_available c.f";
    }
    else if(empty($job_pickup_date)){
        $err_msg = 'Please enter job pickup date.';
    }
    else if($job_pickup_date > $job_loading_date){
        $err_msg = 'Job pick up date should come before job loading date.';
    }
//    else if(empty($blankets)){
//        $err_msg = 'Please enter blankets.';
//    }
//    else if(empty($cft_pu)){
//        $err_msg = 'Please enter CFT P/U.';
//    }
//    else if(empty($cost_per_cft)){
//        $err_msg = 'Please enter cost per cft.';
//    }
//    else if(!is_numeric($cost_per_cft)){
//        $err_msg = 'Please enter valid cost per cft.';
//    }
//    else if(empty($client_balance_due)){
//        $err_msg = 'Please enter client balance due.';
//    }
//    else if(!is_numeric($client_balance_due)){
//        $err_msg = 'Please enter valid client balance due.';
//    }
//    else if(empty($balance_to_company)){
//        $err_msg = 'Please enter balance to company.';
//    }
//    else if(!is_numeric($balance_to_company)){
//        $err_msg = 'Please enter valid balance to company.';
//    }
//    else if(empty($balance_to_carrier)){
//        $err_msg = 'Please enter balance to carrier.';
//    }
//    else if(!is_numeric($balance_to_carrier)){
//        $err_msg = 'Please enter valid balance to carrier.';
//    }
    if(empty($err_msg)){
        $awarded_job = array(
            'id_carrier' => $id_carrier,
            'id_user' => $user_id,
            'id_post' => $id_post,
            'dispatcher_name' => $dispatcher_name,
            'cus_name' => $cus_name,
            'cus_job_id' => $cus_job_id,
            'job_loading_date' => date('Y-m-d', strtotime($job_loading_date)),
            'cft_loaded' => $cft_loaded,
            'balance_to_carrier' => $balance_to_carrier,
            'dispatcher_phone' => $dispatcher_phone,
            'cus_phone' => $cus_phone,
            'job_pickup_date' => date('Y-m-d', strtotime($job_pickup_date)),
            'blankets' => $blankets,
            'cft_pu' => $cft_pu,
            'cost_per_cft' => $cost_per_cft,
            'client_balance_due' => $client_balance_due,
            'balance_to_company' => $balance_to_company,
            'job_comments' => $job_comments,
            'job_delivered' => $job_delivered,
            'balance_paid' => $balance_paid,
            'close_job' => $close_job,
            'post_type' => 'loads'
        );
        if(!empty($_POST['id_job']) && is_numeric($_POST['id_job'])){
            $wpdb->update('wp_awarded_job',$awarded_job, array('id_job' => $_POST['id_job']));
            $job_id = $_POST['id_job'];
            $suc_msg .= 'Job updated successfully.';
            $_SESSION['show_msg'] = $suc_msg;
            $subject = "Your job has been modified.";
        }else{
            $add_job = $wpdb->insert('wp_awarded_job',$awarded_job);
            if($add_job){
                $job_id = $wpdb->insert_id;
                $suc_msg .= 'Job awarded successfully.';
                $_SESSION['show_msg'] = $suc_msg;
                $subject = "Your job details link.";
            }
        }
        $carrier_data = get_userdata($id_carrier);
        $carrier_email = $carrier_data->user_email;
        $from = get_option('admin_email');
        $from_name = "Moving Loads USA";
        $headers = "From: movinngloadsusa <$from>\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $msg = "Hello $carrier_name,<br/>";
        $msg .= "Click the link below to view your job details.<br/>";
        $msg .= "<a href='".site_url()."/view-job-details/?token=".base64_encode($cus_job_id.'/'.$job_id)."'>click here</a><br/><br/>";
        $msg .= "Best regards<br/>Moving Loads USA Admin";
        wp_mail( $carrier_email, $subject, $msg, $headers );
        wp_safe_redirect(site_url().'/dispatch-board/');
        exit();
    }
}
get_header(); ?>
<script type="text/javascript">
$(function() {
    $( ".datepicker" ).datepicker({
        showOn: "button",
        buttonImage: "<?php echo get_template_directory_uri();?>/images/img_3.png",
        buttonImageOnly: true,
        dateFormat: "mm-dd-yy"
    });
    $('input[type=button][name=submit_career]').click(function(){
        if($('#tc').is(':checked') == false){
            alert('Please check Terms & Conditions');
            return false;
        }
        var url = '<?php echo get_template_directory_uri();?>/add_career.php';
        var queryString  = $('#add_career_form').serialize();
        $.getJSON(url, queryString, function(json){
            $('#add_career_result').removeClass().addClass(json.class).html(json.message);
            if(json.class == 'suc_msg'){
                $('#add_career_form').each(function(){ 
                    this.reset(); 
                });
                $('#dot').val(json.dot);
                $('#carrier_name').val(json.carrier_name);
                $('#carrier_phone').val(json.carrier_phone);
                $('#id_carrier').val(json.id_carrier);
            }
        });
    });
    $("#dot")
    .bind("keyup", function() {
        $('#carrier_name').val('');
        $('#carrier_phone').val('');
        if($(this).val() == ''){
            $('#add_carrier').prop('disabled', true);
        }
        else if($(this).val() != ''){
            $('#new_carrier_dot').val($(this).val());
            $('#add_carrier').prop('disabled', false);
        }
        if(isNaN($(this).val()) == true){
            $(this).val('');
            $('#add_carrier').prop('disabled', true);
            $('#err_msgs').addClass('err_msg').text('Only enter numeric digits');
            return false;
        }
        else if(isNaN($(this).val()) == false){
            $('#err_msgs').removeClass('err_msg').empty();
        }
        
    })   
    .autocomplete({
       source: <?php echo json_encode(autocomplete_carrier_list());?>,
        select: function(event, ui) {
            $("#id_carrier").val(ui.item ? ui.item.id : this.value);
            $('#add_carrier').prop('disabled', true);
            var url = '<?php echo get_template_directory_uri();?>/carrier_meta.php/?id_carrier=' + $("#id_carrier").val();
            $.getJSON(url, function(json){
                $('#carrier_name').val(json.carrier_name);
                $('#carrier_phone').val(json.carrier_phone);
            });
        }
    });
    $('input[type=button][name=review]').click(function(){
        var job_id = $('#id_job').val();
        if(job_id == ''){
            alert('Premission denied');
            return false;
        }
        var ratings = $('.rateit').rateit('value');
        var comments = $('#close_job_comments').val();
        var url = '<?php echo get_template_directory_uri();?>/close_job.php?job_id=' + job_id + '&ratings=' + ratings + '&comments=' + comments;
        $.getJSON(url, function(json){
            $('#close_job_status').removeClass().addClass(json.class).html(json.message);
            if(json.class == 'suc_msg'){
                if(confirm('Are you sure you want to close this job - IF YOU ARE SURE, ONCE CLOSED CANNOT BE RE-OPENED') == true){
                    $( "#award_job .button").click();
                }
            }
        });
    });
});
</script>
            <div class="ctn">
                    <!--contain-->
                    <div class="contain_main">
                        <div class="ctn_in">
                            <h1>AWARD LOAD</h1>
                                <div class="registration_main">
                                    <div class="open_post_bg ">
                                        <div id="toPopup" class="pp3">
                                            <div class="close"></div>
                                            <span class="ecs_tooltip">Press Esc to close <span class="arrow"></span></span>
                                            <form action="" method="POST" name="add_career_form" id="add_career_form">
                                                <div class="open_post_bg_in pp2">
                                                   <h2>ADD CARRIER</h2>  
                                                   <div id="popup_content">
                                                   <p id="add_career_result"></p>
                                                   <div class="post_loads_div">
                                                 <p><label>Company Name:<strong style="color: red;">*</strong></label><input name="company_name" type="text" class="post_loads_txtfld" value="<?php if(isset($company_name)){ echo $company_name;}?>"></p>
                                                 <p><label>DOT #:<strong style="color: red;">*</strong></label><input name="dot" id="new_carrier_dot" type="text" maxlength="7" class="post_loads_txtfld" value="<?php if(isset($dot)){ echo $dot;}?>"></p>
                                                 <p><label>MC #:</label><input name="mc" type="text" maxlength="6" class="post_loads_txtfld"  value="<?php if(isset($mc)){ echo $mc;}?>" ></p>
                                                 <p><label>Company Phone:<strong style="color: red;">*</strong></label><input autocomplete="off" maxlength="15" name="company_phone" id="company_phone" type="text" class="post_loads_txtfld" value="<?php if(isset($company_phone)){ echo format_phone($company_phone);}?>"></p>
                                                 <p><label>Address:<strong style="color: red;">*</strong></label><input name="address" type="text" class="post_loads_txtfld" value="<?php if(isset($address)){ echo $address;}?>"></p>
                                                 <p><label>City:<strong style="color: red;">*</strong></label><input name="city" type="text" class="post_loads_txtfld" value="<?php if(isset($city)){ echo $city;}?>"></p>
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
                                                 <p><label class="zip_width">Zip:<strong style="color: red;">*</strong></label><input name="zip" type="text" class="post_loads_txtfld_002" value="<?php if(isset($zip)){ echo $zip;}?>"></p>
                                                 <p><label>Contact First Name:<strong style="color: red;">*</strong></label><input name="first_name" type="text" class="post_loads_txtfld" value="<?php if(isset($first_name)){ echo $first_name;}?>"></p>
                                                 <p><label>Contact Last Name:<strong style="color: red;">*</strong></label><input name="last_name" type="text" class="post_loads_txtfld" value="<?php if(isset($last_name)){ echo $last_name;}?>"></p>
                                                 <p><label>Phone:<strong style="color: red;">*</strong></label><input maxlength="15" autocomplete="off" id="phone" name="phone"  type="text" class="post_loads_txtfld" value="<?php if(isset($phone)){ echo format_phone($phone);}?>"></p>
                                                 <p><label>Email:<strong style="color: red;">*</strong></label><input name="email" type="email" class="post_loads_txtfld" value="<?php if(isset($email)){ echo $email;}?>"></p>
                                                 <div class="terms_conditions_div">
                                                     <p><label></label><img src="<?php echo get_template_directory_uri();?>/captcha/CaptchaSecurityImages.php?width=100&height=40&characters=5" /></p>
                                                     <p><label></label><input id="security_code" placeholder="Enter the code above" name="security_code" type="text" class="post_loads_txtfld" value="<?php echo (isset($_POST['security_code']))? $_POST['security_code']:''; ?>"/></p>
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
                                                     <p><input name="tc" id="tc" type="checkbox" value="on" class="check_1" required><label>I have read and agree to the terms and conditions above</label></p>
                                                 </div>
                                                 </div>
                                                 <div class="button_div1">
                                                     <input type="hidden" name="user_status" id="user_status" value="1">
                                                     <input name="submit_career" type="button" value="Add Career" class="button">
                                                 </div>

                                                 </div>
                                                 </div>
                                             </form>
                                        </div> <!--toPopup end-->
                                        <div id="toPopupClose">
                                            <div class="close"></div>
                                            <span class="ecs_tooltip">Press Esc to close <span class="arrow"></span></span>
                                            <form name="close_job_form" id="close_job_form" action="" method="post">
                                                <div class="open_post_bg_in pp2">
                                                    <h2>REVIEW</h2> 
                                                    <div id="popup_content"> <!--your content start-->
                                                        <p id="close_job_status"></p>
                                                        <div class="post_loads_div">
                                                            <p><label>Rate Your Carrier</label><div class="rateit bigstars" data-rateit-starwidth="32" data-rateit-starheight="32"></div></p>
                                                            <p><label>Comments</label><textarea name="close_job_comments" id="close_job_comments" class="post_loads_txtarea2"><?php if(isset($close_job_comments)){echo $close_job_comments;}?></textarea></p>
                                                        </div>
                                                        <div class="button_div1">
                                                            <input name="review" type="button" value="Submit Review" class="button">
                                                        </div>
                                                    </div> <!--your content end-->
                                                </div>
                                            </form>
                                        </div> <!--toPopup end-->
                                        <div class="loader"></div>
                                        <div id="backgroundPopup"></div>
                                        <form name="award_job" id="award_job" action="" method="post">
                                            <div class="open_post_bg_in">
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
                                                        <li>
                                                            <div class="open_post_li_width">Current Load Size:</div>
                                                            <div class="open_post_li_width2"><?php echo $current_space_available; ?> c.f</div>
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
                                        <h2>LOADS AWARDED TO:</h2>
                                        <p id="err_msgs" class="<?php if(!empty($err_msg)){echo 'err_msg';}else if(!empty($war_msg)){echo 'war_msg';}else{echo 'suc_msg';}?>">
                                            <?php
                                                if(!empty($err_msg)){echo $err_msg;}
                                                else if(!empty($war_msg)){echo $war_msg;}
                                                else{echo $suc_msg;}
                                            ?>
                                         </p>
                                        <div class="post_loads_div">
                                            <p><label>DOT #:<strong style="color: red;">*</strong></label><input name="dot" id="dot" placeholder="Search DOT" maxlength="7" value="<?php if(isset($dot)){echo $dot;}?>" type="text" class="post_loads_txtfld_01"><input name="add_carrier" id="add_carrier" type="button" value="Add Carrier" class="addbutton topopup" disabled></p>
                                            <p><label>Carrier:</label><input name="carrier_name" id="carrier_name" type="text" readonly class="post_loads_txtfld" value="<?php if(isset($carrier_name)){echo $carrier_name;}?>"></p>
                                            <p><label>Carrier Phone #:</label><input name="carrier_phone" id="carrier_phone" readonly value="<?php if(isset($carrier_phone)){echo $carrier_phone;}?>" type="text" class="post_loads_txtfld"></p>
                                            <p><label>Driver/Dispatcher Name:</label><input name="dispatcher_name" id="dispatcher_name" type="text" class="post_loads_txtfld" value="<?php if(isset($dispatcher_name)){echo $dispatcher_name;}?>"></p>
                                            <p><label>Driver/Disp Phone #:</label><input name="dispatcher_phone" id="dispatcher_phone" autocomplete="off" maxlength="14" value="<?php if(isset($dispatcher_phone)){echo format_phone($dispatcher_phone);}?>" type="text" class="post_loads_txtfld phone_format"></p>                                            
                                            <p><label>Cust Job #:</label><input name="cus_job_id" id="cus_job_id" value="<?php if(isset($cus_job_id)){echo $cus_job_id;}?>" type="text" class="post_loads_txtfld" readonly></p>
                                            <p><label>Cust Name:</label><input name="cus_name" id="cus_name" type="text" class="post_loads_txtfld" value="<?php if(isset($cus_name)){echo $cus_name;}?>"></p>
                                            <p><label>Cust Phone #:</label><input name="cus_phone" id="cus_phone" autocomplete="off" maxlength="15" value="<?php if(isset($cus_phone)){echo format_phone($cus_phone);}?>" type="text" class="post_loads_txtfld phone_format"></p>
                                            <p><label>Job Loading Date:<strong style="color: red;">*</strong></label><input name="job_loading_date" id="job_loading_date" value="<?php if(($job_loading_date)){echo date('m-d-Y',  strtotime($job_loading_date));}?>" type="text" autocomplete="off" readonly class="post_loads_txtfld3 datepicker job_33"></p>
                                            <p><label>CFT Loaded:<strong style="color: red;">*</strong></label><input name="cft_loaded" id="cft_loaded" value="<?php if(isset($cft_loaded)){echo $cft_loaded;}?>" type="text" class="post_loads_txtfld" <?php echo ($current_space_available == 0)?'readonly':'';?>></p>
                                            <p><label>Job Pickup Date:<strong style="color: red;">*</strong></label><input name="job_pickup_date" id="job_pickup_date" value="<?php if(($job_pickup_date)){echo date('m-d-Y',strtotime($job_pickup_date));}?>" type="text" autocomplete="off" readonly class="post_loads_txtfld_001 datepicker job_33"></p>
                                            <p><label class="zip_width2"># Blankets:</label><input name="blankets" id="blankets" value="<?php if(isset($blankets)){echo $blankets;}?>" type="text" class="post_loads_txtfld_002"></p>
                                            <p><label>CFT P/U:</label><input name="cft_pu" id="cft_pu" value="<?php if(isset($cft_pu)){echo $cft_pu;}?>" type="text" class="post_loads_txtfld"></p>
                                            <p><label>$ per CFT:</label><input name="cost_per_cft" id="cost_per_cft" value="<?php if(isset($cost_per_cft)){echo $cost_per_cft;}?>" type="text" class="post_loads_txtfld"></p>
                                            <p><label>Client Balance Due:</label><input name="client_balance_due" id="client_balance_due" value="<?php if(isset($client_balance_due)){echo $client_balance_due;}?>" type="text" class="post_loads_txtfld"></p>
                                            <p><label>Balance to Company:</label><input name="balance_to_company" id="balance_to_company" value="<?php if(isset($balance_to_company)){echo $balance_to_company;}?>" type="text" class="post_loads_txtfld"></p>
                                            <p><label>Balance to Carrier:</label><input name="balance_to_carrier" id="balance_to_carrier" value="<?php if(isset($balance_to_carrier)){echo $balance_to_carrier;}?>" type="text" class="post_loads_txtfld"></p>
                                            <p><label>COMMENTS</label><textarea name="job_comments" id="job_comments" class="post_loads_txtarea2"><?php if(isset($job_comments)){echo $job_comments;}?></textarea></p>
                                            <p><label>Job Delivered:</label>
                                                <select name="job_delivered" id="job_delivered" class="post_loads_txtarea" <?php echo (isset($job_delivered) && $job_delivered == 1 || !isset($_REQUEST['job_id']))?'disabled':'';?>>
                                                    <option value="0" <?php if($job_delivered == '0'){echo "selected='selected'";}?>>No</option>
                                                    <option value="1" <?php if($job_delivered == '1'){echo "selected='selected'";}?>>Yes</option>
                                                </select>
                                            </p>
                                             <p><label>Balance Paid:</label>
                                                 <select name="balance_paid" id="balance_paid" class="post_loads_txtarea" <?php echo (isset($balance_paid) && $balance_paid == 1 || !isset($_REQUEST['job_id']))?'disabled':'';?>>
                                                    <option value="0" <?php if($balance_paid == '0'){echo "selected='selected'";}?>>No</option>
                                                    <option value="1" <?php if($balance_paid == '1'){echo "selected='selected'";}?>>Yes</option>
                                                 </select>
                                             </p>
                                             <p><label>Close Job:</label>
                                                 <select name="close_job" id="close_job" class="post_loads_txtarea" <?php echo ($current_space_available != 0 || isset($close_job) && $close_job == 1)?'disabled':'';?>>
                                                    <option value="0" <?php if($close_job == '0'){echo "selected='selected'";}?>>No</option>
                                                    <option value="1" <?php if($close_job == '1'){echo "selected='selected'";}?>>Yes</option>
                                                 </select>
                                             </p>
                                        </div>
                                        <div class="button_div1">
                                            <input type="hidden" name="id_carrier" id="id_carrier" value="<?php if(isset($id_carrier)){ echo $id_carrier;}?>">
                                            <input type="hidden" name="id_job" id="id_job" value="<?php if(isset($_REQUEST['job_id'])){echo $_REQUEST['job_id'];}?>">
                                            <input type="hidden" name="id_post" id="id_post" value="<?php if(isset($_REQUEST['post_id'])){echo $_REQUEST['post_id'];}?>">
                                            <input name="submit" type="submit" value="Save" class="button">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
    <!--cnt_div-end-->
</div>
<?php get_footer(); ?>