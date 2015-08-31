<?php

/**
 * W3 Total Cache plugins API
 */

/**
 * Add W3TC action callback
 *
 * @param string $key
 * @param mixed $callback
 * @return void
 */
function w3tc_add_ob_callback($key, $callback) {
    $GLOBALS['_w3tc_ob_callbacks'][$key] = $callback;
}

function w3tc_do_ob_callbacks($order, &$value) {
    foreach ($order as $key) {
        if (isset($GLOBALS['_w3tc_ob_callbacks'][$key])) {
            $callback = $GLOBALS['_w3tc_ob_callbacks'][$key];
            if (is_callable($callback)) {
                $value = call_user_func($callback, $value);
            }
        }
    }
    return $value;
}

/**
 * Add W3TC action callback
 *
 * @param string $action
 * @param mixed $callback
 * @param int $priority
 * @return void
 */
function w3tc_add_action($action, $callback, $priority = 10) {
    $GLOBALS['_w3tc_actions'][$action][$priority][] = $callback;
    ksort($GLOBALS['_w3tc_actions'][$action]);
}

/**
 * Do W3TC action
 *
 * @param string $action
 * @param mixed $value
 * @return mixed
 */
function w3tc_do_action($action, $value = null) {
    if (isset($GLOBALS['_w3tc_actions'][$action])) {
        foreach ((array) $GLOBALS['_w3tc_actions'][$action] as $callbacks) {
            foreach ($callbacks as $callback) {
                if (is_callable($callback)) {
                    $value = call_user_func($callback, $value);
                }
            }
        }
    }

    return $value;
}

/**
 * Do W3TC action
 *
 * @param string $action
 * @param mixed $value
 * @return mixed
 */
function w3tc_do_action_by_ref($action, &$value = null) {
    if (isset($GLOBALS['_w3tc_actions'][$action])) {
        foreach ((array) $GLOBALS['_w3tc_actions'][$action] as $callbacks) {
            foreach ($callbacks as $callback) {
                if (is_callable($callback)) {
                    $value = call_user_func($callback, $value);
                }
            }
        }
    }

    return $value;
}

/**
 * Shortcut for page cache flush
 *
 * @return boolean
 */
function w3tc_pgcache_flush() {
    /**
     * @var $w3_cacheflush W3_CacheFlush
     */
    $w3_cacheflush = w3_instance('W3_CacheFlush');
    return $w3_cacheflush->pgcache_flush();
}

/**
 * Shortcut for page post cache flush
 *
 * @param integer $post_id
 * @return boolean
 */
function w3tc_pgcache_flush_post($post_id) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');

    return $w3_cacheflush->pgcache_flush_post($post_id);
}

/**
 * Shortcut for url page cache flush
 *
 * @param string $url
 * @return boolean
 */
function w3tc_pgcache_flush_url($url) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');

    return $w3_cacheflush->pgcache_flush_url($url);
}


/**
 * Shortcut for refreshing the media query string.
 */
function w3tc_browsercache_flush() {
    $config = w3_instance('W3_Config');
    if ($config->get_boolean('browsercache.enabled')) {
        $config->set('browsercache.timestamp', time());
        try {
            $config->save();
        } catch (Exception $ex) {}
    }

}
/**
 * Shortcut for database cache flush
 *
 */
function w3tc_dbcache_flush() {
    /**
     * @var $w3_cacheflush W3_CacheFlush
     */
    $w3_cacheflush = w3_instance('W3_CacheFlush');
    $w3_cacheflush->dbcache_flush();
}

/**
 * Shortcut for minify cache flush
 *
 */
function w3tc_minify_flush() {
    /**
     * @var $w3_cacheflush W3_CacheFlush
     */
    $w3_cacheflush = w3_instance('W3_CacheFlush');
    $w3_cacheflush->minifycache_flush();

}

/**
 * Shortcut for objectcache cache flush
 *
 */
function w3tc_objectcache_flush() {
    /**
     * @var $w3_cacheflush W3_CacheFlush
     */
    $w3_cacheflush = w3_instance('W3_CacheFlush');
    $w3_cacheflush->objectcache_flush();
}

/**
 * Shortcut for CDN cache post purge
 * @param $post_id
 * @return mixed
 */
function w3tc_cdncache_purge_post($post_id) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');
    return $w3_cacheflush->cdncache_purge_post($post_id);
}

/**
 * Shortcut for CDN cache url purge
 * @param string $url
 * @return mixed
 */
function w3tc_cdncache_purge_url($url) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');
    return $w3_cacheflush->cdncache_purge_url($url);
}

