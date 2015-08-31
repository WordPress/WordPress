<?php
if (!defined('W3TC')) { die(); }

class W3_UI_Settings_General extends W3_UI_Settings_SettingsBase{
    protected  function strings() {
        return array(
            'general' => array(
                'widget.pagespeed.enabled' => __('Enable Google Page Speed dashboard widget', 'w3-total-cache'),
                'widget.pagespeed.key' => __('Page Speed <acronym title="Application Programming Interface">API</acronym> Key:', 'w3-total-cache'),
                'common.force_master' => __('Use single network configuration file for all sites.', 'w3-total-cache'),
                'common.visible_by_master_only' => __('Hide performance settings', 'w3-total-cache'),
                'config.path' => __('Nginx server configuration file path', 'w3-total-cache'),
                'config.check' => __('Verify rewrite rules', 'w3-total-cache'),
                'plugin.license_key' => __('License', 'w3-total-cache')
            )
        );
    }
}