<?php
/**
 * W3 Total Cache support requests files
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
    define('W3TC_DIR', WP_PLUGIN_DIR . '/w3-total-cache');
}

if (!@is_dir(W3TC_DIR) || !file_exists(W3TC_DIR . '/inc/define.php')) {
    @header('X-Robots-Tag: noarchive, noodp, nosnippet');
    echo(sprintf('<strong>W3 Total Cache Error:</strong> some files appear to be missing or out of place. Please re-install plugin or remove <strong>%s</strong>.', dirname(__FILE__)));
}

require_once W3TC_DIR . '/inc/define.php';

$attachment_location = filter_var(urldecode($_REQUEST['file']), FILTER_SANITIZE_STRING);
$md5 = md5($attachment_location);
$nonce = $_REQUEST['nonce'];
$stored_nonce = get_site_option('w3tc_support_request') ? get_site_option('w3tc_support_request') : get_option('w3tc_support_request');
$stored_attachment = get_site_option('w3tc_support_request') ? get_site_option('attachment_' . $md5) : get_option('attachment_' . $md5);

if (file_exists($attachment_location) && $nonce == $stored_nonce && !empty($stored_nonce) && $stored_attachment == $attachment_location) {
    w3_require_once(W3TC_INC_DIR . '/functions/mime.php');
    $type = w3_get_mime_type($attachment_location);
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Content-Type: " . $type);
    header("Content-Length:".filesize($attachment_location));
    header("Content-Disposition: attachment; filename=" . basename($attachment_location));

    $file = fopen($attachment_location, 'rb');
    if ( $file !== false ) {
        fpassthru($file);
        fclose($file);
    }

    w3tc_file_log('success', $attachment_location);
    die();
} elseif ($nonce != $stored_nonce || $stored_attachment != $attachment_location) {
    header($_SERVER["SERVER_PROTOCOL"] . " 401");
    w3tc_file_log('Unauthorized access', $attachment_location);
    die("Unauthorized access.");
} else {
    header($_SERVER["SERVER_PROTOCOL"] . " 404");
    w3tc_file_log('File not found', $attachment_location);
    die("File not found.");
}


/**
 * Write log entry
 *
 * @param string message
 * @param string $file
 * @return bool|int
 */
function w3tc_file_log($message, $file) {
    if (defined('W3_SUPPORT_DEBUG') && W3_SUPPORT_DEBUG) {
        w3_require_once(W3TC_INC_DIR . '/functions/file.php');
        $data = sprintf("[%s] %s %s\n", date('r'), $message, $file);
        if (get_site_option('w3tc_support_request'))
            $blog_id = 0;
        else
            $blog_id = null;
        $filename = w3_cache_blog_dir('log', $blog_id) . '/file-sender.log';
        if (!is_dir(dirname($filename)))
            w3_mkdir_from(dirname($filename), W3TC_CACHE_DIR);

        @file_put_contents($filename, $data, FILE_APPEND);
    }
}