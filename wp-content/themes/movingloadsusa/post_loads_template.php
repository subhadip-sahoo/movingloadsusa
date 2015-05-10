<?php
/* Template Name:Post Loads */
global $wpdb;
$user_id=get_current_user_id();
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

if(isset($_REQUEST['post_id'])){
    if(!is_current_user_post($_REQUEST['post_id'], 'wp_loads')){
        wp_safe_redirect(home_url().'/dashboard/');
        exit();
    }
    $get_records = $wpdb->get_results("SELECT * FROM wp_loads WHERE id = ".$_REQUEST['post_id'], ARRAY_A);
    if($wpdb->num_rows > 0){
        foreach ($get_records as $get_record) {
             extract($get_record);
        }
    }
}
if(isset($_POST['post_load_now'])){
    $o_zip = esc_sql($_POST['o_zip']);
    $o_city = esc_sql($_POST['o_city']);
    $o_state = esc_sql($_POST['o_state']);		
    $o_loading = esc_sql($_POST['o_loading']);
    $o_date = str_replace('-', '/', esc_sql($_POST['o_date']));
    $o_stairs = esc_sql($_POST['o_stairs']);
    $o_traileravlble = esc_sql($_POST['o_traileravlble']);
    $o_haul = esc_sql($_POST['o_haul']);
    $o_loadsize = esc_sql($_POST['o_loadsize']);
    $d_zip = esc_sql($_POST['d_zip']);
    $d_city = esc_sql($_POST['d_city']);
    $d_state = esc_sql($_POST['d_state']);
    $d_delivary = esc_sql($_POST['d_delivary']);
    $d_date = str_replace('-', '/', esc_sql($_POST['d_date']));
    $d_comments = esc_sql($_POST['d_comments']);
    $post_type = esc_sql($_POST['post_type']);
    if(empty($_POST['id'])){
        $purchased_package_id = esc_sql($_POST['purchased_package_id']);
    }
    if(empty($o_zip)){
        $err_msg .="Origin states zip code should not be blank.";
    }
    elseif(empty($o_city)){
        $err_msg .="Origin state city should not be blank";
    }
    elseif($o_city === 'Invalid US Zipcode.'){
        $err_msg .="Please enter correct US zipcode.";
    }
    elseif(empty($o_state)){
        $err_msg .="Please enter Origin state";
    }
    elseif($o_state === 'Invalid US Zipcode.'){
        $err_msg .="Please enter correct US zipcode.";
    }
    elseif(empty($o_loading)){
        $err_msg .="Please select Origin state Loading from";
    }
    elseif(empty($o_date)){
        $err_msg .="Pick up should not be blank";
    }
    elseif(empty($o_stairs)){
        $err_msg .="Select any stairs";
    }
    elseif(empty($o_traileravlble)){
        $err_msg .="Please select available trailer";
    }
    elseif(empty($o_haul)){
        $err_msg .="Please select  haul";
    }
    elseif(empty($o_loadsize)){
        $err_msg .="Please select  loadsize";
    }
    elseif(empty($d_zip)){
        $err_msg .="Delivery state zip code should not be blank";
    }
    elseif(empty($d_city)){
        $err_msg .="Delivery state city should not be blank";
    }
    elseif(empty($d_state)){
        $err_msg .="Please select Delivery state";
    }
    elseif(empty($d_date)){
        $err_msg .="Please select date load available for delivery";
    }
    elseif($d_date < $o_date){
        $err_msg .="Delivery date should come after pickup date.";
    }
    else{
        $o_states = $wpdb->get_results("SELECT state_short_name FROM wp_statemaster WHERE state_full_name = '$o_state'", ARRAY_A);
        if($wpdb->num_rows == 1){
            foreach ($o_states as $state) {
                $o_state = $state['state_short_name'];
            }
        }
        $d_states = $wpdb->get_results("SELECT state_short_name FROM wp_statemaster WHERE state_full_name = '$d_state'", ARRAY_A);
        if($wpdb->num_rows == 1){
            foreach ($d_states as $state) {
                $d_state = $state['state_short_name'];
            }
        }
        $post_load_arr = array(
            'userid'=>$user_id,
            'o_zip'=>$o_zip,
            'o_city'=>$o_city,
            'o_state'=>$o_state,
            'o_loading'=>$o_loading,
            'o_date'=>date('Y-m-d', strtotime($o_date)),
            'o_stairs'=>$o_stairs,
            'o_traileravlble'=>$o_traileravlble,
            'o_haul'=>$o_haul,
            'o_loadsize'=>$o_loadsize,
            'd_zip'=>$d_zip,
            'd_city'=>$d_city,
            'd_state'=>$d_state,
            'd_delivary'=>$d_delivary,
            'd_date'=>date('Y-m-d', strtotime($d_date)),
            'd_comments'=>$d_comments,
            'post_type'=>$post_type,
            'post_date'=>date('Y-m-d H:i:s')
        );
        if(!empty($_POST['id'])){
            if($wpdb->update('wp_loads', $post_load_arr, array('id' => $_POST['id']))){
                $suc_msg .="Post is successfully updated.";
            }
            else{
                $err_msg .= 'post update failed!';
            }
        }
        else{
            $sql = "    SELECT 
                            pm.*, pa.* 
                        FROM 
                            wp_post_package_master AS pm 
                        INNER JOIN 
                            wp_user_postaccount AS pa 
                        ON 
                            pm.id_post_package = pa.id_package 
                        WHERE 
                            pa.id = $purchased_package_id
                        AND 
                            pa.expiry_date >= NOW()
                    ";
            $get_expiry_posts = $wpdb->get_results($sql, ARRAY_A);
            if($wpdb->num_rows > 0){
                foreach ($get_expiry_posts as $get_expiry_post) {
                    $post_expiry_date = date('Y-m-d H:i:s', strtotime('+'.$get_expiry_post['expiry_per_post'].'day'));
                    $post_count = $get_expiry_post['post_count'];
                }
            }
            array_push_associative($post_load_arr, array('post_expiry_date' => $post_expiry_date, 'purchased_package_id' => $purchased_package_id));
            $post_loads = $wpdb->insert('wp_loads',$post_load_arr);
            if($post_loads){
                $post_count = $post_count-1;
                if($wpdb->update('wp_user_postaccount', array('post_count' => $post_count), array('id' => $purchased_package_id))){
                    $suc_msg .="Successfully Loaded Your Post";
                }
            }
            //unset($_POST);
            $o_zip = NULL;
            $o_city = NULL;
            $o_state = NULL;		
            $o_loading = NULL;
            $o_date = NULL;
            $o_stairs = NULL;
            $o_traileravlble = NULL;
            $o_haul = NULL;
            $o_loadsize = NULL;
            $d_zip = NULL;
            $d_city = NULL;
            $d_state = NULL;
            $d_delivary = NULL;
            $d_date = NULL;
            $d_comments = NULL;
        }
    }									
}
get_header();
?>
<script type="text/javascript">
$(function() {
    $( ".datepicker" ).datepicker({
        showOn: "button",
        buttonImage: "<?php echo get_template_directory_uri();?>/images/img_3.png",
        buttonImageOnly: true,
        dateFormat: "mm-dd-yy"
    });
    $('#o_zip_code').keyup(function(){
        var url = '<?php echo get_template_directory_uri();?>/get_address.php?zipcode=' + $('#o_zip_code').val();
        $.getJSON(url, function(json){
            $('#o_city').val(json.city);
            $('#o_state').val(json.state);
        });
    });
    $('#d_zip_code').keyup(function(){
        var url = '<?php echo get_template_directory_uri();?>/get_address.php?zipcode=' + $('#d_zip_code').val();
        $.getJSON(url, function(json){
            $('#d_city').val(json.city);
            $('#d_state').val(json.state);
        });
    });
});
</script>
        <div class="ctn">
            <!--contain-->
            <div class="contain_main">
            	<div class="ctn_in">
                    <div>
                        <h1>POST LOADS</h1>
                    </div>
                    <div class="registration_main">                        
                        <div class="open_post_bg">
                        <form action="" method="POST">
                            <div class="open_post_bg_in">
                              <h2>ORIGIN STATE</h2>
                                    <p id="err_msgs" class="<?php if(!empty($err_msg)){echo 'err_msg';}else if(!empty($war_msg)){echo 'war_msg';}else{echo 'suc_msg';}?>">
                                       <?php
                                            if(!empty($err_msg)){echo $err_msg;}
                                            else if(!empty($war_msg)){echo $war_msg;}
                                            else{echo $suc_msg;}
                                       ?>
                                    </p>
                                    <div class="post_loads_div">                            	
                                        <p><label>Zip Code</label><input name="o_zip" type="text" id="o_zip_code" class="post_loads_txtfld" autocomplete="off" value="<?php if(isset($o_zip)){ echo $o_zip;}?>" ></p>
                                        <p><label>City</label><input name="o_city" id="o_city" type="text" class="post_loads_txtfld" value="<?php if(isset($o_city)){ echo $o_city;}?>"> </p>
                                        <p><label>State</label><input name="o_state" id="o_state" type="text" class="post_loads_txtfld" value="<?php if(isset($o_state)){ echo $o_state;}?>"> </p>
                                        <p><label>Loading From</label>
                                             <select name="o_loading" class="post_loads_txtarea"> 
                                                <option value="residence" <?php if($o_loading == 'residence'){echo "selected='selected'";}?>>Residence</option>
                                                <option value="warehouse" <?php if($o_loading == 'warehouse'){echo "selected='selected'";}?>>Warehouse</option>
                                                <option value="storage" <?php if($o_loading == 'storage'){echo "selected='selected'";}?>>Storage</option>
                                                <option value="shop/store" <?php if($o_loading == 'shop/store'){echo "selected='selected'";}?>>Shop/Store</option>
                                            </select>	
                                        </p>
                                    <p><label>Date Load Available for Pickup</label><input name="o_date" type="text" autocomplete="off" readonly class="post_loads_txtfld3 datepicker" value="<?php if($o_date){ echo date('m-d-Y', strtotime($o_date));}?>" /></p>
                                    <p><label>Stairs</label>                            	
                                        <select name="o_stairs" class="post_loads_txtarea"> 
                                            <option value="yes" <?php if($o_stairs == 'yes'){echo "selected='selected'";}?>>Yes</option>
                                            <option value="no" <?php if($o_stairs == 'no'){echo "selected='selected'";}?>>No</option>
                                            <option value="not sure" <?php if($o_stairs == 'not sure'){echo "selected='selected'";}?>>Not Sure</option>
                                        </select>	
                                    </p>
                                    <p><label>53' Trailer Available</label>
                                    	<select name="o_traileravlble" class="post_loads_txtarea">
                                            <option value="yes" <?php if($o_traileravlble == 'yes'){echo "selected='selected'";}?>>Yes</option>
                                            <option value="no" <?php if($o_traileravlble == 'no'){echo "selected='selected'";}?>>No</option>
                                            <option value="not sure" <?php if($o_traileravlble == 'not sure'){echo "selected='selected'";}?>>Not Sure</option>
                                        </select>
                                    </p>
                                    <p><label>Long Haul</label>
                                    	<select name="o_haul" class="post_loads_txtarea">
                                            <option value="yes" <?php if($o_haul == 'yes'){echo "selected='selected'";}?>>Yes</option>
                                            <option value="no" <?php if($o_haul == 'no'){echo "selected='selected'";}?>>No</option>
                                            <option value="not sure" <?php if($o_haul == 'not sure'){echo "selected='selected'";}?>>Not Sure</option>
                                        </select>
                                    </p>
                                    <p><label>Load Size</label>
                                    	<select name="o_loadsize" class="post_loads_txtarea">
                                            <?php
                                                $i = 200;
                                                while($i <= 4000){?>
                                                    <option value="<?php echo $i;?>" <?php if($o_loadsize == $i){echo "selected='selected'";}?>><?php echo $i;?> c.f.</option>
                                            <?php
                                                    $i = $i + 100;
                                                }
                                            ?>
                                        </select>
                                    </p>                                    
                            </div>                            
                                
                            <h2>DESTINATION STATE</h2>
                               <div class="post_loads_div">                            	
                                    <p><label>Dest Zip Code</label><input name="d_zip" type="text" id="d_zip_code" class="post_loads_txtfld" autocomplete="off" value="<?php if(isset($d_zip)){ echo $d_zip;}?>"></p>
                                    <p><label>Dest City</label><input name="d_city" id="d_city" type="text" class="post_loads_txtfld" value="<?php if(isset($d_city)){ echo $d_city;}?>"></p>
                                    <p><label>State</label><input name="d_state" id="d_state" type="text" class="post_loads_txtfld" value="<?php if(isset($d_state)){ echo $d_state;}?>"></p>
                                    <p><label>Delivery To</label>
                                    	 <select name="d_delivary" class="post_loads_txtarea"> 
                                            <option value="residence" <?php if($d_delivary == 'residence'){echo "selected='selected'";}?>>Residence</option>
                                            <option value="warehouse" <?php if($d_delivary == 'warehouse'){echo "selected='selected'";}?>>Warehouse</option>
                                            <option value="storage" <?php if($d_delivary == 'storage'){echo "selected='selected'";}?>>Storage</option>
                                            <option value="shop/store" <?php if($d_delivary == 'shop/store'){echo "selected='selected'";}?>>Shop/Store</option>
                                        </select>	
                                    </p>
                                    <p><label>Date Load Available for Delivery</label><input name="d_date" autocomplete="off" readonly type="text" class="post_loads_txtfld3 datepicker" value="<?php if($d_date){ echo date('m-d-Y', strtotime($d_date));}?>" /></p>
                                    <p><label>COMMENTS</label><textarea name="d_comments" class="post_loads_txtarea2"><?php if(isset($d_comments)){ echo $d_comments;}?></textarea></p>
                                </div>
                            
                                <?php
                                    $query = "  SELECT 
                                                    pm.*, pa.* 
                                                FROM 
                                                    wp_post_package_master AS pm 
                                                INNER JOIN 
                                                    wp_user_postaccount AS pa 
                                                ON 
                                                    pm.id_post_package = pa.id_package 
                                                WHERE 
                                                    pa.id_user = $user_id 
                                                AND 
                                                    pa.expiry_date >= CURDATE() 
                                                AND 
                                                    pa.post_count > 0
                                                ORDER BY 
                                                    pa.expiry_date
                                                ASC
                                                ";
                                    $purchased_packages = $wpdb->get_results($query, ARRAY_A);
                                    if(!empty($purchased_packages)){
                                        foreach ($purchased_packages as $purchased_package) {?>
                                        <input type="hidden" name="purchased_package_id" id="purchased_package_id" value="<?php if(isset($purchased_package['id'])){echo $purchased_package['id'];}?>">
                                <?php
                                            break;
                                            }
                                        }
                                ?>
                             <input type="hidden" name="post_type" id="post_type" value="loads">
                             <input type="hidden" name="id" id="id" value="<?php if(isset($_REQUEST['post_id'])){echo $_REQUEST['post_id'];}?>">
                             <div class="button_div1">
                             	<input name="post_load_now" type="submit" value="POST LOAD NOW" class="button" >
                                <p>Thank you, go back to <a href="<?php echo home_url();?>/dashboard">main menu</a></p>
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