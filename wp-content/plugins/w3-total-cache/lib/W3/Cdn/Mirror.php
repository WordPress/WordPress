<?php

/**
 * W3 CDN Mirror Class
 */
if (!defined('ABSPATH')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Cdn/Base.php');

/**
 * Class W3_Cdn_Mirror
 */
class W3_Cdn_Mirror extends W3_Cdn_Base {
    /**
     * PHP5 Constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        $config = array_merge(array(
            'domain' => array(),
        ), $config);

        parent::__construct($config);
    }

    /**
     * Uploads files stub
     *
     * @param array $files
     * @param array $results
     * @param boolean $force_rewrite
     * @return boolean
     */
    function upload($files, &$results, $force_rewrite = false) {
        $results = $this->_get_results($files, W3TC_CDN_RESULT_OK, 'OK');

        return true;
    }

    /**
     * Deletes files stub
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function delete($files, &$results) {
        $results = $this->_get_results($files, W3TC_CDN_RESULT_OK, 'OK');

        return true;
    }

    /**
     * Tests mirror
     *
     * @param string $error
     * @return bool
     */
    function test(&$error) {
        if (!parent::test($error)) {
            return false;
        }

        $results = array();
        $files = array('' => 'purge_test_' . time());

        if (!$this->purge($files, $results) && isset($results[0]['error'])) {
            $error = $results[0]['error'];

            return false;
        }

        return true;
    }

    /**
     * Returns array of CDN domains
     *
     * @return array
     */
    function get_domains() {
        if (!empty($this->_config['domain'])) {
            return (array) $this->_config['domain'];
        }

        return array();
    }

    /**
     * How and if headers should be set
     * @return string W3TC_CDN_HEADER_NONE, W3TC_CDN_HEADER_UPLOADABLE, W3TC_CDN_HEADER_MIRRORING
     */
    function headers_support() {
        return W3TC_CDN_HEADER_MIRRORING;
    }
}
