<?php
/* Template Name: Braintree Webhooks */
require_once trailingslashit(get_template_directory()) . '/Braintree/lib/Braintree.php';
require_once trailingslashit(get_template_directory()) . '/Braintree/Braintree_account.php';
if(isset($_GET["bt_challenge"])) {
    echo(Braintree_WebhookNotification::verify($_GET["bt_challenge"]));
}
if(isset($_POST["bt_signature"]) && isset($_POST["bt_payload"])) {
    $webhookNotification = Braintree_WebhookNotification::parse(
        $_POST["bt_signature"], $_POST["bt_payload"]
    );

    $message =
        "[Webhook Received " . $webhookNotification->timestamp->format('Y-m-d H:i:s') . "] "
        . "Kind: " . $webhookNotification->kind . " | "
        . "Subscription: " . $webhookNotification->subscription->id . "\n";

    file_put_contents("/tmp/webhook.log", $message, FILE_APPEND);
}
get_header();
?>
<?php get_footer(); ?>