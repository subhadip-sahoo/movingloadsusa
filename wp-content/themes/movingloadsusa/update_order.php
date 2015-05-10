<?php
require $_SERVER['DOCUMENT_ROOT']."/wp-blog-header.php";
global $wpdb, $user_ID;
$get_rows = 0;
foreach ($_REQUEST['dndSort'] as $key => $item) {
    $sequence = $get_rows + $key + 1;
    $wpdb->update('wp_spaces', array('sequence' => $sequence), array('id_space' => $item));
}
echo json_encode(array('success' => 1));
?>
