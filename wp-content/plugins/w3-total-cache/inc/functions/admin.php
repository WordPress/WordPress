<?php

/**
 * Redirects when in WP Admin
 * @param array $params
 * @param bool $check_referrer
 * @param string $page
 */
function w3_admin_redirect($params = array(), $check_referrer = false, $page = '') {
    w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

    $url = W3_Request::get_string('redirect');
    $page_url = W3_Request::get_string('page');
    if ($url == '') {
        if ($check_referrer && !empty($_SERVER['HTTP_REFERER'])) {
            $url = $_SERVER['HTTP_REFERER'];
        } else {
            $url = 'admin.php';
            if (empty($page))
                $page = $page_url;
            $params = array_merge(array(
                'page' => $page
            ), $params);
        }
    }

    w3_redirect($url, $params);
}

/**
 * Redirect function to current admin page with errors and messages specified
 *
 * @param array $params
 * @param array $errors
 * @param array $notes
 * @param bool $check_referrer
 * @return void
 */
function w3_admin_redirect_with_custom_messages($params, $errors = null, $notes = null, $check_referrer = false) {
    if (empty($errors) && w3_admin_single_system_item($notes)) {
        w3_admin_redirect(array_merge($params, array(
            'w3tc_note' => $notes[0])), $check_referrer);
        return;
    }
    if (w3_admin_single_system_item($errors) && empty($notes)) {
        w3_admin_redirect(array_merge($params, array(
            'w3tc_error' => $errors[0])), $check_referrer);
        return;
    }

    $message_id = uniqid();
    set_transient('w3tc_message.' . $message_id,
        array('errors' => $errors, 'notes' => $notes), 600);

    w3_admin_redirect(array_merge($params, array(
        'w3tc_message' => $message_id)), $check_referrer);
}


/*
 * Checks if contains single message item
 *
 * @param $a array
 * @return boolean
 */
function w3_admin_single_system_item($a) {
    if (!is_array($a) || count($a) != 1)
        return false;

    $pos = strpos($a[0], ' ');
    if ($pos === false)
        return true;

    return false;
}

/**
 * Save config, can't decline save process. (difference from action_save)
 *
 * Do some actions on config keys update
 * Used in several places such as:
 *
 * 1. common config save
 * 2. import settings
 *
 * @param W3_Config $current_config
 * @param W3_Config $new_config
 * @param W3_ConfigAdmin $new_config_admin
 * @return bool
 * @throws Exception
 */