/**
 * Shortcut for CDN cache purge
 * @return mixed
 */
function w3tc_cdncache_purge() {
    $w3_cacheflush = w3_instance('W3_CacheFlush');
    return $w3_cacheflush->cdncache_purge();
}

/**
 * Shortcut for CDN purge files
 * @param array $files Array consisting of uri paths (i.e wp-content/uploads/image.pnp)
 * @return mixed
 */
function w3tc_cdn_purge_files($files) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');
    return $w3_cacheflush->cdn_purge_files($files);
}

/**
 * Prints script tag for scripts group
 *
 * @param string $location
 * @retun void
 */
function w3tc_minify_script_group($location) {
    $w3_plugin_minify = w3_instance('W3_Plugin_Minify');
    $w3_plugin_minify->printed_scripts[] = $location;

    echo $w3_plugin_minify->get_script_group($location);
}

/**
 * Prints style tag for styles group
 *
 * @param string $location
 * @retun void
 */
function w3tc_minify_style_group($location) {
    $w3_plugin_minify = w3_instance('W3_Plugin_Minify');
    $w3_plugin_minify->printed_styles[] = $location;

    echo $w3_plugin_minify->get_style_group($location);
}

/**
 * Prints script tag for custom scripts
 *
 * @param string|array $files
 * @param boolean $blocking
 * @return void
 */
function w3tc_minify_script_custom($files, $blocking = true) {
    $w3_plugin_minify = w3_instance('W3_Plugin_Minify');
    echo $w3_plugin_minify->get_script_custom($files, $blocking);
}

/**
 * Prints style tag for custom styles
 *
 * @param string|array $files
 * @param boolean $import
 * @return void
 */
function w3tc_minify_style_custom($files, $import = false) {
    $w3_plugin_minify = w3_instance('W3_Plugin_Minify');
    echo $w3_plugin_minify->get_style_custom($files, $import);
}

/**
 * @param string $fragment_group
 * @param boolean $global If group is for whole network in MS install
 * @return mixed
 */
function w3tc_fragmentcache_flush_group($fragment_group, $global = false) {
    $w3_fragmentcache = w3_instance('W3_CacheFlush');
    return $w3_fragmentcache->fragmentcache_flush_group($fragment_group, $global);
}

/**
 * Flush all fragment groups
 * @return mixed
 */
function w3tc_fragmentcache_flush() {
    $w3_fragmentcache = w3_instance('W3_CacheFlush');
    return $w3_fragmentcache->fragmentcache_flush();
}

/**
 * Register a fragment group and connected actions for current blog
 * @param string $group
 * @param array $actions on which actions group should be flushed
 * @param integer $expiration in seconds
 * @return mixed
 */
function w3tc_register_fragment_group($group, $actions, $expiration) {
    if (!is_int($expiration)) {
        $expiration = (int) $expiration;
        trigger_error(__FUNCTION__ . ' needs expiration parameter to be an int.', E_USER_WARNING);
    }
    $w3_fragmentcache = w3_instance('W3_Pro_Plugin_FragmentCache');
    return $w3_fragmentcache->register_group($group, $actions, $expiration);
}

/**
 * Register a fragment group for whole network in MS install
 * @param $group
 * @param $actions
 * @param integer $expiration in seconds
 * @return mixed
 */
function w3tc_register_fragment_global_group($group, $actions, $expiration) {
    if (!is_int($expiration)) {
        $expiration = (int) $expiration;
        trigger_error(__FUNCTION__ . ' needs expiration parameter to be an int.', E_USER_WARNING);
    }
    $w3_fragmentcache = w3_instance('W3_Pro_Plugin_FragmentCache');
    return $w3_fragmentcache->register_global_group($group, $actions, 
        $expiration);
}

/**
 * Starts caching output
 *
 * @param string $id the fragment id
 * @param string $group the fragment group name.
 * @param string $hook name of the action/filter hook to disable on fragment found
 * @return bool returns true if cached fragment is echoed
 */
function w3tc_fragmentcache_start($id, $group = '', $hook = '') {
    $fragment = w3tc_fragmentcache_get($id, $group);
    if (false === $fragment) {
        _w3tc_caching_fragment($id, $group);
        ob_start();
    } else {
        echo $fragment;
        if ($hook) {
            global $wp_filter;
            $wp_filter[$hook] = array();
        }
        return true;
    }
    return false;
}

/**
 * Starts caching filter, returns if filter already cached.
 *
 * @param string $id the fragment id
 * @param string $group the fragment group name.
 * @param string $hook name of the action/filter hook to disable on fragment found
 * @param mixed $data the data returned by the filter
 * @return mixed
 */
