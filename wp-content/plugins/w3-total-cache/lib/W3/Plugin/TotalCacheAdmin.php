<?php

/**
 * W3 Total Cache Admin plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_TotalCacheAdmin
 */
class W3_Plugin_TotalCacheAdmin extends W3_Plugin {
    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_dashboard';

    /**
     * Notes
     *
     * @var array
     */
    var $_notes = array();

    /**
     * Errors
     *
     * @var array
     */
    var $_errors = array();

    /**
     * Admin configuration
     *
     * @var W3_ConfigAdmin
     */
    var $_config_admin;

    /**
     * Runs plugin
     *
     * @return void
     */
    function run() {

        $this->_config_admin = w3_instance('W3_ConfigAdmin');

        add_action('admin_init', array(
            &$this,
            'admin_init'
        ));

        add_action('admin_enqueue_scripts', array(
            $this,
            'admin_enqueue_scripts'));

        add_action('admin_head', array(
            &$this,
            'admin_head'
        ));
        
         // Trigger a config cache refresh when adding 'home'
        add_action('add_option_home', array(
             &$this,
             'refresh_config_cache',
        ));

        // Trigger a config cache refresh when updating 'home'
        add_action('update_option_home', array(
            &$this,
            'refresh_config_cache',
        ));

        if (is_network_admin()) {
            add_action('network_admin_menu', array(
                    &$this,
                    'admin_menu'
            ));
        } else {
            add_action('admin_menu', array(
                    &$this,
                    'admin_menu'
            ));
        }

        add_filter('contextual_help_list', array(
            &$this,
            'contextual_help_list'
        ));

        add_filter('plugin_action_links_' . W3TC_FILE, array(
            &$this,
            'plugin_action_links'
        ));

        add_filter('favorite_actions', array(
            &$this,
            'favorite_actions'
        ));

        add_action('in_plugin_update_message-' . W3TC_FILE, array(
            &$this,
            'in_plugin_update_message'
        ));

        if ($this->_config->get_boolean('pgcache.enabled') || $this->_config->get_boolean('minify.enabled')) {
            add_filter('pre_update_option_active_plugins', array(
                &$this,
                'pre_update_option_active_plugins'
            ));
        }
    }

    /**
     * Load action
     *
     * @return void
     */
    function load() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        $this->_page = w3tc_get_current_page();

        /**
         * Run plugin action
         */
        $action = false;

        foreach ($_REQUEST as $key => $value) {
            if (strpos($key, 'w3tc_') === 0) {
                $action = 'action_' . substr($key, 5);
                break;
            }
        }
        $flush = false;
        $cdn = false;
        $support = false;
        $action_handler = w3_instance('W3_AdminActions_ActionHandler');
        $action_handler->set_default($this);
        $action_handler->set_current_page($this->_page);
        if ($action && $action_handler->exists($action)) {
            if (!wp_verify_nonce(W3_Request::get_string('_wpnonce'), 'w3tc'))
                wp_nonce_ays('w3tc');

            try {
                $action_handler->execute($action);
            } catch (Exception $e) {
                w3_admin_redirect_with_custom_messages(array(), array($e->getMessage()));
            }

            exit();
        }
    }

    /**
     * Admin init
     *
     * @return void
     */
    function admin_init() {
        if (function_exists('ats_register_plugin')) {
            // plugin registration
            ats_register_plugin('w3-total-cache', W3TC_FILE);

            // enable tickets module
            ats_enable_tickets('w3-total-cache',
                array(
                    'custom_fields' => array(
                        __('SSH / FTP host', 'w3-total-cache'),
                        __('SSH / FTP login', 'w3-total-cache'),
                        __('SSH / FTP password', 'w3-total-cache')
                    )
                )
            );
        }

        // special handling for deactivation link, it's plugins.php file
        if (W3_Request::get_string('action') == 'w3tc_deactivate_plugin')
            $this->action_deactivate_plugin();
    }

    function admin_enqueue_scripts() {
        wp_register_style('w3tc-options', plugins_url('pub/css/options.css', W3TC_FILE), array(), W3TC_VERSION);
        wp_register_style('w3tc-lightbox', plugins_url('pub/css/lightbox.css', W3TC_FILE), array(), W3TC_VERSION);
        wp_register_style('w3tc-widget', plugins_url('pub/css/widget.css', W3TC_FILE), array(), W3TC_VERSION);

        wp_register_script('w3tc-metadata', plugins_url('pub/js/metadata.js', W3TC_FILE), array(), W3TC_VERSION);
        wp_register_script('w3tc-options', plugins_url('pub/js/options.js', W3TC_FILE), array(), W3TC_VERSION);
        wp_register_script('w3tc-lightbox', plugins_url('pub/js/lightbox.js', W3TC_FILE), array(), W3TC_VERSION);
        wp_register_script('w3tc-widget', plugins_url('pub/js/widget.js', W3TC_FILE), array(), W3TC_VERSION);
        wp_register_script('jquery-masonry', plugins_url('pub/js/jquery.masonry.min.js', W3TC_FILE), array('jquery'), W3TC_VERSION);
    }

