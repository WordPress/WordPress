<?php
class W3_WordPressSEO {
    /**
     * @var W3_Config
     */
    private $_config;

    public function run() {
        $this->_config = w3_instance('W3_Config');
        if ($this->_config->get_boolean('cdn.enabled')) {
            add_filter( 'wpseo_xml_sitemap_img_src', array($this, 'wpseo_cdn_filter' ));
        }
    }

    /**
     * Hook into WordPress SEO sitemap image filter.
     * @param $uri
     * @return string
     */
    public function wpseo_cdn_filter($uri) {
        /**
         * @var W3_Plugin_CdnCommon $w3_plugin_cdncommon
         */
        $w3_plugin_cdncommon = w3_instance('W3_Plugin_CdnCommon');
        $cdn = $w3_plugin_cdncommon->get_cdn();
        $parsed = parse_url($uri);
        $path = $parsed['path'];
        $remote_path = $w3_plugin_cdncommon->uri_to_cdn_uri($path);
        $new_url = $cdn->format_url($remote_path);

        return  $new_url;
    }
}

$ext = new W3_WordPressSEO();
$ext->run();
