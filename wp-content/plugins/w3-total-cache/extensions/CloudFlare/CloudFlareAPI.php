<?php

/**
 * W3 CloudFlare Class
 */
define('W3TC_CLOUDFLARE_API_URL', 'https://www.cloudflare.com/api_json.html');
define('W3TC_CLOUDFLARE_EXTERNAL_EVENT_URL', 'https://www.cloudflare.com/ajax/external-event.html');
define('W3TC_CLOUDFLARE_IP4_URL', 'https://www.cloudflare.com/ips-v4');
define('W3TC_CLOUDFLARE_IP6_URL', "https://www.cloudflare.com/ips-v6");

/**
 * Class CloudFlareAPI
 */
class CloudFlareAPI {
    /**
     * Config array
     *
     * @var array
     */
    var $_cf_config = array();

    /**
     * @var W3_Config
     */
    var $_config;

    /**
     * Checks if cloudflare error pushed to output
     *
     * @var boolean
     */
    var $_fault_signaled = false;

    /**
     * PHP5-style constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        $this->_config = w3_instance('W3_Config');
        if (empty($config)) {
            $this->_cf_config = array(
                        'email' => w3tc_get_extension_config('cloudflare','email'),
                        'key' => w3tc_get_extension_config('cloudflare','key'),
                        'zone' => w3tc_get_extension_config('cloudflare','zone'));
        } else {
            $this->_cf_config = array_merge(array(
                'email' => '',
                'key' => '',
                'zone' => ''
            ), $config);
        }

    }

    /**
     * Makes API request
     *
     * @param string $action
     * @param string $value
     * @return object
     */
    function api_request($action, $value = null) {
        w3_require_once(W3TC_INC_DIR . '/functions/http.php');
        if (empty($this->_cf_config['email']) || !filter_var($this->_cf_config['email'], FILTER_VALIDATE_EMAIL)) {
            $this->_set_last_error(__('CloudFlare requires "email" to be set.','w3-total-cache'));
            return false;
        }

        if (empty($this->_cf_config['key']) || !is_string($this->_cf_config['key'])) {
            $this->_set_last_error(__('CloudFlare requires "API key" to be set.','w3-total-cache'));
            return false;
        }


        if (empty($this->_cf_config['zone']) || !is_string($this->_cf_config['zone']) || strpos($this->_cf_config['zone'], '.') === false){
            $this->_set_last_error(__('CloudFlare requires "domain" to be set.','w3-total-cache'));
            return false;
        }

        $url = sprintf('%s?email=%s&tkn=%s&z=%s&a=%s', W3TC_CLOUDFLARE_API_URL, urlencode($this->_cf_config['email']), urlencode($this->_cf_config['key']), urlencode($this->_cf_config['zone']), urlencode($action));

        if ($value !== null) {
            $url .= sprintf('&v=%s', urlencode($value));
        }

        $response = w3_http_get($url);

        if (!is_wp_error($response)) {
            $response = json_decode($response['body']);
            if (isset($response->result) && $response->result == 'error') {
                $this->_set_last_error('Setting: ' . $action . ' ' . (isset($response->msg) ? $response->msg : 'Unknown error'));
            } else {
                $this->clear_last_error();
            }

            return $response;
        } else {
        	$this->_set_last_error('Setting: ' . $action. ' ' . $response->get_error_message());
    	}

        return false;
    }

    /**
     * Makes external event request
     *
     * @param string $type
     * @param string $value
     * @return array
     */
    function external_event($type, $value) {
        w3_require_once(W3TC_INC_DIR . '/functions/http.php');
        if (empty($this->_cf_config['email']) || !filter_var($this->_cf_config['email'], FILTER_VALIDATE_EMAIL))
            return false;

        if (empty($this->_cf_config['key']) || !is_string($this->_cf_config['key']))
            return false;

        if ($this->get_last_error())
            return false;

        $url = sprintf('%s?u=%s&tkn=%s&evnt_t=%s&evnt_v=%s', W3TC_CLOUDFLARE_EXTERNAL_EVENT_URL, urlencode($this->_cf_config['email']), urlencode($this->_cf_config['key']), urlencode($type), urlencode($value));
        $response = w3_http_get($url);

        if (!is_wp_error($response)) {
            return json_decode($response['body']);
        }

        return false;
    }

