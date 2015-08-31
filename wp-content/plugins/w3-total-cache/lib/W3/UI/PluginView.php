<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cyonite
 * Date: 5/17/13
 * Time: 2:45 PM
 * To change this template use File | Settings | File Templates.
 */

w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin_ui.php');
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/http.php');
abstract class W3_UI_PluginView {
    /**
     * Config
     *
     * @var W3_Config
     */
    var $_config = null;

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
     * Rule-related errors about modifications in .htaccess
     *
     * @var array
     */
    var $_rule_errors = array();

    /**
     * Rule-related errors about modification of root config file
     * @var array
     */
    var $_rule_errors_root = array();

    /**
     * Link for hiding of root rules notification
     *
     * @var string
     */
    var $_rule_errors_root_hide = '';

    /**
     * If missing folder error
     * @var string
     */
    var $_use_ftp_form = false;

    /**
     * Link for auto-installing of rules
     *
     * @var string
     */
    var $_rule_errors_autoinstall = '';

    /**
     * Link for hiding of rules notification
     *
     * @var string
     */
    var $_rule_errors_hide = '';

    /**
     * Used in PHPMailer init function
     *
     * @var string
     */
    var $_phpmailer_sender = '';

    /**
     * Admin configuration
     *
     * @var W3_ConfigAdmin
     */
    var $_config_admin;

    /**
     * Master configuration
     *
     * @var W3_Config
     */
    var $_config_master;

    /**
     * @var string WordPress FTP form
     */
    var $_ftp_form;

    var $_disable_cache_write_notification = false;
    var $_disable_add_in_files_notification = false;
    var $_disable_minify_error_notification = false;
    var $_disable_file_operation_notification = false;

    var $_page;

    function __construct() {
        $this->_config = w3_instance('W3_Config');
        $this->_config_master = new W3_Config(true);
        $this->_config_admin = w3_instance('W3_ConfigAdmin');
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        $this->_page = w3tc_get_current_page();
    }

