<?php

/**
 * Amazon CloudFront (Custom origin) CDN engine
 */
if (!defined('ABSPATH')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Cdn/S3/Cf.php');

class W3_Cdn_S3_Cf_Custom extends W3_Cdn_S3_Cf {
    var $type = W3TC_CDN_CF_TYPE_CUSTOM;

    /**
     * How and if headers should be set
     * @return string W3TC_CDN_HEADER_NONE, W3TC_CDN_HEADER_UPLOADABLE, W3TC_CDN_HEADER_MIRRORING
     */
    function headers_support() {
        return W3TC_CDN_HEADER_MIRRORING;
    }

    /**
     * If the CDN supports full page mirroring
     * @return bool
     */
    function supports_full_page_mirroring() {
        return true;
    }
}
