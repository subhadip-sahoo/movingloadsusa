<?php
class payment_details_List_Table extends WP_List_Table {
    function __construct(){
        global $status, $page, $table_name_payments;

        parent::__construct(array(
            'singular' => '',
            'plural' => '',
            'ajax' => false
        ));
    }
    function column_default($item, $column_name){
        return $item[$column_name];
    }

    function column_name($item){
        $actions = array(
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'payment_details')),
        );

        return sprintf('%s %s', $item['name'], $this->row_actions($actions));
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    function get_columns(){
        $columns = array(
            'cb' => '<input type="checkbox" />', 
            'name' => __('Username', 'payment_details'),
            'full_name' => __('Full Name', 'payment_details'),
            'user_email' => __('Email Address', 'payment_details'),
            'phone' => __('Contact No', 'payment_details'),
            'status' => __('Status', 'payment_details'),
            'date' => __('Payment Date', 'payment_details'),
            'amount' => __('Amount', 'payment_details'),
            'item' => __('Item name', 'payment_details'),
            'transaction_id' => __('Transaction ID', 'payment_details')
            
        );
        return $columns;
    }

    function get_sortable_columns(){
        global $wpdb;
        $sortable_columns = array(
            'name' => array('name', false),
            'full_name' => array('full_name', false),
            'user_email' => array('user_email', false),
            'phone' => array('phone', false),
            'status' => array('status', false),
            'date' => array('date', false),
            'amount' => array('amount', false),
            'item' => array('item', false),
            'transaction_id' => array('transaction_id', false)        
        ); 

        return $sortable_columns;
    }

    function get_bulk_actions(){
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action(){
        global $wpdb, $table_name_payments;
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name_payments WHERE id IN($ids)");
               
            }
        }
    }
    function prepare_items(){
        global $wpdb, $table_name_payments;
        $user = get_current_user_id();
        $screen = get_current_screen();
        $option = $screen->get_option('per_page', 'option');
        $per_page = get_user_meta($user, $option, true);

        if ( empty ( $per_page) || $per_page < 1 ) {
            $per_page = $screen->get_option( 'per_page', 'default');
        }
        $this->_column_headers = $this->get_column_info();
        $this->process_bulk_action();
        $args = array(
                'posts_per_page' => $per_page,
                'orderby' => 'id',
                'order' => 'DESC',
                'offset' => ( $this->get_pagenum() - 1 ) * $per_page );
        $where = '';
        if (isset($_REQUEST['s']) && ! empty( $_REQUEST['s'] ) ){
                $args['s'] = $_REQUEST['s'];
                $where = " WHERE wpud.name LIKE '%%".$args['s']."%%' OR wpud.status LIKE '%%".$args['s']."%%' OR wpud.date LIKE '%%".$args['s']."%%' OR wpud.amount LIKE '%%".$args['s']."%%' OR wpud.item LIKE '%%".$args['s']."%%' OR wpud.transaction_id LIKE '%%".$args['s']."%%' OR wpu.user_email LIKE '%%".$args['s']."%%' OR wpu.user_email LIKE '%%".$args['s']."%%' OR CONCAT((SELECT meta_value FROM `wp_usermeta` WHERE user_id = wpud.userid AND meta_key = 'first_name'),' ',(SELECT meta_value FROM `wp_usermeta` WHERE user_id = wpud.userid AND meta_key = 'last_name')) LIKE '%%".$args['s']."%%' OR (SELECT meta_value FROM `wp_usermeta` WHERE user_id = wpud.userid AND meta_key = 'phone') LIKE '%%".format_phone($args['s'])."%%'";
        }
        if ( ! empty( $_REQUEST['orderby'] ) ) {
                if ( 'name' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'name';
                }
                elseif ( 'status' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'status';
                }
                elseif ( 'date' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'date';
                }
                elseif ( 'amount' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'amount';
                }
                elseif ( 'item' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'item';
                }
                 elseif ( 'transaction_id' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'transaction_id';
                }
        }
        
        if ( ! empty( $_REQUEST['order'] ) ) {
                if ( 'asc' == strtolower( $_REQUEST['order'] ) ){
                        $args['order'] = 'ASC';
                }
                elseif ( 'desc' == strtolower( $_REQUEST['order'] ) ){
                        $args['order'] = 'DESC';
                }
        }
        $this->items = $wpdb->get_results($wpdb->prepare("
                                            SELECT  wpud.*, 
                                                    wpu.user_email, 
                                                    CONCAT((SELECT 
                                                                meta_value 
                                                            FROM 
                                                                `wp_usermeta` 
                                                            WHERE 
                                                                user_id = wpud.userid 
                                                            AND 
                                                                meta_key = 'first_name'
                                                            ),' ',
                                                            (
                                                            SELECT 
                                                                meta_value 
                                                            FROM 
                                                                `wp_usermeta` 
                                                            WHERE 
                                                                user_id = wpud.userid 
                                                            AND 
                                                                meta_key = 'last_name')
                                                    ) AS full_name,
                                                    (   SELECT 
                                                            meta_value 
                                                        FROM 
                                                            `wp_usermeta` 
                                                        WHERE 
                                                            user_id = wpud.userid 
                                                        AND 
                                                            meta_key = 'phone'
                                                    ) AS phone			
                                            FROM 
                                                    `wp_user_details` AS wpud 
                                            INNER JOIN 
                                                    `wp_users` AS wpu 
                                            ON wpud.userid = wpu.ID
                                            $where ORDER BY ".$args['orderby']." ".$args['order']." LIMIT %d OFFSET %d", $per_page, $args['offset']), ARRAY_A);
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name_payments $where");
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'total_pages' => ceil($total_items / $per_page),
            'per_page' => $per_page
        ));
    }
}
?>