<?php
session_start();
/* Template Name: Dispatch board */
global $wpdb;$user_id=get_current_user_id();
if(!$user_id){
    wp_safe_redirect(home_url().'/login/');
    exit();
}
if(!is_activated_post()){
    wp_safe_redirect(site_url().'/post-packages/');
    exit();
}
$query = "  SELECT 
                            j.*, 
                            l.id, l.post_date, l.userid, l.o_state, l.d_state, l.o_date, l.d_date, 
                            l.o_zip, l.d_zip, l.o_city, l.d_city, l.o_haul, l.o_loading, l.o_loadsize, l.o_stairs, 
                            l.o_traileravlble, l.d_delivary, l.d_comments, NULL AS space_available, NULL AS trailer_size,
                            NULL AS comments,
                            w.*
            FROM 
                            wp_awarded_job AS j
            INNER JOIN 
                            wp_loads AS l
            ON 
                            j.id_post = l.id
            INNER JOIN 
                            wp_users AS w
            ON 
                            j.id_carrier = w.ID
            WHERE 
                            j.id_user = $user_id
            AND 
                            j.close_job = 0
            AND 
                            j.post_type = 'loads'

            UNION

            SELECT 
                            j.*,
                            s.id_space AS id, s.post_date, s.userid, s.o_state, s.d_state, s.o_date, s.d_date, 
                            NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, s.space_available, s.trailer_size, 
                            s.comments,
                            w.*
            FROM 
                            wp_awarded_job AS j
            INNER JOIN 
                            wp_spaces AS s
            ON 
                            j.id_post = s.id_space
            INNER JOIN 
                            wp_users AS w
            ON 
                            j.id_carrier = w.ID
            WHERE 
                            j.id_user = $user_id
            AND 
                            j.close_job = 0
            AND 
                            j.post_type = 'spaces'
            ORDER BY 
                            job_loading_date 
            DESC";
$get_jobs = $wpdb->get_results($query, ARRAY_A);
get_header(); ?>
       <div class="ctn">
            <!--contain-->
            <div class="contain_main">
            	<div class="ctn_in">
            	<h1>DISPATCH BOARD OPEN JOBS</h1>
                <div class="clear"></div>
			 <?php
                            if(isset($_SESSION['show_msg'])){
                        ?>
                        <span style=" color:green;"><?php echo $_SESSION['show_msg'];?></span>
                        <?php 
                                unset($_SESSION['show_msg']);
                            } 
                        ?>
                    <div class="add_closed_div">
                       
                    	<a href="<?php echo home_url();?>/closed-job/"><input name="" type="button" value="VIEW CLOSED JOBS" class="btn_2 nw1" ></a>
                        <a href="<?php echo home_url();?>/view-open-post/"><input name="" type="button" value="ADD NEW JOB " class="btn_2 nw1"></a>
                    </div>
                    <div class="table_div3">
                	<table class="table_main">
                            <tr class="fst_tr">
                              <td>Cust Job #:</td>
                              <td>Cust Name</td>
                              <td>P/U Date</td>
                              <td>Carrier</td>
                              <td>CFT P/U</td>
                              <td>From</td>
                              <td>To</td>
                              <td class="more_div">View Details</td>
                            </tr>
                             <?php 
                                $i = 0;
                                if(!empty($get_jobs)){
                                    foreach ($get_jobs as $get_job) {
                                        $class = ($i % 2 == 0)?'tr_3rd':'tr_2nd';
                                        $expected_url = ($get_job['post_type'] == 'loads')?home_url().'/add-job/':home_url().'/add-job-space/';
                            ?>
                        <tr class="<?php echo $class;?>">
                          <td><a href="<?php echo $expected_url?>?job_id=<?php echo $get_job['id_job'];?>&post_id=<?php echo $get_job['id_post'];?>"><?php echo $get_job['cus_job_id'];?></a></td>
                          <td><a href="<?php echo $expected_url?>?job_id=<?php echo $get_job['id_job'];?>&post_id=<?php echo $get_job['id_post'];?>"><?php echo $get_job['cus_name'];?></a></td>
                          <td><a href="<?php echo $expected_url?>?job_id=<?php echo $get_job['id_job'];?>&post_id=<?php echo $get_job['id_post'];?>"><?php echo date('m-d-Y',  strtotime($get_job['job_loading_date']));?></a></td>
                          <td><a href="<?php echo $expected_url?>?job_id=<?php echo $get_job['id_job'];?>&post_id=<?php echo $get_job['id_post'];?>"><?php echo $get_job['display_name'];?></a></td>
                          <td><a href="<?php echo $expected_url?>?job_id=<?php echo $get_job['id_job'];?>&post_id=<?php echo $get_job['id_post'];?>"><?php echo $get_job['cft_pu'];?></a></td>
                          <td><a href="<?php echo $expected_url?>?job_id=<?php echo $get_job['id_job'];?>&post_id=<?php echo $get_job['id_post'];?>"><?php echo $get_job['o_state'];?></a></td>
                          <td><a href="<?php echo $expected_url?>?job_id=<?php echo $get_job['id_job'];?>&post_id=<?php echo $get_job['id_post'];?>"><?php echo $get_job['d_state'];?></a></td>
                          <td><a href="<?php echo $expected_url?>?job_id=<?php echo $get_job['id_job'];?>&post_id=<?php echo $get_job['id_post'];?>"><input name="more_info" type="button" value="More info" class="tbl_read_more_btn"></a></td>
                        </tr>
                       <?php
                            $i++;}
                        }else{
                    ?>
                        <tr class="tr_3rd">
                          <td colspan="8" align="center">No job to display</td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
<!--cnt_div-end-->
</div>
 <?php get_footer(); ?>