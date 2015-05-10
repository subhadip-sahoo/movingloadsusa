<?php
/* Template Name:home */
global $wpdb;
get_header(); ?> 
<?php $posts=query_posts($query_string); ?>
<?php if (have_posts()) : ?>
<?php while (have_posts()) :the_post(); ?>   
    <div class="banner_main">
        <?php echo get_the_post_thumbnail($posts->ID,"full");?>
    </div>
            <!--banner-end-->
            <div class="ctn">
            <!--contain-->
            <div class="contain_main">
            	<div class="contain_img_div">
                    <?php echo $find_space = get_post_meta($post->ID,"Find space now",true);?>
                </div>
                <div class="contain_img_div">
                        <?php echo $find_loads = get_post_meta($post->ID,"Find loads now",true);?>
                </div>
                <div class="contain_text_div">
                	<?php echo get_the_excerpt();?>
               		<div class="read_more_nav"><a href="<?php get_permalink($id);?>">Read More</a></div>
                </div>
            </div>
			<?php endwhile; else: ?>
			<?php _e('Sorry, no posts matched your criteria.'); ?>
			<?php endif; ?>
            <!--contain-end-->
            
            <!--cnt_div_2-->
            <div class="ctn_div_2">
            	<div class="ctn_div_2_h2_head">
            		<h2>MOST RECENT ACTIVITY</h2>
                	<span><a href="<?php echo site_url();?>/view-open-post/">View All</a></span>
                </div>
                <div class="table_div">
                	<table class="table_main">
                   
                      <tr class="fst_tr">
                        <td>Origin <br>State</td>
                        <td>P/U Date</td>
                        <td>Dest.<br> State</td>
                        <td>Delivery <br>Date</td>
                        <td>Load SIZE<br>(cu. ft)</td>
                        <td>Vendor<br> Rating</td>
                        <td class="more_div">More <br>Info</td>
                      </tr>
<?php
    $query = "      SELECT 
                        l.id, l.post_type, l.post_date, l.userid, l.o_state, l.d_state, l.o_date, l.d_date, 
                        l.o_zip, l.d_zip, l.o_city, l.d_city, l.o_haul, l.o_loading, l.o_loadsize, l.o_stairs, 
                        l.o_traileravlble, l.d_delivary, l.d_comments, NULL AS space_available, NULL AS trailer_size,
                        NULL AS comments
                    FROM 
                        wp_loads AS l
                    INNER JOIN 
                        wp_user_postaccount AS p 
                    ON 
                        l.purchased_package_id = p.id 
                    WHERE 
                        l.post_expiry_date >= CURDATE() 
                    AND 
                        p.expiry_date >= CURDATE()

                    UNION  

                    SELECT 
                        s.id_space AS id, s.post_type, s.post_date, s.userid, s.o_state, s.d_state, s.o_date, s.d_date, 
                        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, s.space_available, s.trailer_size, 
                        s.comments
                    FROM 
                        wp_spaces AS s
                    INNER JOIN 
                        wp_user_postaccount AS p 
                    ON 
                        s.purchased_package_id = p.id 
                    WHERE 
                        s.post_expiry_date >= CURDATE() 
                    AND 
                        p.expiry_date >= CURDATE()
                    ORDER BY
                        post_date
                    DESC LIMIT 0, 5";
    $myrows_wp_loads = $wpdb->get_results($query);
    $i = 0;
    if( !empty($myrows_wp_loads) ) { 
        foreach($myrows_wp_loads as $loads_info){
            $class = ($i % 2 == 0)?'tr_2nd':'tr_3rd';
			
$originalDate = $loads_info->o_date;
$o_date = date("m-d-Y", strtotime($originalDate));

$originalDatea = $loads_info->d_date;
$d_date = date("m-d-Y", strtotime($originalDatea));          
?>
                      <tr class="<?php echo $class;?>">
                        <td><a href="<?php echo site_url();?>/view-open-single-post/?id=<?php echo $loads_info->id; ?>&pt=<?php echo $loads_info->post_type; ?>"><?php echo $loads_info->o_state;?></a></td>
                        <td><a href="<?php echo site_url();?>/view-open-single-post/?id=<?php echo $loads_info->id; ?>&pt=<?php echo $loads_info->post_type; ?>"><?php echo $o_date;?></a></td>
                        <td><a href="<?php echo site_url();?>/view-open-single-post/?id=<?php echo $loads_info->id; ?>&pt=<?php echo $loads_info->post_type; ?>"><?php echo $loads_info->d_state;?></a></td>
                        <td><a href="<?php echo site_url();?>/view-open-single-post/?id=<?php echo $loads_info->id; ?>&pt=<?php echo $loads_info->post_type; ?>"><?php echo $d_date;?></a></td>
                        <td><a href="<?php echo site_url();?>/view-open-single-post/?id=<?php echo $loads_info->id; ?>&pt=<?php echo $loads_info->post_type; ?>"><?php echo ($loads_info->post_type == 'loads')?$loads_info->o_loadsize:$loads_info->trailer_size;?></a></td>
                        <td>
                            <?php echo print_star_ratings($loads_info->post_type, $loads_info->id); ?>
                        </td>
						
                        <td>
                            <a href="<?php echo site_url();?>/view-open-single-post/?id=<?php echo $loads_info->id; ?>&pt=<?php echo $loads_info->post_type; ?>">
                            <input name="" type="button" value="Read more" class="tbl_read_more_btn" >
                            </a>
                        </td>
                      </tr>
                <?php
                      $i++;  }
                    }else{
                ?>
                    <tr class="tr_2nd">
                        <td colspan="7">No Results Found.</td>
                    </tr>          
                <?php
                    }
                ?>
                      
                </table>
                </div>
                <div class="add_div"><img src="<?php echo get_template_directory_uri();?>/images/add.png" alt=""></div>
            </div>
            <!--cnt_div_2-end-->
            </div>
<?php get_footer(); ?>