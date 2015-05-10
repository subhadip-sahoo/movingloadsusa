<?php
require $_SERVER['DOCUMENT_ROOT']."/movingloadsusa/wp-blog-header.php";
global $wpdb, $user_ID;
if(!$user_ID){
    wp_safe_redirect(home_url().'/login/');
    exit();
}
$err_msg = '';
$suc_msg = '';
$job_id = esc_sql($_REQUEST['job_id']);
$ratings = esc_sql($_REQUEST['ratings']);
$comments = esc_sql($_REQUEST['comments']);
if(empty($job_id) || !is_numeric($job_id)) { 
    $err_msg .= "This request can't be preocessed.";
}
else if($ratings == 0) { 
    $err_msg .= "Please rate your carrier.";
}		
else if(empty($comments)) { 
    $err_msg .= "Please enter your comments.";
}
else {
    $info = array(
        'ratings' => $ratings,
        'close_job_comments' => $comments
    );
    if($wpdb->update('wp_awarded_job', $info, array('id_job' => $job_id))){
        $suc_msg .= 'Thanks for your review.';
    }
}
if(!empty($err_msg)){
    $class = 'err_msg';
    echo json_encode(array('class' => $class, 'message' => $err_msg));
}
else{
    $class = 'suc_msg';
    echo json_encode(array('class' => $class, 'message' => $suc_msg));
}
?>