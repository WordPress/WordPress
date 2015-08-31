<?php
if (!defined('W3TC')) { die(); }

class W3_UI_Settings_Monitoring extends W3_UI_Settings_SettingsBase{
    protected  function strings() {
        return array(
            'general' => array(
                'newrelic.enabled' => __('New Relic:', 'w3-total-cache'),
                'newrelic.api_key' => __('<acronym title="Application Programming Interface">API</acronym> key:','w3-total-cache'),
                'newrelic.use_network_wide_id' => __('Use above application name and ID for all sites in network:', 'w3-total-cache'),
            ),
            'settings' => array(
                'newrelic.cache_time' => __('Cache time:', 'w3-total-cache'),
                'newrelic.accept.logged_roles' => __('Use <acronym title="Real User Monitoring">RUM</acronym> only for following user roles', 'w3-total-cache'),
                'newrelic.accept.roles' => __('Select user roles that <acronym title="Real User Monitoring">RUM</acronym> should be enabled for:', 'w3-total-cache'),
                'newrelic.include_rum' => __('Include <acronym title="Real User Monitoring">RUM</acronym> in compressed or cached pages', 'w3-total-cache'),
                'newrelic.appname_prefix' => __('Prefix network sites:', 'w3-total-cache'),
                'newrelic.merge_with_network' => __('Include network sites stats in network:', 'w3-total-cache'),
                'newrelic.use_php_function' => __('Use PHP function to set application name:', 'w3-total-cache'),
                'newrelic.enable_xmit' => __('Enable XMIT', 'w3-total-cache')
            ));
    }
}