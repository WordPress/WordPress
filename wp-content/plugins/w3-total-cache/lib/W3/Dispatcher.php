<?php

/**
 * Interplugin communication
 */

/**
 * Class W3_Dispatcher
 */
class W3_Dispatcher {
    /**
     * Config
     *
     * @var W3_Config
     */
    var $_config = null;

    /**
     * minify plugin enabled flag
     *
     * @var boolean
     */
    var $_minify_enabled = false;
    /**
     * minify
     *
     * @var W3_Minify
     */
    var $_minify = null;

    /**
     * @var W3_Plugin_CdnAdmin
     */
    var $_cdnadmin = null;

    /**
     * @var W3_Plugin_CdnCommon
     */
    var $_cdncommon = null;

    /**
     * PHP5 constructor
     */
    function __construct() {
        $this->_config = w3_instance('W3_Config');
        $this->_minify_enabled = $this->_config->get_boolean('minify.enabled');
    }

    /**
     * Checks if specific local url is uploaded to CDN
     * @param string $url
     * @return bool
     */
    function is_url_cdn_uploaded($url) {
        if ($this->_minify_enabled) {
            $data = $this->_get_minify()->get_url_custom_data($url);
            if (is_array($data) && isset($data['cdn.status']) && $data['cdn.status'] == 'uploaded') {
                return true;
            }
        }
        // supported only for minify-based urls, futher is not needed now
        return false;
    }
    
    /**
     * Creates file for CDN upload.
     * Needed because minify can handle urls of non-existing files but CDN needs
     * real file to upload it
     */
    function create_file_for_cdn($file_name) {
        if ($this->_minify_enabled) {
            $minify_document_root = w3_cache_blog_dir('minify') . '/';
        
            if (!substr($file_name, 0, strlen($minify_document_root)) == $minify_document_root) {
                // unexpected file name
                return;
            }
            
            $short_file_name = substr($file_name, strlen($minify_document_root));
            $this->_get_minify()->store_to_file($short_file_name, $file_name);
        }
    }
    
    /**
     * Called on successful file upload to CDN
     * 
     * @param $file_name
     */
    function on_cdn_file_upload($file_name) {
        if ($this->_minify_enabled) {
            $minify_document_root = w3_cache_blog_dir('minify') . '/';
        
            if (!substr($file_name, 0, strlen($minify_document_root)) == $minify_document_root) {
                // unexpected file name
                return;
            }
            
            $short_file_name = substr($file_name, strlen($minify_document_root));
            $this->_get_minify()->set_file_custom_data($short_file_name, 
                    array('cdn.status' => 'uploaded'));
        }
    }
    
    /**
     * Returns cached minify object
     * @return W3_Minify
     */
    function _get_minify() {
        if (is_null($this->_minify)) {
            $this->_minify = w3_instance('W3_Minify');
        }
        
        return $this->_minify;
    }

    /**
     * Generates canonical header code for nginx if browsercache plugin has
     * to generate it
     * @param W3_Config $config
     * @param boolean $cdnftp if CDN FTP is used
     * @param string $section
     * @return string
     */
    function on_browsercache_rules_generation_for_section($config, $cdnftp, 
            $section) {
        if ($section != 'other')
            return '';
        if ($this->canonical_generated_by($config, $cdnftp) != 'browsercache')
            return '';

        $rules_generator = w3_instance('W3_SharedRules');
        return $rules_generator->canonical_without_location($cdnftp);
    }

    /**
     * Checks whether canonical should be generated or not by browsercache plugin
     * @param W3_Config $config
     * @param boolean $cdnftp
     * @return string|null
     */
    public function canonical_generated_by($config, $cdnftp = false) {
        if (!$this->_should_canonical_be_generated($config, $cdnftp))
            return null;

        if (w3_is_nginx()) {
            // in nginx - browsercache generates canonical if its enabled, 
            // since it does not allow multiple location tags
            if ($config->get_boolean('browsercache.enabled'))
                return 'browsercache';
        }

        if ($config->get_boolean('cdn.enabled'))
            return 'cdn';

        return null;
    }

    /**
     * Basic check if canonical generation should be done
     * @param W3_Config $config
     * @param boolean $cdnftp
     * @return bool
     */
    private function _should_canonical_be_generated($config, $cdnftp) {
        if (is_null($this->_cdncommon)) {
            $this->_cdncommon = w3_instance('W3_Plugin_CdnCommon');
        }

        if (!$config->get_boolean('cdn.canonical_header'))
            return false;

        $cdn = $this->_cdncommon->get_cdn();
        return (($config->get_string('cdn.engine') != 'ftp' || $cdnftp) &&
                $cdn->headers_support() == W3TC_CDN_HEADER_MIRRORING);
    }

    /**
     * Checks whether canonical should be generated or not by browsercache plugin
     * @param W3_Config $config
     * @return string|null
     */
    public function allow_origin_generated_by($config) {
        if ($config->get_boolean('cdn.enabled'))
            return 'cdn';

        return null;
    }

    /**
     * If BrowserCache should generate rules specific for CDN. Used with CDN FTP
     * @param W3_Config $config
     * @return boolean;
     */
    public function should_browsercache_generate_rules_for_cdn($config) {
        if ($config->get_boolean('cdn.enabled') && 
                $config->get_string('cdn.engine') == 'ftp') {
            if (is_null($this->_cdncommon)) {
                $this->_cdncommon = w3_instance('W3_Plugin_CdnCommon');
            }
            $cdn = $this->_cdncommon->get_cdn();
            $domain = $cdn->get_domain();

            if ($domain)
                return true;
        }
        return false;
    }

    /**
     * Returns the domain used with the cdn.
     * @param string
     * @return string
     */
    public function get_cdn_domain($path = '') {
        if (is_null($this->_cdncommon)) {
            $this->_cdncommon = w3_instance('W3_Plugin_CdnCommon');
        }
        $cdn = $this->_cdncommon->get_cdn();
        return $cdn->get_domain($path);
    }

    /**
     * @param W3_Config $config
     * @return bool
     */
    public function send_minify_headers($config) {
        return apply_filters('w3tc_send_minify_headers', false);
    }

    /**
     * Sets New Relic appname for an application if current state meets requirements.
     *
     * @param W3_Config $config
     * @return boolean If appname was set or not
     */
    public function set_newrelic_appname($config) {
        if ($config->get_boolean('newrelic.enabled') && !$config->get_boolean('late_init.enabled')) {
            if (w3_is_multisite() && $config->get_boolean('common.force_master'))
                return false;
            /**
             * @var W3_Plugin_NewRelic $nr
             */
            $nr = w3_instance('W3_Plugin_NewRelic');
            $nr->set_appname();
            return true;
        }
        return false;
    }

}

