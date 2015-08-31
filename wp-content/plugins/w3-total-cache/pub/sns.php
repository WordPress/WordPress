<?php

$message = file_get_contents('php://input');
// switch blog before any action
try {
    $message_object = json_decode($message);
} catch (Exception $e) {
    echo('SNS listener');
    exit();
}

if (isset($message_object->Type) && isset($message_object->Message)) {
    if ($message_object->Type == 'Notification') {
        $w3tc_message = $message_object->Message;
        $w3tc_message_object = json_decode($w3tc_message);

        if (isset($w3tc_message_object->blog_id)) {
            global $w3_current_blog_id;
            $w3_current_blog_id = $w3tc_message_object->blog_id;
        }
        if (isset($w3tc_message_object->host) && !is_null($w3tc_message_object->host)) {
            $_SERVER['HTTP_HOST'] = $w3tc_message_object->host;
        }
    }
    else if ($message_object->Type != 'SubscriptionConfirmation'){
        echo('Unsupported message type');
        exit();
    }
}

/**
 * W3 Total Cache SNS module
 */
define('W3TC_WP_LOADING', true);

if (!defined('ABSPATH')) {
    if (file_exists(dirname(__FILE__) . '/../../../../wp-load.php')) {
        require_once dirname(__FILE__) . '/../../../../wp-load.php';
    }
    else {
        require_once dirname(__FILE__) . '/../../w3tc-wp-loader.php';
    }
}

if (!defined('W3TC_DIR')) {
    define('W3TC_DIR', realpath(dirname(__FILE__) . '/..'));
}

if (!@is_dir(W3TC_DIR) || !file_exists(W3TC_DIR . '/inc/define.php')) {
    @header('X-Robots-Tag: noarchive, noodp, nosnippet');
    echo(sprintf('<strong>W3 Total Cache Error:</strong> some files appear to be missing or out of place. Please re-install plugin or remove <strong>%s</strong>.', dirname(__FILE__)));
}

require_once W3TC_DIR . '/inc/define.php';

$server = w3_instance('W3_Enterprise_SnsServer');
$server->process_message();

?>
