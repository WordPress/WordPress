<?php

/**
 * W3 Total Cache Minify module
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

$w3_minify = w3_instance('W3_Minify');
$w3_minify->process();