function w3_config_save($current_config, $new_config, $new_config_admin) {
    $master_config = ($new_config->is_master() ? $new_config : new W3_Config(true));

    if ($master_config->get_integer('common.instance_id', 0) == 0) {
        $master_config->set('common.instance_id', mt_rand());
        if (!$new_config->is_master())
            $master_config->save();
    }

    $old_config = new W3_Config();
    $browsercache_dependencies = array();

    if ($new_config->get_boolean('browsercache.enabled')) {
        $browsercache_dependencies = array_merge($browsercache_dependencies, array(
            'browsercache.cssjs.replace',
            'browsercache.html.replace',
            'browsercache.other.replace'
        ));

        if ($new_config->get_boolean('browsercache.cssjs.replace')) {
            $browsercache_dependencies = array_merge($browsercache_dependencies, array(
                'browsercache.cssjs.compression',
                'browsercache.cssjs.expires',
                'browsercache.cssjs.lifetime',
                'browsercache.cssjs.cache.control',
                'browsercache.cssjs.cache.policy',
                'browsercache.cssjs.etag',
                'browsercache.cssjs.w3tc'
            ));
        }

        if ($new_config->get_boolean('browsercache.html.replace')) {
            $browsercache_dependencies = array_merge($browsercache_dependencies, array(
                'browsercache.html.compression',
                'browsercache.html.expires',
                'browsercache.html.lifetime',
                'browsercache.html.cache.control',
                'browsercache.html.cache.policy',
                'browsercache.html.etag',
                'browsercache.html.w3tc'
            ));
        }

        if ($new_config->get_boolean('browsercache.other.replace')) {
            $browsercache_dependencies = array_merge($browsercache_dependencies, array(
                'browsercache.other.compression',
                'browsercache.other.expires',
                'browsercache.other.lifetime',
                'browsercache.other.cache.control',
                'browsercache.other.cache.policy',
                'browsercache.other.etag',
                'browsercache.other.w3tc'
            ));
        }
    }

    /**
     * Show need empty page cache notification
     */
    if ($new_config->get_boolean('pgcache.enabled')) {

        $pgcache_dependencies = array_merge($browsercache_dependencies, array(
            'pgcache.debug',
            'dbcache.enabled',
            'objectcache.enabled',
            'minify.enabled',
            'mobile.enabled',
            'referrer.enabled'
        ));

        if ($new_config->get_boolean('dbcache.enabled')) {
            $pgcache_dependencies = array_merge($pgcache_dependencies, array(
                'dbcache.debug'
            ));
        }

        if ($new_config->get_boolean('objectcache.enabled')) {
            $pgcache_dependencies = array_merge($pgcache_dependencies, array(
                'objectcache.debug'
            ));
        }

        if ($new_config->get_boolean('minify.enabled')) {
            $pgcache_dependencies = array_merge($pgcache_dependencies, array(
                'minify.auto',
                'minify.debug',
                'minify.rewrite',
                'minify.html.enable',
                'minify.html.engine',
                'minify.html.inline.css',
                'minify.html.inline.js',
                'minify.html.strip.crlf',
                'minify.html.comments.ignore',
                'minify.css.enable',
                'minify.css.engine',
                'minify.css.groups',
                'minify.js.enable',
                'minify.js.engine',
                'minify.js.groups',
                'minify.htmltidy.options.clean',
                'minify.htmltidy.options.hide-comments',
                'minify.htmltidy.options.wrap',
                'minify.reject.logged',
                'minify.reject.ua',
                'minify.reject.uri'
            ));
        }
        /**
         * @var W3_ModuleStatus $modules
         */
        $modules = w3_instance('W3_ModuleStatus');
        if ($modules->is_running('cdn')) {
            $pgcache_dependencies = array_merge($pgcache_dependencies, array(
                'cdn.enabled',
                'cdn.debug',
                'cdn.engine',
                'cdn.uploads.enable',
                'cdn.includes.enable',
                'cdn.includes.files',
                'cdn.theme.enable',
                'cdn.theme.files',
                'cdn.minify.enable',
                'cdn.custom.enable',
                'cdn.custom.files',
                'cdn.ftp.domain',
                'cdn.ftp.ssl',
                'cdn.s3.cname',
                'cdn.s3.ssl',
                'cdn.cf.cname',
                'cdn.cf.ssl',
                'cdn.cf2.cname',
                'cdn.cf2.ssl',
                'cdn.rscf.cname',
                'cdn.rscf.ssl',
                'cdn.azure.cname',
                'cdn.azure.ssl',
                'cdn.mirror.domain',
                'cdn.mirror.ssl',
                'cdn.netdna.domain',
                'cdn.netdna.ssl',
                'cdn.cotendo.domain',
                'cdn.cotendo.ssl',
                'cdn.edgecast.domain',
                'cdn.edgecast.ssl',
                'cdn.att.domain',
                'cdn.att.ssl',
                'cdn.reject.logged_roles',
                'cdn.reject.roles',
                'cdn.reject.ua',
                'cdn.reject.uri',
                'cdn.reject.files'
            ));
        } elseif ($old_config->get_boolean('cdn.enabled') && !$new_config->get_boolean('cdn.enabled')) {
            $pgcache_dependencies = array_merge($pgcache_dependencies, array('cdn.enabled'));
        }

        if ($new_config->get_boolean('mobile.enabled')) {
            $pgcache_dependencies = array_merge($pgcache_dependencies, array(
                'mobile.rgroups'
            ));
        }

        if ($new_config->get_boolean('referrer.enabled')) {
            $pgcache_dependencies = array_merge($pgcache_dependencies, array(
                'referrer.rgroups'
            ));
        }


        if ($new_config->get_boolean('browsercache.enabled') &&
            $new_config->get_string('pgcache.engine') == 'file_generic') {
            $pgcache_dependencies = array_merge($pgcache_dependencies, array(
                'browsercache.html.last_modified',
                'browsercache.other.last_modified'
            ));
        }

        $old_pgcache_dependencies_values = array();
        $new_pgcache_dependencies_values = array();

        foreach ($pgcache_dependencies as $pgcache_dependency) {
            $old_pgcache_dependencies_values[] = $old_config->get($pgcache_dependency);
            $new_pgcache_dependencies_values[] = $new_config->get($pgcache_dependency);
        }

        if (serialize($old_pgcache_dependencies_values) != serialize($new_pgcache_dependencies_values)) {
            $new_config->set('notes.need_empty_pgcache', true);
        }
    }

    /**
     * Show need empty minify notification
     */
    if ($current_config->get_boolean('minify.enabled') && $new_config->get_boolean('minify.enabled') && (($new_config->get_boolean('minify.css.enable') && ($new_config->get_boolean('minify.auto') || count($new_config->get_array('minify.css.groups')))) || ($new_config->get_boolean('minify.js.enable') && ($new_config->get_boolean('minify.auto') || count($new_config->get_array('minify.js.groups')))))) {
        $minify_dependencies = array_merge($browsercache_dependencies, array(
            'minify.auto',
            'minify.debug',
            'minify.options',
            'minify.symlinks',
            'minify.css.enable',
            'minify.js.enable'
        ));

        if ($new_config->get_boolean('minify.css.enable') && ($new_config->get_boolean('minify.auto') || count($new_config->get_array('minify.css.groups')))) {
            $minify_dependencies = array_merge($minify_dependencies, array(
                'minify.css.engine',
                'minify.css.combine',
                'minify.css.strip.comments',
                'minify.css.strip.crlf',
                'minify.css.imports',
                'minify.css.groups',
                'minify.yuicss.path.java',
                'minify.yuicss.path.jar',
                'minify.yuicss.options.line-break',
                'minify.csstidy.options.remove_bslash',
                'minify.csstidy.options.compress_colors',
                'minify.csstidy.options.compress_font-weight',
                'minify.csstidy.options.lowercase_s',
                'minify.csstidy.options.optimise_shorthands',
                'minify.csstidy.options.remove_last_;',
                'minify.csstidy.options.case_properties',
                'minify.csstidy.options.sort_properties',
                'minify.csstidy.options.sort_selectors',
                'minify.csstidy.options.merge_selectors',
                'minify.csstidy.options.discard_invalid_properties',
                'minify.csstidy.options.css_level',
                'minify.csstidy.options.preserve_css',
                'minify.csstidy.options.timestamp',
                'minify.csstidy.options.template'
            ));
        }

        if ($new_config->get_boolean('minify.js.enable') && ($new_config->get_boolean('minify.auto') || count($new_config->get_array('minify.js.groups')))) {
            $minify_dependencies = array_merge($minify_dependencies, array(
                'minify.js.engine',
                'minify.js.combine.header',
                'minify.js.combine.body',
                'minify.js.combine.footer',
                'minify.js.strip.comments',
                'minify.js.strip.crlf',
                'minify.js.groups',
                'minify.yuijs.path.java',
                'minify.yuijs.path.jar',
                'minify.yuijs.options.line-break',
                'minify.yuijs.options.nomunge',
                'minify.yuijs.options.preserve-semi',
                'minify.yuijs.options.disable-optimizations',
                'minify.ccjs.path.java',
                'minify.ccjs.path.jar',
                'minify.ccjs.options.compilation_level',
                'minify.ccjs.options.formatting'
            ));
        }

        /**
         * @var W3_ModuleStatus $modules
         */
        $modules = w3_instance('W3_ModuleStatus');
        if ($modules->is_running('cdn')) {
            $minify_dependencies = array_merge($minify_dependencies, array(
                'cdn.engine','cdn.enabled'
            ));
        } elseif ($old_config->get_boolean('cdn.enabled') && !$new_config->get_boolean('cdn.enabled')) {
            $minify_dependencies = array_merge($minify_dependencies, array('cdn.enabled'));
        }

        $old_minify_dependencies_values = array();
        $new_minify_dependencies_values = array();

        foreach ($minify_dependencies as $minify_dependency) {
            $old_minify_dependencies_values[] = $old_config->get($minify_dependency);
            $new_minify_dependencies_values[] = $new_config->get($minify_dependency);
        }

        if (serialize($old_minify_dependencies_values) != serialize($new_minify_dependencies_values)) {
            $new_config->set('notes.need_empty_minify', true);
        }
    }

    if ($new_config->get_boolean('cdn.enabled') && !w3_is_cdn_mirror($new_config->get_string('cdn.engine'))) {
        /**
         * Show notification when CDN enabled
         */
        if (!$old_config->get_boolean('cdn.enabled')) {
            $new_config->set('notes.cdn_upload', true);
        }

        /**
         * Show notification when Browser Cache settings changes
         */
        $cdn_dependencies = array(
            'browsercache.enabled'
        );

        if ($new_config->get_boolean('cdn.enabled')) {
            $cdn_dependencies = array(
                'browsercache.cssjs.compression',
                'browsercache.cssjs.expires',
                'browsercache.cssjs.lifetime',
                'browsercache.cssjs.cache.control',
                'browsercache.cssjs.cache.policy',
                'browsercache.cssjs.etag',
                'browsercache.cssjs.w3tc',
                'browsercache.html.compression',
                'browsercache.html.expires',
                'browsercache.html.lifetime',
                'browsercache.html.cache.control',
                'browsercache.html.cache.policy',
                'browsercache.html.etag',
                'browsercache.html.w3tc',
                'browsercache.other.compression',
                'browsercache.other.expires',
                'browsercache.other.lifetime',
                'browsercache.other.cache.control',
                'browsercache.other.cache.policy',
                'browsercache.other.etag',
                'browsercache.other.w3tc'
            );
        }

        $old_cdn_dependencies_values = array();
        $new_cdn_dependencies_values = array();

        foreach ($cdn_dependencies as $cdn_dependency) {
            $old_cdn_dependencies_values[] = $old_config->get($cdn_dependency);
            $new_cdn_dependencies_values[] = $new_config->get($cdn_dependency);
        }

        if (serialize($old_cdn_dependencies_values) != serialize($new_cdn_dependencies_values)) {
            $new_config->set('notes.cdn_reupload', true);
        }
    }

    /**
     * Show need empty object cache notification
     */
    if ($current_config->get_boolean('objectcache.enabled')) {
        $objectcache_dependencies = array(
            'objectcache.groups.global',
            'objectcache.groups.nonpersistent'
        );

        $old_objectcache_dependencies_values = array();
        $new_objectcache_dependencies_values = array();

        foreach ($objectcache_dependencies as $objectcache_dependency) {
            $old_objectcache_dependencies_values[] = $old_config->get($objectcache_dependency);
            $new_objectcache_dependencies_values[] = $new_config->get($objectcache_dependency);
        }

        if (serialize($old_objectcache_dependencies_values) != serialize($new_objectcache_dependencies_values)) {
            $new_config->set('notes.need_empty_objectcache', true);
        }
    }

    if ($current_config->get_boolean('newrelic.enabled')) {
        if ($current_config->get_boolean('pgcache.enabled')) {
            if (w3_is_network() && $current_config->get_boolean('common.force_master')) {
                $new_config->set('pgcache.late_init', true);
            }
        }
    }

    do_action('w3tc_saved_options', $new_config, $new_config_admin);

    /**
     * Save config
     */
    try {
        $new_config_admin->save();
        $new_config->save();
    } catch (Exception $ex) {
        // try to fix environment, it potentially can be fixed silently
        // dont show error here, it will be called again later
        // in admin_notices
        try {
            $environment = w3_instance('W3_AdminEnvironment');
            $environment->fix_in_wpadmin($new_config);
        } catch (Exception $ex) {
        }

        // retry save process and complain now on failure
        try {
            $new_config_admin->save();
            $new_config->save();
        } catch (Exception $ex) {
            throw new Exception(
                '<strong>Can\'t change configuration</strong>: ' .
                $ex->getMessage());
        }
    }

    $w3_plugin_cdn = w3_instance('W3_Plugin_CdnAdmin');

    /**
     * Empty caches on engine change or cache enable/disable
     */
    if ($old_config->get_string('pgcache.engine') != $new_config->get_string('pgcache.engine') || $old_config->get_string('pgcache.enabled') != $new_config->get_string('pgcache.enabled')) {
        w3tc_pgcache_flush();
    }

    if ($old_config->get_string('dbcache.engine') != $new_config->get_string('dbcache.engine') || $old_config->get_string('dbcache.enabled') != $new_config->get_string('dbcache.enabled')) {
        w3tc_dbcache_flush();
    }

    if ($old_config->get_string('objectcache.engine') != $new_config->get_string('objectcache.engine') || $old_config->get_string('objectcache.enabled') != $new_config->get_string('objectcache.enabled')) {
        w3tc_objectcache_flush();
    }

    if ($old_config->get_string('minify.engine') != $new_config->get_string('minify.engine') || $old_config->get_string('minify.enabled') != $new_config->get_string('minify.enabled')) {
        w3tc_minify_flush();
    }

    /**
     * Update CloudFront CNAMEs
     */
    $update_cf_cnames = false;

    if ($new_config->get_boolean('cdn.enabled') && in_array($new_config->get_string('cdn.engine'), array('cf', 'cf2'))) {
        if ($new_config->get_string('cdn.engine') == 'cf') {
            $old_cnames = $old_config->get_array('cdn.cf.cname');
            $new_cnames = $new_config->get_array('cdn.cf.cname');
        } else {
            $old_cnames = $old_config->get_array('cdn.cf2.cname');
            $new_cnames = $new_config->get_array('cdn.cf2.cname');
        }

        if (count($old_cnames) != count($new_cnames) || count(array_diff($old_cnames, $new_cnames))) {
            $update_cf_cnames = true;
        }
    }

    /**
     * Refresh config
     */
    $current_config->load();

    /**
     * React to config changes
     */
    $environment = w3_instance('W3_AdminEnvironment');
    $environment->fix_on_event($new_config, 'config_change', $old_config);

    /**
     * Update support us option
     */
    w3_instance('W3_AdminLinks')->link_update($current_config);

    /**
     * Auto upload minify files to CDN
     */
    if ($new_config->get_boolean('minify.enabled') && $new_config->get_boolean('minify.upload') && $new_config->get_boolean('cdn.enabled') && !w3_is_cdn_mirror($new_config->get_string('cdn.engine'))) {
        w3_cdn_upload_minify();
    }

    /**
     * Auto upload browsercache files to CDN
     */
    if ($new_config->get_boolean('cdn.enabled') && $new_config->get_string('cdn.engine') == 'ftp') {
        w3_cdn_delete_browsercache($current_config);
        w3_cdn_upload_browsercache($current_config);
    }

    /**
     * Update CloudFront CNAMEs
     */
    if ($update_cf_cnames) {
        $error = null;
        $w3_plugin_cdn->update_cnames($error);
    }

    return true;
}