    /**
     * Fix client's IP-address
     *
     * @return void
     */
    function fix_remote_addr() {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && !empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            w3_require_once(W3TC_INC_DIR . '/functions/ip_in_range.php');
            if (strpos($_SERVER["REMOTE_ADDR"], ":") === FALSE) {
                $ip4_ranges = w3tc_get_extension_config('cloudflare','ips.ip4', $this->_config, array());
                foreach ($ip4_ranges as $range) {
                    if (w3_ipv4_in_range($_SERVER['REMOTE_ADDR'], $range)) {
                        $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
                        break;
                    }
                }
            } else {
                $ip6_ranges = w3tc_get_extension_config('cloudflare','ips.ip6', $this->_config, array());
                $ip6 = w3_get_ipv6_full($_SERVER["REMOTE_ADDR"]);
                foreach ($ip6_ranges as $range) {
                    if (w3_ipv6_in_range($ip6, $range)) {
                        $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
                        break;
                    }
                }
            }
        }
    }

    static function get_last_error() {
        return get_option('w3tc_clourflare_last_error');
    }

    static function clear_last_error() {
        update_option('w3tc_clourflare_last_error', false);
    }

    private function _set_last_error($message) {
        update_option('w3tc_clourflare_last_error', $message);
    }

    /**
     * Check
     * @throws FilesystemOperationException
     * @throws FileOperationException
     */
    public function update_ip_ranges() {
        w3_require_once(W3TC_INC_DIR . '/functions/http.php');
        $ip4_diff = $ip6_diff = false;
        $response =w3_http_get(W3TC_CLOUDFLARE_IP4_URL);
        $extensions_settings = $this->_config->get_array('extensions.settings', array());
        if (!is_wp_error($response)) {
            $ip4_data = $response['body'];
            $ip4_data = explode("\n", $ip4_data);
            $ip4_data_old = w3tc_get_extension_config('cloudflare','ips.ip4', $this->_config, array());
            if ($ip4_diff = array_diff($ip4_data, $ip4_data_old)) {
                $extensions_settings['cloudflare']['ips.ip4'] = $ip4_data;
                $this->_config->set('extensions.settings', $extensions_settings);
            }
        }
        $response =w3_http_get(W3TC_CLOUDFLARE_IP6_URL);
        if (!is_wp_error($response)) {
            $ip6_data = $response['body'];
            $ip6_data = explode("\n", $ip6_data);
            $ip6_data_old =  w3tc_get_extension_config('cloudflare','ips.ip6', $this->_config, array());
            if ($ip6_diff = array_diff($ip6_data, $ip6_data_old)) {
                $extensions_settings['cloudflare']['ips.ip6'] = $ip6_data;
                $this->_config->set('extensions.settings', $extensions_settings);
            }
        }
        if ($ip4_diff || $ip6_diff)
            try {
                $this->_config->save();
                $this->_config->refresh_cache();
            } catch(Exception $ex){}
    }

    // Report spam.
    function report_if_spam($id, $status) {
        // If spam, send this info over to CloudFlare.
        if ($status == "spam") {
            $comment = get_comment($id);
            $value = array("a" => $comment->comment_author,
                "am" => $comment->comment_author_email,
                "ip" => $comment->comment_author_IP,
                "con" => substr($comment->comment_content, 0, 100));
            $this->external_event('WP_SPAM', json_encode($value));
        }
    }

    /**
     * @return array
     */
    function get_settings() {
        $settings = get_transient('w3tc_cloudflare_settings');

        if (false === $settings) {
            $settings = array('sec_lvl' => 'eoff', 'devmode' => 0, 'async' => 0, 'minify' => 0);
            $response = $this->api_request('zone_settings');

            if ($response && $response->result == 'success' && isset($response->response->result->objs[0])) {
                switch ($response->response->result->objs[0]->sec_lvl) {
                    case 'I\'m under attack!':
                        $seclvl = 'help';
                        break;

                    case 'Essentially Off':
                        $seclvl = 'eoff';
                        break;

                    case 'High';
                        $seclvl = 'high';
                        break;

                    case 'Medium';
                        $seclvl = 'med';
                        break;

                    case 'Low';
                        $seclvl = 'low';
                        break;
                    default:
                        $seclvl = $response->response->result->objs[0]->sec_lvl;
                }
                $settings['sec_lvl'] = $seclvl;
                $devmode = ($response->response->result->objs[0]->dev_mode >= time() ? $response->response->result->objs[0]->dev_mode : 0);
                $settings['devmode'] = $devmode;
                $settings['async'] = $response->response->result->objs[0]->async;
                $settings['minify'] = $response->response->result->objs[0]->minify;
                set_transient('w3tc_cloudflare_settings', $settings, 3600);
            }
        }
        return $settings;
    }


    function check_lasterror() {
        if (w3tc_get_extension_config('cloudflare','enabled') && self::get_last_error()) {
            if (!$this->_fault_signaled) {
                $this->_fault_signaled = true;
                return sprintf('Unable to communicate with CloudFlare API: %s.', self::get_last_error());
            }
        }
        return false;
    }

    /**
     * If error has been reported
     * @return bool
     */
    function get_fault_signaled() {
        return $this->_fault_signaled;
    }

    public function get_options() {
        return array(
            'sec_lvl' => array(
                'help' => 'I\'m under attack!',
                'high' => 'High',
                'med' => 'Medium',
                'low' => 'Low',
                'eoff' => 'Essentially Off'
            ),
            'devmode' => array(
                1 => 'On',
                0 => 'Off'
            ),
            'dev_mode' => array(
                1 => 'On',
                0 => 'Off'
            ),
            'async'=> array(
                0 => 'Off',
                'a' => 'Automatic',
                'm' => 'Manual'
            ),
            'minify' => array(
                0 => 'Off',
                1 => 'JavaScript only',
                2 => 'CSS only',
                3 => 'JavaScript and CSS',
                4 => 'HTML only',
                5 => 'JavaScript and HTML',
                6 => 'CSS and HTML',
                7 => 'CSS, JavaScript, and HTML'
            )
        );
    }

    /**
     * @return mixed
     */
    public function purge() {
        @set_time_limit(w3tc_get_extension_config('cloudflare','timelimit.api_request', null, 180));
        delete_transient('w3tc_cloudflare_settings');
        return $this->api_request('fpurge_ts', 1);
    }

    /**
     * If minify is enabled at all
     * @return bool
     */
    public function minify_enabled() {
        $settings  = $this->get_settings();
        return $settings['minify'] > 0;
    }

    /**
     * @param $cf_values
     * @return bool
     */
    public function save_settings($cf_values) {
        @set_time_limit(w3tc_get_extension_config('cloudflare','timelimit.api_request', null, 180));
        ksort($cf_values);
        $cf_values = $this->_cleanup_settings($cf_values);
        foreach ($cf_values as $key => $settings) {
            if ($settings['old'] != $settings['new']) {
                $response = $this->api_request($key, $settings['new']);
                if (!$response || ($response && $response->result != 'success')) {
                    return false;
                }
            }
        }
        delete_transient('w3tc_cloudflare_settings');
        return true;
    }

    /**
     * Takes an array with posted CF settings and reformat it.
     * @param $settings
     * @return array
     */
    private function _cleanup_settings($settings) {
        $keys = array_keys($this->get_options());
        $clean_settings = array();
        foreach ($settings as $key => $value) {
            $setting = substr($key, 0, strlen($key)-4);
            if (in_array($setting, $keys)) {
                if (strpos($key, '_old') !== false) {
                    $clean_settings[$setting]['old'] = $value;
                } elseif (strpos($key, '_new') !== false) {
                    $clean_settings[$setting]['new'] = $value;
                }
            }
        }
        return $clean_settings;
    }

    /*
     * Resets settings cache
     */
    public function reset_settings_cache() {
        return delete_transient('w3tc_cloudflare_settings');
    }
}
