<?php
class user_details_list_table extends WP_List_Table {
    function __construct(){
        global $status, $page;

        parent::__construct(array(
            'singular' => '',
            'plural' => '',
            'ajax' => false
        ));
    }
    function column_default($item, $column_name){
        return $item[$column_name];
    }

    function column_user_email($item){
        $actions = array(
            'edit' => sprintf('<a href="?page=edit-user-details&id=%s">%s</a>', $item['ID'], __('Edit', 'user_details')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['ID'], __('Delete', 'user_details')),
        );

        return sprintf('%s %s', $item['user_email'], $this->row_actions($actions));
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['ID']
        );
    }

    function get_columns(){
        $columns = array(
            'cb' => '<input type="checkbox" />', 
            'user_email' => __('User Email', 'user_details'),
            'user_nicename' => __('Name', 'user_details'),
            'user_status' => __('Status', 'user_details')
        );
        return $columns;
    }

    function get_sortable_columns(){
        $sortable_columns = array(
            'user_email' => array('user_email', false),
            'user_nicename' => array('user_nicename', false),
            'status' => array('status', TRUE)
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
        global $wpdb, $table_name_users;

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name_users WHERE ID IN($ids)");
            }
        }
    }
    function prepare_items(){
        global $wpdb, $table_name_users;
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
                'orderby' => 'ID',
                'order' => 'DESC',
                'offset' => ( $this->get_pagenum() - 1 ) * $per_page );
        $where = '';
        if (isset($_REQUEST['s']) && ! empty( $_REQUEST['s'] ) ){
                $args['s'] = $_REQUEST['s'];
                $status = ($args['s'] == 'Active')?1:2;
                $where = " WHERE user_email LIKE '%%".$args['s']."%%' OR user_nicename LIKE '%%".$args['s']."%%' OR user_status LIKE '%%".$args['s']."%%'";
        }
        if ( ! empty( $_REQUEST['orderby'] ) ) {
                if ( 'image_center_name' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'user_email';
                }
                elseif ( 'user_nicename' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'user_nicename';
                }
                elseif ( 'status' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'user_status';
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
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT ID, user_email, user_nicename, user_status FROM $table_name_users $where ORDER BY ".$args['orderby']." ".$args['order']." LIMIT %d OFFSET %d", $per_page, $args['offset']), ARRAY_A);
        $total_items = $wpdb->get_var("SELECT COUNT(ID) FROM $table_name_users $where");
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'total_pages' => ceil($total_items / $per_page),
            'per_page' => $per_page
        ));
    }
}

?>
