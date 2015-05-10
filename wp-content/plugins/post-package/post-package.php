<?php
/* 
 * Plugin Name: Post Package
 * Plugin URI: http://qss.in/
 * Description: Admin can create, modify and delete post packages.
 * Version: 1.1
 * Author: Quintessential Software Solutions Private Limited (QSS)
 * Author URI: http://qss.in/
 * Licence: GPL2
*/
    global $post_package_db_version, $wpdb;
    $post_package_db_version = '1.1';
    global $table_name;
    $table_name = $wpdb->prefix . 'post_package_master';
    function post_package_db_install(){
       global $wpdb, $table_name;
       global $post_package_db_version;
       $sql = "CREATE TABLE IF NOT EXISTS ".$table_name."(
                   id_post_package int(11) NOT NULL AUTO_INCREMENT,
                   name VARCHAR(255) NOT NULL,
                   total_posts INT(11) NOT NULL,
                   expiry_per_post INT(11) NOT NULL,
                   expiry_package INT(11) NOT NULL,
                   package_price DECIMAL(7,2) NOT NULL,
                   PRIMARY KEY (id_post_package)
               );";
       require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
       dbDelta($sql);
       add_option('post_package_db_version', $post_package_db_version);
    }
    register_activation_hook(__FILE__, 'post_package_db_install');

    function post_package_update_db_check(){
       global $post_package_db_version;
       if (get_site_option('post_package_db_version') != $post_package_db_version) {
           post_package_db_install();
       }
   }
   add_action('plugins_loaded', 'post_package_update_db_check');

   if (!class_exists('WP_List_Table')) {
   require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
require_once dirname(__FILE__).'/class/post_package_list_table.php';

function post_package_admin_menu(){
    $hook = add_menu_page(__('Post Packages', 'post_package'), __('Post Packages', 'post_package'), 'activate_plugins', 'post_packages', 'post_package_main', plugins_url().'/post-package/images/code_list.png');
    add_submenu_page('post_packages', __('Create New Package', 'post_package'), __('Create New Package', 'post_package'), 'activate_plugins', 'edit-package', 'post_package_codes_edit_handler');
    add_action('load-'.$hook, 'pp_add_option');
}

function pp_add_option() {
    $option = 'per_page';
    $args = array(
        'label' => 'Packages',
        'default' => 10,
        'option' => 'packages_per_page'
    );
    
    $screen = get_current_screen();
    add_filter( 'manage_'.$screen->id.'_columns', array( 'post_package_List_Table', 'get_columns' ));
    add_screen_option( $option, $args );
}
add_action('admin_menu', 'post_package_admin_menu');
add_filter('set-screen-option', 'pp_set_option', 10, 3);

function pp_set_option($status, $option, $value) {
    if ( 'packages_per_page' == $option ) return $value;
    return $status;
}
function show_post_packages_shortcode(){
    ob_start();
    extract( shortcode_atts( array(), $atts ) );
    require_once dirname(__FILE__).'/template/show-post-package.php';
    return ob_get_clean();
}
add_shortcode('show-post-packages', 'show_post_packages_shortcode');
function post_package_main($per_page){
    global $wpdb;
    $table = new post_package_List_Table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('%d Items deleted.', 'post_package'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
<div class="wrap">
    <div class="icon32" id="icon-users"><br></div>
    <h2><?php _e('List of Packages', 'post_package')?> 
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=edit-package');?>"><?php _e('Create New Package', 'post_package')?></a>
<?php
    if ( ! empty( $_REQUEST['s'] ) ) {
		echo sprintf( '<span class="subtitle">'
			. __( 'Search results for &#8220;%s&#8221;', 'post_package' )
			. '</span>', esc_html( $_REQUEST['s'] ) );
	}
?>
    </h2>
    <?php echo $message; ?>

    <form method="get" action="">
        <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>"/>
        <?php $table->search_box( __( 'Search', 'post_package' ), 'packages' ); ?>
        <?php $table->display(); ?>
    </form>
</div>
 <?php   
}

function post_package_codes_edit_handler(){
    global $wpdb, $table_name;
    $message = '';
    $notice = '';
    $default = array(
        'id_post_package' => 0,
        'name' => '',
        'total_posts' => '',
        'expiry_per_post' => '',
        'expiry_package' => '',
        'package_price' => ''
    );

    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        $item = shortcode_atts($default, $_REQUEST);
        $item_valid = post_package_validate($item);
        if ($item_valid === true) {
            if ($item['id_post_package'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id_post_package'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'post_package');
                } else {
                    $notice = __('There was an error while saving item', 'post_package');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('id_post_package' => $item['id_post_package']));
                if ($result) {
                    $message = __('Item was successfully updated', 'post_package');
                } else {
                    $notice = __('There was an error while updating item', 'post_package');
                }
            }
        } else {
            $notice = $item_valid;
        }
    }
    else {
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id_post_package = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'post_package');
            }
        }
    }
    add_meta_box('post_package_meta_box', 'Post Package', 'post_package_meta_box_handler', 'packages', 'normal', 'default');
    ?>
