<?php

/**
 * W3 ObjectCache plugin
 */
if (!defined('W3TC')) {
    die();
}
define('W3TC_MARKER_BEGIN_CLOUDFLARE', '# BEGIN W3TC CloudFlare');
define('W3TC_MARKER_END_CLOUDFLARE', '# END W3TC CloudFlare');

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_CloudFlare
 */
class W3_Plugin_CloudFlare extends W3_Plugin{
    /**
     * @var CloudFlareAPI $cf
     */
    private $cf;

    /**
     * Runs plugin
     */
    function run() {
        w3_require_once(W3TC_CORE_EXTENSION_DIR. '/CloudFlare/CloudFlareAPI.php');
        $this->cf = new CloudFlareAPI();
        add_action('wp_set_comment_status', array($this, 'set_comment_status'), 1, 2);
        add_action('w3tc_flush_all', array($this, 'flush_all'));
        $this->cf->fix_remote_addr();
    }

    public function flush_all() {
        $this->flush_cloudflare();
    }

    /**
     * @param $id
     * @param $status
     */
    function set_comment_status($id, $status) {
        $this->cf->report_if_spam($id, $status);
    }

    /**
     * @param $state
     * @return bool
     */
    public function send_minify_headers($state) {
        /**
         * @var W3_Config $config
         */
        $config = w3_instance('W3_Config');
        return ($config->get_boolean('cloudflare.enabled') && !$this->cf->minify_enabled());
    }


    /**
     * Purge the CloudFlare cache
     * @return void
     */
    function flush_cloudflare() {
        $response = null;

        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $email = $this->_config->get_string('email');
        $key = $this->_config->get_string('key');
        $zone = $this->_config->get_string('zone');


        if ($email && $key && $zone) {
            $config = array(
                'email' => $email,
                'key' => $key,
                'zone' => $zone
            );

            w3_require_once(W3TC_CORE_EXTENSION_DIR . '/CloudFlare/CloudFlareAPI.php');
            @$cloudflareAPI = new CloudFlareAPI($config);
            $cloudflareAPI->purge();
        }
    }

    public function menu_bar($menu_items) {
            $menu_items = array_merge($menu_items, array(
                array(
                    'id' => 'cloudflare',
                    'title' => __('CloudFlare', 'w3-total-cache'),
                    'href' => 'https://www.cloudflare.com'
                ),
                array(
                    'id' => 'cloudflare-my-websites',
                    'parent' => 'cloudflare',
                    'title' => __('My Websites', 'w3-total-cache'),
                    'href' => 'https://www.cloudflare.com/my-websites.html'
                ),
                array(
                    'id' => 'cloudflare-analytics',
                    'parent' => 'cloudflare',
                    'title' => __('Analytics', 'w3-total-cache'),
                    'href' => 'https://www.cloudflare.com/analytics.html'
                ),
                array(
                    'id' => 'cloudflare-account',
                    'parent' => 'cloudflare',
                    'title' => __('Account', 'w3-total-cache'),
                    'href' => 'https://www.cloudflare.com/my-account.html'
                )
            ));
        return $menu_items;
    }
}
