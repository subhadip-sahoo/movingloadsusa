  </div>
    </div>
	  <footer>
    	<div class="footer_top"></div>
		<div class="main_body">
        	<div class="main_body_width">
            	<div class="footer_nav">
                	<?php wp_nav_menu(array('theme_location'=>'primary','menu'=>'main-menu','container'=>'<div>','container_class'=>'footer_nav'));?>
                </div>
                <div class="copy"> 
                            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Copyright Sidebar') ) : ?>
                           <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>
    <!--footer-end-->
</div>
<!--wrapper-end-->

<?php wp_footer(); ?>
</body>
</html>