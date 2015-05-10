<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>
	 <div class="contain_main">
            	<div class="ctn_in">
				
			<?php while ( have_posts() ) : the_post(); ?>
				<?php //get_template_part( 'content', 'page' ); ?>
				<h1><?php the_title();?></h1>
				<div class="clear"></div>
                             <?php if ( has_post_thumbnail() ) { ?>				
				<p><?php the_post_thumbnail();?></p>
                               <?php } ?>
                                <?php 
                                    if(is_page('Post Packages')){
                                        if(!is_activated_post()){
                                            echo '<p class="err_msg">You have no post left in your account. Please purchase a post package to continue posting.</p>';
                                        }
                                    }
                                ?>
				<?php the_content();?>
				
			<?php endwhile; // end of the loop. ?>

		</div>
	</div>
<?php get_footer(); ?>