/**
 * Uploads minify files to CDN
 *
 * @return void
 */
function w3_cdn_upload_minify() {
    $w3_plugin_cdn = w3_instance('W3_Plugin_Cdn');
    $w3_plugin_cdncommon = w3_instance('W3_Plugin_CdnCommon');

    $files = $w3_plugin_cdn->get_files_minify();

    $upload = array();
    $results = array();

    foreach ($files as $file) {
        $upload[] = $w3_plugin_cdncommon->build_file_descriptor($w3_plugin_cdncommon->docroot_filename_to_absolute_path($file),
            $w3_plugin_cdncommon->uri_to_cdn_uri($w3_plugin_cdncommon->docroot_filename_to_uri($file)));
    }

    $w3_plugin_cdncommon->upload($upload, true, $results);
}

/**
 * Uploads Browser Cache .htaccess to FTP
 *
 * @var W3_Config $config
 * @return void
 */
function w3_cdn_upload_browsercache($config) {
    $w3_plugin_cdncommon = w3_instance('W3_Plugin_CdnCommon');
    $w3_plugin_cdnadmin = w3_instance('W3_Plugin_CdnAdmin');

    $ce = w3_instance('W3_CdnAdminEnvironment');
    $rules = $ce->rules_generate_for_ftp($config);

    if ($config->get_boolean('browsercache.enabled')) {
        $be = w3_instance('W3_BrowserCacheAdminEnvironment');
        $rules .= $be->rules_cache_generate_for_ftp($config);
    }

    $cdn_path = w3_get_cdn_rules_path();
    $tmp_path = W3TC_CACHE_TMP_DIR . '/' . $cdn_path;

    if (@file_put_contents($tmp_path, $rules)) {
        $results = array();
        $upload = array($w3_plugin_cdncommon->build_file_descriptor($tmp_path, $cdn_path));

        $w3_plugin_cdncommon->upload($upload, true, $results);
    }
}

