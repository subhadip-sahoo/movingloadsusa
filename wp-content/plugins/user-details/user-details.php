<?php
/* 
 * Plugin Name: Activate User Account
 * Plugin URI: http://businessprodesigns.com
 * Description: Activate users account.
 * Version: 1.0
 * Author: BusinessProDesigns
 * Author URI: http://businessprodesigns.com
 * Licence: GPL2
*/
global $table_name_users;
$table_name_users = $wpdb->prefix . 'users';

if (!class_exists('WP_List_Table')) {
   require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

require_once dirname(__FILE__).'/class/user_details_list_table.class.php';

function user_details_admin_menu(){
    $hook = add_menu_page(__('User Details', 'user_details'), __('User Details', 'user_details'), 'activate_plugins', 'user-details', 'user_details_main', plugins_url().'/user-details/images/addEdit.png');
    add_submenu_page(NULL, __('Activate User', 'user_details'), __('Activate User', 'user_details'), 'activate_plugins', 'edit-user-details', 'user_details_edit_handler');
    add_action('load-'.$hook, 'cct_add_option');
}

function cct_add_option() {
    $option = 'per_page';
    $args = array(
        'label' => 'Users',
        'default' => 10,
        'option' => 'users_per_page'
    );
    
    $screen = get_current_screen();
    add_filter( 'manage_'.$screen->id.'_columns', array( 'user_details_list_table', 'get_columns' ));
    add_screen_option( $option, $args );
}
add_action('admin_menu', 'user_details_admin_menu');
add_filter('set-screen-option', 'cct_set_option', 10, 3);

function cct_set_option($status, $option, $value) {
    if ( 'users_per_page' == $option ) return $value;
    return $status;
}

function user_details_main($per_page){
    global $wpdb;
    $table = new user_details_list_table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('%d Items deleted.', 'user_details'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
<div class="wrap">
    <div class="icon32" id="icon-users"><br></div>
    <h2><?php _e('All Centers', 'user_details')?> 
<?php
    if ( ! empty( $_REQUEST['s'] ) ) {
		echo sprintf( '<span class="subtitle">'
			. __( 'Search results for &#8220;%s&#8221;', 'user_details' )
			. '</span>', esc_html( $_REQUEST['s'] ) );
	}
?>
    </h2>
    <?php echo $message; ?>

    <form method="get" action="">
        <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>"/>
        <?php $table->search_box( __( 'Search', 'user_details' ), 'centers' ); ?>
        <?php $table->display(); ?>
    </form>
</div>
 <?php   
}

function user_details_edit_handler(){
    global $wpdb, $table_name_users;
    
    $message = '';
    $notice = '';
    
    $default = array(
        'ID' => 0,
        'user_email' => '',
        'user_login' => '',
        'user_pass' => '',
        'user_status' => ''
    );

    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        $item = shortcode_atts($default, $_REQUEST);
        $item_valid = user_details_validate($item);
        if ($item_valid === true) {
            if ($item['ID'] == 0) {
                $result = $wpdb->insert($table_name_users, $item);
                $item['ID'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'user_details');
                } else {
                    $notice = __('There was an error while saving item', 'user_details');
                }
            } else {
                //$result = $wpdb->update($table_name_users, $item, array('ID' => $item['ID']));
                wp_update_user(array(
                    'ID' => esc_sql($item['ID']),
                    'user_login' => esc_sql($item['user_login']),
                    'user_pass' => esc_sql($item['user_pass'])
                ));
//                if ($result) {
//                    $message = __('Item was successfully updated', 'user_details');
//                } else {
//                    $notice = __('There was an error while updating item', 'user_details');
//                }
                echo 'success';
            }
        } else {
            $notice = $item_valid;
        }
    }
    else {
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_users WHERE ID = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'user_details');
            }
        }
    }
    add_meta_box('user_details_meta_box', 'User Details', 'user_details_meta_box_handler', 'user_detailss', 'normal', 'default');
    ?>
<div class="wrap">
    <div class="icon32" id="icon-users"><br></div>
    <h2><?php _e('Imaging Center', 'user_details')?> 
        <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=user-details');?>"><?php _e('Back to list', 'user_details')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <input type="hidden" name="ID" value="<?php echo $item['ID'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php do_meta_boxes('user_detailss', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Save', 'user_details')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}

function user_details_meta_box_handler($item){
?>

<table cellspacing="2" cellpadding="5" style="width: 95%;" class="form-table">
    <tbody>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="user_login"><?php _e('Username', 'user_details')?></label>
            </th>
            <td>
                <input id="user_login" name="user_login" type="text" style="width: 95%" value="" class="code" />
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="user_pass"><?php _e('Password', 'user_details')?></label>
            </th>
            <td>
                <input id="user_pass" name="user_pass" type="password" style="width: 95%" value="" class="code" />
            </td>
        </tr>
    </tbody>
</table>
<?php
}

function user_details_validate($item){
    $messages = array();

    if (empty($item['user_login'])) $messages[] = __('Username is required', 'user_details');
    if (empty($item['user_pass'])) $messages[] = __('Password is required', 'user_details');
//    if (empty($item['user_status'])) $messages[] = __('Insurance accepted is required', 'user_details');

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

function user_details_languages(){
    load_plugin_textdomain('user_details', false, dirname(plugin_basename(__FILE__)));
}
add_action('init', 'user_details_languages');
?>