<div class="wrap">
    <div class="icon32" id="icon-users"><br></div>
    <h2><?php _e('Post Package', 'post_package')?> 
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=post_packages');?>"><?php _e('Back to list', 'post_package')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <input type="hidden" name="id_post_package" value="<?php echo $item['id_post_package'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php do_meta_boxes('packages', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Save', 'post_package')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}

function post_package_meta_box_handler($item){
    ?>
<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="package_name"><?php _e('Package Name', 'post_package')?></label>
        </th>
        <td>
            <input id="name" name="name" type="text" style="width: 95%" value="<?php echo esc_attr($item['name'])?>" class="code" required />
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="total_posts"><?php _e('Total Posts', 'post_package')?></label>
        </th>
        <td>
            <input id="total_posts" name="total_posts" type="text" style="width: 95%" value="<?php echo esc_attr($item['total_posts'])?>" class="code" required />
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="expiry_per_post"><?php _e('Validity Per Post(In days)', 'post_package')?></label>
        </th>
        <td>
            <input id="expiry_per_post" name="expiry_per_post" type="text" style="width: 95%" value="<?php echo esc_attr($item['expiry_per_post'])?>" class="code" required />
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="expiry_package"><?php _e('Validity of Package(In days)', 'post_package')?></label>
        </th>
        <td>
            <input id="expiry_package" name="expiry_package" type="text" style="width: 95%" value="<?php echo esc_attr($item['expiry_package'])?>" class="code" required />
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="package_price"><?php _e('Price of Package(In USD)', 'post_package')?></label>
        </th>
        <td>
            <input id="package_price" name="package_price" type="text" style="width: 95%" value="<?php echo esc_attr($item['package_price'])?>" class="code" required />
        </td>
    </tr>
    </tbody>
</table>
<?php
}
function post_package_validate($item){
    $messages = array();

    if (empty($item['name'])) $messages[] = __('Package name is required', 'post_package');
    if (empty($item['total_posts'])) $messages[] = __('Total posts is required', 'post_package');
    if (!ctype_digit($item['total_posts'])) $messages[] = __('Total posts in wrong format. Only digit required.', 'post_package');
    if (empty($item['expiry_per_post'])) $messages[] = __('Validity per post is required', 'post_package');
    if (!ctype_digit($item['expiry_per_post'])) $messages[] = __('Validity per post in wrong format. Only digit required.', 'post_package');
    if (empty($item['expiry_package'])) $messages[] = __('Validity of package is required', 'post_package');
    if (!ctype_digit($item['expiry_package'])) $messages[] = __('Validity of package in wrong format. Only digit required.', 'post_package');
    if (empty($item['package_price'])) $messages[] = __('Price of package is required', 'post_package');
    if (!is_numeric($item['package_price'])) $messages[] = __('Price of package in wrong format. Only decimal value required.', 'post_package');
    
    if (empty($messages)) return true;
    return implode('<br />', $messages);
}
function post_package_languages(){
    load_plugin_textdomain('post_package', false, dirname(plugin_basename(__FILE__)));
}
add_action('init', 'post_package_languages');
?>