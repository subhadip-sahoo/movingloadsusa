<?php
// Set up the content width value based on the theme's design and stylesheet.
if ( ! isset( $content_width ) )
	$content_width = 625;


function twentytwelve_setup() {
	/*
	 * Makes Twenty Twelve available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Twenty Twelve, use a find and replace
	 * to change 'twentytwelve' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'twentytwelve', get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// This theme supports a variety of post formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'status' ) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'movingloadsusa' ) );

	/*
	 * This theme supports custom background color and image,
	 * and here we also set up the default background color.
	 */
	add_theme_support( 'custom-background', array(
		'default-color' => 'e6e6e6',
	) );

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 624, 9999 ); // Unlimited height, soft crop
}
add_action( 'after_setup_theme', 'twentytwelve_setup' );

/**
 * Add support for a custom header image.
 */
require( get_template_directory() . '/inc/custom-header.php' );

/**
 * Return the Google font stylesheet URL if available.
 *
 * The use of Open Sans by default is localized. For languages that use
 * characters not supported by the font, the font can be disabled.
 *
 * @since Twenty Twelve 1.2
 *
 * @return string Font stylesheet or empty string if disabled.
 */
function twentytwelve_get_font_url() {
	$font_url = '';

	/* translators: If there are characters in your language that are not supported
	 * by Open Sans, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off', 'twentytwelve' ) ) {
		$subsets = 'latin,latin-ext';

		/* translators: To add an additional Open Sans character subset specific to your language,
		 * translate this to 'greek', 'cyrillic' or 'vietnamese'. Do not translate into your own language.
		 */
		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)', 'twentytwelve' );

		if ( 'cyrillic' == $subset )
			$subsets .= ',cyrillic,cyrillic-ext';
		elseif ( 'greek' == $subset )
			$subsets .= ',greek,greek-ext';
		elseif ( 'vietnamese' == $subset )
			$subsets .= ',vietnamese';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => 'Open+Sans:400italic,700italic,400,700',
			'subset' => $subsets,
		);
		$font_url = add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" );
	}

	return $font_url;
}

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since Twenty Twelve 1.0
 *
 * @return void
 */

/**
 * Filter TinyMCE CSS path to include Google Fonts.
 *
 * Adds additional stylesheets to the TinyMCE editor if needed.
 *
 * @uses twentytwelve_get_font_url() To get the Google Font stylesheet URL.
 *
 * @since Twenty Twelve 1.2
 *
 * @param string $mce_css CSS path to load in TinyMCE.
 * @return string Filtered CSS path.
 */
function twentytwelve_mce_css( $mce_css ) {
	$font_url = twentytwelve_get_font_url();

	if ( empty( $font_url ) )
		return $mce_css;

	if ( ! empty( $mce_css ) )
		$mce_css .= ',';

	$mce_css .= esc_url_raw( str_replace( ',', '%2C', $font_url ) );

	return $mce_css;
}
add_filter( 'mce_css', 'twentytwelve_mce_css' );

function twentytwelve_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'twentytwelve' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'twentytwelve_wp_title', 10, 2 );

/**
 * Filter the page menu arguments.
 *
 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_page_menu_args( $args ) {
	if ( ! isset( $args['show_home'] ) )
		$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentytwelve_page_menu_args' );

/**
 * Register sidebars.
 *
 * Registers our main widget area and the front page widget areas.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'twentytwelve' ),
		'id' => 'sidebar-1',
		'description' => __( 'Appears on posts and pages except the optional Front Page template, which has its own widgets', 'twentytwelve' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
        register_sidebar( array(
                'name' => __( 'Footer Copyright Sidebar', 'twentytwelve' ),
                'id' => 'foooter_copyright',
                'description' => __( 'Appears on posts and pages except the optional Front Page template, which has its own widgets', 'twentytwelve' ),
                'before_widget' => '',
                'after_widget' => '',
                'before_title' => '',
                'after_title' => '',
        ) );
        register_sidebar( array(
                'name' => __( 'Logo', 'movingloadsusa' ),
                'id' => 'header_logo',
                'description' => '',
                'before_widget' => '',
                'after_widget' => '',
                'before_title' => '',
                'after_title' => '',
        ) );
}
add_action( 'widgets_init', 'twentytwelve_widgets_init' );

if ( ! function_exists( 'twentytwelve_content_nav' ) ) :
/**
 * Displays navigation to next/previous pages when applicable.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_content_nav( $html_id ) {
	global $wp_query;

	$html_id = esc_attr( $html_id );

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentytwelve' ); ?></h3>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentytwelve' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) ); ?></div>
		</nav><!-- #<?php echo $html_id; ?> .navigation -->
	<?php endif;
}
endif;

if ( ! function_exists( 'twentytwelve_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentytwelve_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Twelve 1.0
 *
 * @return void
 */