// Define icon styles for the custom post type
    function admin_head() {
        if (isset($_GET['page']) && $_GET['page'] == 'w3tc_dashboard'): ?>
<script type="text/javascript">
    jQuery(function() {
        jQuery('#normal-sortables').masonry({
            itemSelector : '.postbox'
        });
    });
</script>
            <?php
        endif;
        ?>
    <style type="text/css" media="screen">
        #toplevel_page_w3tc_dashboard .wp-menu-image {
            background: url(<?php echo plugins_url('w3-total-cache/pub/img/w3tc-sprite.png')?>) no-repeat 0 -32px !important;
        }
        #toplevel_page_w3tc_dashboard:hover .wp-menu-image,
        #toplevel_page_w3tc_dashboard.wp-has-current-submenu .wp-menu-image {
            background-position:0 0 !important;
        }
        #icon-edit.icon32-posts-casestudy {
            background: url(<?php echo plugins_url('w3-total-cache/pub/img/w3tc-sprite.png') ?>) no-repeat;
        }
        /**
        * HiDPI Displays
        */
        @media print,
        (-o-min-device-pixel-ratio: 5/4),
        (-webkit-min-device-pixel-ratio: 1.25),
        (min-resolution: 120dpi) {
            
            #toplevel_page_w3tc_dashboard .wp-menu-image {
                background-image: url(<?php echo plugins_url('w3-total-cache/pub/img/w3tc-sprite-retina.png')?>) !important;
                background-size: 30px 64px !important;
            }
            #toplevel_page_w3tc_dashboard:hover .wp-menu-image,
            #toplevel_page_w3tc_dashboard.wp-has-current-submenu .wp-menu-image {
                background-position:0 0 !important;
            }
            #icon-edit.icon32-posts-casestudy {
                background-image: url(<?php echo plugins_url('w3-total-cache/pub/img/w3tc-sprite-retina.png') ?>) !important;
                background-size: 30px 64px !important;
            }
        }
    </style>

    <?php }

    /**
     * Admin menu
     *
     * @return void
     */
    function admin_menu() {
        if (current_user_can('manage_options')) {
            $menus = w3_instance('W3_Menus');
            $submenu_pages = $menus->generate();

            /**
             * Only admin can modify W3TC settings
             */
            foreach ($submenu_pages as $submenu_page) {
                add_action('load-' . $submenu_page, array(
                    &$this,
                    'load'
                ));

                add_action('admin_print_styles-' . $submenu_page, array(
                    &$this,
                    'admin_print_styles'
                ));

                add_action('admin_print_scripts-' . $submenu_page, array(
                    &$this,
                    'admin_print_scripts'
                ));
            }

            global $pagenow;
            if ($pagenow == 'plugins.php') {
                add_action('admin_print_scripts', array($this, 'load_plugins_page_js'));
                add_action('admin_print_styles', array($this, 'print_plugins_page_css'));
            }
            /**
             * Only admin can see W3TC notices and errors
             */
            add_action('admin_notices', array(
                &$this,
                'admin_notices'
            ));
            add_action('network_admin_notices', array(
                &$this,
                'admin_notices'
            ));
        }
    }
    
    /**
     * add_option_home and update_option_home hook
     * We trigger a config cache refresh, to make sure we always have the latest value of 'home' in 
     * the config cache.
     * 
     * @return void
     **/
    function refresh_config_cache() {
        $this->_config->refresh_cache();
    }

    /**
     * Print styles
     *
     * @return void
     */
    function admin_print_styles() {
        wp_enqueue_style('w3tc-options');
        wp_enqueue_style('w3tc-lightbox');
    }

    /**
     * Print scripts
     *
     * @return void
     */
    function admin_print_scripts() {
        wp_enqueue_script('w3tc-metadata');
        wp_enqueue_script('w3tc-options');
        wp_enqueue_script('w3tc-lightbox');

        switch ($this->_page) {
            case 'w3tc_minify':
            case 'w3tc_mobile':
            case 'w3tc_referrer':
            case 'w3tc_cdn':
                wp_enqueue_script('jquery-ui-sortable');
                break;
        }
        if($this->_page=='w3tc_cdn')
            wp_enqueue_script('jquery-ui-dialog');
        if($this->_page=='w3tc_dashboard')
            wp_enqueue_script('jquery-masonry');
    }


    function load_plugins_page_js() {
        wp_enqueue_script('w3tc-options');
    }

    function print_plugins_page_css() {
        echo "<style type=\"text/css\">.w3tc-missing-files ul {
                margin-left: 20px;
                list-style-type: disc;
              }
              #w3tc {
              padding: 0;
              }
              #w3tc span {
    font-size: 0.6em;
    font-style: normal;
    text-shadow: none;
}
ul.w3tc-incomp-plugins, ul.w3-bullet-list {
    list-style: disc outside;
    margin-left: 17px;
    margin-top: 0;
    margin-bottom: 0;
}
ul.w3tc-incomp-plugins li div{
    width: 170px;
    display: inline-block;
}
              </style>";
    }

    /**
     * Contextual help list filter
     *
     * @param string $list
     * @return string
     */
    function contextual_help_list($list) {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/other.php');
        $faq = w3_parse_faq();

        if (isset($faq['Usage'])) {
            $columns = array_chunk($faq['Usage'], ceil(count($faq['Usage']) / 3));

            ob_start();
            include W3TC_INC_OPTIONS_DIR . '/common/help.php';
            $help = ob_get_contents();
            ob_end_clean();

            $hook = get_plugin_page_hookname($this->_page, 'w3tc_dashboard');

            $list[$hook] = $help;
        }

        return $list;
    }

    /**
     * Plugin action links filter
     *
     * @param array $links
     * @return array
     */
    function plugin_action_links($links) {
        array_unshift($links, 
            '<a class="edit" href="admin.php?page=w3tc_general">Settings</a>');

        w3_require_once(W3TC_INC_DIR . '/functions/rule.php');
        if (!is_writable(WP_CONTENT_DIR) || 
            !is_writable(w3_get_browsercache_rules_cache_path())) {
            $delete_link = '<a href="' . 
                wp_nonce_url(admin_url('plugins.php?action=w3tc_deactivate_plugin'), 'w3tc') .
                '">Uninstall</a>';
            array_unshift($links, $delete_link);
        }

        return $links;
    }

    /**
     * favorite_actions filter
     *
     * @param array $actions
     * @return array
     */
    function favorite_actions($actions) {
        $actions[wp_nonce_url(admin_url('admin.php?page=w3tc_dashboard&amp;w3tc_flush_all'), 'w3tc')] = array(
            __('Empty Caches', 'w3-total-cache'),
            'manage_options'
        );

        return $actions;
    }

    /**
     * Active plugins pre update option filter
     *
     * @param string $new_value
     * @return string
     */
    function pre_update_option_active_plugins($new_value) {
        $old_value = (array) get_option('active_plugins');

        if ($new_value !== $old_value && in_array(W3TC_FILE, (array) $new_value) && in_array(W3TC_FILE, (array) $old_value)) {
                $this->_config->set('notes.plugins_updated', true);
                try {
                    $this->_config->save();
                } catch(Exception $ex) {}
        }

        return $new_value;
    }

    /**
     * Show plugin changes
     *
     * @return void
     */
    function in_plugin_update_message() {
        w3_require_once(W3TC_INC_DIR . '/functions/http.php');
        $response = w3_http_get(W3TC_README_URL);

        if (!is_wp_error($response) && $response['response']['code'] == 200) {
            w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin_ui.php');
            w3_in_plugin_update_message($response['body']);
        }
    }

    /**
     * Admin notices action
     *
     * @return void
     */
    function admin_notices() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin_ui.php');
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        $cookie_domain = w3_get_cookie_domain();

        $error_messages = array(
            'fancy_permalinks_disabled_pgcache' => sprintf(__('Fancy permalinks are disabled. Please %s it first, then re-attempt to enabling enhanced disk mode.', 'w3-total-cache'), w3_button_link('enable', 'options-permalink.php')),
            'fancy_permalinks_disabled_browsercache' => sprintf(__('Fancy permalinks are disabled. Please %s it first, then re-attempt to enabling the \'Do not process 404 errors for static objects with WordPress\'.', 'w3-total-cache'), w3_button_link('enable', 'options-permalink.php')),
            'support_request_type' => __('Please select request type.', 'w3-total-cache'),
            'support_request_url' => __('Please enter the address of the site in the site <acronym title="Uniform Resource Locator">URL</acronym> field.', 'w3-total-cache'),
            'support_request_name' => __('Please enter your name in the Name field', 'w3-total-cache'),
            'support_request_email' => __('Please enter valid email address in the E-Mail field.', 'w3-total-cache'),
            'support_request_phone' => __('Please enter your phone in the phone field.', 'w3-total-cache'),
            'support_request_subject' => __('Please enter subject in the subject field.', 'w3-total-cache'),
            'support_request_description' => __('Please describe the issue in the issue description field.', 'w3-total-cache'),
            'support_request_wp_login' => __('Please enter an administrator login. Create a temporary one just for this support case if needed.', 'w3-total-cache'),
            'support_request_wp_password' => __('Please enter WP Admin password, be sure it\'s spelled correctly.', 'w3-total-cache'),
            'support_request_ftp_host' => __('Please enter <acronym title="Secure Shell">SSH</acronym> or <acronym title="File Transfer Protocol">FTP</acronym> host for the site.', 'w3-total-cache'),
            'support_request_ftp_login' => __('Please enter <acronym title="Secure Shell">SSH</acronym> or <acronym title="File Transfer Protocol">FTP</acronym> login for the server. Create a temporary one just for this support case if needed.', 'w3-total-cache'),
            'support_request_ftp_password' => __('Please enter <acronym title="Secure Shell">SSH</acronym> or <acronym title="File Transfer Protocol">FTP</acronym> password for the <acronym title="File Transfer Protocol">FTP</acronym> account.', 'w3-total-cache'),
            'support_request' => __('Unable to send the support request.', 'w3-total-cache'),
            'config_import_no_file' => __('Please select config file.', 'w3-total-cache'),
            'config_import_upload' => __('Unable to upload config file.', 'w3-total-cache'),
            'config_import_import' => __('Configuration file could not be imported.', 'w3-total-cache'),
            'config_reset' => sprintf(__('Default settings could not be restored. Please run <strong>chmod 777 %s</strong> to make the configuration file write-able, then try again.', 'w3-total-cache'), W3TC_CONFIG_DIR),
            'cdn_purge_attachment' => __('Unable to purge attachment.', 'w3-total-cache'),
            'pgcache_purge_post' => __('Unable to purge post.', 'w3-total-cache'),
            'pgcache_purge_page' => __('Unable to purge page.', 'w3-total-cache'),
            'enable_cookie_domain' => sprintf(__('<strong>%swp-config.php</strong> could not be written, please edit config and add:<br /><strong style="color:#f00;">define(\'COOKIE_DOMAIN\', \'%s\');</strong> before <strong style="color:#f00;">require_once(ABSPATH . \'wp-settings.php\');</strong>.', 'w3-total-cache'), ABSPATH, addslashes($cookie_domain)),
            'disable_cookie_domain' => sprintf(__('<strong>%swp-config.php</strong> could not be written, please edit config and add:<br /><strong style="color:#f00;">define(\'COOKIE_DOMAIN\', false);</strong> before <strong style="color:#f00;">require_once(ABSPATH . \'wp-settings.php\');</strong>.', 'w3-total-cache'), ABSPATH),
            'pull_zone' => __('Pull Zone could not be automatically created.', 'w3-total-cache')
        );

        $note_messages = array(
            'config_save' => __('Plugin configuration successfully updated.', 'w3-total-cache'),
            'flush_all' => __('All caches successfully emptied.', 'w3-total-cache'),
            'flush_memcached' => __('Memcached cache(s) successfully emptied.', 'w3-total-cache'),
            'flush_opcode' => __('Opcode cache(s) successfully emptied.', 'w3-total-cache'),
            'flush_apc_system' => __('APC system cache successfully emptied', 'w3-total-cache'),
            'flush_file' => __('Disk cache(s) successfully emptied.', 'w3-total-cache'),
            'flush_pgcache' => __('Page cache successfully emptied.', 'w3-total-cache'),
            'flush_dbcache' => __('Database cache successfully emptied.', 'w3-total-cache'),
            'flush_objectcache' => __('Object cache successfully emptied.', 'w3-total-cache'),
            'flush_fragmentcache' => __('Fragment cache successfully emptied.', 'w3-total-cache'),
            'flush_minify' => __('Minify cache successfully emptied.', 'w3-total-cache'),
            'flush_browser_cache' => __('Media Query string has been successfully updated.', 'w3-total-cache'),
            'flush_varnish' => __('Varnish servers successfully purged.', 'w3-total-cache'),
            'flush_cdn' => __('CDN was successfully purged.', 'w3-total-cache'),
            'support_request' => __('The support request has been successfully sent.', 'w3-total-cache'),
            'config_import' => __('Settings successfully imported.', 'w3-total-cache'),
            'config_reset' => __('Settings successfully restored.', 'w3-total-cache'),
            'preview_enable' => __('Preview mode was successfully enabled', 'w3-total-cache'),
            'preview_disable' => __('Preview mode was successfully disabled', 'w3-total-cache'),
            'preview_deploy' => __('Preview settings successfully deployed. Preview mode remains enabled until it\'s disabled. Continue testing new settings or disable preview mode if done.', 'w3-total-cache'),
            'cdn_purge_attachment' => __('Attachment successfully purged.', 'w3-total-cache'),
            'pgcache_purge_post' => __('Post successfully purged.', 'w3-total-cache'),
            'pgcache_purge_page' => __('Page successfully purged.', 'w3-total-cache'),
            'new_relic_save' => __('New relic settings have been updated.', 'w3-total-cache'),
            'add_in_removed' => __('The add-in has been removed.', 'w3-total-cache'),
            'sns_subscribed' => __('Site has been subscribed.', 'w3-total-cache'),
            'enabled_edge' => __('Edge mode has been enabled.', 'w3-total-cache'),
            'disabled_edge' => __('Edge mode has been disabled.', 'w3-total-cache'),
            'pull_zone' => __('Pull Zone was automatically created.', 'w3-total-cache'),
            'extension_activated' => __('Extension has been successfully activated.', 'w3-total-cache')
        );

        $errors = array();
        $notes = array();
        $environment_error_present = false;

        // print errors happened during last request execution,
        // when we decided to redirect with error message instead of
        // printing it directly (to avoid reexecution on refresh)
        $message_id = W3_Request::get_string('w3tc_message');
        if ($message_id) {
            $v = get_transient('w3tc_message.' . $message_id);
            set_transient('w3tc_message.' . $message_id, null);

            if (isset($v['errors']) && is_array($v['errors'])) {
                foreach ($v['errors'] as $error) {
                    if (isset($error_messages[$error]))
                        $errors[] = $error_messages[$error];
                    else
                        $errors[] = $error;
                }
            }
            if (isset($v['notes']) && is_array($v['notes'])) {
                foreach ($v['notes'] as $note) {
                    if (isset($note_messages[$note]))
                        $notes[] = $note_messages[$note];
                    else
                        $notes[] = $note;
                }
            }
        }

        /*
         * Filesystem environment fix, if needed
         */
        try {
            global $pagenow;
            if ($pagenow == 'plugins.php' || 
                    substr(W3_Request::get_string('page'), 0, 5) == 'w3tc_') {
                $environment = w3_instance('W3_AdminEnvironment');
                $environment->fix_in_wpadmin($this->_config);

                if (isset($_REQUEST['upgrade'])) 
                    $notes[] = __('Required files and directories have been automatically created', 'w3-total-cache');
            }
        } catch (SelfTestExceptions $exs) {
            $r = w3_parse_selftest_exceptions($exs);

            foreach ($r['before_errors'] as $e)
                $errors[] = $e;

            if (strlen($r['required_changes']) > 0) {
                $changes_style = 'border: 1px solid black; ' .
                    'background: white; ' .
                    'margin: 10px 30px 10px 30px; ' . 
                    'padding: 10px; display: none';
                $ftp_style = 'border: 1px solid black; background: white; ' .
                    'margin: 10px 30px 10px 30px; ' . 
                    'padding: 10px; display: none';
                $ftp_form = str_replace('class="wrap"', '', 
                    $exs->credentials_form());
                $ftp_form = str_replace('<form ', '<form name="w3tc_ftp_form" ', 
                    $ftp_form);
                $ftp_form = str_replace('<fieldset>', '', $ftp_form);
                $ftp_form = str_replace('</fieldset>', '', $ftp_form);
                $ftp_form = str_replace('id="upgrade" class="button"', 
                    'id="upgrade" class="button w3tc-button-save"', $ftp_form);

                $error = '<strong>W3 Total Cache Error:</strong> ' .
                    'Files and directories could not be automatically ' .
                    'created to complete the installation. ' .
                    '<table>' .
                    '<tr>' .
                    '<td>Please execute commands manually</td>' .
                    '<td>' . 
                    w3_button('View required changes', '',
                        'w3tc-show-required-changes') . 
                    '</td>' .
                    '</tr>' .
                    '<tr>' .
                    '<td>or use FTP form to allow ' . 
                    '<strong>W3 Total Cache</strong> make it automatically.' . 
                    '</td>' .
                    '<td>' . 
                    w3_button('Update via FTP', '', 'w3tc-show-ftp-form') .
                    '</td>' .
                    '</tr></table>'.

                    '<div class="w3tc-required-changes" style="' . 
                    $changes_style . '">' . $r['required_changes'] . '</div>' .
                    '<div class="w3tc-ftp-form" style="' . $ftp_style . '">' . 
                    $ftp_form . '</div>';

                $environment_error_present = true;
                $errors[] = $error;
            }

            foreach ($r['later_errors'] as $e)
                $errors[] = $e;
        }

        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $note = W3_Request::get_string('w3tc_note');

        if (isset($note_messages[$note])) {
            $notes[] = $note_messages[$note];
        }

        /**
         * CDN notifications
         */
        if ($this->_config->get_boolean('cdn.enabled')) {
            /**
             * @var $ui_cdn_notes W3_UI_CdnNotes
             */
            $cdn_notes = w3_instance('W3_UI_CdnNotes');
            $this->_notes = array_merge($this->_notes, $cdn_notes->notifications($this->_config, $this->_config_admin));
            //$this->_errors = array_merge($this->_errors, $cdn_notes->errors());
        }

        /**
         * Show notification after plugin activate/deactivate
         */
        if ($this->_config->get_boolean('notes.plugins_updated')) {
            $texts = array();

            if ($this->_config->get_boolean('pgcache.enabled')) {
                $texts[] = w3_button_link(__('empty the page cache', 'w3-total-cache'), wp_nonce_url(sprintf('admin.php?page=%s&w3tc_flush_pgcache', $this->_page), 'w3tc'));
            }

            if ($this->_config->get_boolean('minify.enabled')) {
                $texts[] = sprintf(__('check the %s to maintain the desired user experience', 'w3-total-cache'), w3_button_hide_note(__('minify settings', 'w3-total-cache'), 'plugins_updated', 'admin.php?page=w3tc_minify'));
            }

            if (count($texts)) {
                $notes[] = sprintf(__('One or more plugins have been activated or deactivated, please %s. %s', 'w3-total-cache'), implode(__(' and ', 'w3-total-cache'), $texts), w3_button_hide_note(__('Hide this message', 'w3-total-cache'), 'plugins_updated'));
            }
        }

        /**
         * Show notification when page cache needs to be emptied
         */
        if ($this->_config->get_boolean('pgcache.enabled') && $this->_config->get('notes.need_empty_pgcache') && !$this->_config->is_preview()) {
            $notes[] = sprintf('The setting change(s) made either invalidate the cached data or modify the behavior of the site. %s now to provide a consistent user experience.', w3_button_link('Empty the page cache', wp_nonce_url(sprintf('admin.php?page=%s&w3tc_flush_pgcache', $this->_page), 'w3tc')));
        }

        /**
         * Show notification when object cache needs to be emptied
         */
        if ($this->_config->get_boolean('objectcache.enabled') && $this->_config->get('notes.need_empty_objectcache') && !$this->_config->is_preview()) {
            $notes[] = sprintf(__('The setting change(s) made either invalidate the cached data or modify the behavior of the site. %s now to provide a consistent user experience.', 'w3-total-cache'), w3_button_link(__('Empty the object cache', 'w3-total-cache'), wp_nonce_url(sprintf('admin.php?page=%s&w3tc_flush_objectcache', $this->_page), 'w3tc')));
        }

        /**
         * Minify notifications
         */
        if ($this->_config->get_boolean('minify.enabled')) {
            /**
             * Minify error occured
             */
            if ($this->_config_admin->get_boolean('notes.minify_error')) {
                $errors[] = sprintf(__('Recently an error occurred while creating the CSS / JS minify cache: %s. %s', 'w3-total-cache'), $this->_config_admin->get_string('minify.error.last'), w3_button_hide_note(__('Hide this message', 'w3-total-cache'), 'minify_error', '', true));
            }

            /**
             * Show notification when minify needs to be emptied
             */
            if ($this->_config->get_boolean('notes.need_empty_minify') && !$this->_config->is_preview()) {
                $notes[] = sprintf(__('The setting change(s) made either invalidate the cached data or modify the behavior of the site. %s now to provide a consistent user experience.', 'w3-total-cache'), w3_button_link(__('Empty the minify cache', 'w3-total-cache'), wp_nonce_url(sprintf('admin.php?page=%s&w3tc_flush_minify', $this->_page), 'w3tc')));
            }
        }

        if ($this->_config->get_boolean('newrelic.enabled') && $this->_config_admin->get_boolean('notes.new_relic_page_load_notification')) {
            /**
             * @var W3_UI_NewRelicNotes $newrelic_notes
             */
            $newrelic_notes = w3_instance('W3_UI_NewRelicNotes');
            $this->_notes = array_merge($this->_notes, $newrelic_notes->notifications($this->_config));
        }

        /**
         * Show notification if user can remove old w3tc folders
         */
        if ($this->_config_admin->get_boolean('notes.remove_w3tc')) {
            w3_require_once(W3TC_INC_DIR . '/functions/update.php');
            $folders = w3_find_old_folders();
            $folders = array_map('basename', $folders);
            $notes[] = sprintf(__('The directory w3tc can be deleted. %s: %s. However, <em>do not remove the w3tc-config directory</em>. %s', 'w3-total-cache')
                                , WP_CONTENT_DIR, implode(', ',$folders)
                                , w3_button_hide_note('Hide this message', 'remove_w3tc', '', true));
        }

        // print errors which happened during current request execution
        foreach ($this->_errors as $error)
            $errors[] = $error;

        // print notes which happened during current request execution
        foreach ($this->_notes as $note)
            $notes[] = $note;

        $errors = apply_filters('w3tc_errors', $errors);
        $notes = apply_filters('w3tc_notes', $notes);

        /**
         * Show messages
         */
        foreach ($notes as $note) {
            echo sprintf('<div class="updated fade"><p>%s</p></div>', $note);
        }

        foreach ($errors as $error) {
            echo sprintf('<div class="error"><p>%s</p></div>', $error);
        }
    }

    /**
     * Deactivates plugin
     **/
    function action_deactivate_plugin() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/activation.php');
        array_merge($this->_errors, w3_deactivate_plugin());
    }

    /**
     * Flush all cache
     *
     * @param bool $flush_cf
     * @return void
     */
    function flush_all($flush_cf = true) {
        _doing_it_wrong('flush_all', 'This function is deprecated. Use w3tc_flush_all() instead.', '0.9.3');
        w3tc_flush_all();
    }

    /**
     * Flush page cache
     *
     * @return void
     */
    function flush_pgcache() {
        _doing_it_wrong('flush_pgcache', 'This function is deprecated. Use w3tc_flush_all() instead.', '0.9.3');
        w3tc_pgcache_flush();
    }

    /**
     * Flush database cache
     *
     * @return void
     */
    function flush_dbcache() {
        _doing_it_wrong('flush_dbcache', 'This function is deprecated. Use w3tc_dbcache_flush() instead.', '0.9.3');
        w3tc_dbcache_flush();
    }

    /**
     * Flush object cache
     *
     * @return void
     */
    function flush_objectcache() {
        _doing_it_wrong('flush_objectcache', 'This function is deprecated. Use w3tc_objectcache_flush() instead.', '0.9.3');
        w3tc_objectcache_flush();
    }

    /**
     * Flush fragment cache
     */
    function flush_fragmentcache() {
        _doing_it_wrong('flush_fragmentcache', 'This function is deprecated. Use w3tc_fragmentcache_flush() instead.', '0.9.3');
        w3tc_fragmentcache_flush();
    }

    /**
     * Flush minify cache
     *
     * @return void
     */
    function flush_minify() {
        _doing_it_wrong('flush_minify', 'This function is deprecated. Use w3tc_minify_flush() instead.', '0.9.3');
        w3tc_minify_flush();
    }

    /**
     * Flush browsers cache
     */
    function flush_browser_cache() {
        _doing_it_wrong('flush_browser_cache', 'This function is deprecated. Use w3tc_browsercache_flush() instead.', '0.9.3');
        w3tc_browsercache_flush();

    }

    /**
     * Flush varnish cache
     */
    function flush_varnish() {
        _doing_it_wrong('flush_varnish', 'This function is deprecated. Use w3tc_varnish_flush() instead.', '0.9.3');
        w3tc_varnish_flush();
    }

    /**
     * Flush CDN mirror
     */
    function flush_cdn() {
        _doing_it_wrong('flush_cdn', 'This function is deprecated. Use w3tc_cdncache_purge() instead.', '0.9.3');
        w3tc_cdncache_purge();
    }
}