/**
 * Deletes Browser Cache .htaccess from FTP
 *
 * @return void
 */
function w3_cdn_delete_browsercache() {
    $w3_plugin_cdn = w3_instance('W3_Plugin_CdnCommon');

    $cdn_path = w3_get_cdn_rules_path();
    $tmp_path = W3TC_CACHE_TMP_DIR . '/' . $cdn_path;

    $results = array();
    $delete = array(
        $w3_plugin_cdn->build_file_descriptor($tmp_path, $cdn_path)
    );

    $w3_plugin_cdn->delete($delete, false, $results);
}


/**
 * Returns cookie domain
 *
 * @return string
 */
function w3_get_cookie_domain() {
    $site_url = get_option('siteurl');
    $parse_url = @parse_url($site_url);

    if ($parse_url && !empty($parse_url['host'])) {
        return $parse_url['host'];
    }

    return $_SERVER['HTTP_HOST'];
}

/*
 * Returns current w3tc admin page
 */
function w3tc_get_current_page() {
    w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

    $page = W3_Request::get_string('page');

    switch (true) {
        case ($page == 'w3tc_dashboard'):
        case ($page == 'w3tc_general'):
        case ($page == 'w3tc_pgcache'):
        case ($page == 'w3tc_minify'):
        case ($page == 'w3tc_dbcache'):
        case ($page == 'w3tc_objectcache'):
        case ($page == 'w3tc_fragmentcache'):
        case ($page == 'w3tc_browsercache'):
        case ($page == 'w3tc_mobile'):
        case ($page == 'w3tc_referrer'):
        case ($page == 'w3tc_cdn'):
        case ($page == 'w3tc_monitoring'):
        case ($page == 'w3tc_extensions'):
        case ($page == 'w3tc_install'):
        case ($page == 'w3tc_faq'):
        case ($page == 'w3tc_about'):
        case ($page == 'w3tc_support'):
            break;

        default:
            $page = 'w3tc_dashboard';
    }

    return $page;
}

/**
 * Check if current page is a W3TC admin page
 * @return bool
 */
function is_w3tc_admin_page() {
    return isset($_GET['page']) && substr($_GET['page'], 0, 5) == 'w3tc_';
}


function w3tc_make_track_call($params) {
    wp_remote_post(W3TC_TRACK_URL, array(
        'timeout' => 45,
        'redirection' => 5,
        'blocking' => false,
        'headers' => array(),
        'body' => array_merge($params, array('id' => md5(home_url())))
    ));
}

/**
 * Returns current WordPress page
 * @return string
 */
function w3tc_get_current_wp_page() {
    w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
    return W3_Request::get_string('page');
}
