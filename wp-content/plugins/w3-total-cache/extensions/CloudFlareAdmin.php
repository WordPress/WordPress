<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 2014-02-21
 * Time: 09:47
 */

class CloudFlareAdmin {
    /**
     * @var W3_Config
     */
    private $_config;
    /**
     * @var CloudFlareAPI
     */
    private $cloudflareAPI;

    function run() {
        if (w3_is_extension_active('cloudflare'))
            $this->init();
        add_filter('w3tc_extensions', array($this, 'extension'), 10, 2);
    }

    private function init() {
        $this->_config = w3_instance('W3_Config');
        add_filter('w3tc_save_options', array($this, 'save_settings'),10,3);
        add_filter('w3tc_general_anchors', array($this, 'general_settings_anchors'));
        add_filter('w3tc_errors', array($this, 'register_error'));
        add_filter('w3tc_dashboard_actions', array($this, 'dashboard_actions'));
        add_action('w3tc_general_boxarea_cloudflare',array($this, 'general_settings_box'));

        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $page = W3_Request::get_string('page');
        if ($page && strpos($page, 'w3tc_') !== false) {
            /**
             * Only admin can see W3TC notices and errors
             */
            add_action('admin_notices', array(
                $this,
                'admin_notices'
            ));
            add_action('network_admin_notices', array(
                $this,
                'admin_notices'
            ));
        }
        add_action('wp_ajax_w3tc_cloudflare_api_request', array($this, 'action_cloudflare_api_request'));


        w3_require_once(W3TC_CORE_EXTENSION_DIR . '/CloudFlare/CloudFlareAPI.php');
        $this->cloudflareAPI = new CloudFlareAPI();
        
        if (w3tc_get_extension_config('cloudflare', 'enabled', $this->_config, false)) {
            $this->check_ip_versions();
        }

        add_filter('w3tc_setting_label', array($this, 'setting_label'));
        add_filter('w3tc_custom_module_settings', array($this, 'set_settings_class'));
        add_filter("w3tc_extension_plugin_links-cloudflare", array($this, 'extension_settings_link'));
    }
    function extension_settings_link($links) {
        $links = array();
        $links[] = '<a class="edit" href="' . esc_attr(w3_admin_url('admin.php?page=w3tc_general#cloudflare')).'">'. __('Settings').'</a>';

        return $links;
    }
    function set_settings_class($modules) {
        $modules['cloudflare'] = array($this, 'load_settings_class');
        return $modules;
    }

    function load_settings_class() {
        w3_require_once(W3TC_CORE_EXTENSION_DIR . '/CloudFlare/CloudFlareSettings.php');
        return new CloudFlareSettings();
    }