function w3tc_fragmentcache_filter_start($id, $group = '', $hook = '', $data) {
    _w3tc_caching_fragment($id, $group);
    $fragment = w3tc_fragmentcache_get($id, $group);
    if (false !== $fragment) {
        if ($hook) {
            global $wp_filter;
            $wp_filter[$hook] = array();
        }
        return $fragment;
    }
    return  $data;
}

/**
 * Ends the caching of output. Stores it and outputs the content
 *
 * @param string $id the fragment id
 * @param string $group the fragment group
 * @param bool $debug
 */
function w3tc_fragmentcache_end($id, $group = '', $debug = false) {
    if (w3tc_is_caching_fragment($id, $group)) {
        $content = ob_get_contents();
        if ($debug)
            $content = sprintf("\r\n".'<!-- fragment start (%s%s)-->'."\r\n".'%s'."\r\n".'<!-- fragment end (%1$s%2$s) cached at %s by W3 Total Cache expires in %d seconds -->'."\r\n", $group, $id,$content, date_i18n('Y-m-d H:i:s'), 1000);
        w3tc_fragmentcache_store($id, $group, $content);
        ob_end_flush();
    }
}


/**
 * Ends the caching of filter. Stores it and returns the content
 *
 * @param string $id the fragment id
 * @param string $group the fragment group
 * @param mixed $data
 * @return mixed
 */
function w3tc_fragmentcache_filter_end($id, $group = '', $data) {
    if (w3tc_is_caching_fragment($id, $group)) {
        w3tc_fragmentcache_store($id, $group, $data);
    }
    return $data;
}

/**
 * Stores an fragment
 *
 * @param $id
 * @param string $group
 * @param string $content
 */
function w3tc_fragmentcache_store($id, $group = '', $content) {
   set_transient("{$group}{$id}", $content, 
    1000 /* default expiration in a case its not catched by fc plugin */);
}

/**
 * @param $id
 * @param string $group
 * @return object
 */
function w3tc_fragmentcache_get($id, $group = '') {
    return get_transient("{$group}{$id}");
}

/**
 * Flushes a fragment from the cache
 * @param $id
 * @param string $group
 */
function w3tc_fragmentcache_flush_fragment($id, $group = '') {
    delete_transient("{$group}{$id}");
}

/**
 * Checks wether page fragment caching is being done for the item
 * @param string $id fragment id
 * @param string $group which group fragment belongs too
 * @return bool
 */
function w3tc_is_caching_fragment($id, $group = '') {
    global $w3tc_caching_fragment;
    return isset($w3tc_caching_fragment["{$group}{$id}"]) && $w3tc_caching_fragment["{$group}{$id}"];
}

/**
 * Internal function, sets if page fragment by $id and $group is being cached
 * @param string $id fragment id
 * @param string $group which group fragment belongs too
 */
function _w3tc_caching_fragment($id, $group = '') {
    global $w3tc_caching_fragment;
    $w3tc_caching_fragment["{$group}{$id}"] = true;
}

/**
 * @param string $extension
 * @param string $setting
 * @param null $config
 * @param string $default
 * @return null|array|bool|string|int returns null if key not set or provided default value
 */
function w3tc_get_extension_config($extension, $setting = '', $config = null, $default = '') {
    /**
     * @var W3_Config $config
     */
    if (is_null($config))
        $config = w3_instance('W3_Config');
    $val = null;
    $extensions = $config->get_array('extensions.settings');
    if ($setting && isset($extensions[$extension][$setting])) {
        $val =  $extensions[$extension][$setting];
    } elseif(empty($setting)) {
        return $extensions[$extension];
    } elseif (!empty($setting)) {
        return $default;
    }
    return $val;
}

/**
 * Shortcut for varnish flush
 *
 * @return boolean
 */
function w3tc_varnish_flush() {
    $w3_pgcache = w3_instance('W3_CacheFlush');
    return $w3_pgcache->varnish_flush();
}

/**
 * Shortcut for post varnish flush
 *
 * @param integer $post_id
 * @return boolean
 */
function w3tc_varnish_flush_post($post_id) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');

    return $w3_cacheflush->varnish_flush_post($post_id);
}

/**
 * Shortcut for url varnish flush
 *
 * @param string $url
 * @return boolean
 */
function w3tc_varnish_flush_url($url) {
    $w3_cacheflush = w3_instance('W3_CacheFlush');

    return $w3_cacheflush->varnish_flush_url($url);
}

/**
 * Shortcut for url varnish flush
 */
