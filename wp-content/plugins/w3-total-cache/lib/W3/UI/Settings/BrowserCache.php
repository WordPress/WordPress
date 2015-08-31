<?php
if (!defined('W3TC')) { die(); }

class W3_UI_Settings_BrowserCache extends W3_UI_Settings_SettingsBase{
    protected  function strings() {
        return array(
            'general' => array(
                'browsercache.enabled' => __('Browser Cache:', 'w3-total-cache')
            ),
            'settings' => array(
                'browsercache.replace.exceptions' => __('Prevent caching exception list:', 'w3-total-cache'),
                'browsercache.no404wp' => __('Do not process 404 errors for static objects with WordPress', 'w3-total-cache'),
                'browsercache.no404wp.exceptions' => __('404 error exception list:', 'w3-total-cache'),
                'browsercache.cssjs.last_modified' => __('Set Last-Modified header', 'w3-total-cache'),
                'browsercache.cssjs.expires' => __('Set expires header', 'w3-total-cache'),
                'browsercache.cssjs.lifetime' => __('Expires header lifetime:', 'w3-total-cache'),
                'browsercache.cssjs.cache.control' => __('Set cache control header', 'w3-total-cache'),
                'browsercache.cssjs.cache.policy' => __('Cache Control policy:', 'w3-total-cache'),
                'browsercache.cssjs.etag' => __('Set entity tag (ETag)', 'w3-total-cache'),
                'browsercache.cssjs.w3tc' => __('Set W3 Total Cache header', 'w3-total-cache'),
                'browsercache.cssjs.compression' => __('Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> (gzip) compression', 'w3-total-cache'),
                'browsercache.cssjs.replace' => __('Prevent caching of objects after settings change', 'w3-total-cache'),
                'browsercache.cssjs.nocookies' => __('Disable cookies for static files', 'w3-total-cache'),
                'browsercache.html.last_modified' => __('Set Last-Modified header', 'w3-total-cache'),
                'browsercache.html.expires' => __('Set expires header', 'w3-total-cache'),
                'browsercache.html.lifetime' => __('Expires header lifetime:', 'w3-total-cache'),
                'browsercache.html.cache.control' => __('Set cache control header', 'w3-total-cache'),
                'browsercache.html.cache.policy' => __('Cache Control policy:', 'w3-total-cache'),
                'browsercache.html.etag' => __('Set entity tag (ETag)', 'w3-total-cache'),
                'browsercache.html.w3tc' => __('Set W3 Total Cache header', 'w3-total-cache'),
                'browsercache.html.compression' => __('Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> (gzip) compression', 'w3-total-cache'),
                'browsercache.other.last_modified' => __('Set Last-Modified header', 'w3-total-cache'),
                'browsercache.other.expires' => __('Set expires header', 'w3-total-cache'),
                'browsercache.other.lifetime' => __('Expires header lifetime:', 'w3-total-cache'),
                'browsercache.other.cache.control' => __('Set cache control header', 'w3-total-cache'),
                'browsercache.other.cache.policy' => __('Cache Control policy:', 'w3-total-cache'),
                'browsercache.other.etag' => __('Set entity tag (ETag)', 'w3-total-cache'),
                'browsercache.other.w3tc' => __('Set W3 Total Cache header', 'w3-total-cache'),
                'browsercache.other.compression' => __('Enable <acronym title="Hypertext Transfer Protocol">HTTP</acronym> (gzip) compression</label>', 'w3-total-cache'),
                'browsercache.other.replace' => __('Prevent caching of objects after settings change', 'w3-total-cache'),
                'browsercache.other.nocookies' => __('Disable cookies for static files', 'w3-total-cache')
            )
        );
    }
}
