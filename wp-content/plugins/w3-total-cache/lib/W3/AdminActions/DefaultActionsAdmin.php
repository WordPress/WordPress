<?php
if (!defined('W3TC')) {
    die();
}
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');

define('W3TC_PLUGIN_TOTALCACHE_REGEXP_COOKIEDOMAIN', '~define\s*\(\s*[\'"]COOKIE_DOMAIN[\'"]\s*,.*?\)~is');

class W3_AdminActions_DefaultActionsAdmin {

    /**
     * @var W3_Config $_config
     */
    private $_config = null;

    /**
     * @var W3_ConfigAdmin $_config_admin
     */
    private $_config_admin = null;

    /**
     * @var W3_Config $_config_master
     */
    private $_config_master = null;

    /**
     * Current page
     * @var null|string
     */
    private $_page = null;

    function __construct() {
        $this->_config = w3_instance('W3_Config');
        $this->_config_admin = w3_instance('W3_ConfigAdmin');
        $this->_config_master = new W3_Config(true);
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        $this->_page = w3tc_get_current_page();
    }

    /**
     * Start previewing
     */
    function action_default_previewing() {
        setcookie('w3tc_preview', true, 0, '/');
        w3_redirect(w3_get_home_url());
    }

    /**
     * Stop previewing the site
     */
    function action_default_stop_previewing() {
        setcookie("w3tc_preview", "", time()-3600, '/');
        w3_admin_redirect(array(), true);
    }

    /**
     * Hide note action
     *
     * @return void
     */
    function action_default_save_licence_key() {
        $license = W3_Request::get_string('license_key');
        try {
            $old_config = new W3_Config();

            $this->_config->set('plugin.license_key', $license);
            $this->_config->save();

            w3_instance('W3_Licensing')->possible_state_change($this->_config,
+                $old_config);
        } catch(Exception $ex){
            echo json_encode(array('result' => 'failed'));
            exit;
        }
        echo json_encode(array('result' => 'success'));
        exit;
    }

    /**
     * Hide note action
     *
     * @return void
     */
    function action_default_hide_note() {
        $note = W3_Request::get_string('note');
        $admin = W3_Request::get_boolean('admin');
        $setting = sprintf('notes.%s', $note);
        if ($admin) {
            $this->_config_admin->set($setting, false);
            $this->_config_admin->save();
        } else {
            $this->_config->set($setting, false);
            $this->_config->save();
        }
        do_action("w3tc_hide_button-{$note}");
        w3_admin_redirect(array(), true);
    }

    /**
     * Hide note custom action
     */
    function action_default_hide_note_custom() {
        $note = W3_Request::get_string('note');
        do_action("w3tc_hide_button_custom-{$note}");
        w3_admin_redirect(array(), true);
    }

