<?php
session_start();
/* Template Name: Payment Results */
if(!isset($_SESSION['status'])){
    wp_safe_redirect(site_url().'/my-account');
    exit();
}
get_header();
?>
<div class="ctn">
    <!--contain-->
    <div class="contain_main">
        <div class="ctn_in">
            <h1>PAYMENT STATUS</h1>
            <div class="clear"></div>
		<div class="pay_res">
            <?php
                if(isset($_SESSION['status'])){
                    echo $_SESSION['status'];
                    unset($_SESSION['status']);
                }
            ?>
		</div>
        </div>
    </div>
<!--cnt_div-end-->
</div>
<?php get_footer();?>