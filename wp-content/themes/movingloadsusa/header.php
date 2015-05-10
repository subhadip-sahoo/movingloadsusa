<?php ?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>><![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>><![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]>
<!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=0" >
    <meta name="viewport" content="width=device-width" />
    <title><?php if( is_404() ) echo '404 message goes here | ';else wp_title( '|', true, 'right' );?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link href="<?php echo get_template_directory_uri(); ?>/style.css" rel="stylesheet" type="text/css" media="all">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/meanmenu.css" media="all" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/extra_style.css" media="all" />
    <!--for-nav-->
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery-1.9.1.js"></script> 
    <script src="<?php echo get_template_directory_uri(); ?>/js/jquery.meanmenu.js"></script> 
    <script>jQuery(document).ready(function () {    jQuery('header nav').meanmenu();});</script>

    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" /><?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
    <!--[if lt IE 9]>
    <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript">
    </script><![endif]-->
        <?php wp_head(); ?>
        <?php global $wpdb ;$user_id=get_current_user_id();?>
</head>

<body <?php body_class();?>>
<div class="wrapper">	
<div class="main_body">    	
<div class="main_body_width">            
<!--header-->            
<header>            	
<!--logo-->                

<div class="logo"><a href="<?php echo home_url();?>">
    <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Logo') ) : ?>
       <?php endif; ?>
</a></div>                
<!--logo-->                
<!--right-->                

<div class="header_right">
     <?php if(!$user_id){?>               	
            <div class="log_main_div">
                <img src="<?php echo get_template_directory_uri(); ?>/images/sign_up_icon.png" alt="">                        
                <p>Sign Up For FREE!</p>                        
                <img src="<?php echo get_template_directory_uri(); ?>/images/icon_2.png" alt="">                        
                <div class="link_div"><a href="<?php echo home_url()?>/registration">Click Here</a></div>						                    
            </div>					
                <?php }?>					                    

            <div class="log_main_div">						
                <?php if(!$user_id){?>                    	
                    <img src="<?php echo get_template_directory_uri(); ?>/images/log_in_icon.png" alt="">													
                    <p>Already Registered?</p>                       
                    <img src="<?php echo get_template_directory_uri(); ?>/images/icon_2.png" alt="">                        

                    <div class="link_div">	
                        <a href="<?php echo home_url()?>/login">Login</a>																
                    </div>	
                <?php }else { ?>	
                    <img src="<?php echo get_template_directory_uri(); ?>/images/log_in_icon.png" alt="">								
                    <?php $current_user = wp_get_current_user();?>					
                    <p>Welcome <?php if($current_user->first_name){ echo  $current_user->first_name; } else { echo $current_user->user_login; } ?></p>                <img src="<?php echo get_template_directory_uri(); ?>/images/icon_2.png" alt="">                        
                <div class="link_div">													
                    <a href="<?php echo wp_logout_url(home_url().'/login/' ); ?>">Logout</a>																
                </div>	
                    <?php }?>                    
            </div>   
            <?php if($user_id){?>                                

            <div class="log_main_div">
                <a href="<?php echo site_url();?>/dashboard/">

            <p>Go back to Main Menu</p></a>                                      						                            
            </div>                  
    <?php }?>                                                               
</div>                
<!--right-end-->                
<!--nav-->                	

<div class="nav_menu">		
    <nav>       
    <?php wp_nav_menu(array('theme_location'=>'primary','menu'=>'main-menu','container'=>'<div>','container_class'=>'nav_menu'));?>													
    </nav>                                                
</div>                 
<!--nav-end-->            
</header>