    function options() {
        $remove_results = array();
        $w3tc_error = array();
        w3_require_once(W3TC_INC_DIR . '/functions/activation.php');

        $preview = $this->_config->is_preview();
        if (w3_is_network() && !$this->is_master()) {
            $this->_config_master = new W3_Config(true);
        }
        else
            $this->_config_master = $this->_config;

        /**
         * Check for page cache availability
         */
        $wp_config_edit = false;

        /**
         * Check memcached
         */
        $memcaches_errors = array();

        if ($this->_config->get_boolean('pgcache.enabled') && $this->_config->get_string('pgcache.engine') == 'memcached') {
            $pgcache_memcached_servers = $this->_config->get_array('pgcache.memcached.servers');

            if (!$this->is_memcache_available($pgcache_memcached_servers)) {
                $memcaches_errors[] = sprintf(__('Page Cache: %s.', 'w3-total-cache'), implode(', ', $pgcache_memcached_servers));
            }
        }

        if ($this->_config->get_boolean('minify.enabled') && $this->_config->get_string('minify.engine') == 'memcached') {
            $minify_memcached_servers = $this->_config->get_array('minify.memcached.servers');

            if (!$this->is_memcache_available($minify_memcached_servers)) {
                $memcaches_errors[] = sprintf(__('Minify: %s.', 'w3-total-cache'), implode(', ', $minify_memcached_servers));
            }
        }

        if ($this->_config->get_boolean('dbcache.enabled') && $this->_config->get_string('dbcache.engine') == 'memcached') {
            $dbcache_memcached_servers = $this->_config->get_array('dbcache.memcached.servers');

            if (!$this->is_memcache_available($dbcache_memcached_servers)) {
                $memcaches_errors[] = sprintf(__('Database Cache: %s.', 'w3-total-cache'), implode(', ', $dbcache_memcached_servers));
            }
        }

        if ($this->_config->get_boolean('objectcache.enabled') && $this->_config->get_string('objectcache.engine') == 'memcached') {
            $objectcache_memcached_servers = $this->_config->get_array('objectcache.memcached.servers');

            if (!$this->is_memcache_available($objectcache_memcached_servers)) {
                $memcaches_errors[] = sprintf(__('Object Cache: %s.', 'w3-total-cache'), implode(', ', $objectcache_memcached_servers));
            }
        }

        if (count($memcaches_errors)) {
            $memcache_error = __('The following memcached servers are not responding or not running:</p><ul>', 'w3-total-cache');

            foreach ($memcaches_errors as $memcaches_error) {
                $memcache_error .= '<li>' . $memcaches_error . '</li>';
            }

            $memcache_error .= __('</ul><p>This message will automatically disappear once the issue is resolved.', 'w3-total-cache');

            $this->_errors[] = $memcache_error;
        }

        /**
         * Check CURL extension
         */
        if ($this->_config->get_boolean('notes.no_curl') && $this->_config->get_boolean('cdn.enabled') && !function_exists('curl_init')) {
            $this->_notes[] = sprintf(__('The <strong>CURL PHP</strong> extension is not available. Please install it to enable S3 or CloudFront functionality. %s', 'w3-total-cache'), w3_button_hide_note('Hide this message', 'no_curl'));
        }

        /**
         * Check Zlib extension
         */
        if ($this->_config->get_boolean('notes.no_zlib') && !function_exists('gzencode')) {
            $this->_notes[] = sprintf(__('Unfortunately the PHP installation is incomplete, the <strong>zlib module is missing</strong>. This is a core PHP module. Notify the server administrator. %s', 'w3-total-cache'), w3_button_hide_note(__('Hide this message', 'w3-total-cache'), 'no_zlib'));
        }

        /**
         * Check if Zlib output compression is enabled
         */
        if ($this->_config->get_boolean('notes.zlib_output_compression') && w3_zlib_output_compression()) {
            $this->_notes[] = sprintf(__('Either the PHP configuration, web server configuration or a script in the WordPress installation has <strong>zlib.output_compression</strong> enabled.<br />Please locate and disable this setting to ensure proper HTTP compression behavior. %s', 'w3-total-cache'), w3_button_hide_note(__('Hide this message', 'w3-total-cache'), 'zlib_output_compression'));
        }

        /**
         * Check wp-content permissions
         */
        if (!W3TC_WIN && $this->_config->get_boolean('notes.wp_content_perms')) {
            w3_require_once( W3TC_INC_DIR . '/functions/file.php');
            $wp_content_mode = w3_get_file_permissions(WP_CONTENT_DIR);

            if ($wp_content_mode > 0755) {
                $this->_notes[] = sprintf(__('<strong>%s</strong> is write-able. When finished installing the plugin,
                                        change the permissions back to the default: <strong>chmod 755 %s</strong>.
                                        Permissions are currently %s. %s', 'w3-total-cache')
                    , WP_CONTENT_DIR
                    , WP_CONTENT_DIR
                    , base_convert(w3_get_file_permissions(WP_CONTENT_DIR), 10, 8)
                    , w3_button_hide_note(__('Hide this message', 'w3-total-cache'), 'wp_content_perms'));
            }
        }

        /**
         * Check wp-content permissions
         */
        if (!W3TC_WIN && $this->_config->get_boolean('notes.wp_content_changed_perms')) {
            $perm = get_transient('w3tc_prev_permission');
            $current_perm = w3_get_file_permissions(WP_CONTENT_DIR);
            if ($perm && $perm != base_convert($current_perm, 10, 8) && ($current_perm > 0755 || $perm < base_convert($current_perm, 10, 8))) {
                $this->_notes[] = sprintf(__('<strong>%s</strong> permissions were changed during the setup process.
                                        Permissions are currently %s.<br />To restore permissions run
                                        <strong>chmod %s %s</strong>. %s', 'w3-total-cache')
                    , WP_CONTENT_DIR
                    , base_convert($current_perm, 10, 8)
                    , $perm
                    , WP_CONTENT_DIR
                    , w3_button_hide_note(__('Hide this message', 'w3-total-cache'), 'wp_content_changed_perms'));
            }
        }

        /**
         * Check permalinks
         */
        if ($this->_config->get_boolean('notes.no_permalink_rules') && (($this->_config->get_boolean('pgcache.enabled') && $this->_config->get_string('pgcache.engine') == 'file_generic') || ($this->_config->get_boolean('browsercache.enabled') && $this->_config->get_boolean('browsercache.no404wp'))) && !w3_is_permalink_rules()) {
            $this->_errors[] = sprintf(__('The required directives for fancy permalinks could not be detected, please confirm they are available: <a href="http://codex.wordpress.org/Using_Permalinks#Creating_and_editing_.28.htaccess.29">Creating and editing</a> %s', 'w3-total-cache'), w3_button_hide_note(__('Hide this message', 'w3-total-cache'), 'no_permalink_rules'));
        }

        /**
         * CDN
         */

        if ($this->_config->get_boolean('cdn.enabled')) {
            /**
             * Check upload settings
             */
            $upload_info = w3_upload_info();

            if (!$upload_info) {
                $upload_path = get_option('upload_path');
                $upload_path = trim($upload_path);

                if (empty($upload_path)) {
                    $upload_path = WP_CONTENT_DIR . '/uploads';

                    $this->_errors[] = sprintf(__('The uploads directory is not available. Default WordPress directories will be created: <strong>%s</strong>.', 'w3-total-cache'), $upload_path);
                }

                if (!w3_is_multisite()) {
                    $this->_errors[] = sprintf(__('The uploads path found in the database (%s) is inconsistent with the actual path. Please manually adjust the upload path either in miscellaneous settings or if not using a custom path %s automatically to resolve the issue.', 'w3-total-cache'), $upload_path, w3_button_link(__('update the path', 'w3-total-cache'), wp_nonce_url(sprintf('admin.php?page=%s&w3tc_config_update_upload_path', $this->_page), 'w3tc')));
                }
            }

            /**
             * Check CDN settings
             */
            $cdn_engine = $this->_config->get_string('cdn.engine');
            $error = '';
            switch (true) {
                case ($cdn_engine == 'ftp' && !count($this->_config->get_array('cdn.ftp.domain'))):
                    $this->_errors[] = __('A configuration issue prevents <acronym title="Content Delivery Network">CDN</acronym> from working:
                                            The <strong>"Replace default hostname with"</strong>
                                            field cannot be empty. Enter <acronym
                                            title="Content Delivery Network">CDN</acronym>
                                            provider hostname <a href="?page=w3tc_cdn#configuration">here</a>.
                                            <em>(This is the hostname used in order to view objects
                                            in a browser.)</em>', 'w3-total-cache');
                    break;

                case ($cdn_engine == 's3' && ($this->_config->get_string('cdn.s3.key') == '' || $this->_config->get_string('cdn.s3.secret') == '' || $this->_config->get_string('cdn.s3.bucket') == '')):
                    $error = __('The <strong>"Access key", "Secret key" and "Bucket"</strong> fields cannot be empty.', 'w3-total-cache');
                    break;

                case ($cdn_engine == 'cf' && ($this->_config->get_string('cdn.cf.key') == '' || $this->_config->get_string('cdn.cf.secret') == '' || $this->_config->get_string('cdn.cf.bucket') == '' || ($this->_config->get_string('cdn.cf.id') == '' && !count($this->_config->get_array('cdn.cf.cname'))))):
                    $error = __('The <strong>"Access key", "Secret key", "Bucket" and "Replace default hostname with"</strong> fields cannot be empty.', 'w3-total-cache');
                    break;

                case ($cdn_engine == 'cf2' && ($this->_config->get_string('cdn.cf2.key') == '' || $this->_config->get_string('cdn.cf2.secret') == '' || ($this->_config->get_string('cdn.cf2.id') == '' && !count($this->_config->get_array('cdn.cf2.cname'))))):
                    $error = __('The <strong>"Access key", "Secret key" and "Replace default hostname with"</strong> fields cannot be empty.', 'w3-total-cache');
                    break;

                case ($cdn_engine == 'rscf' && ($this->_config->get_string('cdn.rscf.user') == '' || $this->_config->get_string('cdn.rscf.key') == '' || $this->_config->get_string('cdn.rscf.container') == '' || !count($this->_config->get_array('cdn.rscf.cname')))):
                    $error = __('The <strong>"Username", "API key", "Container" and "Replace default hostname with"</strong> fields cannot be empty.', 'w3-total-cache');
                    break;

                case ($cdn_engine == 'azure' && ($this->_config->get_string('cdn.azure.user') == '' || $this->_config->get_string('cdn.azure.key') == '' || $this->_config->get_string('cdn.azure.container') == '')):
                    $error = __('The <strong>"Account name", "Account key" and "Container"</strong> fields cannot be empty.', 'w3-total-cache');
                    break;

                case ($cdn_engine == 'mirror' && !count($this->_config->get_array('cdn.mirror.domain'))):
                    $error = __('The <strong>"Replace default hostname with"</strong> field cannot be empty.', 'w3-total-cache');
                    break;

                case ($cdn_engine == 'netdna'):
                    $fields = array();
                    if ($this->_config->get_string('cdn.netdna.authorization_key') == '')
                        $fields[] = '"' . __('Authorization key', 'w3-total-cache') . '"';

                    if (!count($this->_config->get_array('cdn.netdna.domain')))
                        $fields[] = '"' . __('Replace default hostname with', 'w3-total-cache') . '"';

                    if ($fields) {
                        $error = sprintf(__('The <strong>%s</strong> field(s) cannot be empty.', 'w3-total-cache'),
                            implode(__(' and ', 'w3-total-cache'), $fields));
                    }

                    if ($this->_config->get_string('cdn.netdna.authorization_key') != '' &&
                        sizeof(explode('+', $this->_config->get_string('cdn.netdna.authorization_key'))) != 3)
                        $error .= __('The <strong>"Authorization key"</strong> is not correct.', 'w3-total-cache');
                    elseif ($this->_config->get_integer('cdn.netdna.zone_id', 0) <= 0)
                        $error .= __('You need to select / create a pull zone.', 'w3-total-cache');
                    break;

                case ($cdn_engine == 'maxcdn'):
                    $fields = array();
                    if ($this->_config->get_string('cdn.maxcdn.authorization_key') == '')
                        $fields[] = '"' . __('Authorization key', 'w3-total-cache') . '"';

                    if (!count($this->_config->get_array('cdn.maxcdn.domain')))
                        $fields[] = '"' . __('Replace default hostname with', 'w3-total-cache') . '"';

                    if ($fields) {
                        $error = sprintf(__('The <strong>%s</strong> field(s) cannot be empty.', 'w3-total-cache'),
                            implode(__(' and ', 'w3-total-cache'), $fields));
                    }

                    if ($this->_config->get_string('cdn.maxcdn.authorization_key') != '' &&
                        sizeof(explode('+', $this->_config->get_string('cdn.maxcdn.authorization_key'))) != 3)
                        $error .= __('The <strong>"Authorization key"</strong> is not correct.', 'w3-total-cache');
                    elseif ($this->_config->get_integer('cdn.maxcdn.zone_id', 0) <= 0)
                        $error .= __('You need to select / create a pull zone.', 'w3-total-cache');

                    break;

                case ($cdn_engine == 'cotendo' && !count($this->_config->get_array('cdn.cotendo.domain'))):
                    $error = __('The <strong>"Replace default hostname with"</strong> field cannot be empty.', 'w3-total-cache');
                    break;

                case ($cdn_engine == 'edgecast' && !count($this->_config->get_array('cdn.edgecast.domain'))):
                    $error = __('The <strong>"Replace default hostname with"</strong> field cannot be empty.', 'w3-total-cache');
                    break;

                case ($cdn_engine == 'att' && !count($this->_config->get_array('cdn.att.domain'))):
                    $error = __('The <strong>"Replace default hostname with"</strong> field cannot be empty.', 'w3-total-cache');
                    break;

                case ($cdn_engine == 'akamai' && !count($this->_config->get_array('cdn.akamai.domain'))):
                    $error = 'The <strong>"Replace default hostname with"</strong> field cannot be empty.';
                    break;
            }

            if ($error) {
                $this->_errors[] = __('A configuration issue prevents <acronym title="Content Delivery Network">CDN</acronym> from working: ', 'w3-total-cache') . $error . __(' <a href="?page=w3tc_cdn#configuration">Specify it here</a>.', 'w3-total-cache');
            }
        }


        /**
         * Preview mode
         */
        if ($this->_config->is_preview()) {
            $this->_notes[] = sprintf(__('Preview mode is active: Changed settings will not take effect until preview mode is %s or %s.', 'w3-total-cache'), w3_button_link(__('deploy', 'w3-total-cache'), wp_nonce_url(sprintf('admin.php?page=%s&w3tc_config_preview_deploy', $this->_page), 'w3tc')), w3_button_link(__('disable', 'w3-total-cache'), wp_nonce_url(sprintf('admin.php?page=%s&w3tc_config_preview_disable', $this->_page), 'w3tc'))) .
                                    '<br /><span class="description">'. sprintf(__('To preview any changed settings (without deploying): %s', 'w3-total-cache'), w3tc_get_preview_link()). '</span>';
        }

        /**
         *
         */

        if ($this->_config->get_boolean('notes.root_rules') && count($this->_rule_errors_root) > 0) {
            $this->_rule_errors_root_hide = w3_button_hide_note(__('Hide this message', 'w3-total-cache'), 'root_rules');
        } else {
            $this->_rule_errors_root = array();
        }

        $this->_disable_file_operation_notification = $this->_disable_add_in_files_notification || $this->_disable_cache_write_notification;

        if (!$this->_disable_file_operation_notification && isset($file_operation_exception) && $file_operation_exception) {
            $tech_message = '<ul>';
            $core_rules_perms = '';
            if (w3_get_file_permissions(w3_get_wp_config_path()) != 0644)
                $core_config_perms = sprintf(__('File permissions are <strong>%s</strong>, however they should be
					<strong>644</strong>.', 'w3-total-cache')
                    , base_convert(w3_get_file_permissions(w3_get_wp_config_path()), 10, 8)
                );
            else
                $core_config_perms = sprintf(__('File permissions are <strong>%s</strong>', 'w3-total-cache'), base_convert(w3_get_file_permissions(w3_get_wp_config_path()), 10, 8));

            if (w3_get_file_permissions(w3_get_pgcache_rules_core_path()) != 0644)
                $core_rules_perms = sprintf(__('File permissions are <strong>%s</strong>, however they should be
											<strong>644</strong>.', 'w3-total-cache')
                    , base_convert(w3_get_file_permissions(w3_get_pgcache_rules_core_path()), 10, 8)
                );
            else
                $core_rules_perms = sprintf(__('File permissions are <strong>%s</strong>', 'w3-total-cache'), base_convert(w3_get_file_permissions(w3_get_pgcache_rules_core_path()), 10, 8));

            $wp_content_perms = '';
            if (w3_get_file_permissions(WP_CONTENT_DIR) != 0755)
                $wp_content_perms = sprintf(__('Directory permissions are <strong>%s</strong>, however they should be
											<strong>755</strong>.', 'w3-total-cache')
                    , base_convert(w3_get_file_permissions(WP_CONTENT_DIR), 10, 8)
                );
            $tech_message .= '<li>' . sprintf(__('File: <strong>%s</strong> %s File owner: %s', 'w3-total-cache')
                    ,w3_get_wp_config_path()
                    ,$core_config_perms
                    , w3_get_file_owner(w3_get_wp_config_path())) .
                '</li>' ;

            $tech_message .= '<li>' . sprintf(__('File: <strong>%s</strong> %s File owner: %s', 'w3-total-cache')
                    ,w3_get_pgcache_rules_core_path()
                    ,$core_rules_perms
                    , w3_get_file_owner(w3_get_pgcache_rules_core_path())) .
                '</li>' ;

            $tech_message .= '<li>' . sprintf(__('Directory: <strong>%s</strong> %s File owner: %s', 'w3-total-cache')
                    , WP_CONTENT_DIR
                    , $wp_content_perms
                    , w3_get_file_owner(WP_CONTENT_DIR)) .
                '</li>' ;

            $tech_message .= '<li>' . sprintf(__('Owner of current file: %s', 'w3-total-cache'), w3_get_file_owner()) .
                '</li>' ;
            if (!(w3_get_file_owner() == w3_get_file_owner(w3_get_pgcache_rules_core_path()) &&
                w3_get_file_owner() == w3_get_file_owner(WP_CONTENT_DIR)))
                $tech_message .= __('<li>The files and directories have different ownership, they should have the same ownership.
								 </li>', 'w3-total-cache');
            $tech_message .= '</ul>';
            $tech_message = '<div class="w3tc-technical-info" style="display:none">' . $tech_message . '</div>';
            $w3tc_error[] = sprintf(__('<strong>W3 Total Cache Error:</strong> The plugin tried to edit, %s, but failed.
								Files and directories cannot be modified. Please review your
								<a target="_blank" href="http://codex.wordpress.org/Changing_File_Permissions">
								file permissions</a>. A common cause is %s and %s having different ownership or permissions.
								%s %s', 'w3-total-cache')
                , $wp_config_edit ? w3_get_wp_config_path() :  w3_get_pgcache_rules_core_path()
                , $wp_config_edit ? basename(w3_get_wp_config_path()) :  basename(w3_get_pgcache_rules_core_path())
                , WP_CONTENT_DIR
                , w3_button(__('View technical information', 'w3-total-cache'), '', 'w3tc-show-technical-info')
                ,$tech_message);
        }

        /**
         * Remove functions results
         */
        if ($remove_results) {
            foreach ($remove_results as $result) {
                $this->_errors = array_merge($this->_errors, $result['errors']);
                if (!isset($this->_ftp_form) && isset($result['ftp_form'])) {
                    $extra_ftp_message = __('Please enter FTP details <a href="#ftp_upload_form">below</a> to remove the disabled modules. ', 'w3-total-cache');
                    $this->_ftp_form = $result['ftp_form'];
                    $this->_use_ftp_form = true;
                }
            }
            if (isset($extra_ftp_message))
                $this->_errors[] = $extra_ftp_message;
        }

        foreach ($w3tc_error as $error)
            array_unshift($this->_errors, $error);

        if (isset($this->_ftp_form))
            $this->_use_ftp_form = true;
        $this->view();
    }

    /**
     * Returns postbox header
     *
     * @param string $title
     * @param string $class
     * @param string $id
     * @return string
     */
    function postbox_header($title, $class = '', $id = '') {
        if ( !empty( $id ) ) {
            $id = ' id="' . esc_attr( $id ) . '"';
        }
        return '<div' . $id . ' class="postbox ' . $class . '"><div class="handlediv" title="' . __('Click to toggle', 'w3-total-cache') . '"><br /></div><h3 class="hndle"><span>' . $title . '</span></h3><div class="inside">';
    }

    /**
     * Returns postbox footer
     *
     * @return string
     */
    function postbox_footer() {
        return '</div></div>';
    }


    /**
     * Returns nonce field HTML
     *
     * @param string $action
     * @param string $name
     * @param bool $referer
     * @internal param bool $echo
     * @return string
     */
    function nonce_field($action = -1, $name = '_wpnonce', $referer = true) {
        $name = esc_attr($name);
        $return = '<input type="hidden" name="' . $name . '" value="' . wp_create_nonce($action) . '" />';

        if ($referer) {
            $return .= wp_referer_field(false);
        }

        return $return;
    }

    /**
     * Returns true if config section is sealed
     * @param string $section
     * @return boolean
     */
    protected function is_sealed($section) {
        if ($this->is_master())
            return false;

        if (w3_is_network() && !$this->is_master() && w3_force_master())
            return true;

        // browsercache settings change rules, so not available in child settings
        if ($section == 'browsercache')
            return true;

        if ($section == 'minify' && !$this->_config_master->get_boolean('minify.enabled'))
            return true;

        return $this->_config_admin->get_boolean($section . '.configuration_sealed');
    }

    /**
     * Returns true if we edit master config
     *
     * @return boolean
     */
    protected function is_master() {
        return $this->_config->is_master();
    }

    /**
     * Prints checkbox with config option value
     *
     * @param string $option_id
     * @param bool $disabled
     * @param string $class_prefix
     * @param bool $label
     */
    protected function checkbox($option_id, $disabled = false, $class_prefix = '', $label = true) {
        $section = substr($option_id, 0, strpos($option_id, '.'));

        $disabled = $disabled || $this->is_sealed($section);

        if (!$disabled)
            echo '<input type="hidden" name="' . $option_id . '" value="0" />';

        $name = str_replace('.', '_', $option_id);

        if ($label)
            echo '<label>';
        echo '<input class="'.$class_prefix.'enabled" type="checkbox" id="' . $name .
            '" name="' . $option_id . '" value="1" ';
        checked($this->_config->get_boolean($option_id), true);

        if ($disabled)
            echo 'disabled="disabled" ';

        echo ' />';
    }

    /**
     * Prints a radio button and if config value matches value
     * @param string $option_id config id
     * @param $value
     * @param bool $disabled
     * @param string $class_prefix
     */
    protected function radio($option_id, $value, $disabled = false, $class_prefix = ''){
        $section = substr($option_id, 0, strpos($option_id, '.'));

        if(is_bool($value))
            $rValue = $value?'1':'0';
        else
            $rValue = $value;
        $disabled = $disabled || $this->is_sealed($section);

        $name = str_replace('.', '_', $option_id);

        echo '<label>';
        echo '<input class="'.$class_prefix.'enabled" type="radio" id="' . $name .
            '" name="' . $option_id . '" value="',$rValue,'" ';
        checked($this->_config->get_boolean($option_id), $value);

        if ($disabled)
            echo 'disabled="disabled" ';

        echo ' />';
    }

    /**
     * Prints checkbox for debug option
     *
     * @param string $option_id
     */
    protected function checkbox_debug($option_id) {
        $section = substr($option_id, 0, strrpos($option_id, '.'));
        $section_enabled = $this->_config->get_boolean($section . '.enabled');
        $disabled = $this->is_sealed($section) || !$section_enabled;

        if (!$disabled)
            echo '<input type="hidden" name="' . $option_id . '" value="0" />';

        echo '<label>';
        echo '<input class="enabled" type="checkbox" name="' . $option_id .
            '" value="1" ';
        checked($this->_config->get_boolean($option_id), true);

        if ($disabled)
            echo 'disabled="disabled" ';

        echo ' />';
    }

    protected function sealing_disabled($section) {
        if ($this->is_sealed($section))
            echo 'disabled="disabled" ';
    }

    /**
     * Prints checkbox with admin config option value
     *
     * @param string $option_id
     * @param boolean $disabled
     */
    protected function checkbox_admin($option_id, $disabled = false) {
        if (!$disabled)
            $disabled = $this->_config->get_boolean('common.force_master');
        $checked = $this->_config_admin->get_boolean($option_id) || $disabled;
        if (!$disabled)
            echo '<input type="hidden" name="' . $option_id . '" value="0" />';

        echo '<label>';
        $id = str_replace('.', '_', $option_id);
        $class = $disabled ? 'disabled' : 'enabled';
        echo '<input id="' . $id . '" class="' . $class . '" type="checkbox" name="' . $option_id .
            '" value="1"';
        checked($checked, true);
        if ($disabled)
            echo ' disabled="disabled"';

        echo ' />';
    }

    protected abstract function view();

    /**
     * Check if memcache is available
     *
     * @param array $servers
     * @return boolean
     */
    function is_memcache_available($servers) {
        static $results = array();

        $key = md5(implode('', $servers));

        if (!isset($results[$key])) {
            w3_require_once(W3TC_LIB_W3_DIR . '/Cache/Memcached.php');

            @$memcached = new W3_Cache_Memcached(array(
                'servers' => $servers,
                'persistant' => false
            ));

            $test_string = sprintf('test_' . md5(time()));
            $test_value = array('content' => $test_string);
            $memcached->set($test_string, $test_value, 60);
            $test_value = $memcached->get($test_string);
            $results[$key] = ( $test_value['content'] == $test_string);
        }

        return $results[$key];
    }
}
