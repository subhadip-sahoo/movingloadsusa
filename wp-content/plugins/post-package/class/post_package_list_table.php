<?php
class post_package_List_Table extends WP_List_Table {
    function __construct(){
        global $status, $page, $table_name;

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
            'edit' => sprintf('<a href="?page=edit-package&id=%s">%s</a>', $item['id_post_package'], __('Edit', 'post_package')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id_post_package'], __('Delete', 'post_package')),
        );

        return sprintf('%s %s', $item['name'], $this->row_actions($actions));
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id_post_package']
        );
    }

    function get_columns(){
        $columns = array(
            'cb' => '<input type="checkbox" />', 
            'name' => __('Package Name', 'post_package'),
            'total_posts' => __('Total Posts', 'post_package'),
            'expiry_per_post' => __('Validity Per Post(In days)', 'post_package'),
            'expiry_package' => __('Validity of Package(In days)', 'post_package'),
            'package_price' => __('Price of Package(In USD)', 'post_package')
        );
        return $columns;
    }

    function get_sortable_columns(){
        $sortable_columns = array(
            'name' => array('name', true),
            'total_posts' => array('total_posts', false),
            'expiry_per_post' => array('expiry_per_post', false),
            'expiry_package' => array('expiry_package', false),
            'package_price' => array('package_price', false)
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
        global $wpdb, $table_name;
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id_post_package IN($ids)");
            }
        }
    }
    function prepare_items(){
        global $wpdb, $table_name;
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
                'orderby' => 'name',
                'order' => 'ASC',
                'offset' => ( $this->get_pagenum() - 1 ) * $per_page );
        $where = '';
        if (isset($_REQUEST['s']) && ! empty( $_REQUEST['s'] ) ){
                $args['s'] = $_REQUEST['s'];
                $where = " WHERE name LIKE '%%".$args['s']."%%' OR total_posts LIKE '%%".$args['s']."%%' OR expiry_per_post LIKE '%%".$args['s']."%%' OR expiry_package LIKE '%%".$args['s']."%%' OR package_price LIKE '%%".$args['s']."%%'";
        }
        if ( ! empty( $_REQUEST['orderby'] ) ) {
                if ( 'name' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'name';
                }
                elseif ( 'total_posts' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'total_posts';
                }
                elseif ( 'expiry_per_post' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'expiry_per_post';
                }
                elseif ( 'expiry_package' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'expiry_package';
                }
                elseif ( 'package_price' == $_REQUEST['orderby'] ){
                        $args['orderby'] = 'package_price';
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
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name $where ORDER BY ".$args['orderby']." ".$args['order']." LIMIT %d OFFSET %d", $per_page, $args['offset']), ARRAY_A);
        $total_items = $wpdb->get_var("SELECT COUNT(id_post_package) FROM $table_name $where");
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'total_pages' => ceil($total_items / $per_page),
            'per_page' => $per_page
        ));
    }
}
?>