<?php
/* 
 * Plugin Name:Payment Details
 * Plugin URI: http://qss.in/
 * Description: Display all Payments.
 * Version: 1.5
 * Author: Quintessential Software Solutions Private Limited (QSS)
 * Author URI: http://qss.in/
 * Licence: GPL2
*/
    global $payments_details_db_version, $wpdb;
    $payments_details_db_version = '1.1';
    global $table_name_payments;
    $table_name_payments = 'wp_user_details';
   if (!class_exists('WP_List_Table')) {
   require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
require_once dirname(__FILE__).'/class/payment_details_list_table.php';

function payment_details_admin_menu(){
    $hook = add_menu_page(__('User Activities', 'payment_details'), __('User Activities', 'payment_details'), 'activate_plugins', 'payment_details', 'payment_details_main', plugins_url().'/post-package/images/code_list.png');
    add_action('load-'.$hook, 'pd_add_option');
}

function pd_add_option() {
    $option = 'per_page';
    $args = array(
        'label' => 'Payment Details Listing',
        'default' => 10,
        'option' => 'payments_per_page'
    );
    
    $screen = get_current_screen();
    add_filter( 'manage_'.$screen->id.'_columns', array( 'payment_details_List_Table', 'get_columns' ));
    add_screen_option( $option, $args );
}
add_action('admin_menu', 'payment_details_admin_menu');
add_filter('set-screen-option', 'pd_set_option', 10, 3);

function pd_set_option($status, $option, $value) {
    if ( 'payments_per_page' == $option ) return $value;
    return $status;
}

function payment_details_main($per_page){
    global $wpdb;
    $table = new payment_details_List_Table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('%d Items deleted.', 'payment_details'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
<div class="wrap">
    <div class="icon32" id="icon-users"><br></div>
    <h2><?php _e('List of Payments', 'payment_details')?> 
<?php
    if ( ! empty( $_REQUEST['s'] ) ) {
		echo sprintf( '<span class="subtitle">'
			. __( 'Search results for &#8220;%s&#8221;', 'payment_details' )
			. '</span>', esc_html( $_REQUEST['s'] ) );
	}
?>
    </h2>
    <?php echo $message; ?>

    <form method="get" action="">
        <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>"/>
        <?php $table->search_box( __( 'Search', 'payment_details' ), 'payments' ); ?>
        <?php $table->display(); ?>
    </form>
</div>
 <?php   
}

function payment_details_languages(){
    load_plugin_textdomain('payment_details', false, dirname(plugin_basename(__FILE__)));
}
add_action('init', 'payment_details_languages');
?>