function twentytwelve_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'twentytwelve' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<header class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 44 );
					printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',
						get_comment_author_link(),
						// If current post author is also comment author, make it known visually.
						( $comment->user_id === $post->post_author ) ? '<span>' . __( 'Post author', 'twentytwelve' ) . '</span>' : ''
					);
					printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'twentytwelve' ), get_comment_date(), get_comment_time() )
					);
				?>
			</header><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentytwelve' ); ?></p>
			<?php endif; ?>

			<section class="comment-content comment">
				<?php comment_text(); ?>
				<?php edit_comment_link( __( 'Edit', 'twentytwelve' ), '<p class="edit-link">', '</p>' ); ?>
			</section><!-- .comment-content -->

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'twentytwelve' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;

if ( ! function_exists( 'twentytwelve_entry_meta' ) ) :
/**
 * Set up post entry meta.
 *
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own twentytwelve_entry_meta() to override in a child theme.
 *
 * @since Twenty Twelve 1.0
 *
 * @return void
 */
function twentytwelve_entry_meta() {
	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'twentytwelve' ) );

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'twentytwelve' ) );

	$date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>',
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'twentytwelve' ), get_the_author() ) ),
		get_the_author()
	);

	// Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
	if ( $tag_list ) {
		$utility_text = __( 'This entry was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.', 'twentytwelve' );
	} elseif ( $categories_list ) {
		$utility_text = __( 'This entry was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.', 'twentytwelve' );
	} else {
		$utility_text = __( 'This entry was posted on %3$s<span class="by-author"> by %4$s</span>.', 'twentytwelve' );
	}

	printf(
		$utility_text,
		$categories_list,
		$tag_list,
		$date,
		$author
	);
}
endif;

/**
 * Extend the default WordPress body classes.
 *
 * Extends the default WordPress body class to denote:
 * 1. Using a full-width layout, when no active widgets in the sidebar
 *    or full-width template.
 * 2. Front Page template: thumbnail in use and number of sidebars for
 *    widget areas.
 * 3. White or empty background color to change the layout and spacing.
 * 4. Custom fonts enabled.
 * 5. Single or multiple authors.
 *
 * @since Twenty Twelve 1.0
 *
 * @param array $classes Existing class values.
 * @return array Filtered class values.
 */
function twentytwelve_body_class( $classes ) {
	$background_color = get_background_color();
	$background_image = get_background_image();

	if ( ! is_active_sidebar( 'sidebar-1' ) || is_page_template( 'page-templates/full-width.php' ) )
		$classes[] = 'full-width';

	if ( is_page_template( 'page-templates/front-page.php' ) ) {
		$classes[] = 'template-front-page';
		if ( has_post_thumbnail() )
			$classes[] = 'has-post-thumbnail';
		if ( is_active_sidebar( 'sidebar-2' ) && is_active_sidebar( 'sidebar-3' ) )
			$classes[] = 'two-sidebars';
	}

	if ( empty( $background_image ) ) {
		if ( empty( $background_color ) )
			$classes[] = 'custom-background-empty';
		elseif ( in_array( $background_color, array( 'fff', 'ffffff' ) ) )
			$classes[] = 'custom-background-white';
	}

	// Enable custom font class only if the font CSS is queued to load.
	if ( wp_style_is( 'twentytwelve-fonts', 'queue' ) )
		$classes[] = 'custom-font-enabled';

	if ( ! is_multi_author() )
		$classes[] = 'single-author';

	return $classes;
}
add_filter( 'body_class', 'twentytwelve_body_class' );