    /**
     *
     */
    function action_default_remove_add_in() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/activation.php');
        $module = W3_Request::get_string('w3tc_default_remove_add_in');
        switch($module) {
            case 'pgcache':
                w3_wp_delete_file(W3TC_ADDIN_FILE_ADVANCED_CACHE);
                $src = W3TC_INSTALL_FILE_ADVANCED_CACHE;
                $dst = W3TC_ADDIN_FILE_ADVANCED_CACHE;
                try {
                    w3_wp_copy_file($src, $dst);
                } catch (FilesystemOperationException $ex) {}
                break;
            case 'dbcache':
                w3_wp_delete_file(W3TC_ADDIN_FILE_DB);
                break;
            case 'objectcache':
                w3_wp_delete_file(W3TC_ADDIN_FILE_OBJECT_CACHE);
                break;
        }
        w3_admin_redirect(array(
            'w3tc_note' => 'add_in_removed'
        ), true);
    }

    /**
     * Options save action
     *
     * @return void
     */
    function action_save_options() {
        if (!current_user_can('manage_options'))
            wp_die( __('You do not have the rights to perform this action.', 'w3-total-cache'));

        /**
         * Redirect params
         */
        $params = array();

        /**
         * Store error message regarding permalink not enabled
         */
        $redirect_permalink_error = '';

        /**
         * Read config
         * We should use new instance of WP_Config object here
         */
        $config = new W3_Config();

        $this->read_request($config);

        $config_admin = new W3_ConfigAdmin();
        $this->read_request($config_admin);

        if ($this->_page == 'w3tc_dashboard') {
            if (W3_Request::get_boolean('maxcdn')) {
                $config->set('cdn.enabled', true);
                $config->set('cdn.engine', 'maxcdn');
            }
        }

        /**
         * General tab
         */
        if ($this->_page == 'w3tc_general') {
            $file_nfs = W3_Request::get_boolean('file_nfs');
            $file_locking = W3_Request::get_boolean('file_locking');

            $config->set('pgcache.file.nfs', $file_nfs);
            $config->set('minify.file.nfs', $file_nfs);

            $config->set('dbcache.file.locking', $file_locking);
            $config->set('objectcache.file.locking', $file_locking);
            $config->set('pgcache.file.locking', $file_locking);
            $config->set('minify.file.locking', $file_locking);

            if (is_network_admin()) {
                if (($this->_config->get_boolean('common.force_master') !==
                        $config->get_boolean('common.force_master')) ||
                    //Blogs cache is wrong so empty it.
                    (!w3_force_master() && $this->_config->get_boolean('common.force_master')
                        && $config->get_boolean('common.force_master')) ||
                    (w3_force_master() && !$this->_config->get_boolean('common.force_master')
                        && !$config->get_boolean('common.force_master'))) {
                    @unlink(W3TC_CACHE_BLOGMAP_FILENAME);
                    $blogmap_dir = dirname(W3TC_CACHE_BLOGMAP_FILENAME) . '/' .
                        basename(W3TC_CACHE_BLOGMAP_FILENAME, '.php') . '/';
                    if (@is_dir($blogmap_dir))
                        w3_rmdir($blogmap_dir);
                }
                if ($config->get_boolean('common.force_master'))
                    $config_admin->set('common.visible_by_master_only', true);
            }

            /**
             * Check permalinks for page cache
             */
            if ($config->get_boolean('pgcache.enabled') && $config->get_string('pgcache.engine') == 'file_generic'
                && !get_option('permalink_structure')) {
                $config->set('pgcache.enabled', false);
                $redirect_permalink_error = 'fancy_permalinks_disabled_pgcache';
            }

            /**
             * Get New Relic application id
             */
            if ($config->get_boolean('newrelic.enabled')) {
                $method = W3_Request::get_string('application_id_method');
                $newrelic_prefix = '';
                if (w3_is_network() && w3_get_blog_id() != 0)
                    $newrelic_prefix = $this->_config->get_string('newrelic.appname_prefix');
                if (($newrelic_api_key = $config->get_string('newrelic.api_key')) && !$config->get_string('newrelic.account_id')) {
                    $nerser = w3_instance('W3_NewRelicService');
                    $account_id = $nerser->get_account_id($newrelic_api_key);
                    $config->set('newrelic.account_id', $account_id);
                }

                if ($method == 'dropdown' && $config->get_string('newrelic.application_id')) {
                    $application_id = $config->get_string('newrelic.application_id');
                    if ($config->get_string('newrelic.api_key') && $config->get_string('newrelic.account_id')) {
                        w3_require_once(W3TC_LIB_W3_DIR .'/NewRelicService.php');
                        $nerser = new W3_NewRelicService($config->get_string('newrelic.api_key'),
                            $config->get_string('newrelic.account_id'));
                        $appname = $nerser->get_application_name($application_id);
                        $config->set('newrelic.appname', $appname);
                    }
                } else if ($method == 'manual' && $config->get_string('newrelic.appname')) {
                    if ($newrelic_prefix != '' && strpos($config->get_string('newrelic.appname'), $newrelic_prefix) === false) {
                        $application_name = $newrelic_prefix . $config->get_string('newrelic.appname');
                        $config->set('newrelic.appname', $application_name);
                    } else
                        $application_name = $config->get_string('newrelic.appname');

                    if ($config->get_string('newrelic.api_key') && $config->get_string('newrelic.account_id') ) {
                        w3_require_once(W3TC_LIB_W3_DIR .'/NewRelicService.php');
                        $nerser = new W3_NewRelicService($config->get_string('newrelic.api_key'),
                            $config->get_string('newrelic.account_id'));
                        $application_id = $nerser->get_application_id($application_name);
                        if ($application_id)
                            $config->set('newrelic.application_id', $application_id);
                    }
                }
            }

            if (($config->get_boolean('minify.enabled') && !$this->_config->get_boolean('minify.enabled'))
                ||
                ($config->get_boolean('minify.enabled') && $config->get_boolean('browsercache.enabled')
                    && !$this->_config->get_boolean('browsercache.enabled'))
                ||
                ($config->get_boolean('minify.enabled') && $config->get_boolean('minify.auto') &&
                    !$this->_config->get_boolean('minify.auto'))
                ||
                ($config->get_boolean('minify.enabled') &&
                    $config->get_string('minify.engine') != $this->_config->get_string('minify.engine'))) {
                delete_transient('w3tc_minify_tested_filename_length');
            }
            if (!w3_is_pro($this->_config))
                delete_transient('w3tc_license_status');
        }

        /**
         * Minify tab
         */
        if ($this->_page == 'w3tc_minify' && !$this->_config->get_boolean('minify.auto')) {
            $js_groups = array();
            $css_groups = array();

            $js_files = W3_Request::get_array('js_files');
            $css_files = W3_Request::get_array('css_files');

            foreach ($js_files as $theme => $templates) {
                foreach ($templates as $template => $locations) {
                    foreach ((array) $locations as $location => $types) {
                        foreach ((array) $types as $files) {
                            foreach ((array) $files as $file) {
                                if (!empty($file)) {
                                    $js_groups[$theme][$template][$location]['files'][] = w3_normalize_file_minify($file);
                                }
                            }
                        }
                    }
                }
            }

            foreach ($css_files as $theme => $templates) {
                foreach ($templates as $template => $locations) {
                    foreach ((array) $locations as $location => $files) {
                        foreach ((array) $files as $file) {
                            if (!empty($file)) {
                                $css_groups[$theme][$template][$location]['files'][] = w3_normalize_file_minify($file);
                            }
                        }
                    }
                }
            }

            $config->set('minify.js.groups', $js_groups);
            $config->set('minify.css.groups', $css_groups);

            $js_theme = W3_Request::get_string('js_theme');
            $css_theme = W3_Request::get_string('css_theme');

            $params = array_merge($params, array(
                'js_theme' => $js_theme,
                'css_theme' => $css_theme
            ));
        }

        if ($this->_page == 'w3tc_minify') {
            if ($config->get_integer('minify.auto.filename_length') > 246) {
                $config->set('minify.auto.filename_length', 246);
            }
            delete_transient('w3tc_minify_tested_filename_length');
        }

        /**
         * Browser Cache tab
         */
        if ($this->_page == 'w3tc_browsercache') {
            if ($config->get_boolean('browsercache.enabled') && $config->get_boolean('browsercache.no404wp') && !get_option('permalink_structure')) {
                $config->set('browsercache.no404wp', false);
                $redirect_permalink_error = 'fancy_permalinks_disabled_browsercache';
            }
            $config->set('browsercache.timestamp', time());

            if (in_array($engine = $this->_config->get_string('cdn.engine'), array('netdna', 'maxcdn'))) {
                w3_require_once(W3TC_LIB_NETDNA_DIR . '/NetDNA.php');
                $keys = explode('+', $this->_config->get_string('cdn.'.$engine.'.authorization_key'));
                if (sizeof($keys) == 3) {
                    list($alias, $consumerkey, $consumersecret) =  $keys;
                    try {
                        $api = new NetDNA($alias, $consumerkey, $consumersecret);
                        $disable_cooker_header = $config->get_boolean('browsercache.other.nocookies') ||
                            $config->get_boolean('browsercache.cssjs.nocookies');
                        $api->update_pull_zone($this->_config->get_string('cdn.' . $engine .'.zone_id'), array('ignore_setcookie_header' => $disable_cooker_header));
                    } catch(Exception $ex) {}
                }
            }
        }

        /**
         * Mobile tab
         */
        if ($this->_page == 'w3tc_mobile') {
            $groups = W3_Request::get_array('mobile_groups');

            $mobile_groups = array();
            $cached_mobile_groups = array();

            foreach ($groups as $group => $group_config) {
                $group = strtolower($group);
                $group = preg_replace('~[^0-9a-z_]+~', '_', $group);
                $group = trim($group, '_');

                if ($group) {
                    $theme = (isset($group_config['theme']) ? trim($group_config['theme']) : 'default');
                    $enabled = (isset($group_config['enabled']) ? (boolean) $group_config['enabled'] : true);
                    $redirect = (isset($group_config['redirect']) ? trim($group_config['redirect']) : '');
                    $agents = (isset($group_config['agents']) ? explode("\r\n", trim($group_config['agents'])) : array());

                    $mobile_groups[$group] = array(
                        'theme' => $theme,
                        'enabled' => $enabled,
                        'redirect' => $redirect,
                        'agents' => $agents
                    );

                    $cached_mobile_groups[$group] = $agents;
                }
            }

            /**
             * Allow plugins modify WPSC mobile groups
             */
            $cached_mobile_groups = apply_filters('cached_mobile_groups', $cached_mobile_groups);

            /**
             * Merge existent and delete removed groups
             */
            foreach ($mobile_groups as $group => $group_config) {
                if (isset($cached_mobile_groups[$group])) {
                    $mobile_groups[$group]['agents'] = (array) $cached_mobile_groups[$group];
                } else {
                    unset($mobile_groups[$group]);
                }
            }

            /**
             * Add new groups
             */
            foreach ($cached_mobile_groups as $group => $agents) {
                if (!isset($mobile_groups[$group])) {
                    $mobile_groups[$group] = array(
                        'theme' => '',
                        'enabled' => true,
                        'redirect' => '',
                        'agents' => $agents
                    );
                }
            }

            /**
             * Allow plugins modify W3TC mobile groups
             */
            $mobile_groups = apply_filters('w3tc_mobile_groups', $mobile_groups);

            /**
             * Sanitize mobile groups
             */
            foreach ($mobile_groups as $group => $group_config) {
                $mobile_groups[$group] = array_merge(array(
                    'theme' => '',
                    'enabled' => true,
                    'redirect' => '',
                    'agents' => array()
                ), $group_config);

                $mobile_groups[$group]['agents'] = array_unique($mobile_groups[$group]['agents']);
                $mobile_groups[$group]['agents'] = array_map('strtolower', $mobile_groups[$group]['agents']);
                sort($mobile_groups[$group]['agents']);
            }
            $enable_mobile = false;
            foreach ($mobile_groups as $group_config) {
                if ($group_config['enabled']) {
                    $enable_mobile = true;
                    break;
                }
            }
            $config->set('mobile.enabled', $enable_mobile);
            $config->set('mobile.rgroups', $mobile_groups);
        }

        /**
         * Referrer tab
         */
        if ($this->_page == 'w3tc_referrer') {
            $groups = W3_Request::get_array('referrer_groups');

            $referrer_groups = array();

            foreach ($groups as $group => $group_config) {
                $group = strtolower($group);
                $group = preg_replace('~[^0-9a-z_]+~', '_', $group);
                $group = trim($group, '_');

                if ($group) {
                    $theme = (isset($group_config['theme']) ? trim($group_config['theme']) : 'default');
                    $enabled = (isset($group_config['enabled']) ? (boolean) $group_config['enabled'] : true);
                    $redirect = (isset($group_config['redirect']) ? trim($group_config['redirect']) : '');
                    $referrers = (isset($group_config['referrers']) ? explode("\r\n", trim($group_config['referrers'])) : array());

                    $referrer_groups[$group] = array(
                        'theme' => $theme,
                        'enabled' => $enabled,
                        'redirect' => $redirect,
                        'referrers' => $referrers
                    );
                }
            }

            /**
             * Allow plugins modify W3TC referrer groups
             */
            $referrer_groups = apply_filters('w3tc_referrer_groups', $referrer_groups);

            /**
             * Sanitize mobile groups
             */
            foreach ($referrer_groups as $group => $group_config) {
                $referrer_groups[$group] = array_merge(array(
                    'theme' => '',
                    'enabled' => true,
                    'redirect' => '',
                    'referrers' => array()
                ), $group_config);

                $referrer_groups[$group]['referrers'] = array_unique($referrer_groups[$group]['referrers']);
                $referrer_groups[$group]['referrers'] = array_map('strtolower', $referrer_groups[$group]['referrers']);
                sort($referrer_groups[$group]['referrers']);
            }

            $enable_referrer = false;
            foreach ($referrer_groups as $group_config) {
                if ($group_config['enabled']) {
                    $enable_referrer = true;
                    break;
                }
            }
            $config->set('referrer.enabled', $enable_referrer);

            $config->set('referrer.rgroups', $referrer_groups);
        }

        /**
         * CDN tab
         */
        if ($this->_page == 'w3tc_cdn') {
            $cdn_cnames = W3_Request::get_array('cdn_cnames');
            $cdn_domains = array();

            foreach ($cdn_cnames as $cdn_cname) {
                $cdn_cname = trim($cdn_cname);

                /**
                 * Auto expand wildcard domain to 10 subdomains
                 */
                $matches = null;

                if (preg_match('~^\*\.(.*)$~', $cdn_cname, $matches)) {
                    $cdn_domains = array();

                    for ($i = 1; $i <= 10; $i++) {
                        $cdn_domains[] = sprintf('cdn%d.%s', $i, $matches[1]);
                    }

                    break;
                }

                if ($cdn_cname) {
                    $cdn_domains[] = $cdn_cname;
                }
            }

            switch ($this->_config->get_string('cdn.engine')) {
                case 'ftp':
                    $config->set('cdn.ftp.domain', $cdn_domains);
                    break;

                case 's3':
                    $config->set('cdn.s3.cname', $cdn_domains);
                    break;

                case 'cf':
                    $config->set('cdn.cf.cname', $cdn_domains);
                    break;

                case 'cf2':
                    $config->set('cdn.cf2.cname', $cdn_domains);
                    break;

                case 'rscf':
                    $config->set('cdn.rscf.cname', $cdn_domains);
                    break;

                case 'azure':
                    $config->set('cdn.azure.cname', $cdn_domains);
                    break;
                case 'mirror':
                    $config->set('cdn.mirror.domain', $cdn_domains);
                    break;

                case 'maxcdn':
                    $config->set('cdn.maxcdn.domain', $cdn_domains);
                    break;

                case 'netdna':
                    $config->set('cdn.netdna.domain', $cdn_domains);
                    break;

                case 'cotendo':
                    $config->set('cdn.cotendo.domain', $cdn_domains);
                    break;

                case 'edgecast':
                    $config->set('cdn.edgecast.domain', $cdn_domains);
                    break;

                case 'att':
                    $config->set('cdn.att.domain', $cdn_domains);
                    break;

                case 'akamai':
                    $config->set('cdn.akamai.domain', $cdn_domains);
                    break;
            }
        }

        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/extensions.php');

        w3_extensions_admin_init();
        $all_extensions = w3_get_extensions($config);
        $old_extensions = $this->_config->get_array('extensions.settings', array());
        foreach ($all_extensions as $extension => $descriptor) {
            $extension_values = W3_Request::get_as_array('extensions.settings.');
            $extension_keys = array();
            $extension_settings = array();
            $tmp_grp = str_replace('.', '_', $extension) . '_';
            foreach($extension_values as $key => $value) {
                if(strpos($key, $tmp_grp) !== false) {
                    $extension_settings[str_replace($tmp_grp, '', $key)] = $value;
                }
            }
            if ($extension_settings) {
                $old_extension_settings = isset($old_extensions[$extension]) ? $old_extensions[$extension] : array();
                if (!isset($old_extensions[$extension]))
                    $old_extensions[$extension] = array();
                $extension_keys[$extension] = apply_filters("w3tc_save_extension_settings-{$extension}",
                                                    $extension_settings,
                                                    $old_extension_settings);
                $new_settings = array_merge($old_extensions ,$extension_keys);
                $config->set("extensions.settings", $new_settings);
                $old_extensions = $config->get_array('extensions.settings', array());
            }

        }
        //CloudFront does not support expires header. So disable it when its used
        if ($config->get_string('cdn.engine') == 'cf2') {
            $config->set('browsercache.cssjs.expires', false);
            $config->set('browsercache.html.expires', false);
            $config->set('browsercache.other.expires', false);
        }
        $config = apply_filters('w3tc_save_options', $config, $this->_config, $config_admin);
        $config = apply_filters("w3tc_save_options-{$this->_page}", $config, $this->_config, $config_admin);

        do_action('w3tc_saving_options', $config, $this->_config, $config_admin);
        do_action("w3tc_saving_options-{$this->_page}", $config, $this->_config, $config_admin);

        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        w3_config_save($this->_config, $config, $config_admin);

        switch ($this->_page) {
            case 'w3tc_cdn':
                /**
                 * Handle Set Cookie Domain
                 */
                $set_cookie_domain_old = W3_Request::get_boolean('set_cookie_domain_old');
                $set_cookie_domain_new = W3_Request::get_boolean('set_cookie_domain_new');

                if ($set_cookie_domain_old != $set_cookie_domain_new) {
                    if ($set_cookie_domain_new) {
                        if (!$this->enable_cookie_domain()) {
                            w3_admin_redirect(array_merge($params, array(
                                'w3tc_error' => 'enable_cookie_domain'
                            )));
                        }
                    } else {
                        if (!$this->disable_cookie_domain()) {
                            w3_admin_redirect(array_merge($params, array(
                                'w3tc_error' => 'disable_cookie_domain'
                            )));
                        }
                    }
                }
                break;

            case 'w3tc_general':
                break;
        }

        $notes[] = 'config_save';

        if ($redirect_permalink_error) {
            w3_admin_redirect(array(
                'w3tc_error' => $redirect_permalink_error,
                'w3tc_note' => 'config_save'
            ));
        }

        w3_admin_redirect_with_custom_messages($params, null, $notes, true);
    }

    /**
     * Enables COOKIE_DOMAIN
     *
     * @return bool
     */
    function enable_cookie_domain() {
        $config_path = w3_get_wp_config_path();
        $config_data = @file_get_contents($config_path);

        if ($config_data === false) {
            return false;
        }

        $cookie_domain = w3_get_cookie_domain();

        if ($this->is_cookie_domain_define($config_data)) {
            $new_config_data = preg_replace(W3TC_PLUGIN_TOTALCACHE_REGEXP_COOKIEDOMAIN, "define('COOKIE_DOMAIN', '" . addslashes($cookie_domain) . "')", $config_data, 1);
        } else {
            $new_config_data = preg_replace('~<\?(php)?~', "\\0\r\ndefine('COOKIE_DOMAIN', '" . addslashes($cookie_domain) . "'); // " . __('Added by W3 Total Cache', 'w3-total-cache') . "\r\n", $config_data, 1);
        }

        if ($new_config_data != $config_data) {
            if (!@file_put_contents($config_path, $new_config_data)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Disables COOKIE_DOMAIN
     *
     * @return bool
     */
    function disable_cookie_domain() {
        $config_path = w3_get_wp_config_path();
        $config_data = @file_get_contents($config_path);

        if ($config_data === false) {
            return false;
        }

        if ($this->is_cookie_domain_define($config_data)) {
            $new_config_data = preg_replace(W3TC_PLUGIN_TOTALCACHE_REGEXP_COOKIEDOMAIN, "define('COOKIE_DOMAIN', false)", $config_data, 1);

            if ($new_config_data != $config_data) {
                if (!@file_put_contents($config_path, $new_config_data)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Checks COOKIE_DOMAIN definition existence
     *
     * @param string $content
     * @return int
     */
    function is_cookie_domain_define($content) {
        return preg_match(W3TC_PLUGIN_TOTALCACHE_REGEXP_COOKIEDOMAIN, $content);
    }


    /**
     * Returns true if config section is sealed
     * @param string $section
     * @return boolean
     */
    protected function is_sealed($section) {
        if ($this->_config->is_master())
            return false;

        if (w3_is_network() && !$this->_config->is_master() && w3_force_master())
            return true;

        // browsercache settings change rules, so not available in child settings
        if ($section == 'browsercache')
            return true;

        if ($section == 'minify' && !$this->_config_master->get_boolean('minify.enabled'))
            return true;

        return $this->_config_admin->get_boolean($section . '.configuration_sealed');
    }

    /**
     * Reads config from request
     *
     * @param W3_Config|W3_ConfigAdmin $config
     */
    function read_request($config) {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $request = W3_Request::get_request();

        foreach ($request as $request_key => $request_value) {
            if  (is_array($request_value))
                array_map('stripslashes_deep', $request_value);
            else
                $request_value = stripslashes($request_value);
            if (strpos($request_key, 'memcached_servers'))
                $request_value = explode(',', $request_value);
            $config->set($request_key, $request_value);
        }
    }
}