    /**
     * @param $extensions
     * @param W3_Config $config
     * @return mixed
     */
    function extension($extensions, $config) {
        global $current_user;
        $message = array();
        $message[] = 'CloudFlare';
        $cloudflare_signup_email = '';
        $cloudflare_signup_user = '';

        if (is_a($current_user, 'WP_User')) {
            if ($current_user->user_email) {
                $cloudflare_signup_email = $current_user->user_email;
            }

            if ($current_user->user_login && $current_user->user_login != 'admin') {
                $cloudflare_signup_user = $current_user->user_login;
            }
        }
        $extensions['cloudflare'] = array (
            'name' => 'CloudFlare',
            'author' => 'W3 EDGE',
            'description' =>  sprintf( __('CloudFlare protects and accelerates websites. <a href="%s" target="_blank">Sign up now for free</a> to get started,
        	or if you have an account simply log in to obtain your <abbr title="Application Programming Interface">API</abbr> key from the <a target="_blank" href="https://www.cloudflare.com/my-account">account page</a> to enter it on the General Settings box that appears after plugin activation.
        	Contact the CloudFlare <a href="http://www.cloudflare.com/help.html" target="_blank">support team</a> with any questions.', 'w3-total-cache'), 'https://www.cloudflare.com/sign-up.html?affiliate=w3edge&amp;seed_domain=' . w3_get_host() . '&amp;email=' . htmlspecialchars($cloudflare_signup_email) . '&amp;username=' . htmlspecialchars($cloudflare_signup_user) ),
            'author uri' => 'http://www.w3-edge.com/',
            'extension uri' => 'http://www.w3-edge.com/',
            'extension id' => 'cloudflare',
            'version' => '1.0',
            'enabled' => true,
            'requirements' => implode(', ', $message),
            'path' => 'w3-total-cache/extensions/CloudFlare.php'
        );

        return $extensions;
    }

    public function register_error($errors) {
        if ($error = $this->cloudflareAPI->check_lasterror()) {
            $errors[] = $error;
        }elseif(W3_Request::get_string('w3tc_error')) {
            $errors[] = __('Unable to make CloudFlare API request.', 'w3-total-cache');
        }
        return $errors;
    }

    public function register_notifications($notes) {
        $notes['flush_all_except_cf'] = __('All caches except CloudFlare successfully emptied.', 'w3-total-cache');
        $notes['cloudflare_api_request'] = __('Unable to make CloudFlare API request.', 'w3-total-cache');
        return $notes;
    }
    public function dashboard_actions($actions) {
        $cloudflare_enabled = true;

        if ($cloudflare_enabled && w3tc_get_extension_config('cloudflare','email') && w3tc_get_extension_config('cloudflare','key')) {
            $can_empty_cloudflare = true;
        } else {
            $can_empty_cloudflare = false;
        }
        $modules = w3_instance('W3_ModuleStatus');
        $can_empty_memcache = $modules->can_empty_memcache();
        $can_empty_opcode = $modules->can_empty_opcode();
        $can_empty_file = $modules->can_empty_file();
        $can_empty_varnish = $modules->can_empty_varnish();

        $actions[] = ' or <input id="flush_all_except_cf" class="button" type="submit" name="w3tc_flush_all_except_cf" value="'.
        __('empty all caches except CloudFlare', 'w3-total-cache').'"'.
            ((! $can_empty_memcache && ! $can_empty_opcode && ! $can_empty_file && ! $can_empty_varnish) ?
            'disabled="disabled"':'') . '> ' . __('at once', 'w3-total-cache');
        return $actions;
    }
    /**
     * Check if last check has expired. If so update CloudFlare ips
     */
    function check_ip_versions() {
        $checked = get_transient('w3tc_cloudflare_ip_check');

        if (false === $checked) {
            try {
                $this->cloudflareAPI->update_ip_ranges();
            } catch (Exception $ex) {}
            set_transient('w3tc_cloudflare_ip_check', time(), 3600*24);
        }
    }

    function admin_notices() {
        $plugins = get_plugins();
        if (array_key_exists('cloudflare/cloudflare.php', $plugins) && $this->_config->get_boolean('notes.cloudflare_plugin')) {
            w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/other.php');
            echo sprintf('<div class="error"><p>%s %s</p></div>', __('CloudFlare plugin detected. We recommend removing the
            plugin as it offers no additional capabilities when W3 Total Cache is installed. This message will disappear
            when CloudFlare is removed.', 'w3-total-cache'),
                w3tc_button_hide_note('Hide this message', 'cloudflare_plugin')
            );
        }
    }


    /**
     * Send CloudFlare API request
     *
     * @return void
     */
    function action_cloudflare_api_request() {
        $result = false;
        $response = null;

        $actions = array(
            'devmode',
            'sec_lvl',
            'fpurge_ts'
        );

        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $email = W3_Request::get_string('email');
        $key = W3_Request::get_string('key');
        $zone = W3_Request::get_string('zone');
        $action = W3_Request::get_string('command');
        $value = W3_Request::get_string('value');
        $nonce = W3_Request::get_string('_wpnonce');

        if (!wp_verify_nonce($nonce, 'w3tc')) {
            $error ='Access denied.';
        } elseif (!$email) {
            $error = 'Empty email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email.';
        } elseif (!$key) {
            $error = 'Empty key.';
        } elseif (!$zone) {
            $error = 'Empty zone.';
        } elseif (strpos($zone, '.') === false) {
            $error = 'Invalid domain.';
        } elseif (!in_array($action, $actions)) {
            $error = 'Invalid action.';
        } else {
            $config = array(
                'email' => $email,
                'key' => $key,
                'zone' => $zone
            );

            w3_require_once(W3TC_CORE_EXTENSION_DIR. '/CloudFlare/CloudFlareAPI.php');
            @$this->cloudflareAPI = new CloudFlareAPI($config);

            @set_time_limit(w3tc_get_extension_config('cloudflare','timelimit.api_request', $this->_config, 180));
            $response = $this->cloudflareAPI->api_request($action, $value);

            if ($response) {
                if ($response->result == 'success') {
                    $result = true;
                    $error = 'OK';
                } else {
                    $error = $response->msg;
                }
            } else {
                $error = 'Unable to make CloudFlare API request.';
            }
        }

        $return = array(
            'result' => $result,
            'error' => $error,
            'response' => $response
        );

        echo json_encode($return);
        exit;
    }

    public function general_settings_anchors($anchors) {
        $anchors[] = array('id'=>'cloudflare', 'text' => 'CloudFlare');
        return $anchors;
    }
    public function general_settings_box() {
        global $current_user;
        $cloudflare_enabled = true;

        $cf_options = $this->cloudflareAPI->get_options();
        $cloudflare_seclvls = $cf_options['sec_lvl'];
        $cloudflare_devmodes = $cf_options['dev_mode'];

        $cloudflare_rocket_loaders = $cf_options['async'];
        $cloudflare_minifications = $cf_options['minify'];

        $cloudflare_seclvl = 'med';
        $cloudflare_devmode_expire = 0;
        $cloudflare_devmode = 0;
        $cloudflare_rocket_loader = 0;
        $cloudflare_minify = 0;

        if ($cloudflare_enabled && w3tc_get_extension_config('cloudflare', 'email') && w3tc_get_extension_config('cloudflare', 'key')) {
            $settings = $this->cloudflareAPI->get_settings();
            $cloudflare_seclvl = $settings['sec_lvl'];
            $cloudflare_devmode_expire = $settings['devmode'];
            $cloudflare_rocket_loader = $settings['async'];
            $cloudflare_devmode = ($cloudflare_devmode_expire ? 1 : 0);
            $cloudflare_minify = $settings['minify'];
            $can_empty_cloudflare = true;
        } else {
            $can_empty_cloudflare = false;
        }

        include  W3TC_CORE_EXTENSION_DIR . '/CloudFlare/general-settings-box.php';
    }

    /**
     * Flsuh all caches except CloudFlare
     */
    function action_flush_all_except_cf() {
        //$this->flush_all(false);

        w3_admin_redirect(array(
            'w3tc_note' => 'flush_all_except_cf'
        ), true);
    }

    /**
     * @param W3_Config $config
     * @param $old
     * @param $config_admin
     * @return \W3_Config
     */
    public function save_settings($config, $old, $config_admin) {
        w3_require_once(W3TC_CORE_EXTENSION_DIR . '/CloudFlare/CloudFlareAPI.php');
        $this->cloudflareAPI = new CloudFlareAPI();
        $this->cloudflareAPI->reset_settings_cache();
        if ((boolean)w3tc_get_extension_config('cloudflare', 'enabled') && $this->cloudflareAPI->minify_enabled() && $config->get_boolean('minify.enabled')) {
            $config->set('minify.enabled',false);
        }

        /**
         * Handle CloudFlare changes
         */
        if (((w3_get_blog_id() == 0) ||
                (w3_get_blog_id() != 0 && !w3_extension_is_sealed('cloudflare'))
            )) {
            /**
             * @var $this->cloudflareAPI W3_CloudFlare
             */
            $cf_values = W3_Request::get_as_array('cloudflare_');
            $this->cloudflareAPI->save_settings($cf_values);
        }
        return $config;
    }
}

$cf = new CloudFlareAdmin();
$cf->run();
