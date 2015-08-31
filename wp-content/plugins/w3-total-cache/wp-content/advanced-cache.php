<?php

/**
 * W3 Total Cache advanced cache module
 */
if (!defined('ABSPATH')) {
    die();
}

/**
 * Abort W3TC loading if WordPress is upgrading
 */
if (defined('WP_INSTALLING') && WP_INSTALLING)
    return;

if (!defined('W3TC_IN_MINIFY')) {
    if (!defined('W3TC_DIR')) {
        define('W3TC_DIR', (defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins') . '/w3-total-cache');
    }

    if (!@is_dir(W3TC_DIR) || !file_exists(W3TC_DIR . '/inc/define.php')) {
        if (defined('WP_ADMIN')) { // lets don't show error on front end
            echo(sprintf('<strong>W3 Total Cache Error:</strong> some files appear to be missing or out of place. Please re-install plugin or remove <strong>%s</strong>.', __FILE__));
        }
    } else {
        require_once W3TC_DIR . '/inc/define.php';

        $redirect = w3_instance('W3_Redirect');
        $redirect->process();

        $config = w3_instance('W3_Config');
        if ($config->get_boolean('pgcache.enabled')) {
            $w3_pgcache = w3_instance('W3_PgCache');
            $w3_pgcache->process();
        }
    }
}
