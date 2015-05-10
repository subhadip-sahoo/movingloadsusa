<?php
/* Template Name:Dashboard */
 if(!is_user_logged_in()){
    wp_safe_redirect(home_url());
    exit();
 }
get_header(); 
?>
<div class="ctn">
    <!--contain-->
    <div class="contain_main">
        <div class="ctn_in">
            <h1><img src="<?php echo get_template_directory_uri();?>/images/dashboard_icon.png" alt="">DASHBOARD MAIN MENU</h1>
            <div class="clear"></div>
            <form>
                <div class="state_table_div">
                    <div class="dashboard_maindiv">
                        <div class="dashboard_btn"><input name="" type="button" value="POST LOADS" class="db_btn" onclick="window.location.href='<?php echo home_url();?>/post-loads'"></div>
                        <div class="dashboard_btn"><input name="" type="button" value="FIND LOADS" class="db_btn" onclick="window.location.href='<?php echo home_url();?>/find-loads'"></div>
                        <div class="dashboard_btn"><input name="" type="button" value="POST SPACE" class="db_btn" onclick="window.location.href='<?php echo home_url();?>/post-space'"></div>
                        <div class="dashboard_btn"><input name="" type="button" value="FIND SPACE" class="db_btn" onclick="window.location.href='<?php echo home_url();?>/find-space'"></div>
                    </div>
                </div>
                <div class="state_table_div">
                    <div class="dashboard_maindiv">
                        <div class="dashboard_btn"><input name="" type="button" value="MY ACCOUNT" class="db_btn" onclick="window.location.href='<?php echo home_url();?>/my-account'"></div>
                        <div class="dashboard_btn"><input name="" type="button" value="VIEW OPEN POSTS" class="db_btn" onclick="window.location.href='<?php echo home_url();?>/view-open-post'"></div>
                        <div class="dashboard_btn"><input name="" type="button" value="DISPATCH BOARD" class="db_btn" onclick="window.location.href='<?php echo home_url();?>/dispatch-board'"></div>
                        <!--<div class="dashboard_btn"><input name="" type="button" value="POST PACKAGES" class="db_btn" onclick="window.location.href='<?php //echo home_url();?>/post-packages'"></div>-->
                    </div>
              </div>
            </form>
        </div>
    </div>
<!--cnt_div-end-->
</div>
<?php get_footer(); ?>