<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');

class W3_UI_CdnAdminView extends W3_UI_PluginView {
    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_cdn';

    /**
     * CDN tab
     *
     * @return void
     */
    function view() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
        $cdn_enabled = $this->_config->get_boolean('cdn.enabled');
        $cdn_engine = $this->_config->get_string('cdn.engine');
        $cdn_mirror = w3_is_cdn_mirror($cdn_engine);
        $cdn_mirror_purge_all = w3_cdn_can_purge_all($cdn_engine);
        $cdn_common = w3_instance('W3_Plugin_CdnCommon');

        $cdn = $cdn_common->get_cdn();
        $cdn_supports_header = $cdn->headers_support() == W3TC_CDN_HEADER_MIRRORING;
        $cdn_supports_full_page_mirroring = $cdn->supports_full_page_mirroring();
        $minify_enabled = ($this->_config->get_boolean('minify.enabled') && $this->_config->get_boolean('minify.rewrite') && (!$this->_config->get_boolean('minify.auto') || w3_is_cdn_mirror($this->_config->get_string('cdn.engine'))));

        $cookie_domain = $this->get_cookie_domain();
        $set_cookie_domain = $this->is_cookie_domain_enabled();

        // Required for Update Media Query String button
        $browsercache_enabled = $this->_config->get_boolean('browsercache.enabled');
        $browsercache_update_media_qs = ($this->_config->get_boolean('browsercache.cssjs.replace') || $this->_config->get_boolean('browsercache.other.replace'));
        if (in_array($cdn_engine, array('netdna', 'maxcdn'))) {
            $pull_zones = array();
            $authorization_key = $this->_config->get_string("cdn.$cdn_engine.authorization_key");
            $zone_id = $this->_config->get_integer("cdn.$cdn_engine.zone_id");
            $alias = $consumerkey = $consumersecret = '';

            if ($authorization_key) {
                $keys = explode('+', $authorization_key);
                if (sizeof($keys) == 3) {
                    list($alias, $consumerkey, $consumersecret) =  $keys;
                }
            }

            $authorized = $authorization_key != '' && $alias && $consumerkey && $consumersecret;
            $have_zone = $zone_id != 0;
            if ($authorized) {
                w3_require_once(W3TC_LIB_NETDNA_DIR . '/NetDNA.php');
                try {
                $api = new NetDNA($alias, $consumerkey, $consumersecret);
                $pull_zones = $api->get_zones_by_url(w3_get_home_url());
                } catch (Exception $ex) {
                    w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');

                    w3_e_error_box('<p>There is an error with your CDN settings: ' . $ex->getMessage() . '</p>');
                }
            }
        }
        include W3TC_INC_DIR . '/options/cdn.php';
    }

    /**
     * Returns cookie domain
     *
     * @return string
     */
    function get_cookie_domain() {
        $site_url = get_option('siteurl');
        $parse_url = @parse_url($site_url);

        if ($parse_url && !empty($parse_url['host'])) {
            return $parse_url['host'];
        }

        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Checks if COOKIE_DOMAIN is enabled
     *
     * @return bool
     */
    function is_cookie_domain_enabled() {
        $cookie_domain = $this->get_cookie_domain();

        return (defined('COOKIE_DOMAIN') && COOKIE_DOMAIN == $cookie_domain);
    }
}