function w3tc_flush_all() {
    /**
     * @var $w3_cacheflush W3_CacheFlush
     */
    $w3_cacheflush = w3_instance('W3_CacheFlush');

    $w3_cacheflush->flush_all();
}

/**
 * Deletes files.
 *
 * @param string $mask regular expression matching files to be deleted
 * @param bool $http if delete request should be made over http to current site. Default false.
 * @return mixed
 */
function w3tc_apc_delete_files_based_on_regex($mask, $http = false) {
    if (!$http) {
        $w3_cacheflush = w3_instance('W3_CacheFlush');

        return $w3_cacheflush->apc_delete_files_based_on_regex($mask);
    } else {
        $url = WP_PLUGIN_URL . '/' . dirname(W3TC_FILE) . '/pub/apc.php';
        $path = parse_url($url, PHP_URL_PATH);
        $post = array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'body' => array( 'nonce' => wp_hash($path), 'command' => 'delete_files', 'regex' => $mask),
        );
        $result = wp_remote_post($url, $post);
        if (is_wp_error($result)) {
            return $result;
        } elseif ($result['response']['code'] != '200') {
            return $result['response']['code'];
        }

        return true;
    }
}

/**
 * Reloads files.
 * @param string[] $files list of files supports, fullpath, from root, wp-content
 * @param bool $http if delete request should be made over http to current site. Default false.
 * @return mixed
 */
function w3tc_apc_reload_files($files, $http = false) {

    if (!$http) {
        $w3_cacheflush = w3_instance('W3_CacheFlush');

        return $w3_cacheflush->apc_reload_files($files);
    } else {
        $url = WP_PLUGIN_URL . '/' . dirname(W3TC_FILE) . '/pub/apc.php';
        $path = parse_url($url, PHP_URL_PATH);

        $post = array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'body' => array( 'nonce' => wp_hash($path), 'command' => 'reload_files', 'files' => $files),
        );
        $result = wp_remote_post($url, $post);
        if (is_wp_error($result)) {
            return $result;
        } elseif ($result['response']['code'] != '200') {
            return $result['response']['code'];
        }

        return true;
    }
}

/**
 * Use w3tc_get_themes() to get a list themenames to use with user agent groups
 * @param $group_name
 * @param string $theme the themename default is default theme. For childtheme it should be parentthemename/childthemename
 * @param string $redirect
 * @param array $agents Remember to escape special characters like spaces, dots or dashes with a backslash. Regular expressions are also supported.
 * @param bool $enabled
 */
function w3tc_save_user_agent_group($group_name, $theme = 'default', $redirect = '', $agents = array(), $enabled = false) {
    /**
     * @var $w3_mobile W3_Mobile
     */
    $w3_mobile = w3_instance('W3_Mobile');
    $w3_mobile->save_group($group_name, $theme, $redirect, $agents, $enabled);
}

/**
 * @param $group
 */
function w3tc_delete_user_agent_group($group) {
    /**
     * @var $w3_mobile W3_Mobile
     */
    $w3_mobile = w3_instance('W3_Mobile');
    $w3_mobile->delete_group($group);

}

/**
 * @param $group
 * @return mixed
 */
function w3tc_get_user_agent_group($group) {
    /**
     * @var $w3_mobile W3_Mobile
     */
    $w3_mobile = w3_instance('W3_Mobile');
    return $w3_mobile->get_group_values($group);
}

/**
 * Use w3tc_get_themes() to get a list themenames to use with referrer groups
 * @param $group_name
 * @param string $theme the themename default is default theme. For childtheme it should be parentthemename/childthemename
 * @param string $redirect
 * @param array $referrers Remember to escape special characters like spaces, dots or dashes with a backslash. Regular expressions are also supported.
 * @param bool $enabled
 */
function w3tc_save_referrer_group($group_name, $theme = 'default', $redirect = '', $referrers = array(), $enabled = false) {
    /**
     * @var $w3_referrer W3_Referrer
     */
    $w3_referrer = w3_instance('W3_Referrer');
    $w3_referrer->save_group($group_name, $theme, $redirect, $referrers, $enabled);
}

/**
 * @param $group
 */
function w3tc_delete_referrer_group($group) {
    /**
     * @var $w3_referrer W3_Referrer
     */
    $w3_referrer = w3_instance('W3_Referrer');
    $w3_referrer->delete_group($group);
}

/**
 * @param $group
 * @return mixed
 */
function w3tc_get_referrer_group($group) {
    /**
     * @var $w3_mobile W3_Referrer
     */
    $w3_referrer = w3_instance('W3_Referrer');
    return $w3_referrer->get_group_values($group);
}