/**
 * Adjust content width in certain contexts.
 *
 * Adjusts content_width value for full-width and single image attachment
 * templates, and when there are no active widgets in the sidebar.
 *
 * @since Twenty Twelve 1.0
 *
 * @return void
 */
function twentytwelve_content_width() {
	if ( is_page_template( 'page-templates/full-width.php' ) || is_attachment() || ! is_active_sidebar( 'sidebar-1' ) ) {
		global $content_width;
		$content_width = 960;
	}
}
add_action( 'template_redirect', 'twentytwelve_content_width' );

/**
 * Register postMessage support.
 *
 * Add postMessage support for site title and description for the Customizer.
 *
 * @since Twenty Twelve 1.0
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 * @return void
 */
function twentytwelve_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'twentytwelve_customize_register' );

/**
 * Enqueue Javascript postMessage handlers for the Customizer.
 *
 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
 *
 * @since Twenty Twelve 1.0
 *
 * @return void
 */
function twentytwelve_customize_preview_js() {
	wp_enqueue_script( 'twentytwelve-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20130301', true );
}
add_action( 'customize_preview_init', 'twentytwelve_customize_preview_js' );
/*add page excerpt*/
add_post_type_support( 'page', 'excerpt');
/*Admin bar only for admin only*/
add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
      show_admin_bar(false);
    }
}
function enqueue_custom_scripts_styles(){
    wp_register_style('mlusa_datepicker_ui_css', '//code.jquery.com/ui/1.10.4/themes/humanity/jquery-ui.css', TRUE);
    wp_enqueue_style('mlusa_datepicker_ui_css');
    wp_register_style('mlusa_popup_css', get_template_directory_uri().'/css/popup_css.css', TRUE);
    wp_enqueue_style('mlusa_popup_css');
    wp_register_style('mlusa_close_job_css', get_template_directory_uri().'/css/close_job.css', TRUE);
    wp_enqueue_style('mlusa_close_job_css');
    wp_register_style('mlusa_rateit_css', get_template_directory_uri().'/src/rateit.css', TRUE);
    wp_enqueue_style('mlusa_rateit_css');
    wp_register_style('mlusa_bigstars_css', get_template_directory_uri().'/content/bigstars.css', TRUE);
    wp_enqueue_style('mlusa_bigstars_css');
    wp_register_script('mlusa_datepicker_ui_js', '//code.jquery.com/ui/1.10.4/jquery-ui.js', TRUE);
    wp_enqueue_script('mlusa_datepicker_ui_js');
    wp_register_script('mlusa_popup_js', get_template_directory_uri().'/js/pop_up.js', TRUE);
    wp_enqueue_script('mlusa_popup_js');
    wp_register_script('mlusa_close_job_js', get_template_directory_uri().'/js/close_job.js', TRUE);
    wp_enqueue_script('mlusa_close_job_js');
    wp_register_script('mlusa_rateit_js', get_template_directory_uri().'/src/jquery.rateit.js', TRUE);
    wp_enqueue_script('mlusa_rateit_js');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts_styles');
/******login with email******/
function login_with_email_address($username) {
	$user = get_user_by_email($username);
	if(!empty($user->user_login))
		$username = $user->user_login;
	return $username;
}
add_action('wp_authenticate','login_with_email_address');

function mlusa_new_user_reg($usre_id){
    global $wpdb;
    update_user_meta($usre_id,'account_status', 0);
    $key = wp_generate_password( 20, false );
    do_action( 'retrieve_password_key', $usre_id, $key );
    $wpdb->update( $wpdb->users, array( 'user_activation_key' => $key ), array( 'ID' => $usre_id ) );
}
add_action('user_register', 'mlusa_new_user_reg', 10, 1);

function mlusa_login_auth($user, $password){
    $errors = new WP_Error();
    $userdata  = get_userdata( $user->ID );
    if(implode(', ', $userdata->roles) != 'administrator'){
        if(get_user_meta($user->ID, 'account_status', true) == 1){
            if(get_user_meta($user->ID, 'account_expiry', true) >= date('Y-m-d H:i:s')){
                return $user;
            }else{
                $errors->add('account_expired', __('Your account has been expired. Please be a paid member to continue.'));
                $errors->add('user_login', __($user->user_login));
                $errors->add('user_email', __($user->user_email));
                $errors->add('user_activation_key', __($user->user_activation_key));
                return $errors;
            }
        }
        else{
            $errors->add('verification_failed', __('Your account is not verified yet. Please check your mail to verify.'));
            return $errors;
        }
    }else{
        return $user;
    }
}
add_filter('wp_authenticate_user', 'mlusa_login_auth',10,2);
function currentPageURL() {
    $curpageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {$curpageURL.= "s";}
    $curpageURL.= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $curpageURL.= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $curpageURL.= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return untrailingslashit($curpageURL);
}
function parse_address_google($address) {
	$url = 'http://maps.googleapis.com/maps/api/geocode/json?region=US&sensor=false&address='.urlencode($address);
	$results = json_decode(file_get_contents($url),1);
	$parts = array(
	  'address'=>array('street_number','route'),
	  'locality'=>array('locality'),
	  'sublocality'=>array('sublocality'),
	  'neighborhood'=>array('neighborhood'),
	  'county'=>array('administrative_area_level_2'),
	  'sub_county'=>array('administrative_area_level_3'),
	  'state'=>array('administrative_area_level_1'),
	  'country'=>array('country'),
	  'zip'=>array('postal_code'),
	);
	if (!empty($results['results'][0]['address_components'])) {
	  $ac = $results['results'][0]['address_components'];
	  foreach($parts as $need=>&$types) {
		foreach($ac as &$a) {
		  if (in_array($a['types'][0],$types)) $address_out[$need] = $a['long_name'];
		  elseif (empty($address_out[$need])) $address_out[$need] = '';
		}
	  }
	} else echo 'empty results';
	return $address_out;
}
function is_activated_post(){
    global $user_ID, $wpdb;
    $posts = $wpdb->get_results("SELECT * FROM wp_user_postaccount WHERE id_user = $user_ID AND expiry_date >= NOW()", ARRAY_A);
    $post_count = 0;
    if($wpdb->num_rows > 0){
        foreach ($posts as $post) {
            $post_count = $post_count + $post['post_count'];
        }
    }
    if($post_count == 0){
        return false;
    }else{
        return true;
    }
}
function mlusa_get_option($arg){
    global $wpdb;
    $get_options = get_option('mlusa_custom_options');
    return $get_options[$arg];
}
function array_push_associative(&$arr) {
    $ret = 0;
    $args = func_get_args();
    foreach ($args as $arg) {
       if (is_array($arg)) {
           foreach ($arg as $key => $value) {
               $arr[$key] = $value;
               $ret++;
           }
       }else{
           $arr[$arg] = "";
       }
    }
    return $ret;
}
function is_dot_exists($dot){
    global $wpdb, $user_ID;
    $dot_exists = FALSE;
    $get_dots = $wpdb->get_results("SELECT * FROM `wp_usermeta` WHERE `meta_key` = 'dot' AND `meta_value` = '$dot'", ARRAY_A);
    if($wpdb->num_rows == 1){
        foreach ($get_dots as $get_dot) {
            if($get_dot['user_id'] == $user_ID){
                $dot_exists = FALSE;
            }else{
                $dot_exists = TRUE;
            }
        }
    }
    else if($wpdb->num_rows < 1){
        $dot_exists = FALSE;
    }
    else if($wpdb->num_rows > 1){
        $dot_exists = TRUE;
    }
    if($dot_exists == FALSE){
        return TRUE;
    }else{
        return FALSE;
    }
}
function is_current_user_post($post_id,$table_name){
    global $wpdb, $user_ID;
    $valid = FALSE;
    $wpdb->get_results("SELECT * FROM $table_name WHERE id = $post_id AND userid = $user_ID");
    if($wpdb->num_rows == 1){
        $valid = TRUE;
    }
    if($valid == TRUE){
        return TRUE;
    }else {
        return FALSE;
    }
}
function add_new_dot($dot){
    global $wpdb, $user_ID;
    $dot_exists = FALSE;
    $get_dots = $wpdb->get_results("SELECT * FROM `wp_usermeta` WHERE `meta_key` = 'dot' AND `meta_value` = '$dot'", ARRAY_A);
    if($wpdb->num_rows > 0){
        $dot_exists = TRUE;
    }
    if($dot_exists == FALSE){
        return TRUE;
    }else{
        return $get_dots;
    }
}
function autocomplete_carrier_list() {
    global $wpdb;
    $carrier_list = array();
    $get_carriers = $wpdb->get_results("SELECT * FROM wp_usermeta WHERE meta_key = 'dot' ORDER BY umeta_id ASC", ARRAY_A);
    if($wpdb->num_rows > 0){
        foreach ($get_carriers as $get_carrier) {
            $first_name = get_user_meta($get_carrier['user_id'], 'first_name', true);
            $last_name = get_user_meta($get_carrier['user_id'], 'last_name', true);
            array_push($carrier_list, array("id"=>$get_carrier['user_id'], "label"=>$get_carrier['meta_value'].' | '.$first_name.' '.$last_name, "value" => strip_tags($get_carrier['meta_value'])));
        }
    }
    return $carrier_list;
}
function is_current_user_job($job_id){
    global $wpdb, $user_ID;
    $valid = FALSE;
    $wpdb->get_results("SELECT * FROM wp_awarded_job WHERE id_job = $job_id AND id_user = $user_ID OR id_carrier = $user_ID");
    if($wpdb->num_rows == 1){
        $valid = TRUE;
    }
    if($valid == TRUE){
        return TRUE;
    }else {
        return FALSE;
    }
}
function is_current_user_spaces($post_id,$table_name){
    global $wpdb, $user_ID;
    $valid = FALSE;
    $wpdb->get_results("SELECT * FROM $table_name WHERE id_space = $post_id AND userid = $user_ID");
    if($wpdb->num_rows == 1){
        $valid = TRUE;
    }
    if($valid == TRUE){
        return TRUE;
    }else {
        return FALSE;
    }
}
//function to return the pagination string
function getPaginationString($page = 1, $totalitems, $limit = 10, $adjacents = 1, $targetpage = "/", $pagestring = "?page="){	
    global $wpdb; $user_id = get_current_user_id();
	//defaults
	if(!$adjacents) $adjacents = 1;
	if(!$limit) $limit = 10;
	if(!$page) $page = 1;
	if(!$targetpage) $targetpage = "/";
	
	//other vars
	$prev = $page - 1;									//previous page is page - 1
	$next = $page + 1;									//next page is page + 1
	$lastpage = ceil($totalitems / $limit);				//lastpage is = total items / items per page, rounded up.
	$lpm1 = $lastpage - 1;								//last page minus 1
        $offset = ($page - 1) * $limit;
	
	/* 
		Now we apply our rules and draw the pagination object. 
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/

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
                        l.userid = $user_id
                    AND 
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
                        s.userid = $user_id
                    AND 
                        s.post_expiry_date >= CURDATE() 
                    AND 
                        p.expiry_date >= CURDATE()
                    ORDER BY
                        post_date
                    DESC LIMIT $offset, $limit";
    $myrows_wp_loads = $wpdb->get_results($query);
    if( !empty($myrows_wp_loads) ) { 
        foreach($myrows_wp_loads as $loads_info){
            $expected_url = '';
            $display_edit = FALSE;
            if($loads_info->userid == $user_id){
                $display_edit = TRUE;
                if($loads_info->post_type == 'loads'){
                    $expected_url .= site_url()."/post-loads/?post_id=$loads_info->id";
                }
                else{
                   $expected_url .= site_url()."/post-space/?post_id=$loads_info->id"; 
                }
            }
        ?>
        <h2 <?php if($loads_info->post_type=='loads'){ ?> class="loads_cls" <?php } if($loads_info->post_type=='spaces'){ ?>class="spaces_cls"  <?php } ?> ><?php echo ucwords($loads_info->post_type);?>
            <?php
                if($display_edit == TRUE){
            ?>
                <span><a href="<?php echo $expected_url;?>" title="Edit">Edit</a><img src="<?php echo get_template_directory_uri();?>/images/edit_icon_1.png" alt=""></span>
            <?php
                }
            ?>
        </h2>  
    <?php if($loads_info->post_type == 'loads'){?>
        <div class="open_post_ul_div">
            <ul>
                <li>
                    <div class="open_post_li_width">Zip Code:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->o_zip; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">City:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->o_city; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">State:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->o_state; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Loading From:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->o_loading; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Date Load Available for Pickup:</div>
                    <div class="open_post_li_width2"><?php echo date('m-d-Y', strtotime($loads_info->o_date)); ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Stairs:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->o_stairs; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">53' Trailer Available:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->o_traileravlble; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Long Haul:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->o_haul; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Load Size:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->o_loadsize; ?> c.f. </div>
                </li>
            </ul>
            <div class="open_post_ul_bottom"></div>
            <ul>
                <li>
                    <div class="open_post_li_width">Dest Zip Code:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->d_zip; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Dest City:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->d_city; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">State:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->d_state; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Delivery To:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->d_delivary; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Date Load Available for Delivery:</div>
                    <div class="open_post_li_width2"><?php echo date('m-d-Y', strtotime($loads_info->d_date)); ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Comments:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->d_comments; ?></div>
                </li>
            </ul>
            <div class="open_post_ul_bottom"><a href="<?php echo site_url();?>/add-job/?post_id=<?php echo $loads_info->id;?>"><input name="award_job" type="button"  class="btn_4" <?php if($loads_info->post_type=='loads'){ ?> value="Award Load" <?php } ?>></a></div>
        </div>
    <?php }?>
    <?php if($loads_info->post_type == 'spaces'){?>
        <div class="open_post_ul_div">
            <ul>
                <li>
                    <div class="open_post_li_width">From Date:</div>
                    <div class="open_post_li_width2"><?php echo date('m-d-Y', strtotime($loads_info->o_date)); ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">To Date:</div>
                    <div class="open_post_li_width2"><?php echo date('m-d-Y', strtotime($loads_info->d_date)); ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Space Available:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->space_available; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Trailer Size:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->trailer_size; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Origin State:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->o_state; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Destination State:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->d_state; ?></div>
                </li>
                <li>
                    <div class="open_post_li_width">Comments:</div>
                    <div class="open_post_li_width2"><?php echo $loads_info->comments; ?></div>
                </li>
            </ul>
            <div class="open_post_ul_bottom"><a href="<?php echo site_url();?>/add-job-space/?post_id=<?php echo $loads_info->id;?>"><input name="award_job" type="button"  class="btn_4" <?php if($loads_info->post_type=='spaces'){ ?> value="Award Space" <?php } ?>></a></div>
        </div>
    <?php       } 
            } 
            $pagination = "";
	if($lastpage > 1)
	{	
		$pagination .= "<div class=\"pagination\"";
		if($margin || $padding)
		{
			$pagination .= " style=\"";
			if($margin)
				$pagination .= "margin: $margin;";
			if($padding)
				$pagination .= "padding: $padding;";
			$pagination .= "\"";
		}
		$pagination .= ">";

		//previous button
		if ($page > 1) 
			$pagination .= "<a href=\"$targetpage$pagestring$prev\">« prev</a>";
		else
			$pagination .= "<span class=\"disabled\">« prev</span>";	
		
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination .= "<span class=\"current\">$counter</span>";
				else
					$pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>";					
			}
		}
		elseif($lastpage >= 7 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 3))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination .= "<span class=\"current\">$counter</span>";
					else
						$pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>";					
				}
				$pagination .= "<span class=\"elipses\">...</span>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . $lpm1 . "\">$lpm1</a>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . $lastpage . "\">$lastpage</a>";		
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination .= "<a href=\"" . $targetpage . $pagestring . "1\">1</a>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . "2\">2</a>";
				$pagination .= "<span class=\"elipses\">...</span>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination .= "<span class=\"current\">$counter</span>";
					else
						$pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>";					
				}
				$pagination .= "...";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . $lpm1 . "\">$lpm1</a>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . $lastpage . "\">$lastpage</a>";		
			}
			//close to end; only hide early pages
			else
			{
				$pagination .= "<a href=\"" . $targetpage . $pagestring . "1\">1</a>";
				$pagination .= "<a href=\"" . $targetpage . $pagestring . "2\">2</a>";
				$pagination .= "<span class=\"elipses\">...</span>";
				for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination .= "<span class=\"current\">$counter</span>";
					else
						$pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>";					
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
			$pagination .= "<a href=\"" . $targetpage . $pagestring . $next . "\">next »</a>";
		else
			$pagination .= "<span class=\"disabled\">next »</span>";
		$pagination .= "</div>\n";
	}
            echo $pagination;
        }
        
}
function print_star_ratings($post_type, $post_id){
    global $wpdb;
    $get_stars = $wpdb->get_results("SELECT AVG(ratings) AS average_ratings FROM wp_awarded_job WHERE post_type = '$post_type' AND id_post = $post_id", ARRAY_A);
    if(!empty($get_stars)){
        $print_star = '';
        foreach ($get_stars as $get_star) {
            $number  = floor($get_star['average_ratings']);
            if($number == 0)
                return 'NOT YET RATED';
            $fraction = $get_star['average_ratings'] - $number;
            for($i = 1; $i <= $number; $i++){
                $print_star .= '<img src="'.get_template_directory_uri().'/images/star.png" alt="">';
            }
            for($i = 0; $i < $fraction; $i++){
                $print_star .= '<img src="'.get_template_directory_uri().'/images/half_star.png" alt="">';
            }
        }
        return $print_star;
    }
}

function format_phone($phone){
    $phone = preg_replace("/[^0-9]/", "", $phone);
    
    if(strlen($phone) == 7){
        return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
    }
    elseif(strlen($phone) == 10){
        return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
    }
    elseif(strlen($phone) == 11){
        return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "($1) $2-$3", $phone);
    }
    else{
        return $phone;
    }
}

function current_space_available($post_id, $post_type){
    global $wpdb;
    $get_total_used_spaces = $wpdb->get_results("SELECT sum(cft_loaded) as used_space FROM wp_awarded_job WHERE id_post = $post_id AND post_type = '$post_type'");
    if(!empty($get_total_used_spaces)){
        foreach ($get_total_used_spaces as $get_total_used_space) {
            $used_space = $get_total_used_space->used_space;
        }
    }else{
        $used_space = 0;
    }
    $where = ($post_type == 'spaces')?'id_space':'id';
    $select = ($post_type == 'spaces')?'space_available':'o_loadsize';
    $get_available_spaces  = $wpdb->get_results("SELECT $select FROM wp_$post_type WHERE $where = $post_id");
    if(!empty($get_available_spaces)){
        foreach ($get_available_spaces as $get_available_space) {
            $original_space = $get_available_space->$select;
        }
    }else{
        return FALSE;
    }
    if($original_space > $used_space){
        return ($original_space - $used_space);
    }else{
        return FALSE;
    }
}
?>