<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

	   <div class="contain_main">
            	<div class="ctn_in">		
					
					<h1><?php _e( 'Page not found', 'movingloadsusa' ); ?></h1>		
					<div class="clear"></div>
					<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for.', 'movingloadsusa' ); ?></p>
					<?php// get_search_form(); ?>
		

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>