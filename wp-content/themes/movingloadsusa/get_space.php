<?php
require $_SERVER['DOCUMENT_ROOT']."/wp-load.php";
global $wpdb, $user_ID;
if(isset($_REQUEST['post_id'])){
    if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete'){
        $wpdb->delete('wp_spaces', array('id_space' => $_REQUEST['post_id']));
         echo json_encode(array('success' => 1));
    }else{
        $getSpace_by_posts = $wpdb->get_results("SELECT * FROM wp_spaces WHERE userid = $user_ID AND id_space = ".$_REQUEST['post_id'], ARRAY_A);
        foreach ($getSpace_by_posts as $getSpace_by_post) {
            echo json_encode($getSpace_by_post);
        }
    }
}
?>
