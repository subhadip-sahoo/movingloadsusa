<?php
    require $_SERVER['DOCUMENT_ROOT']."/movingloadsusa/wp-blog-header.php";
    $environment = (mlusa_get_option('paypal_environment') == 'sandbox')?'sandbox':'production';
    Braintree_Configuration::environment($environment);
    Braintree_Configuration::merchantId(mlusa_get_option('braintree_merchant_ID')); // r7zgjybn2stbwt3t
    Braintree_Configuration::publicKey(mlusa_get_option('braintree_public_key')); // k2zvnvyqypxxc44f
    Braintree_Configuration::privateKey(mlusa_get_option('braintree_private_key')); // 0272e924516a44ca6dcc2407961e3024
?>
